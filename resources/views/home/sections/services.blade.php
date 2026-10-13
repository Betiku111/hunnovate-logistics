@php
    $services = [
        ['icon' => 'zap',          'title' => 'Same-Day Delivery',   'desc' => 'Urgent packages delivered within hours — across the city and beyond.'],
        ['icon' => 'package',      'title' => 'Package Delivery',    'desc' => 'Documents, parcels, and small shipments handled with care and speed.'],
        ['icon' => 'building-2',   'title' => 'Business Logistics',  'desc' => 'Tailored delivery solutions for companies of every size and industry.'],
        ['icon' => 'navigation',   'title' => 'Intercity Delivery',  'desc' => 'Reliable transport between major Nigerian cities — safe and on time.'],
        ['icon' => 'briefcase',    'title' => 'Corporate Delivery',  'desc' => 'Scheduled pickups and dedicated account management for enterprise.'],
        ['icon' => 'bike',         'title' => 'Rider Pickup',        'desc' => 'On-demand rider pickup — our riders come to your doorstep immediately.'],
    ];
@endphp

<section id="services" class="py-20 sm:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <x-section-title
            align="center"
            title="Everything you need to move anything"
            subtitle="From urgent documents to heavy cargo — one platform, three vehicle types, and a network of verified riders."
        />

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 sm:gap-6 mt-12">
            @foreach($services as $s)
                <div class="group bg-white border border-[color:var(--color-border)] rounded-[var(--radius-lg)] p-6 hover:border-brand/30 hover:shadow-[var(--shadow-hover)] transition-all duration-300">
                    <div class="w-11 h-11 rounded-[var(--radius)] bg-brand-light text-brand flex items-center justify-center group-hover:bg-brand group-hover:text-white transition-colors">
                        <x-icon :name="$s['icon']" :size="20" />
                    </div>

                    <h3 class="mt-5 text-base font-semibold text-ink">{{ $s['title'] }}</h3>
                    <p class="mt-2 text-sm text-ink-muted leading-relaxed">{{ $s['desc'] }}</p>

                    <a href="{{ route('customer.dashboard') }}" class="mt-5 inline-flex items-center gap-1.5 text-sm font-medium text-brand">
                        Get Started
                        <x-icon name="arrow-right" :size="14" />
                    </a>
                </div>
            @endforeach
        </div>
    </div>
</section>