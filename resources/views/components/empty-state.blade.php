@props([
    'icon'  => 'inbox',
    'title' => 'Nothing here yet',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center text-center py-12 px-6']) }}>
    <div class="w-14 h-14 rounded-full bg-slate-100 flex items-center justify-center text-ink-subtle mb-4">
        <x-icon :name="$icon" :size="24" />
    </div>
    <h3 class="text-base font-semibold text-ink">{{ $title }}</h3>
    @if($description)
        <p class="text-sm text-ink-muted mt-1 max-w-sm">{{ $description }}</p>
    @endif

    @isset($action)
        <div class="mt-5">{{ $action }}</div>
    @endisset
</div>