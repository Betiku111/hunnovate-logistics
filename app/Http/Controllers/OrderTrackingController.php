<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderTrackingController extends Controller
{
    public function form(): View
    {
        return view('customer.track.form');
    }

    public function show(Request $request, string $trackingNumber): View
    {
        $order = Order::where('tracking_number', $trackingNumber)
            ->with(['statusHistory', 'rider.user'])
            ->first();

        return view('customer.track.show', [
            'order' => $order,
            'trackingNumber' => $trackingNumber,
        ]);
    }
}
