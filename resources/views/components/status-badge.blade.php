@props(['status' => 'active'])

@php
  $map = [
    'active'     => ['bg-green-100 text-green-800',  'Active'],
    'past_due'   => ['bg-amber-100 text-amber-800',  'Suspended'],
    'terminated' => ['bg-red-100 text-red-800',       'Cancelled'],
    'cancelled'  => ['bg-red-100 text-red-800',       'Cancelled'],
  ];
  [$classes, $label] = $map[$status] ?? ['bg-gray-100 text-gray-700', ucfirst($status)];
@endphp

<span class="inline-flex px-2 py-0.5 rounded-full text-xs font-medium {{ $classes }}">
  {{ $label }}
</span>