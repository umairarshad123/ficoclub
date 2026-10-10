@extends('admin.layout')

@section('title', 'Site Content')

@php
  $input = 'mt-1 w-full text-sm px-3 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-gold';
  $label = 'text-xs font-semibold text-gray-600';
@endphp

@section('content')

@if ($errors->any())
  <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">⚠ {{ $errors->first() }}</div>
@endif

<form method="POST" action="{{ route('admin.site-content.update') }}"
      x-data="{
        salesOpen: {{ old('sales_open', $c['sales_open']) ? 'true' : 'false' }},
        annOn: {{ old('announce_on', $c['announce_on']) ? 'true' : 'false' }},
        annText: @js(old('announce_text', $c['announce_text'])),
        annStyle: @js(old('announce_style', $c['announce_style'])),
        seoTitle: @js(old('seo_title', $c['seo_title'])),
        seoDesc: @js(old('seo_description', $c['seo_description'])),
        bg: { green: '#16a34a', gold: '#d97706', red: '#dc2626', navy: '#0F2044' },
      }"
      class="space-y-6">
  @csrf

  <div class="flex flex-wrap items-center gap-3">
    <p class="text-sm text-gray-600 flex-1 min-w-[260px]">Edit what visitors see on <a href="{{ url('/') }}" target="_blank" class="text-blue-700 hover:underline">850ficoclub.com</a>. Everything goes live the moment you click <strong>Save all changes</strong>.</p>
    <a href="{{ url('/') }}" target="_blank" class="px-4 py-2 text-sm font-semibold bg-white border border-gray-300 rounded-lg hover:bg-gray-50">View website ↗</a>
    <button class="px-5 py-2 text-sm font-bold bg-gold text-white rounded-lg hover:bg-gold-dark shadow-[0_4px_14px_rgba(34,197,94,.35)]">Save all changes</button>
  </div>

  {{-- ─── Sales switch ─── --}}
  <section class="rounded-2xl border-2 p-5" :class="salesOpen ? 'border-green-200 bg-white' : 'border-amber-300 bg-amber-50'">
    <div class="flex flex-col md:flex-row md:items-center gap-4">
      <div class="flex-1">
        <h2 class="font-semibold text-ink">Accepting new payments</h2>
        <p class="text-xs text-gray-500 mt-0.5">Turn off to pause sales: the website stays up, but checkout shows your message and won't take payments. (For taking the whole site down, use Website → Maintenance.)</p>
      </div>
      <label class="flex items-center gap-3 cursor-pointer select-none">
        <input type="hidden" name="sales_open" value="0">
        <input type="checkbox" name="sales_open" value="1" x-model="salesOpen" class="sr-only peer">
        <span class="w-12 h-7 rounded-full relative transition" :class="salesOpen ? 'bg-gold' : 'bg-gray-300'">
          <span class="absolute top-1 left-1 w-5 h-5 bg-white rounded-full shadow transition" :class="salesOpen ? 'translate-x-5' : ''"></span>
        </span>
        <span class="text-sm font-bold" :class="salesOpen ? 'text-gold-dark' : 'text-amber-700'" x-text="salesOpen ? 'Sales open' : 'Sales paused'"></span>
      </label>
    </div>
    <div class="mt-4" x-show="!salesOpen" x-cloak>
      <label class="{{ $label }}">Message shown on checkout while paused</label>
      <input name="sales_paused_message" value="{{ old('sales_paused_message', $c['sales_paused_message']) }}" maxlength="200" class="{{ $input }}">
    </div>
  </section>

  {{-- ─── Announcement bar ─── --}}
  <section class="bg-white rounded-2xl border border-gray-200 p-5">
    <div class="flex items-center justify-between gap-3">
      <div>
        <h2 class="font-semibold text-ink">Announcement bar</h2>
        <p class="text-xs text-gray-500 mt-0.5">A message across the top of the homepage (replaces the scrolling ticker while on) and the checkout page. Great for promos and holiday hours.</p>
      </div>
      <label class="flex items-center gap-2 text-sm font-semibold cursor-pointer">
        <input type="hidden" name="announce_on" value="0">
        <input type="checkbox" name="announce_on" value="1" x-model="annOn" class="w-4 h-4 accent-green-600"> Show
      </label>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
      <div class="md:col-span-2">
        <label class="{{ $label }}">Message</label>
        <input name="announce_text" x-model="annText" maxlength="140" placeholder="🎉 Holiday special — save $100 on Gold this week" class="{{ $input }}">
      </div>
      <div>
        <label class="{{ $label }}">Link <span class="font-normal text-gray-400">(optional)</span></label>
        <input name="announce_link" value="{{ old('announce_link', $c['announce_link']) }}" placeholder="https://850ficoclub.com/#pricing" class="{{ $input }}">
      </div>
      <div>
        <label class="{{ $label }}">Colour</label>
        <select name="announce_style" x-model="annStyle" class="{{ $input }}">
          <option value="green">Green</option><option value="gold">Gold</option><option value="red">Red</option><option value="navy">Navy</option>
        </select>
      </div>
    </div>
    <div class="mt-3 rounded-lg px-4 py-2.5 text-center text-white text-sm font-extrabold" :style="'background:' + bg[annStyle]" x-show="annOn && annText" x-text="annText"></div>
  </section>

  {{-- ─── Hero ─── --}}
  <section class="bg-white rounded-2xl border border-gray-200 p-5">
    <h2 class="font-semibold text-ink">Homepage headline</h2>
    <p class="text-xs text-gray-500 mt-0.5">The big section at the top of the homepage. Green parts are shown in brand green.</p>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
      <div class="md:col-span-2">
        <label class="{{ $label }}">Badge above the headline</label>
        <input name="hero_eyebrow" value="{{ old('hero_eyebrow', $c['hero_eyebrow']) }}" maxlength="120" class="{{ $input }}">
      </div>
      <div><label class="{{ $label }}">Headline — line 1</label><input name="hero_line1" value="{{ old('hero_line1', $c['hero_line1']) }}" maxlength="60" class="{{ $input }}"></div>
      <div><label class="{{ $label }}">Headline — <span class="text-gold-dark">green part</span></label><input name="hero_green1" value="{{ old('hero_green1', $c['hero_green1']) }}" maxlength="60" class="{{ $input }}"></div>
      <div><label class="{{ $label }}">Headline — middle</label><input name="hero_mid" value="{{ old('hero_mid', $c['hero_mid']) }}" maxlength="60" class="{{ $input }}"></div>
      <div><label class="{{ $label }}">Headline — <span class="text-gold-dark">green ending</span> (underlined)</label><input name="hero_green2" value="{{ old('hero_green2', $c['hero_green2']) }}" maxlength="60" class="{{ $input }}"></div>
      <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-[1fr_160px] gap-4">
        <div><label class="{{ $label }}">Sub-headline</label><textarea name="hero_lead" rows="2" maxlength="400" class="{{ $input }}">{{ old('hero_lead', $c['hero_lead']) }}</textarea></div>
        <div><label class="{{ $label }}">Bold ending <span class="font-normal text-gray-400">(optional)</span></label><input name="hero_lead_strong" value="{{ old('hero_lead_strong', $c['hero_lead_strong']) }}" maxlength="40" class="{{ $input }}"></div>
      </div>
      <div><label class="{{ $label }}">Green button</label><input name="hero_cta1" value="{{ old('hero_cta1', $c['hero_cta1']) }}" maxlength="40" class="{{ $input }}"></div>
      <div><label class="{{ $label }}">Outline button</label><input name="hero_cta2" value="{{ old('hero_cta2', $c['hero_cta2']) }}" maxlength="40" class="{{ $input }}"></div>
    </div>
  </section>

  <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
    {{-- ─── Stats ─── --}}
    <section class="bg-white rounded-2xl border border-gray-200 p-5">
      <h2 class="font-semibold text-ink">Stats band</h2>
      <p class="text-xs text-gray-500 mt-0.5">The five numbers that count up on the homepage.</p>
      <div class="mt-4 space-y-2">
        <div class="grid grid-cols-[48px_96px_64px_1fr] gap-2 text-[11px] font-semibold text-gray-500 uppercase"><span>Before</span><span>Number</span><span>After</span><span>Label</span></div>
        @foreach (old('stats', $c['stats']) as $i => $s)
          <div class="grid grid-cols-[48px_96px_64px_1fr] gap-2">
            <input name="stats[{{ $i }}][prefix]" value="{{ $s['prefix'] }}" maxlength="4" class="{{ $input }} !mt-0 text-center">
            <input name="stats[{{ $i }}][count]" type="number" min="0" value="{{ $s['count'] }}" class="{{ $input }} !mt-0">
            <input name="stats[{{ $i }}][suffix]" value="{{ $s['suffix'] }}" maxlength="10" class="{{ $input }} !mt-0 text-center">
            <input name="stats[{{ $i }}][label]" value="{{ $s['label'] }}" maxlength="80" class="{{ $input }} !mt-0">
          </div>
        @endforeach
      </div>
    </section>

    {{-- ─── Ticker ─── --}}
    <section class="bg-white rounded-2xl border border-gray-200 p-5">
      <h2 class="font-semibold text-ink">Scrolling ticker</h2>
      <p class="text-xs text-gray-500 mt-0.5">The moving strip at the very top of the homepage. One item per line.</p>
      <textarea name="ticker" rows="9" class="{{ $input }} font-mono">{{ old('ticker', implode("\n", $c['ticker'])) }}</textarea>
    </section>
  </div>

  <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
    {{-- ─── Referral partners ─── --}}
    <section class="bg-white rounded-2xl border border-gray-200 p-5">
      <h2 class="font-semibold text-ink">Referral partner codes</h2>
      <p class="text-xs text-gray-500 mt-0.5">Codes accepted in referral links (<span class="font-mono">850ficoclub.com/?ref=CODE</span>). Sales with a code go to the GHL referral webhook and show in Referrals.</p>
      <input name="referral_codes" value="{{ old('referral_codes', implode(', ', \App\Support\SiteContent::referralCodes())) }}" placeholder="DL, EL, NL" class="{{ $input }} font-mono uppercase">
      <div class="mt-3 flex flex-wrap gap-2 text-xs">
        @foreach (\App\Support\SiteContent::referralCodes() as $code)
          <span class="px-2 py-1 rounded bg-gold/15 text-gold-dark font-mono">850ficoclub.com/?ref={{ $code }}</span>
        @endforeach
      </div>
    </section>

    {{-- ─── Booking + SEO ─── --}}
    <section class="bg-white rounded-2xl border border-gray-200 p-5 space-y-4">
      <div>
        <h2 class="font-semibold text-ink">Consultation calendar</h2>
        <p class="text-xs text-gray-500 mt-0.5">The GHL booking calendar behind "Free Consultation".</p>
        <input name="booking_url" value="{{ old('booking_url', $c['booking_url']) }}" class="{{ $input }}">
      </div>
      <div>
        <h2 class="font-semibold text-ink">Google search listing</h2>
        <label class="{{ $label }} mt-2 block">Title <span class="font-normal" :class="seoTitle.length > 60 ? 'text-amber-600' : 'text-gray-400'" x-text="seoTitle.length + '/60'"></span></label>
        <input name="seo_title" x-model="seoTitle" maxlength="70" class="{{ $input }}">
        <label class="{{ $label }} mt-3 block">Description <span class="font-normal" :class="seoDesc.length > 155 ? 'text-amber-600' : 'text-gray-400'" x-text="seoDesc.length + '/155'"></span></label>
        <textarea name="seo_description" x-model="seoDesc" rows="2" maxlength="170" class="{{ $input }}"></textarea>
        <div class="mt-3 rounded-lg border border-gray-200 p-3">
          <div class="text-[11px] text-gray-500">850ficoclub.com</div>
          <div class="text-[#1a0dab] text-base leading-snug" x-text="seoTitle"></div>
          <div class="text-xs text-gray-600 leading-snug" x-text="seoDesc"></div>
        </div>
      </div>
    </section>
  </div>

  <div class="flex items-center gap-3">
    <button class="px-5 py-2.5 text-sm font-bold bg-gold text-white rounded-lg hover:bg-gold-dark shadow-[0_4px_14px_rgba(34,197,94,.35)]">Save all changes</button>
    @if ($customized)
      <button type="submit" form="reset-content" class="ml-auto text-xs text-gray-500 hover:text-red-700" onclick="return confirm('Reset all website text back to the original?')">Reset everything to original</button>
    @endif
  </div>
</form>

@if ($customized)
  <form id="reset-content" method="POST" action="{{ route('admin.site-content.reset') }}" class="hidden">@csrf</form>
@endif

@endsection
