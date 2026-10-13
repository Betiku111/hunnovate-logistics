@props([
    'name'  => null,
    'label' => null,
    'type'  => 'text',
    'hint'  => null,
    'icon'  => null,
    'required' => false,
    'value' => null,
])

@php
    $id = $attributes->get('id') ?? $name ?? 'input-' . \Illuminate\Support\Str::random(6);
    $hasError = $name && $errors->has($name);
    $inputValue = old($name, $value);
@endphp

<div {{ $attributes->only('class')->merge(['class' => 'space-y-1.5']) }}>
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-ink">
            {{ $label }}
            @if($required) <span class="text-danger">*</span> @endif
        </label>
    @endif

    <div class="relative">
        @if($icon)
            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-ink-subtle pointer-events-none">
                <x-icon :name="$icon" :size="18" />
            </span>
        @endif

        <input
            type="{{ $type }}"
            name="{{ $name }}"
            id="{{ $id }}"
            value="{{ $inputValue }}"
            @if($required) required @endif
            {{ $attributes->except(['class', 'id'])->merge([
                'class' => trim(
                    'block w-full h-10 rounded-[var(--radius)] border bg-white text-sm text-ink placeholder:text-ink-subtle transition-colors ' .
                    ($icon ? 'pl-10 pr-3 ' : 'px-3 ') .
                    ($hasError
                        ? 'border-danger focus:border-danger focus:ring-danger/30'
                        : 'border-[color:var(--color-border-strong)] focus:border-brand focus:ring-brand/30'
                    ) . ' focus:ring-2 focus:outline-none'
                ),
            ]) }}
        >
    </div>

    @if($hasError)
        <p class="text-xs text-danger flex items-center gap-1">
            <x-icon name="alert-circle" :size="12" />
            {{ $errors->first($name) }}
        </p>
    @elseif($hint)
        <p class="text-xs text-ink-muted">{{ $hint }}</p>
    @endif
</div>