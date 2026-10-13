@props(['tabs' => [], 'active' => null, 'name' => 'tabs-' . uniqid()])

@php
    $activeTab = $active ?? array_key_first($tabs);
@endphp

<div x-data="{ active: '{{ $activeTab }}' }" {{ $attributes }}>
    <div class="flex gap-1 border-b border-[color:var(--color-border)] mb-5 overflow-x-auto scrollbar-thin">
        @foreach($tabs as $key => $label)
            <button
                type="button"
                @click="active = '{{ $key }}'"
                :class="active === '{{ $key }}' ? 'border-brand text-brand' : 'border-transparent text-ink-muted hover:text-ink'"
                class="shrink-0 px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors"
            >
                {{ $label }}
            </button>
        @endforeach
    </div>

    @foreach($tabs as $key => $label)
        <div x-show="active === '{{ $key }}'" x-cloak>
            {{ $$key ?? '' }}
        </div>
    @endforeach

    {{ $slot }}
</div>