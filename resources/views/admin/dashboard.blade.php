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

<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-6">
  <x-kpi label="Past Due" :value="number_format($kpis['past_due'])" sub="failed payments" tone="amber" />
  <x-kpi label="Cancelled" :value="number_format($kpis['cancelled'])" sub="terminated subs" tone="red" />
  <x-kpi label="New This Month" :value="number_format($kpis['new_subs_this_month'])" :sub="'$' . number_format($kpis['month_initial_revenue'], 0) . ' initial revenue'" tone="slate" />
  <x-kpi label="Lead Conversion" :value="$kpis['conversion_pct'] . '%'" :sub="number_format($kpis['total_leads']) . ' total leads'" tone="slate" />
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
    <canvas id="mrrChart" height="220"></canvas>
  </div>

  <div class="bg-white rounded-xl border border-gray-200 p-5">
    <div class="flex items-center justify-between mb-3">
      <h2 class="font-semibold text-ink">New Signups by Plan</h2>
    </div>
    <canvas id="signupsChart" height="220"></canvas>
  </div>
</div>

{{-- ─── Activity Feed (full width) ─────────────────────────────────────────── --}}
<div class="mb-6">
  @include('admin.partials.activity-feed', ['events' => $activityFeed])
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
        { label: 'Silver',   data: @json(array_column($newSubsSeries, 'silver')),   backgroundColor: '#94a3b8' },
        { label: 'Gold',     data: @json(array_column($newSubsSeries, 'gold')),     backgroundColor: '#b8a449' },
        { label: 'Platinum', data: @json(array_column($newSubsSeries, 'platinum')), backgroundColor: '#475569' },
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
