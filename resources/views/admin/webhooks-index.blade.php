@extends('admin.layout')

@section('title', 'Webhooks')

@section('content')

@php
    $sigBadge = function ($val): array {
        if ($val === true || $val === 1)  return ['OK',       'bg-green-100 text-green-800'];
        if ($val === false || $val === 0) return ['INVALID',  'bg-red-100 text-red-800'];
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

{{-- ─── 24h activity chart ─────────────────────────────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-200 p-5 mb-4">
  <div class="flex items-center justify-between mb-3">
    <h2 class="font-semibold text-ink">Webhook Activity — Last 24 Hours</h2>
    <span class="text-xs text-gray-500">{{ array_sum(array_column($series, 'count')) }} events</span>
  </div>
  <canvas id="webhooks24hChart" height="100"></canvas>
</div>

{{-- ─── Filter bar ─────────────────────────────────────────────────────────── --}}
<form method="GET" action="{{ route('admin.webhooks') }}"
      class="bg-white rounded-xl border border-gray-200 p-4 mb-4">
  <div class="grid grid-cols-1 md:grid-cols-5 gap-3">

    <div class="md:col-span-2">
      <label class="text-xs font-medium text-gray-600 uppercase">Search</label>
      <input type="text" name="q" value="{{ $filters['q'] ?? '' }}"
             placeholder="customer name, email, ARB id, invoice, IP..."
             class="mt-1 w-full text-sm px-3 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-gold">
    </div>

    <div>
      <label class="text-xs font-medium text-gray-600 uppercase">Event Type</label>
      <select name="event_type"
              class="mt-1 w-full text-sm px-3 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-gold">
        <option value="">All event types</option>
        @foreach ($byTypeAll as $row)
          <option value="{{ $row->event_type }}" @selected(($filters['event_type'] ?? '') === $row->event_type)>
            {{ str_replace('net.authorize.', '', $row->event_type) }} ({{ $row->cnt }})
          </option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="text-xs font-medium text-gray-600 uppercase">Signature</label>
      <select name="signature"
              class="mt-1 w-full text-sm px-3 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-gold">
        <option value="">Any</option>
        <option value="ok"         @selected(($filters['signature'] ?? '') === 'ok')>Valid</option>
        <option value="invalid"    @selected(($filters['signature'] ?? '') === 'invalid')>Invalid</option>
        <option value="unverified" @selected(($filters['signature'] ?? '') === 'unverified')>Unverified</option>
      </select>
    </div>

    <div class="grid grid-cols-2 gap-2">
      <div>
        <label class="text-xs font-medium text-gray-600 uppercase">From</label>
        <input type="date" name="from" value="{{ $filters['from'] ?? '' }}"
               class="mt-1 w-full text-sm px-2 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-gold">
      </div>
      <div>
        <label class="text-xs font-medium text-gray-600 uppercase">To</label>
        <input type="date" name="to" value="{{ $filters['to'] ?? '' }}"
               class="mt-1 w-full text-sm px-2 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-gold">
      </div>
    </div>
  </div>

  <div class="mt-3 flex items-center gap-2">
    <button type="submit"
            class="px-4 py-2 text-sm font-semibold bg-olive-dark text-paper rounded-lg hover:bg-olive">Apply Filters</button>
    <a href="{{ route('admin.webhooks') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-ink">Reset</a>
  </div>
</form>

{{-- ─── Events table + Modal (Alpine) ──────────────────────────────────────── --}}
<div x-data="{ open: false, current: {} }" class="bg-white rounded-xl border border-gray-200 overflow-hidden">
  <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between">
    <h2 class="font-semibold text-ink">Inbound Webhooks</h2>
    <span class="text-xs text-gray-500">{{ number_format($events->total()) }} {{ \Illuminate\Support\Str::plural('event', $events->total()) }} matching</span>
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
              <div class="flex items-center gap-2">
                <span class="inline-flex px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $catCls }}">
                  {{ ucfirst($ev->category()) }}
                </span>
              </div>
              <div class="font-mono text-[11px] text-gray-700 mt-1">{{ $shortType }}</div>
              @if ($ev->description)
                <div class="text-xs text-gray-500 mt-1 max-w-md">{{ $ev->description }}</div>
              @endif
            </td>
            <td class="py-3 px-3">
              @if ($ev->matched_subscription_id || $custName !== '—')
                <div class="text-sm font-medium text-ink">{{ $custName }}</div>
                @if ($ev->customer_email)
                  <div class="text-xs text-gray-500">{{ $ev->customer_email }}</div>
                @endif
                @if ($ev->matched_subscription_id)
                  <a href="{{ route('admin.subscription.show', $ev->matched_subscription_id) }}"
                     class="text-[11px] text-olive hover:text-gold underline-offset-2 hover:underline">view subscription →</a>
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
              @php
                $modalData = [
                    'id'              => $ev->id,
                    'event_type'      => $ev->event_type,
                    'description'     => $ev->description,
                    'customer_name'   => $custName,
                    'customer_email'  => $ev->customer_email,
                    'amount'          => $ev->amount,
                    'entity_id'       => $ev->entity_id,
                    'invoice_number'  => $ev->invoice_number,
                    'notification_id' => $ev->notification_id,
                    'received_at'     => $ev->received_at?->format('M j, Y g:i:s A'),
                    'source_ip'       => $ev->source_ip,
                    'signature'       => $sigLabel,
                    'status_label'    => $statusBg['label'],
                    'response_code'   => $ev->response_code,
                    'response_label'  => $ev->responseCodeLabel(),
                    'arb_status'      => $ev->arb_status,
                    'subscription_id' => $ev->matched_subscription_id,
                    'payload'         => json_encode($ev->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
                ];
              @endphp
              <button type="button"
                      @click="current = {{ \Illuminate\Support\Js::from($modalData) }}; open = true"
                      class="text-xs px-3 py-1.5 rounded-md border border-gray-200 hover:bg-gray-50 text-gray-700 font-medium">
                View Details
              </button>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="py-12 text-center">
              <div class="text-3xl mb-2">📭</div>
              <div class="text-sm text-gray-600 font-medium">No webhook events match your filters</div>
              <div class="text-xs text-gray-400 mt-1">Once Auth.net posts to /webhooks/authorize-net, rows will appear here.</div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @if ($events->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">
      {{ $events->links() }}
    </div>
  @endif

  {{-- ─── Modal: full webhook detail (powered by current{} state) ─────────── --}}
  <div x-show="open" x-cloak
       x-transition.opacity
       class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4"
       @keydown.escape.window="open = false"
       @click.self="open = false">

    <div class="bg-white rounded-xl shadow-2xl w-full max-w-3xl max-h-[92vh] overflow-hidden flex flex-col"
         x-transition>
      <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between">
        <div>
          <div class="text-xs uppercase text-gray-500 tracking-wide">Webhook Event</div>
          <div class="font-mono text-sm text-ink mt-0.5" x-text="current.event_type"></div>
        </div>
        <button type="button" @click="open = false"
                class="text-gray-400 hover:text-ink p-1">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
      </div>

      <div class="overflow-y-auto p-5 space-y-4 text-sm">
        <div class="bg-gray-50 rounded-lg p-3">
          <div class="text-xs text-gray-500 uppercase mb-1">In plain English</div>
          <div class="text-ink" x-text="current.description || current.event_type"></div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div>
            <div class="text-xs text-gray-500 uppercase">Customer</div>
            <div class="font-medium" x-text="current.customer_name || '—'"></div>
            <div class="text-xs text-gray-500" x-text="current.customer_email || ''"></div>
          </div>
          <div>
            <div class="text-xs text-gray-500 uppercase">Amount</div>
            <div class="font-medium">
              <template x-if="current.amount !== null && current.amount !== undefined">
                <span x-text="'$' + Number(current.amount).toFixed(2)"></span>
              </template>
              <template x-if="current.amount === null || current.amount === undefined">
                <span class="text-gray-400">—</span>
              </template>
            </div>
          </div>
          <div>
            <div class="text-xs text-gray-500 uppercase">Status</div>
            <div class="font-medium" x-text="current.status_label"></div>
          </div>
          <div>
            <div class="text-xs text-gray-500 uppercase">Signature</div>
            <div class="font-medium" x-text="current.signature"></div>
          </div>
          <div>
            <div class="text-xs text-gray-500 uppercase">Response Code</div>
            <div class="font-medium">
              <template x-if="current.response_code">
                <span><span x-text="current.response_code"></span> — <span x-text="current.response_label"></span></span>
              </template>
              <template x-if="!current.response_code">
                <span class="text-gray-400">—</span>
              </template>
            </div>
          </div>
          <div>
            <div class="text-xs text-gray-500 uppercase">ARB Status</div>
            <div class="font-medium" x-text="current.arb_status || '—'"></div>
          </div>
          <div>
            <div class="text-xs text-gray-500 uppercase">Entity / Transaction ID</div>
            <div class="font-mono text-xs" x-text="current.entity_id || '—'"></div>
          </div>
          <div>
            <div class="text-xs text-gray-500 uppercase">Invoice</div>
            <div class="font-mono text-xs" x-text="current.invoice_number || '—'"></div>
          </div>
          <div class="col-span-2">
            <div class="text-xs text-gray-500 uppercase">Notification ID</div>
            <div class="font-mono text-xs break-all" x-text="current.notification_id || '—'"></div>
          </div>
          <div>
            <div class="text-xs text-gray-500 uppercase">Received</div>
            <div class="font-medium" x-text="current.received_at || '—'"></div>
          </div>
          <div>
            <div class="text-xs text-gray-500 uppercase">Source IP</div>
            <div class="font-mono text-xs" x-text="current.source_ip || '—'"></div>
          </div>
        </div>

        <div x-data="{ copied: false }">
          <div class="flex items-center justify-between mb-1">
            <div class="text-xs text-gray-500 uppercase">Raw JSON Payload</div>
            <button type="button"
                    @click="navigator.clipboard.writeText(current.payload); copied = true; setTimeout(() => copied = false, 1500)"
                    class="text-[11px] px-2 py-1 rounded-md border border-gray-200 hover:bg-gray-50 text-gray-700">
              <span x-show="!copied">Copy JSON</span>
              <span x-show="copied" x-cloak class="text-green-700">Copied ✓</span>
            </button>
          </div>
          <pre class="bg-gray-900 text-gray-100 rounded-lg p-4 text-[11px] leading-relaxed overflow-x-auto max-h-72 overflow-y-auto whitespace-pre-wrap" x-text="current.payload"></pre>
        </div>
      </div>

      <div class="px-5 py-3 border-t border-gray-200 flex items-center justify-between bg-gray-50">
        <template x-if="current.subscription_id">
          <a :href="'/admin/subscriptions/' + current.subscription_id"
             class="text-sm text-olive hover:text-gold underline-offset-2 hover:underline">
            Open subscription →
          </a>
        </template>
        <template x-if="!current.subscription_id"><span></span></template>
        <button type="button" @click="open = false"
                class="px-4 py-2 text-sm font-semibold bg-olive-dark text-paper rounded-lg hover:bg-olive">
          Close
        </button>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  new Chart(document.getElementById('webhooks24hChart'), {
    type: 'bar',
    data: {
      labels: @json(array_column($series, 'label')),
      datasets: [{
        label: 'Webhooks',
        data: @json(array_column($series, 'count')),
        backgroundColor: '#c9a54a',
        borderColor: '#3d4020',
        borderWidth: 1,
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        x: { grid: { display: false } },
        y: { beginAtZero: true, ticks: { precision: 0, stepSize: 1 } }
      }
    }
  });
</script>
@endpush

@endsection
