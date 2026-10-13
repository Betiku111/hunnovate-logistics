@props(['title', 'subtitle' => null, 'align' => 'left'])

<div class="mb-6 {{ $align === 'center' ? 'text-center' : '' }}">
    <h2 class="text-2xl sm:text-3xl font-semibold text-ink tracking-tight">{{ $title }}</h2>
    @if($subtitle)
        <p class="mt-2 text-base text-ink-muted max-w-2xl {{ $align === 'center' ? 'mx-auto' : '' }}">{{ $subtitle }}</p>
    @endif
</div>