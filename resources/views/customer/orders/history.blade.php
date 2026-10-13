@extends('layouts.customer')

@section('title', 'Order History')
@section('page_title', 'Order History')
@section('page_subtitle', 'Completed and cancelled deliveries.')

@section('content')

    @if($orders->isEmpty())
        <x-card>
            <x-empty-state
                icon="clock"
                title="No history yet"
                description="Completed and cancelled orders will appear here."
            />
        </x-card>
    @else
        <div class="bg-white border border-[color:var(--color-border)] rounded-[var(--radius-lg)] overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 border-b border-[color:var(--color-border)]">
                    <tr>
                        <th class="text-left px-5 py-3 font-medium text-ink-muted text-xs uppercase tracking-wider">Tracking</th>
                        <th class="text-left px-5 py-3 font-medium text-ink-muted text-xs uppercase tracking-wider hidden sm:table-cell">Route</th>
                        <th class="text-left px-5 py-3 font-medium text-ink-muted text-xs uppercase tracking-wider hidden md:table-cell">Date</th>
                        <th class="text-left px-5 py-3 font-medium text-ink-muted text-xs uppercase tracking-wider">Status</th>
                        <th class="text-right px-5 py-3 font-medium text-ink-muted text-xs uppercase tracking-wider">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[color:var(--color-border)]">
                    @foreach($orders as $order)
                        <tr class="hover:bg-slate-50">
                            <td class="px-5 py-3">
                                <a href="{{ route('customer.orders.show', $order) }}" class="font-medium text-brand hover:text-brand-hover">
                                    #{{ $order->tracking_number }}
                                </a>
                            </td>
                            <td class="px-5 py-3 text-ink-muted hidden sm:table-cell">
                                {{ $order->pickup_city }} → {{ $order->dropoff_city }}
                            </td>
                            <td class="px-5 py-3 text-ink-muted hidden md:table-cell">
                                {{ $order->created_at->format('M d, Y') }}
                            </td>
                            <td class="px-5 py-3">
                                <x-status-badge :status="$order->status" />
                            </td>
                            <td class="px-5 py-3 text-right font-medium text-ink">
                                ₦{{ number_format($order->total_fee, 0) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            <x-pagination :paginator="$orders" />
        </div>
    @endif

@endsection