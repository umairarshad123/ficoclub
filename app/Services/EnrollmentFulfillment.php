<?php

namespace App\Services;

use App\Models\CheckoutOrder;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Turns a paid Commas checkout into an enrollment — exactly once.
 *
 * Three paths can report the same payment (browser confirm, payment.succeeded
 * webhook, commas:reconcile) because Commas webhooks are at-most-once with no
 * retries. All of them call markPaid(); the row lock + status check means only
 * the first one creates the subscription/payment rows, and notify() guards
 * GHL + Meta with an atomic notified_at flip.
 */
class EnrollmentFulfillment
{
    /**
     * @param array{
     *   product_id?: ?string, amount: float|string|null, email?: ?string,
     *   transaction_id?: ?string, payment_id?: ?string, buyer_id?: ?string,
     *   raw?: array
     * } $payment
     * @param string $via browser | webhook | reconcile
     * @return bool true only for the call that actually fulfilled the order
     */
    public function markPaid(CheckoutOrder $order, array $payment, string $via): bool
    {
        $txnId     = isset($payment['transaction_id']) ? (string) $payment['transaction_id'] : null;
        $paymentId = isset($payment['payment_id']) ? (string) $payment['payment_id'] : null;

        if ($this->claimedByAnotherOrder($order, $txnId, $paymentId)) {
            Log::warning('[Commas] Payment already claimed by a different order — ignoring', [
                'order'          => $order->uuid,
                'transaction_id' => $txnId,
                'payment_id'     => $paymentId,
                'via'            => $via,
            ]);
            return false;
        }

        return DB::transaction(function () use ($order, $payment, $via, $txnId, $paymentId) {
            /** @var CheckoutOrder $locked */
            $locked = CheckoutOrder::whereKey($order->getKey())->lockForUpdate()->firstOrFail();

            if ($locked->status !== CheckoutOrder::STATUS_PENDING) {
                // Already handled by another path — just backfill identifiers it didn't have.
                $locked->fill(array_filter([
                    'commas_transaction_id' => $locked->commas_transaction_id ? null : $txnId,
                    'commas_payment_id'     => $locked->commas_payment_id ? null : $paymentId,
                ]))->save();
                $order->setRawAttributes($locked->getAttributes(), true);
                return false;
            }

            $paidAmount = round((float) ($payment['amount'] ?? 0), 2);
            $productId  = $payment['product_id'] ?? null;

            $locked->fill([
                'commas_transaction_id' => $txnId,
                'commas_payment_id'     => $paymentId,
                'commas_buyer_id'       => $payment['buyer_id'] ?? null,
                'paid_amount'           => $paidAmount,
                'paid_at'               => now(),
                'confirmed_via'         => $via,
            ]);

            $mismatch = $this->mismatchReason($locked, $productId, $paidAmount);
            if ($mismatch !== null) {
                $locked->status          = CheckoutOrder::STATUS_MISMATCH;
                $locked->mismatch_reason = $mismatch;
                $locked->save();
                $order->setRawAttributes($locked->getAttributes(), true);

                Log::error('[Commas] Paid checkout does not match its plan — NOT fulfilled, review in admin', [
                    'order'  => $locked->uuid,
                    'reason' => $mismatch,
                    'via'    => $via,
                ]);
                return false;
            }

            $ref = $paymentId ?? $txnId ?? $locked->client_transaction_ref;

            $subscription = Subscription::create([
                'provider'         => 'commas',
                'first_name'       => $locked->first_name,
                'last_name'        => $locked->last_name,
                'email'            => $locked->email,
                'phone'            => $locked->phone,
                'address'          => $locked->address,
                'city'             => $locked->city,
                'state'            => $locked->state,
                'zip'              => $locked->zip,
                'plan_key'         => $locked->plan_key,
                'plan_label'       => $locked->plan_label,
                'amount'           => $locked->amount,
                'recurring_amount' => null,
                'invoice_number'   => $locked->invoice_number,
                'transaction_id'   => $ref,
                'referral_code'    => $locked->referral_code,
                'status'           => 'active',
                'subscribed_at'    => now(),
                'next_billing_date'=> null,
            ]);

            Payment::create([
                'provider'        => 'commas',
                'subscription_id' => $subscription->id,
                'transaction_id'  => $ref,
                'invoice_number'  => $locked->invoice_number,
                'amount'          => $paidAmount,
                'type'            => 'initial',
                'status'          => 'captured',
                'event_type_raw'  => 'commas.' . $via,
                'charged_at'      => now(),
                'raw_payload'     => $payment['raw'] ?? null,
            ]);

            $locked->status          = CheckoutOrder::STATUS_PAID;
            $locked->subscription_id = $subscription->id;
            $locked->save();
            $order->setRawAttributes($locked->getAttributes(), true);

            // OnboardingFormController reads this cache entry (plan + prefill).
            Cache::put('checkout_customer_' . $locked->invoice_number, $locked->customerPayload(), now()->addMinutes(120));

            Log::info('[Commas] Order fulfilled', [
                'order'   => $locked->uuid,
                'invoice' => $locked->invoice_number,
                'plan'    => $locked->plan_key,
                'amount'  => $paidAmount,
                'via'     => $via,
            ]);

            return true;
        });
    }

    /**
     * Fire GHL + Meta CAPI for a paid order, at most once (atomic flag flip).
     * Slow (two outbound HTTP calls) — web callers should run it after the response.
     */
    public function notify(CheckoutOrder $order): void
    {
        if ($order->status !== CheckoutOrder::STATUS_PAID) {
            return;
        }

        $claimed = CheckoutOrder::whereKey($order->getKey())
            ->whereNull('notified_at')
            ->update(['notified_at' => now()]);

        if ($claimed !== 1) {
            return;
        }

        $ref = $order->commas_payment_id ?? $order->commas_transaction_id ?? $order->client_transaction_ref ?? $order->invoice_number;

        $this->fireGhlWebhook($order, $ref);
        $this->fireMetaCapi($order, $ref);
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function mismatchReason(CheckoutOrder $order, ?string $productId, float $paidAmount): ?string
    {
        if ($order->commas_product_id && $productId && $productId !== $order->commas_product_id) {
            return "Paid for product {$productId}, expected {$order->commas_product_id} ({$order->plan_key})";
        }

        if (abs($paidAmount - (float) $order->amount) > 0.009) {
            return sprintf('Paid $%.2f, expected $%.2f (%s)', $paidAmount, (float) $order->amount, $order->plan_key);
        }

        return null;
    }

    private function claimedByAnotherOrder(CheckoutOrder $order, ?string $txnId, ?string $paymentId): bool
    {
        if (! $txnId && ! $paymentId) {
            return false;
        }

        return CheckoutOrder::where('id', '!=', $order->id)
            ->where(function ($q) use ($txnId, $paymentId) {
                if ($txnId)     { $q->orWhere('commas_transaction_id', $txnId); }
                if ($paymentId) { $q->orWhere('commas_payment_id', $paymentId); }
            })
            ->exists();
    }

    // ── GHL — same payloads/URLs the Authorize.Net checkout always sent ─────
    private function fireGhlWebhook(CheckoutOrder $order, string $transId): void
    {
        $referralCode = $order->referral_code;

        $payload = [
            'first_name'     => $order->first_name,
            'last_name'      => $order->last_name,
            'email'          => $order->email,
            'phone'          => $order->phone,
            'address'        => $order->address,
            'city'           => $order->city,
            'state'          => $order->state,
            'zip'            => $order->zip,
            'plan'           => $order->plan_label,
            'amount'         => number_format((float) $order->amount, 2, '.', ''),
            'invoice_number' => $order->invoice_number,
            'transaction_id' => $transId,
        ];

        if ($referralCode) {
            $url = config('services.ghl.referral_webhook_url');
            $payload += [
                'plan_key'      => $order->plan_key,
                'referral_code' => $referralCode,
                'source'        => 'referral_partner',
                'tags'          => [
                    'referral-partner',
                    'partner-' . strtolower(str_replace('PARTNER-', '', $referralCode)),
                ],
            ];
        } else {
            $url = config('services.ghl.checkout_webhook_url');
            $payload['source'] = '850_fico_checkout';
        }

        if (! $url) {
            Log::warning('[Commas] GHL webhook URL not set in .env', [
                'invoice'       => $order->invoice_number,
                'referral_code' => $referralCode,
            ]);
            return;
        }

        try {
            $response = Http::timeout(15)->post($url, $payload);
            Log::info('[Commas] GHL webhook fired', [
                'invoice'       => $order->invoice_number,
                'referral_code' => $referralCode,
                'status'        => $response->status(),
            ]);
        } catch (\Throwable $e) {
            Log::error('[Commas] GHL webhook failed', [
                'invoice' => $order->invoice_number,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    // ── Meta Conversions API — server-side Purchase event ───────────────────
    private function fireMetaCapi(CheckoutOrder $order, string $transId): void
    {
        $pixelId   = config('services.meta.pixel_id');
        $capiToken = config('services.meta.capi_token');

        if (! $pixelId || ! $capiToken) {
            Log::warning('[Commas] Meta CAPI skipped — META_PIXEL_ID or META_CAPI_TOKEN not set', [
                'invoice' => $order->invoice_number,
            ]);
            return;
        }

        $hash = fn (?string $v) => hash('sha256', strtolower(trim((string) $v)));

        $event = [
            'data' => [[
                'event_name'    => 'Purchase',
                'event_time'    => time(),
                'action_source' => 'website',
                'event_id'      => 'purchase_' . $transId, // dedup key
                'user_data'     => [
                    'em'                => [$hash($order->email)],
                    'ph'                => [hash('sha256', preg_replace('/\D/', '', (string) $order->phone))],
                    'fn'                => [$hash($order->first_name)],
                    'ln'                => [$hash($order->last_name)],
                    'zp'                => [hash('sha256', trim((string) $order->zip))],
                    'ct'                => [$hash($order->city)],
                    'st'                => [$hash($order->state)],
                    'client_ip_address' => $order->ip_address,
                    'client_user_agent' => $order->user_agent ?? '',
                ],
                'custom_data'   => [
                    'currency' => 'USD',
                    'value'    => number_format((float) $order->amount, 2, '.', ''),
                    'order_id' => $order->invoice_number,
                ],
            ]],
        ];

        try {
            $response = Http::timeout(10)->post(
                "https://graph.facebook.com/v19.0/{$pixelId}/events?access_token={$capiToken}",
                $event
            );
            Log::info('[Commas] Meta CAPI response', [
                'invoice' => $order->invoice_number,
                'status'  => $response->status(),
            ]);
        } catch (\Throwable $e) {
            Log::error('[Commas] Meta CAPI request failed', [
                'invoice' => $order->invoice_number,
                'error'   => $e->getMessage(),
            ]);
        }
    }
}
