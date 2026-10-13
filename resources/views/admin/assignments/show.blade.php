@extends('layouts.admin')

@section('title', 'Assign — #' . $order->tracking_number)
@section('page_title', 'Assign Rider')
@section('page_subtitle', $order->tracking_number . ' · ' . $order->pickup_city . ' → ' . $order->dropoff_city)

@section('content')

    <a href="{{ route('admin.assignments.index') }}" class="inline-flex items-center gap-1.5 text-sm text-ink-muted hover:text-ink mb-5">
        <x-icon name="arrow-left" :size="16" />
        Back to assignments
    </a>

    <div class="grid lg:grid-cols-3 gap-5">

        {{-- Order summary --}}
        <div class="lg:col-span-2 space-y-5">

            <x-card padding="none">
                <x-card-header title="Order Details" />
                <div class="p-5 space-y-4">
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                        <div>
                            <p class="text-xs text-ink-muted">Vehicle</p>
                            <p class="font-medium text-ink mt-0.5">{{ $order->vehicle_type->label() }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-ink-muted">Weight</p>
                            <p class="font-medium text-ink mt-0.5">{{ $order->package_weight_kg }} kg</p>
                        </div>
                        <div>
                            <p class="text-xs text-ink-muted">Distance</p>
                            <p class="font-medium text-ink mt-0.5">{{ $order->distance_km ? $order->distance_km . ' km' : '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-ink-muted">Fee</p>
                            <p class="font-medium text-brand mt-0.5">₦{{ number_format($order->total_fee, 0) }}</p>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-[color:var(--color-border)] space-y-4">
                        <div class="flex gap-3">
                            <span class="w-2 h-2 rounded-full bg-brand mt-2 shrink-0"></span>
                            <div class="min-w-0">
                                <p class="text-xs text-ink-muted uppercase tracking-wider">Pickup</p>
                                <p class="text-sm font-medium text-ink mt-0.5">{{ $order->pickup_contact_name }} · {{ $order->pickup_contact_phone }}</p>
                                <p class="text-sm text-ink-muted">{{ $order->pickup_address }}, {{ $order->pickup_city }}</p>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <span class="w-2 h-2 rounded-full bg-success mt-2 shrink-0"></span>
                            <div class="min-w-0">
                                <p class="text-xs text-ink-muted uppercase tracking-wider">Deliver To</p>
                                <p class="text-sm font-medium text-ink mt-0.5">{{ $order->dropoff_contact_name }} · {{ $order->dropoff_contact_phone }}</p>
                                <p class="text-sm text-ink-muted">{{ $order->dropoff_address }}, {{ $order->dropoff_city }}</p>
                            </div>
                        </div>
                    </div>

                    @if($order->special_instructions)
                        <div class="pt-4 border-t border-[color:var(--color-border)]">
                            <p class="text-xs text-ink-muted uppercase tracking-wider">Special Instructions</p>
                            <p class="text-sm text-ink mt-1">{{ $order->special_instructions }}</p>
                        </div>
                    @endif
                </div>
            </x-card>

            {{-- Recommended riders --}}
            <x-card padding="none">
                <x-card-header title="Recommended Riders" subtitle="Best match based on location, rating, and experience" />

                @if($recommendations->isEmpty())
                    <x-empty-state
                        icon="user-x"
                        title="No matching riders"
                        description="No verified, available riders match this order's vehicle type. Use manual override below."
                    />
                @else
                    <div class="divide-y divide-[color:var(--color-border)]">
                        @foreach($recommendations as $rec)
                            @php $r = $rec['rider']; @endphp
                            <div class="p-5">
                                <div class="flex items-start gap-4">
                                    <span class="w-12 h-12 rounded-full bg-brand text-white flex items-center justify-center text-sm font-semibold shrink-0">
                                        {{ collect(explode(' ', $r->user->name))->take(2)->map(fn($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('') }}
                                    </span>

                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <p class="text-sm font-semibold text-ink">{{ $r->user->name }}</p>
                                            @if($rec['is_recommended'])
                                                <x-badge color="success" dot>Recommended</x-badge>
                                            @endif
                                            <x-badge color="brand">Score {{ number_format($rec['score'], 0) }}</x-badge>
                                        </div>

                                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1 text-xs text-ink-muted">
                                            <span>★ {{ number_format((float) $r->rating, 1) }}</span>
                                            <span>{{ $r->total_deliveries }} deliveries</span>
                                            @if($rec['distance_km'] !== null)
                                                <span>{{ number_format($rec['distance_km'], 1) }} km away</span>
                                            @endif
                                            @if($vehicle = $r->vehicles->first())
                                                <span>{{ $vehicle->type->label() }} · {{ $vehicle->plate_number }}</span>
                                            @endif
                                        </div>

                                        @if(! empty($rec['reasons']))
                                            <div class="flex flex-wrap gap-1.5 mt-2">
                                                @foreach($rec['reasons'] as $reason)
                                                    <span class="text-[10px] font-medium text-ink-muted bg-slate-100 px-2 py-0.5 rounded-full">{{ $reason }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    <form method="POST" action="{{ route('admin.assignments.assign', $order) }}" class="shrink-0">
                                        @csrf
                                        <input type="hidden" name="rider_id" value="{{ $r->id }}">
                                        <x-button type="submit" size="sm" icon="check">
                                            Assign
                                        </x-button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </x-card>

            {{-- Manual override --}}
            <x-card padding="none">
                <x-card-header title="Manual Override" subtitle="Assign any verified rider" />

                <div x-data="{ open: false }" class="p-5">
                    <button @click="open = !open" class="text-sm text-brand hover:underline">
                        Show all verified riders
                    </button>

                    <div x-show="open" x-cloak class="mt-4">
                        <form method="POST" action="{{ route('admin.assignments.assign', $order) }}" class="space-y-3">
                            @csrf

                            <div>
                                <label class="block text-sm font-medium text-ink mb-1.5">Select Rider</label>
                                <select name="rider_id" required
                                    class="w-full h-10 pl-3 pr-9 text-sm border border-[color:var(--color-border-strong)] rounded-[var(--radius)] focus:outline-none">
                                    <option value="">Choose a rider…</option>
                                    @foreach($allRiders as $r)
                                        <option value="{{ $r->id }}">
                                            {{ $r->user->name }} — ★ {{ number_format((float) $r->rating, 1) }}
                                            @if($v = $r->vehicles->first())
                                                · {{ $v->type->label() }} ({{ $v->plate_number }})
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <x-textarea name="note" label="Note (optional)" rows="2" placeholder="Any special instructions for this rider…" />

                            <x-button type="submit" full icon="check">
                                Assign Selected Rider
                            </x-button>
                        </form>
                    </div>
                </div>
            </x-card>
        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">
            <x-card padding="none">
                <x-card-header title="Customer" />
                <div class="p-5">
                    @if($order->customer)
                        <a href="{{ route('admin.customers.show', $order->customer) }}" class="flex items-center gap-3 group">
                            <span class="w-10 h-10 rounded-full bg-brand text-white flex items-center justify-center text-xs font-semibold">
                                {{ collect(explode(' ', $order->customer->name))->take(2)->map(fn($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('') }}
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-ink truncate group-hover:text-brand">{{ $order->customer->name }}</p>
                                <p class="text-xs text-ink-muted truncate">{{ $order->customer->email }}</p>
                            </div>
                        </a>
                    @else
                        <p class="text-sm text-ink-muted">No customer record.</p>
                    @endif
                </div>
            </x-card>

            <x-card padding="none">
                <x-card-header title="Queue Info" />
                <div class="p-5 space-y-2.5 text-sm">
                    <div class="flex justify-between">
                        <span class="text-ink-muted">Created</span>
                        <span class="font-medium text-ink">{{ $order->created_at->format('M d, H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-ink-muted">Waiting</span>
                        <span class="font-medium text-ink">{{ $order->created_at->diffForHumans(null, true) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-ink-muted">ETA</span>
                        <span class="font-medium text-ink">{{ $order->eta_minutes ? $order->eta_minutes . ' min' : '—' }}</span>
                    </div>
                </div>
            </x-card>

            <x-alert type="info" title="Matching criteria">
                Riders must be: verified, online, available, have matching vehicle type, and not on an active delivery.
            </x-alert>
        </div>
    </div>

@endsection