@props([
    'variant' => 'primary', // primary | secondary | outline | ghost | danger | success
    'size'    => 'md',      // sm | md | lg
    'type'    => 'button',
    'href'    => null,
    'icon'    => null,
    'iconAfter' => null,
    'loading' => false,
    'full'    => false,
])

@php
    $base = 'inline-flex items-center justify-center gap-2 font-medium rounded-[var(--radius)] transition-colors duration-150 disabled:opacity-50 disabled:cursor-not-allowed focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2';

    $sizes = [
        'sm' => 'h-8 px-3 text-xs',
        'md' => 'h-10 px-4 text-sm',
        'lg' => 'h-12 px-6 text-base',
    ];

    $variants = [
        'primary'   => 'bg-brand text-white hover:bg-brand-hover focus-visible:ring-brand',
        'secondary' => 'bg-navy text-white hover:bg-slate-800 focus-visible:ring-navy',
        'outline'   => 'border border-[color:var(--color-border-strong)] text-ink bg-white hover:bg-slate-50 focus-visible:ring-brand',
        'ghost'     => 'text-ink hover:bg-slate-100 focus-visible:ring-brand',
        'danger'    => 'bg-danger text-white hover:bg-red-700 focus-visible:ring-danger',
        'success'   => 'bg-success text-white hover:bg-green-700 focus-visible:ring-success',
    ];

    $classes = trim(implode(' ', [
        $base,
        $sizes[$size] ?? $sizes['md'],
        $variants[$variant] ?? $variants['primary'],
        $full ? 'w-full' : '',
    ]));

    $tag = $href ? 'a' : 'button';
@endphp

<{{ $tag }}
    @if($href) href="{{ $href }}" @else type="{{ $type }}" @endif
    {{ $attributes->merge(['class' => $classes]) }}
    @if($loading) disabled @endif
>
    @if($loading)
        <svg class="animate-spin h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
        </svg>
    @elseif($icon)
        <x-icon :name="$icon" :size="16" />
    @endif

    {{ $slot }}

    @if($iconAfter && ! $loading)
        <x-icon :name="$iconAfter" :size="16" />
    @endif
</{{ $tag }}>