@extends('layouts.public')

@section('title', 'Tracking ' . $trackingNumber . ' — Hunnovate Logistics')

@section('content')
    <section class="py-12 sm:py-16 bg-slate-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

            <a href="{{ route('track') }}" class="inline-flex items-center gap-1.5 text-sm text-ink-muted hover:text-ink mb-5">
                <x-icon name="arrow-left" :size="16" />
                Track another order
            </a>

            @if(! $order)
                <x-card padding="lg">
                    <x-error-state
                        title="Order not found"
                        description="We could not find an order with the tracking number “{{ $trackingNumber }}”. Please check the number and try again."
                    >
                        <x-slot:action>
                            <x-button href="{{ route('track') }}" icon="arrow-left">Try again</x-button>
                        </x-slot:action>
                    </x-error-state>
                </x-card>
            @else
                {{-- Header --}}
                <x-card padding="none" class="mb-5">
                    <div class="p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <p class="text-xs text-ink-muted uppercase tracking-wider">Tracking</p>
                            <p class="font-mono text-lg font-semibold text-ink mt-0.5">{{ $order->tracking_number }}</p>
                        </div>
                        <x-status-badge :status="$order->status" />
                    </div>
                </x-card>

                <div class="grid md:grid-cols-3 gap-5">
                    {{-- Route --}}
                    <div class="md:col-span-2 space-y-5">
                        <x-card padding="none">
                            <x-card-header title="Route" />
                            <div class="p-5 space-y-5">
                                <div class="flex gap-4">
                                    <div class="flex flex-col items-center pt-1 shrink-0">
                                        <span class="w-3 h-3 rounded-full bg-brand"></span>
                                        <span class="w-px flex-1 bg-slate-200 my-1"></span>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs font-medium text-ink-muted uppercase tracking-wider">Pickup</p>
                                        <p class="text-sm text-ink mt-1">{{ $order->pickup_address }}</p>
                                        <p class="text-xs text-ink-muted mt-0.5">{{ $order->pickup_city }}, {{ $order->pickup_state }}</p>
                                    </div>
                                </div>
                                <div class="flex gap-4">
                                    <div class="flex flex-col items-center pt-1 shrink-0">
                                        <span class="w-3 h-3 rounded-full bg-success"></span>
                                    </div>
                                    <div class="flex-1">
                                        <p class="text-xs font-medium text-ink-muted uppercase tracking-wider">Deliver To</p>
                                        <p class="text-sm text-ink mt-1">{{ $order->dropoff_address }}</p>
                                        <p class="text-xs text-ink-muted mt-0.5">{{ $order->dropoff_city }}, {{ $order->dropoff_state }}</p>
                                    </div>
                                </div>
                            </div>
                        </x-card>

              <x-card padding="none">
    <x-card-header title="Live Map" subtitle="Rider location updates automatically" />
    <div class="p-5">
        <x-tracking-map
            :order="$order"
            height="360px"
            :refresh-seconds="15"
        />
    </div>
</x-card>
                        @if($order->statusHistory->isNotEmpty())
                            <x-card padding="none">
                                <x-card-header title="Timeline" />
                                <div class="p-5">
                                    <x-order-timeline :order="$order" />
                                </div>
                            </x-card>
                        @endif
                    </div>

                    {{-- Sidebar --}}
                    <div class="space-y-5">
                        @if($order->rider)
                            <x-card padding="none">
                                <x-card-header title="Rider" />
                                <div class="p-5">
                                    <div class="flex items-center gap-3">
                                        <span class="w-10 h-10 rounded-full bg-brand text-white flex items-center justify-center text-xs font-semibold">
                                            {{ collect(explode(' ', $order->rider->user->name))->take(2)->map(fn($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('') }}
                                        </span>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-ink truncate">{{ $order->rider->user->name }}</p>
                                            <p class="text-xs text-ink-muted">{{ $order->vehicle_type->label() }}</p>
                                        </div>
                                    </div>
                                </div>
                            </x-card>
                        @endif

                        <x-card padding="none">
                            <x-card-header title="Delivery Info" />
                            <div class="p-5 space-y-2.5 text-sm">
                                <div class="flex items-center justify-between">
                                    <span class="text-ink-muted">Vehicle</span>
                                    <span class="font-medium text-ink">{{ $order->vehicle_type->label() }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-ink-muted">Weight</span>
                                    <span class="font-medium text-ink">{{ $order->package_weight_kg }} kg</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-ink-muted">Created</span>
                                    <span class="font-medium text-ink">{{ $order->created_at->format('M d, Y') }}</span>
                                </div>
                            </div>
                        </x-card>
                    </div>
                </div>
            @endif
        </div>
    </section>
@endsection