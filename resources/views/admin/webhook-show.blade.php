@extends('admin.layout')

@section('title', 'Webhook Detail')

@section('content')

@php
    $sigBadge = function ($val): array {
        if ($val === true || $val === 1)  return ['Signature OK',      'bg-green-100 text-green-800'];
        if ($val === false || $val === 0) return ['Signature INVALID', 'bg-red-100 text-red-800'];
        return ['Signature N/A', 'bg-gray-100 text-gray-600'];
    };
    [$sigLabel, $sigCls] = $sigBadge($event->signature_valid);
@endphp

<div class="mb-4">
  <a href="{{ route('admin.webhooks') }}" class="text-sm text-gray-500 hover:text-ink">← Back to webhooks</a>
</div>

<div class="grid lg:grid-cols-3 gap-4">
  <div class="lg:col-span-2 space-y-4">

    {{-- ─── Header / metadata ────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
      <div class="flex items-start justify-between mb-4">
        <div>
          <div class="text-xs text-gray-500 uppercase tracking-wide">Event</div>
          <div class="font-mono text-sm text-ink mt-1">{{ $event->event_type }}</div>
        </div>
        <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $sigCls }}">{{ $sigLabel }}</span>
      </div>

      @if ($event->description)
        <div class="bg-gray-50 rounded-lg p-3 mb-4">
          <div class="text-xs text-gray-500 uppercase mb-1">In plain English</div>
          <div class="text-sm text-ink">{{ $event->description }}</div>
        </div>
      @endif

      @php $statusBg = $event->statusBadge(); @endphp
      <div class="flex flex-wrap gap-2 mb-4">
        <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold {{ $statusBg['class'] }}">
          {{ $statusBg['label'] }}
        </span>
        @if ($event->responseCodeLabel())
          <span class="inline-flex px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
            Response {{ $event->response_code }} — {{ $event->responseCodeLabel() }}
          </span>
        @endif
      </div>

      <div class="grid grid-cols-2 gap-4 text-sm pt-4 border-t border-gray-100">
        <div>
          <div class="text-xs text-gray-500 uppercase">Customer</div>
          <div class="font-medium">{{ $event->customerDisplayName() }}</div>
          @if ($event->customer_email)
            <div class="text-xs text-gray-500">{{ $event->customer_email }}</div>
          @endif
        </div>
        <div>
          <div class="text-xs text-gray-500 uppercase">Amount</div>
          <div class="font-medium">{{ $event->amount !== null ? '$' . number_format($event->amount, 2) : '—' }}</div>
        </div>
        <div>
          <div class="text-xs text-gray-500 uppercase">Received</div>
          <div class="font-medium">{{ $event->received_at?->format('M j, Y g:i:s A') ?? '—' }}</div>
          <div class="text-xs text-gray-500">{{ $event->received_at?->diffForHumans() }}</div>
        </div>
        <div>
          <div class="text-xs text-gray-500 uppercase">Source IP</div>
          <div class="font-mono text-xs">{{ $event->source_ip ?: '—' }}</div>
        </div>
        <div>
          <div class="text-xs text-gray-500 uppercase">Notification ID</div>
          <div class="font-mono text-xs break-all">{{ $event->notification_id ?: '—' }}</div>
        </div>
        <div>
          <div class="text-xs text-gray-500 uppercase">Entity / Txn ID</div>
          <div class="font-mono text-xs">{{ $event->entity_id ?: '—' }}</div>
        </div>
        <div>
          <div class="text-xs text-gray-500 uppercase">Invoice Number</div>
          <div class="font-mono text-xs">{{ $event->invoice_number ?: '—' }}</div>
        </div>
        <div>
          <div class="text-xs text-gray-500 uppercase">ARB Status</div>
          <div class="font-medium">{{ $event->arb_status ?: '—' }}</div>
        </div>
        <div>
          <div class="text-xs text-gray-500 uppercase">Recorded</div>
          <div class="text-xs">{{ $event->created_at?->format('M j, Y g:i A') }}</div>
        </div>
      </div>
    </div>

    {{-- ─── Raw JSON payload ─────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden" x-data="{ copied: false }">
      <div class="flex items-center justify-between px-5 py-3 border-b border-gray-200">
        <h3 class="font-semibold text-ink">Raw Payload</h3>
        <button type="button"
                @click="navigator.clipboard.writeText($refs.payload.innerText); copied = true; setTimeout(() => copied = false, 1500)"
                class="text-xs px-3 py-1.5 rounded-md border border-gray-200 hover:bg-gray-50 text-gray-700">
          <span x-show="!copied">Copy JSON</span>
          <span x-show="copied" x-cloak class="text-green-700">Copied ✓</span>
        </button>
      </div>
      <pre x-ref="payload"
           class="font-mono text-[12px] leading-relaxed text-gray-800 p-5 overflow-x-auto whitespace-pre-wrap">{{ json_encode($event->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
    </div>

  </div>

  {{-- ─── Right column: matched subscription card ───────────────────────── --}}
  <div class="space-y-4">
    <div class="bg-white rounded-xl border border-gray-200 p-6">
      <h3 class="font-semibold text-ink mb-3">Matched Subscription</h3>
      @if ($event->matchedSubscription)
        @php $sub = $event->matchedSubscription; @endphp
        <div class="text-sm">
          <a href="{{ route('admin.subscription.show', $sub->id) }}"
             class="font-medium text-ink hover:text-gold underline-offset-2 hover:underline">
            {{ $sub->first_name }} {{ $sub->last_name }}
          </a>
          <div class="text-xs text-gray-500 mt-1">{{ $sub->email }}</div>
          <div class="mt-3 pt-3 border-t border-gray-100 space-y-2 text-xs">
            <div>
              <div class="text-gray-500 uppercase">Plan</div>
              <div>{{ $sub->plan_label }}</div>
            </div>
            <div>
              <div class="text-gray-500 uppercase">Status</div>
              <div><x-status-badge :status="$sub->status" /></div>
            </div>
            <div>
              <div class="text-gray-500 uppercase">ARB Subscription ID</div>
              <div class="font-mono">{{ $sub->arb_subscription_id ?: '—' }}</div>
            </div>
          </div>
        </div>
      @else
        <div class="text-sm text-gray-400">
          No subscription matched.
          <div class="text-xs text-gray-400 mt-1">
            This event's <code class="font-mono bg-gray-100 px-1 rounded">entity_id</code> / invoice did not resolve to a local subscription. Common for customer-profile and paymentProfile events.
          </div>
        </div>
      @endif
    </div>
  </div>
</div>

@endsection
