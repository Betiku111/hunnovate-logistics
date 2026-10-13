@extends('layouts.admin')

@section('title', 'Order #' . $order->tracking_number)
@section('page_title', 'Order #' . $order->tracking_number)
@section('page_subtitle', $order->pickup_city . ' → ' . $order->dropoff_city)

@section('content')

    <a href="{{ route('admin.orders.index') }}" class="inline-flex items-center gap-1.5 text-sm text-ink-muted hover:text-ink mb-5">
        <x-icon name="arrow-left" :size="16" />
        Back to orders
    </a>

    <div class="grid lg:grid-cols-3 gap-5">

        <div class="lg:col-span-2 space-y-5">
            <x-card padding="none">
                <div class="p-5 flex items-center justify-between">
                    <x-status-badge :status="$order->status" />
                    <p class="text-lg font-semibold text-ink">₦{{ number_format($order->total_fee, 2) }}</p>
                </div>
            </x-card>

            <x-card padding="none">
                <x-card-header title="Route" />
                <div class="p-5 space-y-5">
                    <div class="flex gap-3">
                        <span class="w-2 h-2 rounded-full bg-brand mt-2 shrink-0"></span>
                        <div>
                            <p class="text-xs text-ink-muted uppercase tracking-wider">Pickup</p>
                            <p class="text-sm font-medium text-ink mt-0.5">{{ $order->pickup_contact_name }}</p>
                            <p class="text-sm text-ink-muted">{{ $order->pickup_contact_phone }}</p>
                            <p class="text-sm text-ink-muted">{{ $order->pickup_address }}, {{ $order->pickup_city }}</p>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <span class="w-2 h-2 rounded-full bg-success mt-2 shrink-0"></span>
                        <div>
                            <p class="text-xs text-ink-muted uppercase tracking-wider">Deliver To</p>
                            <p class="text-sm font-medium text-ink mt-0.5">{{ $order->dropoff_contact_name }}</p>
                            <p class="text-sm text-ink-muted">{{ $order->dropoff_contact_phone }}</p>
                            <p class="text-sm text-ink-muted">{{ $order->dropoff_address }}, {{ $order->dropoff_city }}</p>
                        </div>
                    </div>
                </div>
            </x-card>

            <x-card padding="none">
                <x-card-header title="Package" />
                <div class="p-5 grid grid-cols-2 sm:grid-cols-4 gap-3 text-sm">
                    <div><p class="text-xs text-ink-muted">Vehicle</p><p class="font-medium text-ink mt-0.5">{{ $order->vehicle_type->label() }}</p></div>
                    <div><p class="text-xs text-ink-muted">Weight</p><p class="font-medium text-ink mt-0.5">{{ $order->package_weight_kg }} kg</p></div>
                    <div><p class="text-xs text-ink-muted">Distance</p><p class="font-medium text-ink mt-0.5">{{ $order->distance_km ? $order->distance_km . ' km' : '—' }}</p></div>
                    <div><p class="text-xs text-ink-muted">Express</p><p class="font-medium text-ink mt-0.5">{{ $order->is_express ? 'Yes' : 'No' }}</p></div>
                    @if($order->package_description)
                        <div class="col-span-full"><p class="text-xs text-ink-muted">Description</p><p class="text-ink mt-0.5">{{ $order->package_description }}</p></div>
                    @endif
                </div>
            </x-card>

            <x-card padding="none">
                <x-card-header title="Timeline" />
                <div class="p-5">
                    @if($order->statusHistory->isEmpty())
                        <p class="text-sm text-ink-muted">No history yet.</p>
                    @else
                        <x-order-timeline :order="$order" />
                    @endif
                </div>
            </x-card>
        </div>

        <div class="space-y-5">
            <x-card padding="none">
                <x-card-header title="Customer" />
                <div class="p-5">
                    @if($order->customer)
                        <a href="{{ route('admin.customers.show', $order->customer) }}" class="flex items-center gap-3 group">
                            <span class="w-10 h-10 rounded-full bg-brand text-white flex items-center justify-center text-xs font-semibold">
                                {{ collect(explode(' ', $order->customer->name))->take(2)->map(fn($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('') }}
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-ink truncate group-hover:text-brand">{{ $order->customer->name }}</p>
                                <p class="text-xs text-ink-muted truncate">{{ $order->customer->email }}</p>
                            </div>
                        </a>
                    @else
                        <p class="text-sm text-ink-muted">No customer record.</p>
                    @endif
                </div>
            </x-card>

            <x-card padding="none">
                <x-card-header title="Rider" />
                <div class="p-5">
                    @if($order->rider)
                        <a href="{{ route('admin.riders.show', $order->rider) }}" class="flex items-center gap-3 group">
                            <span class="w-10 h-10 rounded-full bg-brand text-white flex items-center justify-center text-xs font-semibold">
                                {{ collect(explode(' ', $order->rider->user->name))->take(2)->map(fn($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('') }}
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-ink truncate group-hover:text-brand">{{ $order->rider->user->name }}</p>
                                <p class="text-xs text-ink-muted">★ {{ number_format((float) $order->rider->rating, 1) }}</p>
                            </div>
                        </a>
                    @else
                        <p class="text-sm text-ink-muted">No rider assigned.</p>
                        <a href="{{ route('admin.assignments.index') }}" class="text-xs text-brand mt-2 inline-block">Assign rider →</a>
                    @endif
                </div>
            </x-card>

            <x-card padding="none">
                <x-card-header title="Payment" />
                <div class="p-5 space-y-2.5 text-sm">
                    <div class="flex justify-between"><span class="text-ink-muted">Method</span><span class="font-medium">{{ $order->payment_method ? ucfirst($order->payment_method) : '—' }}</span></div>
                    <div class="flex justify-between"><span class="text-ink-muted">Status</span><x-status-badge :status="$order->payment_status" /></div>
                    <div class="flex justify-between pt-3 border-t border-[color:var(--color-border)]"><span class="font-medium">Total</span><span class="font-bold text-brand">₦{{ number_format($order->total_fee, 2) }}</span></div>
                </div>
            </x-card>

            @if(! $order->status->isFinal())
               <x-card padding="none">
    <x-card-header title="Rider">
        @if($order->rider && $order->status === \App\Enums\OrderStatus::Assigned)
            <x-slot:actions>
                <a href="{{ route('admin.assignments.show', $order) }}" class="text-xs font-medium text-brand">
                    Reassign →
                </a>
            </x-slot:actions>
        @endif
    </x-card-header>
    <div class="p-5">
        @if($order->rider)
            <a href="{{ route('admin.riders.show', $order->rider) }}" class="flex items-center gap-3 group">
                <span class="w-10 h-10 rounded-full bg-brand text-white flex items-center justify-center text-xs font-semibold">
                    {{ collect(explode(' ', $order->rider->user->name))->take(2)->map(fn($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('') }}
                </span>
                <div class="min-w-0">
                    <p class="text-sm font-semibold text-ink truncate group-hover:text-brand">{{ $order->rider->user->name }}</p>
                    <p class="text-xs text-ink-muted">★ {{ number_format((float) $order->rider->rating, 1) }}</p>
                </div>
            </a>
        @else
            <p class="text-sm text-ink-muted">No rider assigned.</p>
            @if($order->status === \App\Enums\OrderStatus::WaitingAdmin)
                <a href="{{ route('admin.assignments.show', $order) }}" class="mt-3 inline-flex items-center gap-1.5 text-sm font-medium text-brand hover:text-brand-hover">
                    <x-icon name="user-plus" :size="14" />
                    Assign Rider
                </a>
            @endif
        @endif
    </div>
</x-card>
            @endif
        </div>
    </div>

@endsection