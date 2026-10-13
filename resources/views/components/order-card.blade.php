@props(['order'])

<a href="{{ route('customer.orders.show', $order) }}"
   class="block bg-white border border-[color:var(--color-border)] rounded-[var(--radius-lg)] shadow-[var(--shadow-card)] hover:shadow-[var(--shadow-hover)] transition-shadow p-5">
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0">
            <p class="text-xs font-medium text-ink-muted tracking-wide">#{{ $order->tracking_number }}</p>
            <p class="mt-1 text-sm font-semibold text-ink truncate">
                {{ $order->pickup_city }} → {{ $order->dropoff_city }}
            </p>
            <p class="text-xs text-ink-muted truncate mt-0.5">
                {{ $order->package_description ?: 'Package' }}
            </p>
        </div>
        <x-status-badge :status="$order->status" />
    </div>

    <div class="flex items-center justify-between mt-4 pt-4 border-t border-[color:var(--color-border)]">
        <div class="flex items-center gap-2 text-xs text-ink-muted">
            <x-icon :name="$order->vehicle_type->value" :size="14" />
            <span>{{ $order->vehicle_type->label() }}</span>
        </div>
        <div class="text-sm font-semibold text-ink">
            {{ number_format($order->total_fee, 2) }} <span class="text-xs text-ink-muted">{{ $order->currency }}</span>
        </div>
    </div>
</a>