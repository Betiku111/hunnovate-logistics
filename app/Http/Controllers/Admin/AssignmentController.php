<?php

namespace App\Http\Controllers\Admin;

use App\Enums\OrderStatus;
use App\Enums\RiderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Rider;
use App\Services\OrderService;
use App\Services\RiderMatchingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AssignmentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::query()
            ->where('status', OrderStatus::WaitingAdmin->value)
            ->with(['customer'])
            ->oldest(); // oldest first — FIFO queue

        if ($search = trim((string) $request->input('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('tracking_number', 'like', "%{$search}%")
                  ->orWhere('pickup_city', 'like', "%{$search}%")
                  ->orWhere('dropoff_city', 'like', "%{$search}%");
            });
        }

        if ($vehicle = $request->input('vehicle')) {
            if (\App\Enums\VehicleType::tryFrom($vehicle)) {
                $query->where('vehicle_type', $vehicle);
            }
        }

        $orders = $query->paginate(20)->withQueryString();

        return view('admin.assignments.index', [
            'orders' => $orders,
            'search' => $search ?? '',
            'vehicle' => $vehicle,
            'pendingCount' => Order::where('status', OrderStatus::WaitingAdmin->value)->count(),
        ]);
    }

    public function show(Order $order, RiderMatchingService $matcher): View
    {
        if ($order->status !== OrderStatus::WaitingAdmin) {
            return redirect()
                ->route('admin.assignments.index')
                ->with('error', 'This order is no longer available for assignment.');
        }

        $order->load(['customer']);

        $recommendations = $matcher->recommendFor($order, 10);

        // Also fetch all verified riders (for manual override)
        $allRiders = Rider::query()
            ->where('status', RiderStatus::Verified->value)
            ->with(['user', 'vehicles'])
            ->orderByDesc('rating')
            ->limit(30)
            ->get();

        return view('admin.assignments.show', [
            'order'           => $order,
            'recommendations' => $recommendations,
            'allRiders'       => $allRiders,
        ]);
    }

    public function assign(Request $request, Order $order, OrderService $service): RedirectResponse
    {
        $data = $request->validate([
            'rider_id' => ['required', 'exists:riders,id'],
            'note'     => ['nullable', 'string', 'max:500'],
        ]);

        $rider = Rider::findOrFail($data['rider_id']);

        try {
            $service->assignRider($order, $rider, $request->user(), $data['note'] ?? null);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return redirect()
            ->route('admin.orders.show', $order)
            ->with('success', "Rider {$rider->user->name} assigned to order {$order->tracking_number}.");
    }

    public function reassign(Request $request, Order $order, OrderService $service): RedirectResponse
    {
        $data = $request->validate([
            'rider_id' => ['required', 'exists:riders,id'],
            'note'     => ['nullable', 'string', 'max:500'],
        ]);

        $rider = Rider::findOrFail($data['rider_id']);

        try {
            $service->reassignRider($order, $rider, $request->user(), $data['note'] ?? null);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return back()->with('success', "Order reassigned to {$rider->user->name}.");
    }
}