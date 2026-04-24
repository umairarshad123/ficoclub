@props([
    'label' => '',
    'value' => '—',
    'sub'   => null,
    'tone'  => 'default',
])

@php
  $toneClasses = match($tone) {
    'green'  => 'border-green-200',
    'red'    => 'border-red-200',
    'amber'  => 'border-amber-200',
    'gold'   => 'border-gold/40 bg-gradient-to-br from-gold/5 to-transparent',
    default  => 'border-gray-200',
  };
  $valueTone = match($tone) {
    'green' => 'text-green-700',
    'red'   => 'text-red-700',
    'amber' => 'text-amber-700',
    'gold'  => 'text-olive-dark',
    default => 'text-ink',
  };
@endphp

<div class="bg-white rounded-xl border {{ $toneClasses }} p-4">
  <div class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ $label }}</div>
  <div class="text-2xl font-semibold mt-1.5 {{ $valueTone }}">{{ $value }}</div>
  @if ($sub)
    <div class="text-xs text-gray-500 mt-0.5">{{ $sub }}</div>
  @endif
</div>
