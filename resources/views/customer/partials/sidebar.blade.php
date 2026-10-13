@php
    $nav = [
        [
            'label' => 'Dashboard',
            'route' => 'customer.dashboard',
            'icon'  => 'home',
            'match' => 'customer.dashboard',
        ],
        [
            'label' => 'Create Order',
            'route' => 'customer.orders.create',
            'icon'  => 'plus-circle',
            'match' => 'customer.orders.create',
            'primary' => true,
        ],
        [
            'label' => 'My Orders',
            'route' => 'customer.orders.index',
            'icon'  => 'package',
            'match' => 'customer.orders.*',
            'badge' => auth()->user()->isCustomer()
                ? auth()->user()->orders()->whereNotIn('status', ['delivered', 'cancelled'])->count()
                : 0,
        ],
        [
            'label' => 'Track Order',
            'route' => 'track',
            'icon'  => 'map-pin',
            'match' => 'track',
        ],
        [
            'label' => 'Order History',
            'route' => 'customer.orders.history',
            'icon'  => 'clock',
            'match' => 'customer.orders.history',
        ],
        ['divider' => true],
        [
            'label' => 'Notifications',
            'route' => 'customer.notifications',
            'icon'  => 'bell',
            'match' => 'customer.notifications',
        ],
        [
            'label' => 'Profile',
            'route' => 'profile.edit',
            'icon'  => 'user',
            'match' => 'profile.*',
        ],
        [
            'label' => 'Support',
            'route' => 'customer.support.index',
            'icon'  => 'headphones',
            'match' => 'customer.support.*',
        ],
    ];
@endphp

<aside
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-[color:var(--color-navy-sidebar)] text-slate-300 flex flex-col transform lg:translate-x-0 transition-transform duration-200 ease-in-out"
>
    {{-- Logo --}}
    <div class="h-16 px-5 flex items-center justify-between border-b border-white/10 shrink-0">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5">
            <span class="w-8 h-8 rounded-[8px] bg-brand text-white flex items-center justify-center font-bold text-xs">HL</span>
            <span class="font-semibold text-white text-sm">Hunnovate</span>
        </a>
        <button
            @click="sidebarOpen = false"
            class="lg:hidden w-8 h-8 rounded-[var(--radius)] hover:bg-white/10 flex items-center justify-center text-slate-300"
            aria-label="Close sidebar"
        >
            <x-icon name="x" :size="16" />
        </button>
    </div>

    {{-- User --}}
    <div class="p-4 border-b border-white/10 shrink-0">
        <div class="flex items-center gap-3">
            <span class="w-9 h-9 rounded-full bg-brand text-white flex items-center justify-center text-xs font-semibold shrink-0">
                {{ collect(explode(' ', auth()->user()->name))->take(2)->map(fn($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('') }}
            </span>
            <div class="min-w-0">
                <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-slate-400 truncate">{{ auth()->user()->email }}</p>
            </div>
        </div>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 overflow-y-auto scrollbar-thin p-3 space-y-0.5">
        @foreach($nav as $item)
            @if(isset($item['divider']))
                <div class="my-3 border-t border-white/10"></div>
            @else
                @php
                    $isActive = request()->routeIs($item['match']);
                    $isPrimary = $item['primary'] ?? false;
                @endphp
                <a
                    href="{{ route($item['route']) }}"
                    @class([
                        'flex items-center gap-3 px-3 py-2.5 rounded-[var(--radius)] text-sm font-medium transition-colors group',
                        'bg-brand text-white' => $isActive,
                        'bg-brand/15 text-white hover:bg-brand/25' => ! $isActive && $isPrimary,
                        'text-slate-300 hover:bg-white/5 hover:text-white' => ! $isActive && ! $isPrimary,
                    ])
                >
                    <x-icon :name="$item['icon']" :size="18" class="shrink-0" />
                    <span class="flex-1 truncate">{{ $item['label'] }}</span>
                    @if(!empty($item['badge']))
                        <span class="shrink-0 min-w-5 h-5 px-1.5 rounded-full bg-white/10 text-[10px] font-semibold flex items-center justify-center">
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
            <button
                type="submit"
                class="w-full flex items-center gap-3 px-3 py-2.5 rounded-[var(--radius)] text-sm font-medium text-slate-300 hover:bg-white/5 hover:text-white transition-colors"
            >
                <x-icon name="log-out" :size="18" />
                Sign Out
            </button>
        </form>
    </div>
</aside>