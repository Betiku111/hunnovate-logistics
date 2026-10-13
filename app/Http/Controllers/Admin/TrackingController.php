<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Rider;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TrackingController extends Controller
{
    public function index(Request $request): View
    {
        $activeOrders = Order::query()
            ->whereNotIn('status', [OrderStatus::Delivered->value, OrderStatus::Cancelled->value])
            ->with(['rider.user', 'customer'])
            ->orderByRaw("CASE WHEN status = 'in_transit' THEN 0 WHEN status = 'picked_up' THEN 1 ELSE 2 END")
            ->latest()
            ->limit(50)
            ->get();

        $onlineRiders = Rider::query()
            ->where('status', 'verified')
            ->where('is_online', true)
            ->with('user')
            ->get();

        return view('admin.tracking.index', [
            'activeOrders' => $activeOrders,
            'onlineRiders' => $onlineRiders,
        ]);
    }
}