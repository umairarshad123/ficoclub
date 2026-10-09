<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SiteMaintenance;
use App\Models\CheckoutOrder;
use App\Services\CommasService;
use App\Services\EnrollmentFulfillment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Commas embedded checkout.
 *
 *   GET  /accept-checkout?plan=…        branded page: contact details + agreements
 *   POST /checkout/order                 validate, create CheckoutOrder + Commas session → iframe config
 *        (customer pays inside the Commas iframe — card data never touches this server)
 *   POST /checkout/confirm               browser reports checkout:success → verify via API → fulfill
 *   GET  /checkout/status/{uuid}         polled by the completion page while we wait for confirmation
 *   GET  /checkout/complete/{uuid}       success redirect → hands off to /onboardingform
 *
 * The browser is never trusted on its own: a payment counts only once the Commas
 * API (or a signed webhook) confirms the right product and amount.
 */
class CommasCheckoutController extends Controller
{
    public function __construct(
        private CommasService $commas,
        private EnrollmentFulfillment $fulfillment,
    ) {
    }

    public function show()
    {
        Log::info('Commas checkout page opened', [
            'ip'            => request()->ip(),
            'plan'          => request()->query('plan'),
            'referral_code' => session('referral_code'),
        ]);

        return view('payments.commas-checkout', [
            'commasReady' => $this->commas->isConfigured() && config('services.commas.creator_slug'),
        ]);
    }

    public function createOrder(Request $request)
    {
        $plans = config('plans.plans');

        $validated = $request->validate([
            'first_name'       => 'required|string|max:100',
            'last_name'        => 'required|string|max:100',
            'email'            => 'required|email|max:150',
            'phone'            => 'required|string|max:30',
            'address'          => 'required|string|max:255',
            'city'             => 'required|string|max:100',
            'state'            => 'required|string|max:10',
            'zip'              => 'required|string|max:20',
            'selected_plan'    => 'required|string|in:' . implode(',', array_keys($plans)),
            'agree_terms'      => 'accepted',
            'agree_privacy'    => 'accepted',
            'marketing_opt_in' => 'nullable|boolean',
            'referral_code'    => 'nullable|string|max:50',
        ]);

        $plan = $plans[$validated['selected_plan']];

        if (! empty($plan['hidden']) && ! SiteMaintenance::hasPreviewAccess($request)) {
            return response()->json([
                'success' => false,
                'message' => 'This plan is not available.',
            ], 422);
        }

        if (! $this->commas->isConfigured() || empty($plan['commas_product_id']) || ! config('services.commas.creator_slug')) {
            Log::error('[Commas] Checkout not configured', [
                'plan'        => $plan['key'],
                'api_key_set' => $this->commas->isConfigured(),
                'product_set' => ! empty($plan['commas_product_id']),
                'slug_set'    => (bool) config('services.commas.creator_slug'),
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Secure checkout is temporarily unavailable. Please try again shortly.',
            ], 503);
        }

        // Referral: form input → session (set by ReferralMiddleware from ?ref= / cookie)
        $referralCode = strtoupper(trim($validated['referral_code'] ?? session('referral_code', '') ?? '')) ?: null;

        $order = CheckoutOrder::create([
            'plan_key'          => $plan['key'],
            'plan_label'        => $plan['label'],
            'amount'            => $plan['amount'],
            'commas_product_id' => $plan['commas_product_id'],
            'first_name'        => $validated['first_name'],
            'last_name'         => $validated['last_name'],
            'email'             => strtolower(trim($validated['email'])),
            'phone'             => $validated['phone'],
            'address'           => $validated['address'],
            'city'              => $validated['city'],
            'state'             => $validated['state'],
            'zip'               => $validated['zip'],
            'referral_code'     => $referralCode,
            'marketing_opt_in'  => (bool) ($validated['marketing_opt_in'] ?? false),
            'agreed_terms_at'   => now(),
            'ip_address'        => $request->ip(),
            'user_agent'        => $request->userAgent(),
        ]);

        try {
            $session = $this->commas->createEmbeddedSession([
                'order_uuid' => $order->uuid,
                'invoice'    => $order->invoice_number,
                'plan_key'   => $order->plan_key,
            ]);
        } catch (\Throwable $e) {
            Log::error('[Commas] Could not create embedded session', [
                'order' => $order->uuid,
                'error' => $e->getMessage(),
            ]);
            $order->delete();

            return response()->json([
                'success' => false,
                'message' => "We couldn't start secure checkout. Please try again in a moment.",
            ], 502);
        }

        $order->update(['commas_session_id' => $session['id'] ?: null]);
        session(['commas_order_uuid' => $order->uuid]);

        Log::info('[Commas] Checkout order created', [
            'order'         => $order->uuid,
            'invoice'       => $order->invoice_number,
            'plan'          => $order->plan_key,
            'referral_code' => $referralCode,
        ]);

        return response()->json([
            'success'     => true,
            'order'       => $order->uuid,
            'checkout'    => [
                'creatorId'             => (string) config('services.commas.creator_slug'),
                'productId'             => (string) $plan['commas_product_id'],
                'checkoutSessionSecret' => $session['secret'],
                'environment'           => $this->commas->environment(),
                'metadata'              => ['order_uuid' => $order->uuid],
            ],
            'success_url' => route('checkout.complete', $order->uuid),
            'prefill'     => [
                'email'      => $order->email,
                'first_name' => $order->first_name,
                'last_name'  => $order->last_name,
                'phone'      => $this->e164($order->phone),
                'address'    => [
                    'country'     => 'US',
                    'line1'       => $order->address,
                    'city'        => $order->city,
                    'state'       => $order->state,
                    'postal_code' => $order->zip,
                ],
            ],
        ]);
    }

    public function confirm(Request $request)
    {
        $validated = $request->validate([
            'order'          => 'required|uuid',
            'transaction_id' => 'nullable|string|max:120',
        ]);

        $order = CheckoutOrder::where('uuid', $validated['order'])->firstOrFail();

        if ($order->status === CheckoutOrder::STATUS_PENDING && ! empty($validated['transaction_id']) && ! $order->client_transaction_ref) {
            $order->update(['client_transaction_ref' => $validated['transaction_id']]);
        }

        $this->verifyFromBrowser($order);

        return response()->json($this->statusPayload($order));
    }

    public function status(string $uuid)
    {
        $order = CheckoutOrder::where('uuid', $uuid)->firstOrFail();

        // Re-try API verification at most every 5s per order while we wait on the webhook.
        if ($order->status === CheckoutOrder::STATUS_PENDING
            && $order->client_transaction_ref
            && Cache::add('commas_verify_' . $order->id, true, 5)) {
            $this->verifyFromBrowser($order);
        }

        // Still waiting after 20s → look the payment up in Commas' transaction list ourselves
        // (at most every 15s), so a lost webhook never depends on the cron job.
        if ($order->refresh()->status === CheckoutOrder::STATUS_PENDING
            && $order->created_at->lt(now()->subSeconds(20))
            && Cache::add('commas_reconcile_' . $order->id, true, 15)) {
            try {
                $this->fulfillment->reconcileOrder($order);
            } catch (\Throwable $e) {
                Log::warning('[Commas] Order lookup from status poll failed', ['order' => $order->uuid, 'error' => $e->getMessage()]);
            }
        }

        return response()->json($this->statusPayload($order));
    }

    public function complete(string $uuid)
    {
        $order = CheckoutOrder::where('uuid', $uuid)->firstOrFail();

        if ($order->isPaid()) {
            $this->handOffToOnboarding($order);
            return redirect('/onboardingform');
        }

        return view('payments.commas-complete', [
            'order'     => $order,
            'statusUrl' => route('checkout.status', $order->uuid),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Look up the transaction id the SDK reported and fulfill only if Commas
     * confirms it: same buyer email (locked in the iframe), paid after this order
     * was created. Product + amount are checked inside markPaid().
     */
    private function verifyFromBrowser(CheckoutOrder $order): void
    {
        if ($order->status !== CheckoutOrder::STATUS_PENDING || ! $order->client_transaction_ref) {
            return;
        }

        try {
            $txn = $this->commas->getTransaction($order->client_transaction_ref);
        } catch (\Throwable $e) {
            Log::warning('[Commas] Browser-reported transaction lookup failed — waiting for webhook', [
                'order' => $order->uuid,
                'error' => $e->getMessage(),
            ]);
            return;
        }

        if (! $txn) {
            Log::info('[Commas] Browser-reported transaction not found yet — waiting for webhook', ['order' => $order->uuid]);
            return;
        }

        $email    = strtolower((string) data_get($txn, 'fan.email', ''));
        $paidAt   = data_get($txn, 'transaction_date');
        $tooEarly = $paidAt && strtotime($paidAt) < $order->created_at->copy()->subMinutes(5)->getTimestamp();

        if ($email !== strtolower($order->email) || $tooEarly) {
            Log::warning('[Commas] Browser-reported transaction does not belong to this order — ignoring', [
                'order'     => $order->uuid,
                'txn_email' => $email,
                'paid_at'   => $paidAt,
            ]);
            return;
        }

        $fulfilled = $this->fulfillment->markPaid($order, [
            'product_id'     => data_get($txn, 'product.id') ?? data_get($txn, 'service.id'),
            'amount'         => data_get($txn, 'amount'),
            'email'          => $email,
            'transaction_id' => (string) data_get($txn, 'id'),
            'buyer_id'       => data_get($txn, 'fan.id'),
            'raw'            => ['source' => 'browser_confirm', 'transaction' => $txn],
        ], 'browser');

        if ($fulfilled) {
            // GHL + Meta take seconds — run them after the customer gets their response.
            $this->afterResponse(fn () => $this->fulfillment->notify($order->fresh()));
        }
    }

    private function statusPayload(CheckoutOrder $order): array
    {
        $order->refresh();

        return [
            'status'   => $order->status,
            'redirect' => $order->isPaid() ? route('checkout.complete', $order->uuid) : null,
        ];
    }

    /** Same session + cache keys the Authorize.Net flow set, so onboarding works unchanged. */
    private function handOffToOnboarding(CheckoutOrder $order): void
    {
        $ref = $order->commas_payment_id ?? $order->commas_transaction_id ?? $order->client_transaction_ref;

        session([
            'acceptjs_payment_success' => true,
            'acceptjs_invoice_number'  => $order->invoice_number,
            'acceptjs_transaction_id'  => $ref,
            'acceptjs_auth_code'       => null,
            'acceptjs_customer'        => array_intersect_key($order->customerPayload(), array_flip([
                'first_name', 'last_name', 'email', 'phone', 'address', 'city', 'state', 'zip',
            ])),
        ]);

        // Re-warm in case the 120-minute entry from fulfillment expired.
        Cache::put('checkout_customer_' . $order->invoice_number, $order->customerPayload(), now()->addMinutes(120));
    }

    /** US numbers → +1XXXXXXXXXX for the SDK prefill; anything we can't interpret → null (not prefilled). */
    private function e164(string $phone): ?string
    {
        $digits = preg_replace('/\D/', '', $phone);
        if (strlen($digits) === 10) {
            return '+1' . $digits;
        }
        if (strlen($digits) === 11 && $digits[0] === '1') {
            return '+' . $digits;
        }
        return null;
    }
}
