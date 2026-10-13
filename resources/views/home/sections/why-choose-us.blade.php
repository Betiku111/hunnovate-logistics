@php
    $points = [
        ['icon' => 'shield-check', 'title' => 'Verified Riders',      'desc' => 'Every rider passes document verification and background review.'],
        ['icon' => 'map',          'title' => 'Live Tracking',         'desc' => 'Real-time GPS updates from pickup to final delivery.'],
        ['icon' => 'lock',         'title' => 'Secure Packages',       'desc' => 'Sealed handling, insured transit, and photo confirmation.'],
        ['icon' => 'banknote',     'title' => 'Transparent Pricing',   'desc' => 'Clear fees calculated upfront. No hidden charges.'],
        ['icon' => 'clock',        'title' => 'Fast Delivery',         'desc' => 'Same-day options in metro areas. Nationwide intercity reach.'],
        ['icon' => 'headphones',   'title' => 'Professional Support',  'desc' => 'Responsive support team available throughout your delivery.'],
    ];
@endphp

<section class="py-20 sm:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="grid lg:grid-cols-12 gap-12 items-start">
            <div class="lg:col-span-5 lg:sticky lg:top-24">
                <span class="inline-block text-xs font-semibold text-brand uppercase tracking-wider">Why Choose Hunnovate</span>
                <h2 class="mt-3 text-3xl sm:text-4xl font-bold text-ink tracking-tight leading-tight">
                    Built for reliability.<br>
                    Trusted for delivery.
                </h2>
                <p class="mt-4 text-base text-ink-muted leading-relaxed max-w-md">
                    Whether you are sending a single document across town or managing distribution for a business — Hunnovate gives you a system you can depend on.
                </p>

                <div class="mt-8 flex flex-wrap items-center gap-4">
                    <x-button href="{{ route('customer.dashboard') }}" size="lg" icon="plus">
                        Start a Delivery
                    </x-button>
                    <a href="{{ route('track') }}" class="inline-flex items-center gap-2 text-sm font-medium text-ink hover:text-brand">
                        Track an existing order
                        <x-icon name="arrow-right" :size="16" />
                    </a>
                </div>
            </div>

            <div class="lg:col-span-7 grid sm:grid-cols-2 gap-5">
                @foreach($points as $p)
                    <div class="bg-slate-50 border border-[color:var(--color-border)] rounded-[var(--radius-lg)] p-5 hover:bg-white hover:shadow-[var(--shadow-card)] transition-all">
                        <div class="w-10 h-10 rounded-[10px] bg-white text-brand flex items-center justify-center shadow-sm">
                            <x-icon :name="$p['icon']" :size="18" />
                        </div>
                        <h3 class="mt-4 text-base font-semibold text-ink">{{ $p['title'] }}</h3>
                        <p class="mt-1.5 text-sm text-ink-muted leading-relaxed">{{ $p['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>