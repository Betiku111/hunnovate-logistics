<?php

namespace App\Http\Controllers\Rider;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

use App\Services\Order\StatusTransitionService;
use Illuminate\Validation\ValidationException;

class DeliveryController extends Controller
{
    public function index(Request $request): View
    {
        $rider = $request->user()->rider;

        $active = Order::where('rider_id', $rider->id)
            ->whereNotIn('status', [OrderStatus::Delivered->value, OrderStatus::Cancelled->value])
            ->with(['customer'])
            ->latest()
            ->get();

        $completed = Order::where('rider_id', $rider->id)
            ->whereIn('status', [OrderStatus::Delivered->value, OrderStatus::Cancelled->value])
            ->with(['customer'])
            ->latest()
            ->limit(20)
            ->get();

        return view('rider.deliveries.index', [
            'active'    => $active,
            'completed' => $completed,
        ]);
    }

    public function show(Request $request, Order $order): View
    {
        $this->authorizeDelivery($request, $order);

        return view('rider.deliveries.show', [
            'order'      => $order->load(['customer', 'statusHistory', 'proofOfDelivery']),
            'nextStatus' => $this->nextStatus($order),
        ]);
    }

    public function advance(Request $request, Order $order, OrderService $service): RedirectResponse
    {
        $this->authorizeDelivery($request, $order);

        $next = $this->nextStatus($order);

        if (! $next) {
            return back()->with('error', 'No further action available for this delivery.');
        }

        $data = $request->validate([
            'note' => ['nullable', 'string', 'max:255'],
            'lat'  => ['nullable', 'numeric', 'between:-90,90'],
            'lng'  => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $service->transition(
            order: $order,
            to: $next,
            actor: $request->user(),
            note: $data['note'] ?? null,
            lat: isset($data['lat']) ? (float) $data['lat'] : null,
            lng: isset($data['lng']) ? (float) $data['lng'] : null,
        );

        // Side effects
        if ($next === OrderStatus::Delivered) {
            $rider = $request->user()->rider;
            $rider->increment('total_deliveries');
            $rider->update(['is_available' => true]);
        }

        return back()->with('success', 'Status updated: ' . $next->label());
    }

    /**
     * The next legal status in the rider-driven workflow.
     * Returns null if the order is in a terminal or admin-controlled state.
     */
    protected function nextStatus(Order $order): ?OrderStatus
    {
        return match ($order->status) {
            OrderStatus::Assigned      => OrderStatus::Accepted,
            OrderStatus::Accepted      => OrderStatus::RiderArriving,
            OrderStatus::RiderArriving => OrderStatus::Arrived,
            OrderStatus::Arrived       => OrderStatus::PickedUp,
            OrderStatus::PickedUp      => OrderStatus::InTransit,
            OrderStatus::InTransit     => OrderStatus::Delivered,
            default                    => null,
        };
    }

    protected function authorizeDelivery(Request $request, Order $order): void
    {
        if ($order->rider_id !== $request->user()->rider?->id) {
            abort(403, 'This delivery is not assigned to you.');
        }
    }



public function cancel(Request $request, Order $order, StatusTransitionService $transitions): RedirectResponse
{
    $this->authorizeDelivery($request, $order);

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

    // Free the rider explicitly (policy does this, but be explicit)
    $request->user()->rider->update(['is_available' => true]);

    return redirect()
        ->route('rider.deliveries.index')
        ->with('success', 'Delivery cancelled.');
}
}