<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title', 'Admin') · 850 FICO Club</title>

<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>

<script>
  tailwind.config = {
    theme: {
      extend: {
        colors: {
          olive:   { DEFAULT: '#3d4020', dark: '#2a2c15', light: '#5a5e30' },
          gold:    { DEFAULT: '#c9a54a', dark: '#a38538', light: '#e0c06e' },
          ink:     '#1a1a1a',
          paper:   '#faf8f2',
        },
        fontFamily: {
          sans: ['Inter', 'system-ui', 'sans-serif'],
        },
      },
    },
  };
</script>

<style>
  [x-cloak] { display: none !important; }
  body { font-feature-settings: 'cv11', 'ss01'; }
</style>
</head>
<body class="bg-paper text-ink antialiased min-h-screen">

<div class="flex min-h-screen" x-data="{ sidebarOpen: false }">

  {{-- ─── Sidebar ─────────────────────────────────────────────────────────── --}}
  <aside
    class="fixed inset-y-0 left-0 z-30 w-64 bg-olive-dark text-paper transform transition-transform lg:translate-x-0 lg:static lg:inset-0"
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
  >
    <div class="h-16 flex items-center px-6 border-b border-white/10">
      <div class="flex items-center gap-2">
        <div class="w-8 h-8 rounded bg-gold flex items-center justify-center font-bold text-olive-dark">850</div>
        <div class="leading-tight">
          <div class="font-semibold text-sm">FICO Club</div>
          <div class="text-xs text-paper/60">Admin</div>
        </div>
      </div>
    </div>

    <nav class="mt-4 px-3 space-y-1 text-sm">
      @php
        $isActive = fn ($name) => request()->routeIs($name)
            ? 'bg-gold text-olive-dark font-semibold'
            : 'text-paper/80 hover:bg-white/5 hover:text-paper';
      @endphp

      <a href="{{ route('admin.dashboard') }}"
         class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ $isActive('admin.dashboard') }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
        Dashboard
      </a>

      <a href="{{ route('admin.subscriptions') }}"
         class="flex items-center gap-3 px-3 py-2.5 rounded-lg transition {{ $isActive('admin.subscriptions') }}">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
        Subscriptions
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

    <div class="absolute bottom-0 inset-x-0 p-4 border-t border-white/10">
      <div class="text-xs text-paper/50 mb-2 truncate">{{ session('admin_email') }}</div>
      <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button class="w-full text-left text-xs text-paper/70 hover:text-gold transition">Log out →</button>
      </form>
    </div>
  </aside>

  {{-- ─── Main ────────────────────────────────────────────────────────────── --}}
  <div class="flex-1 flex flex-col lg:ml-0">

    <header class="h-16 bg-white border-b border-gray-200 flex items-center px-4 lg:px-8 sticky top-0 z-20">
      <button @click="sidebarOpen = !sidebarOpen" class="lg:hidden mr-3 text-ink">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
      </button>
      <h1 class="text-lg font-semibold text-ink">@yield('title', 'Admin')</h1>
      <div class="ml-auto text-sm text-gray-500 hidden sm:block">
        {{ now()->timezone(config('app.timezone', 'UTC'))->format('M j, Y · g:i A') }}
      </div>
    </header>

    <main class="flex-1 p-4 lg:p-8">
      @yield('content')
    </main>
  </div>

  <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
       class="fixed inset-0 bg-black/40 z-20 lg:hidden"></div>
</div>

@stack('scripts')

</body>
</html>
