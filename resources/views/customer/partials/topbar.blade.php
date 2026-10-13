<header class="h-16 bg-white border-b border-[color:var(--color-border)] sticky top-0 z-30">
    <div class="h-full max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between gap-4">

        {{-- Mobile menu + title --}}
        <div class="flex items-center gap-3 min-w-0">
            <button
                @click="sidebarOpen = true"
                class="lg:hidden w-10 h-10 rounded-[var(--radius)] hover:bg-slate-100 flex items-center justify-center text-ink-muted"
                aria-label="Open menu"
            >
                <x-icon name="menu" :size="20" />
            </button>

            <div class="min-w-0">
                <h1 class="text-base sm:text-lg font-semibold text-ink truncate">
                    @yield('page_title', 'Dashboard')
                </h1>
                @hasSection('page_subtitle')
                    <p class="text-xs text-ink-muted truncate">@yield('page_subtitle')</p>
                @endif
            </div>
        </div>

        {{-- Right actions --}}
        <div class="flex items-center gap-2">
            <a
                href="{{ route('customer.orders.create') }}"
                class="hidden sm:inline-flex items-center gap-2 h-9 px-3.5 rounded-[var(--radius)] bg-brand text-white text-sm font-medium hover:bg-brand-hover transition-colors"
            >
                <x-icon name="plus" :size="16" />
                New Order
            </a>

            <a
                href="{{ route('customer.notifications') }}"
                class="relative w-10 h-10 rounded-[var(--radius)] hover:bg-slate-100 flex items-center justify-center text-ink-muted"
                aria-label="Notifications"
            >
                <x-icon name="bell" :size="18" />
                {{-- Unread badge — populated in STEP 15 --}}
            </a>

            <div x-data="{ open: false }" class="relative">
                <button
                    @click="open = !open"
                    @click.outside="open = false"
                    class="flex items-center gap-2 h-10 pl-1 pr-2 rounded-[var(--radius)] hover:bg-slate-100 transition-colors"
                >
                    <span class="w-8 h-8 rounded-full bg-brand text-white flex items-center justify-center text-xs font-semibold">
                        {{ collect(explode(' ', auth()->user()->name))->take(2)->map(fn($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('') }}
                    </span>
                    <x-icon name="chevron-down" :size="14" class="text-ink-muted" />
                </button>

                <div
                    x-show="open"
                    x-cloak
                    x-transition
                    class="absolute right-0 mt-2 w-56 bg-white border border-[color:var(--color-border)] rounded-[var(--radius)] shadow-lg overflow-hidden z-40"
                >
                    <div class="px-4 py-3 border-b border-[color:var(--color-border)]">
                        <p class="text-sm font-medium text-ink truncate">{{ auth()->user()->name }}</p>
                        <p class="text-xs text-ink-muted truncate">{{ auth()->user()->email }}</p>
                    </div>
                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2.5 text-sm text-ink hover:bg-slate-50">Profile Settings</a>
                    <a href="{{ route('customer.support.index') }}" class="block px-4 py-2.5 text-sm text-ink hover:bg-slate-50">Support</a>
                    <form method="POST" action="{{ route('logout') }}" class="border-t border-[color:var(--color-border)]">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-danger hover:bg-danger-bg">Sign Out</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>