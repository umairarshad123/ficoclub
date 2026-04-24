<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Models\Subscription;
use App\Models\SubscriptionEvent;
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
            'referralBreakdown' => $this->buildReferralBreakdown(),
            'recentSubs'        => Subscription::orderByDesc('created_at')->limit(10)->get(),
            'activityFeed'      => $this->buildActivityFeed(20),
            'atRiskCount'       => Subscription::where('status', 'past_due')->count(),
            'atRiskPreview'     => Subscription::where('status', 'past_due')
                                    ->orderBy('grace_period_ends_at')
                                    ->limit(5)
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
        $sub = Subscription::with(['events' => fn ($q) => $q->orderByDesc('created_at')])
                  ->findOrFail($id);
        return view('admin.subscription-show', ['sub' => $sub]);
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
        ]);
    }

    // ═════════════════════════════════════════════════════════════════════════
    // REFERRALS
    // ═════════════════════════════════════════════════════════════════════════
    public function referrals()
    {
        return view('admin.referrals', [
            'breakdown' => $this->buildReferralBreakdown(),
        ]);
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

        $months = [];
        $cursor = $start->copy();
        for ($i = 0; $i < 12; $i++) {
            $months[$cursor->format('Y-m')] = [
                'label'    => $cursor->format('M Y'),
                'silver'   => 0, 'gold' => 0, 'platinum' => 0,
            ];
            $cursor->addMonth();
        }
        foreach ($rows as $r) {
            if (! isset($months[$r->ym])) { continue; }
            $plan = in_array($r->plan_key, ['silver','gold','platinum'], true) ? $r->plan_key : 'silver';
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
