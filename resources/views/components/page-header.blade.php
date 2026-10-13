@props([
    'title' => null,
    'subtitle' => null,
    'breadcrumbs' => [], // [['label' => 'Home', 'url' => '/'], ...]
])

<div {{ $attributes->merge(['class' => 'mb-6']) }}>
    @if(!empty($breadcrumbs))
        <nav class="mb-2 text-xs text-ink-muted flex items-center gap-1.5">
            @foreach($breadcrumbs as $i => $crumb)
                @if($i > 0)<x-icon name="chevron-right" :size="12" class="text-ink-subtle" />@endif
                @if(isset($crumb['url']))
                    <a href="{{ $crumb['url'] }}" class="hover:text-ink">{{ $crumb['label'] }}</a>
                @else
                    <span class="text-ink font-medium">{{ $crumb['label'] }}</span>
                @endif
            @endforeach
        </nav>
    @endif

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            @if($title)
                <h1 class="text-2xl font-semibold text-ink">{{ $title }}</h1>
            @endif
            @if($subtitle)
                <p class="text-sm text-ink-muted mt-1">{{ $subtitle }}</p>
            @endif
        </div>

        @isset($actions)
            <div class="flex items-center gap-2">{{ $actions }}</div>
        @endisset
    </div>
</div>