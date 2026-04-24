@extends('admin.layout')

@section('title', $sub->first_name . ' ' . $sub->last_name)

@section('content')

<div class="mb-4">
  <a href="{{ route('admin.subscriptions') }}" class="text-sm text-gray-500 hover:text-ink">← Back to subscriptions</a>
</div>

<div class="grid lg:grid-cols-3 gap-4">

  {{-- ─── Customer card ──────────────────────────────────────────────────────── --}}
  <div class="lg:col-span-2 space-y-4">

    <div class="bg-white rounded-xl border border-gray-200 p-6">
      <div class="flex items-start justify-between mb-4">
        <div>
          <h2 class="text-xl font-semibold text-ink">{{ $sub->first_name }} {{ $sub->last_name }}</h2>
          <div class="text-sm text-gray-500">{{ $sub->email }} · {{ $sub->phone ?: 'no phone' }}</div>
        </div>
        <x-status-badge :status="$sub->status" />
      </div>

      <div class="grid grid-cols-2 gap-4 text-sm pt-4 border-t border-gray-100">
        <div>
          <div class="text-xs text-gray-500 uppercase">Plan</div>
          <div class="font-medium">{{ $sub->plan_label }}</div>
        </div>
        <div>
          <div class="text-xs text-gray-500 uppercase">Referral Code</div>
          <div class="font-medium">
            @if ($sub->referral_code)
              <span class="text-xs bg-gold/15 text-gold-dark px-2 py-0.5 rounded font-semibold">{{ $sub->referral_code }}</span>
            @else
              <span class="text-gray-400">Direct (no code)</span>
            @endif
          </div>
        </div>
        <div>
          <div class="text-xs text-gray-500 uppercase">Initial Charge</div>
          <div class="font-medium">${{ number_format($sub->amount, 2) }}</div>
        </div>
        <div>
          <div class="text-xs text-gray-500 uppercase">Recurring</div>
          <div class="font-medium">${{ number_format($sub->recurring_amount, 2) }} / mo</div>
        </div>
        <div>
          <div class="text-xs text-gray-500 uppercase">Lifetime Revenue</div>
          <div class="font-medium text-green-700">${{ number_format($lifetimeRevenue, 2) }}</div>
          <div class="text-xs text-gray-500">{{ $paymentsCount }} {{ \Illuminate\Support\Str::plural('payment', $paymentsCount) }} captured</div>
        </div>
        <div>
          <div class="text-xs text-gray-500 uppercase">Days Until Next Billing</div>
          @php
            $nb = $sub->next_billing_date;
            $daysUntil = $nb ? max(0, now()->diffInDays($nb, false)) : null;
          @endphp
          <div class="font-medium">
            @if ($nb && $sub->status === 'active')
              {{ $daysUntil }} {{ \Illuminate\Support\Str::plural('day', $daysUntil) }}
              <span class="text-xs text-gray-500">({{ $nb->format('M j, Y') }})</span>
            @else
              <span class="text-gray-400">—</span>
            @endif
          </div>
        </div>
        <div>
          <div class="text-xs text-gray-500 uppercase">Signed Up</div>
          <div class="font-medium">{{ optional($sub->subscribed_at ?? $sub->created_at)->format('M j, Y g:i A') }}</div>
        </div>
        <div>
          <div class="text-xs text-gray-500 uppercase">Next Billing</div>
          <div class="font-medium">{{ $sub->next_billing_date ? $sub->next_billing_date->format('M j, Y') : '—' }}</div>
        </div>
        @if ($sub->terminated_at)
          <div>
            <div class="text-xs text-gray-500 uppercase">Terminated</div>
            <div class="font-medium text-red-700">{{ $sub->terminated_at->format('M j, Y') }}</div>
          </div>
        @endif
        @if ($sub->failed_payment_count > 0)
          <div>
            <div class="text-xs text-gray-500 uppercase">Failed Payments</div>
            <div class="font-medium text-amber-700">{{ $sub->failed_payment_count }}</div>
          </div>
        @endif
      </div>
    </div>

    {{-- ─── Payments timeline ───────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-ink">Payments</h3>
        <span class="text-xs text-gray-500">{{ $sub->payments->count() }} {{ \Illuminate\Support\Str::plural('record', $sub->payments->count()) }}</span>
      </div>

      @if ($sub->payments->isEmpty())
        <div class="text-sm text-gray-400">
          No payment rows recorded.
          <div class="text-xs text-gray-400 mt-1">
            Rows appear here automatically from Auth.net webhooks. For legacy subs, run <code class="font-mono bg-gray-100 px-1 rounded">php artisan payments:backfill</code>.
          </div>
        </div>
      @else
        <div class="overflow-x-auto">
          <table class="w-full text-sm">
            <thead class="text-xs uppercase text-gray-500 border-b border-gray-200">
              <tr>
                <th class="text-left py-2 pr-3 font-medium">When</th>
                <th class="text-left py-2 pr-3 font-medium">Type</th>
                <th class="text-left py-2 pr-3 font-medium">Status</th>
                <th class="text-right py-2 pr-3 font-medium">Amount</th>
                <th class="text-left py-2 font-medium">Txn ID</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              @foreach ($sub->payments as $pay)
                @php
                  $isNeg = in_array($pay->type, ['refund', 'void'], true);
                  $typeCls = match($pay->type) {
                    'initial'   => 'bg-slate-100 text-slate-700',
                    'recurring' => 'bg-green-100 text-green-800',
                    'refund'    => 'bg-red-100 text-red-800',
                    'void'      => 'bg-gray-200 text-gray-700',
                    default     => 'bg-gray-100 text-gray-700',
                  };
                  $statusCls = match($pay->status) {
                    'captured' => 'text-green-700',
                    'refunded' => 'text-red-700',
                    'voided'   => 'text-gray-500',
                    'failed'   => 'text-red-700',
                    default    => 'text-gray-700',
                  };
                @endphp
                <tr class="hover:bg-gray-50">
                  <td class="py-2.5 pr-3 text-xs text-gray-600">
                    {{ $pay->charged_at?->format('M j, Y') ?? '—' }}
                    <div class="text-[11px] text-gray-400">{{ $pay->charged_at?->format('g:i A') }}</div>
                  </td>
                  <td class="py-2.5 pr-3">
                    <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-semibold {{ $typeCls }}">{{ ucfirst($pay->type) }}</span>
                  </td>
                  <td class="py-2.5 pr-3 text-xs font-medium {{ $statusCls }}">{{ ucfirst($pay->status) }}</td>
                  <td class="py-2.5 pr-3 text-right font-semibold {{ $isNeg ? 'text-red-700' : 'text-ink' }}">
                    {{ $isNeg ? '-' : '' }}${{ number_format($pay->amount, 2) }}
                  </td>
                  <td class="py-2.5 font-mono text-[11px] text-gray-500">{{ $pay->transaction_id ?: '—' }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      @endif
    </div>

    {{-- Address + Auth.net IDs --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
      <h3 class="font-semibold text-ink mb-3">Address & Payment IDs</h3>
      <div class="grid grid-cols-2 gap-4 text-sm">
        <div>
          <div class="text-xs text-gray-500 uppercase">Address</div>
          <div>{{ $sub->address }}<br>{{ $sub->city }}, {{ $sub->state }} {{ $sub->zip }}</div>
        </div>
        <div class="space-y-2">
          <div>
            <div class="text-xs text-gray-500 uppercase">ARB Subscription ID</div>
            <div class="font-mono text-xs">{{ $sub->arb_subscription_id ?: '—' }}</div>
          </div>
          <div>
            <div class="text-xs text-gray-500 uppercase">Invoice</div>
            <div class="font-mono text-xs">{{ $sub->invoice_number }}</div>
          </div>
          <div>
            <div class="text-xs text-gray-500 uppercase">Transaction ID</div>
            <div class="font-mono text-xs">{{ $sub->transaction_id ?: '—' }}</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- ─── Event timeline ─────────────────────────────────────────────────────── --}}
  <div class="bg-white rounded-xl border border-gray-200 p-6">
    <h3 class="font-semibold text-ink mb-4">Event Timeline</h3>

    @forelse ($sub->events as $e)
      <div class="relative pl-5 pb-5 border-l border-gray-200 last:border-transparent">
        @php
          $dot = match($e->event_type) {
            'payment_failed'    => 'bg-red-500',
            'payment_recovered' => 'bg-green-500',
            'terminated'        => 'bg-gray-500',
            'ghl_notified'      => 'bg-blue-500',
            default             => 'bg-gold',
          };
        @endphp
        <div class="absolute w-2.5 h-2.5 rounded-full {{ $dot }} -left-[5px] top-1 ring-2 ring-white"></div>
        <div class="text-sm font-medium text-ink capitalize">{{ str_replace('_', ' ', $e->event_type) }}</div>
        <div class="text-xs text-gray-500">{{ $e->created_at->format('M j, Y g:i A') }}</div>
        @if ($e->note)
          <div class="text-xs text-gray-600 mt-1">{{ $e->note }}</div>
        @endif
      </div>
    @empty
      <div class="text-sm text-gray-400">No events recorded yet.</div>
    @endforelse
  </div>

</div>

@endsection
