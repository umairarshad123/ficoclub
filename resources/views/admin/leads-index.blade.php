@extends('admin.layout')

@section('title', 'Leads')

@section('content')

{{-- ─── Quick filter tabs ──────────────────────────────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-200 p-4 mb-4">
  <div class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-3">Quick filters</div>
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
    @php
      $current = $filters['lead_status'] ?? '';
      $tabs = [
        ''            => ['label' => 'All Leads',   'count' => $tabCounts['total'],      'tone' => 'slate'],
        'converted'   => ['label' => 'Converted',   'count' => $tabCounts['converted'],  'tone' => 'green'],
        'in_ghl'      => ['label' => 'In GHL',      'count' => $tabCounts['in_ghl'],     'tone' => 'blue'],
        'not_in_ghl'  => ['label' => 'Not in GHL',  'count' => $tabCounts['not_in_ghl'], 'tone' => 'red'],
      ];
    @endphp
    @foreach ($tabs as $key => $t)
      @php
        $isActive = $current === $key;
        $activeCls = match($t['tone']) {
          'green' => 'border-green-500 bg-green-50',
          'blue'  => 'border-blue-500 bg-blue-50',
          'red'   => 'border-red-500 bg-red-50',
          'slate' => 'border-olive bg-olive/5',
          default => 'border-gray-400 bg-gray-50',
        };
        $numTone = match($t['tone']) {
          'green' => 'text-green-700',
          'blue'  => 'text-blue-700',
          'red'   => 'text-red-700',
          'slate' => 'text-olive-dark',
          default => 'text-gray-700',
        };
      @endphp
      <a href="{{ route('admin.leads', array_merge(request()->except('lead_status','page'), $key ? ['lead_status' => $key] : [])) }}"
         class="block rounded-lg border-2 transition p-3 text-center {{ $isActive ? $activeCls : 'border-gray-200 hover:border-gray-300 bg-white' }}">
        <div class="text-2xl font-bold {{ $numTone }}">{{ number_format($t['count']) }}</div>
        <div class="text-xs font-medium text-gray-600 mt-0.5">{{ $t['label'] }}</div>
      </a>
    @endforeach
  </div>
</div>

{{-- ─── Filter bar ─────────────────────────────────────────────────────────── --}}
<form method="GET" action="{{ route('admin.leads') }}"
      class="bg-white rounded-xl border border-gray-200 p-4 mb-4">
  <div class="grid grid-cols-2 lg:grid-cols-5 gap-3">

    <div class="col-span-2">
      <label class="text-xs font-medium text-gray-600">Search</label>
      <input type="text" name="q" value="{{ $filters['q'] ?? '' }}"
             placeholder="Name, email, phone, business…"
             class="mt-1 w-full text-sm px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gold focus:border-transparent outline-none">
    </div>

    <div>
      <label class="text-xs font-medium text-gray-600">Funding Type</label>
      <select name="funding_type" class="mt-1 w-full text-sm px-3 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-gold">
        <option value="">All</option>
        <option value="personal" @selected(($filters['funding_type'] ?? '') === 'personal')>Personal</option>
        <option value="business" @selected(($filters['funding_type'] ?? '') === 'business')>Business</option>
        <option value="both"     @selected(($filters['funding_type'] ?? '') === 'both')>Both</option>
      </select>
    </div>

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

  <div class="flex items-center gap-2 mt-3 justify-end">
    <a href="{{ route('admin.leads') }}" class="px-4 py-2 text-sm text-gray-600 hover:text-ink">Reset</a>
    <a href="{{ route('admin.leads.csv', request()->query()) }}"
       class="px-4 py-2 text-sm font-medium bg-white border border-gray-300 rounded-lg hover:bg-gray-50">
      Export CSV
    </a>
    <button type="submit"
            class="px-4 py-2 text-sm font-semibold bg-olive-dark text-paper rounded-lg hover:bg-olive transition">
      Apply
    </button>
  </div>
</form>

{{-- ─── Results table ──────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
  <div class="flex items-center justify-between px-5 py-3 border-b border-gray-200 text-sm">
    <div class="text-gray-600">
      Showing <span class="font-medium text-ink">{{ $leads->firstItem() ?? 0 }}–{{ $leads->lastItem() ?? 0 }}</span>
      of <span class="font-medium text-ink">{{ number_format($leads->total()) }}</span>
    </div>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="text-xs uppercase text-gray-500 bg-gray-50 border-b border-gray-200">
        <tr>
          <th class="text-left py-2.5 px-5 font-medium">Lead</th>
          <th class="text-left py-2.5 px-3 font-medium">Contact</th>
          <th class="text-left py-2.5 px-3 font-medium">Funding</th>
          <th class="text-left py-2.5 px-3 font-medium">Location</th>
          <th class="text-left py-2.5 px-3 font-medium">Converted?</th>
          <th class="text-left py-2.5 px-3 font-medium">GHL</th>
          <th class="text-left py-2.5 px-3 font-medium">Created</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse ($leads as $lead)
          <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('admin.lead.show', $lead->id) }}'">
            <td class="py-3 px-5">
              <div class="font-medium text-ink">
                {{ $lead->first_name ?: '—' }} {{ $lead->last_name }}
              </div>
              @if ($lead->biz_name)
                <div class="text-xs text-gray-500">{{ $lead->biz_name }}</div>
              @endif
            </td>
            <td class="py-3 px-3 text-xs">
              <div class="text-gray-700">{{ $lead->final_email ?? $lead->email ?? '—' }}</div>
              <div class="text-gray-500">{{ $lead->final_phone ?? $lead->phone ?? '—' }}</div>
            </td>
            <td class="py-3 px-3">
              @if ($lead->funding_type)
                <span class="text-xs bg-gray-100 text-gray-700 px-2 py-0.5 rounded capitalize">{{ $lead->funding_type }}</span>
              @else
                <span class="text-xs text-gray-400">—</span>
              @endif
            </td>
            <td class="py-3 px-3 text-xs text-gray-600">
              {{ $lead->city ? $lead->city . ', ' : '' }}{{ $lead->state ?? '—' }}
            </td>
            <td class="py-3 px-3">
              @if ($lead->converted)
                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">✓ Paying</span>
              @else
                <span class="text-xs text-gray-400">—</span>
              @endif
            </td>
            <td class="py-3 px-3">
              @if ($lead->ghl_pushed)
                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">In GHL</span>
              @else
                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600">—</span>
              @endif
            </td>
            <td class="py-3 px-3 text-gray-500 text-xs">{{ $lead->created_at->format('M j, Y') }}</td>
          </tr>
        @empty
          <tr><td colspan="7" class="py-10 text-center text-gray-400">No leads match your filters.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="px-5 py-3 border-t border-gray-200">
    {{ $leads->links() }}
  </div>
</div>

@endsection
