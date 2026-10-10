@extends('admin.layout')

@section('title', 'Plans & Pricing')

@php
  $cardBg = [
    'silver-card'   => 'linear-gradient(160deg,#22c55e 0%,#15803d 100%)',
    'gold-card'     => 'linear-gradient(160deg,#fb923c 0%,#c2410c 100%)',
    'platinum-card' => 'linear-gradient(160deg,#f87171 0%,#b91c1c 100%)',
  ];
@endphp

@section('content')

<div class="flex flex-col lg:flex-row lg:items-start gap-4 mb-6">
  <div class="flex-1 text-sm text-gray-600 leading-relaxed">
    Edit what customers see on the pricing cards and checkout. Changes go live the moment you click <strong>Save</strong>.
    <span class="block mt-1 text-xs text-gray-500">Changing a <strong>price</strong> automatically creates a matching product in Commas, so customers are always charged the price shown. Past sales aren't affected.</span>
  </div>

  {{-- Card processing fee (Commas surcharge) --}}
  <form method="POST" action="{{ route('admin.plans.surcharge') }}" class="bg-white rounded-2xl border border-gray-200 p-4 w-full lg:w-[380px]">
    @csrf
    <div class="text-xs font-semibold uppercase tracking-wide text-gray-500">Card processing fee</div>
    <p class="text-xs text-gray-500 mt-1">Commas adds this % on top of every price. It's shown on the pricing cards and checkout so customers aren't surprised. Set 0 to hide it.</p>
    <div class="mt-3 flex items-center gap-2">
      <div class="relative">
        <input type="number" name="surcharge_percent" step="0.01" min="0" max="15" value="{{ rtrim(rtrim(number_format($surcharge, 2), '0'), '.') }}"
               class="w-24 pr-7 pl-3 py-2 text-sm border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-gold">
        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-sm text-gray-500">%</span>
      </div>
      <button class="px-4 py-2 text-sm font-semibold bg-gold text-white rounded-lg hover:bg-gold-dark">Save</button>
      <span class="text-xs text-gray-500">e.g. $697 → ${{ number_format(\App\Support\PlanCatalog::chargedToday(697), 2) }}</span>
    </div>
  </form>
</div>

@if ($errors->any())
  <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">⚠ {{ $errors->first() }}</div>
@endif

<div class="space-y-6">
@foreach ($plans as $key => $p)
  @php
    $state = [
      'label'           => old('label', $p['label']),
      'tagline'         => old('tagline', $p['tagline'] ?? ''),
      'desc'            => old('desc', $p['desc'] ?? ''),
      'amount'          => old('amount', (float) $p['amount']),
      'compare_at'      => old('compare_at', $p['compare_at'] ? (float) $p['compare_at'] : ''),
      'period'          => old('period', $p['period'] ?? ''),
      'monitoring_note' => old('monitoring_note', $p['monitoring_note'] ?? ''),
      'billing_note'    => old('billing_note', $p['billing_note'] ?? ''),
      'badge'           => old('badge', $p['badge'] ?? ''),
      'cta'             => old('cta', $p['cta'] ?? ''),
      'features'        => old('features', $p['features'] ?? []),
      'best_for'        => old('best_for', $p['best_for'] ?? ''),
      'visible'         => (bool) old('visible', empty($p['hidden'])),
      'origAmount'      => (float) $p['amount'],
    ];
  @endphp

  <form id="plan-{{ $key }}" method="POST" action="{{ route('admin.plans.update', $key) }}"
        x-data="planEditor(@js($state))"
        @submit="if (priceChanged() && !confirm('Change the ' + label + ' price from $' + fmt(origAmount) + ' to $' + fmt(amount) + '?\n\nA new Commas product will be created at the new price and checkout switches to it immediately.')) $event.preventDefault()"
        class="bg-white rounded-2xl border border-gray-200 overflow-hidden scroll-mt-24">
    @csrf

    <div class="px-5 py-4 border-b border-gray-200 flex flex-wrap items-center gap-3">
      <div class="flex items-center gap-2">
        <span class="w-3 h-3 rounded-full" style="background: {{ $cardBg[$p['color']] ?? '#64748b' }}"></span>
        <h2 class="font-display font-bold text-lg text-ink" x-text="label"></h2>
      </div>
      @if ($p['customized'])
        <span class="text-[10px] font-bold uppercase tracking-wide px-2 py-0.5 rounded bg-gold/15 text-gold-dark">edited</span>
      @endif
      <label class="ml-auto flex items-center gap-2 text-sm cursor-pointer select-none">
        <input type="hidden" name="visible" value="0">
        <input type="checkbox" name="visible" value="1" x-model="visible" class="w-4 h-4 accent-green-600">
        <span :class="visible ? 'text-gold-dark font-semibold' : 'text-gray-500'" x-text="visible ? 'Shown on website' : 'Hidden (can\'t be bought)'"></span>
      </label>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_340px] gap-0">
      {{-- ─── Fields ─── --}}
      <div class="p-5 space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div class="md:col-span-1">
            <label class="text-xs font-semibold text-gray-600">Plan name</label>
            <input name="label" x-model="label" maxlength="60" required class="mt-1 w-full text-sm px-3 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-gold">
          </div>
          <div>
            <label class="text-xs font-semibold text-gray-600">Price</label>
            <div class="relative mt-1">
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-500">$</span>
              <input name="amount" type="number" step="0.01" min="1" x-model="amount" required class="w-full text-sm pl-7 pr-3 py-2 border rounded-lg outline-none focus:ring-2 focus:ring-gold" :class="priceChanged() ? 'border-amber-400 bg-amber-50' : 'border-gray-300'">
            </div>
            <p x-show="priceChanged()" x-cloak class="text-[11px] text-amber-700 mt-1">New Commas product will be created on save.</p>
          </div>
          <div>
            <label class="text-xs font-semibold text-gray-600">"Was" price <span class="font-normal text-gray-400">(crossed out, optional)</span></label>
            <div class="relative mt-1">
              <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-gray-500">$</span>
              <input name="compare_at" type="number" step="0.01" min="0" x-model="compare_at" class="w-full text-sm pl-7 pr-3 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-gold">
            </div>
            <p class="text-[11px] text-gray-500 mt-1" x-text="save() ? 'Shows a SAVE $' + save() + ' badge' : 'No savings badge'"></p>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
          <div>
            <label class="text-xs font-semibold text-gray-600">Line under the price</label>
            <input name="period" x-model="period" maxlength="60" placeholder="one-time program fee" class="mt-1 w-full text-sm px-3 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-gold">
          </div>
          <div>
            <label class="text-xs font-semibold text-gray-600">Monitoring note</label>
            <input name="monitoring_note" x-model="monitoring_note" maxlength="120" class="mt-1 w-full text-sm px-3 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-gold">
          </div>
          <div>
            <label class="text-xs font-semibold text-gray-600">Corner badge <span class="font-normal text-gray-400">(optional)</span></label>
            <input name="badge" x-model="badge" maxlength="20" placeholder="+ VIP" class="mt-1 w-full text-sm px-3 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-gold">
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="text-xs font-semibold text-gray-600">Tagline <span class="font-normal text-gray-400">(checkout page)</span></label>
            <input name="tagline" x-model="tagline" maxlength="80" class="mt-1 w-full text-sm px-3 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-gold">
          </div>
          <div>
            <label class="text-xs font-semibold text-gray-600">Button text</label>
            <input name="cta" x-model="cta" maxlength="40" required class="mt-1 w-full text-sm px-3 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-gold">
          </div>
        </div>

        <div>
          <label class="text-xs font-semibold text-gray-600">Description <span class="font-normal text-gray-400">(checkout page)</span></label>
          <textarea name="desc" x-model="desc" rows="2" maxlength="400" class="mt-1 w-full text-sm px-3 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-gold"></textarea>
        </div>

        {{-- Bullet points --}}
        <div>
          <div class="flex items-center justify-between">
            <label class="text-xs font-semibold text-gray-600">Bullet points <span class="font-normal text-gray-400" x-text="'(' + features.length + ')'"></span></label>
            <button type="button" @click="features.push(''); $nextTick(() => $el.closest('div').parentElement.querySelector('ul li:last-child input').focus())" class="text-xs font-semibold text-gold-dark hover:underline">+ Add bullet</button>
          </div>
          <ul class="mt-2 space-y-1.5">
            <template x-for="(f, i) in features" :key="i">
              <li class="flex items-center gap-1.5">
                <span class="text-gold-dark text-sm w-4 text-center">✓</span>
                <input name="features[]" x-model="features[i]" maxlength="200" class="flex-1 min-w-0 text-sm px-3 py-1.5 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-gold">
                <button type="button" @click="move(i, -1)" :disabled="i === 0" class="w-7 h-7 rounded-md text-gray-500 hover:bg-gray-100 disabled:opacity-30" title="Move up">↑</button>
                <button type="button" @click="move(i, 1)" :disabled="i === features.length - 1" class="w-7 h-7 rounded-md text-gray-500 hover:bg-gray-100 disabled:opacity-30" title="Move down">↓</button>
                <button type="button" @click="features.splice(i, 1)" class="w-7 h-7 rounded-md text-red-600 hover:bg-red-50" title="Remove">✕</button>
              </li>
            </template>
          </ul>
        </div>

        <div>
          <label class="text-xs font-semibold text-gray-600">Best for</label>
          <textarea name="best_for" x-model="best_for" rows="2" maxlength="300" class="mt-1 w-full text-sm px-3 py-2 border border-gray-300 rounded-lg outline-none focus:ring-2 focus:ring-gold"></textarea>
        </div>

        <input type="hidden" name="billing_note" :value="billing_note">

        <div class="flex flex-wrap items-center gap-3 pt-2 border-t border-gray-100">
          <button type="submit" class="px-5 py-2.5 rounded-lg bg-gold text-white text-sm font-bold hover:bg-gold-dark shadow-[0_4px_14px_rgba(34,197,94,.35)]">Save {{ $p['label'] }}</button>
          <a href="{{ url('/#pricing') }}" target="_blank" class="text-sm text-blue-700 hover:underline">View pricing on website ↗</a>
          <a href="{{ url('/accept-checkout?plan=' . $key) }}" target="_blank" class="text-sm text-blue-700 hover:underline">View checkout ↗</a>
          @if ($p['customized'])
            <button type="submit" form="reset-{{ $key }}" class="ml-auto text-xs text-gray-500 hover:text-red-700"
                    onclick="return confirm('Reset {{ $p['label'] }} to its original wording and price?')">Reset to original</button>
          @endif
        </div>
      </div>

      {{-- ─── Live preview (matches the pricing card) ─── --}}
      <div class="bg-paper p-5 border-t xl:border-t-0 xl:border-l border-gray-200">
        <div class="text-[11px] font-semibold uppercase tracking-wide text-gray-500 mb-2">Live preview</div>
        <div class="relative rounded-2xl p-5 text-white shadow-lg" :class="visible ? '' : 'opacity-50'" style="background: {{ $cardBg[$p['color']] ?? '#334155' }};">
          <template x-if="badge"><div class="absolute -top-0 right-4 bg-yellow-300 text-ink text-[10px] font-extrabold px-2.5 py-1 rounded-b-lg" x-text="badge"></div></template>
          <div class="font-display text-sm font-extrabold tracking-wider uppercase" x-text="label"></div>
          <div class="mt-2 text-sm line-through opacity-70 h-5" x-text="compare_at ? '$' + fmt0(compare_at) : ''"></div>
          <div class="font-display font-black text-5xl leading-none"><sup class="text-xl align-top">$</sup><span x-text="fmt0(amount)"></span></div>
          <div class="mt-2 text-xs font-bold" x-text="period"></div>
          <div class="text-[11px] italic opacity-90" x-text="monitoring_note"></div>
          <div class="text-[11px] opacity-90" x-show="{{ $surcharge > 0 ? 'true' : 'false' }}">+ {{ rtrim(rtrim(number_format($surcharge, 2), '0'), '.') }}% card processing fee at checkout</div>
          <template x-if="save()"><div class="mt-3 bg-black/15 rounded-lg px-3 py-1.5 text-[11px] font-extrabold" x-text="'SAVE $' + save()"></div></template>
          <div class="my-3 border-t border-white/25"></div>
          <ul class="space-y-1.5 text-xs">
            <template x-for="f in features.filter(x => x.trim())"><li class="flex gap-1.5"><span class="opacity-80">✓</span><span x-text="f"></span></li></template>
          </ul>
          <div class="mt-3 pt-3 border-t border-dashed border-white/30 text-[11px]" x-show="best_for"><b>Best for:</b> <span x-text="best_for"></span></div>
          <div class="mt-4 bg-white text-ink text-center text-sm font-bold rounded-xl py-2.5" x-text="cta"></div>
        </div>
        <p class="text-[11px] text-gray-500 mt-3" x-show="!visible">Hidden plans don't appear on the website and can't be purchased.</p>
      </div>
    </div>
  </form>

  @if ($p['customized'])
    <form id="reset-{{ $key }}" method="POST" action="{{ route('admin.plans.reset', $key) }}" class="hidden">@csrf</form>
  @endif
@endforeach
</div>

@push('scripts')
<script>
  function planEditor(state) {
    return Object.assign({}, state, {
      priceChanged() { return Math.abs(parseFloat(this.amount || 0) - this.origAmount) > 0.009; },
      save() {
        var c = parseFloat(this.compare_at || 0), a = parseFloat(this.amount || 0);
        return c > a ? (Math.round((c - a) * 100) / 100).toString() : '';
      },
      fmt(v)  { return Number(v || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); },
      fmt0(v) { var n = Number(v || 0); return n % 1 === 0 ? n.toLocaleString('en-US') : this.fmt(n); },
      move(i, d) { var f = this.features, j = i + d; if (j < 0 || j >= f.length) return; var t = f[i]; f[i] = f[j]; f[j] = t; this.features = f.slice(); },
    });
  }
</script>
@endpush

@endsection
