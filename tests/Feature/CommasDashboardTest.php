<?php

namespace Tests\Feature;

use App\Models\CommasTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class CommasDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'services.commas.api_key'            => 'test-key',
            'services.commas.environment'        => 'sandbox',
            'plans.plans.gold.commas_product_id' => 'GoLd1',
        ]);

        Http::fake([
            'api-sandbox.commas.net/public-api/checkout-sessions/transactions*' => Http::response([
                'status' => 'success',
                'data'   => [
                    'transactions' => [
                        $this->txn(101, now()->subDay(), 932.88, 36.40, 'GoLd1', 'Gold Plan', 'jane@example.com', 'Jane Doe'),
                        $this->txn(102, now()->subDays(2), 520.00, 20.29, 'X9', 'Gold Package -500/3M', 'bob@example.com', 'Bob Roe', released: true),
                        $this->txn(103, now()->subDays(40), 260.00, 10.29, 'X9', 'Gold Package -500/3M', 'old@example.com', 'Old Buyer', released: true),
                        $this->txn(104, now()->subHours(3), 1.04, 0.33, 'X2', 'Test thing', 'ref@example.com', 'Ref Und', refund: 1.04),
                    ],
                    'pagination' => ['has_more' => false],
                ],
            ]),
        ]);
    }

    private function txn(int $id, $date, float $amount, float $fee, string $productId, string $title, string $email, string $name, bool $released = false, float $refund = 0): array
    {
        return [
            'id' => $id,
            'transaction_date' => $date->toIso8601String(),
            'fan' => ['id' => 'f' . $id, 'name' => $name, 'email' => $email, 'phone' => '3135550100'],
            'servicePayment' => ['payment_type' => 'upfront', 'fund_release_on' => $date->copy()->addDays(2)->format('Y-m-d H:i:s'), 'fund_released' => $released ? 1 : 0],
            'product' => ['id' => $productId, 'title' => $title, 'price' => (string) $amount],
            'refunds' => $refund ? [['amount' => $refund, 'refund_cost' => $refund + 0.03]] : [],
            'fee_amount' => $fee,
            'net_amount' => round($amount - $fee, 2),
            'amount' => $amount,
        ];
    }

    public function test_sync_mirrors_transactions_and_is_idempotent(): void
    {
        $this->artisan('commas:sync')->assertSuccessful();
        $this->artisan('commas:sync')->assertSuccessful();

        $this->assertSame(4, CommasTransaction::count());

        $t = CommasTransaction::where('commas_id', '104')->first();
        $this->assertSame('1.04', $t->refunded_amount);
        $this->assertSame('1.07', $t->refund_cost);
        $this->assertSame(1, $t->refund_count);
        $this->assertTrue(CommasTransaction::where('commas_id', '101')->first()->isWebsiteSale());
        $this->assertFalse(CommasTransaction::where('commas_id', '102')->first()->isWebsiteSale());
    }

    public function test_dashboard_shows_commas_numbers_for_the_range(): void
    {
        $this->artisan('commas:sync');
        $this->withSession(['is_admin' => true]);

        // 30-day window: 101 + 102 + 104 (103 is 40 days old)
        $this->get('/admin?range=30d')->assertOk()
            ->assertSee('Commas · live')
            ->assertSee('$1,454')                 // gross 932.88 + 520 + 1.04
            ->assertSee('Gold Plan')
            ->assertSee('Gold Package -500/3M')
            ->assertSee('Jane Doe');

        // 90 days includes the older sale, bucketed by week
        $this->get('/admin?range=90d')->assertOk()->assertSee('$1,714')->assertSee('Gross sales by week');
    }

    public function test_commas_sales_page_filters_and_exports(): void
    {
        $this->artisan('commas:sync');
        $this->withSession(['is_admin' => true]);

        $this->get('/admin/commas-sales')->assertOk()->assertSee('Showing')->assertSee('$1,713.92');

        $this->get('/admin/commas-sales?source=website')->assertOk()
            ->assertSee('jane@example.com')->assertDontSee('bob@example.com');

        $this->get('/admin/commas-sales?state=refunded')->assertOk()
            ->assertSee('ref@example.com')->assertDontSee('jane@example.com');

        $csv = $this->get('/admin/commas-sales/export?source=other')->assertOk()->streamedContent();
        $this->assertStringContainsString('bob@example.com', $csv);
        $this->assertStringNotContainsString('jane@example.com', $csv);
    }

    public function test_sync_now_button(): void
    {
        $this->withSession(['is_admin' => true]);

        $this->from('/admin')->post('/admin/commas/sync')->assertRedirect('/admin')->assertSessionHas('success');
        $this->assertSame(4, CommasTransaction::count());
    }
}
