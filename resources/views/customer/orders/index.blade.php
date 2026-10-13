@extends('layouts.customer')

@section('title', 'My Orders')
@section('page_title', 'My Orders')
@section('page_subtitle', 'All your delivery orders in one place.')

@section('content')

    {{-- Filter bar --}}
    <div class="bg-white border border-[color:var(--color-border)] rounded-[var(--radius-lg)] p-4 mb-5">
        <form method="GET" action="{{ route('customer.orders.index') }}" class="flex flex-col sm:flex-row gap-3">
            {{-- Search --}}
            <div class="flex-1 relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-ink-subtle pointer-events-none">
                    <x-icon name="search" :size="16" />
                </span>
                <input
                    type="search"
                    name="q"
                    value="{{ $search }}"
                    placeholder="Search by tracking number, city..."
                    class="w-full h-10 pl-10 pr-3 text-sm bg-white border border-[color:var(--color-border-strong)] rounded-[var(--radius)] focus:border-brand focus:ring-2 focus:ring-brand/30 focus:outline-none"
                >
            </div>

            {{-- Status filter --}}
            <select
                name="status"
                class="h-10 pl-3 pr-9 text-sm bg-white border border-[color:var(--color-border-strong)] rounded-[var(--radius)] focus:border-brand focus:ring-2 focus:ring-brand/30 focus:outline-none"
            >
                <option value="">All statuses</option>
                @foreach(\App\Enums\OrderStatus::cases() as $status)
                    <option value="{{ $status->value }}" @selected($activeStatus === $status->value)>
                        {{ $status->label() }}
                    </option>
                @endforeach
            </select>

            <x-button type="submit" variant="secondary" icon="filter" size="md">
                Filter
            </x-button>
        </form>
    </div>

    {{-- Results --}}
    @if($orders->isEmpty())
        <x-card>
            <x-empty-state
                icon="package"
                title="{{ $search || $activeStatus ? 'No matching orders' : 'No orders yet' }}"
                description="{{ $search || $activeStatus ? 'Try a different filter or search term.' : 'Start by creating your first delivery.' }}"
            >
                <x-slot:action>
                    @if($search || $activeStatus)
                        <x-button href="{{ route('customer.orders.index') }}" variant="outline">
                            Clear filters
                        </x-button>
                    @else
                        <x-button href="{{ route('customer.orders.create') }}" icon="plus">
                            Create Order
                        </x-button>
                    @endif
                </x-slot:action>
            </x-empty-state>
        </x-card>
    @else
        <div class="grid md:grid-cols-2 gap-4">
            @foreach($orders as $order)
                <x-order-card :order="$order" />
            @endforeach
        </div>

        <div class="mt-6">
            <x-pagination :paginator="$orders" />
        </div>
    @endif

@endsection