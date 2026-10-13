<section class="py-20 sm:py-24 bg-navy text-white overflow-hidden relative">
    {{-- Background accents --}}
    <div class="absolute -top-40 -right-40 w-96 h-96 rounded-full bg-brand/20 blur-3xl"></div>
    <div class="absolute -bottom-40 -left-40 w-96 h-96 rounded-full bg-indigo-500/10 blur-3xl"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 items-center">

            {{-- Copy --}}
            <div>
                <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 border border-white/20 text-xs font-medium tracking-wide uppercase">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    Live Tracking
                </span>

                <h2 class="mt-5 text-3xl sm:text-4xl font-bold tracking-tight leading-tight">
                    Know exactly where<br>
                    your package is.
                </h2>

                <p class="mt-4 text-base text-slate-300 leading-relaxed max-w-lg">
                    Every Hunnovate delivery updates in real time. Track your rider on the map, see the estimated arrival, and get notified at every milestone.
                </p>

                <div class="mt-8 flex flex-wrap items-center gap-3">
                    <x-button href="{{ route('track') }}" size="lg" icon="search" class="!bg-white !text-navy hover:!bg-slate-100">
                        Track My Order
                    </x-button>
                </div>
            </div>

            {{-- Preview card --}}
            <div class="relative">
                <div class="bg-white rounded-[var(--radius-xl)] shadow-2xl overflow-hidden text-ink">
                    {{-- Header --}}
                    <div class="px-5 py-4 border-b border-[color:var(--color-border)] flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                            <span class="text-xs font-medium text-ink-muted uppercase tracking-wider">Live</span>
                        </div>
                        <span class="text-xs font-mono text-ink-muted">HNL-2026-000245</span>
                    </div>

                    {{-- Map mock --}}
                    <div class="relative aspect-[16/9] bg-gradient-to-br from-slate-100 via-slate-50 to-blue-50">
                        {{-- Faux map grid --}}
                        <svg class="absolute inset-0 w-full h-full" preserveAspectRatio="none" viewBox="0 0 400 225">
                            <defs>
                                <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
                                    <path d="M 40 0 L 0 0 0 40" fill="none" stroke="#E2E8F0" stroke-width="0.5"/>
                                </pattern>
                            </defs>
                            <rect width="400" height="225" fill="url(#grid)"/>
                            {{-- Route path --}}
                            <path d="M 60 180 Q 140 140 200 120 T 340 40"
                                  fill="none" stroke="#4F46E5" stroke-width="3"
                                  stroke-linecap="round" stroke-dasharray="6 4"/>
                            {{-- Pickup marker --}}
                            <circle cx="60" cy="180" r="8" fill="#4F46E5" opacity="0.2"/>
                            <circle cx="60" cy="180" r="4" fill="#4F46E5"/>
                            {{-- Rider marker --}}
                            <circle cx="180" cy="140" r="12" fill="#4F46E5" opacity="0.15">
                                <animate attributeName="r" values="12;18;12" dur="2s" repeatCount="indefinite"/>
                                <animate attributeName="opacity" values="0.15;0;0.15" dur="2s" repeatCount="indefinite"/>
                            </circle>
                            <circle cx="180" cy="140" r="6" fill="#4F46E5"/>
                            <circle cx="180" cy="140" r="2.5" fill="#ffffff"/>
                            {{-- Dropoff marker --}}
                            <circle cx="340" cy="40" r="8" fill="#16A34A" opacity="0.2"/>
                            <circle cx="340" cy="40" r="4" fill="#16A34A"/>
                        </svg>

                        {{-- ETA chip --}}
                        <div class="absolute top-3 right-3 bg-white rounded-[var(--radius)] shadow-md px-3 py-1.5">
                            <p class="text-[10px] text-ink-muted uppercase tracking-wide">ETA</p>
                            <p class="text-sm font-semibold text-ink">35 min</p>
                        </div>
                    </div>

                    {{-- Details --}}
                    <div class="p-5 space-y-4">
                        <div class="flex items-center justify-between">
                            <x-status-badge :status="\App\Enums\OrderStatus::InTransit" />
                            <span class="text-sm font-semibold text-ink">₦2,450</span>
                        </div>

                        <div class="flex items-center gap-3 pt-3 border-t border-[color:var(--color-border)]">
                            <span class="w-10 h-10 rounded-full bg-brand text-white flex items-center justify-center font-semibold text-sm">JK</span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-ink">John Kelvin</p>
                                <p class="text-xs text-ink-muted flex items-center gap-2">
                                    <x-icon name="bike" :size="12" />
                                    Motorcycle · Verified
                                </p>
                            </div>
                            <a href="{{ route('track') }}" class="text-xs font-medium text-brand hover:text-brand-hover">
                                View →
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Floating badge --}}
                <div class="absolute -bottom-5 -left-5 hidden sm:flex items-center gap-3 bg-white rounded-[var(--radius-lg)] shadow-xl px-4 py-3">
                    <div class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <x-icon name="check-circle" :size="18" />
                    </div>
                    <div>
                        <p class="text-xs text-ink-muted">Delivered today</p>
                        <p class="text-sm font-semibold text-ink">1,247 packages</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>