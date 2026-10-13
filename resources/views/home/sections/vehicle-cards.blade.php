@php
    $vehicles = [
        [
            'type'  => 'Motorcycle',
            'icon'  => 'bike',
            'image' => 'images/vehicles/motorcycle.jpg',
            'desc'  => 'Fast local delivery for documents, food, small packages, and urgent items.',
            'tags'  => ['Same-Day', 'Up to 20kg', 'Urban'],
        ],
        [
            'type'  => 'Car',
            'icon'  => 'car-front',
            'image' => 'images/vehicles/car.jpg',
            'desc'  => 'Reliable transportation for medium-sized packages and business deliveries.',
            'tags'  => ['Business', 'Up to 200kg', 'Intercity'],
        ],
        [
            'type'  => 'Truck',
            'icon'  => 'truck',
            'image' => 'images/vehicles/truck.jpg',
            'desc'  => 'Powerful logistics solutions for heavy, large, and long-distance shipments.',
            'tags'  => ['Heavy Cargo', 'Up to 5,000kg', 'Long Distance'],
        ],
    ];
@endphp

<section class="relative -mt-12 sm:-mt-16 z-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid md:grid-cols-3 gap-5 sm:gap-6">
            @foreach($vehicles as $v)
                <article class="group bg-white border border-[color:var(--color-border)] rounded-[var(--radius-lg)] shadow-[var(--shadow-card)] hover:shadow-[var(--shadow-hover)] overflow-hidden transition-all duration-300 flex flex-col">
                    {{-- Image --}}
                    <div class="relative aspect-[16/10] overflow-hidden bg-slate-100">
                        <img
                            src="{{ asset($v['image']) }}"
                            alt="{{ $v['type'] }} delivery"
                            class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                            loading="lazy"
                        >
                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"></div>

                        {{-- Icon chip --}}
                        <div class="absolute top-3 left-3 w-9 h-9 rounded-[10px] bg-white/95 backdrop-blur flex items-center justify-center text-brand shadow-sm">
                            <x-icon :name="$v['icon']" :size="18" />
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="p-5 flex-1 flex flex-col">
                        <h3 class="text-lg font-semibold text-ink">{{ $v['type'] }} Delivery</h3>
                        <p class="text-sm text-ink-muted mt-2 leading-relaxed flex-1">{{ $v['desc'] }}</p>

                        <div class="flex flex-wrap gap-1.5 mt-4">
                            @foreach($v['tags'] as $tag)
                                <span class="text-[11px] font-medium text-ink-muted bg-slate-100 px-2 py-0.5 rounded-full">{{ $tag }}</span>
                            @endforeach
                        </div>

                        <a href="#services" class="mt-5 inline-flex items-center gap-1.5 text-sm font-medium text-brand hover:text-brand-hover group/link">
                            Learn More
                            <x-icon name="arrow-right" :size="14" class="group-hover/link:translate-x-0.5 transition-transform" />
                        </a>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>