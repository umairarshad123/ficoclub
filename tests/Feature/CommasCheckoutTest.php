<?php

namespace Tests\Feature;

use App\Models\CheckoutOrder;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\WebhookEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CommasCheckoutTest extends TestCase
{
    use RefreshDatabase;

    private const SECRET  = 'whsk_test_secret';
    private const PRODUCT = 'GoLd1';

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'payments.provider'                   => 'commas',
            'services.commas.api_key'             => 'test-key',
            'services.commas.environment'         => 'sandbox',
            'services.commas.creator_slug'        => '850ficoclub',
            'services.commas.webhook_secret'      => self::SECRET,
            'plans.plans.gold.commas_product_id'  => self::PRODUCT,
            'services.ghl.checkout_webhook_url'   => 'https://ghl.test/checkout',
            'services.ghl.referral_webhook_url'   => 'https://ghl.test/referral',
            'services.meta.pixel_id'              => '123',
            'services.meta.capi_token'            => 'tok',
        ]);

        Http::fake([
            'api-sandbox.commas.net/public-api/checkout-sessions/embedded' => Http::response([
                'status' => 'success',
                'data'   => ['id' => 'sess_1', 'checkout_session_secret' => 'secret-uuid'],
            ]),
            'ghl.test/*'          => Http::response('ok'),
            'graph.facebook.com/*' => Http::response(['events_received' => 1]),
        ]);
    }

    // ── helpers ─────────────────────────────────────────────────────────────

    private function orderPayload(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'Jane', 'last_name' => 'Doe',
            'email' => 'Jane@Example.com', 'phone' => '(313) 555-0100',
            'address' => '1 Main St', 'city' => 'Detroit', 'state' => 'MI', 'zip' => '48201',
            'selected_plan' => 'gold',
            'agree_terms' => 1, 'agree_privacy' => 1, 'marketing_opt_in' => 0,
        ], $overrides);
    }

    private function createOrder(array $overrides = []): CheckoutOrder
    {
        $this->postJson('/checkout/order', $this->orderPayload($overrides))->assertOk();

        return CheckoutOrder::latest('id')->firstOrFail();
    }

    private function webhook(array $envelope, ?string $secret = self::SECRET)
    {
        $raw = json_encode($envelope);

        return $this->call('POST', '/webhooks/commas', [], [], [], [
            'CONTENT_TYPE'             => 'application/json',
            'HTTP_X_WEBHOOK_SIGNATURE' => hash_hmac('sha256', $raw, (string) $secret),
        ], $raw);
    }

    private function paymentSucceeded(CheckoutOrder $order, array $data = []): array
    {
        return [
            'id'         => 'evt-' . uniqid(),
            'type'       => 'payment.succeeded',
            'created_at' => now()->toIso8601String(),
            'data'       => array_merge([
                'payment_id'   => 'ORD-AAAA-BBBB-CCCC',
                'amount'       => 897.00,
                'status'       => 'succeeded',
                'payment_type' => 'onetime',
                'buyer'        => ['id' => 'user_abc', 'name' => 'Jane Doe', 'email' => 'jane@example.com'],
                'item'         => ['id' => self::PRODUCT, 'title' => 'Gold Plan', 'type' => 'onetime'],
                'api_metadata' => ['data' => ['order_uuid' => $order->uuid]],
            ], $data),
        ];
    }

    private function ghlCalls(): int
    {
        return count(Http::recorded(fn (HttpRequest $r) => str_starts_with($r->url(), 'https://ghl.test/')));
    }

    // ── order creation ──────────────────────────────────────────────────────

    public function test_order_creation_returns_embedded_config_and_tags_the_session(): void
    {
        $res = $this->postJson('/checkout/order', $this->orderPayload(['referral_code' => 'dl']))->assertOk();

        $order = CheckoutOrder::firstOrFail();
        $this->assertSame('gold', $order->plan_key);
        $this->assertSame('897.00', $order->amount);
        $this->assertSame('jane@example.com', $order->email);
        $this->assertSame('DL', $order->referral_code);
        $this->assertNotNull($order->agreed_terms_at);
        $this->assertStringStartsWith('INV-', $order->invoice_number);

        $res->assertJsonPath('order', $order->uuid)
            ->assertJsonPath('checkout.productId', self::PRODUCT)
            ->assertJsonPath('checkout.creatorId', '850ficoclub')
            ->assertJsonPath('checkout.checkoutSessionSecret', 'secret-uuid')
            ->assertJsonPath('checkout.environment', 'sandbox')
            ->assertJsonPath('prefill.phone', '+13135550100');

        Http::assertSent(fn (HttpRequest $r) => str_contains($r->url(), '/checkout-sessions/embedded')
            && $r->header('x-api-key')[0] === 'test-key'
            && $r['metadata']['order_uuid'] === $order->uuid);
    }

    public function test_order_requires_agreements(): void
    {
        $this->postJson('/checkout/order', $this->orderPayload(['agree_terms' => 0]))
            ->assertStatus(422)->assertJsonValidationErrors('agree_terms');

        $this->assertSame(0, CheckoutOrder::count());
    }

    public function test_order_refused_when_commas_not_configured(): void
    {
        config(['plans.plans.gold.commas_product_id' => null]);

        $this->postJson('/checkout/order', $this->orderPayload())->assertStatus(503);
        $this->assertSame(0, CheckoutOrder::count());
    }

    // ── webhook path ────────────────────────────────────────────────────────

    public function test_webhook_payment_fulfills_order_once_and_fires_ghl_once(): void
    {
        $order = $this->createOrder();
        $event = $this->paymentSucceeded($order);

        $this->webhook($event)->assertOk();
        $this->webhook($event)->assertOk()->assertSee('Duplicate');           // same envelope id
        $this->webhook(array_merge($event, ['id' => 'evt-other']))->assertOk(); // same payment, new id

        $order->refresh();
        $this->assertSame('paid', $order->status);
        $this->assertSame('webhook', $order->confirmed_via);
        $this->assertSame('ORD-AAAA-BBBB-CCCC', $order->commas_payment_id);
        $this->assertNotNull($order->notified_at);

        $this->assertSame(1, Subscription::where('provider', 'commas')->count());
        $this->assertSame(1, Payment::where('provider', 'commas')->where('type', 'initial')->count());
        $this->assertSame('897.00', Payment::first()->amount);
        $this->assertSame(1, $this->ghlCalls());

        Http::assertSent(fn (HttpRequest $r) => $r->url() === 'https://ghl.test/checkout'
            && $r['invoice_number'] === $order->invoice_number
            && $r['source'] === '850_fico_checkout'
            && $r['plan'] === 'Gold Plan'
            && $r['amount'] === '897.00');

        $this->assertSame(2, WebhookEvent::where('provider', 'commas')->count());
        $this->assertNotNull(WebhookEvent::first()->processed_at);
    }

    public function test_referral_order_goes_to_referral_ghl_webhook(): void
    {
        $order = $this->createOrder(['referral_code' => 'EL']);
        $this->webhook($this->paymentSucceeded($order))->assertOk();

        Http::assertSent(fn (HttpRequest $r) => $r->url() === 'https://ghl.test/referral'
            && $r['referral_code'] === 'EL'
            && $r['tags'] === ['referral-partner', 'partner-el']);
        Http::assertNotSent(fn (HttpRequest $r) => $r->url() === 'https://ghl.test/checkout');
    }

    public function test_invalid_signature_is_rejected_and_not_processed(): void
    {
        $order = $this->createOrder();

        $this->webhook($this->paymentSucceeded($order), 'wrong-secret')->assertStatus(401);

        $this->assertSame('pending', $order->refresh()->status);
        $this->assertSame(0, Subscription::count());
        $this->assertFalse((bool) WebhookEvent::first()->signature_valid);
    }

    public function test_tampered_product_or_amount_is_not_fulfilled(): void
    {
        $order = $this->createOrder();

        $this->webhook($this->paymentSucceeded($order, [
            'amount' => 5.00,
            'item'   => ['id' => 'Cheap1', 'title' => 'Something cheap', 'type' => 'onetime'],
        ]))->assertOk();

        $order->refresh();
        $this->assertSame('mismatch', $order->status);
        $this->assertStringContainsString('Cheap1', $order->mismatch_reason);
        $this->assertSame(0, Subscription::count());
        $this->assertSame(0, $this->ghlCalls());

        $this->get('/checkout/complete/' . $order->uuid)->assertOk()->assertSee('We received your payment');
    }

    public function test_payment_without_our_metadata_is_recorded_unlinked(): void
    {
        $this->webhook([
            'id' => 'evt-ext', 'type' => 'payment.succeeded', 'created_at' => now()->toIso8601String(),
            'data' => [
                'payment_id' => 'ORD-EXT1-0000-0000', 'amount' => 49.00,
                'buyer' => ['id' => 'u1', 'name' => 'Funnel Buyer', 'email' => 'f@example.com'],
                'item'  => ['id' => 'X1', 'title' => 'GHL funnel product', 'type' => 'onetime'],
            ],
        ])->assertOk();

        $p = Payment::firstOrFail();
        $this->assertNull($p->subscription_id);
        $this->assertSame('commas', $p->provider);
        $this->assertSame('ORD-EXT1-0000-0000', $p->transaction_id);
        $this->assertSame(0, $this->ghlCalls());
    }

    public function test_refund_webhook_records_negative_payment_against_subscription(): void
    {
        $order = $this->createOrder();
        $this->webhook($this->paymentSucceeded($order))->assertOk();

        $this->webhook([
            'id' => 'evt-refund', 'type' => 'refund.created', 'created_at' => now()->toIso8601String(),
            'data' => [
                'refund_id' => 'rf1', 'amount' => 897.00, 'refund_type' => 'full', 'reason' => 'requested_by_customer',
                'buyer' => ['id' => 'user_abc', 'name' => 'Jane Doe', 'email' => 'jane@example.com'],
                'item'  => ['id' => self::PRODUCT, 'title' => 'Gold Plan', 'type' => 'onetime'],
            ],
        ])->assertOk();

        $refund = Payment::where('type', 'refund')->firstOrFail();
        $this->assertSame($order->refresh()->subscription_id, $refund->subscription_id);
        $this->assertSame(0.0, Subscription::first()->lifetimeRevenue());
    }

    // ── browser confirm path ────────────────────────────────────────────────

    public function test_browser_confirm_verifies_with_api_then_webhook_is_a_noop(): void
    {
        $order = $this->createOrder();

        Http::fake([
            'api-sandbox.commas.net/public-api/transactions/*' => Http::response([
                'status' => 'success',
                'data'   => [
                    'id' => 919049, 'transaction_date' => now()->toIso8601String(), 'amount' => 897.00,
                    'fan' => ['id' => 'f1', 'email' => 'jane@example.com'],
                    'product' => ['id' => self::PRODUCT, 'title' => 'Gold Plan', 'price' => '897.00'],
                ],
            ]),
        ]);

        $this->postJson('/checkout/confirm', ['order' => $order->uuid, 'transaction_id' => 'pX9vQ'])
            ->assertOk()
            ->assertJsonPath('status', 'paid')
            ->assertJsonPath('redirect', route('checkout.complete', $order->uuid));

        $this->assertSame('browser', $order->refresh()->confirmed_via);
        $this->assertSame('919049', $order->commas_transaction_id);

        $this->webhook($this->paymentSucceeded($order))->assertOk();

        $this->assertSame(1, Subscription::count());
        $this->assertSame(1, Payment::count());
        $this->assertSame(1, $this->ghlCalls());
        $this->assertSame('ORD-AAAA-BBBB-CCCC', $order->refresh()->commas_payment_id); // backfilled
    }

    public function test_browser_confirm_ignores_someone_elses_transaction(): void
    {
        $order = $this->createOrder();

        Http::fake([
            'api-sandbox.commas.net/public-api/transactions/*' => Http::response([
                'status' => 'success',
                'data'   => [
                    'id' => 1, 'transaction_date' => now()->toIso8601String(), 'amount' => 897.00,
                    'fan' => ['email' => 'someone-else@example.com'],
                    'product' => ['id' => self::PRODUCT],
                ],
            ]),
        ]);

        $this->postJson('/checkout/confirm', ['order' => $order->uuid, 'transaction_id' => 'stolen'])
            ->assertOk()->assertJsonPath('status', 'pending');

        $this->assertSame(0, Subscription::count());
    }

    // ── completion + onboarding handoff ─────────────────────────────────────

    public function test_complete_page_waits_then_hands_off_to_onboarding(): void
    {
        $order = $this->createOrder();

        $this->get('/checkout/complete/' . $order->uuid)->assertOk()->assertSee('Finalizing your enrollment');
        $this->getJson('/checkout/status/' . $order->uuid)->assertJsonPath('status', 'pending');

        $this->webhook($this->paymentSucceeded($order))->assertOk();

        $this->getJson('/checkout/status/' . $order->uuid)->assertJsonPath('status', 'paid');
        $this->get('/checkout/complete/' . $order->uuid)->assertRedirect('/onboardingform');

        $this->get('/onboardingform')
            ->assertOk()
            ->assertSee('Gold Plan')
            ->assertSee('Jane');
    }

    // ── reconcile path ──────────────────────────────────────────────────────

    public function test_reconcile_fulfills_a_paid_order_whose_webhook_was_lost(): void
    {
        $order = $this->createOrder();
        $this->travel(5)->minutes();

        Http::fake([
            'api-sandbox.commas.net/public-api/checkout-sessions/transactions*' => Http::response([
                'status' => 'success',
                'data'   => [
                    'transactions' => [[
                        'id' => 777, 'transaction_date' => now()->subMinutes(3)->toIso8601String(), 'amount' => 897.00,
                        'fan' => ['id' => 'f1', 'email' => 'jane@example.com'],
                        'product' => ['id' => self::PRODUCT],
                    ]],
                    'pagination' => ['has_more' => false],
                ],
            ]),
        ]);

        $this->artisan('commas:reconcile')->assertSuccessful();
        $this->artisan('commas:reconcile')->assertSuccessful();   // second run is a no-op

        $order->refresh();
        $this->assertSame('paid', $order->status);
        $this->assertSame('reconcile', $order->confirmed_via);
        $this->assertSame('777', $order->commas_transaction_id);
        $this->assertSame(1, Subscription::count());
        $this->assertSame(1, $this->ghlCalls());
    }

    public function test_reconcile_attaches_id_to_webhook_confirmed_order_instead_of_fulfilling_a_retry(): void
    {
        // Customer started checkout twice; paid on the second order; webhook confirmed it.
        Http::fake([
            'api-sandbox.commas.net/public-api/checkout-sessions/transactions*' => Http::response([
                'status' => 'success',
                'data'   => [
                    'transactions' => [[
                        'id' => 888, 'transaction_date' => now()->toIso8601String(), 'amount' => 897.00,
                        'fan' => ['email' => 'jane@example.com'], 'product' => ['id' => self::PRODUCT],
                    ]],
                    'pagination' => ['has_more' => false],
                ],
            ]),
        ]);

        $abandoned = $this->createOrder();
        $paid      = $this->createOrder();
        $this->webhook($this->paymentSucceeded($paid))->assertOk();

        $this->artisan('commas:reconcile')->assertSuccessful();

        $this->assertSame('888', $paid->refresh()->commas_transaction_id);
        $this->assertSame('pending', $abandoned->refresh()->status);
        $this->assertSame(1, Subscription::count());
        $this->assertSame(1, $this->ghlCalls());
    }

    // ── admin ───────────────────────────────────────────────────────────────

    public function test_admin_orders_and_payments_pages_show_commas_activity(): void
    {
        $paid = $this->createOrder();
        $this->webhook($this->paymentSucceeded($paid))->assertOk();

        $bad = $this->createOrder(['email' => 'bad@example.com']);
        $this->webhook($this->paymentSucceeded($bad, [
            'payment_id' => 'ORD-BAD0-0000-0000', 'amount' => 1.00,
            'buyer' => ['id' => 'u2', 'name' => 'Bad Actor', 'email' => 'bad@example.com'],
        ]))->assertOk();

        $this->withSession(['is_admin' => true]);

        $this->get('/admin/orders')->assertOk()
            ->assertSee($paid->invoice_number)
            ->assertSee('Needs review')
            ->assertSee('Paid $1.00, expected $897.00');

        $this->get('/admin/orders?status=mismatch')->assertOk()
            ->assertSee('bad@example.com')
            ->assertDontSee($paid->invoice_number);

        $this->get('/admin/payments?provider=commas')->assertOk()
            ->assertSee('Commas')
            ->assertSee('ORD-AAAA-BBBB-CCCC');
    }

    // ── legacy guard ────────────────────────────────────────────────────────

    public function test_legacy_raw_card_endpoint_is_disabled_when_commas_is_active(): void
    {
        $this->postJson('/accept-payment', ['cardNumber' => '4111111111111111'])->assertStatus(410);
    }
}
