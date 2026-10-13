@props([
    'name'    => null,
    'label'   => null,
    'hint'    => null,
    'checked' => false,
    'value'   => '1',
])

@php
    $id = $attributes->get('id') ?? $name ?? 'checkbox-' . \Illuminate\Support\Str::random(6);
    $isChecked = old($name) ? true : $checked;
@endphp

<label for="{{ $id }}" class="flex items-start gap-2.5 cursor-pointer group">
    <input
        type="checkbox"
        name="{{ $name }}"
        id="{{ $id }}"
        value="{{ $value }}"
        @checked($isChecked)
        {{ $attributes->except(['id'])->merge([
            'class' => 'mt-0.5 h-4 w-4 rounded border-[color:var(--color-border-strong)] text-brand focus:ring-brand focus:ring-2 focus:ring-offset-0',
        ]) }}
    >
    @if($label || $hint)
        <span class="flex-1">
            @if($label)
                <span class="block text-sm font-medium text-ink">{{ $label }}</span>
            @endif
            @if($hint)
                <span class="block text-xs text-ink-muted">{{ $hint }}</span>
            @endif
        </span>
    @endif
</label>