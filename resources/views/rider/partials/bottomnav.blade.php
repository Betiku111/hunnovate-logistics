@php
    $tabs = [
        ['route' => 'rider.home',             'icon' => 'home',        'label' => 'Home',      'match' => 'rider.home'],
        ['route' => 'rider.jobs.index',       'icon' => 'package',     'label' => 'Jobs',      'match' => 'rider.jobs.*'],
        ['route' => 'rider.deliveries.index', 'icon' => 'truck',       'label' => 'Deliveries','match' => 'rider.deliveries.*'],
        ['route' => 'rider.earnings',         'icon' => 'wallet',      'label' => 'Earnings',  'match' => 'rider.earnings'],
    ];
@endphp

<nav class="fixed bottom-0 inset-x-0 z-30 bg-white border-t border-[color:var(--color-border)]" style="padding-bottom: env(safe-area-inset-bottom);">
    <div class="max-w-2xl mx-auto grid grid-cols-4 h-16">
        @foreach($tabs as $tab)
            @php $isActive = request()->routeIs($tab['match']); @endphp
            <a href="{{ route($tab['route']) }}"
               class="flex flex-col items-center justify-center gap-1 transition-colors
                   {{ $isActive ? 'text-brand' : 'text-ink-muted hover:text-ink' }}">
                <x-icon :name="$tab['icon']" :size="20" />
                <span class="text-[10px] font-medium">{{ $tab['label'] }}</span>
            </a>
        @endforeach
    </div>
</nav>