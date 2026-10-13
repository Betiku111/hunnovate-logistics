@props([
    'name'  => null,
    'label' => null,
    'rows'  => 4,
    'hint'  => null,
    'required' => false,
    'value' => null,
])

@php
    $id = $attributes->get('id') ?? $name ?? 'textarea-' . \Illuminate\Support\Str::random(6);
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

    <textarea
        name="{{ $name }}"
        id="{{ $id }}"
        rows="{{ $rows }}"
        @if($required) required @endif
        {{ $attributes->except(['class', 'id'])->merge([
            'class' => trim(
                'block w-full rounded-[var(--radius)] border bg-white text-sm text-ink placeholder:text-ink-subtle px-3 py-2 transition-colors focus:ring-2 focus:outline-none ' .
                ($hasError
                    ? 'border-danger focus:border-danger focus:ring-danger/30'
                    : 'border-[color:var(--color-border-strong)] focus:border-brand focus:ring-brand/30'
                )
            ),
        ]) }}
    >{{ $inputValue }}</textarea>

    @if($hasError)
        <p class="text-xs text-danger flex items-center gap-1">
            <x-icon name="alert-circle" :size="12" />
            {{ $errors->first($name) }}
        </p>
    @elseif($hint)
        <p class="text-xs text-ink-muted">{{ $hint }}</p>
    @endif
</div>