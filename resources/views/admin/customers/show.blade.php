@extends('layouts.admin')

@section('title', $customer->name)
@section('page_title', $customer->name)
@section('page_subtitle', $customer->email)

@section('content')

    <div class="grid lg:grid-cols-3 gap-5">

        <div class="lg:col-span-2 space-y-5">
            <x-card padding="none">
                <x-card-header title="Order History" />
                @if($orders->isEmpty())
                    <x-empty-state icon="package" title="No orders" description="This customer has not placed any orders." />
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-slate-50 border-b border-[color:var(--color-border)]">
                                <tr>
                                    <th class="text-left px-5 py-2.5 text-xs font-medium text-ink-muted uppercase tracking-wider">Tracking</th>
                                    <th class="text-left px-5 py-2.5 text-xs font-medium text-ink-muted uppercase tracking-wider">Status</th>
                                    <th class="text-right px-5 py-2.5 text-xs font-medium text-ink-muted uppercase tracking-wider">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[color:var(--color-border)]">
                                @foreach($orders as $order)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-5 py-2.5">
                                            <a href="{{ route('admin.orders.show', $order) }}" class="font-mono text-xs text-brand">{{ $order->tracking_number }}</a>
                                        </td>
                                        <td class="px-5 py-2.5"><x-status-badge :status="$order->status" /></td>
                                        <td class="px-5 py-2.5 text-right font-medium text-xs">₦{{ number_format($order->total_fee, 0) }}</td>
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
        </div>

        <div class="space-y-5">
            <x-card padding="none">
                <x-card-header title="Profile" />
                <div class="p-5 space-y-3 text-sm">
                    <div><p class="text-xs text-ink-muted">Email</p><p class="font-medium text-ink mt-0.5">{{ $customer->email }}</p></div>
                    <div><p class="text-xs text-ink-muted">Phone</p><p class="font-medium text-ink mt-0.5">{{ $customer->phone ?? '—' }}</p></div>
                    <div><p class="text-xs text-ink-muted">Joined</p><p class="font-medium text-ink mt-0.5">{{ $customer->created_at->format('M d, Y') }}</p></div>
                    <div><p class="text-xs text-ink-muted">Status</p><div class="mt-1"><x-badge :color="$customer->is_active ? 'success' : 'slate'" dot>{{ $customer->is_active ? 'Active' : 'Inactive' }}</x-badge></div></div>
                </div>
            </x-card>

            <x-card padding="none">
                <x-card-header title="Stats" />
                <div class="p-5 space-y-3 text-sm">
                    <div class="flex justify-between"><span class="text-ink-muted">Total Orders</span><span class="font-semibold text-ink">{{ $customer->orders()->count() }}</span></div>
                    <div class="flex justify-between"><span class="text-ink-muted">Lifetime Spend</span><span class="font-semibold text-success">₦{{ number_format($spent, 0) }}</span></div>
                </div>
            </x-card>

            <form method="POST" action="{{ route('admin.customers.toggle', $customer) }}">
                @csrf
                <x-button type="submit" variant="{{ $customer->is_active ? 'danger' : 'success' }}" full>
                    {{ $customer->is_active ? 'Deactivate Account' : 'Activate Account' }}
                </x-button>
            </form>
        </div>
    </div>

@endsection