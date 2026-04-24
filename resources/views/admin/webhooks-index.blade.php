@extends('admin.layout')

@section('title', 'Webhooks')

@section('content')

@php
    $sigBadge = function ($val): array {
        if ($val === true || $val === 1)  return ['OK',      'bg-green-100 text-green-800'];
        if ($val === false || $val === 0) return ['INVALID', 'bg-red-100 text-red-800'];
        return ['N/A', 'bg-gray-100 text-gray-600'];
    };

    $catBadge = function (string $category): string {
        return match ($category) {
            'payment'      => 'bg-emerald-100 text-emerald-800',
            'subscription' => 'bg-indigo-100 text-indigo-800',
            'profile'      => 'bg-cyan-100 text-cyan-800',
            'customer'     => 'bg-slate-100 text-slate-800',
            default        => 'bg-gray-100 text-gray-700',
        };
    };
@endphp

{{-- ─── Stats strip ────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 lg:grid-cols-5 gap-3 mb-4">
  <div class="bg-white rounded-xl border border-gray-200 p-4">
    <div class="text-xs text-gray-500 uppercase tracking-wide">Today</div>
    <div class="text-2xl font-semibold text-ink mt-1">{{ number_format($stats['today']) }}</div>
    <div class="text-xs text-gray-500 mt-1">webhooks received</div>
  </div>
  <div class="bg-white rounded-xl border border-gray-200 p-4">
    <div class="text-xs text-gray-500 uppercase tracking-wide">Last Hour</div>
    <div class="text-2xl font-semibold text-ink mt-1">{{ number_format($stats['last_hour']) }}</div>
    <div class="text-xs text-gray-500 mt-1">most recent activity</div>
  </div>
  <div class="bg-white rounded-xl border border-gray-200 p-4">
    <div class="text-xs text-gray-500 uppercase tracking-wide">Last 7 Days</div>
    <div class="text-2xl font-semibold text-ink mt-1">{{ number_format($stats['last_7d']) }}</div>
    <div class="text-xs text-gray-500 mt-1">rolling total</div>
  </div>
  <div class="bg-white rounded-xl border-2 {{ $stats['signature_invalid_today'] > 0 ? 'border-red-300' : 'border-gray-200' }} p-4">
    <div class="text-xs text-gray-500 uppercase tracking-wide">Signature (today)</div>
    <div class="text-2xl font-semibold mt-1">
      <span class="text-green-700">{{ $stats['signature_ok_today'] }}</span>
      <span class="text-gray-400 text-base">/</span>
      <span class="text-red-700">{{ $stats['signature_invalid_today'] }}</span>
    </div>
    <div class="text-xs text-gray-500 mt-1">OK / INVALID</div>
  </div>
  <div class="bg-white rounded-xl border border-gray-200 p-4">
    <div class="text-xs text-gray-500 uppercase tracking-wide">Last Received</div>
    <div class="text-lg font-semibold text-ink mt-1">
      {{ $stats['last_received_at'] ? $stats['last_received_at']->diffForHumans() : 'never' }}
    </div>
    <div class="text-xs text-gray-500 mt-1">
      {{ $stats['last_received_at']?->format('M j, g:i A') ?? '—' }}
    </div>
  </div>
</div>

{{-- ─── Filter bar ─────────────────────────────────────────────────────────── --}}
<form method="GET" action="{{ route('admin.webhooks') }}"
      class="bg-white rounded-xl border border-gray-200 p-4 mb-4">
  <div class="grid grid-cols-2 lg:grid-cols-6 gap-3">

    <div class="col-span-2 lg:col-span-2">
      <label class="text-xs font-medium text-gray-600">Search</label>
      <input type="text" name="q" value="{{ $filters['q'] ?? '' }}"
             placeholder="Customer name, email, ARB id, invoice…"
             class="mt-1 w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent outline-none">
    </div>

    <div>
      <label class="text-xs font-medium text-gray-600">Event Type</label>
      <select name="event_type" class="mt-1 w-full text-sm px-3 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-gold">
        <option value="">All</option>
        @foreach ($byTypeAll as $row)
          <option value="{{ $row->event_type }}" @selected(($filters['event_type'] ?? '') === $row->event_type)>
            {{ str_replace('net.authorize.', '', $row->event_type) }} ({{ $row->cnt }})
          </option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="text-xs font-medium text-gray-600">Signature</label>
      <select name="signature" class="mt-1 w-full text-sm px-3 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-gold">
        <option value="">Any</option>
        <option value="ok"         @selected(($filters['signature'] ?? '') === 'ok')>Valid</option>
        <option value="invalid"    @selected(($filters['signature'] ?? '') === 'invalid')>Invalid</option>
        <option value="unverified" @selected(($filters['signature'] ?? '') === 'unverified')>Unverified</option>
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
      <a href="{{ route('admin.webhooks') }}"
         class="px-4 py-2 text-sm text-gray-600 hover:text-ink">Reset</a>
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
      Showing <span class="font-medium text-ink">{{ $events->firstItem() ?? 0 }}–{{ $events->lastItem() ?? 0 }}</span>
      of <span class="font-medium text-ink">{{ number_format($events->total()) }}</span>
    </div>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="text-xs uppercase text-gray-500 bg-gray-50 border-b border-gray-200">
        <tr>
          <th class="text-left  py-2.5 px-5 font-medium">Event Type</th>
          <th class="text-left  py-2.5 px-3 font-medium">Customer</th>
          <th class="text-right py-2.5 px-3 font-medium">Amount</th>
          <th class="text-left  py-2.5 px-3 font-medium">Status</th>
          <th class="text-left  py-2.5 px-3 font-medium">Date / Time</th>
          <th class="text-right py-2.5 px-5 font-medium">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse ($events as $ev)
          @php
            $shortType = str_replace('net.authorize.', '', $ev->event_type);
            $catCls    = $catBadge($ev->category());
            $statusBg  = $ev->statusBadge();
            [$sigLabel, $sigCls] = $sigBadge($ev->signature_valid);
            $custName  = $ev->customerDisplayName();
          @endphp
          <tr class="hover:bg-gray-50 align-top">
            <td class="py-3 px-5">
              <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $catCls }}">
                {{ ucfirst($ev->category()) }}
              </span>
              <div class="font-mono text-[11px] text-gray-700 mt-1">{{ $shortType }}</div>
              @if ($ev->description)
                <div class="text-xs text-gray-500 mt-1 max-w-md">{{ $ev->description }}</div>
              @endif
            </td>
            <td class="py-3 px-3">
              @if ($custName !== '—')
                <div class="text-sm font-medium text-ink">{{ $custName }}</div>
                @if ($ev->customer_email)
                  <div class="text-xs text-gray-500">{{ $ev->customer_email }}</div>
                @endif
              @else
                <span class="text-gray-300 text-xs">unmatched</span>
              @endif
              @if ($ev->entity_id)
                <div class="text-[11px] text-gray-400 font-mono mt-1">id: {{ $ev->entity_id }}</div>
              @endif
            </td>
            <td class="py-3 px-3 text-right">
              @if ($ev->amount !== null)
                <span class="font-semibold text-ink">${{ number_format($ev->amount, 2) }}</span>
              @else
                <span class="text-gray-300">—</span>
              @endif
            </td>
            <td class="py-3 px-3">
              <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $statusBg['class'] }}">
                {{ $statusBg['label'] }}
              </span>
              <div class="mt-1">
                <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-medium {{ $sigCls }}">sig: {{ $sigLabel }}</span>
              </div>
            </td>
            <td class="py-3 px-3 whitespace-nowrap text-xs text-gray-700">
              {{ $ev->received_at?->format('M j, Y') ?? '—' }}
              <div class="text-[11px] text-gray-400">{{ $ev->received_at?->format('g:i:s A') }}</div>
              <div class="text-[11px] text-gray-400">{{ $ev->received_at?->diffForHumans() }}</div>
            </td>
            <td class="py-3 px-5 text-right whitespace-nowrap">
              <a href="{{ route('admin.webhook.show', $ev->id) }}"
                 class="inline-flex items-center text-xs px-3 py-1.5 rounded-md border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium">
                View Details
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="py-12 text-center">
              <div class="text-3xl mb-2">📭</div>
              <div class="text-sm text-gray-600 font-medium">No webhooks yet</div>
              <div class="text-xs text-gray-400 mt-1">
                Once Authorize.Net posts to <span class="font-mono">/webhooks/authorize-net</span>, rows will appear here.
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if ($events->hasPages())
    <div class="px-5 py-3 border-t border-gray-200">
      {{ $events->links() }}
    </div>
  @endif
</div>

@endsection
