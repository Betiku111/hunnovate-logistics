@extends('layouts.customer')

@section('title', 'Dashboard')
@section('page_title', 'Welcome back, ' . explode(' ', auth()->user()->name)[0] . ' 👋')
@section('page_subtitle', 'Here is what is happening with your deliveries.')

@section('content')

    {{-- Metrics --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-stat-card label="Total Orders" value="{{ number_format($metrics['total']) }}" icon="package" color="brand" />
        <x-stat-card label="Active" value="{{ number_format($metrics['active']) }}" icon="truck" color="info" />
        <x-stat-card label="Delivered" value="{{ number_format($metrics['delivered']) }}" icon="check-circle" color="success" />
        <x-stat-card label="Total Spent" value="₦{{ number_format($metrics['spent'], 0) }}" icon="wallet" color="warning" />
    </div>

    {{-- Quick actions --}}
    <div class="grid sm:grid-cols-3 gap-4 mb-6">
        <a href="{{ route('customer.orders.create') }}"
           class="group bg-white border border-[color:var(--color-border)] rounded-[var(--radius-lg)] p-5 hover:border-brand/30 hover:shadow-[var(--shadow-hover)] transition-all">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-[10px] bg-brand-light text-brand flex items-center justify-center group-hover:bg-brand group-hover:text-white transition-colors">
                    <x-icon name="plus-circle" :size="20" />
                </span>
                <div>
                    <p class="text-sm font-semibold text-ink">Create Order</p>
                    <p class="text-xs text-ink-muted">Start a new delivery</p>
                </div>
            </div>
        </a>

        <a href="{{ route('track') }}"
           class="group bg-white border border-[color:var(--color-border)] rounded-[var(--radius-lg)] p-5 hover:border-brand/30 hover:shadow-[var(--shadow-hover)] transition-all">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-[10px] bg-info-bg text-info flex items-center justify-center group-hover:bg-info group-hover:text-white transition-colors">
                    <x-icon name="map-pin" :size="20" />
                </span>
                <div>
                    <p class="text-sm font-semibold text-ink">Track Order</p>
                    <p class="text-xs text-ink-muted">Find by tracking number</p>
                </div>
            </div>
        </a>

        <a href="{{ route('customer.support.index') }}"
           class="group bg-white border border-[color:var(--color-border)] rounded-[var(--radius-lg)] p-5 hover:border-brand/30 hover:shadow-[var(--shadow-hover)] transition-all">
            <div class="flex items-center gap-3">
                <span class="w-10 h-10 rounded-[10px] bg-success-bg text-success flex items-center justify-center group-hover:bg-success group-hover:text-white transition-colors">
                    <x-icon name="headphones" :size="20" />
                </span>
                <div>
                    <p class="text-sm font-semibold text-ink">Support</p>
                    <p class="text-xs text-ink-muted">Get help with anything</p>
                </div>
            </div>
        </a>
    </div>

    {{-- Active orders --}}
    @if($activeOrders->isNotEmpty())
        <section class="mb-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-semibold text-ink">Active Deliveries</h2>
                    <p class="text-xs text-ink-muted mt-0.5">Orders currently in progress</p>
                </div>
                <a href="{{ route('customer.orders.index') }}" class="text-sm font-medium text-brand hover:text-brand-hover">
                    View all →
                </a>
            </div>

            <div class="grid md:grid-cols-2 gap-4">
                @foreach($activeOrders as $order)
                    <x-order-card :order="$order" />
                @endforeach
            </div>
        </section>
    @endif

    {{-- Recent orders --}}
    <section>
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-base font-semibold text-ink">Recent Orders</h2>
                <p class="text-xs text-ink-muted mt-0.5">Your latest deliveries</p>
            </div>
            <a href="{{ route('customer.orders.history') }}" class="text-sm font-medium text-brand hover:text-brand-hover">
                View history →
            </a>
        </div>

        @if($recentOrders->isEmpty())
            <x-card>
                <x-empty-state
                    icon="package"
                    title="No orders yet"
                    description="Create your first delivery and it will appear here."
                >
                    <x-slot:action>
                        <x-button href="{{ route('customer.orders.create') }}" icon="plus">
                            Create Order
                        </x-button>
                    </x-slot:action>
                </x-empty-state>
            </x-card>
        @else
            <div class="grid md:grid-cols-2 gap-4">
                @foreach($recentOrders as $order)
                    <x-order-card :order="$order" />
                @endforeach
            </div>
        @endif
    </section>

@endsection