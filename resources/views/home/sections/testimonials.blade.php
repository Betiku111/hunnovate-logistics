<section class="py-20 sm:py-24 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <x-section-title
            align="center"
            title="Trusted by businesses and customers"
            subtitle="Real feedback from real users who depend on Hunnovate Logistics every day."
        />

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-5 mt-12">
            @foreach($testimonials as $t)
                <article class="bg-white border border-[color:var(--color-border)] rounded-[var(--radius-lg)] p-6 shadow-[var(--shadow-card)] flex flex-col">
                    {{-- Stars --}}
                    <div class="flex items-center gap-0.5 text-amber-400">
                        @for($i = 0; $i < $t['rating']; $i++)
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="#F59E0B" stroke="#F59E0B" stroke-width="1">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                            </svg>
                        @endfor
                    </div>

                    {{-- Text --}}
                    <p class="mt-4 text-sm text-ink leading-relaxed flex-1">"{{ $t['text'] }}"</p>

                    {{-- Author --}}
                    <div class="mt-5 pt-5 border-t border-[color:var(--color-border)] flex items-center gap-3">
                        <span class="w-9 h-9 rounded-full bg-brand text-white flex items-center justify-center font-semibold text-xs">
                            {{ $t['initial'] }}
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-ink truncate">{{ $t['name'] }}</p>
                            <p class="text-xs text-ink-muted truncate">{{ $t['role'] }}</p>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>