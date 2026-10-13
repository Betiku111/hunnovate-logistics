<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;

class CustomerDashboardService
{
    public function metrics(User $customer): array
    {
        $base = Order::where('customer_id', $customer->id);

        return [
            'total' => (clone $base)->count(),
            'active' => (clone $base)->whereNotIn('status', [
                OrderStatus::Delivered->value,
                OrderStatus::Cancelled->value,
            ])->count(),
            'delivered' => (clone $base)->where('status', OrderStatus::Delivered->value)->count(),
            'spent' => (float) (clone $base)->where('status', OrderStatus::Delivered->value)->sum('total_fee'),
        ];
    }

    public function recentOrders(User $customer, int $limit = 5)
    {
        return Order::where('customer_id', $customer->id)
            ->with(['rider.user'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function activeOrders(User $customer, int $limit = 3)
    {
        return Order::where('customer_id', $customer->id)
            ->whereNotIn('status', [
                OrderStatus::Delivered->value,
                OrderStatus::Cancelled->value,
            ])
            ->with(['rider.user'])
            ->latest()
            ->limit($limit)
            ->get();
    }
}
