<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center justify-center gap-2 h-10 px-5 bg-brand text-white text-sm font-medium rounded-[var(--radius)] hover:bg-brand-hover focus:outline-none focus:ring-2 focus:ring-brand focus:ring-offset-2 transition-colors disabled:opacity-50']) }}>
    {{ $slot }}
</button>