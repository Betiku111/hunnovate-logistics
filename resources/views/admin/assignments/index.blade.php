@extends('layouts.admin')

@section('title', 'Assignments')
@section('page_title', 'Rider Assignments')
@section('page_subtitle', $pendingCount . ' order(s) waiting for assignment')

@section('content')

    <x-card padding="none">
        <div class="p-4 border-b border-[color:var(--color-border)]">
            <form method="GET" class="flex flex-col sm:flex-row gap-3">
                <input type="search" name="q" value="{{ $search }}" placeholder="Search by tracking, city…"
                    class="flex-1 h-10 px-3 text-sm border border-[color:var(--color-border-strong)] rounded-[var(--radius)] focus:outline-none">
                <select name="vehicle" class="h-10 pl-3 pr-9 text-sm border border-[color:var(--color-border-strong)] rounded-[var(--radius)] focus:outline-none">
                    <option value="">All vehicles</option>
                    @foreach(\App\Enums\VehicleType::cases() as $t)
                        <option value="{{ $t->value }}" @selected($vehicle === $t->value)>{{ $t->label() }}</option>
                    @endforeach
                </select>
                <x-button type="submit" variant="secondary" icon="search" size="md">Filter</x-button>
            </form>
        </div>

        @if($orders->isEmpty())
            <x-empty-state
                icon="check-circle"
                title="All caught up!"
                description="No orders are waiting for assignment. New orders will appear here."
            />
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-[color:var(--color-border)]">
                        <tr>
                            <th class="text-left px-5 py-3 text-xs font-medium text-ink-muted uppercase tracking-wider">Tracking</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-ink-muted uppercase tracking-wider hidden md:table-cell">Customer</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-ink-muted uppercase tracking-wider hidden lg:table-cell">Route</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-ink-muted uppercase tracking-wider">Vehicle</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-ink-muted uppercase tracking-wider hidden lg:table-cell">Waiting</th>
                            <th class="text-right px-5 py-3 text-xs font-medium text-ink-muted uppercase tracking-wider"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[color:var(--color-border)]">
                        @foreach($orders as $o)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3 font-mono text-xs text-brand">
                                    <a href="{{ route('admin.assignments.show', $o) }}">{{ $o->tracking_number }}</a>
                                </td>
                                <td class="px-5 py-3 hidden md:table-cell text-ink-muted text-xs">{{ $o->customer?->name ?? '—' }}</td>
                                <td class="px-5 py-3 hidden lg:table-cell text-ink-muted text-xs">{{ $o->pickup_city }} → {{ $o->dropoff_city }}</td>
                                <td class="px-5 py-3">
                                    <x-badge color="brand">{{ $o->vehicle_type->label() }}</x-badge>
                                </td>
                                <td class="px-5 py-3 hidden lg:table-cell text-ink-muted text-xs">
                                    {{ $o->created_at->diffForHumans(null, true) }}
                                </td>
                                <td class="px-5 py-3 text-right">
                                    <a href="{{ route('admin.assignments.show', $o) }}"
                                       class="inline-flex items-center gap-1.5 h-8 px-3 text-xs font-medium bg-brand text-white rounded-[var(--radius)] hover:bg-brand-hover">
                                        <x-icon name="user-plus" :size="14" />
                                        Assign
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-5 border-t border-[color:var(--color-border)]">
                <x-pagination :paginator="$orders" />
            </div>
        @endif
    </x-card>

@endsection