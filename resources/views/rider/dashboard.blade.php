@extends('layouts.rider')

@section('title', 'Home')

@section('content')

    {{-- Active delivery card --}}
    @if($activeDelivery)
        <div class="px-4 pt-4">
            <a href="{{ route('rider.deliveries.show', $activeDelivery) }}" class="block bg-gradient-to-br from-brand to-brand-hover rounded-[var(--radius-lg)] p-5 text-white shadow-lg">
                <div class="flex items-center justify-between mb-3">
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium bg-white/15 px-2 py-0.5 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        Active Delivery
                    </span>
                    <x-status-badge :status="$activeDelivery->status" />
                </div>

                <p class="text-xs text-indigo-100 mb-1">{{ $activeDelivery->tracking_number }}</p>
                <p class="text-base font-semibold">{{ $activeDelivery->pickup_city }} → {{ $activeDelivery->dropoff_city }}</p>
                <p class="text-xs text-indigo-100 mt-2 truncate">{{ $activeDelivery->dropoff_address }}</p>

                <div class="mt-4 flex items-center gap-2 text-sm font-medium">
                    Continue delivery
                    <x-icon name="arrow-right" :size="16" />
                </div>
            </a>
        </div>
    @endif

    {{-- Stats grid --}}
    <div class="px-4 pt-4 grid grid-cols-2 gap-3">
        <div class="bg-white border border-[color:var(--color-border)] rounded-[var(--radius-lg)] p-4">
            <p class="text-xs text-ink-muted font-medium">Today</p>
            <p class="text-2xl font-bold text-ink mt-1">{{ $stats['today_deliveries'] }}</p>
            <p class="text-xs text-ink-muted mt-0.5">deliveries</p>
        </div>

        <div class="bg-white border border-[color:var(--color-border)] rounded-[var(--radius-lg)] p-4">
            <p class="text-xs text-ink-muted font-medium">Today</p>
            <p class="text-2xl font-bold text-success mt-1">₦{{ number_format($stats['today_earnings'], 0) }}</p>
            <p class="text-xs text-ink-muted mt-0.5">earned</p>
        </div>

        <div class="bg-white border border-[color:var(--color-border)] rounded-[var(--radius-lg)] p-4">
            <p class="text-xs text-ink-muted font-medium">Total</p>
            <p class="text-2xl font-bold text-ink mt-1">{{ $stats['total_deliveries'] }}</p>
            <p class="text-xs text-ink-muted mt-0.5">deliveries</p>
        </div>

        <div class="bg-white border border-[color:var(--color-border)] rounded-[var(--radius-lg)] p-4">
            <p class="text-xs text-ink-muted font-medium">Rating</p>
            <p class="text-2xl font-bold text-amber-500 mt-1">{{ number_format($stats['rating'], 1) }}</p>
            <p class="text-xs text-ink-muted mt-0.5">out of 5.0</p>
        </div>
    </div>

    {{-- Available jobs --}}
    <div class="px-4 pt-6">
        <div class="flex items-center justify-between mb-3">
            <div>
                <h2 class="text-base font-semibold text-ink">Available Jobs</h2>
                <p class="text-xs text-ink-muted mt-0.5">{{ $availableJobsCount }} job(s) matching your vehicle</p>
            </div>
            <a href="{{ route('rider.jobs.index') }}" class="text-sm font-medium text-brand">See all →</a>
        </div>

        @if(! $rider->is_online)
            <x-card>
                <x-empty-state
                    icon="power-off"
                    title="You are offline"
                    description="Go online to see and accept jobs."
                />
            </x-card>
        @elseif($availableJobsCount === 0)
            <x-card>
                <x-empty-state
                    icon="inbox"
                    title="No jobs available"
                    description="New jobs will appear here when customers place orders."
                />
            </x-card>
        @else
            <a href="{{ route('rider.jobs.index') }}" class="block bg-white border border-[color:var(--color-border)] rounded-[var(--radius-lg)] p-4 hover:border-brand/40">
                <div class="flex items-center gap-3">
                    <span class="w-10 h-10 rounded-full bg-brand-light text-brand flex items-center justify-center">
                        <x-icon name="package" :size="20" />
                    </span>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-ink">{{ $availableJobsCount }} job(s) waiting</p>
                        <p class="text-xs text-ink-muted">Tap to view</p>
                    </div>
                    <x-icon name="chevron-right" :size="18" class="text-ink-subtle" />
                </div>
            </a>
        @endif
    </div>

@endsection