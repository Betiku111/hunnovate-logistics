<?php

namespace App\Http\Controllers\Customer;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\Order\StatusTransitionService;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $customer = $request->user();

        $query = Order::query()
            ->where('customer_id', $customer->id)
            ->with(['rider.user'])
            ->latest();

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

        return view('customer.orders.index', [
            'orders'       => $query->paginate(15)->withQueryString(),
            'activeStatus' => $status,
            'search'       => $search ?? '',
        ]);
    }

    public function create(Request $request): View
    {
        return view('customer.orders.create', [
            'user' => $request->user(),
        ]);
    }

    public function store(StoreOrderRequest $request, OrderService $service): RedirectResponse
    {
        $order = $service->create($request->user(), $request->validated());

        return redirect()
            ->route('customer.orders.confirmation', $order)
            ->with('success', 'Your order has been created successfully.');
    }

    public function confirmation(Request $request, Order $order): View
    {
        $this->authorize('view', $order);

        return view('customer.orders.confirmation', [
            'order' => $order->load(['statusHistory', 'rider.user']),
        ]);
    }

    public function show(Request $request, Order $order): View
    {
        $this->authorize('view', $order);

        $order->load([
            'rider.user',
            'rider.vehicles',
            'statusHistory.changedBy',
            'proofOfDelivery',
            'payment',
            'review',
        ]);

        return view('customer.orders.show', [
            'order' => $order,
        ]);
    }

    public function history(Request $request): View
    {
        $orders = Order::where('customer_id', $request->user()->id)
            ->whereIn('status', [OrderStatus::Delivered->value, OrderStatus::Cancelled->value])
            ->with(['rider.user'])
            ->latest()
            ->paginate(15);

        return view('customer.orders.history', [
            'orders' => $orders,
        ]);
    }

public function cancel(Request $request, Order $order, StatusTransitionService $transitions): RedirectResponse
{
    $this->authorize('view', $order);

    $data = $request->validate([
        'reason' => ['required', 'string', 'max:500'],
    ]);

    try {
        $transitions->transition(
            order: $order,
            to: OrderStatus::Cancelled,
            actor: $request->user(),
            note: $data['reason'],
        );
    } catch (ValidationException $e) {
        return back()->withErrors($e->errors());
    }

    return redirect()
        ->route('customer.orders.show', $order)
        ->with('success', 'Order cancelled.');
}

}