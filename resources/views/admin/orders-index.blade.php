@extends('admin.layout')

@section('title', 'Checkout Orders')

@section('content')

{{-- ─── Status tabs ───────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-200 p-4 mb-4">
  <div class="flex items-center justify-between mb-3">
    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Commas checkout attempts</div>
    <div class="text-xs text-gray-400">Pending = started checkout, no confirmed payment (abandoned carts are normal)</div>
  </div>
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
    @php
      $current = $filters['status'] ?? '';
      $tabs = [
        ''         => ['label' => 'All',           'count' => $tabCounts['total'],    'tone' => 'slate'],
        'paid'     => ['label' => 'Paid',          'count' => $tabCounts['paid'],     'tone' => 'green'],
        'pending'  => ['label' => 'Pending',       'count' => $tabCounts['pending'],  'tone' => 'amber'],
        'mismatch' => ['label' => 'Needs review',  'count' => $tabCounts['mismatch'], 'tone' => 'red'],
      ];
    @endphp

    @foreach ($tabs as $key => $t)
      @php
        $isActive = $current === $key;
        $activeCls = match($t['tone']) {
          'green' => 'border-green-500 bg-green-50',
          'amber' => 'border-amber-500 bg-amber-50',
          'red'   => 'border-red-500 bg-red-50',
          default => 'border-olive bg-olive/5',
        };
        $numTone = match($t['tone']) {
          'green' => 'text-green-700',
          'amber' => 'text-amber-700',
          'red'   => 'text-red-700',
          default => 'text-olive-dark',
        };
      @endphp
      <a href="{{ route('admin.orders', array_merge(request()->except('status','page'), $key ? ['status' => $key] : [])) }}"
         class="block rounded-lg border-2 transition p-3 text-center {{ $isActive ? $activeCls : 'border-gray-200 hover:border-gray-300 bg-white' }}">
        <div class="text-2xl font-bold {{ $numTone }}">{{ number_format($t['count']) }}</div>
        <div class="text-xs font-medium text-gray-600 mt-0.5">{{ $t['label'] }}</div>
      </a>
    @endforeach
  </div>
</div>

@if (($tabCounts['mismatch'] ?? 0) > 0)
  <div class="bg-red-50 border-2 border-red-300 rounded-xl p-4 mb-4 text-sm text-red-900">
    <strong>{{ $tabCounts['mismatch'] }} paid {{ \Illuminate\Support\Str::plural('order', $tabCounts['mismatch']) }} need review.</strong>
    The customer paid, but the product or amount didn't match the plan they chose, so no enrollment, GHL webhook or onboarding was triggered.
    Check the payment in the Commas dashboard, then enroll or refund the customer manually.
  </div>
@endif

{{-- ─── Filter bar ─────────────────────────────────────────────────────────── --}}
<form method="GET" action="{{ route('admin.orders') }}" class="bg-white rounded-xl border border-gray-200 p-4 mb-4">
  <input type="hidden" name="status" value="{{ $filters['status'] ?? '' }}">
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
    <div class="col-span-2">
      <label class="text-xs font-medium text-gray-600">Search</label>
      <input type="text" name="q" value="{{ $filters['q'] ?? '' }}"
             placeholder="Name, email, phone, invoice, ORD- id…"
             class="mt-1 w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent outline-none">
    </div>
    <div>
      <label class="text-xs font-medium text-gray-600">Plan</label>
      <select name="plan" class="mt-1 w-full text-sm px-3 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-gold">
        <option value="">All</option>
        @foreach (config('plans.plans') as $key => $plan)
          <option value="{{ $key }}" @selected(($filters['plan'] ?? '') === $key)>{{ $plan['label'] }}</option>
        @endforeach
      </select>
    </div>
    <div class="flex items-end gap-2 justify-end">
      <a href="{{ route('admin.orders') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-ink">Reset</a>
      <button type="submit" class="px-4 py-2 text-sm font-semibold bg-olive-dark text-paper rounded-lg hover:bg-olive transition">Apply</button>
    </div>
  </div>
</form>

{{-- ─── Results table ──────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
  <div class="px-5 py-3 border-b border-gray-200 text-sm text-gray-600">
    Showing <span class="font-medium text-ink">{{ $orders->firstItem() ?? 0 }}–{{ $orders->lastItem() ?? 0 }}</span>
    of <span class="font-medium text-ink">{{ number_format($orders->total()) }}</span>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="text-xs uppercase text-gray-500 bg-gray-50 border-b border-gray-200">
        <tr>
          <th class="text-left py-2.5 px-5 font-medium">Started</th>
          <th class="text-left py-2.5 px-3 font-medium">Customer</th>
          <th class="text-left py-2.5 px-3 font-medium">Plan</th>
          <th class="text-right py-2.5 px-3 font-medium">Amount</th>
          <th class="text-left py-2.5 px-3 font-medium">Status</th>
          <th class="text-left py-2.5 px-3 font-medium">Confirmed</th>
          <th class="text-left py-2.5 px-3 font-medium">Invoice</th>
          <th class="text-left py-2.5 px-5 font-medium">Commas ID</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse ($orders as $order)
          @php
            $statusCls = match($order->status) {
              'paid'     => 'bg-green-100 text-green-800',
              'mismatch' => 'bg-red-100 text-red-800',
              default    => 'bg-amber-100 text-amber-800',
            };
            $statusLabel = match($order->status) {
              'paid'     => 'Paid',
              'mismatch' => 'Needs review',
              default    => 'Pending',
            };
          @endphp
          <tr class="hover:bg-gray-50 align-top {{ $order->subscription_id ? 'cursor-pointer' : '' }}"
              @if ($order->subscription_id) onclick="window.location='{{ route('admin.subscription.show', $order->subscription_id) }}'" @endif>
            <td class="py-3 px-5 text-xs text-gray-500">
              <div>{{ $order->created_at->format('M j, Y') }}</div>
              <div class="text-gray-400">{{ $order->created_at->format('g:i A') }}</div>
            </td>
            <td class="py-3 px-3">
              <div class="font-medium text-ink">{{ $order->fullName() }}</div>
              <div class="text-xs text-gray-500">{{ $order->email }}</div>
              <div class="text-xs text-gray-400">{{ $order->phone }}</div>
            </td>
            <td class="py-3 px-3 text-xs text-gray-700">
              {{ $order->plan_label }}
              @if ($order->referral_code)
                <div class="mt-1"><span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-semibold bg-gold/20 text-olive-dark">REF {{ $order->referral_code }}</span></div>
              @endif
            </td>
            <td class="py-3 px-3 text-right font-semibold text-ink">
              ${{ number_format($order->paid_amount ?? $order->amount, 2) }}
              @if ($order->paid_amount !== null && (float) $order->paid_amount !== (float) $order->amount)
                <div class="text-[11px] font-normal text-red-700">expected ${{ number_format($order->amount, 2) }}</div>
              @endif
            </td>
            <td class="py-3 px-3">
              <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $statusCls }}">{{ $statusLabel }}</span>
              @if ($order->mismatch_reason)
                <div class="text-[11px] text-red-700 mt-1 max-w-[220px]">{{ $order->mismatch_reason }}</div>
              @endif
            </td>
            <td class="py-3 px-3 text-xs text-gray-500">
              @if ($order->paid_at)
                <div>{{ $order->paid_at->format('M j, g:i A') }}</div>
                <div class="text-gray-400">via {{ $order->confirmed_via }}</div>
              @else
                —
              @endif
            </td>
            <td class="py-3 px-3 text-xs text-gray-500 font-mono">{{ $order->invoice_number }}</td>
            <td class="py-3 px-5 text-xs text-gray-500 font-mono">
              {{ $order->commas_payment_id ?? $order->commas_transaction_id ?? '—' }}
            </td>
          </tr>
        @empty
          <tr><td colspan="8" class="py-10 text-center text-gray-400">No checkout orders yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="px-5 py-3 border-t border-gray-200">
    {{ $orders->links() }}
  </div>
</div>

@endsection
