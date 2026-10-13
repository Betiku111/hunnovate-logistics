@props([
    'name'    => null,
    'label'   => null,
    'options' => [], // ['value' => 'Label'] or [['value'=>..., 'label'=>...]]
    'placeholder' => 'Select…',
    'hint'    => null,
    'required'=> false,
    'value'   => null,
])

@php
    $id = $attributes->get('id') ?? $name ?? 'select-' . \Illuminate\Support\Str::random(6);
    $hasError = $name && $errors->has($name);
    $selected = old($name, $value);

    $normalized = collect($options)->map(function ($opt, $key) {
        if (is_array($opt) && isset($opt['value'])) {
            return ['value' => (string) $opt['value'], 'label' => $opt['label'] ?? $opt['value']];
        }
        return ['value' => (string) $key, 'label' => $opt];
    })->values();
@endphp

<div {{ $attributes->only('class')->merge(['class' => 'space-y-1.5']) }}>
    @if($label)
        <label for="{{ $id }}" class="block text-sm font-medium text-ink">
            {{ $label }}
            @if($required) <span class="text-danger">*</span> @endif
        </label>
    @endif

    <div class="relative">
        <select
            name="{{ $name }}"
            id="{{ $id }}"
            @if($required) required @endif
            {{ $attributes->except(['class', 'id'])->merge([
                'class' => trim(
                    'block w-full h-10 rounded-[var(--radius)] border bg-white text-sm text-ink pl-3 pr-10 appearance-none focus:ring-2 focus:outline-none transition-colors ' .
                    ($hasError
                        ? 'border-danger focus:border-danger focus:ring-danger/30'
                        : 'border-[color:var(--color-border-strong)] focus:border-brand focus:ring-brand/30'
                    )
                ),
            ]) }}
        >
            @if($placeholder)
                <option value="" disabled {{ $selected ? '' : 'selected' }}>{{ $placeholder }}</option>
            @endif
            @foreach($normalized as $opt)
                <option value="{{ $opt['value'] }}" @selected((string) $selected === $opt['value'])>{{ $opt['label'] }}</option>
            @endforeach
        </select>
        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-ink-subtle pointer-events-none">
            <x-icon name="chevron-down" :size="16" />
        </span>
    </div>

    @if($hasError)
        <p class="text-xs text-danger">{{ $errors->first($name) }}</p>
    @elseif($hint)
        <p class="text-xs text-ink-muted">{{ $hint }}</p>
    @endif
</div>