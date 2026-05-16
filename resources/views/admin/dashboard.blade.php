@extends('admin.layout')

@section('title', 'Dashboard')

@section('content')

{{-- ─── KPI row ────────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
  <x-kpi label="Total Customers" :value="number_format($kpis['total_customers'])" sub="all-time signups" tone="slate" />
  <x-kpi label="Active" :value="number_format($kpis['active'])" sub="paying customers" tone="green" />
  <x-kpi label="MRR" :value="'$' . number_format($kpis['mrr'], 0)" sub="monthly recurring" tone="gold" />
  <x-kpi label="At Risk" :value="number_format($kpis['at_risk'])" sub="in grace period" :tone="$kpis['at_risk'] > 0 ? 'amber' : 'slate'" />
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
  <x-kpi label="Past Due" :value="number_format($kpis['past_due'])" sub="failed payments" tone="amber" />
  <x-kpi label="Cancelled" :value="number_format($kpis['cancelled'])" sub="terminated subs" tone="red" />
  <x-kpi label="New This Month" :value="number_format($kpis['new_subs_this_month'])" :sub="'$' . number_format($kpis['month_initial_revenue'], 0) . ' initial revenue'" tone="slate" />
  <x-kpi label="Lead Conversion" :value="$kpis['conversion_pct'] . '%'" :sub="number_format($kpis['total_leads']) . ' total leads'" tone="slate" />
</div>

{{-- ─── Revenue + MRR movement row ─────────────────────────────────────────── --}}
@php
  $deltaPct   = $kpis['revenue_delta_pct'];
  $deltaLabel = $deltaPct === null
      ? 'no comparable last month'
      : ($deltaPct >= 0 ? '+' . $deltaPct . '% vs last month' : $deltaPct . '% vs last month');
  $revTone = $deltaPct === null ? 'slate' : ($deltaPct >= 0 ? 'green' : 'red');
  $netMrrTone = $kpis['net_new_mrr'] >= 0 ? 'green' : 'red';
  $netMrrLabel = ($kpis['net_new_mrr'] >= 0 ? '+' : '') . '$' . number_format($kpis['net_new_mrr'], 0);
  $churnTone  = $kpis['churn_pct'] >= 5 ? 'red' : ($kpis['churn_pct'] >= 2 ? 'amber' : 'green');
  $recovLabel = $kpis['recovery_rate'] === null ? 'no failures' : $kpis['recovery_rate'] . '%';
  $recovTone  = $kpis['recovery_rate'] === null ? 'slate' : ($kpis['recovery_rate'] >= 50 ? 'green' : 'amber');
@endphp
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
  <x-kpi label="Cash This Month" :value="'$' . number_format($kpis['revenue_this_month'], 0)" :sub="$deltaLabel" :tone="$revTone" />
  <x-kpi label="Net New MRR" :value="$netMrrLabel" :sub="'+$' . number_format($kpis['mrr_added'], 0) . ' / -$' . number_format($kpis['mrr_lost'], 0)" :tone="$netMrrTone" />
  <x-kpi label="Churn (this month)" :value="$kpis['churn_pct'] . '%'" sub="monthly attrition" :tone="$churnTone" />
  <x-kpi label="Recovery Rate (30d)" :value="$recovLabel" sub="past_due → active" :tone="$recovTone" />
</div>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
  <x-kpi label="ARPU" :value="'$' . number_format($kpis['arpu'], 0)" sub="avg / active customer" tone="slate" />
  <x-kpi label="Avg Lifetime" :value="number_format($kpis['avg_lifetime_days'], 0) . ' days'" sub="for churned subs" tone="slate" />
  <x-kpi label="Cash Last Month" :value="'$' . number_format($kpis['revenue_last_month'], 0)" sub="initial + recurring − refunds" tone="slate" />
  <x-kpi label="Webhooks Today" :value="number_format($health['webhooks_today_total'])" :sub="number_format($health['webhooks_last_hour']) . ' in last hour'" tone="slate" />
</div>

{{-- ─── Operational Health widget ──────────────────────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-6">
  <div class="px-5 py-3 border-b border-gray-200">
    <h2 class="font-semibold text-ink">System Health</h2>
  </div>
  <div class="grid grid-cols-1 lg:grid-cols-4 gap-0 divide-y lg:divide-y-0 lg:divide-x divide-gray-100">
    <div class="p-4">
      <div class="text-xs text-gray-500 uppercase tracking-wide">Last Auth.net Webhook</div>
      <div class="text-lg font-semibold mt-1 {{ $health['last_webhook_at'] && $health['last_webhook_at']->gt(now()->subDay()) ? 'text-green-700' : 'text-amber-700' }}">
        {{ $health['last_webhook_at'] ? $health['last_webhook_at']->diffForHumans() : 'never' }}
      </div>
      <div class="text-xs text-gray-500 mt-1">
        {{ $health['last_webhook_at']?->format('M j, g:i A') ?? 'no webhooks seen yet' }}
      </div>
    </div>
    <div class="p-4">
      <div class="text-xs text-gray-500 uppercase tracking-wide">Last subs:sync</div>
      <div class="text-lg font-semibold mt-1 {{ $health['last_sync_at'] && $health['last_sync_at']->gt(now()->subHours(2)) ? 'text-green-700' : 'text-amber-700' }}">
        {{ $health['last_sync_at'] ? $health['last_sync_at']->diffForHumans() : 'never' }}
      </div>
      <div class="text-xs text-gray-500 mt-1">
        {{ $health['last_sync_at']?->format('M j, g:i A') ?? 'hourly sync pending' }}
      </div>
    </div>
    <div class="p-4">
      <div class="text-xs text-gray-500 uppercase tracking-wide">Last Grace-Period Termination</div>
      <div class="text-lg font-semibold mt-1 text-gray-700">
        {{ $health['last_terminate_at'] ? $health['last_terminate_at']->diffForHumans() : 'never' }}
      </div>
      <div class="text-xs text-gray-500 mt-1">
        {{ $health['last_terminate_at']?->format('M j, g:i A') ?? 'daily 2 AM run' }}
      </div>
    </div>
    <div class="p-4">
      <div class="text-xs text-gray-500 uppercase tracking-wide">Signature Verification (today)</div>
      <div class="text-lg font-semibold mt-1">
        <span class="text-green-700">{{ $health['signature_ok_today'] }}</span>
        <span class="text-gray-400 text-sm">OK</span>
        @if ($health['signature_invalid_today'] > 0)
          · <span class="text-red-700">{{ $health['signature_invalid_today'] }}</span> <span class="text-gray-400 text-sm">INVALID</span>
        @endif
        @if ($health['signature_unverified_today'] > 0)
          · <span class="text-gray-500">{{ $health['signature_unverified_today'] }}</span> <span class="text-gray-400 text-sm">N/A</span>
        @endif
      </div>
      <div class="text-xs text-gray-500 mt-1">
        enforcement is <span class="font-medium {{ $health['enforce_signature'] ? 'text-green-700' : 'text-amber-700' }}">{{ $health['enforce_signature'] ? 'ON' : 'OFF (log-only)' }}</span>
      </div>
    </div>
  </div>
  @if (!empty($health['webhooks_today_by_type']))
    <div class="px-5 py-3 border-t border-gray-100 text-xs text-gray-600">
      <span class="uppercase text-gray-500 tracking-wide">Today by event:</span>
      @foreach ($health['webhooks_today_by_type'] as $type => $cnt)
        <span class="inline-block mr-3 mt-1">
          <span class="font-mono text-[11px] text-gray-500">{{ str_replace('net.authorize.', '', $type) }}</span>
          <span class="font-semibold text-ink">{{ $cnt }}</span>
        </span>
      @endforeach
      <a href="{{ route('admin.webhooks') }}" class="ml-2 text-gold hover:underline">view full webhook log →</a>
    </div>
  @endif
</div>

{{-- ─── At-Risk Alert (only shows if needed) ───────────────────────────────── --}}
@if ($atRiskCount > 0)
<div class="bg-amber-50 border-2 border-amber-400 rounded-xl p-4 mb-6">
  <div class="flex items-start gap-3">
    <div class="text-2xl">⚠️</div>
    <div class="flex-1">
      <div class="text-sm font-semibold text-amber-900">
        {{ $atRiskCount }} {{ \Illuminate\Support\Str::plural('customer', $atRiskCount) }} at risk right now
      </div>
      <div class="text-xs text-amber-800 mt-1">
        @foreach ($atRiskPreview as $s)
          <span class="inline-block mr-3">{{ $s->first_name }} {{ $s->last_name }} (${{ number_format($s->recurring_amount, 0) }}/mo)</span>
        @endforeach
      </div>
    </div>
    <a href="{{ route('admin.at-risk') }}"
       class="px-3 py-1.5 text-xs font-semibold bg-amber-500 text-white rounded-lg hover:bg-amber-600 whitespace-nowrap">
      View All →
    </a>
  </div>
</div>
@endif

{{-- ─── Charts row ─────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
  <div class="bg-white rounded-xl border border-gray-200 p-5">
    <div class="flex items-center justify-between mb-3">
      <h2 class="font-semibold text-ink">MRR Trend (12 Months)</h2>
    </div>
    <div class="relative w-full" style="height: 280px; max-height: 400px;">
      <canvas id="mrrChart"></canvas>
    </div>
  </div>

  <div class="bg-white rounded-xl border border-gray-200 p-5">
    <div class="flex items-center justify-between mb-3">
      <h2 class="font-semibold text-ink">New Signups by Plan</h2>
    </div>
    <div class="relative w-full" style="height: 280px; max-height: 400px;">
      <canvas id="signupsChart"></canvas>
    </div>
  </div>
</div>

{{-- ─── Activity Feed (full width) ─────────────────────────────────────────── --}}
<div class="mb-6">
  @include('admin.partials.activity-feed', ['events' => $activityFeed])
</div>

{{-- ─── Plan Mix + Recent Payments ─────────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">

  <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="px-5 py-3 border-b border-gray-200">
      <h2 class="font-semibold text-ink">Plan Mix</h2>
    </div>
    <table class="w-full text-sm">
      <thead class="text-xs uppercase text-gray-500 bg-gray-50 border-b border-gray-200">
        <tr>
          <th class="text-left py-2.5 px-5 font-medium">Plan</th>
          <th class="text-right py-2.5 px-3 font-medium">Active</th>
          <th class="text-right py-2.5 px-3 font-medium">Past Due</th>
          <th class="text-right py-2.5 px-3 font-medium">Cancelled</th>
          <th class="text-right py-2.5 px-3 font-medium">Churn</th>
          <th class="text-right py-2.5 px-5 font-medium">MRR</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @foreach ($planMix as $p)
          <tr class="hover:bg-gray-50">
            <td class="py-2.5 px-5 font-medium">{{ $p['label'] }}</td>
            <td class="py-2.5 px-3 text-right text-green-700">{{ $p['active'] }}</td>
            <td class="py-2.5 px-3 text-right text-amber-700">{{ $p['past_due'] }}</td>
            <td class="py-2.5 px-3 text-right text-red-700">{{ $p['cancelled'] }}</td>
            <td class="py-2.5 px-3 text-right text-gray-600">{{ $p['churn_pct'] }}%</td>
            <td class="py-2.5 px-5 text-right font-semibold">${{ number_format($p['mrr'], 0) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <div class="px-5 py-3 border-b border-gray-200">
      <h2 class="font-semibold text-ink">Recent Payments</h2>
    </div>
    <table class="w-full text-sm">
      <thead class="text-xs uppercase text-gray-500 bg-gray-50 border-b border-gray-200">
        <tr>
          <th class="text-left py-2.5 px-5 font-medium">Customer</th>
          <th class="text-left py-2.5 px-3 font-medium">Type</th>
          <th class="text-right py-2.5 px-3 font-medium">Amount</th>
          <th class="text-right py-2.5 px-5 font-medium">When</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse ($recentPayments as $pay)
          @php
            $sub = $pay->subscription;
            $isNeg = in_array($pay->type, ['refund', 'void'], true);
            $amtCls = $isNeg ? 'text-red-700' : 'text-ink';
            $typeCls = match($pay->type) {
              'initial'   => 'bg-slate-100 text-slate-700',
              'recurring' => 'bg-green-100 text-green-800',
              'refund'    => 'bg-red-100 text-red-800',
              'void'      => 'bg-gray-200 text-gray-700',
              default     => 'bg-gray-100 text-gray-700',
            };
          @endphp
          <tr class="hover:bg-gray-50 {{ $sub ? 'cursor-pointer' : '' }}"
              @if ($sub) onclick="window.location='{{ route('admin.subscription.show', $sub->id) }}'" @endif>
            <td class="py-2.5 px-5">
              @if ($sub)
                <div class="font-medium">{{ $sub->first_name }} {{ $sub->last_name }}</div>
                <div class="text-xs text-gray-500">{{ $sub->email }}</div>
              @else
                <div class="text-gray-400 text-xs">(unlinked — {{ $pay->invoice_number ?: 'no invoice' }})</div>
              @endif
            </td>
            <td class="py-2.5 px-3">
              <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $typeCls }}">{{ ucfirst($pay->type) }}</span>
            </td>
            <td class="py-2.5 px-3 text-right font-semibold {{ $amtCls }}">
              {{ $isNeg ? '-' : '' }}${{ number_format($pay->amount, 2) }}
            </td>
            <td class="py-2.5 px-5 text-right text-xs text-gray-500">
              {{ $pay->charged_at?->diffForHumans() ?? '—' }}
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="py-8 text-center text-gray-400 text-sm">
              No payments recorded yet.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- ─── Referrals summary ──────────────────────────────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-4">
  <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between">
    <h2 class="font-semibold text-ink">Referral Performance</h2>
    <a href="{{ route('admin.referrals') }}" class="text-xs text-gold hover:underline">Full breakdown →</a>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="text-xs uppercase text-gray-500 bg-gray-50 border-b border-gray-200">
        <tr>
          <th class="text-left py-2.5 px-5 font-medium">Code</th>
          <th class="text-right py-2.5 px-3 font-medium">Signups</th>
          <th class="text-right py-2.5 px-3 font-medium">Active</th>
          <th class="text-right py-2.5 px-3 font-medium">Cancelled</th>
          <th class="text-right py-2.5 px-5 font-medium">MRR</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @foreach ($referralBreakdown as $b)
          <tr class="hover:bg-gray-50">
            <td class="py-2.5 px-5">
              <span class="text-xs bg-gold/15 text-gold-dark px-2 py-0.5 rounded font-semibold">{{ $b['code'] }}</span>
            </td>
            <td class="py-2.5 px-3 text-right font-medium">{{ $b['total_signups'] }}</td>
            <td class="py-2.5 px-3 text-right text-green-700">{{ $b['active'] }}</td>
            <td class="py-2.5 px-3 text-right text-red-700">{{ $b['cancelled'] }}</td>
            <td class="py-2.5 px-5 text-right font-semibold">${{ number_format($b['mrr_contribution'], 0) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@push('scripts')
<script>
  // MRR Chart
  new Chart(document.getElementById('mrrChart'), {
    type: 'line',
    data: {
      labels: @json(array_column($mrrSeries, 'label')),
      datasets: [{
        label: 'MRR',
        data: @json(array_column($mrrSeries, 'value')),
        borderColor: '#b8a449',
        backgroundColor: 'rgba(184, 164, 73, 0.1)',
        fill: true,
        tension: 0.3,
        pointRadius: 3,
      }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: { y: { beginAtZero: true, ticks: { callback: v => '$' + v } } }
    }
  });

  // Signups Chart (stacked by plan)
  new Chart(document.getElementById('signupsChart'), {
    type: 'bar',
    data: {
      labels: @json(array_column($newSubsSeries, 'label')),
      datasets: [
        @foreach ($planSeriesMeta as $pm)
        { label: @json($pm['label']), data: @json(array_column($newSubsSeries, $pm['key'])), backgroundColor: @json($pm['color']) },
        @endforeach
      ]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: { legend: { position: 'bottom' } },
      scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true, ticks: { stepSize: 1 } } }
    }
  });
</script>
@endpush

@endsection
