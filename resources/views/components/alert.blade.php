@props([
    'type' => 'info', // success | warning | danger | info
    'title' => null,
    'dismissible' => false,
])

@php
    $styles = [
        'success' => ['bg' => 'bg-success-bg', 'border' => 'border-green-200', 'text' => 'text-green-800', 'icon' => 'check-circle', 'iconColor' => 'text-success'],
        'warning' => ['bg' => 'bg-warning-bg', 'border' => 'border-amber-200', 'text' => 'text-amber-800', 'icon' => 'alert-triangle', 'iconColor' => 'text-warning'],
        'danger'  => ['bg' => 'bg-danger-bg',  'border' => 'border-red-200',   'text' => 'text-red-800', 'icon' => 'alert-circle', 'iconColor' => 'text-danger'],
        'info'    => ['bg' => 'bg-info-bg',    'border' => 'border-blue-200',  'text' => 'text-blue-800', 'icon' => 'info', 'iconColor' => 'text-info'],
    ];
    $s = $styles[$type] ?? $styles['info'];
@endphp

<div
    @if($dismissible) x-data="{ show: true }" x-show="show" x-transition @endif
    {{ $attributes->merge(['class' => "flex items-start gap-3 rounded-[var(--radius)] border p-3.5 {$s['bg']} {$s['border']} {$s['text']}"]) }}
>
    <span class="shrink-0 {{ $s['iconColor'] }} mt-0.5">
        <x-icon :name="$s['icon']" :size="18" />
    </span>
    <div class="flex-1 text-sm">
        @if($title)
            <p class="font-semibold">{{ $title }}</p>
        @endif
        <div>{{ $slot }}</div>
    </div>
    @if($dismissible)
        <button @click="show = false" type="button" class="shrink-0 text-current opacity-60 hover:opacity-100">
            <x-icon name="x" :size="16" />
        </button>
    @endif
</div>