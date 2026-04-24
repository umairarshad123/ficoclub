@extends('admin.layout')

@section('title', 'Subscriptions')

@section('content')

{{-- ─── Auth.net-style quick filter tabs ──────────────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-200 p-4 mb-4">
  <div class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-3">Quick filters</div>
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
    @php
      $current = $filters['status'] ?? '';
      $tabs = [
        ''           => ['label' => 'All',        'count' => $tabCounts['total'],     'tone' => 'slate'],
        'active'     => ['label' => 'Active',     'count' => $tabCounts['active'],    'tone' => 'green'],
        'past_due'   => ['label' => 'Suspended',  'count' => $tabCounts['suspended'], 'tone' => 'amber'],
        'cancelled'  => ['label' => 'Cancelled',  'count' => $tabCounts['cancelled'], 'tone' => 'red'],
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
      <a href="{{ route('admin.subscriptions', array_merge(request()->except('status','page'), $key ? ['status' => $key] : [])) }}"
         class="block rounded-lg border-2 transition p-3 text-center {{ $isActive ? $activeCls : 'border-gray-200 hover:border-gray-300 bg-white' }}">
        <div class="text-2xl font-bold {{ $numTone }}">{{ number_format($t['count']) }}</div>
        <div class="text-xs font-medium text-gray-600 mt-0.5">{{ $t['label'] }}</div>
      </a>
    @endforeach
  </div>
</div>

{{-- ─── Filter bar ─────────────────────────────────────────────────────────── --}}
<form method="GET" action="{{ route('admin.subscriptions') }}"
      class="bg-white rounded-xl border border-gray-200 p-4 mb-4">
  <div class="grid grid-cols-2 lg:grid-cols-6 gap-3">

    <div class="col-span-2 lg:col-span-2">
      <label class="text-xs font-medium text-gray-600">Search</label>
      <input type="text" name="q" value="{{ $filters['q'] ?? '' }}"
             placeholder="Name, email, phone, invoice…"
             class="mt-1 w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent outline-none">
    </div>

    <div>
      <label class="text-xs font-medium text-gray-600">Status</label>
      <select name="status" class="mt-1 w-full text-sm px-3 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-gold">
        <option value="">All</option>
        <option value="active"    @selected(($filters['status'] ?? '') === 'active')>Active</option>
        <option value="past_due"  @selected(($filters['status'] ?? '') === 'past_due')>Suspended</option>
        <option value="cancelled" @selected(($filters['status'] ?? '') === 'cancelled')>Cancelled</option>
      </select>
    </div>

    <div>
      <label class="text-xs font-medium text-gray-600">Plan</label>
      <select name="plan" class="mt-1 w-full text-sm px-3 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-gold">
        <option value="">All</option>
        @foreach (['silver','gold','platinum'] as $p)
          <option value="{{ $p }}" @selected(($filters['plan'] ?? '') === $p)>{{ ucfirst($p) }}</option>
        @endforeach
      </select>
    </div>

    <div>
      <label class="text-xs font-medium text-gray-600">Referral</label>
      <select name="referral" class="mt-1 w-full text-sm px-3 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-gold">
        <option value="">All</option>
        @foreach (['DL','EL','NL','EXP'] as $r)
          <option value="{{ $r }}" @selected(($filters['referral'] ?? '') === $r)>{{ $r }}</option>
        @endforeach
        <option value="direct" @selected(($filters['referral'] ?? '') === 'direct')>Direct (no code)</option>
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

    <div class="flex items-end gap-2 col-span-2 lg:col-span-4 justify-end">
      <a href="{{ route('admin.subscriptions') }}"
         class="px-4 py-2 text-sm text-gray-600 hover:text-ink">Reset</a>

      <a href="{{ route('admin.subscriptions.csv', request()->query()) }}"
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
      Showing <span class="font-medium text-ink">{{ $subs->firstItem() ?? 0 }}–{{ $subs->lastItem() ?? 0 }}</span>
      of <span class="font-medium text-ink">{{ number_format($subs->total()) }}</span>
    </div>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="text-xs uppercase text-gray-500 bg-gray-50 border-b border-gray-200">
        <tr>
          <th class="text-left py-2.5 px-5 font-medium">Customer</th>
          <th class="text-left py-2.5 px-3 font-medium">Plan</th>
          <th class="text-right py-2.5 px-3 font-medium">Initial</th>
          <th class="text-right py-2.5 px-3 font-medium">Monthly</th>
          <th class="text-left py-2.5 px-3 font-medium">Status</th>
          <th class="text-left py-2.5 px-3 font-medium">Ref</th>
          <th class="text-left py-2.5 px-3 font-medium">Signed Up</th>
          <th class="text-left py-2.5 px-3 font-medium">Next Billing</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse ($subs as $s)
          <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('admin.subscription.show', $s->id) }}'">
            <td class="py-3 px-5">
              <div class="font-medium text-ink">{{ $s->first_name }} {{ $s->last_name }}</div>
              <div class="text-xs text-gray-500">{{ $s->email }}</div>
            </td>
            <td class="py-3 px-3 text-gray-700">{{ $s->plan_label }}</td>
            <td class="py-3 px-3 text-right">${{ number_format($s->amount, 2) }}</td>
            <td class="py-3 px-3 text-right font-medium">${{ number_format($s->recurring_amount, 2) }}</td>
            <td class="py-3 px-3"><x-status-badge :status="$s->status" /></td>
            <td class="py-3 px-3">
              @if ($s->referral_code)
                <span class="text-xs bg-gold/15 text-gold-dark px-1.5 py-0.5 rounded font-semibold">{{ $s->referral_code }}</span>
              @else
                <span class="text-xs text-gray-400">—</span>
              @endif
            </td>
            <td class="py-3 px-3 text-gray-500 text-xs">{{ $s->created_at->format('M j, Y') }}</td>
            <td class="py-3 px-3 text-gray-500 text-xs">
              {{ $s->next_billing_date ? $s->next_billing_date->format('M j, Y') : '—' }}
            </td>
          </tr>
        @empty
          <tr><td colspan="8" class="py-10 text-center text-gray-400">No results match your filters.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="px-5 py-3 border-t border-gray-200">
    {{ $subs->links() }}
  </div>
</div>

@endsection