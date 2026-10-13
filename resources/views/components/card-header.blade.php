@props(['title' => null, 'subtitle' => null])

<div class="flex items-start justify-between gap-4 border-b border-[color:var(--color-border)] px-5 py-4">
    <div>
        @if($title)
            <h3 class="text-base font-semibold text-ink">{{ $title }}</h3>
        @endif
        @if($subtitle)
            <p class="text-xs text-ink-muted mt-0.5">{{ $subtitle }}</p>
        @endif
    </div>
    @isset($actions)
        <div class="shrink-0">{{ $actions }}</div>
    @endisset
</div>