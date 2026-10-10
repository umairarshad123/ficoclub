<?php

namespace Tests\Feature;

use App\Models\CheckoutOrder;
use App\Support\PlanCatalog;
use App\Support\SiteSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PlansAndContentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->clean();

        config([
            'payments.provider'                  => 'commas',
            'services.commas.api_key'            => 'test-key',
            'services.commas.environment'        => 'sandbox',
            'services.commas.creator_slug'       => '850ficoclub',
            'plans.plans.gold.commas_product_id' => 'GoLd1',
        ]);
        PlanCatalog::boot();   // re-capture defaults with the test product id

        Http::fake([
            'api-sandbox.commas.net/public-api/products/create' => Http::response([
                'status' => 'success',
                'data'   => ['product_id' => 999, 'payment_link' => 'https://www.fanbasis.com/agency-checkout/850ficoclub/NeWgD'],
            ]),
            'api-sandbox.commas.net/public-api/checkout-sessions/embedded' => Http::response([
                'status' => 'success', 'data' => ['id' => 's1', 'checkout_session_secret' => 'secret'],
            ]),
        ]);

        $this->withSession(['is_admin' => true]);
    }

    protected function tearDown(): void
    {
        $this->clean();
        parent::tearDown();
    }

    private function clean(): void
    {
        File::delete(PlanCatalog::path());
        File::delete(SiteSettings::path());
        SiteSettings::flush();
    }

    private function goldForm(array $overrides = []): array
    {
        return array_merge([
            'label' => 'Gold Plan', 'tagline' => 'Public Records Program', 'desc' => 'Public records focus.',
            'amount' => '897', 'compare_at' => '1097', 'period' => 'one-time program fee',
            'monitoring_note' => '+ $34.95/mo Smart Credit monitoring', 'billing_note' => 'one-time program fee',
            'badge' => '', 'cta' => 'Apply For Gold', 'features' => ['Bankruptcy records challenged', 'Repossessions challenged'],
            'best_for' => 'Public records work.', 'visible' => '1',
        ], $overrides);
    }

    // ── Plans & Pricing ─────────────────────────────────────────────────────

    public function test_text_edits_go_live_without_touching_commas(): void
    {
        $this->get('/admin/plans')->assertOk()->assertSee('Gold Plan')->assertDontSee('Test Plan');

        $this->post('/admin/plans/gold', $this->goldForm([
            'label' => 'Gold Plus', 'cta' => 'Start Gold Plus', 'features' => ['Brand new bullet', '', 'Second bullet'],
            'best_for' => 'Edited best-for text.',
        ]))->assertRedirect()->assertSessionHas('success');

        Http::assertNotSent(fn (HttpRequest $r) => str_contains($r->url(), 'products/create'));

        $this->get('/')->assertOk()
            ->assertSee('Gold Plus')->assertSee('Start Gold Plus')->assertSee('Brand new bullet')
            ->assertSee('Edited best-for text.')->assertSee('SAVE $200');

        $this->assertSame('GoLd1', config('plans.plans.gold.commas_product_id'));
        $this->assertSame(['Brand new bullet', 'Second bullet'], config('plans.plans.gold.features'));
    }

    public function test_price_change_creates_a_new_commas_product_and_checkout_uses_it(): void
    {
        $this->post('/admin/plans/gold', $this->goldForm(['amount' => '997', 'compare_at' => '1297']))
            ->assertSessionHas('success');

        Http::assertSent(fn (HttpRequest $r) => str_contains($r->url(), 'products/create') && (float) $r['price'] === 997.0);
        $this->assertSame('NeWgD', config('plans.plans.gold.commas_product_id'));
        $this->assertSame('997.00', config('plans.plans.gold.amount'));
        $this->assertSame('300', config('plans.plans.gold.save'));

        $this->get('/')->assertSee('997')->assertSee('SAVE $300');

        $this->postJson('/checkout/order', [
            'first_name' => 'Jane', 'last_name' => 'Doe', 'email' => 'j@example.com', 'phone' => '3135550100',
            'address' => '1 Main', 'city' => 'Detroit', 'state' => 'MI', 'zip' => '48201',
            'selected_plan' => 'gold', 'agree_terms' => 1, 'agree_privacy' => 1,
        ])->assertOk()->assertJsonPath('checkout.productId', 'NeWgD');

        $this->assertSame('997.00', CheckoutOrder::first()->amount);
    }

    public function test_failed_commas_product_leaves_price_unchanged(): void
    {
        Http::swap(new \Illuminate\Http\Client\Factory());   // drop the success stub from setUp
        Http::fake(['api-sandbox.commas.net/public-api/products/create' => Http::response(['status' => 'error', 'message' => 'nope'], 500)]);

        $this->post('/admin/plans/gold', $this->goldForm(['amount' => '1200']))->assertSessionHas('error');

        $this->assertSame('897.00', config('plans.plans.gold.amount'));
        $this->assertSame('GoLd1', config('plans.plans.gold.commas_product_id'));
    }

    public function test_hiding_a_plan_and_reset(): void
    {
        $this->post('/admin/plans/gold', $this->goldForm(['visible' => '0']));
        $this->get('/')->assertDontSee('/accept-checkout?plan=gold', false);

        $this->post('/admin/plans/gold/reset')->assertSessionHas('success');
        $this->get('/')->assertSee('/accept-checkout?plan=gold', false);
    }

    public function test_surcharge_is_shown_on_cards_and_checkout_and_editable(): void
    {
        $this->get('/')->assertSee('+ 4% card processing fee at checkout');
        $this->get('/accept-checkout?plan=gold')->assertSee('Card processing fee (4%)');

        $this->post('/admin/plans/surcharge', ['surcharge_percent' => '0'])->assertSessionHas('success');
        SiteSettings::flush();
        $this->get('/')->assertDontSee('card processing fee at checkout');
    }

    // ── Site Content ────────────────────────────────────────────────────────

    private function contentForm(array $overrides = []): array
    {
        $c = \App\Support\SiteContent::defaults();

        return array_merge([
            'sales_open' => '1', 'sales_paused_message' => $c['sales_paused_message'],
            'announce_on' => '0', 'announce_text' => '', 'announce_link' => '', 'announce_style' => 'green',
            'ticker' => implode("\n", $c['ticker']),
            'hero_eyebrow' => $c['hero_eyebrow'], 'hero_line1' => $c['hero_line1'], 'hero_green1' => $c['hero_green1'],
            'hero_mid' => $c['hero_mid'], 'hero_green2' => $c['hero_green2'], 'hero_lead' => $c['hero_lead'],
            'hero_lead_strong' => $c['hero_lead_strong'], 'hero_cta1' => $c['hero_cta1'], 'hero_cta2' => $c['hero_cta2'],
            'stats' => $c['stats'], 'booking_url' => $c['booking_url'],
            'seo_title' => $c['seo_title'], 'seo_description' => $c['seo_description'],
            'referral_codes' => implode(', ', $c['referral_codes']),
        ], $overrides);
    }

    public function test_untouched_content_renders_the_original_homepage(): void
    {
        $this->get('/')->assertOk()
            ->assertSee('Improve Your Credit')
            ->assertSee('We Specialize in Consumer Law, FCRA, FDCPA &amp; Metro 2 Compliance', false)
            ->assertSee('Collections Challenged')
            ->assertSee('data-count="10000"', false)
            ->assertSee('jdPgwX73WgVZQuCJKFlI');
    }

    public function test_content_edits_show_on_the_homepage(): void
    {
        $this->get('/admin/site-content')->assertOk()->assertSee('Accepting new payments');

        $stats = \App\Support\SiteContent::defaults()['stats'];
        $stats[0] = ['count' => 12000, 'prefix' => '', 'suffix' => '+', 'label' => 'Happy Clients'];

        $this->post('/admin/site-content', $this->contentForm([
            'hero_line1' => 'Fix Your Credit Fast', 'ticker' => "Brand New Ticker\nSecond Item",
            'stats' => $stats, 'seo_title' => 'New SEO Title',
            'announce_on' => '1', 'announce_text' => 'Holiday sale this week', 'announce_style' => 'red',
        ]))->assertSessionHas('success');
        SiteSettings::flush();

        $this->get('/')->assertOk()
            ->assertSee('Fix Your Credit Fast')->assertSee('Happy Clients')->assertSee('data-count="12000"', false)
            ->assertSee('<title>New SEO Title</title>', false)
            ->assertSee('Holiday sale this week')
            ->assertDontSee('Brand New Ticker');   // announcement replaces the ticker while on

        $this->get('/accept-checkout?plan=gold')->assertSee('Holiday sale this week');
    }

    public function test_pausing_sales_blocks_new_orders(): void
    {
        $this->post('/admin/site-content', $this->contentForm(['sales_open' => '0', 'sales_paused_message' => 'Back Monday!']));
        SiteSettings::flush();

        $this->get('/accept-checkout?plan=gold')->assertSee('Back Monday!');
        $this->postJson('/checkout/order', ['selected_plan' => 'gold'])->assertStatus(503)->assertJsonPath('message', 'Back Monday!');
        $this->assertSame(0, CheckoutOrder::count());
    }

    public function test_new_referral_code_is_accepted(): void
    {
        $this->post('/admin/site-content', $this->contentForm(['referral_codes' => 'dl, zz9']));
        SiteSettings::flush();

        $this->get('/?ref=ZZ9')->assertOk()->assertSessionHas('referral_code', 'ZZ9');
        $this->flushSession();
        $this->get('/?ref=LAL')->assertOk()->assertSessionMissing('referral_code');   // removed from the list
    }
}
