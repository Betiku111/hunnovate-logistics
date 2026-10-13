@props([
    'title' => 'Something went wrong',
    'description' => 'Please try again. If the problem persists, contact support.',
])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center text-center py-12 px-6']) }}>
    <div class="w-14 h-14 rounded-full bg-danger-bg flex items-center justify-center text-danger mb-4">
        <x-icon name="alert-triangle" :size="24" />
    </div>
    <h3 class="text-base font-semibold text-ink">{{ $title }}</h3>
    <p class="text-sm text-ink-muted mt-1 max-w-sm">{{ $description }}</p>

    @isset($action)
        <div class="mt-5">{{ $action }}</div>
    @endisset
</div>