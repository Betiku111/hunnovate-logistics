@props([
    'name',
    'title' => null,
    'maxWidth' => 'md', // sm | md | lg | xl | 2xl
    'show' => false,
])

@php
    $maxes = [
        'sm'  => 'max-w-sm',
        'md'  => 'max-w-md',
        'lg'  => 'max-w-lg',
        'xl'  => 'max-w-xl',
        '2xl' => 'max-w-2xl',
    ];
    $maxWidthClass = $maxes[$maxWidth] ?? $maxes['md'];
@endphp

<div
    x-data="{ show: @js($show) }"
    x-on:open-modal.window="$event.detail === '{{ $name }}' && (show = true)"
    x-on:close-modal.window="$event.detail === '{{ $name }}' && (show = false)"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    role="dialog"
    aria-modal="true"
>
    {{-- Backdrop --}}
    <div
        x-show="show"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-navy/50 backdrop-blur-sm"
        @click="show = false"
    ></div>

    {{-- Panel --}}
    <div class="relative min-h-full flex items-center justify-center p-4">
        <div
            x-show="show"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-4"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-4"
            class="relative w-full {{ $maxWidthClass }} bg-white rounded-[var(--radius-lg)] shadow-xl"
            @click.stop
        >
            @if($title)
                <div class="flex items-center justify-between border-b border-[color:var(--color-border)] px-5 py-4">
                    <h3 class="text-base font-semibold text-ink">{{ $title }}</h3>
                    <button type="button" @click="show = false" class="text-ink-subtle hover:text-ink">
                        <x-icon name="x" :size="18" />
                    </button>
                </div>
            @endif

            <div class="p-5">
                {{ $slot }}
            </div>

            @isset($footer)
                <div class="flex justify-end gap-2 border-t border-[color:var(--color-border)] px-5 py-4">
                    {{ $footer }}
                </div>
            @endisset
        </div>
    </div>
</div>