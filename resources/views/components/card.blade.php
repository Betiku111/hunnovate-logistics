@props([
    'padding' => 'md', // none | sm | md | lg
    'hover'   => false,
])

@php
    $pads = [
        'none' => '',
        'sm'   => 'p-3',
        'md'   => 'p-5',
        'lg'   => 'p-6 sm:p-8',
    ];
    $classes = 'bg-white border border-[color:var(--color-border)] rounded-[var(--radius-lg)] shadow-[var(--shadow-card)] ' .
               ($hover ? 'transition-shadow hover:shadow-[var(--shadow-hover)] ' : '') .
               ($pads[$padding] ?? $pads['md']);
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>