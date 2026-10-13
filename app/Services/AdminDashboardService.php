<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\RiderStatus;
use App\Models\Order;
use App\Models\Rider;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AdminDashboardService
{
    /**
     * Top-level KPI cards.
     */
    public function kpis(): array
    {
        $totalOrders     = Order::count();
        $pendingOrders   = Order::where('status', OrderStatus::WaitingAdmin->value)->count();
        $activeDelivery  = Order::whereNotIn('status', [OrderStatus::Delivered->value, OrderStatus::Cancelled->value])->count();
        $delivered       = Order::where('status', OrderStatus::Delivered->value)->count();
        $customers       = User::where('role', 'customer')->count();
        $riders          = Rider::where('status', RiderStatus::Verified->value)->count();
        $revenue         = (float) Order::where('status', OrderStatus::Delivered->value)->sum('total_fee');

        return compact('totalOrders', 'pendingOrders', 'activeDelivery', 'delivered', 'customers', 'riders', 'revenue');
    }

    /**
     * Order status distribution for the doughnut chart.
     */
    public function statusDistribution(): array
    {
        $rows = Order::query()
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $out = [];
        foreach (OrderStatus::cases() as $status) {
            $out[] = [
                'key'   => $status->value,
                'label' => $status->label(),
                'color' => $this->statusHex($status),
                'count' => (int) ($rows[$status->value] ?? 0),
            ];
        }

        return $out;
    }

    /**
     * Revenue trend for the last N days.
     */
    public function revenueTrend(int $days = 14): array
    {
        $start = now()->subDays($days - 1)->startOfDay();

        $rows = Order::query()
            ->where('status', OrderStatus::Delivered->value)
            ->where('delivered_at', '>=', $start)
            ->select(
                DB::raw('DATE(delivered_at) as day'),
                DB::raw('SUM(total_fee) as total')
            )
            ->groupBy('day')
            ->pluck('total', 'day')
            ->toArray();

        $out = [];
        for ($i = 0; $i < $days; $i++) {
            $day = $start->copy()->addDays($i);
            $key = $day->toDateString();
            $out[] = [
                'date'  => $key,
                'label' => $day->format('M j'),
                'value' => (float) ($rows[$key] ?? 0),
            ];
        }

        return $out;
    }

    /**
     * Latest orders for the dashboard table.
     */
    public function recentOrders(int $limit = 8)
    {
        return Order::with(['customer', 'rider.user'])
            ->latest()
            ->limit($limit)
            ->get();
    }

    /**
     * Top active riders by today's deliveries.
     */
    public function activeRiders(int $limit = 5)
    {
        return Rider::query()
            ->where('status', RiderStatus::Verified->value)
            ->with('user')
            ->withCount([
                'orders as today_deliveries' => function ($q) {
                    $q->where('status', OrderStatus::Delivered->value)
                      ->whereDate('delivered_at', today());
                },
            ])
            ->orderByDesc('today_deliveries')
            ->limit($limit)
            ->get();
    }

    protected function statusHex(OrderStatus $status): string
    {
        return match ($status->color()) {
            'success' => '#16A34A',
            'warning' => '#F59E0B',
            'danger'  => '#DC2626',
            'info'    => '#2563EB',
            default   => '#64748B',
        };
    }
}