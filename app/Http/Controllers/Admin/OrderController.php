<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Order\StatusTransitionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::query()->with(['customer', 'rider.user'])->latest();

        if ($status = $request->input('status')) {
            if (OrderStatus::tryFrom($status)) {
                $query->where('status', $status);
            }
        }

        if ($search = trim((string) $request->input('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('tracking_number', 'like', "%{$search}%")
                  ->orWhere('pickup_city', 'like', "%{$search}%")
                  ->orWhere('dropoff_city', 'like', "%{$search}%");
            });
        }

        return view('admin.orders.index', [
            'orders' => $query->paginate(20)->withQueryString(),
            'search' => $search ?? '',
            'status' => $status,
        ]);
    }

    public function show(Order $order): View
    {
        $order->load([
            'customer',
            'rider.user',
            'rider.vehicles',
            'statusHistory.changedBy',
            'proofOfDelivery',
            'payment',
            'review',
            'assignments.rider.user',
        ]);

        return view('admin.orders.show', [
            'order' => $order,
        ]);
    }

    public function cancel(Request $request, Order $order, StatusTransitionService $transitions): RedirectResponse
    {
        $data = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ]);

        try {
            $transitions->transition(
                order: $order,
                to: OrderStatus::Cancelled,
                actor: $request->user(),
                note: 'Admin cancelled: ' . $data['reason'],
            );
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return back()->with('success', 'Order cancelled.');
    }
}