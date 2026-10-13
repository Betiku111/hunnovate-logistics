@props([
    'order' => null,
    'dataUrl' => null,
    'height' => '400px',
    'refreshSeconds' => 15,
    'interactive' => true,
])

@php
    // Prefer explicit dataUrl; fall back to order-based route
    $resolvedUrl = $dataUrl
        ?? ($order ? (auth()->check()
            ? route('orders.tracking-data', $order)
            : route('track.data', $order->tracking_number))
            : null);
    $mapId = 'map-' . ($order?->id ?? uniqid());
@endphp

<div
    {{ $attributes->merge(['class' => 'relative bg-slate-100 rounded-[var(--radius-lg)] overflow-hidden border border-[color:var(--color-border)]']) }}
    style="height: {{ $height }}"
    x-data="trackingMap({
        id: '{{ $mapId }}',
        dataUrl: '{{ $resolvedUrl }}',
        refreshSeconds: {{ $refreshSeconds }},
        interactive: {{ $interactive ? 'true' : 'false' }},
    })"
    x-init="init()"
    wire:ignore
>
    <div id="{{ $mapId }}" class="absolute inset-0 z-0"></div>

    {{-- Loading overlay --}}
    <div
        x-show="loading"
        x-transition.opacity
        class="absolute inset-0 z-10 flex items-center justify-center bg-slate-100/80"
    >
        <div class="text-center">
            <svg class="animate-spin text-brand mx-auto" width="24" height="24" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
            </svg>
            <p class="text-xs text-ink-muted mt-2">Loading map…</p>
        </div>
    </div>

    {{-- No data overlay --}}
    <div
        x-show="! loading && ! hasData"
        x-transition.opacity
        class="absolute inset-0 z-10 flex items-center justify-center bg-slate-50/95"
    >
        <div class="text-center px-6">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-ink-subtle mx-auto mb-2">
                <path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/>
                <circle cx="12" cy="10" r="3"/>
            </svg>
            <p class="text-sm font-medium text-ink">No location data yet</p>
            <p class="text-xs text-ink-muted mt-1">Waiting for the rider to begin tracking.</p>
        </div>
    </div>

    {{-- Live status badge --}}
    <div
        x-show="! loading && hasData"
        class="absolute top-3 right-3 z-10 bg-white rounded-[var(--radius)] shadow-md px-3 py-2 flex items-center gap-2"
    >
        <span class="w-2 h-2 rounded-full" :class="fresh ? 'bg-emerald-500 animate-pulse' : 'bg-amber-500'"></span>
        <div class="text-xs">
            <p class="font-medium text-ink" x-text="fresh ? 'Live' : 'Last seen'"></p>
            <p class="text-ink-muted text-[10px]" x-text="lastUpdated"></p>
        </div>
    </div>

    {{-- ETA badge --}}
    <div
        x-show="! loading && hasData && eta"
        class="absolute bottom-3 left-3 z-10 bg-white rounded-[var(--radius)] shadow-md px-3 py-2"
    >
        <p class="text-[10px] text-ink-muted uppercase tracking-wide">ETA</p>
        <p class="text-sm font-semibold text-ink" x-text="eta"></p>
    </div>
</div>

@once
@push('head')
    {{-- Leaflet CSS --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <style>
        .leaflet-container { font-family: inherit; }
        .leaflet-popup-content-wrapper { border-radius: 10px; }
        .rider-marker {
            background-color: #4F46E5;
            width: 36px; height: 36px;
            border-radius: 50%;
            border: 3px solid white;
            box-shadow: 0 2px 8px rgba(79,70,229,0.4);
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: bold; font-size: 12px;
        }
        .pin-marker {
            width: 24px; height: 24px;
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
    function trackingMap(config) {
        return {
            loading: true,
            hasData: false,
            fresh: false,
            lastUpdated: '',
            eta: '',
            map: null,
            riderMarker: null,
            pickupMarker: null,
            dropoffMarker: null,
            routeLine: null,
            refreshTimer: null,
            data: null,

            async init() {
                await this.loadLeaflet();
                this.initMap();
                await this.refresh();
                this.startRefresh();
            },

            loadLeaflet() {
                return new Promise((resolve) => {
                    if (window.L) return resolve();
                    const check = setInterval(() => {
                        if (window.L) { clearInterval(check); resolve(); }
                    }, 50);
                });
            },

            initMap() {
                const el = document.getElementById(config.id);
                if (! el) return;

                this.map = L.map(el, {
                    zoomControl: config.interactive,
                    dragging: config.interactive,
                    scrollWheelZoom: config.interactive,
                    doubleClickZoom: config.interactive,
                    boxZoom: config.interactive,
                    keyboard: config.interactive,
                    attributionControl: true,
                }).setView([6.5244, 3.3792], 12); // Lagos default

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                    attribution: '© OpenStreetMap',
                }).addTo(this.map);
            },

            async refresh() {
                if (! config.dataUrl) {
                    this.loading = false;
                    return;
                }

                try {
                    const res = await fetch(config.dataUrl, {
                        headers: { 'Accept': 'application/json' },
                        credentials: 'same-origin',
                    });

                    if (! res.ok) throw new Error('Failed to fetch tracking data');
                    this.data = await res.json();
                    this.applyData();
                } catch (e) {
                    console.error('Tracking refresh failed:', e);
                } finally {
                    this.loading = false;
                }
            },

            applyData() {
                const d = this.data;
                if (! d) return;

                const pickup = d.order.pickup;
                const dropoff = d.order.dropoff;
                const rider = d.rider;

                const hasPickup = pickup.lat && pickup.lng;
                const hasDropoff = dropoff.lat && dropoff.lng;
                const hasRider = rider && rider.lat && rider.lng;

                this.hasData = hasPickup || hasDropoff || hasRider;

                // Update freshness
                if (rider?.location_updated_at) {
                    const dt = new Date(rider.location_updated_at);
                    const seconds = Math.floor((Date.now() - dt.getTime()) / 1000);
                    this.fresh = seconds < 60;
                    this.lastUpdated = seconds < 60
                        ? `${seconds}s ago`
                        : `${Math.floor(seconds / 60)}m ago`;
                } else {
                    this.fresh = false;
                    this.lastUpdated = '—';
                }

                // Update ETA
                this.eta = d.order.eta_minutes ? `${d.order.eta_minutes} min` : '';

                // Pickup marker
                if (hasPickup) {
                    const latlng = [pickup.lat, pickup.lng];
                    if (! this.pickupMarker) {
                        this.pickupMarker = L.marker(latlng, {
                            icon: L.divIcon({
                                className: '',
                                html: '<div class="pin-marker" style="background:#4F46E5"></div>',
                                iconSize: [24, 24],
                                iconAnchor: [12, 12],
                            }),
                        }).addTo(this.map).bindPopup(`<b>Pickup</b><br>${pickup.address}`);
                    } else {
                        this.pickupMarker.setLatLng(latlng);
                    }
                }

                // Dropoff marker
                if (hasDropoff) {
                    const latlng = [dropoff.lat, dropoff.lng];
                    if (! this.dropoffMarker) {
                        this.dropoffMarker = L.marker(latlng, {
                            icon: L.divIcon({
                                className: '',
                                html: '<div class="pin-marker" style="background:#16A34A"></div>',
                                iconSize: [24, 24],
                                iconAnchor: [12, 12],
                            }),
                        }).addTo(this.map).bindPopup(`<b>Deliver to</b><br>${dropoff.address}`);
                    } else {
                        this.dropoffMarker.setLatLng(latlng);
                    }
                }

                // Rider marker
                if (hasRider) {
                    const latlng = [rider.lat, rider.lng];
                    const initials = rider.name
                        .split(' ')
                        .slice(0, 2)
                        .map(p => p[0])
                        .join('')
                        .toUpperCase();

                    if (! this.riderMarker) {
                        this.riderMarker = L.marker(latlng, {
                            icon: L.divIcon({
                                className: '',
                                html: `<div class="rider-marker">${initials}</div>`,
                                iconSize: [36, 36],
                                iconAnchor: [18, 18],
                            }),
                            zIndexOffset: 1000,
                        }).addTo(this.map).bindPopup(`<b>${rider.name}</b><br>★ ${rider.rating}`);
                    } else {
                        this.riderMarker.setLatLng(latlng);
                    }
                }

                // Fit bounds if we have markers
                this.fitBounds();

                // Update route line
                this.drawRoute();
            },

            drawRoute() {
                const d = this.data;
                if (! d) return;

                const points = [];
                const pickup = d.order.pickup;
                const dropoff = d.order.dropoff;
                const rider = d.rider;

                if (pickup.lat && pickup.lng) points.push([pickup.lat, pickup.lng]);
                if (rider?.lat && rider?.lng) points.push([rider.lat, rider.lng]);
                if (dropoff.lat && dropoff.lng) points.push([dropoff.lat, dropoff.lng]);

                if (points.length < 2) return;

                if (this.routeLine) {
                    this.map.removeLayer(this.routeLine);
                }

                this.routeLine = L.polyline(points, {
                    color: '#4F46E5',
                    weight: 3,
                    opacity: 0.7,
                    dashArray: '8, 6',
                    lineCap: 'round',
                }).addTo(this.map);
            },

            fitBounds() {
                const markers = [this.pickupMarker, this.riderMarker, this.dropoffMarker].filter(Boolean);
                if (markers.length === 0) return;

                const group = L.featureGroup(markers);
                this.map.fitBounds(group.getBounds().pad(0.15));
            },

            startRefresh() {
                if (config.refreshSeconds <= 0) return;
                this.refreshTimer = setInterval(() => this.refresh(), config.refreshSeconds * 1000);
            },

            destroy() {
                if (this.refreshTimer) clearInterval(this.refreshTimer);
                if (this.map) this.map.remove();
            },
        };
    }
    </script>
@endpush
@endonce