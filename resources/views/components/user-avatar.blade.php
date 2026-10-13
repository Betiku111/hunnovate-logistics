@props([
    'user' => null,
    'name' => null,
    'src'  => null,
    'size' => 'md', // xs | sm | md | lg | xl
])

@php
    $displayName = $name ?? ($user?->name ?? 'User');
    $imagePath   = $src ?? ($user?->avatar_path ?? null);
    $initials    = collect(explode(' ', $displayName))
        ->filter()
        ->take(2)
        ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))
        ->implode('');

    $sizes = [
        'xs' => 'w-6 h-6 text-[10px]',
        'sm' => 'w-8 h-8 text-xs',
        'md' => 'w-10 h-10 text-sm',
        'lg' => 'w-12 h-12 text-base',
        'xl' => 'w-16 h-16 text-lg',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

@if($imagePath)
    <img
        src="{{ \Illuminate\Support\Facades\Storage::url($imagePath) }}"
        alt="{{ $displayName }}"
        {{ $attributes->merge(['class' => "rounded-full object-cover ring-2 ring-white {$sizeClass}"]) }}
    >
@else
    <span {{ $attributes->merge(['class' => "inline-flex items-center justify-center rounded-full bg-brand text-white font-semibold {$sizeClass}"]) }}>
        {{ $initials ?: 'U' }}
    </span>
@endif