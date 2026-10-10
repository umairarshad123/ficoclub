@extends('admin.layout')

@section('title', 'Website')

@php $money = fn ($v) => '$' . number_format((float) $v, 0); @endphp

@section('content')
<div x-data="{ copied: null, copy(text, id) { navigator.clipboard.writeText(text); this.copied = id; setTimeout(() => this.copied = null, 1500); } }">

{{-- ─── Site status ─────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-4 mb-6">
  <div class="xl:col-span-2 rounded-2xl border-2 {{ $maintenance ? 'border-amber-300 bg-amber-50' : 'border-green-200 bg-white' }} p-6">
    <div class="flex flex-col sm:flex-row sm:items-center gap-4">
      <div class="flex-1">
        <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Website status</div>
        <div class="mt-1 flex items-center gap-2 text-2xl font-bold {{ $maintenance ? 'text-amber-700' : 'text-gold-dark' }}">
          <span class="relative flex h-3 w-3">
            @unless ($maintenance)<span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-gold opacity-60"></span>@endunless
            <span class="relative inline-flex rounded-full h-3 w-3 {{ $maintenance ? 'bg-amber-500' : 'bg-gold' }}"></span>
          </span>
          {{ $maintenance ? 'Maintenance mode' : 'Live for everyone' }}
        </div>
        <p class="text-sm text-gray-600 mt-1">
          {{ $maintenance
              ? 'Visitors see the "We\'ll be back soon" page. Checkout and forms are closed. Admin and payment webhooks keep working.'
              : 'Pages, checkout and forms are open. Turn on maintenance before risky changes.' }}
          @if ($override === null)<span class="text-gray-400">(set by .env)</span>@endif
        </p>
      </div>
      <form method="POST" action="{{ route('admin.website.maintenance') }}"
            onsubmit="return confirm('{{ $maintenance ? 'Make the website LIVE for everyone?' : 'Put the website into MAINTENANCE? Visitors will not be able to pay or submit forms.' }}');">
        @csrf
        <input type="hidden" name="on" value="{{ $maintenance ? 0 : 1 }}">
        <button class="px-5 py-3 rounded-xl font-bold text-sm whitespace-nowrap shadow-sm {{ $maintenance ? 'bg-gold text-white hover:bg-gold-dark' : 'bg-white border-2 border-amber-400 text-amber-700 hover:bg-amber-50' }}">
          {{ $maintenance ? '▶ Go live now' : '❚❚ Turn on maintenance' }}
        </button>
      </form>
    </div>
    @if ($previewLink)
      <div class="mt-4 flex flex-col sm:flex-row sm:items-center gap-2 rounded-xl bg-white/70 border border-gray-200 px-4 py-3 text-sm">
        <span class="text-gray-600">Team preview link <span class="text-gray-400">(sees the real site during maintenance + unlocks the $1 test plan)</span></span>
        <button type="button" @click="copy(@js($previewLink), 'preview')" class="sm:ml-auto font-semibold text-gold-dark hover:underline" x-text="copied === 'preview' ? '✓ Copied' : 'Copy link'"></button>
      </div>
    @endif
  </div>

  <div class="bg-white rounded-2xl border border-gray-200 p-6">
    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Last 30 days</div>
    <div class="grid grid-cols-3 gap-3 mt-3 text-center">
      <div class="rounded-xl bg-paper p-3"><div class="text-2xl font-bold text-ink">{{ $paid30 }}</div><div class="text-[11px] text-gray-500">website sales</div></div>
      <div class="rounded-xl bg-paper p-3"><div class="text-2xl font-bold text-ink">{{ $pending30 }}</div><div class="text-[11px] text-gray-500">abandoned</div></div>
      <div class="rounded-xl bg-paper p-3"><div class="text-2xl font-bold text-ink">{{ $leads30 }}</div><div class="text-[11px] text-gray-500">funding leads</div></div>
    </div>
    <div class="mt-4 flex flex-wrap gap-2 text-xs font-semibold">
      <a href="{{ route('admin.orders') }}" class="px-3 py-1.5 rounded-lg bg-paper hover:bg-gray-100 text-ink">Checkout orders →</a>
      <a href="{{ route('admin.leads') }}" class="px-3 py-1.5 rounded-lg bg-paper hover:bg-gray-100 text-ink">Leads ({{ $leadsTotal }}) →</a>
    </div>
  </div>
</div>

{{-- ─── Plans: prices, Commas products, shareable links ─────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden mb-6">
  <div class="px-5 py-3 border-b border-gray-200 flex flex-col sm:flex-row sm:items-center gap-1">
    <div>
      <h2 class="font-semibold text-ink">Plans &amp; links</h2>
      <p class="text-xs text-gray-500">Checkout link = pay on the website. Onboarding link = for clients who already paid another way (plan is filled in for them).</p>
    </div>
    <span class="sm:ml-auto text-xs text-gray-500">Prices come from <span class="font-mono">config/plans.php</span> · Commas {{ $environment }}</span>
  </div>
  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="text-xs uppercase text-gray-500 bg-gray-50">
        <tr>
          <th class="text-left py-2.5 px-5 font-medium">Plan</th>
          <th class="text-right py-2.5 px-3 font-medium">Price</th>
          <th class="text-left py-2.5 px-3 font-medium">Commas product</th>
          <th class="text-right py-2.5 px-3 font-medium">Sales (30d)</th>
          <th class="text-left py-2.5 px-3 font-medium">Checkout link</th>
          <th class="text-left py-2.5 px-5 font-medium">Onboarding link</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @foreach ($plans as $p)
          <tr class="hover:bg-gray-50">
            <td class="py-3 px-5">
              <div class="font-semibold text-ink">{{ $p['label'] }}</div>
              @if ($p['hidden'])<span class="text-[10px] font-semibold uppercase px-1.5 py-0.5 rounded bg-gray-100 text-gray-600">hidden · internal test</span>@endif
            </td>
            <td class="py-3 px-3 text-right font-semibold tabular-nums">${{ number_format($p['amount'], 2) }}</td>
            <td class="py-3 px-3 text-xs">
              @if ($p['product_id'])
                <span class="font-mono text-gray-700">{{ $p['product_id'] }}</span> <span class="text-gold-dark">✓</span>
              @else
                <span class="text-red-700 font-semibold">⚠ not set</span>
              @endif
            </td>
            <td class="py-3 px-3 text-right tabular-nums">
              <div class="font-semibold text-ink">{{ $p['paid_30d'] }}</div>
              <div class="text-xs text-gray-500">{{ $money($p['rev_30d']) }}</div>
            </td>
            <td class="py-3 px-3 text-xs whitespace-nowrap">
              <a href="{{ $p['checkout'] }}" target="_blank" class="text-blue-700 hover:underline">Open ↗</a>
              <button type="button" @click="copy(@js($p['checkout']), 'c{{ $p['key'] }}')" class="ml-2 font-semibold text-gold-dark hover:underline" x-text="copied === 'c{{ $p['key'] }}' ? '✓ Copied' : 'Copy'"></button>
            </td>
            <td class="py-3 px-5 text-xs whitespace-nowrap">
              @if ($p['onboarding'])
                <a href="{{ $p['onboarding'] }}" target="_blank" class="text-blue-700 hover:underline">Open ↗</a>
                <button type="button" @click="copy(@js($p['onboarding']), 'o{{ $p['key'] }}')" class="ml-2 font-semibold text-gold-dark hover:underline" x-text="copied === 'o{{ $p['key'] }}' ? '✓ Copied' : 'Copy'"></button>
              @else
                <span class="text-gray-400">—</span>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

{{-- ─── Integrations + automation health ────────────────────────────────────── --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-4 mb-6">
  <div class="xl:col-span-2 bg-white rounded-2xl border border-gray-200 overflow-hidden">
    <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between">
      <h2 class="font-semibold text-ink">Connections</h2>
      <span class="text-xs text-gray-500">Live checkout: <strong class="text-ink">{{ $provider === 'commas' ? 'Commas' : 'Authorize.Net' }}</strong></span>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 divide-gray-100">
      @foreach (array_chunk($integrations, (int) ceil(count($integrations) / 2)) as $col)
        <div class="divide-y divide-gray-100 md:border-r md:last:border-r-0 border-gray-100">
          @foreach ($col as [$name, $ok, $hint])
            <div class="px-5 py-3 flex items-center gap-3">
              <span class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold {{ $ok ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">{{ $ok ? '✓' : '!' }}</span>
              <div class="min-w-0">
                <div class="text-sm font-medium text-ink">{{ $name }}</div>
                <div class="text-xs text-gray-500 truncate">{{ $ok ? $hint : 'Not configured — add it in .env' }}</div>
              </div>
            </div>
          @endforeach
        </div>
      @endforeach
    </div>
  </div>

  <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
    <div class="px-5 py-3 border-b border-gray-200"><h2 class="font-semibold text-ink">Automations</h2></div>
    @php
      $fresh = fn ($at, $mins) => $at && $at->gt(now()->subMinutes($mins));
      $jobs = [
        ['Commas data sync',        $lastSyncedAt,                        $fresh($lastSyncedAt, 30)],
        ['Payment safety net',      $health['last_reconcile_at'],         $fresh($health['last_reconcile_at'], 30)],
        ['Last Commas webhook',     $health['last_commas_webhook_at'],    $fresh($health['last_commas_webhook_at'], 60 * 24 * 3)],
        ['Last Authorize.Net webhook (legacy)', $health['last_webhook_at'], true],
      ];
    @endphp
    <div class="divide-y divide-gray-100">
      @foreach ($jobs as [$name, $at, $ok])
        <div class="px-5 py-3 flex items-center justify-between text-sm">
          <span class="text-gray-600">{{ $name }}</span>
          <span class="font-semibold {{ $ok ? 'text-gold-dark' : 'text-amber-700' }}">{{ $ok ? '●' : '▲' }} {{ $at ? $at->diffForHumans() : 'never' }}</span>
        </div>
      @endforeach
      <div class="px-5 py-3 flex items-center justify-between text-sm">
        <span class="text-gray-600">Orders needing review</span>
        <a href="{{ route('admin.orders', ['status' => 'mismatch']) }}" class="font-semibold {{ $health['orders_mismatch'] ? 'text-red-700' : 'text-gold-dark' }}">{{ $health['orders_mismatch'] ? '⚠' : '●' }} {{ $health['orders_mismatch'] }}</a>
      </div>
    </div>
  </div>
</div>

{{-- ─── Public pages ────────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
  <div class="px-5 py-3 border-b border-gray-200"><h2 class="font-semibold text-ink">Public pages</h2></div>
  <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-px bg-gray-100">
    @foreach ($pages as $i => [$name, $url])
      <div class="bg-white px-5 py-3 flex items-center justify-between gap-3 text-sm">
        <span class="font-medium text-ink truncate">{{ $name }}</span>
        <span class="flex-shrink-0 text-xs whitespace-nowrap">
          <a href="{{ $url }}" target="_blank" class="text-blue-700 hover:underline">Open ↗</a>
          <button type="button" @click="copy(@js($url), 'p{{ $i }}')" class="ml-2 font-semibold text-gold-dark hover:underline" x-text="copied === 'p{{ $i }}' ? '✓' : 'Copy'"></button>
        </span>
      </div>
    @endforeach
  </div>
</div>

</div>
@endsection
