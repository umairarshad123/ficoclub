<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\SubscriptionEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Comprehensive Authorize.Net webhook handler.
 *
 * Every event Auth.net actually sends is either handled here or explicitly
 * acknowledged as informational. Nothing gets silently dropped.
 *
 * Event inventory (from real Auth.net traffic on this merchant):
 *
 *   PAYMENT EVENTS
 *     net.authorize.payment.authcapture.created    → Successful charge (initial OR recurring)
 *     net.authorize.payment.fraud.declined         → AVS/fraud decline (informational)
 *
 *   SUBSCRIPTION LIFECYCLE
 *     net.authorize.customer.subscription.created    → ARB sub created (informational, we create row ourselves)
 *     net.authorize.customer.subscription.updated    → ARB sub modified (recovery path if status=active)
 *     net.authorize.customer.subscription.failed     → Recurring payment failed → past_due
 *     net.authorize.customer.subscription.suspended  → After failures → past_due
 *     net.authorize.customer.subscription.cancelled  → User/merchant cancelled → terminated
 *     net.authorize.customer.subscription.expired    → Reached end of term → terminated
 *     net.authorize.customer.subscription.terminated → Hard termination → terminated
 *
 *   CUSTOMER / PAYMENT PROFILE EVENTS (informational)
 *     net.authorize.customer.created
 *     net.authorize.customer.updated
 *     net.authorize.customer.paymentProfile.created
 *     net.authorize.customer.paymentProfile.updated
 */
class AuthorizeNetWebhookController extends Controller
{
    // ── Event type constants (no typos possible) ────────────────────────────
    private const EVT_PAYMENT_SUCCESS  = 'net.authorize.payment.authcapture.created';
    private const EVT_PAYMENT_FRAUD    = 'net.authorize.payment.fraud.declined';

    private const EVT_SUB_CREATED      = 'net.authorize.customer.subscription.created';
    private const EVT_SUB_UPDATED      = 'net.authorize.customer.subscription.updated';
    private const EVT_SUB_FAILED       = 'net.authorize.customer.subscription.failed';
    private const EVT_SUB_SUSPENDED    = 'net.authorize.customer.subscription.suspended';
    private const EVT_SUB_CANCELLED    = 'net.authorize.customer.subscription.cancelled';
    private const EVT_SUB_EXPIRED      = 'net.authorize.customer.subscription.expired';
    private const EVT_SUB_TERMINATED   = 'net.authorize.customer.subscription.terminated';

    private const EVT_CUSTOMER_CREATED        = 'net.authorize.customer.created';
    private const EVT_CUSTOMER_UPDATED        = 'net.authorize.customer.updated';
    private const EVT_PAYMENT_PROFILE_CREATED = 'net.authorize.customer.paymentProfile.created';
    private const EVT_PAYMENT_PROFILE_UPDATED = 'net.authorize.customer.paymentProfile.updated';

    /** Events that mean "this subscription just failed/is past due" */
    private const FAILURE_EVENTS = [
        self::EVT_SUB_FAILED,
        self::EVT_SUB_SUSPENDED,
    ];

    /** Events that mean "this subscription is done, permanently" */
    private const TERMINATION_EVENTS = [
        self::EVT_SUB_CANCELLED,
        self::EVT_SUB_EXPIRED,
        self::EVT_SUB_TERMINATED,
    ];

    /** Events that are just informational — we log them but don't act */
    private const INFORMATIONAL_EVENTS = [
        self::EVT_PAYMENT_FRAUD,
        self::EVT_SUB_CREATED,
        self::EVT_CUSTOMER_CREATED,
        self::EVT_CUSTOMER_UPDATED,
        self::EVT_PAYMENT_PROFILE_CREATED,
        self::EVT_PAYMENT_PROFILE_UPDATED,
    ];

    public function handle(Request $request)
    {
        $payload = json_decode($request->getContent(), true);

        Log::info('Authorize.net webhook received', ['payload' => $payload]);

        $eventType      = $payload['eventType'] ?? null;
        $notificationId = $payload['notificationId'] ?? null;
        $entityId       = $payload['payload']['id'] ?? null;
        $amount         = $payload['payload']['authAmount'] ?? null;
        $invoiceNumber  = $payload['payload']['invoiceNumber'] ?? null;
        $arbStatus      = $payload['payload']['status'] ?? null;

        Log::info('Webhook event parsed', [
            'event_type'      => $eventType,
            'notification_id' => $notificationId,
            'entity_id'       => $entityId,
            'amount'          => $amount,
            'invoice_number'  => $invoiceNumber,
            'arb_status'      => $arbStatus,
        ]);

        // ── Idempotency: dedupe by notificationId (24h window) ──────────────
        if ($notificationId) {
            $dedupeKey = 'authnet_notif_' . $notificationId;
            if (Cache::has($dedupeKey)) {
                Log::info('Duplicate webhook notification — skipping', [
                    'notification_id' => $notificationId,
                    'event_type'      => $eventType,
                ]);
                return response('Duplicate', 200);
            }
            Cache::put($dedupeKey, true, now()->addHours(24));
        }

        // ═════════════════════════════════════════════════════════════════════
        // ROUTE: Successful payment (initial OR recurring renewal)
        // ═════════════════════════════════════════════════════════════════════
        if ($eventType === self::EVT_PAYMENT_SUCCESS) {
            return $this->handlePaymentSuccess($invoiceNumber, $entityId, $amount, $payload);
        }

        // ═════════════════════════════════════════════════════════════════════
        // ROUTE: Subscription failed / suspended → past_due
        // ═════════════════════════════════════════════════════════════════════
        if (in_array($eventType, self::FAILURE_EVENTS, true)) {
            return $this->handleSubscriptionFailure($eventType, $entityId, $payload);
        }

        // ═════════════════════════════════════════════════════════════════════
        // ROUTE: Subscription cancelled / expired / terminated → terminated
        // ═════════════════════════════════════════════════════════════════════
        if (in_array($eventType, self::TERMINATION_EVENTS, true)) {
            return $this->handleSubscriptionTermination($eventType, $entityId, $payload);
        }

        // ═════════════════════════════════════════════════════════════════════
        // ROUTE: Subscription updated → recovery if status flipped to active
        // ═════════════════════════════════════════════════════════════════════
        if ($eventType === self::EVT_SUB_UPDATED) {
            return $this->handleSubscriptionUpdate($entityId, $arbStatus, $payload);
        }

        // ═════════════════════════════════════════════════════════════════════
        // ROUTE: Informational — log and acknowledge
        // ═════════════════════════════════════════════════════════════════════
        if (in_array($eventType, self::INFORMATIONAL_EVENTS, true)) {
            Log::info('Informational webhook acknowledged', [
                'event_type' => $eventType,
                'entity_id'  => $entityId,
            ]);
            return response('Event received', 200);
        }

        // ═════════════════════════════════════════════════════════════════════
        // FALLBACK: Unknown event — log as warning so we notice
        // ═════════════════════════════════════════════════════════════════════
        Log::warning('UNHANDLED webhook event type — add to controller', [
            'event_type' => $eventType,
            'payload'    => $payload,
        ]);

        return response('Event received', 200);
    }

    // ═════════════════════════════════════════════════════════════════════════
    // HANDLER: Successful payment (authcapture)
    //
    // Two sub-cases:
    //   A) Initial charge — invoice is in our cache → fire GHL main/referral webhook
    //   B) Recurring charge — invoice is NOT in cache → auto-recover past_due sub + log event
    // ═════════════════════════════════════════════════════════════════════════
    private function handlePaymentSuccess(?string $invoiceNumber, ?string $transactionId, $amount, array $payload)
    {
        if (!$invoiceNumber) {
            Log::warning('authcapture without invoice number, ignoring');
            return response('Event received', 200);
        }

        Cache::put('paid_invoice_' . $invoiceNumber, true, now()->addMinutes(30));
        Log::info('Payment confirmed and cached', ['invoice_number' => $invoiceNumber]);

        $cachedCustomer = Cache::get('checkout_customer_' . $invoiceNumber);

        if ($cachedCustomer) {
            // ── Sub-case A: INITIAL CHARGE flow ──────────────────────────────
            return $this->fireInitialChargeWebhooks($invoiceNumber, $transactionId, $cachedCustomer);
        }

        // ── Sub-case B: RECURRING CHARGE flow ────────────────────────────────
        // The invoice isn't in our cache because the initial signup was days/weeks ago.
        // Find the subscription by the first payment's invoice number, then:
        //   - log a successful event
        //   - if the sub was past_due, recover it to active
        $subscription = Subscription::where('invoice_number', $invoiceNumber)->first();

        if (!$subscription) {
            // This invoice belongs to a recurring charge whose invoice_number was auto-generated by Auth.net
            // (ARB uses the ORIGINAL invoice for every recur). But just in case, try by transId too.
            if ($transactionId) {
                $subscription = Subscription::where('transaction_id', $transactionId)->first();
            }
        }

        if (!$subscription) {
            Log::info('Recurring charge: no local subscription match (likely a legacy sub)', [
                'invoice_number' => $invoiceNumber,
                'transaction_id' => $transactionId,
            ]);
            return response('Event received', 200);
        }

        // Log the successful recurring charge
        SubscriptionEvent::create([
            'subscription_id' => $subscription->id,
            'event_type'      => 'payment_recovered', // reusing existing enum value
            'payload'         => $payload,
            'note'            => sprintf(
                'Recurring payment successful. Invoice %s, Txn %s, Amount $%s',
                $invoiceNumber,
                $transactionId,
                $amount
            ),
        ]);

        // Auto-recover past_due subs (customer updated card, payment went through)
        if ($subscription->status === 'past_due') {
            $subscription->update([
                'status'               => 'active',
                'failed_payment_count' => 0,
                'first_failed_at'      => null,
                'grace_period_ends_at' => null,
            ]);

            Log::info('Subscription auto-recovered: past_due → active after successful recurring charge', [
                'subscription_id' => $subscription->id,
                'email'           => $subscription->email,
            ]);

            // Optional GHL notification for recovered customers
            $this->fireGhl(
                env('GHL_SUBSCRIPTION_RECOVERED_WEBHOOK_URL'),
                [
                    'first_name'          => $subscription->first_name,
                    'last_name'           => $subscription->last_name,
                    'email'               => $subscription->email,
                    'phone'               => $subscription->phone,
                    'plan'                => $subscription->plan_label,
                    'arb_subscription_id' => $subscription->arb_subscription_id,
                    'source'              => '850_fico_subscription_recovered',
                    'tags'                => ['subscription-recovered', 'payment-recovered'],
                ],
                'RECOVERED',
                $invoiceNumber
            );
        }

        return response('Event received', 200);
    }

    // ═════════════════════════════════════════════════════════════════════════
    // HELPER: fire the right GHL webhook for initial checkout
    // ═════════════════════════════════════════════════════════════════════════
    private function fireInitialChargeWebhooks(string $invoiceNumber, ?string $transactionId, array $customer)
    {
        if (Cache::get('ghl_webhook_fired_' . $invoiceNumber)) {
            Log::info('GHL webhook already fired from controller — skipping duplicate', [
                'invoice_number' => $invoiceNumber,
            ]);
            return response('Payment confirmed', 200);
        }

        $referralCode = $customer['referral_code'] ?? null;

        if ($referralCode) {
            $this->fireGhl(
                config('services.ghl.referral_webhook_url'),
                [
                    'first_name'     => $customer['first_name'],
                    'last_name'      => $customer['last_name'],
                    'email'          => $customer['email'],
                    'phone'          => $customer['phone'],
                    'address'        => $customer['address'],
                    'city'           => $customer['city'],
                    'state'          => $customer['state'],
                    'zip'            => $customer['zip'],
                    'plan'           => $customer['plan_label'],
                    'plan_key'       => $customer['plan_key'],
                    'amount'         => $customer['amount'],
                    'invoice_number' => $invoiceNumber,
                    'transaction_id' => $transactionId,
                    'referral_code'  => $referralCode,
                    'source'         => 'referral_partner',
                    'tags'           => [
                        'referral-partner',
                        'partner-' . strtolower(str_replace('PARTNER-', '', $referralCode)),
                    ],
                ],
                'REFERRAL',
                $invoiceNumber
            );
        } else {
            $this->fireGhl(
                config('services.ghl.checkout_webhook_url'),
                [
                    'first_name'     => $customer['first_name'],
                    'last_name'      => $customer['last_name'],
                    'email'          => $customer['email'],
                    'phone'          => $customer['phone'],
                    'address'        => $customer['address'],
                    'city'           => $customer['city'],
                    'state'          => $customer['state'],
                    'zip'            => $customer['zip'],
                    'plan'           => $customer['plan_label'],
                    'amount'         => $customer['amount'],
                    'invoice_number' => $invoiceNumber,
                    'transaction_id' => $transactionId,
                    'source'         => '850_fico_checkout',
                ],
                'MAIN',
                $invoiceNumber
            );
        }

        return response('Payment confirmed', 200);
    }

    // ═════════════════════════════════════════════════════════════════════════
    // HANDLER: Subscription failed/suspended → mark past_due, fire GHL
    // ═════════════════════════════════════════════════════════════════════════
    private function handleSubscriptionFailure(string $eventType, ?string $arbId, array $payload)
    {
        if (!$arbId) {
            Log::warning('Failure event missing ARB id', ['event_type' => $eventType]);
            return response('Event received', 200);
        }

        $subscription = Subscription::where('arb_subscription_id', $arbId)->first();

        if (!$subscription) {
            Log::warning('Failure event: no matching local subscription', [
                'event_type' => $eventType,
                'arb_id'     => $arbId,
            ]);
            return response('Event received', 200);
        }

        if ($subscription->status === 'terminated') {
            Log::info('Failure ignored: subscription already terminated', [
                'subscription_id' => $subscription->id,
            ]);
            return response('Event received', 200);
        }

        $failedCount = $subscription->failed_payment_count + 1;
        $isFirstFail = $subscription->first_failed_at === null;

        $updates = [
            'status'               => 'past_due',
            'failed_payment_count' => $failedCount,
        ];

        if ($isFirstFail) {
            $updates['first_failed_at']      = now();
            $updates['grace_period_ends_at'] = now()->addDays(7);
        }

        $subscription->update($updates);
        $subscription->refresh();

        Log::info('Subscription marked past_due', [
            'subscription_id'   => $subscription->id,
            'email'             => $subscription->email,
            'failed_count'      => $failedCount,
            'grace_period_ends' => $subscription->grace_period_ends_at,
        ]);

        SubscriptionEvent::create([
            'subscription_id' => $subscription->id,
            'event_type'      => 'payment_failed',
            'payload'         => $payload,
            'note'            => 'Auth.net event: ' . $eventType . '. Failure #' . $failedCount,
        ]);

        $this->fireGhl(
            config('services.ghl.recurring_failed_webhook_url'),
            [
                'first_name'           => $subscription->first_name,
                'last_name'            => $subscription->last_name,
                'email'                => $subscription->email,
                'phone'                => $subscription->phone,
                'address'              => $subscription->address,
                'city'                 => $subscription->city,
                'state'                => $subscription->state,
                'zip'                  => $subscription->zip,
                'plan'                 => $subscription->plan_label,
                'plan_key'             => $subscription->plan_key,
                'recurring_amount'     => $subscription->recurring_amount,
                'invoice_number'       => $subscription->invoice_number,
                'arb_subscription_id'  => $subscription->arb_subscription_id,
                'failed_payment_count' => $failedCount,
                'first_failed_at'      => optional($subscription->first_failed_at)->toIso8601String(),
                'grace_period_ends_at' => optional($subscription->grace_period_ends_at)->toIso8601String(),
                'source'               => '850_fico_recurring_failed',
                'tags'                 => ['recurring-payment-failed', 'past-due', '7-day-grace'],
            ],
            'RECURRING_FAILED',
            $subscription->invoice_number ?? 'unknown'
        );

        return response('Event received', 200);
    }

    // ═════════════════════════════════════════════════════════════════════════
    // HANDLER: Subscription cancelled/expired/terminated → mark terminated
    // ═════════════════════════════════════════════════════════════════════════
    private function handleSubscriptionTermination(string $eventType, ?string $arbId, array $payload)
    {
        if (!$arbId) {
            Log::warning('Termination event missing ARB id', ['event_type' => $eventType]);
            return response('Event received', 200);
        }

        $subscription = Subscription::where('arb_subscription_id', $arbId)->first();

        if (!$subscription) {
            Log::warning('Termination event: no matching local subscription', [
                'event_type' => $eventType,
                'arb_id'     => $arbId,
            ]);
            return response('Event received', 200);
        }

        if ($subscription->status === 'terminated') {
            Log::info('Already terminated, skipping', ['subscription_id' => $subscription->id]);
            return response('Event received', 200);
        }

        $subscription->update([
            'status'        => 'terminated',
            'terminated_at' => now(),
        ]);

        Log::info('Subscription marked terminated', [
            'subscription_id' => $subscription->id,
            'email'           => $subscription->email,
            'event_type'      => $eventType,
        ]);

        SubscriptionEvent::create([
            'subscription_id' => $subscription->id,
            'event_type'      => 'terminated',
            'payload'         => $payload,
            'note'            => 'Auth.net event: ' . $eventType,
        ]);

        $this->fireGhl(
            env('GHL_SUBSCRIPTION_TERMINATED_WEBHOOK_URL'),
            [
                'first_name'          => $subscription->first_name,
                'last_name'           => $subscription->last_name,
                'email'               => $subscription->email,
                'phone'               => $subscription->phone,
                'plan'                => $subscription->plan_label,
                'plan_key'            => $subscription->plan_key,
                'arb_subscription_id' => $subscription->arb_subscription_id,
                'terminated_at'       => now()->toIso8601String(),
                'reason'              => $eventType,
                'source'              => '850_fico_subscription_terminated',
                'tags'                => ['subscription-terminated', str_replace('net.authorize.customer.subscription.', '', $eventType)],
            ],
            'TERMINATED',
            $subscription->invoice_number ?? 'unknown'
        );

        return response('Event received', 200);
    }

    // ═════════════════════════════════════════════════════════════════════════
    // HANDLER: Subscription updated — recovery path
    // ═════════════════════════════════════════════════════════════════════════
    private function handleSubscriptionUpdate(?string $arbId, ?string $arbStatus, array $payload)
    {
        if (!$arbId || !$arbStatus) {
            return response('Event received', 200);
        }

        $subscription = Subscription::where('arb_subscription_id', $arbId)->first();
        if (!$subscription) {
            return response('Event received', 200);
        }

        if ($arbStatus === 'active' && $subscription->status === 'past_due') {
            $subscription->update([
                'status'               => 'active',
                'failed_payment_count' => 0,
                'first_failed_at'      => null,
                'grace_period_ends_at' => null,
            ]);

            Log::info('Subscription recovered: past_due → active via subscription.updated', [
                'subscription_id' => $subscription->id,
                'email'           => $subscription->email,
            ]);

            SubscriptionEvent::create([
                'subscription_id' => $subscription->id,
                'event_type'      => 'payment_recovered',
                'payload'         => $payload,
                'note'            => 'Auth.net subscription.updated with status=active',
            ]);
        }

        return response('Event received', 200);
    }

    // ═════════════════════════════════════════════════════════════════════════
    // Shared GHL poster
    // ═════════════════════════════════════════════════════════════════════════
    private function fireGhl(?string $url, array $data, string $label, string $context)
    {
        if (!$url) {
            Log::info("GHL $label webhook URL not configured — skipping", ['context' => $context]);
            return;
        }

        try {
            Http::timeout(15)->post($url, $data);
            Log::info("GHL $label webhook fired", [
                'context' => $context,
                'email'   => $data['email'] ?? null,
            ]);
        } catch (\Throwable $e) {
            Log::error("GHL $label webhook failed", [
                'context' => $context,
                'error'   => $e->getMessage(),
            ]);
        }
    }
}