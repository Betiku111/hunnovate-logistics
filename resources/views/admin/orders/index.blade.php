@extends('layouts.admin')

@section('title', 'Orders')
@section('page_title', 'Orders')
@section('page_subtitle', 'Manage all customer orders')

@section('content')

    <x-card padding="none">
        <div class="p-4 border-b border-[color:var(--color-border)]">
            <form method="GET" class="flex flex-col sm:flex-row gap-3">
                <input type="search" name="q" value="{{ $search }}" placeholder="Search by tracking, city…"
                    class="flex-1 h-10 px-3 text-sm border border-[color:var(--color-border-strong)] rounded-[var(--radius)] focus:outline-none">
                <select name="status" class="h-10 pl-3 pr-9 text-sm border border-[color:var(--color-border-strong)] rounded-[var(--radius)] focus:outline-none">
                    <option value="">All statuses</option>
                    @foreach(\App\Enums\OrderStatus::cases() as $s)
                        <option value="{{ $s->value }}" @selected($status === $s->value)>{{ $s->label() }}</option>
                    @endforeach
                </select>
                <x-button type="submit" variant="secondary" icon="search" size="md">Filter</x-button>
            </form>
        </div>

        @if($orders->isEmpty())
            <x-empty-state icon="package" title="No orders" description="No orders match your filters." />
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 border-b border-[color:var(--color-border)]">
                        <tr>
                            <th class="text-left px-5 py-3 text-xs font-medium text-ink-muted uppercase tracking-wider">Tracking</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-ink-muted uppercase tracking-wider hidden md:table-cell">Customer</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-ink-muted uppercase tracking-wider hidden lg:table-cell">Route</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-ink-muted uppercase tracking-wider">Status</th>
                            <th class="text-left px-5 py-3 text-xs font-medium text-ink-muted uppercase tracking-wider hidden lg:table-cell">Rider</th>
                            <th class="text-right px-5 py-3 text-xs font-medium text-ink-muted uppercase tracking-wider">Amount</th>
                            <th class="text-right px-5 py-3 text-xs font-medium text-ink-muted uppercase tracking-wider"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[color:var(--color-border)]">
                        @foreach($orders as $o)
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3 font-mono text-xs text-brand">
                                    <a href="{{ route('admin.orders.show', $o) }}">{{ $o->tracking_number }}</a>
                                </td>
                                <td class="px-5 py-3 hidden md:table-cell text-ink-muted text-xs">{{ $o->customer?->name ?? '—' }}</td>
                                <td class="px-5 py-3 hidden lg:table-cell text-ink-muted text-xs">{{ $o->pickup_city }} → {{ $o->dropoff_city }}</td>
                                <td class="px-5 py-3"><x-status-badge :status="$o->status" /></td>
                                <td class="px-5 py-3 hidden lg:table-cell text-xs">
                                    @if($o->rider)
                                        <a href="{{ route('admin.riders.show', $o->rider) }}" class="text-brand">{{ $o->rider->user->name }}</a>
                                    @else
                                        <span class="text-ink-subtle">Unassigned</span>
                                    @endif
                                </td>
                                <td class="px-5 py-3 text-right font-medium text-xs">₦{{ number_format($o->total_fee, 0) }}</td>
                                <td class="px-5 py-3 text-right">
                                    <a href="{{ route('admin.orders.show', $o) }}" class="text-xs text-brand hover:bg-brand-light rounded px-2.5 py-1.5">View</a>
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