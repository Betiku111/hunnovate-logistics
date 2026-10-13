@extends('layouts.customer')

@section('title', 'Order Confirmed')
@section('page_title', 'Order Confirmed')

@section('content')

    <div class="max-w-2xl mx-auto">

        {{-- Success banner --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-success-bg text-success mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
            </div>
            <h1 class="text-2xl sm:text-3xl font-bold text-ink">Your order is confirmed</h1>
            <p class="text-sm text-ink-muted mt-2">We are processing your delivery. You will be notified once a rider is assigned.</p>
        </div>

        {{-- Tracking number --}}
        <x-card padding="lg" class="mb-5">
            <p class="text-xs font-medium text-ink-muted uppercase tracking-wider text-center">Tracking Number</p>
            <p class="mt-2 text-center font-mono text-2xl font-bold text-brand">{{ $order->tracking_number }}</p>
            <div class="mt-4 flex items-center justify-center gap-3">
                <button
                    type="button"
                    onclick="navigator.clipboard.writeText('{{ $order->tracking_number }}'); this.textContent='Copied!'; setTimeout(() => this.textContent='Copy number', 1500);"
                    class="text-xs font-medium text-ink-muted hover:text-ink inline-flex items-center gap-1.5"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                    Copy number
                </button>
            </div>
        </x-card>

        {{-- Summary --}}
        <x-card padding="none" class="mb-5">
            <x-card-header title="Order Summary" />
            <div class="p-5 space-y-4">
                <div class="flex gap-3">
                    <span class="w-2 h-2 rounded-full bg-brand mt-1.5 shrink-0"></span>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium text-ink-muted uppercase tracking-wider">Pickup</p>
                        <p class="text-sm font-medium text-ink mt-0.5">{{ $order->pickup_contact_name }}</p>
                        <p class="text-sm text-ink-muted">{{ $order->pickup_address }}, {{ $order->pickup_city }}</p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <span class="w-2 h-2 rounded-full bg-success mt-1.5 shrink-0"></span>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-medium text-ink-muted uppercase tracking-wider">Deliver To</p>
                        <p class="text-sm font-medium text-ink mt-0.5">{{ $order->dropoff_contact_name }}</p>
                        <p class="text-sm text-ink-muted">{{ $order->dropoff_address }}, {{ $order->dropoff_city }}</p>
                    </div>
                </div>
                <div class="pt-4 border-t border-[color:var(--color-border)] grid grid-cols-2 gap-3 text-sm">
                    <div>
                        <p class="text-xs text-ink-muted">Vehicle</p>
                        <p class="font-medium text-ink mt-0.5">{{ $order->vehicle_type->label() }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-ink-muted">Weight</p>
                        <p class="font-medium text-ink mt-0.5">{{ $order->package_weight_kg }} kg</p>
                    </div>
                    <div>
                        <p class="text-xs text-ink-muted">Estimated ETA</p>
                        <p class="font-medium text-ink mt-0.5">{{ $order->eta_minutes ? $order->eta_minutes . ' min' : '—' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-ink-muted">Total</p>
                        <p class="font-semibold text-brand mt-0.5">₦{{ number_format($order->total_fee, 2) }}</p>
                    </div>
                </div>
            </div>
        </x-card>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row gap-3">
            <x-button href="{{ route('customer.orders.show', $order) }}" icon="eye" variant="outline" full>
                View Order
            </x-button>
            <x-button href="{{ route('track.show', $order->tracking_number) }}" icon="map-pin" full>
                Track Delivery
            </x-button>
        </div>

        <p class="mt-6 text-center text-sm text-ink-muted">
            <a href="{{ route('customer.orders.create') }}" class="text-brand hover:text-brand-hover font-medium">
                Create another order →
            </a>
        </p>
    </div>

@endsection