@extends('admin.layout')

@section('title', 'Referral Performance')

@section('content')

<div class="grid lg:grid-cols-2 gap-4 mb-6">
  <div class="bg-white rounded-xl border border-gray-200 p-5">
    <h2 class="font-semibold text-ink mb-4">Signups by Referral Code</h2>
    <canvas id="signupsChart" height="160"></canvas>
  </div>
  <div class="bg-white rounded-xl border border-gray-200 p-5">
    <h2 class="font-semibold text-ink mb-4">MRR Contribution by Code</h2>
    <canvas id="mrrByCodeChart" height="160"></canvas>
  </div>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
  <div class="px-5 py-3 border-b border-gray-200">
    <h2 class="font-semibold text-ink">Full Breakdown</h2>
  </div>
  <table class="w-full text-sm">
    <thead class="text-xs uppercase text-gray-500 bg-gray-50 border-b border-gray-200">
      <tr>
        <th class="text-left py-3 px-5 font-medium">Code</th>
        <th class="text-right py-3 px-3 font-medium">Total Signups</th>
        <th class="text-right py-3 px-3 font-medium">Active</th>
        <th class="text-right py-3 px-3 font-medium">Cancelled</th>
        <th class="text-right py-3 px-3 font-medium">Churn %</th>
        <th class="text-right py-3 px-3 font-medium">Initial Revenue</th>
        <th class="text-right py-3 px-5 font-medium">MRR Contribution</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-100">
      @foreach ($breakdown as $row)
        @php
          $churn = $row['total_signups'] > 0
            ? round(($row['cancelled'] / $row['total_signups']) * 100, 1)
            : 0;
        @endphp
        <tr class="hover:bg-gray-50">
          <td class="py-3 px-5">
            @if ($row['code'] === 'DIRECT')
              <span class="inline-flex px-2 py-0.5 rounded text-xs bg-gray-100 text-gray-700 font-medium">Direct</span>
            @else
              <span class="inline-flex px-2 py-0.5 rounded text-xs bg-gold/15 text-gold-dark font-semibold">{{ $row['code'] }}</span>
            @endif
          </td>
          <td class="py-3 px-3 text-right font-medium">{{ number_format($row['total_signups']) }}</td>
          <td class="py-3 px-3 text-right text-green-700">{{ number_format($row['active']) }}</td>
          <td class="py-3 px-3 text-right text-red-600">{{ number_format($row['cancelled']) }}</td>
          <td class="py-3 px-3 text-right text-gray-700">{{ $churn }}%</td>
          <td class="py-3 px-3 text-right">${{ number_format($row['initial_revenue'], 2) }}</td>
          <td class="py-3 px-5 text-right font-semibold">${{ number_format($row['mrr_contribution'], 2) }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const breakdown = @json($breakdown);
    const labels    = breakdown.map(r => r.code);
    const gold      = '#c9a54a';
    const olive     = '#3d4020';

    new Chart(document.getElementById('signupsChart'), {
      type: 'bar',
      data: {
        labels,
        datasets: [
          { label: 'Active',    data: breakdown.map(r => r.active),    backgroundColor: '#10b981' },
          { label: 'Cancelled', data: breakdown.map(r => r.cancelled), backgroundColor: '#ef4444' },
        ],
      },
      options: {
        responsive: true, maintainAspectRatio: true,
        plugins: { legend: { position: 'bottom', labels: { boxWidth: 12, font: { size: 11 } } } },
        scales: { x: { stacked: true }, y: { stacked: true, beginAtZero: true, ticks: { precision: 0 } } },
      },
    });

    new Chart(document.getElementById('mrrByCodeChart'), {
      type: 'bar',
      data: {
        labels,
        datasets: [{
          label: 'MRR ($)',
          data: breakdown.map(r => r.mrr_contribution),
          backgroundColor: gold,
          borderColor: olive,
          borderWidth: 1,
        }],
      },
      options: {
        responsive: true, maintainAspectRatio: true,
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true, ticks: { callback: v => '$' + v.toLocaleString() } } },
      },
    });
  });
</script>

@endsection
