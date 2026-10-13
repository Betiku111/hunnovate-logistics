@props([
    'color' => 'slate', // slate | success | warning | danger | info | brand
    'size'  => 'md',    // sm | md
    'dot'   => false,
])

@php
    $colors = [
        'slate'   => 'bg-slate-100 text-slate-700 ring-slate-200',
        'success' => 'bg-success-bg text-success ring-green-200',
        'warning' => 'bg-warning-bg text-warning ring-amber-200',
        'danger'  => 'bg-danger-bg text-danger ring-red-200',
        'info'    => 'bg-info-bg text-info ring-blue-200',
        'brand'   => 'bg-brand-light text-brand ring-indigo-200',
    ];

    $dots = [
        'slate'   => 'bg-slate-400',
        'success' => 'bg-success',
        'warning' => 'bg-warning',
        'danger'  => 'bg-danger',
        'info'    => 'bg-info',
        'brand'   => 'bg-brand',
    ];

    $sizes = [
        'sm' => 'text-[10px] px-1.5 py-0.5',
        'md' => 'text-xs px-2 py-0.5',
    ];

    $classes = 'inline-flex items-center gap-1.5 font-medium rounded-full ring-1 ring-inset ' .
               ($sizes[$size] ?? $sizes['md']) . ' ' .
               ($colors[$color] ?? $colors['slate']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    @if($dot)
        <span class="w-1.5 h-1.5 rounded-full {{ $dots[$color] ?? $dots['slate'] }}"></span>
    @endif
    {{ $slot }}
</span>