<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Admin') · 850 FICO Club</title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Sora:wght@700;800;900&display=swap" rel="stylesheet">

<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>

<script>
  tailwind.config = {
    theme: {
      extend: {
        // Website brand (850ficoclub.com). Token names are historical:
        //   olive = brand navy · gold = brand green (primary accent) · ink = navy text · paper = page bg
        colors: {
          olive:   { DEFAULT: '#1A3056', dark: '#0F2044', light: '#2A4470' },
          gold:    { DEFAULT: '#22C55E', dark: '#16A34A', light: '#4ADE80' },
          brand:   { green: '#22C55E', gold: '#F5A623', red: '#EF4444', navy: '#0F2044' },
          ink:     '#0F2044',
          paper:   '#F4F7FB',
        },
        fontFamily: {
          sans:    ['Manrope', 'system-ui', 'sans-serif'],
          display: ['Sora', 'Manrope', 'sans-serif'],
        },
      },
    },
  };
</script>

<style>
  [x-cloak] { display: none !important; }
  .tabular-nums { font-variant-numeric: tabular-nums; }
  .logo-850 { font-family: 'Sora', sans-serif; font-weight: 900; letter-spacing: -0.5px; line-height: 1; }
</style>
</head>
<body class="bg-paper text-ink antialiased min-h-screen">

<div class="flex min-h-screen" x-data="{ sidebarOpen: false }">

  {{-- ─── Sidebar ─────────────────────────────────────────────────────────── --}}
  <aside
    class="fixed inset-y-0 left-0 z-30 w-64 h-screen flex flex-col bg-olive-dark text-paper transform transition-transform lg:translate-x-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
  >
    <div class="h-16 flex items-center px-6 border-b border-white/10">
      <a href="{{ route('admin.dashboard') }}" class="block leading-none">
        <div class="logo-850 text-[22px]"><span class="text-brand-green">850</span> <span class="text-brand-gold">FICO</span> <span class="text-brand-red">CLUB</span></div>
        <div class="mt-1.5 flex items-center gap-2 text-[10px] font-semibold tracking-wider text-paper/55 uppercase">
          <span>Credit Is King &amp; Cash Is Power</span>
        </div>
      </a>
    </div>

    <nav class="mt-4 px-3 pb-4 space-y-1 text-sm flex-1 overflow-y-auto">
      @php
        $isActive = fn ($name) => request()->routeIs($name)
            ? 'bg-gold text-white font-semibold shadow-[0_4px_14px_rgba(34,197,94,.35)]'
            : 'text-paper/80 hover:bg-white/5 hover:text-paper';
      @endphp

      <a href="{{ route('admin.dashboard') }}"
         class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ $isActive('admin.dashboard') }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        Dashboard
      </a>

      <a href="{{ route('admin.website') }}"
         class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ $isActive('admin.website') }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
        Website
        @if (\App\Support\SiteSettings::maintenanceEnabled())
          <span class="ml-auto inline-flex px-1.5 rounded-full text-[10px] font-bold bg-amber-400 text-olive-dark">MAINT.</span>
        @else
          <span class="ml-auto inline-flex items-center gap-1 text-[10px] font-bold text-brand-green">● LIVE</span>
        @endif
      </a>

      <a href="{{ route('admin.site-content') }}"
         class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ $isActive('admin.site-content') }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
        Site Content
        @unless (\App\Support\SiteContent::salesOpen())
          <span class="ml-auto inline-flex px-1.5 rounded-full text-[10px] font-bold bg-amber-400 text-olive-dark">PAUSED</span>
        @endunless
      </a>

      <a href="{{ route('admin.plans') }}"
         class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ $isActive('admin.plans') }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
        Plans &amp; Pricing
      </a>

      <a href="{{ route('admin.commas-sales') }}"
         class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ $isActive('admin.commas-sales') }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
        Commas Sales
      </a>

      <a href="{{ route('admin.subscriptions') }}"
         class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ $isActive('admin.subscriptions') }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        Subscriptions
      </a>

      <a href="{{ route('admin.payments') }}"
         class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ $isActive('admin.payments') }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M5 6h14a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2zm10 8h4"/></svg>
        Payments
      </a>

      <a href="{{ route('admin.orders') }}"
         class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ $isActive('admin.orders') }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        Checkout Orders
        @php $ordersNeedingReview = \App\Models\CheckoutOrder::where('status', 'mismatch')->count(); @endphp
        @if ($ordersNeedingReview > 0)
          <span class="ml-auto inline-flex px-1.5 rounded-full text-[10px] font-bold bg-red-500 text-white">{{ $ordersNeedingReview }}</span>
        @endif
      </a>

      <a href="{{ route('admin.referrals') }}"
         class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ $isActive('admin.referrals') }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
        Referrals
      </a>
      


<a href="{{ route('admin.at-risk') }}"
   class="flex items-center gap-3 px-4 py-2.5 rounded-lg text-sm font-medium transition
          {{ request()->routeIs('admin.at-risk') ? 'bg-amber-100 text-amber-800' : 'text-gray-600 hover:bg-gray-100' }}">
  <span class="text-base">⚠️</span> At Risk
</a>

      <a href="{{ route('admin.webhooks') }}"
         class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ $isActive('admin.webhooks') }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
        Webhooks
      </a>

    </nav>

    <div class="flex-shrink-0 p-4 border-t border-white/10">
      <div class="text-xs text-paper/50 mb-2 truncate">{{ session('admin_email') }}</div>
      <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button class="w-full text-left text-xs text-paper/70 hover:text-gold transition">Log out →</button>
      </form>
    </div>
  </aside>

  {{-- ─── Main ────────────────────────────────────────────────────────────── --}}
  <div class="flex-1 flex flex-col min-w-0 lg:pl-64">

    <header class="h-16 bg-white border-b border-gray-200 flex items-center px-4 lg:px-8 sticky top-0 z-20">
      <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden mr-3 text-ink">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
      </button>
      <h1 class="text-lg font-semibold text-ink">@yield('title', 'Admin')</h1>
      <div class="ml-auto text-sm text-gray-500 hidden sm:block">
        {{ now()->timezone(config('services.commas.timezone', 'America/New_York'))->format('M j, Y · g:i A T') }}
      </div>
    </header>

    <main class="flex-1 p-4 lg:p-8">
      @if (session('success'))
        <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">✓ {{ session('success') }}</div>
      @endif
      @if (session('error'))
        <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">⚠ {{ session('error') }}</div>
      @endif
      @yield('content')
    </main>
  </div>

  <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
       class="fixed inset-0 bg-black/40 z-20 lg:hidden"></div>
</div>

@stack('scripts')

</body>
</html>
