@props([
    'label',
    'value',
    'icon' => null,
    'delta' => null,       // e.g. "+12%"
    'trend' => null,       // up | down
    'color' => 'brand',    // brand | success | warning | danger | info
    'hint'  => null,
])

@php
    $bg = [
        'brand'   => 'bg-brand-light text-brand',
        'success' => 'bg-success-bg text-success',
        'warning' => 'bg-warning-bg text-warning',
        'danger'  => 'bg-danger-bg text-danger',
        'info'    => 'bg-info-bg text-info',
    ];
    $trendColor = $trend === 'down' ? 'text-danger' : 'text-success';
@endphp

<div {{ $attributes->merge(['class' => 'bg-white border border-[color:var(--color-border)] rounded-[var(--radius-lg)] shadow-[var(--shadow-card)] p-5']) }}>
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="text-xs font-medium text-ink-muted uppercase tracking-wider">{{ $label }}</p>
            <p class="mt-2 text-2xl font-semibold text-ink leading-none">{{ $value }}</p>

            @if($delta || $hint)
                <p class="mt-2 text-xs flex items-center gap-1.5">
                    @if($delta)
                        <span class="font-medium {{ $trendColor }} inline-flex items-center gap-0.5">
                            <x-icon :name="$trend === 'down' ? 'trending-down' : 'trending-up'" :size="12" />
                            {{ $delta }}
                        </span>
                    @endif
                    @if($hint)
                        <span class="text-ink-muted">{{ $hint }}</span>
                    @endif
                </p>
            @endif
        </div>

        @if($icon)
            <span class="shrink-0 w-10 h-10 rounded-[var(--radius)] flex items-center justify-center {{ $bg[$color] ?? $bg['brand'] }}">
                <x-icon :name="$icon" :size="20" />
            </span>
        @endif
    </div>
</div>