<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionEvent;
use App\Models\WebhookEvent;
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
    private const EVT_PAYMENT_REFUND   = 'net.authorize.payment.refund.created';
    private const EVT_PAYMENT_VOID     = 'net.authorize.payment.void.created';

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
        // ── Signature verification (log-only by default) ──────────────────────
        // Runs BEFORE json_decode so we hash the exact raw bytes Auth.net signed.
        // In log-only mode (config services.authorize_net.webhook_enforce_signature=false)
        // mismatches are logged but the request still processes normally. Flip the
        // toggle to true only after logs confirm real webhooks consistently match.
        $sigResult = $this->verifySignature($request);
        $enforceSig = (bool) config('services.authorize_net.webhook_enforce_signature', false);

        if ($sigResult === false) {
            Log::warning('Authorize.net webhook signature INVALID', [
                'ip'         => $request->ip(),
                'enforce'    => $enforceSig,
                'has_header' => $request->hasHeader('X-ANET-Signature'),
            ]);
            if ($enforceSig) {
                return response('Invalid signature', 401);
            }
        } elseif ($sigResult === true) {
            Log::info('Authorize.net webhook signature OK');
        }
        // $sigResult === null → signature key or header absent; already logged inside helper

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

        // ── Persist webhook_events row (audit + dashboard heartbeat) ─────────
        // Never throws — if the insert fails we still want to process the event.
        $webhookEvent = $this->persistWebhookEvent([
            'notification_id' => $notificationId,
            'event_type'      => (string) ($eventType ?? 'unknown'),
            'entity_id'       => $entityId ? (string) $entityId : null,
            'amount'          => is_numeric($amount) ? (float) $amount : null,
            'invoice_number'  => $invoiceNumber,
            'arb_status'      => $arbStatus,
            'signature_valid' => $sigResult,
            'source_ip'       => $request->ip(),
            'received_at'     => now(),
            'payload'         => $payload,
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
            return $this->handlePaymentSuccess($invoiceNumber, $entityId, $amount, $payload, $webhookEvent);
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
        // ROUTE: Refund / Void → persist negative-effect payment row
        // ═════════════════════════════════════════════════════════════════════
        if ($eventType === self::EVT_PAYMENT_REFUND || $eventType === self::EVT_PAYMENT_VOID) {
            return $this->handleRefundOrVoid($eventType, $entityId, $amount, $invoiceNumber, $payload, $webhookEvent);
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
    private function handlePaymentSuccess(?string $invoiceNumber, ?string $transactionId, $amount, array $payload, ?WebhookEvent $webhookEvent = null)
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
            // Persist the initial payment too (best-effort).
            $this->persistPayment([
                'subscription_id' => Subscription::where('invoice_number', $invoiceNumber)->value('id'),
                'transaction_id'  => $transactionId,
                'invoice_number'  => $invoiceNumber,
                'amount'          => (float) ($amount ?? 0),
                'type'            => 'initial',
                'status'          => 'captured',
                'event_type_raw'  => self::EVT_PAYMENT_SUCCESS,
                'charged_at'      => now(),
                'raw_payload'     => $payload,
            ], $webhookEvent);

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

            // Still persist the payment (unlinked) so dashboard totals are accurate.
            $this->persistPayment([
                'subscription_id' => null,
                'transaction_id'  => $transactionId,
                'invoice_number'  => $invoiceNumber,
                'amount'          => (float) ($amount ?? 0),
                'type'            => 'recurring',
                'status'          => 'captured',
                'event_type_raw'  => self::EVT_PAYMENT_SUCCESS,
                'charged_at'      => now(),
                'raw_payload'     => $payload,
            ], $webhookEvent);

            return response('Event received', 200);
        }

        // Persist the recurring payment.
        $this->persistPayment([
            'subscription_id' => $subscription->id,
            'transaction_id'  => $transactionId,
            'invoice_number'  => $invoiceNumber,
            'amount'          => (float) ($amount ?? 0),
            'type'            => 'recurring',
            'status'          => 'captured',
            'event_type_raw'  => self::EVT_PAYMENT_SUCCESS,
            'charged_at'      => now(),
            'raw_payload'     => $payload,
        ], $webhookEvent);

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
                config('services.ghl.subscription_recovered_webhook_url'),
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
            config('services.ghl.subscription_terminated_webhook_url'),
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
    //
    // Returns true on success, false on failure, null when URL was not configured.
    // Logs response status/body preview so silent failures are visible.
    // ═════════════════════════════════════════════════════════════════════════
    private function fireGhl(?string $url, array $data, string $label, string $context)
    {
        if (!$url) {
            Log::info("GHL $label webhook URL not configured — skipping", ['context' => $context]);
            return null;
        }

        try {
            $response = Http::timeout(15)->post($url, $data);
            $ok = $response->successful();

            $logCtx = [
                'context'      => $context,
                'email'        => $data['email'] ?? null,
                'status'       => $response->status(),
                'body_preview' => substr((string) $response->body(), 0, 500),
            ];

            if ($ok) {
                Log::info("GHL $label webhook fired", $logCtx);
                return true;
            }

            Log::error("GHL $label webhook returned non-success status", $logCtx);
            return false;
        } catch (\Throwable $e) {
            Log::error("GHL $label webhook failed", [
                'context'   => $context,
                'email'     => $data['email'] ?? null,
                'error'     => $e->getMessage(),
                'exception' => get_class($e),
            ]);
            return false;
        }
    }

    // ═════════════════════════════════════════════════════════════════════════
    // HANDLER: Refund or Void → persist a negative-effect payment row
    //
    // Auth.net events:
    //   net.authorize.payment.refund.created  — merchant issued a refund
    //   net.authorize.payment.void.created    — merchant voided an auth before settlement
    //
    // We store the row with type=refund|void, status=refunded|voided and a POSITIVE
    // amount. Downstream revenue aggregates (Payment::signedAmount,
    // Subscription::lifetimeRevenue) treat refund/void as negative.
    //
    // Subscription status is NOT automatically flipped here — a refund does not
    // necessarily end the subscription, and a void usually happens before a sub
    // even exists. If the refund terminates service, Auth.net will also send a
    // subscription.cancelled event which is handled separately.
    // ═════════════════════════════════════════════════════════════════════════
    private function handleRefundOrVoid(string $eventType, ?string $transactionId, $amount, ?string $invoiceNumber, array $payload, ?WebhookEvent $webhookEvent = null)
    {
        $type   = $eventType === self::EVT_PAYMENT_REFUND ? 'refund' : 'void';
        $status = $eventType === self::EVT_PAYMENT_REFUND ? 'refunded' : 'voided';

        $subscription = null;
        if ($invoiceNumber) {
            $subscription = Subscription::where('invoice_number', $invoiceNumber)->first();
        }
        if (!$subscription && $transactionId) {
            // Try to match back through the original captured payment's transaction
            $priorPayment = Payment::where('transaction_id', $transactionId)
                ->whereIn('type', ['initial', 'recurring'])
                ->first();
            if ($priorPayment) {
                $subscription = $priorPayment->subscription;
            }
        }

        $this->persistPayment([
            'subscription_id' => optional($subscription)->id,
            'transaction_id'  => $transactionId,
            'invoice_number'  => $invoiceNumber,
            'amount'          => abs((float) ($amount ?? 0)),
            'type'            => $type,
            'status'          => $status,
            'event_type_raw'  => $eventType,
            'charged_at'      => now(),
            'raw_payload'     => $payload,
        ], $webhookEvent);

        if ($subscription) {
            SubscriptionEvent::create([
                'subscription_id' => $subscription->id,
                'event_type'      => 'manual_note',
                'payload'         => $payload,
                'note'            => sprintf(
                    '%s recorded. Txn %s, Amount $%s',
                    ucfirst($type),
                    $transactionId ?? 'unknown',
                    $amount ?? '0'
                ),
            ]);
        }

        Log::info('Auth.net ' . $type . ' event recorded', [
            'event_type'     => $eventType,
            'transaction_id' => $transactionId,
            'invoice_number' => $invoiceNumber,
            'amount'         => $amount,
            'subscription_id'=> optional($subscription)->id,
        ]);

        return response('Event received', 200);
    }

    // ═════════════════════════════════════════════════════════════════════════
    // Persist a row in webhook_events. Best-effort — never throws.
    // ═════════════════════════════════════════════════════════════════════════
    private function persistWebhookEvent(array $attrs): ?WebhookEvent
    {
        try {
            // Resolve matched_subscription_id if we can
            $matchedId = null;
            if (!empty($attrs['entity_id'])) {
                // Most failure/termination events reference the ARB sub id
                $matchedId = Subscription::where('arb_subscription_id', $attrs['entity_id'])->value('id');
            }
            if (!$matchedId && !empty($attrs['invoice_number'])) {
                $matchedId = Subscription::where('invoice_number', $attrs['invoice_number'])->value('id');
            }
            $attrs['matched_subscription_id'] = $matchedId;

            // Idempotent insert on notification_id when present; without notification_id,
            // we always insert (forged / test traffic).
            if (!empty($attrs['notification_id'])) {
                return WebhookEvent::updateOrCreate(
                    ['notification_id' => $attrs['notification_id']],
                    $attrs
                );
            }
            return WebhookEvent::create($attrs);
        } catch (\Throwable $e) {
            Log::error('Failed to persist webhook_events row', [
                'error'           => $e->getMessage(),
                'event_type'      => $attrs['event_type'] ?? null,
                'notification_id' => $attrs['notification_id'] ?? null,
            ]);
            return null;
        }
    }

    // ═════════════════════════════════════════════════════════════════════════
    // Persist a row in payments. Best-effort — never throws, idempotent on
    // (transaction_id + type) so duplicate webhooks don't double-book revenue.
    // ═════════════════════════════════════════════════════════════════════════
    private function persistPayment(array $attrs, ?WebhookEvent $webhookEvent = null): ?Payment
    {
        try {
            if (!empty($attrs['transaction_id'])) {
                return Payment::updateOrCreate(
                    [
                        'transaction_id' => $attrs['transaction_id'],
                        'type'           => $attrs['type'],
                    ],
                    $attrs
                );
            }
            return Payment::create($attrs);
        } catch (\Throwable $e) {
            Log::error('Failed to persist payments row', [
                'error'          => $e->getMessage(),
                'transaction_id' => $attrs['transaction_id'] ?? null,
                'type'           => $attrs['type'] ?? null,
            ]);
            return null;
        }
    }

    // ═════════════════════════════════════════════════════════════════════════
    // Authorize.Net webhook HMAC-SHA512 signature verification
    //
    // Auth.net sends header:   X-ANET-Signature: sha512=<UPPERCASE_HEX>
    // The expected digest is HMAC-SHA512 over the raw request body, keyed by
    // the Signature Key from the merchant portal.
    //
    // Returns:
    //   true   — header present, signature matches
    //   false  — header present, signature does NOT match (caller decides whether to block)
    //   null   — signature key not configured OR header missing (cannot verify)
    // ═════════════════════════════════════════════════════════════════════════
    private function verifySignature(Request $request): ?bool
    {
        $signatureKey = (string) config('services.authorize_net.signature_key', '');
        if ($signatureKey === '') {
            Log::warning('Authorize.net signature key not configured — skipping signature verification');
            return null;
        }

        $header = (string) $request->header('X-ANET-Signature', '');
        if ($header === '') {
            Log::warning('Authorize.net webhook missing X-ANET-Signature header', [
                'ip' => $request->ip(),
            ]);
            return null;
        }

        // Header format: sha512=HEXDIGEST
        $parts = explode('=', $header, 2);
        if (count($parts) !== 2 || strtolower($parts[0]) !== 'sha512' || $parts[1] === '') {
            Log::warning('Authorize.net signature header malformed', [
                'header_prefix' => substr($header, 0, 16),
            ]);
            return false;
        }

        $provided = strtoupper($parts[1]);
        $expected = strtoupper(hash_hmac('sha512', (string) $request->getContent(), $signatureKey));

        return hash_equals($expected, $provided);
    }
}