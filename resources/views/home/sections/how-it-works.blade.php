@php
    $steps = [
        ['num' => '01', 'icon' => 'clipboard-list', 'title' => 'Create Order',    'desc' => 'Tell us what you are sending, where it is going, and when.'],
        ['num' => '02', 'icon' => 'truck',          'title' => 'Choose Delivery', 'desc' => 'Pick Motorcycle, Car, or Truck — based on size, weight, and urgency.'],
        ['num' => '03', 'icon' => 'bike',           'title' => 'Rider Pickup',    'desc' => 'A verified rider arrives at your pickup location.'],
        ['num' => '04', 'icon' => 'map-pin',        'title' => 'Live Tracking',   'desc' => 'Watch your package move in real time on the map.'],
        ['num' => '05', 'icon' => 'package-check',  'title' => 'Delivered',       'desc' => 'Package delivered with photo proof and signature.'],
    ];
@endphp

<section id="how-it-works" class="py-20 sm:py-24 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <x-section-title
            align="center"
            title="How it works"
            subtitle="Five simple steps from order to delivery. Fast, tracked, and professional."
        />

        {{-- Desktop horizontal timeline --}}
        <div class="hidden lg:block mt-16 relative">
            <div class="absolute top-[28px] left-[5%] right-[5%] h-0.5 bg-gradient-to-r from-brand/20 via-brand to-brand/20"></div>
            <ol class="relative grid grid-cols-5 gap-4">
                @foreach($steps as $i => $s)
                    <li class="text-center">
                        <div class="relative inline-flex items-center justify-center w-14 h-14 rounded-full bg-white border-2 border-brand text-brand shadow-[var(--shadow-card)]">
                            <x-icon :name="$s['icon']" :size="22" />
                            <span class="absolute -top-2 -right-2 w-6 h-6 rounded-full bg-brand text-white text-[10px] font-bold flex items-center justify-center">
                                {{ $s['num'] }}
                            </span>
                        </div>
                        <h3 class="mt-5 text-base font-semibold text-ink">{{ $s['title'] }}</h3>
                        <p class="mt-2 text-sm text-ink-muted leading-relaxed max-w-[200px] mx-auto">{{ $s['desc'] }}</p>
                    </li>
                @endforeach
            </ol>
        </div>

        {{-- Mobile vertical timeline --}}
        <ol class="lg:hidden mt-12 relative border-l-2 border-brand/20 ml-5 space-y-8">
            @foreach($steps as $s)
                <li class="ml-8 relative">
                    <span class="absolute -left-[42px] w-10 h-10 rounded-full bg-white border-2 border-brand text-brand flex items-center justify-center">
                        <x-icon :name="$s['icon']" :size="18" />
                    </span>
                    <span class="absolute -left-[50px] -top-1 w-4 h-4 rounded-full bg-brand text-white text-[8px] font-bold flex items-center justify-center">
                        {{ $s['num'] }}
                    </span>
                    <h3 class="text-base font-semibold text-ink">{{ $s['title'] }}</h3>
                    <p class="mt-1 text-sm text-ink-muted leading-relaxed">{{ $s['desc'] }}</p>
                </li>
            @endforeach
        </ol>
    </div>
</section>