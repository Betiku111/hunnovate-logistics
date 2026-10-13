@extends('layouts.rider')

@section('title', 'Job #' . $order->tracking_number)

@section('content')

    <div class="px-4 py-4">

        <a href="{{ route('rider.jobs.index') }}" class="inline-flex items-center gap-1.5 text-sm text-ink-muted mb-4">
            <x-icon name="arrow-left" :size="16" />
            Back to jobs
        </a>

        <x-card padding="none" class="mb-4">
            <div class="p-5 border-b border-[color:var(--color-border)] flex items-center justify-between">
                <div>
                    <p class="text-xs text-ink-muted">Tracking</p>
                    <p class="font-mono text-base font-semibold text-ink mt-0.5">{{ $order->tracking_number }}</p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-ink-muted">Your Payout</p>
                    <p class="text-lg font-bold text-brand">₦{{ number_format($order->total_fee * 0.8, 2) }}</p>
                </div>
            </div>

            <div class="p-5 space-y-4">
                {{-- Pickup --}}
                <div class="flex gap-3">
                    <span class="w-2 h-2 rounded-full bg-brand mt-2 shrink-0"></span>
                    <div class="min-w-0">
                        <p class="text-xs text-ink-muted uppercase tracking-wider">Pickup</p>
                        <p class="text-sm font-medium text-ink mt-0.5">{{ $order->pickup_contact_name }}</p>
                        <p class="text-xs text-ink-muted">{{ $order->pickup_contact_phone }}</p>
                        <p class="text-sm text-ink-muted mt-1">{{ $order->pickup_address }}, {{ $order->pickup_city }}</p>
                    </div>
                </div>

                {{-- Dropoff --}}
                <div class="flex gap-3">
                    <span class="w-2 h-2 rounded-full bg-success mt-2 shrink-0"></span>
                    <div class="min-w-0">
                        <p class="text-xs text-ink-muted uppercase tracking-wider">Deliver to</p>
                        <p class="text-sm font-medium text-ink mt-0.5">{{ $order->dropoff_contact_name }}</p>
                        <p class="text-xs text-ink-muted">{{ $order->dropoff_contact_phone }}</p>
                        <p class="text-sm text-ink-muted mt-1">{{ $order->dropoff_address }}, {{ $order->dropoff_city }}</p>
                    </div>
                </div>

                {{-- Package --}}
                <div class="pt-4 border-t border-[color:var(--color-border)] grid grid-cols-2 gap-3 text-xs">
                    <div>
                        <p class="text-ink-muted">Vehicle</p>
                        <p class="font-medium text-ink mt-0.5">{{ $order->vehicle_type->label() }}</p>
                    </div>
                    <div>
                        <p class="text-ink-muted">Weight</p>
                        <p class="font-medium text-ink mt-0.5">{{ $order->package_weight_kg }} kg</p>
                    </div>
                    <div>
                        <p class="text-ink-muted">Distance</p>
                        <p class="font-medium text-ink mt-0.5">{{ $order->distance_km ? $order->distance_km . ' km' : '—' }}</p>
                    </div>
                    <div>
                        <p class="text-ink-muted">Est. duration</p>
                        <p class="font-medium text-ink mt-0.5">{{ $order->eta_minutes ? $order->eta_minutes . ' min' : '—' }}</p>
                    </div>
                </div>

                @if($order->package_description)
                    <div>
                        <p class="text-xs text-ink-muted uppercase tracking-wider">Package</p>
                        <p class="text-sm text-ink mt-1">{{ $order->package_description }}</p>
                    </div>
                @endif

                @if($order->special_instructions)
                    <div>
                        <p class="text-xs text-ink-muted uppercase tracking-wider">Instructions</p>
                        <p class="text-sm text-ink mt-1">{{ $order->special_instructions }}</p>
                    </div>
                @endif
            </div>
        </x-card>

        {{-- Actions --}}
        <div class="grid grid-cols-2 gap-3">
            <form method="POST" action="{{ route('rider.jobs.reject', $order) }}">
                @csrf
                <x-button type="submit" variant="outline" full icon="x">
                    Skip
                </x-button>
            </form>

            <form method="POST" action="{{ route('rider.jobs.accept', $order) }}">
                @csrf
                <x-button type="submit" full icon="check">
                    Accept Job
                </x-button>
            </form>
        </div>
    </div>

@endsection