@extends('layouts.admin')

@section('title', 'Live Tracking')
@section('page_title', 'Live Tracking')
@section('page_subtitle', $activeOrders->count() . ' active order(s) · ' . $onlineRiders->count() . ' rider(s) online')

@section('content')

    <div class="grid lg:grid-cols-4 gap-5">

        {{-- Map column --}}
        <div class="lg:col-span-3">
            <x-card padding="none" class="overflow-hidden">
                <div
                    x-data="adminLiveMap({
                        orders: {{ $activeOrders->map(fn($o) => [
                            'id' => $o->id,
                            'tracking_number' => $o->tracking_number,
                            'status' => $o->status->value,
                            'status_label' => $o->status->label(),
                            'customer_name' => $o->customer?->name,
                            'pickup' => [
                                'lat' => $o->pickup_lat ? (float) $o->pickup_lat : null,
                                'lng' => $o->pickup_lng ? (float) $o->pickup_lng : null,
                                'label' => $o->pickup_city,
                            ],
                            'dropoff' => [
                                'lat' => $o->dropoff_lat ? (float) $o->dropoff_lat : null,
                                'lng' => $o->dropoff_lng ? (float) $o->dropoff_lng : null,
                                'label' => $o->dropoff_city,
                            ],
                            'rider' => $o->rider ? [
                                'name' => $o->rider->user->name,
                                'lat' => $o->rider->current_lat ? (float) $o->rider->current_lat : null,
                                'lng' => $o->rider->current_lng ? (float) $o->rider->current_lng : null,
                            ] : null,
                        ])->toJson() }},
                        refreshUrl: '{{ route('admin.tracking.index') }}',
                    })"
                    x-init="init()"
                    wire:ignore
                >
                    <div id="admin-live-map" style="height: 600px;"></div>
                </div>
            </x-card>
        </div>

        {{-- Sidebar: active orders --}}
        <div class="space-y-3">
            <h3 class="text-xs font-semibold text-ink-muted uppercase tracking-wider px-1">Active Orders</h3>

            @if($activeOrders->isEmpty())
                <x-card>
                    <x-empty-state icon="inbox" title="No active orders" description="All orders are delivered or cancelled." />
                </x-card>
            @else
                <div class="space-y-2 max-h-[600px] overflow-y-auto scrollbar-thin pr-1">
                    @foreach($activeOrders as $order)
                        <a href="{{ route('admin.orders.show', $order) }}"
                           class="block bg-white border border-[color:var(--color-border)] rounded-[var(--radius)] p-3 hover:border-brand/40 transition-colors">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <div class="min-w-0">
                                    <p class="text-xs font-mono text-ink-muted truncate">{{ $order->tracking_number }}</p>
                                    <p class="text-sm font-medium text-ink truncate mt-0.5">{{ $order->pickup_city }} → {{ $order->dropoff_city }}</p>
                                </div>
                                <x-status-badge :status="$order->status" />
                            </div>
                            <div class="flex items-center gap-2 text-xs text-ink-muted">
                                @if($order->rider)
                                    <span class="w-4 h-4 rounded-full bg-brand text-white flex items-center justify-center text-[8px] font-semibold">
                                        {{ collect(explode(' ', $order->rider->user->name))->take(1)->map(fn($p) => mb_strtoupper(mb_substr($p, 0, 1)))->first() }}
                                    </span>
                                    <span class="truncate">{{ $order->rider->user->name }}</span>
                                @else
                                    <span class="text-ink-subtle">No rider</span>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

@endsection

@push('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        .leaflet-container { font-family: inherit; }
        .rider-marker {
            background: #4F46E5; width: 32px; height: 32px;
            border-radius: 50%; border: 3px solid white;
            box-shadow: 0 2px 8px rgba(79,70,229,0.5);
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: bold; font-size: 11px;
        }
        .pin-marker {
            width: 20px; height: 20px;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
    function adminLiveMap(config) {
        return {
            map: null,
            markers: {},

            init() {
                setTimeout(() => {
                    this.map = L.map('admin-live-map').setView([6.5244, 3.3792], 11);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        maxZoom: 19, attribution: '© OpenStreetMap',
                    }).addTo(this.map);

                    this.renderOrders(config.orders);
                }, 100);
            },

            renderOrders(orders) {
                const bounds = [];
                orders.forEach((o) => {
                    // Pickup
                    if (o.pickup.lat && o.pickup.lng) {
                        L.marker([o.pickup.lat, o.pickup.lng], {
                            icon: L.divIcon({
                                className: '',
                                html: '<div class="pin-marker" style="background:#4F46E5"></div>',
                                iconSize: [20, 20], iconAnchor: [10, 10],
                            }),
                        }).addTo(this.map).bindPopup(`<b>${o.tracking_number}</b><br>Pickup: ${o.pickup.label}`);
                        bounds.push([o.pickup.lat, o.pickup.lng]);
                    }

                    // Dropoff
                    if (o.dropoff.lat && o.dropoff.lng) {
                        L.marker([o.dropoff.lat, o.dropoff.lng], {
                            icon: L.divIcon({
                                className: '',
                                html: '<div class="pin-marker" style="background:#16A34A"></div>',
                                iconSize: [20, 20], iconAnchor: [10, 10],
                            }),
                        }).addTo(this.map).bindPopup(`<b>${o.tracking_number}</b><br>Deliver: ${o.dropoff.label}`);
                        bounds.push([o.dropoff.lat, o.dropoff.lng]);
                    }

                    // Rider
                    if (o.rider?.lat && o.rider?.lng) {
                        const initials = o.rider.name.split(' ').slice(0,2).map(p => p[0]).join('').toUpperCase();
                        const marker = L.marker([o.rider.lat, o.rider.lng], {
                            icon: L.divIcon({
                                className: '',
                                html: `<div class="rider-marker">${initials}</div>`,
                                iconSize: [32, 32], iconAnchor: [16, 16],
                            }),
                            zIndexOffset: 1000,
                        }).addTo(this.map).bindPopup(
                            `<b>${o.rider.name}</b><br>${o.status_label}<br>${o.tracking_number}`
                        );
                        this.markers[o.id] = marker;
                        bounds.push([o.rider.lat, o.rider.lng]);
                    }

                    // Route line
                    const points = [];
                    if (o.pickup.lat && o.pickup.lng) points.push([o.pickup.lat, o.pickup.lng]);
                    if (o.rider?.lat && o.rider?.lng) points.push([o.rider.lat, o.rider.lng]);
                    if (o.dropoff.lat && o.dropoff.lng) points.push([o.dropoff.lat, o.dropoff.lng]);

                    if (points.length >= 2) {
                        L.polyline(points, {
                            color: '#4F46E5', weight: 2, opacity: 0.5, dashArray: '6, 4',
                        }).addTo(this.map);
                    }
                });

                if (bounds.length > 0) {
                    this.map.fitBounds(L.latLngBounds(bounds).pad(0.15));
                }
            },
        };
    }
    </script>
@endpush