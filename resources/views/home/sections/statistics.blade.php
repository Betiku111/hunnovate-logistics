<section class="py-16 sm:py-20 bg-white border-y border-[color:var(--color-border)]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-6">
            @foreach($stats as $stat)
                <div class="text-center">
                    <div class="w-11 h-11 mx-auto rounded-[var(--radius)] bg-brand-light text-brand flex items-center justify-center">
                        <x-icon :name="$stat['icon']" :size="20" />
                    </div>
                    <p class="mt-4 text-3xl sm:text-4xl font-bold text-ink tracking-tight">{{ $stat['value'] }}</p>
                    <p class="mt-1 text-xs sm:text-sm text-ink-muted font-medium">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>