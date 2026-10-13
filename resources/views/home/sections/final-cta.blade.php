<section class="py-20 sm:py-24 bg-gradient-to-br from-brand via-brand-hover to-indigo-700 relative overflow-hidden">
    {{-- Accents --}}
    <div class="absolute top-0 right-0 w-96 h-96 rounded-full bg-white/10 blur-3xl -translate-y-1/2 translate-x-1/2"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 rounded-full bg-indigo-900/30 blur-3xl translate-y-1/2 -translate-x-1/2"></div>

    <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white tracking-tight leading-tight">
            Ready to move your package?
        </h2>
        <p class="mt-5 text-base sm:text-lg text-indigo-100 leading-relaxed max-w-2xl mx-auto">
            Create your delivery with Hunnovate Logistics today. Fast, tracked, and professionally handled from pickup to delivery.
        </p>

        <div class="mt-10 flex flex-wrap items-center justify-center gap-3">
            <x-button
                href="{{ route('customer.dashboard') }}"
                size="lg"
                icon="plus"
                class="!bg-white !text-brand hover:!bg-indigo-50 !h-12"
            >
                Create Order
            </x-button>

            <a
                href="{{ route('track') }}"
                class="inline-flex items-center gap-2 h-12 px-6 rounded-[var(--radius)] border border-white/30 text-white font-medium text-base hover:bg-white/10 transition-colors"
            >
                <x-icon name="search" :size="18" />
                Track Order
            </a>
        </div>
    </div>
</section>