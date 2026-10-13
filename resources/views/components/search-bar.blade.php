@props([
    'name' => 'q',
    'value' => null,
    'placeholder' => 'Search…',
    'action' => null, // if null, uses current URL with query string
    'debounce' => 300,
])

<form
    method="GET"
    action="{{ $action ?? url()->current() }}"
    x-data="{ q: @js($value ?? request($name)) }"
    x-init="$watch('q', () => { clearTimeout(window._t); window._t = setTimeout(() => $el.submit(), {{ $debounce }}); })"
    {{ $attributes->merge(['class' => 'relative']) }}
>
    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-ink-subtle pointer-events-none">
        <x-icon name="search" :size="16" />
    </span>
    <input
        type="search"
        name="{{ $name }}"
        x-model="q"
        placeholder="{{ $placeholder }}"
        class="w-full h-10 pl-10 pr-3 text-sm bg-white border border-[color:var(--color-border-strong)] rounded-[var(--radius)] focus:border-brand focus:ring-2 focus:ring-brand/30 focus:outline-none"
    >
</form>