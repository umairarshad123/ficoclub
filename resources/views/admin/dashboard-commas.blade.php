@extends('admin.layout')

@section('title', 'Dashboard')

@php
  $money  = fn ($v, $d = 0) => '$' . number_format((float) $v, $d);
  $money2 = fn ($v) => '$' . number_format((float) $v, 2);
  $SITE   = '#16a34a';   // categorical: website checkout
  $OTHER  = '#4f46e5';   // categorical: other Commas sales (funnels, payment links)
@endphp

@section('content')

{{-- ─── Header: view switch · range · sync ──────────────────────────────────── --}}
<div class="flex flex-wrap items-center gap-3 mb-6 whitespace-nowrap">
  <div class="inline-flex rounded-lg border border-gray-200 bg-white p-1 text-sm font-medium">
    <span class="px-3 py-1.5 rounded-md bg-olive-dark text-paper">Commas · live</span>
    <a href="{{ route('admin.dashboard', ['view' => 'legacy']) }}" class="px-3 py-1.5 rounded-md text-gray-600 hover:text-ink">Authorize.Net · legacy</a>
  </div>

  <div class="inline-flex rounded-lg border border-gray-200 bg-white p-1 text-sm font-medium overflow-x-auto max-w-full">
    @foreach ($ranges as $key => $label)
      <a href="{{ route('admin.dashboard', ['range' => $key]) }}"
         class="px-3 py-1.5 rounded-md {{ $range === $key ? 'bg-gold text-olive-dark font-semibold' : 'text-gray-600 hover:text-ink' }}">{{ $label }}</a>
    @endforeach
  </div>

  <div class="2xl:ml-auto flex flex-wrap items-center gap-3 text-xs text-gray-500">
    <span>{{ $start->format('M j') }} – {{ $end->format('M j, Y') }}</span>
    <span class="hidden sm:inline text-gray-300">|</span>
    <span>Synced {{ $lastSyncedAt ? $lastSyncedAt->diffForHumans() : 'never' }}</span>
    <form method="POST" action="{{ route('admin.commas.sync') }}">
      @csrf
      <button class="px-3 py-1.5 rounded-lg border border-gray-300 bg-white text-ink font-semibold hover:bg-gray-50">↻ Sync now</button>
    </form>
  </div>
</div>

@if (! $hasData)
  <div class="mb-6 rounded-xl border-2 border-dashed border-gold/50 bg-white p-6 text-center">
    <div class="text-lg font-semibold text-ink">No Commas data yet</div>
    <p class="text-sm text-gray-600 mt-1">Click <strong>Sync now</strong> to pull every Commas transaction (website + funnels + payment links). After that it refreshes automatically every 10 minutes.</p>
  </div>
@endif

{{-- ─── Hero KPIs ───────────────────────────────────────────────────────────── --}}
@php
  $delta = function ($pct, $invert = false) {
      if ($pct === null) return '<span class="text-gray-400">no prior data</span>';
      $up   = $pct >= 0;
      $good = $invert ? ! $up : $up;
      $cls  = $pct == 0 ? 'text-gray-500' : ($good ? 'text-green-700' : 'text-red-700');
      return '<span class="' . $cls . ' font-semibold">' . ($up ? '▲' : '▼') . ' ' . number_format(abs($pct), 1) . '%</span> <span class="text-gray-400">vs prior period</span>';
  };
  $hero = [
    ['Gross sales',      $money($kpis['gross']),  $delta($deltas['gross']),  'Charged to customers, incl. surcharge'],
    ['Net to you',       $money($kpis['net']),    $delta($deltas['net']),    'After Commas fees'],
    ['Orders',           number_format($kpis['orders']), $delta($deltas['orders']), number_format($kpis['customers']) . ' unique customers'],
    ['Avg order value',  $money($kpis['aov']),    $delta($deltas['aov']),    'Gross ÷ paid orders'],
  ];
@endphp
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-4">
  @foreach ($hero as [$label, $value, $deltaHtml, $hint])
    <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
      <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">{{ $label }}</div>
      <div class="text-3xl font-bold text-ink mt-2 tabular-nums">{{ $value }}</div>
      <div class="text-xs mt-1.5">{!! $deltaHtml !!}</div>
      <div class="text-xs text-gray-400 mt-1">{{ $hint }}</div>
    </div>
  @endforeach
</div>

{{-- ─── Secondary KPIs ──────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-2 xl:grid-cols-5 gap-4 mb-6">
  <div class="bg-white rounded-xl border border-gray-200 p-4">
    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Today</div>
    <div class="text-xl font-semibold text-ink mt-1 tabular-nums">{{ $money($today['gross']) }}</div>
    <div class="text-xs text-gray-500 mt-0.5">{{ $today['orders'] }} {{ \Illuminate\Support\Str::plural('order', $today['orders']) }}</div>
  </div>
  <div class="bg-white rounded-xl border border-gray-200 p-4">
    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Commas fees</div>
    <div class="text-xl font-semibold text-ink mt-1 tabular-nums">{{ $money($kpis['fees']) }}</div>
    <div class="text-xs text-gray-500 mt-0.5">{{ number_format($kpis['fee_rate'], 1) }}% of gross</div>
  </div>
  <div class="bg-white rounded-xl border {{ $kpis['refunds'] > 0 ? 'border-red-200' : 'border-gray-200' }} p-4">
    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Refunds</div>
    <div class="text-xl font-semibold mt-1 tabular-nums {{ $kpis['refunds'] > 0 ? 'text-red-700' : 'text-ink' }}">{{ $money($kpis['refunded']) }}</div>
    <div class="text-xs text-gray-500 mt-0.5">{{ $kpis['refunds'] }} {{ \Illuminate\Support\Str::plural('refund', $kpis['refunds']) }} · {{ number_format($kpis['refund_rate'], 1) }}% of orders</div>
  </div>
  <div class="bg-white rounded-xl border border-gray-200 p-4">
    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Kept after refunds</div>
    <div class="text-xl font-semibold text-green-700 mt-1 tabular-nums">{{ $money($kpis['kept']) }}</div>
    <div class="text-xs text-gray-500 mt-0.5">net − refund costs</div>
  </div>
  <div class="bg-white rounded-xl border {{ $disputes > 0 ? 'border-red-300 bg-red-50' : 'border-gray-200' }} p-4 col-span-2 xl:col-span-1">
    <div class="text-xs font-medium uppercase tracking-wide text-gray-500">Chargebacks</div>
    <div class="text-xl font-semibold mt-1 {{ $disputes > 0 ? 'text-red-700' : 'text-ink' }}">{{ $disputes > 0 ? '⚠ ' : '' }}{{ $disputes }}</div>
    <div class="text-xs text-gray-500 mt-0.5">opened in this period</div>
  </div>
</div>

{{-- ─── Revenue chart + payouts ─────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-4 mb-6">
  <div class="xl:col-span-2 bg-white rounded-2xl border border-gray-200 p-5">
    <div class="flex items-baseline justify-between mb-1">
      @php $bucket = \App\Services\CommasDashboard::bucketFor($range); @endphp
      <h2 class="font-semibold text-ink">Gross sales by {{ $bucket }}</h2>
      <span class="text-xs text-gray-500">{{ $rangeLabel }} · all Commas sales</span>
    </div>
    <div class="relative w-full" style="height: 300px;">
      <canvas id="revenueChart" role="img" aria-label="Gross sales per {{ $bucket }}"></canvas>
    </div>
    <details class="mt-3 text-xs text-gray-500">
      <summary class="cursor-pointer select-none">Show as table</summary>
      <div class="max-h-56 overflow-y-auto mt-2">
        <table class="w-full">
          <thead><tr class="text-gray-400"><th class="text-left py-1">{{ ucfirst($bucket) }}</th><th class="text-right py-1">Orders</th><th class="text-right py-1">Gross</th></tr></thead>
          <tbody>
            @foreach ($series as $p)
              <tr class="border-t border-gray-100"><td class="py-1">{{ $p['label'] }}</td><td class="py-1 text-right tabular-nums">{{ $p['orders'] }}</td><td class="py-1 text-right tabular-nums">{{ $money2($p['gross']) }}</td></tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </details>
  </div>

  <div class="bg-white rounded-2xl border border-gray-200 p-5 flex flex-col">
    <h2 class="font-semibold text-ink">Payouts</h2>
    <p class="text-xs text-gray-500 mb-4">Commas holds funds briefly before releasing them to your wallet.</p>

    <div class="rounded-xl bg-amber-50 border border-amber-200 p-4">
      <div class="text-xs font-semibold uppercase tracking-wide text-amber-800">On hold now</div>
      <div class="text-3xl font-bold text-ink mt-1 tabular-nums">{{ $money2($payouts['on_hold']) }}</div>
      <div class="text-xs text-amber-900/80 mt-1">{{ $payouts['on_hold_count'] }} {{ \Illuminate\Support\Str::plural('payment', $payouts['on_hold_count']) }} waiting to release</div>
    </div>

    <div class="mt-4 space-y-3 text-sm">
      <div class="flex justify-between">
        <span class="text-gray-500">Next release</span>
        <span class="font-semibold text-ink text-right">
          @if ($payouts['next_release'])
            {{ $payouts['next_release']->format('D, M j · g:i A') }}<br>
            <span class="text-xs text-gray-500 font-normal">{{ $money2($payouts['next_amount']) }} · {{ $payouts['next_release']->diffForHumans() }}</span>
          @else
            —
          @endif
        </span>
      </div>
      <div class="flex justify-between border-t border-gray-100 pt-3">
        <span class="text-gray-500">Released in {{ strtolower($rangeLabel) }}</span>
        <span class="font-semibold text-green-700 tabular-nums">{{ $money2($payouts['released_range']) }}</span>
      </div>
    </div>
  </div>
</div>

{{-- ─── Sales mix: source split + top products ──────────────────────────────── --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-4 mb-6">
  <div class="bg-white rounded-2xl border border-gray-200 p-5">
    <h2 class="font-semibold text-ink">Where sales come from</h2>
    <p class="text-xs text-gray-500 mb-4">Website checkout vs. other Commas sales (GHL funnels, payment links)</p>

    @php $w = $sources['website']['share']; $o = $sources['other']['share']; @endphp
    <div class="flex h-4 w-full rounded-full overflow-hidden bg-gray-100 gap-0.5" role="img" aria-label="Website {{ number_format($w, 0) }}%, other {{ number_format($o, 0) }}%">
      @if ($w > 0)<div style="width: {{ $w }}%; background: {{ $SITE }};" class="rounded-l-full {{ $o <= 0 ? 'rounded-r-full' : '' }}"></div>@endif
      @if ($o > 0)<div style="width: {{ $o }}%; background: {{ $OTHER }};" class="rounded-r-full {{ $w <= 0 ? 'rounded-l-full' : '' }}"></div>@endif
    </div>

    <div class="mt-5 space-y-4">
      @foreach ([['Website checkout', $sources['website'], $SITE], ['Other Commas sales', $sources['other'], $OTHER]] as [$label, $s, $color])
        <div class="flex items-start gap-3">
          <span class="mt-1 inline-block w-3 h-3 rounded-sm flex-shrink-0" style="background: {{ $color }}"></span>
          <div class="flex-1">
            <div class="flex justify-between text-sm">
              <span class="font-medium text-ink">{{ $label }}</span>
              <span class="font-semibold text-ink tabular-nums">{{ $money($s['gross']) }}</span>
            </div>
            <div class="flex justify-between text-xs text-gray-500">
              <span>{{ $s['orders'] }} {{ \Illuminate\Support\Str::plural('order', $s['orders']) }}</span>
              <span>{{ number_format($s['share'], 0) }}%</span>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>

  <div class="xl:col-span-2 bg-white rounded-2xl border border-gray-200 overflow-hidden">
    <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between">
      <h2 class="font-semibold text-ink">Top products</h2>
      <a href="{{ route('admin.commas-sales') }}" class="text-xs text-gold-dark font-semibold hover:underline">All sales →</a>
    </div>
    <div class="divide-y divide-gray-100">
      @forelse ($products as $p)
        <div class="px-5 py-3">
          <div class="flex items-center justify-between gap-3 text-sm">
            <div class="min-w-0 flex items-center gap-2">
              <span class="truncate font-medium text-ink">{{ $p['title'] }}</span>
              @if ($p['website'])
                <span class="flex-shrink-0 text-[10px] font-semibold uppercase tracking-wide px-1.5 py-0.5 rounded bg-green-100 text-green-800">Website</span>
              @endif
            </div>
            <div class="flex-shrink-0 text-right">
              <span class="font-semibold text-ink tabular-nums">{{ $money($p['gross']) }}</span>
              <span class="text-xs text-gray-500 ml-2">{{ $p['orders'] }} {{ \Illuminate\Support\Str::plural('order', $p['orders']) }}</span>
            </div>
          </div>
          <div class="mt-1.5 h-1.5 rounded-full bg-gray-100 overflow-hidden">
            <div class="h-full rounded-full" style="width: {{ max(1, $p['share']) }}%; background: {{ $p['website'] ? $SITE : $OTHER }};"></div>
          </div>
        </div>
      @empty
        <div class="px-5 py-10 text-center text-sm text-gray-400">No sales in this period.</div>
      @endforelse
    </div>
  </div>
</div>

{{-- ─── Website funnel · follow-ups · referrals ─────────────────────────────── --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-4 mb-6">
  <div class="bg-white rounded-2xl border border-gray-200 p-5">
    <h2 class="font-semibold text-ink">Website checkout funnel</h2>
    <p class="text-xs text-gray-500 mb-4">{{ $rangeLabel }} · people who started checkout on 850ficoclub.com</p>

    @php
      $steps = [
        ['Started checkout', $funnel['people'], 'gray'],
        ['Paid',             $funnel['paid'],   'green'],
      ];
      $max = max(1, $funnel['people']);
    @endphp
    <div class="space-y-3">
      @foreach ($steps as [$label, $n, $tone])
        <div>
          <div class="flex justify-between text-sm"><span class="text-gray-600">{{ $label }}</span><span class="font-semibold text-ink tabular-nums">{{ $n }}</span></div>
          <div class="mt-1 h-2.5 rounded-full bg-gray-100 overflow-hidden">
            <div class="h-full rounded-full" style="width: {{ $n / $max * 100 }}%; background: {{ $tone === 'green' ? $SITE : '#94a3b8' }};"></div>
          </div>
        </div>
      @endforeach
    </div>

    <div class="grid grid-cols-2 gap-3 mt-5">
      <div class="rounded-xl bg-gray-50 p-3">
        <div class="text-xs text-gray-500">Conversion</div>
        <div class="text-xl font-bold text-ink tabular-nums">{{ number_format($funnel['conversion'], 0) }}%</div>
      </div>
      <div class="rounded-xl bg-gray-50 p-3">
        <div class="text-xs text-gray-500">Website revenue</div>
        <div class="text-xl font-bold text-ink tabular-nums">{{ $money($funnel['revenue']) }}</div>
      </div>
    </div>
    @if ($funnel['review'] > 0)
      <a href="{{ route('admin.orders', ['status' => 'mismatch']) }}" class="mt-3 block rounded-lg bg-red-50 border border-red-200 px-3 py-2 text-xs text-red-800 font-medium">⚠ {{ $funnel['review'] }} paid {{ \Illuminate\Support\Str::plural('order', $funnel['review']) }} need review →</a>
    @endif
  </div>

  <div class="xl:col-span-2 bg-white rounded-2xl border border-gray-200 overflow-hidden">
    <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between">
      <div>
        <h2 class="font-semibold text-ink">Follow up: abandoned checkouts</h2>
        <p class="text-xs text-gray-500">Started checkout in the last 30 days, never paid. Easy wins for a call or text.</p>
      </div>
      <a href="{{ route('admin.orders', ['status' => 'pending']) }}" class="text-xs text-gold-dark font-semibold hover:underline whitespace-nowrap">All pending →</a>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <tbody class="divide-y divide-gray-100">
          @forelse ($followUps as $o)
            <tr class="hover:bg-gray-50">
              <td class="py-3 px-5">
                <div class="font-medium text-ink">{{ $o->fullName() }}</div>
                <div class="text-xs text-gray-500">{{ $o->created_at->setTimezone($tz)->diffForHumans() }}</div>
              </td>
              <td class="py-3 px-3 text-xs">
                <a href="mailto:{{ $o->email }}" class="text-blue-700 hover:underline">{{ $o->email }}</a><br>
                <a href="tel:{{ $o->phone }}" class="text-blue-700 hover:underline">{{ $o->phone }}</a>
              </td>
              <td class="py-3 px-3 text-xs text-gray-700">{{ $o->plan_label }}</td>
              <td class="py-3 px-5 text-right font-semibold text-ink tabular-nums">{{ $money($o->amount) }}</td>
            </tr>
          @empty
            <tr><td class="py-10 text-center text-sm text-gray-400">No abandoned checkouts — nice.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

{{-- ─── Recent sales · top customers · referrals ────────────────────────────── --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-4 mb-6">
  <div class="xl:col-span-2 bg-white rounded-2xl border border-gray-200 overflow-hidden">
    <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between">
      <h2 class="font-semibold text-ink">Latest sales</h2>
      <a href="{{ route('admin.commas-sales') }}" class="text-xs text-gold-dark font-semibold hover:underline">View all →</a>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="text-xs uppercase text-gray-500 bg-gray-50">
          <tr>
            <th class="text-left py-2 px-5 font-medium">When</th>
            <th class="text-left py-2 px-3 font-medium">Customer</th>
            <th class="text-left py-2 px-3 font-medium">Product</th>
            <th class="text-right py-2 px-3 font-medium">Gross</th>
            <th class="text-right py-2 px-5 font-medium">Net</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
          @forelse ($recent as $t)
            <tr class="hover:bg-gray-50">
              <td class="py-2.5 px-5 text-xs text-gray-500 whitespace-nowrap">{{ optional($t->transaction_date)->setTimezone($tz)->format('M j, g:i A') }}</td>
              <td class="py-2.5 px-3">
                <div class="font-medium text-ink">{{ $t->customer_name ?: '—' }}</div>
                <div class="text-xs text-gray-500">{{ $t->customer_email }}</div>
              </td>
              <td class="py-2.5 px-3 text-xs text-gray-700">
                <span class="inline-block w-2 h-2 rounded-sm mr-1 align-middle" style="background: {{ $t->isWebsiteSale() ? $SITE : $OTHER }}"></span>{{ $t->product_title }}
              </td>
              <td class="py-2.5 px-3 text-right font-semibold tabular-nums {{ $t->refund_count ? 'text-red-700 line-through' : 'text-ink' }}">{{ $money2($t->amount) }}</td>
              <td class="py-2.5 px-5 text-right tabular-nums text-gray-700">
                {{ $money2($t->net_amount) }}
                @if ($t->refund_count)
                  <div class="text-[10px] font-semibold text-red-700 no-underline">REFUNDED</div>
                @elseif (! $t->fund_released)
                  <div class="text-[10px] font-semibold text-amber-700">ON HOLD</div>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="5" class="py-10 text-center text-sm text-gray-400">No sales synced yet.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="space-y-4">
    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
      <div class="px-5 py-3 border-b border-gray-200"><h2 class="font-semibold text-ink">Top customers</h2></div>
      <div class="divide-y divide-gray-100">
        @forelse ($topCustomers as $i => $c)
          <div class="px-5 py-2.5 flex items-center gap-3 text-sm">
            <span class="w-6 h-6 rounded-full bg-gold/20 text-olive-dark text-xs font-bold flex items-center justify-center flex-shrink-0">{{ $i + 1 }}</span>
            <div class="min-w-0 flex-1">
              <div class="truncate font-medium text-ink">{{ $c['name'] ?: $c['email'] }}</div>
              <div class="truncate text-xs text-gray-500">{{ $c['orders'] }} {{ \Illuminate\Support\Str::plural('order', $c['orders']) }}</div>
            </div>
            <span class="font-semibold text-ink tabular-nums">{{ $money($c['gross']) }}</span>
          </div>
        @empty
          <div class="px-5 py-6 text-center text-sm text-gray-400">—</div>
        @endforelse
      </div>
    </div>

    <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
      <div class="px-5 py-3 border-b border-gray-200"><h2 class="font-semibold text-ink">Website referrals</h2></div>
      <div class="divide-y divide-gray-100">
        @forelse ($referrals as $r)
          <div class="px-5 py-2.5 flex items-center justify-between text-sm">
            <span class="text-xs font-semibold px-2 py-0.5 rounded {{ $r['code'] === 'DIRECT' ? 'bg-gray-100 text-gray-700' : 'bg-gold/15 text-gold-dark' }}">{{ $r['code'] }}</span>
            <span class="text-gray-500 text-xs">{{ $r['orders'] }} {{ \Illuminate\Support\Str::plural('order', $r['orders']) }}</span>
            <span class="font-semibold text-ink tabular-nums">{{ $money($r['revenue']) }}</span>
          </div>
        @empty
          <div class="px-5 py-6 text-center text-sm text-gray-400">No website sales in this period.</div>
        @endforelse
      </div>
    </div>
  </div>
</div>

{{-- ─── System health ───────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-2xl border border-gray-200 overflow-hidden">
  <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between">
    <h2 class="font-semibold text-ink">System health</h2>
    <span class="text-xs text-gray-500">Live checkout: <span class="font-semibold text-ink">{{ $health['payment_provider'] === 'commas' ? 'Commas' : 'Authorize.Net' }}</span></span>
  </div>
  @php
    $ok = fn ($at, $mins) => $at && $at->gt(now()->subMinutes($mins));
    $cells = [
      ['Data sync',          $lastSyncedAt,                    $ok($lastSyncedAt, 30),                    'every 10 min'],
      ['Last Commas webhook',$health['last_commas_webhook_at'], $ok($health['last_commas_webhook_at'], 60 * 24 * 3), 'fires on each payment'],
      ['Payment safety net', $health['last_reconcile_at'],     $ok($health['last_reconcile_at'], 30),     'catches lost webhooks'],
    ];
  @endphp
  <div class="grid grid-cols-1 md:grid-cols-4 divide-y md:divide-y-0 md:divide-x divide-gray-100">
    @foreach ($cells as [$label, $at, $good, $hint])
      <div class="p-4">
        <div class="text-xs text-gray-500 uppercase tracking-wide">{{ $label }}</div>
        <div class="text-base font-semibold mt-1 {{ $good ? 'text-green-700' : 'text-amber-700' }}">{{ $good ? '● ' : '▲ ' }}{{ $at ? $at->diffForHumans() : 'never' }}</div>
        <div class="text-xs text-gray-500 mt-0.5">{{ $hint }}</div>
      </div>
    @endforeach
    <div class="p-4">
      <div class="text-xs text-gray-500 uppercase tracking-wide">Orders needing review</div>
      <div class="text-base font-semibold mt-1 {{ $health['orders_mismatch'] > 0 ? 'text-red-700' : 'text-green-700' }}">{{ $health['orders_mismatch'] > 0 ? '⚠ ' : '● ' }}{{ $health['orders_mismatch'] }}</div>
      <div class="text-xs text-gray-500 mt-0.5">
        @if ($health['orders_mismatch'] > 0)
          <a href="{{ route('admin.orders', ['status' => 'mismatch']) }}" class="text-red-700 hover:underline">review now →</a>
        @else
          all paid orders matched
        @endif
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  (function () {
    var series = @json($series);
    var fmt = function (v) { return '$' + Number(v).toLocaleString('en-US', { maximumFractionDigits: 0 }); };

    new Chart(document.getElementById('revenueChart'), {
      type: 'bar',
      data: {
        labels: series.map(function (p) { return p.label; }),
        datasets: [{
          label: 'Gross sales',
          data: series.map(function (p) { return p.gross; }),
          backgroundColor: @json($SITE),
          hoverBackgroundColor: '#15803d',
          borderRadius: { topLeft: 4, topRight: 4 },
          borderSkipped: 'bottom',
          maxBarThickness: 28,
          categoryPercentage: 0.8,
          barPercentage: 0.9
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: { mode: 'index', intersect: false },
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: '#1a1a1a', padding: 10, displayColors: false,
            callbacks: {
              title: function (items) { return items[0].label; },
              label: function (ctx) {
                var p = series[ctx.dataIndex];
                return [fmt(p.gross) + ' gross', p.orders + (p.orders === 1 ? ' order' : ' orders')];
              }
            }
          }
        },
        scales: {
          x: { grid: { display: false }, ticks: { color: '#6b7280', maxRotation: 0, autoSkip: true, maxTicksLimit: 12 } },
          y: { beginAtZero: true, border: { display: false }, grid: { color: '#f1f5f9' },
               ticks: { color: '#6b7280', callback: fmt, maxTicksLimit: 6 } }
        }
      }
    });
  })();
</script>
@endpush

@endsection
