@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')
@section('page_subtitle', 'Overview of operations')

@section('content')

    {{-- KPI cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <x-stat-card label="Total Orders"      value="{{ number_format($kpis['totalOrders']) }}"      icon="package" color="brand" />
        <x-stat-card label="Pending"           value="{{ number_format($kpis['pendingOrders']) }}"    icon="clock" color="warning" delta="{{ $kpis['pendingOrders'] > 0 ? 'Needs action' : 'All clear' }}" trend="{{ $kpis['pendingOrders'] > 0 ? 'down' : 'up' }}" />
        <x-stat-card label="Active Deliveries" value="{{ number_format($kpis['activeDelivery']) }}"   icon="truck" color="info" />
        <x-stat-card label="Revenue"           value="₦{{ number_format($kpis['revenue'], 0) }}"      icon="wallet" color="success" />
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        <x-stat-card label="Delivered" value="{{ number_format($kpis['delivered']) }}" icon="check-circle" color="success" />
        <x-stat-card label="Customers" value="{{ number_format($kpis['customers']) }}" icon="users" color="brand" />
        <x-stat-card label="Verified Riders" value="{{ number_format($kpis['riders']) }}" icon="bike" color="info" />
    </div>

    {{-- Charts row --}}
    <div class="grid lg:grid-cols-3 gap-5 mb-6">

        {{-- Revenue trend --}}
        <div class="lg:col-span-2">
            <x-card padding="none">
                <x-card-header title="Revenue Trend" subtitle="Last 14 days" />
                <div class="p-5">
                    @php
                        $maxVal = max(1, collect($revenueTrend)->max('value'));
                        $barWidth = 100 / max(1, count($revenueTrend));
                    @endphp
                    <div class="relative h-48">
                        <svg viewBox="0 0 400 160" preserveAspectRatio="none" class="w-full h-full">
                            {{-- grid lines --}}
                            @for($i = 0; $i <= 4; $i++)
                                <line x1="0" y1="{{ $i * 40 }}" x2="400" y2="{{ $i * 40 }}" stroke="#E2E8F0" stroke-width="0.5"/>
                            @endfor
                            {{-- bars --}}
                            @foreach($revenueTrend as $idx => $point)
                                @php
                                    $h = $maxVal > 0 ? ($point['value'] / $maxVal) * 140 : 0;
                                    $x = $idx * $barWidth + ($barWidth * 0.15);
                                    $w = $barWidth * 0.7;
                                @endphp
                                <rect x="{{ $x }}%" y="{{ 150 - $h }}" width="{{ $w }}%" height="{{ $h }}" fill="#4F46E5" rx="1.5" opacity="{{ $loop->last ? '1' : '0.85' }}"/>
                            @endforeach
                        </svg>
                        <div class="flex justify-between mt-2 text-[10px] text-ink-muted">
                            @foreach($revenueTrend as $p)
                                <span>{{ $p['label'] }}</span>
                            @endforeach
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-between border-t border-[color:var(--color-border)] pt-4">
                        <p class="text-xs text-ink-muted">Total: <span class="font-semibold text-ink">₦{{ number_format(collect($revenueTrend)->sum('value'), 0) }}</span></p>
                        <p class="text-xs text-ink-muted">Peak day: <span class="font-semibold text-ink">₦{{ number_format($maxVal, 0) }}</span></p>
                    </div>
                </div>
            </x-card>
        </div>

        {{-- Status distribution --}}
        <x-card padding="none">
            <x-card-header title="Order Status" subtitle="Distribution" />
            <div class="p-5">
                @php
                    $totalDist = max(1, collect($distribution)->sum('count'));
                @endphp
                <div class="space-y-2.5">
                    @foreach($distribution as $d)
                        @if($d['count'] > 0)
                            <div>
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="text-ink-muted">{{ $d['label'] }}</span>
                                    <span class="font-medium text-ink">{{ $d['count'] }}</span>
                                </div>
                                <div class="h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full" style="width: {{ ($d['count'] / $totalDist) * 100 }}%; background-color: {{ $d['color'] }};"></div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                    @if(collect($distribution)->sum('count') === 0)
                        <p class="text-xs text-ink-muted text-center py-4">No orders yet.</p>
                    @endif
                </div>
            </div>
        </x-card>
    </div>

    {{-- Recent orders + active riders --}}
    <div class="grid lg:grid-cols-3 gap-5">

        {{-- Recent orders --}}
        <div class="lg:col-span-2">
            <x-card padding="none">
                <x-card-header title="Recent Orders" subtitle="Latest activity">
                    <x-slot:actions>
                        <a href="{{ route('admin.orders.index') }}" class="text-xs font-medium text-brand">View all →</a>
                    </x-slot:actions>
                </x-card-header>

                @if($recentOrders->isEmpty())
                    <x-empty-state icon="package" title="No orders yet" description="Orders will appear here." />
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="border-b border-[color:var(--color-border)]">
                                <tr>
                                    <th class="text-left px-5 py-2 text-xs font-medium text-ink-muted uppercase tracking-wider">Tracking</th>
                                    <th class="text-left px-5 py-2 text-xs font-medium text-ink-muted uppercase tracking-wider hidden md:table-cell">Customer</th>
                                    <th class="text-left px-5 py-2 text-xs font-medium text-ink-muted uppercase tracking-wider hidden lg:table-cell">Route</th>
                                    <th class="text-left px-5 py-2 text-xs font-medium text-ink-muted uppercase tracking-wider">Status</th>
                                    <th class="text-right px-5 py-2 text-xs font-medium text-ink-muted uppercase tracking-wider">Fee</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[color:var(--color-border)]">
                                @foreach($recentOrders as $order)
                                    <tr class="hover:bg-slate-50">
                                        <td class="px-5 py-2.5">
                                            <a href="{{ route('admin.orders.show', $order) }}" class="font-mono text-xs text-brand hover:text-brand-hover">
                                                {{ $order->tracking_number }}
                                            </a>
                                        </td>
                                        <td class="px-5 py-2.5 hidden md:table-cell text-ink-muted text-xs">{{ $order->customer?->name ?? '—' }}</td>
                                        <td class="px-5 py-2.5 hidden lg:table-cell text-ink-muted text-xs">{{ $order->pickup_city }} → {{ $order->dropoff_city }}</td>
                                        <td class="px-5 py-2.5"><x-status-badge :status="$order->status" /></td>
                                        <td class="px-5 py-2.5 text-right font-medium text-ink text-xs">₦{{ number_format($order->total_fee, 0) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </x-card>
        </div>

        {{-- Active riders --}}
        <x-card padding="none">
            <x-card-header title="Top Riders Today" subtitle="By deliveries" />
            @if($activeRiders->isEmpty())
                <x-empty-state icon="bike" title="No riders" description="No verified riders yet." />
            @else
                <div class="p-5 space-y-3">
                    @foreach($activeRiders as $rider)
                        <a href="{{ route('admin.riders.show', $rider) }}" class="flex items-center gap-3 group">
                            <span class="w-9 h-9 rounded-full bg-brand-light text-brand flex items-center justify-center text-xs font-semibold shrink-0">
                                {{ collect(explode(' ', $rider->user->name))->take(2)->map(fn($p) => mb_strtoupper(mb_substr($p, 0, 1)))->implode('') }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-ink truncate group-hover:text-brand">{{ $rider->user->name }}</p>
                                <p class="text-xs text-ink-muted">{{ $rider->today_deliveries }} delivery(s) today</p>
                            </div>
                            <span class="text-xs font-medium text-ink-muted">★ {{ number_format((float) $rider->rating, 1) }}</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </x-card>
    </div>

@endsection