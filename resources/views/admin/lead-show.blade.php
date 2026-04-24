@extends('admin.layout')

@section('title', 'Lead Detail')

@section('content')

<div class="mb-4">
  <a href="{{ route('admin.leads') }}" class="text-sm text-gray-600 hover:text-ink">← Back to leads</a>
</div>

{{-- ─── Header card ────────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-200 p-6 mb-4">
  <div class="flex items-start justify-between">
    <div>
      <div class="text-2xl font-bold text-ink">
        {{ $lead->first_name ?: 'Unnamed' }} {{ $lead->last_name }}
      </div>
      <div class="text-sm text-gray-600 mt-1">
        {{ $lead->final_email ?? $lead->email ?? '—' }}
        @if (($lead->final_email ?? $lead->email) && ($lead->final_phone ?? $lead->phone))
          ·
        @endif
        {{ $lead->final_phone ?? $lead->phone ?? '' }}
      </div>
      <div class="text-xs text-gray-500 mt-2">
        Lead #{{ $lead->id }} · Created {{ $lead->created_at->format('M j, Y g:i A') }}
      </div>
    </div>
    <div class="flex flex-col gap-2 items-end">
      @if ($matchingSub)
        <a href="{{ route('admin.subscription.show', $matchingSub->id) }}"
           class="px-3 py-1.5 text-xs font-medium bg-green-100 text-green-800 rounded-lg hover:bg-green-200">
          ✓ Converted — View Subscription
        </a>
      @endif
      @if ($lead->ghl_pushed)
        <span class="px-3 py-1.5 text-xs font-medium bg-blue-100 text-blue-800 rounded-lg">In GHL ✓</span>
      @else
        <span class="px-3 py-1.5 text-xs font-medium bg-red-100 text-red-700 rounded-lg">Not in GHL</span>
      @endif
      @if ($lead->sheets_pushed)
        <span class="px-3 py-1.5 text-xs font-medium bg-indigo-100 text-indigo-800 rounded-lg">In Sheets ✓</span>
      @endif
    </div>
  </div>
</div>

{{-- ─── Info grid ──────────────────────────────────────────────────────────── --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">

  {{-- Personal info --}}
  <div class="bg-white rounded-xl border border-gray-200 p-5">
    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-3">Personal Information</div>
    <dl class="grid grid-cols-2 gap-3 text-sm">
      <div>
        <dt class="text-xs text-gray-500">Date of Birth</dt>
        <dd class="text-ink">{{ $lead->dob ? \Carbon\Carbon::parse($lead->dob)->format('M j, Y') : '—' }}</dd>
      </div>
      <div>
        <dt class="text-xs text-gray-500">SSN (Last 4)</dt>
        <dd class="text-ink">{{ $lead->sc_ssn_last4 ? '****-**-' . $lead->sc_ssn_last4 : '—' }}</dd>
      </div>
      <div class="col-span-2">
        <dt class="text-xs text-gray-500">Address</dt>
        <dd class="text-ink">
          {{ $lead->address ?: '—' }}
          @if ($lead->city || $lead->state || $lead->zip)
            <div class="text-gray-600">{{ $lead->city }}{{ $lead->city && $lead->state ? ', ' : '' }}{{ $lead->state }} {{ $lead->zip }}</div>
          @endif
        </dd>
      </div>
      <div>
        <dt class="text-xs text-gray-500">Years at Address</dt>
        <dd class="text-ink">{{ $lead->years_at_address ?: '—' }}</dd>
      </div>
      <div>
        <dt class="text-xs text-gray-500">Own or Rent</dt>
        <dd class="text-ink capitalize">{{ $lead->own_or_rent ?: '—' }}</dd>
      </div>
    </dl>
  </div>

  {{-- Financial info --}}
  <div class="bg-white rounded-xl border border-gray-200 p-5">
    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-3">Financial Snapshot</div>
    <dl class="grid grid-cols-2 gap-3 text-sm">
      <div>
        <dt class="text-xs text-gray-500">Funding Type</dt>
        <dd class="text-ink capitalize">{{ $lead->funding_type ?: '—' }}</dd>
      </div>
      <div>
        <dt class="text-xs text-gray-500">Employment</dt>
        <dd class="text-ink">{{ $lead->employment_status ?: '—' }}</dd>
      </div>
      <div>
        <dt class="text-xs text-gray-500">Annual Income</dt>
        <dd class="text-ink">{{ $lead->annual_income ?: '—' }}</dd>
      </div>
      <div>
        <dt class="text-xs text-gray-500">Monthly Housing</dt>
        <dd class="text-ink">{{ $lead->monthly_housing ?: '—' }}</dd>
      </div>
    </dl>
  </div>
</div>

{{-- ─── Business info (only if they have it) ───────────────────────────────── --}}
@if ($lead->biz_name || $lead->biz_entity_type)
<div class="bg-white rounded-xl border border-gray-200 p-5 mb-4">
  <div class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-3">Business Information</div>
  <dl class="grid grid-cols-2 lg:grid-cols-3 gap-3 text-sm">
    <div>
      <dt class="text-xs text-gray-500">Business Name</dt>
      <dd class="text-ink">{{ $lead->biz_name ?: '—' }}</dd>
    </div>
    <div>
      <dt class="text-xs text-gray-500">Entity Type</dt>
      <dd class="text-ink">{{ $lead->biz_entity_type ?: '—' }}</dd>
    </div>
    <div>
      <dt class="text-xs text-gray-500">Incorp State</dt>
      <dd class="text-ink">{{ $lead->biz_incorp_state ?: '—' }}</dd>
    </div>
    <div>
      <dt class="text-xs text-gray-500">Annual Revenue</dt>
      <dd class="text-ink">{{ $lead->biz_annual_revenue ?: '—' }}</dd>
    </div>
    <div>
      <dt class="text-xs text-gray-500">Has Directors</dt>
      <dd class="text-ink">{{ $lead->biz_has_directors ?: '—' }}</dd>
    </div>
    <div>
      <dt class="text-xs text-gray-500">Has Financials</dt>
      <dd class="text-ink">{{ $lead->biz_has_financials ?: '—' }}</dd>
    </div>
    <div class="col-span-2 lg:col-span-3">
      <dt class="text-xs text-gray-500">Business Address</dt>
      <dd class="text-ink">
        {{ $lead->biz_address ?: '—' }}
        @if ($lead->biz_city || $lead->biz_state)
          <div class="text-gray-600">{{ $lead->biz_city }}{{ $lead->biz_city && $lead->biz_state ? ', ' : '' }}{{ $lead->biz_state }} {{ $lead->biz_zip }}</div>
        @endif
      </dd>
    </div>
    <div>
      <dt class="text-xs text-gray-500">Business Phone</dt>
      <dd class="text-ink">{{ $lead->biz_phone ?: '—' }}</dd>
    </div>
    <div>
      <dt class="text-xs text-gray-500">Business Email</dt>
      <dd class="text-ink">{{ $lead->biz_email ?: '—' }}</dd>
    </div>
    <div>
      <dt class="text-xs text-gray-500">Website</dt>
      <dd class="text-ink">
        @if ($lead->biz_website)
          <a href="{{ str_starts_with($lead->biz_website, 'http') ? $lead->biz_website : 'https://' . $lead->biz_website }}"
             target="_blank" class="text-gold hover:underline">{{ $lead->biz_website }}</a>
        @else
          —
        @endif
      </dd>
    </div>
  </dl>
</div>
@endif

{{-- ─── SmartCredit / Integration info ─────────────────────────────────────── --}}
@if ($lead->sc_email)
<div class="bg-white rounded-xl border border-gray-200 p-5 mb-4">
  <div class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-3">SmartCredit Account</div>
  <dl class="grid grid-cols-2 gap-3 text-sm">
    <div>
      <dt class="text-xs text-gray-500">SC Email</dt>
      <dd class="text-ink">{{ $lead->sc_email }}</dd>
    </div>
    <div>
      <dt class="text-xs text-gray-500">Password</dt>
      <dd class="text-ink text-xs italic text-gray-400">Encrypted</dd>
    </div>
  </dl>
</div>
@endif

{{-- ─── Tracking info ──────────────────────────────────────────────────────── --}}
<div class="bg-white rounded-xl border border-gray-200 p-5">
  <div class="text-xs font-semibold uppercase tracking-wide text-gray-500 mb-3">Tracking</div>
  <dl class="grid grid-cols-2 gap-3 text-sm">
    <div>
      <dt class="text-xs text-gray-500">IP Address</dt>
      <dd class="text-ink font-mono text-xs">{{ $lead->ip_address ?: '—' }}</dd>
    </div>
    <div>
      <dt class="text-xs text-gray-500">Lead UUID</dt>
      <dd class="text-ink font-mono text-xs">{{ $lead->uuid }}</dd>
    </div>
    <div class="col-span-2">
      <dt class="text-xs text-gray-500">User Agent</dt>
      <dd class="text-ink font-mono text-xs break-all">{{ $lead->user_agent ?: '—' }}</dd>
    </div>
  </dl>
</div>

@endsection
