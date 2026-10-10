@extends('admin.layout')

@section('title', 'Commas Sales')

@php
  $money2 = fn ($v) => '$' . number_format((float) $v, 2);
@endphp

@section('content')

<div class="flex flex-col sm:flex-row sm:items-center gap-2 mb-4 text-xs text-gray-500">
  <span>Every Commas transaction — website checkout <em>and</em> GHL funnels / payment links. Times in {{ str_replace('_', ' ', $tz) }}.</span>
  <div class="sm:ml-auto flex items-center gap-3">
    <span>Synced {{ $lastSyncedAt ? $lastSyncedAt->diffForHumans() : 'never' }}</span>
    <form method="POST" action="{{ route('admin.commas.sync') }}">
      @csrf
      <button class="px-3 py-1.5 rounded-lg border border-gray-300 bg-white text-ink font-semibold hover:bg-gray-50">↻ Sync now</button>
    </form>
  </div>
</div>

{{-- ─── Totals for the current filter ───────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-4">
  @foreach ([
      ['Transactions', number_format($totals['count']), 'text-ink'],
      ['Gross',        $money2($totals['gross']),       'text-ink'],
      ['Commas fees',  $money2($totals['fees']),        'text-gray-700'],
      ['Net to you',   $money2($totals['net']),         'text-green-700'],
      ['Refunded',     $money2($totals['refunded']),    $totals['refunded'] > 0 ? 'text-red-700' : 'text-ink'],
  ] as [$label, $value, $tone])
    <div class="bg-white border border-gray-200 rounded-xl p-4">
      <div class="text-xs uppercase tracking-wide text-gray-500">{{ $label }}</div>
      <div class="text-2xl font-bold mt-1 tabular-nums {{ $tone }}">{{ $value }}</div>
    </div>
  @endforeach
</div>

{{-- ─── Filters ────────────────────────────────────────────────────────────── --}}
<form method="GET" action="{{ route('admin.commas-sales') }}" class="bg-white rounded-xl border border-gray-200 p-4 mb-4">
  <div class="grid grid-cols-2 lg:grid-cols-7 gap-3">
    <div class="col-span-2">
      <label class="text-xs font-medium text-gray-600">Search</label>
      <input type="text" name="q" value="{{ $filters['q'] ?? '' }}" placeholder="Name, email, phone, product, Commas ID…"
             class="mt-1 w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent outline-none">
    </div>
    <div>
      <label class="text-xs font-medium text-gray-600">Product</label>
      <select name="product" class="mt-1 w-full text-sm px-3 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-gold">
        <option value="">All</option>
        @foreach ($products as $p)
          <option value="{{ $p }}" @selected(($filters['product'] ?? '') === $p)>{{ $p }}</option>
        @endforeach
      </select>
    </div>
    <div>
      <label class="text-xs font-medium text-gray-600">Source</label>
      <select name="source" class="mt-1 w-full text-sm px-3 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-gold">
        <option value="">All</option>
        <option value="website" @selected(($filters['source'] ?? '') === 'website')>Website checkout</option>
        <option value="other"   @selected(($filters['source'] ?? '') === 'other')>Other (funnels / links)</option>
      </select>
    </div>
    <div>
      <label class="text-xs font-medium text-gray-600">Status</label>
      <select name="state" class="mt-1 w-full text-sm px-3 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-gold">
        <option value="">All</option>
        <option value="on_hold"  @selected(($filters['state'] ?? '') === 'on_hold')>Payout on hold</option>
        <option value="released" @selected(($filters['state'] ?? '') === 'released')>Payout released</option>
        <option value="refunded" @selected(($filters['state'] ?? '') === 'refunded')>Refunded</option>
      </select>
    </div>
    <div>
      <label class="text-xs font-medium text-gray-600">From</label>
      <input type="date" name="from" value="{{ $filters['from'] ?? '' }}" class="mt-1 w-full text-sm px-3 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-gold">
    </div>
    <div>
      <label class="text-xs font-medium text-gray-600">To</label>
      <input type="date" name="to" value="{{ $filters['to'] ?? '' }}" class="mt-1 w-full text-sm px-3 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-gold">
    </div>
    <div class="flex items-end gap-2 col-span-2 lg:col-span-7 justify-end">
      <a href="{{ route('admin.commas-sales') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-ink">Reset</a>
      <a href="{{ route('admin.commas-sales.csv', request()->query()) }}" class="px-4 py-2 text-sm font-medium bg-white border border-gray-300 rounded-lg hover:bg-gray-50">Export CSV</a>
      <button type="submit" class="px-4 py-2 text-sm font-semibold bg-olive-dark text-paper rounded-lg hover:bg-olive transition">Apply</button>
    </div>
  </div>
</form>

{{-- ─── Table ──────────────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
  <div class="px-5 py-3 border-b border-gray-200 text-sm text-gray-600">
    Showing <span class="font-medium text-ink">{{ $rows->firstItem() ?? 0 }}–{{ $rows->lastItem() ?? 0 }}</span>
    of <span class="font-medium text-ink">{{ number_format($rows->total()) }}</span>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="text-xs uppercase text-gray-500 bg-gray-50 border-b border-gray-200">
        <tr>
          <th class="text-left py-2.5 px-5 font-medium">When</th>
          <th class="text-left py-2.5 px-3 font-medium">Customer</th>
          <th class="text-left py-2.5 px-3 font-medium">Product</th>
          <th class="text-right py-2.5 px-3 font-medium">Gross</th>
          <th class="text-right py-2.5 px-3 font-medium">Fee</th>
          <th class="text-right py-2.5 px-3 font-medium">Net</th>
          <th class="text-left py-2.5 px-3 font-medium">Payout</th>
          <th class="text-left py-2.5 px-5 font-medium">Commas ID</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse ($rows as $t)
          @php $isWeb = in_array($t->product_id, $websiteIds, true); @endphp
          <tr class="hover:bg-gray-50 align-top">
            <td class="py-3 px-5 text-xs text-gray-500 whitespace-nowrap">
              <div>{{ optional($t->transaction_date)->setTimezone($tz)->format('M j, Y') }}</div>
              <div class="text-gray-400">{{ optional($t->transaction_date)->setTimezone($tz)->format('g:i A') }}</div>
            </td>
            <td class="py-3 px-3">
              <div class="font-medium text-ink">{{ $t->customer_name ?: '—' }}</div>
              <div class="text-xs text-gray-500">{{ $t->customer_email }}</div>
              @if ($t->customer_phone)<div class="text-xs text-gray-400">{{ $t->customer_phone }}</div>@endif
            </td>
            <td class="py-3 px-3 text-xs text-gray-700">
              {{ $t->product_title }}
              <div class="mt-1">
                <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-semibold {{ $isWeb ? 'bg-green-100 text-green-800' : 'bg-indigo-100 text-indigo-800' }}">{{ $isWeb ? 'Website' : 'Funnel / link' }}</span>
              </div>
            </td>
            <td class="py-3 px-3 text-right font-semibold tabular-nums text-ink">
              {{ $money2($t->amount) }}
              @if ($t->refund_count)
                <div class="text-[11px] font-semibold text-red-700">−{{ $money2($t->refunded_amount) }} refunded</div>
              @endif
            </td>
            <td class="py-3 px-3 text-right text-xs text-gray-500 tabular-nums">{{ $money2($t->fee_amount) }}</td>
            <td class="py-3 px-3 text-right font-semibold text-green-700 tabular-nums">{{ $money2($t->net_amount) }}</td>
            <td class="py-3 px-3 text-xs">
              @if ($t->fund_released)
                <span class="inline-flex px-2 py-0.5 rounded-full font-semibold bg-green-100 text-green-800">● Released</span>
              @else
                <span class="inline-flex px-2 py-0.5 rounded-full font-semibold bg-amber-100 text-amber-800">▲ On hold</span>
              @endif
              @if ($t->fund_release_on)
                <div class="text-gray-400 mt-1">{{ $t->fund_release_on->setTimezone($tz)->format('M j') }}</div>
              @endif
            </td>
            <td class="py-3 px-5 text-xs text-gray-500 font-mono">{{ $t->commas_id }}</td>
          </tr>
        @empty
          <tr><td colspan="8" class="py-10 text-center text-gray-400">No Commas transactions match. If this is the first visit, click <strong>Sync now</strong>.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="px-5 py-3 border-t border-gray-200">{{ $rows->links() }}</div>
</div>

@endsection
