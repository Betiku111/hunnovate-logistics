@php
    $nav = [
        ['label' => 'Dashboard',     'route' => 'admin.dashboard',           'icon' => 'layout-dashboard', 'match' => 'admin.dashboard'],
        ['label' => 'Orders',        'route' => 'admin.orders.index',        'icon' => 'package',          'match' => 'admin.orders.*',
         'badge' => \App\Models\Order::whereNotIn('status', ['delivered','cancelled'])->count()],
        ['label' => 'Assignments',   'route' => 'admin.assignments.index',   'icon' => 'git-branch',       'match' => 'admin.assignments.*',
         'badge' => \App\Models\Order::where('status', 'waiting_admin')->count()],
        ['label' => 'Customers',     'route' => 'admin.customers.index',     'icon' => 'users',            'match' => 'admin.customers.*'],
        ['label' => 'Riders',        'route' => 'admin.riders.index',        'icon' => 'bike',             'match' => 'admin.riders.*',
         'badge' => \App\Models\Rider::where('status', 'pending')->count(), 'badgeColor' => 'warning'],
        ['label' => 'Vehicles',      'route' => 'admin.vehicles.index',      'icon' => 'truck',            'match' => 'admin.vehicles.*'],
        ['label' => 'Outlets',       'route' => 'admin.outlets.index',       'icon' => 'building-2',       'match' => 'admin.outlets.*'],
        ['divider' => true],
        ['label' => 'Payments',      'route' => 'admin.payments.index',      'icon' => 'credit-card',      'match' => 'admin.payments.*'],
        ['label' => 'Reports',       'route' => 'admin.reports.index',       'icon' => 'bar-chart-3',      'match' => 'admin.reports.*'],
        ['label' => 'Notifications', 'route' => 'admin.notifications.index', 'icon' => 'bell',             'match' => 'admin.notifications.*'],
        ['label' => 'Support',       'route' => 'admin.support.index',       'icon' => 'headphones',       'match' => 'admin.support.*'],
        ['label' => 'Settings',      'route' => 'admin.settings.index',      'icon' => 'settings',         'match' => 'admin.settings.*'],
        ['label' => 'Live Tracking', 'route' => 'admin.tracking.index', 'icon' => 'map-pin', 'match' => 'admin.tracking.*'],
    ];
@endphp

<aside
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-50 w-60 bg-[color:var(--color-navy-sidebar)] text-slate-300 flex flex-col transform lg:translate-x-0 transition-transform duration-200 ease-in-out"
>
    {{-- Logo --}}
    <div class="h-16 px-5 flex items-center justify-between border-b border-white/10 shrink-0">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5">
            <span class="w-8 h-8 rounded-[8px] bg-brand text-white flex items-center justify-center font-bold text-xs">HL</span>
            <span class="font-semibold text-white text-sm">Hunnovate Admin</span>
        </a>
        <button @click="sidebarOpen = false" class="lg:hidden w-8 h-8 rounded hover:bg-white/10 flex items-center justify-center text-slate-300">
            <x-icon name="x" :size="16" />
        </button>
    </div>

    {{-- User block --}}
    <div class="p-4 border-b border-white/10 shrink-0">
        <div class="flex items-center gap-3">
            <span class="w-8 h-8 rounded-full bg-brand text-white flex items-center justify-center text-[10px] font-semibold shrink-0">
                {{ collect(explode(' ', auth()->user()->name))->take(2)->map(fn($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('') }}
            </span>
            <div class="min-w-0">
                <p class="text-xs font-medium text-white truncate">{{ auth()->user()->name }}</p>
                <p class="text-[10px] text-slate-400 truncate">{{ auth()->user()->admin?->title ?? 'Administrator' }}</p>
            </div>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 overflow-y-auto scrollbar-thin p-3 space-y-0.5">
        @foreach($nav as $item)
            @if(!empty($item['divider']))
                <div class="my-2 border-t border-white/10"></div>
            @else
                @php
                    $isActive = request()->routeIs($item['match'] ?? $item['route']);
                    $badgeColor = $item['badgeColor'] ?? null;
                @endphp
                <a
                    href="{{ route($item['route']) }}"
                    @class([
                        'flex items-center gap-3 px-3 py-2 rounded-[var(--radius)] text-[13px] font-medium transition-colors',
                        'bg-brand text-white' => $isActive,
                        'text-slate-300 hover:bg-white/5 hover:text-white' => ! $isActive,
                    ])
                >
                    <x-icon :name="$item['icon']" :size="16" class="shrink-0" />
                    <span class="flex-1 truncate">{{ $item['label'] }}</span>
                    @if(!empty($item['badge']))
                        <span @class([
                            'shrink-0 min-w-[20px] h-5 px-1.5 rounded-full text-[10px] font-semibold flex items-center justify-center',
                            'bg-warning/20 text-warning' => $badgeColor === 'warning' && ! $isActive,
                            'bg-white/10 text-white' => $badgeColor !== 'warning' && ! $isActive,
                            'bg-white/20 text-white' => $isActive,
                        ])>
                            {{ $item['badge'] > 99 ? '99+' : $item['badge'] }}
                        </span>
                    @endif
                </a>
            @endif
        @endforeach
    </nav>

    {{-- Footer --}}
    <div class="p-3 border-t border-white/10 shrink-0">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="w-full flex items-center gap-3 px-3 py-2 rounded-[var(--radius)] text-[13px] font-medium text-slate-300 hover:bg-white/5 hover:text-white transition-colors">
                <x-icon name="log-out" :size="16" />
                Sign Out
            </button>
        </form>
    </div>
</aside>