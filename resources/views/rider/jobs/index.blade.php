@extends('layouts.rider')

@section('title', 'Available Jobs')

@section('content')

    <div class="px-4 py-4">
        <div class="mb-4">
            <h1 class="text-xl font-semibold text-ink">Available Jobs</h1>
            <p class="text-xs text-ink-muted mt-1">Jobs matching your vehicle type</p>
        </div>

        @if($jobs->isEmpty())
            <x-card>
                <x-empty-state
                    icon="inbox"
                    title="No jobs right now"
                    description="New jobs will appear here automatically."
                />
            </x-card>
        @else
            <div class="space-y-3">
                @foreach($jobs as $job)
                    <a href="{{ route('rider.jobs.show', $job) }}" class="block bg-white border border-[color:var(--color-border)] rounded-[var(--radius-lg)] p-4 hover:border-brand/40 transition-colors">
                        <div class="flex items-start justify-between gap-3 mb-3">
                            <div class="min-w-0">
                                <p class="text-xs font-mono text-ink-muted">#{{ $job->tracking_number }}</p>
                                <p class="text-sm font-semibold text-ink mt-0.5">{{ $job->pickup_city }} → {{ $job->dropoff_city }}</p>
                            </div>
                            <x-status-badge :status="$job->status" />
                        </div>

                        <div class="grid grid-cols-3 gap-2 text-xs">
                            <div class="bg-slate-50 rounded p-2">
                                <p class="text-ink-muted">Vehicle</p>
                                <p class="font-medium text-ink mt-0.5">{{ $job->vehicle_type->label() }}</p>
                            </div>
                            <div class="bg-slate-50 rounded p-2">
                                <p class="text-ink-muted">Weight</p>
                                <p class="font-medium text-ink mt-0.5">{{ $job->package_weight_kg }} kg</p>
                            </div>
                            <div class="bg-slate-50 rounded p-2">
                                <p class="text-ink-muted">Payout</p>
                                <p class="font-medium text-brand mt-0.5">₦{{ number_format($job->total_fee * 0.8, 0) }}</p>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>

            <div class="mt-4">
                <x-pagination :paginator="$jobs" />
            </div>
        @endif
    </div>

@endsection