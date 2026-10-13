@extends('layouts.rider')

@section('title', 'Delivery #' . $order->tracking_number)

@section('content')

    <div class="px-4 py-4">

        <a href="{{ route('rider.deliveries.index') }}" class="inline-flex items-center gap-1.5 text-sm text-ink-muted mb-4">
            <x-icon name="arrow-left" :size="16" />
            Back
        </a>

        {{-- Status header --}}
        <x-card padding="none" class="mb-4">
            <div class="p-5">
                <div class="flex items-center justify-between mb-3">
                    <p class="font-mono text-sm text-ink-muted">{{ $order->tracking_number }}</p>
                    <x-status-badge :status="$order->status" />
                </div>
                <p class="text-base font-semibold text-ink">{{ $order->pickup_city }} → {{ $order->dropoff_city }}</p>
            </div>
        </x-card>

        {{-- Next action --}}
        @if($nextStatus)
            <form
                method="POST"
                action="{{ route('rider.deliveries.advance', $order) }}"
                x-data="{ submitting: false, capturingLocation: false }"
                @submit.prevent="
                    capturingLocation = true;
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            pos => {
                                $el.querySelector('[name=lat]').value = pos.coords.latitude;
                                $el.querySelector('[name=lng]').value = pos.coords.longitude;
                                submitting = true;
                                $el.submit();
                            },
                            err => {
                                capturingLocation = false;
                                submitting = true;
                                $el.submit();
                            },
                            { enableHighAccuracy: true, timeout: 5000 }
                        );
                    } else {
                        submitting = true;
                        $el.submit();
                    }
                "
                class="mb-4"
            >
                @csrf
                <input type="hidden" name="lat" value="">
                <input type="hidden" name="lng" value="">

                <div class="bg-brand-light border border-brand/20 rounded-[var(--radius-lg)] p-4">
                    <p class="text-xs font-medium text-brand uppercase tracking-wider mb-1">Next Step</p>
                    <p class="text-base font-semibold text-ink mb-3">{{ $nextStatus->label() }}</p>

                    <x-button
                        type="submit"
                        size="lg"
                        full
                        icon="check"
                        ::loading="submitting"
                        class="!bg-brand !text-white"
                    >
                        <span x-show="!submitting">Confirm: {{ $nextStatus->label() }}</span>
                        <span x-show="submitting">Updating…</span>
                    </x-button>
                </div>

                @if(in_array($order->status, [\App\Enums\OrderStatus::Assigned, \App\Enums\OrderStatus::Accepted], true))
    <x-card padding="none" class="mb-4">
        <div class="p-4" x-data="{ open: false }">
            <button @click="open = !open" class="text-xs text-danger hover:underline">
                Cancel this delivery
            </button>

            <div x-show="open" x-cloak class="mt-3">
                <form method="POST" action="{{ route('rider.deliveries.cancel', $order) }}" class="space-y-2">
                    @csrf
                    <x-textarea name="reason" label="Why are you cancelling?" rows="3" required />
                    <x-button type="submit" variant="danger" full icon="x">Confirm</x-button>
                </form>
            </div>
        </div>
    </x-card>
    
@endif


{{-- Add this inside the form, before the submit button --}}
<details class="mt-3">
    <summary class="text-xs text-ink-muted cursor-pointer hover:text-ink">
        Add a note (optional)
    </summary>
    <div class="mt-2">
        <textarea name="note" rows="2"
            placeholder="Any note for the customer or admin…"
            class="block w-full rounded-[var(--radius)] border border-[color:var(--color-border-strong)] text-sm px-3 py-2 focus:border-brand focus:ring-2 focus:ring-brand/30 focus:outline-none"></textarea>
    </div>
</details>
            </form>
        @elseif($order->status === \App\Enums\OrderStatus::Delivered)
            <div class="bg-success-bg border border-green-200 rounded-[var(--radius-lg)] p-4 mb-4 text-center">
                <x-icon name="check-circle" :size="32" class="text-success mx-auto mb-2" />
                <p class="text-sm font-semibold text-success">Delivery Completed</p>
                <p class="text-xs text-success/80 mt-1">Thank you! Your earnings have been recorded.</p>
            </div>
        @endif

        {{-- Route --}}
        <x-card padding="none" class="mb-4">
            <div class="p-5 space-y-4">
                <div class="flex gap-3">
                    <span class="w-2 h-2 rounded-full bg-brand mt-2 shrink-0"></span>
                    <div class="min-w-0">
                        <p class="text-xs text-ink-muted uppercase tracking-wider">Pickup</p>
                        <p class="text-sm font-medium text-ink mt-0.5">{{ $order->pickup_contact_name }}</p>
                        <p class="text-xs text-ink-muted">📞 {{ $order->pickup_contact_phone }}</p>
                        <p class="text-sm text-ink-muted mt-1">{{ $order->pickup_address }}, {{ $order->pickup_city }}</p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <span class="w-2 h-2 rounded-full bg-success mt-2 shrink-0"></span>
                    <div class="min-w-0">
                        <p class="text-xs text-ink-muted uppercase tracking-wider">Deliver to</p>
                        <p class="text-sm font-medium text-ink mt-0.5">{{ $order->dropoff_contact_name }}</p>
                        <p class="text-xs text-ink-muted">📞 {{ $order->dropoff_contact_phone }}</p>
                        <p class="text-sm text-ink-muted mt-1">{{ $order->dropoff_address }}, {{ $order->dropoff_city }}</p>
                    </div>
                </div>
            </div>
        </x-card>

        {{-- Package --}}
        <x-card padding="none" class="mb-4">
            <div class="p-5 space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-ink-muted">Vehicle</span>
                    <span class="font-medium text-ink">{{ $order->vehicle_type->label() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-ink-muted">Weight</span>
                    <span class="font-medium text-ink">{{ $order->package_weight_kg }} kg</span>
                </div>
                @if($order->package_description)
                    <div class="pt-3 border-t border-[color:var(--color-border)]">
                        <p class="text-xs text-ink-muted mb-1">Description</p>
                        <p class="text-ink">{{ $order->package_description }}</p>
                    </div>
                @endif
                @if($order->special_instructions)
                    <div class="pt-3 border-t border-[color:var(--color-border)]">
                        <p class="text-xs text-ink-muted mb-1">Instructions</p>
                        <p class="text-ink">{{ $order->special_instructions }}</p>
                    </div>
                @endif
            </div>
        </x-card>

        {{-- Timeline --}}
        @if($order->statusHistory->isNotEmpty())
            <x-card padding="none">
                <div class="p-5">
                    <p class="text-xs font-medium text-ink-muted uppercase tracking-wider mb-4">Timeline</p>
                    <x-order-timeline :order="$order" />
                </div>
            </x-card>
        @endif
    </div>

@endsection
@push('scripts')
@if(in_array($order->status, [
    \App\Enums\OrderStatus::Assigned,
    \App\Enums\OrderStatus::Accepted,
    \App\Enums\OrderStatus::RiderArriving,
    \App\Enums\OrderStatus::Arrived,
    \App\Enums\OrderStatus::PickedUp,
    \App\Enums\OrderStatus::InTransit,
], true))
<script>
(function() {
    const pingUrl = '{{ route('rider.location.store') }}';
    const csrf = '{{ csrf_token() }}';
    const orderId = '{{ $order->id }}';

    let lastSent = 0;
    const MIN_INTERVAL = 15000; // 15s between pings
    let watchId = null;

    async function ping(lat, lng, accuracy, speed, heading) {
        const now = Date.now();
        if (now - lastSent < MIN_INTERVAL) return;
        lastSent = now;

        try {
            await fetch(pingUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    order_id: orderId,
                    lat: lat,
                    lng: lng,
                    accuracy: accuracy,
                    speed: speed,
                    heading: heading,
                }),
            });
        } catch (e) {
            console.warn('Location ping failed:', e);
        }
    }

    function startWatching() {
        if (! navigator.geolocation) {
            console.warn('Geolocation not supported');
            return;
        }

        watchId = navigator.geolocation.watchPosition(
            (pos) => {
                ping(
                    pos.coords.latitude,
                    pos.coords.longitude,
                    pos.coords.accuracy,
                    pos.coords.speed ? pos.coords.speed * 3.6 : null, // m/s → km/h
                    pos.coords.heading,
                );
            },
            (err) => console.warn('Geolocation error:', err.message),
            {
                enableHighAccuracy: true,
                maximumAge: 10000,
                timeout: 20000,
            }
        );
    }

    // Start pinging when page loads
    if (document.readyState === 'complete') {
        startWatching();
    } else {
        window.addEventListener('load', startWatching);
    }

    // Stop on page unload
    window.addEventListener('beforeunload', () => {
        if (watchId !== null) navigator.geolocation.clearWatch(watchId);
    });
})();
</script>
@endif
@endpush