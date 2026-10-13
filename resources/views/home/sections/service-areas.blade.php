<section id="service-areas" class="py-20 sm:py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <span class="inline-block text-xs font-semibold text-brand uppercase tracking-wider">Coverage</span>
                <h2 class="mt-3 text-3xl sm:text-4xl font-bold text-ink tracking-tight leading-tight">
                    Delivering across<br>
                    Nigeria's major cities.
                </h2>
                <p class="mt-4 text-base text-ink-muted leading-relaxed max-w-lg">
                    Hunnovate operates in key metropolitan and intercity routes, with continuous expansion to new regions.
                </p>

                <div class="mt-8 flex flex-wrap gap-2">
                    @foreach($serviceAreas as $area)
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-100 text-ink text-sm font-medium">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            {{ $area['city'] }}
                        </span>
                    @endforeach
                </div>

                <div class="mt-8">
                    <x-button href="{{ route('customer.dashboard') }}" variant="outline" icon-after="arrow-right">
                        Check Your Area
                    </x-button>
                </div>
            </div>

            {{-- Simple visual: stylized Nigeria map card --}}
            <div class="relative">
                <div class="bg-slate-50 border border-[color:var(--color-border)] rounded-[var(--radius-xl)] p-6 aspect-square max-w-md mx-auto">
                    <div class="h-full w-full rounded-[var(--radius-lg)] bg-gradient-to-br from-brand-light via-white to-slate-50 flex items-center justify-center relative overflow-hidden">
                        {{-- Stylized dots --}}
                        <div class="absolute inset-0">
                            @php
                                $dots = [
                                    ['x' => 15, 'y' => 65, 'label' => 'Lagos', 'major' => true],
                                    ['x' => 48, 'y' => 42, 'label' => 'Abuja', 'major' => true],
                                    ['x' => 40, 'y' => 78, 'label' => 'PH',    'major' => false],
                                    ['x' => 32, 'y' => 62, 'label' => 'Ibadan','major' => false],
                                    ['x' => 58, 'y' => 18, 'label' => 'Kano',  'major' => false],
                                    ['x' => 35, 'y' => 72, 'label' => 'Benin', 'major' => false],
                                ];
                            @endphp

                            @foreach($dots as $dot)
                                <div class="absolute group" style="left: {{ $dot['x'] }}%; top: {{ $dot['y'] }}%;">
                                    <span class="block w-{{ $dot['major'] ? '3' : '2' }} h-{{ $dot['major'] ? '3' : '2' }} rounded-full bg-brand ring-4 ring-brand/20 {{ $dot['major'] ? 'animate-pulse' : '' }}"></span>
                                    <span class="absolute left-1/2 -translate-x-1/2 mt-1 text-[10px] font-medium text-ink-muted whitespace-nowrap">{{ $dot['label'] }}</span>
                                </div>
                            @endforeach

                            {{-- Connecting lines --}}
                            <svg class="absolute inset-0 w-full h-full pointer-events-none" viewBox="0 0 100 100" preserveAspectRatio="none">
                                <line x1="15" y1="65" x2="48" y2="42" stroke="#4F46E5" stroke-width="0.3" stroke-dasharray="1 1" opacity="0.5"/>
                                <line x1="15" y1="65" x2="32" y2="62" stroke="#4F46E5" stroke-width="0.3" stroke-dasharray="1 1" opacity="0.5"/>
                                <line x1="32" y1="62" x2="48" y2="42" stroke="#4F46E5" stroke-width="0.3" stroke-dasharray="1 1" opacity="0.5"/>
                                <line x1="32" y1="62" x2="40" y2="78" stroke="#4F46E5" stroke-width="0.3" stroke-dasharray="1 1" opacity="0.5"/>
                                <line x1="48" y1="42" x2="58" y2="18" stroke="#4F46E5" stroke-width="0.3" stroke-dasharray="1 1" opacity="0.5"/>
                            </svg>
                        </div>

                        <x-icon name="map-pin" :size="80" class="text-brand/10" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>