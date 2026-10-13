<?php

namespace App\Http\Controllers\Rider;

use App\Enums\OrderStatus;
use App\Events\RiderAssignmentAccepted;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\RiderAssignment;
use App\Services\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class JobController extends Controller
{
    public function index(Request $request): View
    {
        $rider = $request->user()->rider;
        $vehicleType = $rider->vehicles()->value('type');

        $jobs = Order::whereNull('rider_id')
            ->where('status', OrderStatus::WaitingAdmin->value)
            ->where('vehicle_type', $vehicleType)
            ->with(['customer'])
            ->latest()
            ->paginate(15);

        return view('rider.jobs.index', [
            'jobs' => $jobs,
        ]);
    }

    public function show(Request $request, Order $order): View
    {
        $this->ensureJobIsVisible($request, $order);

        return view('rider.jobs.show', [
            'order' => $order->load(['customer', 'statusHistory']),
        ]);
    }

    public function accept(Request $request, Order $order, OrderService $service): RedirectResponse
    {
        $this->ensureJobIsVisible($request, $order);

        $rider = $request->user()->rider;

        DB::transaction(function () use ($order, $rider, $request, $service) {
            $order->update([
                'rider_id' => $rider->id,
                'assigned_by' => null,
            ]);

            RiderAssignment::create([
                'order_id' => $order->id,
                'rider_id' => $rider->id,
                'assigned_by' => null,
                'assigned_at' => now(),
                'accepted_at' => now(),
                'is_active' => true,
            ]);

            $service->transition($order, OrderStatus::Assigned, $request->user(), 'Rider self-assigned.');
            $service->transition($order, OrderStatus::Accepted, $request->user(), 'Rider accepted the job.');

            $rider->update(['is_available' => false]);

            event(new RiderAssignmentAccepted($order->fresh(), $rider));
        });

        return redirect()
            ->route('rider.deliveries.show', $order)
            ->with('success', 'Job accepted. Head to the pickup location.');
    }

    public function reject(Request $request, Order $order): RedirectResponse
    {
        $this->ensureJobIsVisible($request, $order);

        $rider = $request->user()->rider;

        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:255'],
        ]);

        // Record the rejection (informational)
        RiderAssignment::create([
            'order_id' => $order->id,
            'rider_id' => $rider->id,
            'assigned_by' => null,
            'assigned_at' => now(),
            'rejected_at' => now(),
            'rejection_reason' => $data['reason'] ?? null,
            'is_active' => false,
        ]);

        return redirect()
            ->route('rider.jobs.index')
            ->with('success', 'Job skipped.');
    }

    protected function ensureJobIsVisible(Request $request, Order $order): void
    {
        $rider = $request->user()->rider;
        $vehicleType = $rider->vehicles()->value('type');

        if ($order->status !== OrderStatus::WaitingAdmin
            || $order->rider_id !== null
            || $order->vehicle_type !== $vehicleType) {
            throw ValidationException::withMessages([
                'order' => 'This job is no longer available.',
            ]);
        }
    }
}
