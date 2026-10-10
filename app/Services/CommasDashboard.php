<?php

namespace App\Services;

use App\Models\CheckoutOrder;
use App\Models\CommasTransaction;
use App\Models\WebhookEvent;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

/**
 * Everything the Commas dashboard shows, for one date range + the equal-length
 * period before it (for the ▲/▼ deltas). Days are bucketed in the business's
 * timezone (Eastern by default) so a 9pm sale lands on the right day.
 *
 * Kept database-agnostic (sums/counts in SQL, grouping in PHP) — the data set
 * is small and this keeps it testable on SQLite.
 */
class CommasDashboard
{
    public const RANGES = [
        'today' => 'Today',
        '7d'    => '7 days',
        '30d'   => '30 days',
        '90d'   => '90 days',
        'mtd'   => 'This month',
        'ytd'   => 'This year',
    ];

    public function __construct(private string $tz = 'America/New_York')
    {
    }

    public static function timezone(): string
    {
        return (string) config('services.commas.timezone', 'America/New_York');
    }

    /** [start, end, prevStart, prevEnd] as UTC Carbons, from a range key. */
    public function window(string $range): array
    {
        $now = Carbon::now($this->tz);

        [$start, $end] = match ($range) {
            'today' => [$now->copy()->startOfDay(), $now->copy()->endOfDay()],
            '7d'    => [$now->copy()->subDays(6)->startOfDay(), $now->copy()->endOfDay()],
            '90d'   => [$now->copy()->subDays(89)->startOfDay(), $now->copy()->endOfDay()],
            'mtd'   => [$now->copy()->startOfMonth(), $now->copy()->endOfDay()],
            'ytd'   => [$now->copy()->startOfYear(), $now->copy()->endOfDay()],
            default => [$now->copy()->subDays(29)->startOfDay(), $now->copy()->endOfDay()],
        };

        // Previous period = same length immediately before (MTD/YTD compare to the same span last month/year).
        [$prevStart, $prevEnd] = match ($range) {
            'mtd'   => [$start->copy()->subMonthNoOverflow(), $now->copy()->subMonthNoOverflow()->endOfDay()],
            'ytd'   => [$start->copy()->subYear(), $now->copy()->subYear()->endOfDay()],
            // Carbon 3 diffs are signed — take the absolute length of the window.
            default => [$start->copy()->subSeconds((int) abs($start->diffInSeconds($end)) + 1), $start->copy()->subSecond()],
        };

        return array_map(fn (Carbon $c) => $c->copy()->utc(), [$start, $end, $prevStart, $prevEnd]);
    }

    public function build(string $range): array
    {
        $range = array_key_exists($range, self::RANGES) ? $range : '30d';
        [$start, $end, $prevStart, $prevEnd] = $this->window($range);

        $current  = $this->totals($start, $end);
        $previous = $this->totals($prevStart, $prevEnd);

        $rows = CommasTransaction::between($start, $end)
            ->orderBy('transaction_date')
            ->get(['transaction_date', 'amount', 'net_amount', 'refund_cost', 'product_id', 'product_title', 'customer_email']);

        return [
            'range'        => $range,
            'rangeLabel'   => self::RANGES[$range],
            'start'        => $start->copy()->setTimezone($this->tz),
            'end'          => $end->copy()->setTimezone($this->tz),
            'kpis'         => $current,
            'deltas'       => $this->deltas($current, $previous),
            'today'        => $this->totals(...array_slice($this->window('today'), 0, 2)),
            'series'       => $this->dailySeries($rows, $start, $end, $range),
            'products'     => $this->topProducts($rows, (float) $current['gross']),
            'sources'      => $this->sources($rows),
            'payouts'      => $this->payouts($start, $end),
            'funnel'       => $this->funnel($start, $end),
            'followUps'    => $this->abandonedCheckouts(),
            'referrals'    => $this->referrals($start, $end),
            'recent'       => CommasTransaction::orderByDesc('transaction_date')->limit(12)->get(),
            'topCustomers' => $this->topCustomers($start, $end),
            'disputes'     => WebhookEvent::where('provider', 'commas')->where('event_type', 'dispute.created')
                                ->whereBetween('received_at', [$start, $end])->count(),
            'hasData'      => CommasTransaction::exists(),
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────

    private function totals(Carbon $start, Carbon $end): array
    {
        $q = fn () => CommasTransaction::between($start, $end);

        $gross      = (float) $q()->sum('amount');
        $fees       = (float) $q()->sum('fee_amount');
        $net        = (float) $q()->sum('net_amount');
        $refunded   = (float) $q()->sum('refunded_amount');
        $refundCost = (float) $q()->sum('refund_cost');
        $orders     = (int) $q()->count();
        $paidOrders = (int) $q()->where('amount', '>', 0)->count();
        $customers  = (int) $q()->whereNotNull('customer_email')->distinct()->count('customer_email');
        $refundsN   = (int) $q()->where('refund_count', '>', 0)->count();

        return [
            'gross'       => $gross,
            'fees'        => $fees,
            'fee_rate'    => $gross > 0 ? $fees / $gross * 100 : 0,
            'net'         => $net,
            'refunded'    => $refunded,
            'refund_cost' => $refundCost,
            'kept'        => $net - $refundCost,                       // what the business actually keeps
            'orders'      => $orders,
            'customers'   => $customers,
            'aov'         => $paidOrders > 0 ? $gross / $paidOrders : 0,
            'refunds'     => $refundsN,
            'refund_rate' => $orders > 0 ? $refundsN / $orders * 100 : 0,
        ];
    }

    /** % change per KPI, null when there is nothing to compare against. */
    private function deltas(array $cur, array $prev): array
    {
        $out = [];
        foreach (['gross', 'net', 'kept', 'orders', 'customers', 'aov', 'fees', 'refunded'] as $k) {
            $out[$k] = $prev[$k] > 0 ? round(($cur[$k] - $prev[$k]) / $prev[$k] * 100, 1) : null;
        }
        return $out;
    }

    /** Bucket size for the chart: days up to a month, weeks for 90 days, months for the year. */
    public static function bucketFor(string $range): string
    {
        return match ($range) { '90d' => 'week', 'ytd' => 'month', default => 'day' };
    }

    /** Gross per bucket, zero-filled, in the business timezone. */
    private function dailySeries(Collection $rows, Carbon $start, Carbon $end, string $range): array
    {
        $bucket = self::bucketFor($range);
        $snap   = fn (Carbon $d) => match ($bucket) {
            'week'  => $d->copy()->startOfWeek(Carbon::MONDAY),
            'month' => $d->copy()->startOfMonth(),
            default => $d->copy()->startOfDay(),
        };

        $byKey = $rows->groupBy(fn ($r) => $snap($r->transaction_date->copy()->setTimezone($this->tz))->format('Y-m-d'))
                      ->map(fn ($g) => ['gross' => round((float) $g->sum('amount'), 2), 'orders' => $g->count()]);

        $period = CarbonPeriod::create(
            $snap($start->copy()->setTimezone($this->tz)),
            '1 ' . $bucket,
            $snap($end->copy()->setTimezone($this->tz))
        );

        $series = [];
        foreach ($period as $d) {
            $key = $d->format('Y-m-d');
            $series[] = [
                'label'  => match ($bucket) { 'month' => $d->format('M'), 'week' => 'Wk of ' . $d->format('M j'), default => $d->format('M j') },
                'gross'  => $byKey[$key]['gross'] ?? 0,
                'orders' => $byKey[$key]['orders'] ?? 0,
            ];
        }

        return $series;
    }

    private function topProducts(Collection $rows, float $gross): array
    {
        $websiteIds = CommasTransaction::websiteProductIds();

        return $rows->groupBy(fn ($r) => $r->product_title ?: 'Unknown product')
            ->map(fn ($g, $title) => [
                'title'   => $title,
                'gross'   => (float) $g->sum('amount'),
                'orders'  => $g->count(),
                'share'   => $gross > 0 ? $g->sum('amount') / $gross * 100 : 0,
                'website' => in_array($g->first()->product_id, $websiteIds, true),
            ])
            ->sortByDesc('gross')
            ->take(8)
            ->values()
            ->all();
    }

    /** Website checkout vs everything else sold through Commas (GHL funnels, payment links). */
    private function sources(Collection $rows): array
    {
        $websiteIds = CommasTransaction::websiteProductIds();
        [$web, $other] = $rows->partition(fn ($r) => in_array($r->product_id, $websiteIds, true));
        $total = (float) $rows->sum('amount');

        return [
            'website' => ['gross' => (float) $web->sum('amount'),   'orders' => $web->count(),
                          'share' => $total > 0 ? $web->sum('amount') / $total * 100 : 0],
            'other'   => ['gross' => (float) $other->sum('amount'), 'orders' => $other->count(),
                          'share' => $total > 0 ? $other->sum('amount') / $total * 100 : 0],
        ];
    }

    private function payouts(Carbon $start, Carbon $end): array
    {
        $onHold = CommasTransaction::where('fund_released', false);
        $next   = CommasTransaction::where('fund_released', false)
            ->where('fund_release_on', '>=', now())
            ->orderBy('fund_release_on')
            ->first(['fund_release_on', 'net_amount']);

        return [
            'on_hold'        => (float) (clone $onHold)->sum('net_amount') - (float) (clone $onHold)->sum('refund_cost'),
            'on_hold_count'  => (clone $onHold)->count(),
            'next_release'   => $next?->fund_release_on?->copy()->setTimezone($this->tz),
            'next_amount'    => (float) ($next?->net_amount ?? 0),
            'released_range' => (float) CommasTransaction::where('fund_released', true)
                                    ->whereBetween('fund_release_on', [$start, $end])->sum('net_amount'),
        ];
    }

    /** Website checkout funnel (the $1 internal test plan is left out). */
    private function funnel(Carbon $start, Carbon $end): array
    {
        $q = fn () => CheckoutOrder::where('plan_key', '!=', 'test')->whereBetween('created_at', [$start, $end]);

        $started = $q()->count();
        $paid    = $q()->where('status', CheckoutOrder::STATUS_PAID)->count();
        $buyers  = $q()->where('status', CheckoutOrder::STATUS_PAID)->distinct()->count('email');
        $people  = $q()->distinct()->count('email');

        return [
            'started'     => $started,
            'people'      => $people,
            'paid'        => $paid,
            'review'      => $q()->where('status', CheckoutOrder::STATUS_MISMATCH)->count(),
            'conversion'  => $people > 0 ? $buyers / $people * 100 : 0,
            'revenue'     => (float) $q()->where('status', CheckoutOrder::STATUS_PAID)->sum('amount'),
        ];
    }

    /**
     * Started checkout, never paid (and haven't paid since) — a follow-up list.
     * Newest first, one row per email, from the last 30 days, older than 1 hour.
     */
    private function abandonedCheckouts(): Collection
    {
        $paidEmails = CheckoutOrder::where('status', '!=', CheckoutOrder::STATUS_PENDING)->pluck('email')->all();

        return CheckoutOrder::where('status', CheckoutOrder::STATUS_PENDING)
            ->where('plan_key', '!=', 'test')
            ->whereBetween('created_at', [now()->subDays(30), now()->subHour()])
            ->whereNotIn('email', $paidEmails ?: ['__none__'])
            ->orderByDesc('created_at')
            ->get()
            ->unique('email')
            ->take(10)
            ->values();
    }

    private function referrals(Carbon $start, Carbon $end): array
    {
        return CheckoutOrder::where('status', CheckoutOrder::STATUS_PAID)
            ->where('plan_key', '!=', 'test')
            ->whereBetween('paid_at', [$start, $end])
            ->get(['referral_code', 'amount'])
            ->groupBy(fn ($o) => $o->referral_code ?: 'DIRECT')
            ->map(fn ($g, $code) => ['code' => $code, 'orders' => $g->count(), 'revenue' => (float) $g->sum('amount')])
            ->sortByDesc('revenue')
            ->values()
            ->all();
    }

    private function topCustomers(Carbon $start, Carbon $end): array
    {
        return CommasTransaction::between($start, $end)
            ->whereNotNull('customer_email')
            ->get(['customer_email', 'customer_name', 'amount'])
            ->groupBy('customer_email')
            ->map(fn ($g, $email) => [
                'email'  => $email,
                'name'   => $g->first()->customer_name,
                'gross'  => (float) $g->sum('amount'),
                'orders' => $g->count(),
            ])
            ->sortByDesc('gross')
            ->take(5)
            ->values()
            ->all();
    }
}
