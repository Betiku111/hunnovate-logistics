@props(['order'])

@php
    $history = $order->statusHistory->sortByDesc('changed_at')->values();
@endphp

<ol class="relative border-l border-[color:var(--color-border)] ml-3 space-y-6">
    @foreach($history as $entry)
        @php
            $status = \App\Enums\OrderStatus::tryFrom($entry->to_status);
            $color = $status?->color() ?? 'slate';
            $dotColor = match ($color) {
                'success' => 'bg-success',
                'warning' => 'bg-warning',
                'danger'  => 'bg-danger',
                'info'    => 'bg-info',
                default   => 'bg-slate-400',
            };
            $roleLabel = match ($entry->changed_by_role) {
                'customer' => ['label' => 'Customer', 'color' => 'info'],
                'rider'    => ['label' => 'Rider',    'color' => 'brand'],
                'admin'    => ['label' => 'Admin',    'color' => 'danger'],
                default    => ['label' => 'System',   'color' => 'slate'],
            };
        @endphp
        <li class="ml-6">
            <span class="absolute -left-[7px] mt-1.5 w-3 h-3 rounded-full ring-4 ring-white {{ $dotColor }}"></span>

            <div class="flex items-start justify-between gap-3 flex-wrap">
                <div>
                    <p class="text-sm font-medium text-ink">{{ $status?->label() ?? $entry->to_status }}</p>
                    <div class="flex items-center gap-2 mt-1 flex-wrap">
                        <x-badge :color="$roleLabel['color']" size="sm">{{ $roleLabel['label'] }}</x-badge>
                        @if($entry->changedBy)
                            <span class="text-xs text-ink-muted">{{ $entry->changedBy->name }}</span>
                        @endif
                        @if($entry->lat && $entry->lng)
                            <a href="https://maps.google.com/?q={{ $entry->lat }},{{ $entry->lng }}"
                               target="_blank"
                               class="text-xs text-brand hover:underline inline-flex items-center gap-0.5">
                                <x-icon name="map-pin" :size="10" />
                                View location
                            </a>
                        @endif
                    </div>
                </div>
                <time class="text-xs text-ink-muted whitespace-nowrap">{{ $entry->changed_at->diffForHumans() }}</time>
            </div>

            @if($entry->notes)
                <p class="text-xs text-ink-muted mt-2 bg-slate-50 rounded-[var(--radius)] px-3 py-2 border-l-2 border-slate-300">
                    {{ $entry->notes }}
                </p>
            @endif
        </li>
    @endforeach
</ol>