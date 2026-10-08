<?php

namespace App\Http\Controllers;

use App\Models\CheckoutOrder;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionEvent;
use App\Models\WebhookEvent;
use App\Services\CommasService;
use App\Services\EnrollmentFulfillment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Commas webhook receiver — POST /webhooks/commas
 *
 * Commas delivers each event AT MOST ONCE and never retries, and expects a 200
 * within a few seconds. So: verify the HMAC signature, persist the event, answer
 * 200 immediately, and do the real work after the response is sent. Anything
 * this endpoint misses is picked up by `php artisan commas:reconcile`.
 *
 * Envelope: { id: <uuid>, type: <event>, data: {...}, created_at }
 */
class CommasWebhookController extends Controller
{
    public function __construct(private EnrollmentFulfillment $fulfillment)
    {
    }

    public function handle(Request $request)
    {
        $raw    = (string) $request->getContent();
        $secret = (string) config('services.commas.webhook_secret', '');

        if ($secret === '') {
            Log::error('[Commas] Webhook received but COMMAS_WEBHOOK_SECRET is not set — rejecting');
            return response('Webhook secret not configured', 503);
        }

        $sigValid = CommasService::signatureIsValid($raw, (string) $request->header('x-webhook-signature', ''), $secret);
        $payload  = json_decode($raw, true);
        $payload  = is_array($payload) ? $payload : [];

        $eventId = isset($payload['id']) ? (string) $payload['id'] : null;
        $type    = (string) ($payload['type'] ?? 'unknown');
        $data    = is_array($payload['data'] ?? null) ? $payload['data'] : [];

        if (! $sigValid) {
            Log::warning('[Commas] Webhook signature INVALID — rejected', [
                'ip'         => $request->ip(),
                'type'       => $type,
                'has_header' => $request->hasHeader('x-webhook-signature'),
            ]);
            // Recorded for the admin webhooks page, never processed.
            $this->persist(null, $type, $data, $payload, false, $request->ip(), processed: true);
            return response('Invalid signature', 401);
        }

        if ($eventId && WebhookEvent::where('notification_id', $eventId)->exists()) {
            Log::info('[Commas] Duplicate webhook delivery — skipping', ['event_id' => $eventId, 'type' => $type]);
            return response('Duplicate', 200);
        }

        $event = $this->persist($eventId, $type, $data, $payload, true, $request->ip());

        // Respond first, work after — the 200 goes out before this runs.
        $this->afterResponse(fn () => $this->process($event, $type, $data, $payload));

        return response('Event received', 200);
    }

    // ═════════════════════════════════════════════════════════════════════════

    public function process(?WebhookEvent $event, string $type, array $data, array $payload): void
    {
        try {
            $subscriptionId = match ($type) {
                'payment.succeeded'                  => $this->onPaymentSucceeded($data, $payload),
                'refund.created'                     => $this->onRefund($data, $payload),
                'dispute.created', 'dispute.updated' => $this->onDispute($type, $data, $payload),
                default                              => $this->informational($type, $data),
            };

            $event?->update(array_filter([
                'processed_at'            => now(),
                'matched_subscription_id' => $subscriptionId ?: null,
            ]));
        } catch (\Throwable $e) {
            Log::error('[Commas] Webhook processing failed', [
                'type'     => $type,
                'event_id' => $event?->notification_id,
                'error'    => $e->getMessage(),
                'file'     => $e->getFile() . ':' . $e->getLine(),
            ]);
        }
    }

    /** One-time checkout payment. Our orders carry order_uuid in the session metadata. */
    private function onPaymentSucceeded(array $data, array $payload): ?int
    {
        $orderUuid = data_get($data, 'api_metadata.data.order_uuid');
        $order     = $orderUuid ? CheckoutOrder::where('uuid', $orderUuid)->first() : null;

        if (! $order) {
            // A Commas sale that didn't start on this website (e.g. a GHL funnel or
            // payment link). Record it so revenue totals are complete; nothing else to do.
            $this->recordExternalPayment($data, $payload);
            return null;
        }

        $this->fulfillment->markPaid($order, [
            'product_id' => data_get($data, 'item.id') ?? data_get($data, 'productID'),
            'amount'     => data_get($data, 'amount'),
            'email'      => data_get($data, 'buyer.email'),
            'payment_id' => data_get($data, 'payment_id'),
            'buyer_id'   => data_get($data, 'buyer.id'),
            'raw'        => $payload,
        ], 'webhook');

        // Idempotent — also covers a browser-confirmed order whose notify never ran.
        $this->fulfillment->notify($order->refresh());

        return $order->subscription_id;
    }

    private function recordExternalPayment(array $data, array $payload): void
    {
        $paymentId = data_get($data, 'payment_id') ?? data_get($data, 'transaction_history_id');

        $attrs = [
            'provider'        => 'commas',
            'subscription_id' => null,
            'transaction_id'  => $paymentId,
            'amount'          => (float) data_get($data, 'amount', 0),
            'type'            => 'initial',
            'status'          => 'captured',
            'event_type_raw'  => 'payment.succeeded',
            'charged_at'      => now(),
            'raw_payload'     => $payload,
        ];

        $paymentId
            ? Payment::updateOrCreate(['transaction_id' => $paymentId, 'type' => 'initial'], $attrs)
            : Payment::create($attrs);

        Log::info('[Commas] Payment not from website checkout — recorded unlinked', [
            'payment_id' => $paymentId,
            'item'       => data_get($data, 'item.title'),
            'amount'     => data_get($data, 'amount'),
        ]);
    }

    private function onRefund(array $data, array $payload): ?int
    {
        $subscription = $this->subscriptionForBuyer(data_get($data, 'buyer.email'));
        $refundId     = (string) (data_get($data, 'refund_id') ?? data_get($data, 'refund_transaction_id') ?? '');

        Payment::updateOrCreate(
            ['transaction_id' => 'refund:' . $refundId, 'type' => 'refund'],
            [
                'provider'        => 'commas',
                'subscription_id' => $subscription?->id,
                'amount'          => abs((float) data_get($data, 'amount', 0)),
                'status'          => 'refunded',
                'event_type_raw'  => 'refund.created',
                'charged_at'      => now(),
                'raw_payload'     => $payload,
            ]
        );

        if ($subscription) {
            SubscriptionEvent::create([
                'subscription_id' => $subscription->id,
                'event_type'      => 'manual_note',
                'payload'         => $payload,
                'note'            => sprintf(
                    'Commas %s refund of $%s (%s)',
                    data_get($data, 'refund_type', ''),
                    data_get($data, 'amount', '0'),
                    data_get($data, 'reason', 'no reason given')
                ),
            ]);
        }

        return $subscription?->id;
    }

    private function onDispute(string $type, array $data, array $payload): ?int
    {
        $subscription = $this->subscriptionForBuyer(data_get($data, 'buyer.email'));

        Log::warning('[Commas] Dispute event', [
            'type'   => $type,
            'status' => data_get($data, 'status'),
            'reason' => data_get($data, 'reason'),
            'amount' => data_get($data, 'amount'),
            'buyer'  => data_get($data, 'buyer.email'),
            'due_by' => data_get($data, 'due_by'),
        ]);

        if ($subscription) {
            SubscriptionEvent::create([
                'subscription_id' => $subscription->id,
                'event_type'      => 'manual_note',
                'payload'         => $payload,
                'note'            => sprintf(
                    'Chargeback %s: status %s, reason %s, $%s (respond by %s)',
                    $type === 'dispute.created' ? 'opened' : 'updated',
                    data_get($data, 'status', '?'),
                    data_get($data, 'reason', '?'),
                    data_get($data, 'amount', '0'),
                    data_get($data, 'due_by', '?')
                ),
            ]);
        }

        return $subscription?->id;
    }

    private function informational(string $type, array $data): ?int
    {
        Log::info('[Commas] Informational webhook acknowledged', [
            'type'  => $type,
            'buyer' => data_get($data, 'buyer.email') ?? data_get($data, 'customer_id'),
        ]);

        return $this->subscriptionForBuyer(data_get($data, 'buyer.email'))?->id;
    }

    private function subscriptionForBuyer(?string $email): ?Subscription
    {
        if (! $email) {
            return null;
        }

        return Subscription::where('provider', 'commas')
            ->whereRaw('LOWER(email) = ?', [strtolower($email)])
            ->latest('id')
            ->first();
    }

    /** Best-effort audit row — never throws. */
    private function persist(?string $eventId, string $type, array $data, array $payload, bool $sigValid, ?string $ip, bool $processed = false): ?WebhookEvent
    {
        try {
            $buyerName = (string) data_get($data, 'buyer.name', '');
            [$first, $last] = array_pad(explode(' ', trim($buyerName), 2), 2, null);

            $orderUuid = data_get($data, 'api_metadata.data.order_uuid');
            $invoice   = $orderUuid ? CheckoutOrder::where('uuid', $orderUuid)->value('invoice_number') : null;

            $attrs = [
                'provider'            => 'commas',
                'event_type'          => $type,
                'entity_id'           => data_get($data, 'payment_id')
                                         ?? data_get($data, 'refund_id')
                                         ?? data_get($data, 'dispute_id')
                                         ?? data_get($data, 'subscription.id')
                                         ?? data_get($data, 'id'),
                'customer_first_name' => $first ?: null,
                'customer_last_name'  => $last,
                'customer_email'      => data_get($data, 'buyer.email'),
                'description'         => WebhookEvent::describeEvent($type, $payload, $first, $last),
                'amount'              => is_numeric(data_get($data, 'amount')) ? (float) data_get($data, 'amount') : null,
                'invoice_number'      => $invoice,
                'signature_valid'     => $sigValid,
                'source_ip'           => $ip,
                'received_at'         => now(),
                'payload'             => $payload,
                'processed_at'        => $processed ? now() : null,
            ];

            return $eventId
                ? WebhookEvent::firstOrCreate(['notification_id' => $eventId], $attrs)
                : WebhookEvent::create($attrs);
        } catch (\Throwable $e) {
            Log::error('[Commas] Failed to persist webhook_events row', [
                'error' => $e->getMessage(),
                'type'  => $type,
            ]);
            return null;
        }
    }
}
