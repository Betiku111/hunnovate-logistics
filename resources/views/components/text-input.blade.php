@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'block w-full h-10 px-3 text-sm bg-white border border-[color:var(--color-border-strong)] rounded-[var(--radius)] focus:border-brand focus:ring-2 focus:ring-brand/30 focus:outline-none transition-colors']) }}>