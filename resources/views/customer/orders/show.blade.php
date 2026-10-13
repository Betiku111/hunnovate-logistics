@extends('layouts.customer')

@section('title', 'Order #' . $order->tracking_number)
@section('page_title', 'Order #' . $order->tracking_number)
@section('page_subtitle', $order->pickup_city . ' → ' . $order->dropoff_city)

@section('content')

    {{-- Back link --}}
    <a href="{{ route('customer.orders.index') }}" class="inline-flex items-center gap-1.5 text-sm text-ink-muted hover:text-ink mb-5">
        <x-icon name="arrow-left" :size="16" />
        Back to orders
    </a>

    <div class="grid lg:grid-cols-3 gap-5">

        {{-- Main --}}
        <div class="lg:col-span-2 space-y-5">

            {{-- Status banner --}}
            <x-card padding="none">
                <div class="p-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <div class="w-11 h-11 rounded-[var(--radius)] bg-brand-light text-brand flex items-center justify-center">
                            <x-icon name="package" :size="20" />
                        </div>
                        <div>
                            <p class="text-xs text-ink-muted uppercase tracking-wider">Current Status</p>
                            <div class="mt-1">
                                <x-status-badge :status="$order->status" />
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-xs text-ink-muted uppercase tracking-wider">Total Amount</p>
                        <p class="text-lg font-semibold text-ink mt-1">
                            ₦{{ number_format($order->total_fee, 2) }}
                        </p>
                    </div>
                </div>
            </x-card>

            @if(in_array($order->status, [\App\Enums\OrderStatus::Created, \App\Enums\OrderStatus::WaitingAdmin], true))
    <x-card padding="none">
        <x-card-header title="Cancel Order" />
        <div class="p-5" x-data="{ open: false }">
            <p class="text-sm text-ink-muted">You can cancel this order before a rider accepts it.</p>
            <button @click="open = !open" class="mt-3 text-sm text-danger hover:underline">
                Cancel order
            </button>

            <div x-show="open" x-cloak class="mt-4">
                <form method="POST" action="{{ route('customer.orders.cancel', $order) }}" class="space-y-3">
                    @csrf
                    <x-textarea name="reason" label="Reason for cancellation" rows="3" required />
                    <x-button type="submit" variant="danger" full icon="x">
                        Confirm Cancellation
                    </x-button>
                </form>
            </div>
        </div>
    </x-card>
@endif

            {{-- Route --}}
            <x-card padding="none">
                <x-card-header title="Delivery Route" subtitle="Pickup and destination details" />
                <div class="p-5 space-y-5">
                    {{-- Pickup --}}
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center pt-1 shrink-0">
                            <span class="w-3 h-3 rounded-full bg-brand"></span>
                            <span class="w-px flex-1 bg-slate-200 my-1"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-ink-muted uppercase tracking-wider">Pickup</p>
                            <p class="text-sm font-semibold text-ink mt-1">{{ $order->pickup_contact_name }}</p>
                            <p class="text-sm text-ink-muted">{{ $order->pickup_contact_phone }}</p>
                            <p class="text-sm text-ink-muted mt-1">{{ $order->pickup_address }}</p>
                            <p class="text-xs text-ink-subtle mt-0.5">{{ $order->pickup_city }}, {{ $order->pickup_state }}</p>
                        </div>
                    </div>

                    {{-- Dropoff --}}
                    <div class="flex gap-4">
                        <div class="flex flex-col items-center pt-1 shrink-0">
                            <span class="w-3 h-3 rounded-full bg-success"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-ink-muted uppercase tracking-wider">Deliver To</p>
                            <p class="text-sm font-semibold text-ink mt-1">{{ $order->dropoff_contact_name }}</p>
                            <p class="text-sm text-ink-muted">{{ $order->dropoff_contact_phone }}</p>
                            <p class="text-sm text-ink-muted mt-1">{{ $order->dropoff_address }}</p>
                            <p class="text-xs text-ink-subtle mt-0.5">{{ $order->dropoff_city }}, {{ $order->dropoff_state }}</p>
                        </div>
                    </div>
                </div>
            </x-card>

            {{-- Package --}}
            <x-card padding="none">
                <x-card-header title="Package Details" />
                <dl class="p-5 grid grid-cols-2 sm:grid-cols-4 gap-4 text-sm">
                    <div>
                        <dt class="text-xs text-ink-muted uppercase tracking-wider">Vehicle</dt>
                        <dd class="mt-1 font-medium text-ink">{{ $order->vehicle_type->label() }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-ink-muted uppercase tracking-wider">Weight</dt>
                        <dd class="mt-1 font-medium text-ink">{{ $order->package_weight_kg }} kg</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-ink-muted uppercase tracking-wider">Fragile</dt>
                        <dd class="mt-1 font-medium text-ink">{{ $order->is_fragile ? 'Yes' : 'No' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-ink-muted uppercase tracking-wider">Express</dt>
                        <dd class="mt-1 font-medium text-ink">{{ $order->is_express ? 'Yes' : 'No' }}</dd>
                    </div>
                    @if($order->package_description)
                        <div class="col-span-2 sm:col-span-4">
                            <dt class="text-xs text-ink-muted uppercase tracking-wider">Description</dt>
                            <dd class="mt-1 text-ink">{{ $order->package_description }}</dd>
                        </div>
                    @endif
                    @if($order->special_instructions)
                        <div class="col-span-2 sm:col-span-4">
                            <dt class="text-xs text-ink-muted uppercase tracking-wider">Special Instructions</dt>
                            <dd class="mt-1 text-ink">{{ $order->special_instructions }}</dd>
                        </div>
                    @endif
                </dl>
            </x-card>

            {{-- Timeline --}}
            <x-card padding="none">
                <x-card-header title="Delivery Timeline" subtitle="Every status update" />
                <div class="p-5">
                    @if($order->statusHistory->isEmpty())
                        <p class="text-sm text-ink-muted">No status updates yet.</p>
                    @else
                        <x-order-timeline :order="$order" />
                    @endif
                </div>
            </x-card>

        </div>

        {{-- Sidebar --}}
        <div class="space-y-5">

            {{-- Rider --}}
            <x-card padding="none">
                <x-card-header title="Your Rider" />
                <div class="p-5">
                    @if($order->rider)
                        <div class="flex items-center gap-3">
                            <span class="w-11 h-11 rounded-full bg-brand text-white flex items-center justify-center font-semibold text-sm">
                                {{ collect(explode(' ', $order->rider->user->name))->take(2)->map(fn($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('') }}
                            </span>
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-ink truncate">{{ $order->rider->user->name }}</p>
                                <p class="text-xs text-ink-muted flex items-center gap-1.5">
                                    <x-icon name="star" :size="12" />
                                    {{ number_format((float) $order->rider->rating, 1) }} · {{ $order->rider->total_deliveries }} deliveries
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <p class="text-sm text-ink-muted">No rider assigned yet.</p>
                            <p class="text-xs text-ink-subtle mt-1">A rider will be assigned shortly.</p>
                        </div>
                    @endif
                </div>
            </x-card>

            {{-- Tracking preview --}}
            <x-card padding="none">
    <x-card-header title="Live Tracking" subtitle="Rider location updates every 15 seconds">
        <x-slot:actions>
            <a href="{{ route('track.show', $order->tracking_number) }}" class="text-xs font-medium text-brand">
                Full view →
            </a>
        </x-slot:actions>
    </x-card-header>
    <div class="p-5">
        @if(in_array($order->status, [
            \App\Enums\OrderStatus::Assigned,
            \App\Enums\OrderStatus::Accepted,
            \App\Enums\OrderStatus::RiderArriving,
            \App\Enums\OrderStatus::Arrived,
            \App\Enums\OrderStatus::PickedUp,
            \App\Enums\OrderStatus::InTransit,
        ], true))
            <x-tracking-map :order="$order" height="260px" :refresh-seconds="15" :interactive="false" />
        @else
            <div class="aspect-video bg-slate-50 rounded-[var(--radius-lg)] flex items-center justify-center">
                <div class="text-center">
                    <x-icon name="map-pin" :size="24" class="text-ink-subtle mx-auto mb-2" />
                    <p class="text-sm text-ink-muted">Tracking begins when a rider accepts.</p>
                </div>
            </div>
        @endif
    </div>
</x-card>

            {{-- Payment --}}
            <x-card padding="none">
                <x-card-header title="Payment" />
                <div class="p-5 space-y-3 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-ink-muted">Method</span>
                        <span class="font-medium text-ink">{{ $order->payment_method ? ucfirst($order->payment_method) : 'Pending' }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-ink-muted">Status</span>
                        <x-status-badge :status="$order->payment_status" />
                    </div>
                    <div class="flex items-center justify-between pt-3 border-t border-[color:var(--color-border)]">
                        <span class="font-medium text-ink">Total</span>
                        <span class="font-semibold text-ink">₦{{ number_format($order->total_fee, 2) }}</span>
                    </div>
                </div>
            </x-card>

        </div>
    </div>

@endsection