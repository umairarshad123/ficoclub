@extends('admin.layout')

@section('title', 'At-Risk Customers')

@section('content')

{{-- ─── Alert banner ───────────────────────────────────────────────────────── --}}
@if ($atRisk->isNotEmpty())
<div class="bg-amber-50 border-2 border-amber-400 rounded-xl p-4 mb-4">
  <div class="flex items-start gap-3">
    <div class="text-2xl">⚠️</div>
    <div class="flex-1">
      <div class="text-sm font-semibold text-amber-900">
        {{ $atRisk->count() }} {{ \Illuminate\Support\Str::plural('customer', $atRisk->count()) }} at risk —
        ${{ number_format($atRiskMrr, 0) }}/mo in jeopardy
      </div>
      <div class="text-xs text-amber-800 mt-1">
        These customers had a failed recurring payment. If their card isn't updated or retried within the grace period, the subscription will terminate automatically.
      </div>
    </div>
  </div>
</div>
@endif

{{-- ─── KPI cards ──────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
  <div class="bg-white rounded-xl border border-gray-200 p-4">
    <div class="text-xs text-gray-500 uppercase tracking-wide">At Risk Right Now</div>
    <div class="text-3xl font-bold text-amber-700 mt-1">{{ $atRisk->count() }}</div>
    <div class="text-xs text-gray-500 mt-1">in grace period</div>
  </div>
  <div class="bg-white rounded-xl border border-gray-200 p-4">
    <div class="text-xs text-gray-500 uppercase tracking-wide">MRR at Risk</div>
    <div class="text-3xl font-bold text-amber-700 mt-1">${{ number_format($atRiskMrr, 0) }}</div>
    <div class="text-xs text-gray-500 mt-1">recurring revenue</div>
  </div>
  <div class="bg-white rounded-xl border border-gray-200 p-4">
    <div class="text-xs text-gray-500 uppercase tracking-wide">Lost Last 30 Days</div>
    <div class="text-3xl font-bold text-red-700 mt-1">{{ $recentlyTerminated->count() }}</div>
    <div class="text-xs text-gray-500 mt-1">customers terminated</div>
  </div>
  <div class="bg-white rounded-xl border border-gray-200 p-4">
    <div class="text-xs text-gray-500 uppercase tracking-wide">Lost MRR (30d)</div>
    <div class="text-3xl font-bold text-red-700 mt-1">${{ number_format($lostMrr, 0) }}</div>
    <div class="text-xs text-gray-500 mt-1">gone from MRR</div>
  </div>
</div>

{{-- ─── At-Risk Table ──────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden mb-6">
  <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between">
    <h2 class="font-semibold text-ink">⚠️ Currently At Risk — Action Needed</h2>
    <span class="text-xs text-gray-500">{{ $atRisk->count() }} {{ \Illuminate\Support\Str::plural('customer', $atRisk->count()) }}</span>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="text-xs uppercase text-gray-500 bg-gray-50 border-b border-gray-200">
        <tr>
          <th class="text-left py-2.5 px-5 font-medium">Customer</th>
          <th class="text-left py-2.5 px-3 font-medium">Plan</th>
          <th class="text-right py-2.5 px-3 font-medium">MRR at Risk</th>
          <th class="text-center py-2.5 px-3 font-medium">Failed #</th>
          <th class="text-left py-2.5 px-3 font-medium">First Failed</th>
          <th class="text-left py-2.5 px-3 font-medium">Grace Ends</th>
          <th class="text-left py-2.5 px-3 font-medium">Urgency</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse ($atRisk as $s)
          @php
            $graceEnd = $s->grace_period_ends_at;
            $daysLeft = $graceEnd ? max(0, now()->diffInDays($graceEnd, false)) : null;
            $urgencyCls = 'bg-gray-100 text-gray-700';
            $urgencyTxt = '—';
            if ($daysLeft !== null) {
              if ($daysLeft <= 1)      { $urgencyCls = 'bg-red-100 text-red-800';     $urgencyTxt = 'CRITICAL'; }
              elseif ($daysLeft <= 3)  { $urgencyCls = 'bg-orange-100 text-orange-800'; $urgencyTxt = 'HIGH'; }
              elseif ($daysLeft <= 7)  { $urgencyCls = 'bg-amber-100 text-amber-800'; $urgencyTxt = 'MEDIUM'; }
              else                     { $urgencyCls = 'bg-blue-100 text-blue-800';   $urgencyTxt = 'LOW'; }
            }
          @endphp
          <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('admin.subscription.show', $s->id) }}'">
            <td class="py-3 px-5">
              <div class="font-medium text-ink">{{ $s->first_name }} {{ $s->last_name }}</div>
              <div class="text-xs text-gray-500">{{ $s->email }}</div>
              @if ($s->phone)
                <div class="text-xs text-gray-500">{{ $s->phone }}</div>
              @endif
            </td>
            <td class="py-3 px-3 text-gray-700">{{ $s->plan_label }}</td>
            <td class="py-3 px-3 text-right font-semibold text-amber-700">${{ number_format($s->recurring_amount, 2) }}</td>
            <td class="py-3 px-3 text-center">
              <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                {{ $s->failed_payment_count }}
              </span>
            </td>
            <td class="py-3 px-3 text-xs text-gray-600">
              {{ $s->first_failed_at ? $s->first_failed_at->format('M j, Y') : '—' }}
              @if ($s->first_failed_at)
                <div class="text-gray-400">{{ $s->first_failed_at->diffForHumans() }}</div>
              @endif
            </td>
            <td class="py-3 px-3 text-xs text-gray-600">
              {{ $graceEnd ? $graceEnd->format('M j, Y') : '—' }}
              @if ($daysLeft !== null)
                <div class="text-gray-400">{{ $daysLeft == 0 ? 'today' : $daysLeft . ' days left' }}</div>
              @endif
            </td>
            <td class="py-3 px-3">
              <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $urgencyCls }}">
                {{ $urgencyTxt }}
              </span>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="py-10 text-center">
              <div class="text-4xl mb-2">🎉</div>
              <div class="text-gray-600 font-medium">All clear — no customers currently at risk</div>
              <div class="text-xs text-gray-400 mt-1">Every active subscription is paying on time</div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

{{-- ─── Recently Terminated (context) ──────────────────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
  <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between">
    <h2 class="font-semibold text-ink">Recently Terminated (Last 30 Days)</h2>
    <span class="text-xs text-gray-500">{{ $recentlyTerminated->count() }} {{ \Illuminate\Support\Str::plural('customer', $recentlyTerminated->count()) }}</span>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="text-xs uppercase text-gray-500 bg-gray-50 border-b border-gray-200">
        <tr>
          <th class="text-left py-2.5 px-5 font-medium">Customer</th>
          <th class="text-left py-2.5 px-3 font-medium">Plan</th>
          <th class="text-right py-2.5 px-3 font-medium">Lost MRR</th>
          <th class="text-left py-2.5 px-3 font-medium">Was With Us</th>
          <th class="text-left py-2.5 px-3 font-medium">Terminated</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse ($recentlyTerminated as $s)
          <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('admin.subscription.show', $s->id) }}'">
            <td class="py-3 px-5">
              <div class="font-medium text-ink">{{ $s->first_name }} {{ $s->last_name }}</div>
              <div class="text-xs text-gray-500">{{ $s->email }}</div>
            </td>
            <td class="py-3 px-3 text-gray-700">{{ $s->plan_label }}</td>
            <td class="py-3 px-3 text-right font-medium text-red-700">-${{ number_format($s->recurring_amount, 0) }}</td>
            <td class="py-3 px-3 text-xs text-gray-600">
              @if ($s->subscribed_at && $s->terminated_at)
                {{ $s->subscribed_at->diffInDays($s->terminated_at) }} days
              @else
                —
              @endif
            </td>
            <td class="py-3 px-3 text-xs text-gray-500">
              {{ $s->terminated_at ? $s->terminated_at->format('M j, Y') : '—' }}
              @if ($s->terminated_at)
                <div class="text-gray-400">{{ $s->terminated_at->diffForHumans() }}</div>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="py-8 text-center text-gray-400">No terminations in the last 30 days.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@endsection
