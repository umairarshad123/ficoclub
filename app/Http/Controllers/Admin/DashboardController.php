<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\SubscriptionEvent;
use App\Models\WebhookEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DashboardController extends Controller
{
    private const CANCELLED_STATUSES = ['terminated', 'cancelled'];
    private const MRR_STATUSES       = ['active', 'past_due'];
    private const REFERRAL_CODES     = ['DL', 'EL', 'NL', 'EXP'];

    // ═════════════════════════════════════════════════════════════════════════
    // Main dashboard
    // ═════════════════════════════════════════════════════════════════════════
    public function index()
    {
        return view('admin.dashboard', [
            'kpis'              => $this->buildKpis(),
            'mrrSeries'         => $this->buildMrrSeries(),
            'newSubsSeries'     => $this->buildNewSubsSeries(),
            'planSeriesMeta'    => $this->planSeriesMeta(),
            'referralBreakdown' => $this->buildReferralBreakdown(),
            'recentSubs'        => Subscription::orderByDesc('created_at')->limit(10)->get(),
            'activityFeed'      => $this->buildActivityFeed(20),
            'atRiskCount'       => Subscription::where('status', 'past_due')->count(),
            'atRiskPreview'     => Subscription::where('status', 'past_due')
                                    ->orderBy('grace_period_ends_at')
                                    ->limit(5)
                                    ->get(),
            'health'            => $this->buildOperationalHealth(),
            'planMix'           => $this->buildPlanMix(),
            'recentPayments'    => Payment::with('subscription:id,first_name,last_name,email')
                                    ->whereIn('type', ['initial', 'recurring', 'refund', 'void'])
                                    ->orderByDesc('charged_at')
                                    ->limit(10)
                                    ->get(),
        ]);
    }

    // ═════════════════════════════════════════════════════════════════════════
    // Subscriptions listing
    // ═════════════════════════════════════════════════════════════════════════
    public function subscriptionsIndex(Request $request)
    {
        $query = Subscription::query();

        if ($search = trim((string) $request->input('q', ''))) {
            $query->where(function ($w) use ($search) {
                $w->where('first_name',  'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email',     'like', "%{$search}%")
                  ->orWhere('phone',     'like', "%{$search}%")
                  ->orWhere('invoice_number', 'like', "%{$search}%");
            });
        }

        if ($status = $request->input('status')) {
            $status === 'cancelled'
                ? $query->whereIn('status', self::CANCELLED_STATUSES)
                : $query->where('status', $status);
        }
        if ($plan = $request->input('plan'))     { $query->where('plan_key', $plan); }
        if ($ref  = $request->input('referral')) {
            $ref === 'direct'
                ? $query->whereNull('referral_code')
                : $query->where('referral_code', $ref);
        }
        if ($from = $request->input('from')) { $query->whereDate('created_at', '>=', $from); }
        if ($to   = $request->input('to'))   { $query->whereDate('created_at', '<=', $to); }

        $subs = $query->orderByDesc('created_at')->paginate(25)->withQueryString();

        $tabCounts = [
            'total'     => Subscription::count(),
            'active'    => Subscription::where('status', 'active')->count(),
            'suspended' => Subscription::where('status', 'past_due')->count(),
            'cancelled' => Subscription::whereIn('status', self::CANCELLED_STATUSES)->count(),
        ];

        return view('admin.subscriptions-index', [
            'subs'      => $subs,
            'filters'   => $request->only(['q', 'status', 'plan', 'referral', 'from', 'to']),
            'tabCounts' => $tabCounts,
        ]);
    }

    public function subscriptionShow(int $id)
    {
        $sub = Subscription::with([
                'events'   => fn ($q) => $q->orderByDesc('created_at'),
                'payments' => fn ($q) => $q->orderByDesc('charged_at'),
            ])->findOrFail($id);

        return view('admin.subscription-show', [
            'sub'             => $sub,
            'lifetimeRevenue' => $sub->lifetimeRevenue(),
            'paymentsCount'   => $sub->paymentsCount(),
        ]);
    }

    // ═════════════════════════════════════════════════════════════════════════
    // LEADS
    // ═════════════════════════════════════════════════════════════════════════
    public function leadsIndex(Request $request)
    {
        $query = Lead::query();

        if ($search = trim((string) $request->input('q', ''))) {
            $query->where(function ($w) use ($search) {
                $w->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email',     'like', "%{$search}%")
                  ->orWhere('phone',     'like', "%{$search}%")
                  ->orWhere('final_email','like', "%{$search}%")
                  ->orWhere('biz_name',  'like', "%{$search}%");
            });
        }

        if ($ft = $request->input('funding_type')) {
            $query->where('funding_type', $ft);
        }

        if ($status = $request->input('lead_status')) {
            if ($status === 'converted') {
                // Leads who also exist as subscriptions (by email match)
                $subEmails = Subscription::pluck('email')->map(fn($e) => strtolower($e))->toArray();
                $query->where(function($q) use ($subEmails) {
                    $q->whereIn(DB::raw('LOWER(email)'), $subEmails)
                      ->orWhereIn(DB::raw('LOWER(final_email)'), $subEmails);
                });
            } elseif ($status === 'in_ghl') {
                $query->where('ghl_pushed', 1);
            } elseif ($status === 'not_in_ghl') {
                $query->where('ghl_pushed', 0);
            } elseif ($status === 'no_email') {
                $query->where(function($q) {
                    $q->whereNull('email')->whereNull('final_email');
                });
            }
        }

        if ($from = $request->input('from')) { $query->whereDate('created_at', '>=', $from); }
        if ($to   = $request->input('to'))   { $query->whereDate('created_at', '<=', $to); }

        $leads = $query->orderByDesc('created_at')->paginate(25)->withQueryString();

        // Tag each lead with conversion status
        $subEmails = Subscription::pluck('email')->map(fn($e) => strtolower($e))->toArray();
        foreach ($leads as $lead) {
            $leadEmail = strtolower($lead->final_email ?? $lead->email ?? '');
            $lead->converted = $leadEmail && in_array($leadEmail, $subEmails, true);
        }

        $tabCounts = [
            'total'        => Lead::count(),
            'converted'    => $this->countConvertedLeads(),
            'in_ghl'       => Lead::where('ghl_pushed', 1)->count(),
            'not_in_ghl'   => Lead::where('ghl_pushed', 0)->count(),
        ];

        return view('admin.leads-index', [
            'leads'     => $leads,
            'filters'   => $request->only(['q', 'funding_type', 'lead_status', 'from', 'to']),
            'tabCounts' => $tabCounts,
        ]);
    }

    public function leadShow(int $id)
    {
        $lead = Lead::findOrFail($id);

        // Find matching subscription if converted
        $emails = array_filter([
            strtolower($lead->email ?? ''),
            strtolower($lead->final_email ?? ''),
        ]);
        $matchingSub = null;
        if ($emails) {
            $matchingSub = Subscription::whereIn(DB::raw('LOWER(email)'), $emails)->first();
        }

        return view('admin.lead-show', [
            'lead'        => $lead,
            'matchingSub' => $matchingSub,
        ]);
    }

    private function countConvertedLeads(): int
    {
        $subEmails = Subscription::pluck('email')->map(fn($e) => strtolower($e))->toArray();
        if (empty($subEmails)) return 0;

        return Lead::where(function($q) use ($subEmails) {
            $q->whereIn(DB::raw('LOWER(email)'), $subEmails)
              ->orWhereIn(DB::raw('LOWER(final_email)'), $subEmails);
        })->count();
    }

    // ═════════════════════════════════════════════════════════════════════════
    // AT-RISK PAGE
    // ═════════════════════════════════════════════════════════════════════════
    public function atRisk()
    {
        $atRisk = Subscription::where('status', 'past_due')
                    ->orderBy('grace_period_ends_at')
                    ->get();

        // Also show recent terminations (last 30 days) — for context
        $recentlyTerminated = Subscription::whereIn('status', self::CANCELLED_STATUSES)
                    ->where('terminated_at', '>=', now()->subDays(30))
                    ->orderByDesc('terminated_at')
                    ->limit(20)
                    ->get();

        $lostMrr = (float) $recentlyTerminated->sum('recurring_amount');
        $atRiskMrr = (float) $atRisk->sum('recurring_amount');

        return view('admin.at-risk', [
            'atRisk'             => $atRisk,
            'recentlyTerminated' => $recentlyTerminated,
            'atRiskMrr'          => $atRiskMrr,
            'lostMrr'            => $lostMrr,
            'graceBuckets'       => $this->buildGracePeriodBuckets(),
        ]);
    }

    // ═════════════════════════════════════════════════════════════════════════
    // REFERRALS
    // ═════════════════════════════════════════════════════════════════════════
    public function referrals()
    {
        return view('admin.referrals', [
            'breakdown' => $this->buildReferralBreakdown(),
            'planMix'   => $this->buildPlanMix(),
        ]);
    }

    // ═════════════════════════════════════════════════════════════════════════
    // Webhooks — Authorize.Net inbound monitoring
    // Read-only surfaces; no webhook processing logic touched.
    // ═════════════════════════════════════════════════════════════════════════

    public function webhooksIndex(Request $request)
    {
        $now      = Carbon::now();
        $dayStart = $now->copy()->startOfDay();
        $hourAgo  = $now->copy()->subHour();
        $weekAgo  = $now->copy()->subDays(7);

        // ── Filters ──────────────────────────────────────────────────────────
        $query = WebhookEvent::query();

        if ($eventType = $request->input('event_type')) {
            $query->where('event_type', $eventType);
        }

        if ($sig = $request->input('signature')) {
            match ($sig) {
                'ok'         => $query->where('signature_valid', true),
                'invalid'    => $query->where('signature_valid', false),
                'unverified' => $query->whereNull('signature_valid'),
                default      => null,
            };
        }

        if ($search = trim((string) $request->input('q', ''))) {
            $query->where(function ($w) use ($search) {
                $w->where('notification_id',     'like', "%{$search}%")
                  ->orWhere('entity_id',          'like', "%{$search}%")
                  ->orWhere('invoice_number',     'like', "%{$search}%")
                  ->orWhere('source_ip',          'like', "%{$search}%")
                  ->orWhere('customer_first_name','like', "%{$search}%")
                  ->orWhere('customer_last_name', 'like', "%{$search}%")
                  ->orWhere('customer_email',     'like', "%{$search}%")
                  ->orWhere('description',        'like', "%{$search}%");
            });
        }

        if ($from = $request->input('from')) { $query->whereDate('received_at', '>=', $from); }
        if ($to   = $request->input('to'))   { $query->whereDate('received_at', '<=', $to); }

        $events = $query->with('matchedSubscription:id,first_name,last_name,email,arb_subscription_id')
            ->orderByDesc('received_at')
            ->paginate(25)
            ->withQueryString();

        // ── Stats strip ──────────────────────────────────────────────────────
        $stats = [
            'today'              => WebhookEvent::whereBetween('received_at', [$dayStart, $now])->count(),
            'last_hour'          => WebhookEvent::whereBetween('received_at', [$hourAgo, $now])->count(),
            'last_7d'            => WebhookEvent::whereBetween('received_at', [$weekAgo, $now])->count(),
            'signature_ok_today' => WebhookEvent::whereBetween('received_at', [$dayStart, $now])
                                        ->where('signature_valid', true)->count(),
            'signature_invalid_today' => WebhookEvent::whereBetween('received_at', [$dayStart, $now])
                                        ->where('signature_valid', false)->count(),
            'last_received_at'   => WebhookEvent::max('received_at'),
        ];
        $stats['last_received_at'] = $stats['last_received_at']
            ? Carbon::parse($stats['last_received_at'])
            : null;

        // ── Event-type breakdown (for filter dropdown + chips) ───────────────
        $byTypeAll = WebhookEvent::select('event_type', DB::raw('COUNT(*) as cnt'))
            ->groupBy('event_type')
            ->orderByDesc('cnt')
            ->get();

        // ── 24h activity series for the chart (hourly buckets) ───────────────
        $series = $this->buildWebhookHourlySeries($now);

        return view('admin.webhooks-index', [
            'events'    => $events,
            'stats'     => $stats,
            'byTypeAll' => $byTypeAll,
            'series'    => $series,
            'filters'   => $request->only(['event_type', 'signature', 'q', 'from', 'to']),
        ]);
    }

    public function webhookShow(int $id)
    {
        $event = WebhookEvent::with('matchedSubscription:id,first_name,last_name,email,plan_label,status,arb_subscription_id')
            ->findOrFail($id);

        return view('admin.webhook-show', [
            'event' => $event,
        ]);
    }

    /**
     * Build a 24-row hourly series ending at the current hour.
     * Each row: ['label' => '14:00', 'count' => N].
     */
    private function buildWebhookHourlySeries(Carbon $now): array
    {
        $start = $now->copy()->subHours(23)->startOfHour();

        $rows = WebhookEvent::where('received_at', '>=', $start)
            ->select(
                DB::raw("DATE_FORMAT(received_at, '%Y-%m-%d %H:00:00') as bucket"),
                DB::raw('COUNT(*) as cnt')
            )
            ->groupBy('bucket')
            ->pluck('cnt', 'bucket')
            ->toArray();

        $series = [];
        $cursor = $start->copy();
        for ($i = 0; $i < 24; $i++) {
            $key = $cursor->format('Y-m-d H:00:00');
            $series[] = [
                'label' => $cursor->format('ga'),
                'count' => (int) ($rows[$key] ?? 0),
            ];
            $cursor->addHour();
        }
        return $series;
    }

    // ═════════════════════════════════════════════════════════════════════════
    // CSV EXPORT
    // ═════════════════════════════════════════════════════════════════════════
    public function exportCsv(Request $request): StreamedResponse
    {
        $filename = 'subscriptions-' . now()->format('Y-m-d-His') . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $query = Subscription::query();
        if ($search = trim((string) $request->input('q', ''))) {
            $query->where(function ($w) use ($search) {
                $w->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email',     'like', "%{$search}%");
            });
        }
        if ($status = $request->input('status')) {
            $status === 'cancelled'
                ? $query->whereIn('status', self::CANCELLED_STATUSES)
                : $query->where('status', $status);
        }
        if ($plan = $request->input('plan')) { $query->where('plan_key', $plan); }
        if ($ref = $request->input('referral')) {
            $ref === 'direct'
                ? $query->whereNull('referral_code')
                : $query->where('referral_code', $ref);
        }
        if ($from = $request->input('from')) { $query->whereDate('created_at', '>=', $from); }
        if ($to   = $request->input('to'))   { $query->whereDate('created_at', '<=', $to); }

        return response()->stream(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'ID','Created','First Name','Last Name','Email','Phone','City','State',
                'Plan','Initial','Recurring','Status','Referral Code','ARB ID',
                'Subscribed At','Next Billing','Terminated At','Failed Payments','Invoice',
            ]);
            $query->orderByDesc('created_at')->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $s) {
                    fputcsv($out, [
                        $s->id,
                        optional($s->created_at)->format('Y-m-d H:i'),
                        $s->first_name, $s->last_name, $s->email, $s->phone,
                        $s->city, $s->state,
                        $s->plan_label, $s->amount, $s->recurring_amount, $s->status,
                        $s->referral_code ?: 'DIRECT',
                        $s->arb_subscription_id,
                        optional($s->subscribed_at)->format('Y-m-d H:i'),
                        optional($s->next_billing_date)->format('Y-m-d'),
                        optional($s->terminated_at)->format('Y-m-d H:i'),
                        $s->failed_payment_count,
                        $s->invoice_number,
                    ]);
                }
            });
            fclose($out);
        }, 200, $headers);
    }

    public function leadsExportCsv(Request $request): StreamedResponse
    {
        $filename = 'leads-' . now()->format('Y-m-d-His') . '.csv';
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $query = Lead::query();
        if ($search = trim((string) $request->input('q', ''))) {
            $query->where(function ($w) use ($search) {
                $w->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email',     'like', "%{$search}%");
            });
        }
        if ($ft = $request->input('funding_type')) { $query->where('funding_type', $ft); }

        return response()->stream(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, [
                'ID','Created','First Name','Last Name','Email','Final Email','Phone',
                'State','Funding Type','Employment','Annual Income',
                'Business Name','Business State','GHL Pushed','Sheets Pushed',
            ]);
            $query->orderByDesc('created_at')->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $l) {
                    fputcsv($out, [
                        $l->id,
                        optional($l->created_at)->format('Y-m-d H:i'),
                        $l->first_name, $l->last_name,
                        $l->email, $l->final_email, $l->phone,
                        $l->state, $l->funding_type,
                        $l->employment_status, $l->annual_income,
                        $l->biz_name, $l->biz_state,
                        $l->ghl_pushed ? 'yes' : 'no',
                        $l->sheets_pushed ? 'yes' : 'no',
                    ]);
                }
            });
            fclose($out);
        }, 200, $headers);
    }

    // ═════════════════════════════════════════════════════════════════════════
    // Aggregate builders (private)
    // ═════════════════════════════════════════════════════════════════════════

    private function buildKpis(): array
    {
        $now         = Carbon::now();
        $monthStart  = $now->copy()->startOfMonth();
        $monthEnd    = $now->copy()->endOfMonth();
        $prevStart   = $monthStart->copy()->subMonth();
        $prevEnd     = $prevStart->copy()->endOfMonth();

        $totalCustomers = Subscription::count();
        $activeCount    = Subscription::where('status', 'active')->count();
        $pastDueCount   = Subscription::where('status', 'past_due')->count();
        $cancelledCount = Subscription::whereIn('status', self::CANCELLED_STATUSES)->count();

        $mrr = (float) Subscription::whereIn('status', self::MRR_STATUSES)->sum('recurring_amount');

        $monthInitialRevenue = (float) Subscription::whereBetween('created_at', [$monthStart, $monthEnd])
                                    ->sum('amount');

        $newSubsThisMonth = Subscription::whereBetween('created_at', [$monthStart, $monthEnd])->count();

        $totalLeads = Lead::count();
        $conversionPct = $totalLeads > 0
            ? round(($totalCustomers / $totalLeads) * 100, 1)
            : 0.0;

        $atRisk = Subscription::where('status', 'past_due')
                    ->whereNotNull('grace_period_ends_at')
                    ->where('grace_period_ends_at', '>', $now)
                    ->count();

        // ── Cash collected (initial + recurring, net of refunds/voids) ────────
        $revenueThisMonth  = $this->revenueCollectedBetween($monthStart, $monthEnd);
        $revenueLastMonth  = $this->revenueCollectedBetween($prevStart, $prevEnd);
        $revenueDeltaPct   = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
            : null;

        // ── Net new MRR this month ────────────────────────────────────────────
        $mrrAdded = (float) Subscription::whereBetween('created_at', [$monthStart, $monthEnd])
                        ->sum('recurring_amount');
        $mrrLost  = (float) Subscription::whereIn('status', self::CANCELLED_STATUSES)
                        ->whereBetween('terminated_at', [$monthStart, $monthEnd])
                        ->sum('recurring_amount');
        $netNewMrr = $mrrAdded - $mrrLost;

        // ── Churn % for current month ─────────────────────────────────────────
        // Denominator: subs that were active at the start of the month.
        $activeAtMonthStart = Subscription::where('created_at', '<', $monthStart)
            ->where(function ($q) use ($monthStart) {
                $q->whereNull('terminated_at')
                  ->orWhere('terminated_at', '>=', $monthStart);
            })->count();
        $terminatedThisMonth = Subscription::whereIn('status', self::CANCELLED_STATUSES)
            ->whereBetween('terminated_at', [$monthStart, $monthEnd])
            ->count();
        $churnPct = $activeAtMonthStart > 0
            ? round(($terminatedThisMonth / $activeAtMonthStart) * 100, 1)
            : 0.0;

        // ── Recovery rate (rolling 30d) ───────────────────────────────────────
        $thirtyDaysAgo   = $now->copy()->subDays(30);
        $failedCount30d  = SubscriptionEvent::where('event_type', 'payment_failed')
                            ->where('created_at', '>=', $thirtyDaysAgo)->count();
        $recoveredCount30d = SubscriptionEvent::where('event_type', 'payment_recovered')
                            ->where('created_at', '>=', $thirtyDaysAgo)->count();
        $recoveryRate = $failedCount30d > 0
            ? round(($recoveredCount30d / $failedCount30d) * 100, 1)
            : null;

        // ── ARPU ──────────────────────────────────────────────────────────────
        $arpu = $activeCount > 0 ? round($mrr / $activeCount, 2) : 0.0;

        // ── Avg subscription lifetime (completed subs only) ───────────────────
        $avgLifetimeDays = (float) Subscription::whereIn('status', self::CANCELLED_STATUSES)
            ->whereNotNull('terminated_at')
            ->whereNotNull('subscribed_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(DAY, subscribed_at, terminated_at)) as avg_days')
            ->value('avg_days');
        $avgLifetimeDays = $avgLifetimeDays ? round($avgLifetimeDays, 1) : 0.0;

        return [
            'total_customers'       => $totalCustomers,
            'active'                => $activeCount,
            'past_due'              => $pastDueCount,
            'cancelled'             => $cancelledCount,
            'at_risk'               => $atRisk,
            'mrr'                   => $mrr,
            'month_initial_revenue' => $monthInitialRevenue,
            'new_subs_this_month'   => $newSubsThisMonth,
            'total_leads'           => $totalLeads,
            'conversion_pct'        => $conversionPct,

            // New KPIs
            'revenue_this_month' => $revenueThisMonth,
            'revenue_last_month' => $revenueLastMonth,
            'revenue_delta_pct'  => $revenueDeltaPct,
            'mrr_added'          => $mrrAdded,
            'mrr_lost'           => $mrrLost,
            'net_new_mrr'        => $netNewMrr,
            'churn_pct'          => $churnPct,
            'recovery_rate'      => $recoveryRate,
            'arpu'               => $arpu,
            'avg_lifetime_days'  => $avgLifetimeDays,
        ];
    }

    /**
     * Sum of captured payments (initial + recurring) minus refund/void payments,
     * all within a single window. Pulls from the payments table.
     */
    private function revenueCollectedBetween(Carbon $start, Carbon $end): float
    {
        $captured = (float) Payment::whereBetween('charged_at', [$start, $end])
            ->whereIn('type', ['initial', 'recurring'])
            ->where('status', 'captured')
            ->sum('amount');

        $refunded = (float) Payment::whereBetween('charged_at', [$start, $end])
            ->whereIn('type', ['refund', 'void'])
            ->sum('amount');

        return max(0.0, $captured - $refunded);
    }

    /**
     * Operational health signals for the automation itself.
     * Dashboard widget + sanity check for the payment ops team.
     */
    private function buildOperationalHealth(): array
    {
        $now       = Carbon::now();
        $dayStart  = $now->copy()->startOfDay();
        $hourAgo   = $now->copy()->subHour();

        $lastWebhookAt = WebhookEvent::max('received_at');
        $lastWebhookAt = $lastWebhookAt ? Carbon::parse($lastWebhookAt) : null;

        $byTypeToday = WebhookEvent::whereBetween('received_at', [$dayStart, $now])
            ->select('event_type', DB::raw('COUNT(*) as cnt'))
            ->groupBy('event_type')
            ->pluck('cnt', 'event_type')
            ->toArray();

        $totalWebhooksToday = array_sum($byTypeToday);
        $totalWebhooksLastHour = WebhookEvent::whereBetween('received_at', [$hourAgo, $now])->count();

        $sigOkToday     = WebhookEvent::whereBetween('received_at', [$dayStart, $now])
                            ->where('signature_valid', true)->count();
        $sigInvalidToday = WebhookEvent::whereBetween('received_at', [$dayStart, $now])
                            ->where('signature_valid', false)->count();
        $sigUnverifiedToday = WebhookEvent::whereBetween('received_at', [$dayStart, $now])
                            ->whereNull('signature_valid')->count();

        // Scheduler heartbeats — file mtime on the log files
        $syncLog      = storage_path('logs/subs-sync.log');
        $terminateLog = storage_path('logs/subs-terminate.log');
        $lastSyncAt      = file_exists($syncLog)      ? Carbon::createFromTimestamp(filemtime($syncLog))      : null;
        $lastTerminateAt = file_exists($terminateLog) ? Carbon::createFromTimestamp(filemtime($terminateLog)) : null;

        return [
            'last_webhook_at'        => $lastWebhookAt,
            'webhooks_today_total'   => $totalWebhooksToday,
            'webhooks_last_hour'     => $totalWebhooksLastHour,
            'webhooks_today_by_type' => $byTypeToday,
            'signature_ok_today'        => $sigOkToday,
            'signature_invalid_today'   => $sigInvalidToday,
            'signature_unverified_today'=> $sigUnverifiedToday,
            'enforce_signature'      => (bool) config('services.authorize_net.webhook_enforce_signature', false),
            'last_sync_at'           => $lastSyncAt,
            'last_terminate_at'      => $lastTerminateAt,
        ];
    }

    /**
     * MRR + churn + customer count broken out by plan_key.
     */
    /**
     * Plan keys to report on: the live catalog (config/plans.php) plus any
     * legacy keys still present in the DB (silver/gold/platinum) so history
     * is never lost.
     */
    private function planKeyList(): array
    {
        $catalog = config('plans.plans', []);
        $keys    = array_keys($catalog);

        $legacy = Subscription::query()
            ->select('plan_key')
            ->distinct()
            ->pluck('plan_key')
            ->filter()
            ->all();

        foreach ($legacy as $lk) {
            if (!in_array($lk, $keys, true)) {
                $keys[] = $lk;
            }
        }
        return $keys;
    }

    private function planLabel(string $key): string
    {
        return config("plans.plans.$key.label", ucfirst($key));
    }

    /** key → label + chart colour, for the stacked signups chart. */
    private function planSeriesMeta(): array
    {
        $palette = ['#22c55e', '#f97316', '#dc2626', '#2563eb', '#94a3b8', '#b8a449', '#475569', '#0ea5e9'];
        $meta = [];
        foreach ($this->planKeyList() as $i => $key) {
            $meta[] = [
                'key'   => $key,
                'label' => $this->planLabel($key),
                'color' => $palette[$i % count($palette)],
            ];
        }
        return $meta;
    }

    private function buildPlanMix(): array
    {
        $plans = $this->planKeyList();
        $out = [];
        foreach ($plans as $key) {
            $label = $this->planLabel($key);
            $active    = Subscription::where('plan_key', $key)->where('status', 'active')->count();
            $pastDue   = Subscription::where('plan_key', $key)->where('status', 'past_due')->count();
            $cancelled = Subscription::where('plan_key', $key)->whereIn('status', self::CANCELLED_STATUSES)->count();
            $total     = $active + $pastDue + $cancelled;
            $mrr       = (float) Subscription::where('plan_key', $key)
                             ->whereIn('status', self::MRR_STATUSES)
                             ->sum('recurring_amount');
            $churnPct  = $total > 0 ? round(($cancelled / $total) * 100, 1) : 0.0;

            $out[] = [
                'key'       => $key,
                'label'     => $label,
                'active'    => $active,
                'past_due'  => $pastDue,
                'cancelled' => $cancelled,
                'total'     => $total,
                'mrr'       => $mrr,
                'churn_pct' => $churnPct,
            ];
        }
        return $out;
    }

    /**
     * Grace-period countdown buckets for the At-Risk page.
     */
    private function buildGracePeriodBuckets(): array
    {
        $now = Carbon::now();
        $bucket = fn ($from, $to) => Subscription::where('status', 'past_due')
            ->whereNotNull('grace_period_ends_at')
            ->where('grace_period_ends_at', '>', $from)
            ->where('grace_period_ends_at', '<=', $to)
            ->orderBy('grace_period_ends_at')
            ->get();

        return [
            'next_24h' => $bucket($now, $now->copy()->addDay()),
            'next_48h' => $bucket($now->copy()->addDay(), $now->copy()->addDays(2)),
            'next_7d'  => $bucket($now->copy()->addDays(2), $now->copy()->addDays(7)),
            'overdue'  => Subscription::where('status', 'past_due')
                ->whereNotNull('grace_period_ends_at')
                ->where('grace_period_ends_at', '<=', $now)
                ->orderBy('grace_period_ends_at')
                ->get(),
        ];
    }

    private function buildMrrSeries(): array
    {
        $series = [];
        $cursor = Carbon::now()->startOfMonth()->subMonths(11);
        for ($i = 0; $i < 12; $i++) {
            $monthEnd = $cursor->copy()->endOfMonth();
            $mrr = (float) Subscription::where('created_at', '<=', $monthEnd)
                ->where(function ($q) use ($monthEnd) {
                    $q->whereNull('terminated_at')
                      ->orWhere('terminated_at', '>', $monthEnd);
                })
                ->sum('recurring_amount');
            $series[] = ['label' => $cursor->format('M Y'), 'value' => $mrr];
            $cursor->addMonth();
        }
        return $series;
    }

    private function buildNewSubsSeries(): array
    {
        $start = Carbon::now()->startOfMonth()->subMonths(11);
        $rows = Subscription::where('created_at', '>=', $start)
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as ym"),
                'plan_key',
                DB::raw('COUNT(*) as cnt')
            )
            ->groupBy('ym', 'plan_key')
            ->get();

        $planKeys = $this->planKeyList();

        $months = [];
        $cursor = $start->copy();
        for ($i = 0; $i < 12; $i++) {
            $row = ['label' => $cursor->format('M Y')];
            foreach ($planKeys as $pk) { $row[$pk] = 0; }
            $months[$cursor->format('Y-m')] = $row;
            $cursor->addMonth();
        }
        foreach ($rows as $r) {
            if (! isset($months[$r->ym])) { continue; }
            $plan = in_array($r->plan_key, $planKeys, true) ? $r->plan_key : ($planKeys[0] ?? 'unknown');
            if (!isset($months[$r->ym][$plan])) { $months[$r->ym][$plan] = 0; }
            $months[$r->ym][$plan] += (int) $r->cnt;
        }
        return array_values($months);
    }

    private function buildReferralBreakdown(): array
    {
        $breakdown = [];
        foreach (array_merge(self::REFERRAL_CODES, [null]) as $code) {
            $label = $code ?? 'DIRECT';
            $q = Subscription::query();
            $code === null ? $q->whereNull('referral_code') : $q->where('referral_code', $code);
            $breakdown[] = [
                'code'             => $label,
                'total_signups'    => (clone $q)->count(),
                'active'           => (clone $q)->where('status', 'active')->count(),
                'cancelled'        => (clone $q)->whereIn('status', self::CANCELLED_STATUSES)->count(),
                'initial_revenue'  => (float) (clone $q)->sum('amount'),
                'mrr_contribution' => (float) (clone $q)->whereIn('status', self::MRR_STATUSES)->sum('recurring_amount'),
            ];
        }
        return $breakdown;
    }

    /**
     * Build activity feed — chronological list of events with display metadata.
     */
    private function buildActivityFeed(int $limit = 20): array
    {
        $events = SubscriptionEvent::with('subscription:id,first_name,last_name,email,plan_label,recurring_amount')
                    ->orderByDesc('created_at')
                    ->limit($limit)
                    ->get();

        $feed = [];
        foreach ($events as $ev) {
            if (! $ev->subscription) { continue; }

            $sub = $ev->subscription;
            $name = trim($sub->first_name . ' ' . $sub->last_name);
            $amount = number_format((float) $sub->recurring_amount, 0);

            $meta = match ($ev->event_type) {
                'payment_failed' => [
                    'icon'    => '⚠️',
                    'tone'    => 'amber',
                    'title'   => "{$name} — payment failed",
                    'detail'  => "{$sub->plan_label} · \${$amount}/mo",
                ],
                'payment_recovered' => [
                    'icon'    => '✅',
                    'tone'    => 'green',
                    'title'   => "{$name} — payment recovered",
                    'detail'  => "{$sub->plan_label} · \${$amount}/mo · back to active",
                ],
                'terminated' => [
                    'icon'    => '❌',
                    'tone'    => 'red',
                    'title'   => "{$name} — cancelled",
                    'detail'  => "{$sub->plan_label} · lost \${$amount}/mo MRR",
                ],
                'manual_note' => [
                    'icon'    => '📝',
                    'tone'    => 'slate',
                    'title'   => "{$name} — {$ev->note}",
                    'detail'  => $sub->plan_label,
                ],
                default => [
                    'icon'    => '•',
                    'tone'    => 'slate',
                    'title'   => "{$name} — {$ev->event_type}",
                    'detail'  => $sub->plan_label,
                ],
            };

            $feed[] = array_merge($meta, [
                'id'              => $ev->id,
                'subscription_id' => $sub->id,
                'when'            => $ev->created_at,
                'when_human'      => $ev->created_at->diffForHumans(),
            ]);
        }

        return $feed;
    }
}
