@extends('admin.layout')

@section('title', 'All Payments')

@section('content')

{{-- ─── Type tabs ─────────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-200 p-4 mb-4">
  <div class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-3">Payment type</div>
  <div class="grid grid-cols-2 sm:grid-cols-5 gap-2">
    @php
      $current = $filters['type'] ?? '';
      $tabs = [
        ''          => ['label' => 'All',       'count' => $tabCounts['total'],     'tone' => 'slate'],
        'initial'   => ['label' => 'Initial',   'count' => $tabCounts['initial'],   'tone' => 'slate'],
        'recurring' => ['label' => 'Recurring', 'count' => $tabCounts['recurring'], 'tone' => 'green'],
        'refund'    => ['label' => 'Refund',    'count' => $tabCounts['refund'],    'tone' => 'red'],
        'void'      => ['label' => 'Void',      'count' => $tabCounts['void'],      'tone' => 'amber'],
      ];
    @endphp

    @foreach ($tabs as $key => $t)
      @php
        $isActive = $current === $key;
        $activeCls = match($t['tone']) {
          'green' => 'border-green-500 bg-green-50',
          'amber' => 'border-amber-500 bg-amber-50',
          'red'   => 'border-red-500 bg-red-50',
          'slate' => 'border-olive bg-olive/5',
          default => 'border-gray-400 bg-gray-50',
        };
        $numTone = match($t['tone']) {
          'green' => 'text-green-700',
          'amber' => 'text-amber-700',
          'red'   => 'text-red-700',
          'slate' => 'text-olive-dark',
          default => 'text-gray-700',
        };
      @endphp
      <a href="{{ route('admin.payments', array_merge(request()->except('type','page'), $key ? ['type' => $key] : [])) }}"
         class="block rounded-lg border-2 transition p-3 text-center {{ $isActive ? $activeCls : 'border-gray-200 hover:border-gray-300 bg-white' }}">
        <div class="text-2xl font-bold {{ $numTone }}">{{ number_format($t['count']) }}</div>
        <div class="text-xs font-medium text-gray-600 mt-0.5">{{ $t['label'] }}</div>
      </a>
    @endforeach
  </div>
</div>

{{-- ─── Totals summary (reflects current filters) ─────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
  <div class="bg-white border border-gray-200 rounded-xl p-4">
    <div class="text-xs uppercase tracking-wide text-gray-500">Rows shown</div>
    <div class="text-2xl font-bold text-ink mt-1">{{ number_format($totals['count']) }}</div>
  </div>
  <div class="bg-white border border-gray-200 rounded-xl p-4">
    <div class="text-xs uppercase tracking-wide text-gray-500">Gross captured</div>
    <div class="text-2xl font-bold text-green-700 mt-1">${{ number_format($totals['gross'], 2) }}</div>
  </div>
  <div class="bg-white border border-gray-200 rounded-xl p-4">
    <div class="text-xs uppercase tracking-wide text-gray-500">Refund + voids</div>
    <div class="text-2xl font-bold text-red-700 mt-1">${{ number_format($totals['neg'], 2) }}</div>
  </div>
  <div class="bg-white border border-gray-200 rounded-xl p-4">
    <div class="text-xs uppercase tracking-wide text-gray-500">Net</div>
    <div class="text-2xl font-bold text-ink mt-1">${{ number_format($totals['net'], 2) }}</div>
  </div>
</div>

{{-- ─── Filter bar ─────────────────────────────────────────────────────────── --}}
<form method="GET" action="{{ route('admin.payments') }}"
      class="bg-white rounded-xl border border-gray-200 p-4 mb-4">
  <div class="grid grid-cols-2 lg:grid-cols-6 gap-3">

    <div class="col-span-2 lg:col-span-2">
      <label class="text-xs font-medium text-gray-600">Search</label>
      <input type="text" name="q" value="{{ $filters['q'] ?? '' }}"
             placeholder="Customer, email, invoice, txn id…"
             class="mt-1 w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent outline-none">
    </div>

    <div>
      <label class="text-xs font-medium text-gray-600">Type</label>
      <select name="type" class="mt-1 w-full text-sm px-3 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-gold">
        <option value="">All</option>
        <option value="initial"   @selected(($filters['type'] ?? '') === 'initial')>Initial</option>
        <option value="recurring" @selected(($filters['type'] ?? '') === 'recurring')>Recurring</option>
        <option value="refund"    @selected(($filters['type'] ?? '') === 'refund')>Refund</option>
        <option value="void"      @selected(($filters['type'] ?? '') === 'void')>Void</option>
      </select>
    </div>

    <div>
      <label class="text-xs font-medium text-gray-600">Status</label>
      <select name="status" class="mt-1 w-full text-sm px-3 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-gold">
        <option value="">All</option>
        <option value="captured" @selected(($filters['status'] ?? '') === 'captured')>Captured</option>
        <option value="refunded" @selected(($filters['status'] ?? '') === 'refunded')>Refunded</option>
        <option value="voided"   @selected(($filters['status'] ?? '') === 'voided')>Voided</option>
        <option value="failed"   @selected(($filters['status'] ?? '') === 'failed')>Failed</option>
      </select>
    </div>

    <div class="grid grid-cols-2 gap-2 col-span-2 lg:col-span-2">
      <div>
        <label class="text-xs font-medium text-gray-600">From</label>
        <input type="date" name="from" value="{{ $filters['from'] ?? '' }}"
               class="mt-1 w-full text-sm px-3 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-gold">
      </div>
      <div>
        <label class="text-xs font-medium text-gray-600">To</label>
        <input type="date" name="to" value="{{ $filters['to'] ?? '' }}"
               class="mt-1 w-full text-sm px-3 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-gold">
      </div>
    </div>

    <div class="flex items-end gap-2 col-span-2 lg:col-span-6 justify-end">
      <a href="{{ route('admin.payments') }}"
         class="px-4 py-2 text-sm text-gray-600 hover:text-ink">Reset</a>

      <a href="{{ route('admin.payments.csv', request()->query()) }}"
         class="px-4 py-2 text-sm font-medium bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
        Export CSV
      </a>

      <button type="submit"
              class="px-4 py-2 text-sm font-semibold bg-olive-dark text-paper rounded-lg hover:bg-olive transition">
        Apply
      </button>
    </div>
  </div>
</form>

{{-- ─── Results table ──────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
  <div class="flex items-center justify-between px-5 py-3 border-b border-gray-200 text-sm">
    <div class="text-gray-600">
      Showing <span class="font-medium text-ink">{{ $payments->firstItem() ?? 0 }}–{{ $payments->lastItem() ?? 0 }}</span>
      of <span class="font-medium text-ink">{{ number_format($payments->total()) }}</span>
    </div>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="text-xs uppercase text-gray-500 bg-gray-50 border-b border-gray-200">
        <tr>
          <th class="text-left py-2.5 px-5 font-medium">When</th>
          <th class="text-left py-2.5 px-3 font-medium">Customer</th>
          <th class="text-left py-2.5 px-3 font-medium">Plan</th>
          <th class="text-left py-2.5 px-3 font-medium">Type</th>
          <th class="text-left py-2.5 px-3 font-medium">Status</th>
          <th class="text-right py-2.5 px-3 font-medium">Amount</th>
          <th class="text-left py-2.5 px-3 font-medium">Invoice</th>
          <th class="text-left py-2.5 px-5 font-medium">Txn ID</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse ($payments as $pay)
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
            $statusCls = match($pay->status) {
              'captured' => 'bg-green-100 text-green-800',
              'refunded' => 'bg-red-100 text-red-800',
              'voided'   => 'bg-gray-200 text-gray-700',
              'failed'   => 'bg-red-100 text-red-800',
              default    => 'bg-gray-100 text-gray-700',
            };
          @endphp
          <tr class="hover:bg-gray-50 {{ $sub ? 'cursor-pointer' : '' }}"
              @if ($sub) onclick="window.location='{{ route('admin.subscription.show', $sub->id) }}'" @endif>
            <td class="py-3 px-5 text-xs text-gray-500">
              <div>{{ optional($pay->charged_at)->format('M j, Y') ?? '—' }}</div>
              <div class="text-gray-400">{{ optional($pay->charged_at)->format('g:i A') }}</div>
            </td>
            <td class="py-3 px-3">
              @if ($sub)
                <div class="font-medium text-ink">{{ $sub->first_name }} {{ $sub->last_name }}</div>
                <div class="text-xs text-gray-500">{{ $sub->email }}</div>
              @else
                <div class="text-gray-400 text-xs">(unlinked)</div>
              @endif
            </td>
            <td class="py-3 px-3 text-gray-700 text-xs">{{ $sub->plan_label ?? '—' }}</td>
            <td class="py-3 px-3">
              <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $typeCls }}">{{ ucfirst($pay->type) }}</span>
            </td>
            <td class="py-3 px-3">
              <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $statusCls }}">{{ ucfirst($pay->status) }}</span>
            </td>
            <td class="py-3 px-3 text-right font-semibold {{ $amtCls }}">
              {{ $isNeg ? '-' : '' }}${{ number_format($pay->amount, 2) }}
            </td>
            <td class="py-3 px-3 text-xs text-gray-500 font-mono">{{ $pay->invoice_number ?: '—' }}</td>
            <td class="py-3 px-5 text-xs text-gray-500 font-mono">{{ $pay->transaction_id ?: '—' }}</td>
          </tr>
        @empty
          <tr><td colspan="8" class="py-10 text-center text-gray-400">No payments match your filters.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="px-5 py-3 border-t border-gray-200">
    {{ $payments->links() }}
  </div>
</div>

@endsection
