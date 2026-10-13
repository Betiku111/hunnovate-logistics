@extends('layouts.rider')

@section('title', 'My Deliveries')

@section('content')

    <div class="px-4 py-4">

        <div class="mb-4">
            <h1 class="text-xl font-semibold text-ink">My Deliveries</h1>
            <p class="text-xs text-ink-muted mt-1">Track your active and completed jobs</p>
        </div>

        @if($active->isEmpty() && $completed->isEmpty())
            <x-card>
                <x-empty-state
                    icon="truck"
                    title="No deliveries yet"
                    description="Accept a job to start your first delivery."
                >
                    <x-slot:action>
                        <x-button href="{{ route('rider.jobs.index') }}" icon="package" size="sm">
                            View Jobs
                        </x-button>
                    </x-slot:action>
                </x-empty-state>
            </x-card>
        @else
            @if($active->isNotEmpty())
                <div class="mb-6">
                    <h2 class="text-xs font-medium text-ink-muted uppercase tracking-wider mb-3">Active</h2>
                    <div class="space-y-3">
                        @foreach($active as $order)
                            <a href="{{ route('rider.deliveries.show', $order) }}" class="block bg-white border border-[color:var(--color-border)] rounded-[var(--radius-lg)] p-4 hover:border-brand/40">
                                <div class="flex items-start justify-between gap-3 mb-3">
                                    <div class="min-w-0">
                                        <p class="text-xs font-mono text-ink-muted">#{{ $order->tracking_number }}</p>
                                        <p class="text-sm font-semibold text-ink mt-0.5">{{ $order->pickup_city }} → {{ $order->dropoff_city }}</p>
                                    </div>
                                    <x-status-badge :status="$order->status" />
                                </div>
                                <div class="text-xs text-ink-muted">{{ $order->dropoff_address }}</div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($completed->isNotEmpty())
                <div>
                    <h2 class="text-xs font-medium text-ink-muted uppercase tracking-wider mb-3">Completed</h2>
                    <div class="space-y-2">
                        @foreach($completed as $order)
                            <a href="{{ route('rider.deliveries.show', $order) }}" class="block bg-white border border-[color:var(--color-border)] rounded-[var(--radius-lg)] p-4 opacity-90 hover:opacity-100">
                                <div class="flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="text-xs font-mono text-ink-muted">#{{ $order->tracking_number }}</p>
                                        <p class="text-sm text-ink mt-0.5 truncate">{{ $order->pickup_city }} → {{ $order->dropoff_city }}</p>
                                    </div>
                                    <x-status-badge :status="$order->status" />
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        @endif
    </div>

@endsection