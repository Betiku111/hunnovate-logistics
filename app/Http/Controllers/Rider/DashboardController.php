<?php

namespace App\Http\Controllers\Rider;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Earning;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $rider = $request->user()->rider;

        $activeDelivery = Order::where('rider_id', $rider->id)
            ->whereNotIn('status', [OrderStatus::Delivered->value, OrderStatus::Cancelled->value])
            ->with(['customer', 'statusHistory'])
            ->latest()
            ->first();

        $stats = [
            'today_deliveries' => Order::where('rider_id', $rider->id)
                ->where('status', OrderStatus::Delivered->value)
                ->whereDate('delivered_at', today())
                ->count(),

            'today_earnings' => (float) Earning::where('rider_id', $rider->id)
                ->whereDate('earned_at', today())
                ->sum('net_amount'),

            'total_deliveries' => $rider->total_deliveries,

            'rating' => (float) $rider->rating,
        ];

        $availableJobsCount = Order::whereNull('rider_id')
            ->where('status', OrderStatus::WaitingAdmin->value)
            ->where('vehicle_type', $rider->vehicles()->value('type'))
            ->count();

        return view('rider.dashboard', [
            'rider'              => $rider,
            'activeDelivery'     => $activeDelivery,
            'stats'              => $stats,
            'availableJobsCount' => $availableJobsCount,
        ]);
    }
}