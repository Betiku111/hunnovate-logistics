<header
    x-data="{ mobileOpen: false, scrolled: false }"
    x-init="window.addEventListener('scroll', () => { scrolled = window.scrollY > 20 })"
    :class="scrolled ? 'bg-white shadow-[var(--shadow-card)]' : 'bg-white/95 backdrop-blur-sm'"
    class="sticky top-0 z-40 w-full border-b border-[color:var(--color-border)] transition-all duration-200"
>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">

            {{-- Logo --}}
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <span class="w-9 h-9 rounded-[10px] bg-brand text-white flex items-center justify-center font-bold text-sm">HL</span>
                <span class="hidden sm:block font-bold text-lg text-ink tracking-tight">
                    Hunnovate<span class="text-brand">.</span>
                </span>
            </a>

            {{-- Desktop nav --}}
            <nav class="hidden lg:flex items-center gap-1">
                <a href="{{ route('home') }}" class="px-3 py-2 text-sm font-medium text-ink-muted hover:text-ink transition-colors">Home</a>
                <a href="#services" class="px-3 py-2 text-sm font-medium text-ink-muted hover:text-ink transition-colors">Services</a>
                <a href="#how-it-works" class="px-3 py-2 text-sm font-medium text-ink-muted hover:text-ink transition-colors">How It Works</a>
                <a href="{{ route('track') }}" class="px-3 py-2 text-sm font-medium text-ink-muted hover:text-ink transition-colors">Track Order</a>
                <a href="#service-areas" class="px-3 py-2 text-sm font-medium text-ink-muted hover:text-ink transition-colors">Coverage</a>
                <a href="#faq" class="px-3 py-2 text-sm font-medium text-ink-muted hover:text-ink transition-colors">FAQ</a>
            </nav>

            {{-- Right actions --}}
            <div class="hidden lg:flex items-center gap-3">
                @auth
                    <a href="{{ 
                        auth()->user()->isAdmin() ? route('admin.dashboard') :
                        (auth()->user()->isRider() ? route('rider.home') : route('customer.dashboard'))
                    }}" class="text-sm font-medium text-ink-muted hover:text-ink">
                        Dashboard
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button class="text-sm font-medium text-ink-muted hover:text-ink">Log Out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-ink-muted hover:text-ink">
                        Login
                    </a>
                @endauth

                <x-button href="{{ route('customer.dashboard') }}" icon="plus" size="sm">
                    Create Order
                </x-button>
            </div>

            {{-- Mobile menu button --}}
            <button
                @click="mobileOpen = !mobileOpen"
                class="lg:hidden w-10 h-10 flex items-center justify-center rounded-[var(--radius)] text-ink-muted hover:bg-slate-100"
                aria-label="Toggle menu"
            >
                <template x-if="!mobileOpen">
                    <x-icon name="menu" :size="22" />
                </template>
                <template x-if="mobileOpen">
                    <x-icon name="x" :size="22" />
                </template>
            </button>
        </div>
    </div>

    {{-- Mobile drawer --}}
    <div
        x-show="mobileOpen"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2"
        class="lg:hidden border-t border-[color:var(--color-border)] bg-white"
    >
        <nav class="max-w-7xl mx-auto px-4 py-4 flex flex-col gap-1">
            <a href="{{ route('home') }}" @click="mobileOpen = false" class="px-3 py-2.5 text-sm font-medium text-ink rounded-[var(--radius)] hover:bg-slate-100">Home</a>
            <a href="#services" @click="mobileOpen = false" class="px-3 py-2.5 text-sm font-medium text-ink rounded-[var(--radius)] hover:bg-slate-100">Services</a>
            <a href="#how-it-works" @click="mobileOpen = false" class="px-3 py-2.5 text-sm font-medium text-ink rounded-[var(--radius)] hover:bg-slate-100">How It Works</a>
            <a href="{{ route('track') }}" @click="mobileOpen = false" class="px-3 py-2.5 text-sm font-medium text-ink rounded-[var(--radius)] hover:bg-slate-100">Track Order</a>
            <a href="#service-areas" @click="mobileOpen = false" class="px-3 py-2.5 text-sm font-medium text-ink rounded-[var(--radius)] hover:bg-slate-100">Coverage</a>
            <a href="#faq" @click="mobileOpen = false" class="px-3 py-2.5 text-sm font-medium text-ink rounded-[var(--radius)] hover:bg-slate-100">FAQ</a>

            <div class="mt-3 pt-3 border-t border-[color:var(--color-border)] flex flex-col gap-2">
                @auth
                    <a href="{{ 
                        auth()->user()->isAdmin() ? route('admin.dashboard') :
                        (auth()->user()->isRider() ? route('rider.home') : route('customer.dashboard'))
                    }}" @click="mobileOpen = false" class="px-3 py-2.5 text-sm font-medium text-ink rounded-[var(--radius)] hover:bg-slate-100">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left px-3 py-2.5 text-sm font-medium text-ink rounded-[var(--radius)] hover:bg-slate-100">Log Out</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" @click="mobileOpen = false" class="px-3 py-2.5 text-sm font-medium text-ink rounded-[var(--radius)] hover:bg-slate-100">Login</a>
                @endauth
                <x-button href="{{ route('customer.dashboard') }}" icon="plus" class="w-full justify-center">
                    Create Order
                </x-button>
            </div>
        </nav>
    </div>
</header>