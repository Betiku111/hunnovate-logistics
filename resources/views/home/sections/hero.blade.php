@php
    $slides = [
        [
            'theme'    => 'truck',
            'kicker'   => 'Heavy Cargo · Long Distance',
            'title_1'  => 'Move More.',
            'title_2'  => 'Deliver Further.',
            'subtitle' => 'Reliable logistics solutions for large and long-distance deliveries. Truck-based transport built for scale.',
            'image'    => 'images/hero/truck.jpg',
            'video'    => null, // 'videos/hero/truck.mp4' when ready
            'cta_primary'   => ['label' => 'Create an Order', 'route' => route('customer.dashboard')],
            'cta_secondary' => ['label' => 'Explore Services', 'href'  => '#services'],
        ],
        [
            'theme'    => 'car',
            'kicker'   => 'Business Delivery · Medium Packages',
            'title_1'  => 'Fast Delivery.',
            'title_2'  => 'Professional Service.',
            'subtitle' => 'Reliable vehicle delivery for businesses and everyday customers. Urban, intercity, and corporate logistics.',
            'image'    => 'images/hero/car.jpg',
            'video'    => null,
            'cta_primary'   => ['label' => 'Create an Order', 'route' => route('customer.dashboard')],
            'cta_secondary' => ['label' => 'Track Order', 'route' => route('track')],
        ],
        [
            'theme'    => 'motorcycle',
            'kicker'   => 'Same-Day · Local Pickup',
            'title_1'  => 'Fast Where',
            'title_2'  => 'It Matters.',
            'subtitle' => 'Get your packages picked up and delivered quickly by verified riders. Perfect for urgent and small deliveries.',
            'image'    => 'images/hero/motorcycle.jpg',
            'video'    => null,
            'cta_primary'   => ['label' => 'Create an Order', 'route' => route('customer.dashboard')],
            'cta_secondary' => ['label' => 'Track Order', 'route' => route('track')],
        ],
    ];
@endphp

<section
    x-data="{
        active: 0,
        total: {{ count($slides) }},
        autoTimer: null,
        paused: false,
        init() {
            this.start();
            $watch('active', () => this.restart());
        },
        start() {
            this.autoTimer = setInterval(() => {
                if (!this.paused) this.active = (this.active + 1) % this.total;
            }, 7000);
        },
        restart() {
            clearInterval(this.autoTimer);
            this.start();
        },
        goTo(i) { this.active = i; },
        next() { this.active = (this.active + 1) % this.total; },
        prev() { this.active = (this.active - 1 + this.total) % this.total; }
    }"
    @mouseenter="paused = true"
    @mouseleave="paused = false"
    class="relative w-full h-[60vh] sm:h-[70vh] lg:h-[80vh] overflow-hidden bg-navy"
>
    {{-- Slides --}}
    @foreach($slides as $i => $slide)
        <div
            x-show="active === {{ $i }}"
            x-transition:enter="transition-opacity ease-out duration-700"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-500 absolute inset-0"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="absolute inset-0"
        >
            {{-- Background: video or image --}}
            @if($slide['video'])
                <video
                    class="absolute inset-0 w-full h-full object-cover"
                    autoplay muted loop playsinline
                    poster="{{ asset($slide['image']) }}"
                >
                    <source src="{{ asset($slide['video']) }}" type="video/mp4">
                </video>
            @else
                <img
                    src="{{ asset($slide['image']) }}"
                    alt="Hunnovate Logistics — {{ $slide['theme'] }} delivery"
                    class="absolute inset-0 w-full h-full object-cover"
                    loading="{{ $i === 0 ? 'eager' : 'lazy' }}"
                    fetchpriority="{{ $i === 0 ? 'high' : 'low' }}"
                >
            @endif

            {{-- Gradient overlay --}}
            <div class="absolute inset-0 bg-gradient-to-r from-navy/90 via-navy/70 to-navy/30"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-navy/60 via-transparent to-transparent"></div>

            {{-- Content --}}
            <div class="relative h-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center">
                <div class="max-w-2xl">
                    <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 text-white text-xs font-medium tracking-wide uppercase">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        {{ $slide['kicker'] }}
                    </span>

                    <h1 class="mt-5 text-white font-bold tracking-tight text-4xl sm:text-5xl lg:text-6xl leading-[1.05]">
                        {{ $slide['title_1'] }}<br>
                        <span class="text-indigo-300">{{ $slide['title_2'] }}</span>
                    </h1>

                    <p class="mt-5 text-base sm:text-lg text-slate-200 leading-relaxed max-w-xl">
                        {{ $slide['subtitle'] }}
                    </p>

                    <div class="mt-8 flex flex-wrap items-center gap-3">
                        <x-button
                            :href="$slide['cta_primary']['route']"
                            size="lg"
                            icon="plus"
                            class="!bg-white !text-ink hover:!bg-slate-100"
                        >
                            {{ $slide['cta_primary']['label'] }}
                        </x-button>

                        <a
                            href="{{ $slide['cta_secondary']['route'] ?? $slide['cta_secondary']['href'] ?? '#' }}"
                            class="inline-flex items-center gap-2 h-12 px-6 rounded-[var(--radius)] text-white font-medium text-base border border-white/30 hover:bg-white/10 transition-colors"
                        >
                            {{ $slide['cta_secondary']['label'] }}
                            <x-icon name="arrow-right" :size="18" />
                        </a>
                    </div>

                    {{-- Trust row --}}
                    <div class="mt-10 hidden sm:flex items-center gap-6 text-white/80 text-xs">
                        <div class="flex items-center gap-2">
                            <x-icon name="shield-check" :size="16" />
                            <span>Verified Riders</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-icon name="map-pin" :size="16" />
                            <span>Live Tracking</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-icon name="clock" :size="16" />
                            <span>Same-Day Delivery</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Controls --}}
    <button
        @click="prev()"
        class="hidden md:flex absolute left-4 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 text-white items-center justify-center hover:bg-white/20 transition-colors"
        aria-label="Previous slide"
    >
        <x-icon name="chevron-left" :size="20" />
    </button>
    <button
        @click="next()"
        class="hidden md:flex absolute right-4 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/10 backdrop-blur-sm border border-white/20 text-white items-center justify-center hover:bg-white/20 transition-colors"
        aria-label="Next slide"
    >
        <x-icon name="chevron-right" :size="20" />
    </button>

    {{-- Indicators --}}
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 flex items-center gap-2 z-10">
        @foreach($slides as $i => $slide)
            <button
                @click="goTo({{ $i }})"
                :class="active === {{ $i }} ? 'w-8 bg-white' : 'w-2 bg-white/40 hover:bg-white/60'"
                class="h-2 rounded-full transition-all duration-300"
                aria-label="Go to slide {{ $i + 1 }}"
            ></button>
        @endforeach
    </div>
</section>