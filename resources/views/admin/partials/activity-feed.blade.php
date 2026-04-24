@props(['events' => []])

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
  <div class="px-5 py-3 border-b border-gray-200 flex items-center justify-between">
    <h2 class="font-semibold text-ink flex items-center gap-2">
      <span class="inline-block w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
      Live Activity
    </h2>
    <span class="text-xs text-gray-500">Most recent events</span>
  </div>

  <div class="divide-y divide-gray-100">
    @forelse ($events as $e)
      @php
        $toneCls = match($e['tone'] ?? 'slate') {
          'green' => 'bg-green-50 border-l-green-500',
          'amber' => 'bg-amber-50 border-l-amber-500',
          'red'   => 'bg-red-50 border-l-red-500',
          'blue'  => 'bg-blue-50 border-l-blue-500',
          default => 'bg-white border-l-gray-300',
        };
      @endphp
      <a href="{{ route('admin.subscription.show', $e['subscription_id']) }}"
         class="flex items-start gap-3 px-5 py-3 border-l-4 hover:bg-gray-50 transition {{ $toneCls }}">
        <div class="text-xl mt-0.5">{{ $e['icon'] }}</div>
        <div class="flex-1 min-w-0">
          <div class="text-sm font-medium text-ink">{{ $e['title'] }}</div>
          <div class="text-xs text-gray-500 mt-0.5">{{ $e['detail'] }}</div>
        </div>
        <div class="text-xs text-gray-400 whitespace-nowrap">{{ $e['when_human'] }}</div>
      </a>
    @empty
      <div class="px-5 py-8 text-center text-sm text-gray-400">
        No recent activity. Events will show here as they happen.
      </div>
    @endforelse
  </div>
</div>
