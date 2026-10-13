<?php

namespace App\Http\Controllers\Api;

use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\RiderLocation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RiderLocationController extends Controller
{
    /**
     * POST /api/rider/location
     * Body: { lat, lng, accuracy?, speed?, heading?, order_id? }
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'lat'         => ['required', 'numeric', 'between:-90,90'],
            'lng'         => ['required', 'numeric', 'between:-180,180'],
            'accuracy'    => ['nullable', 'numeric', 'min:0', 'max:10000'],
            'speed'       => ['nullable', 'numeric', 'min:0', 'max:500'],
            'heading'     => ['nullable', 'numeric', 'between:0,360'],
            'order_id'    => ['nullable', 'uuid', 'exists:orders,id'],
        ]);

        $user = $request->user();
        $rider = $user->rider;

        if (! $rider) {
            throw ValidationException::withMessages([
                'rider' => 'Rider profile not found.',
            ]);
        }

        // Validate order ownership if provided
        $orderId = null;
        if (! empty($data['order_id'])) {
            $order = Order::find($data['order_id']);

            if (! $order || $order->rider_id !== $rider->id) {
                throw ValidationException::withMessages([
                    'order_id' => 'Order not assigned to you.',
                ]);
            }

            if (! $this->isTrackable($order)) {
                throw ValidationException::withMessages([
                    'order_id' => 'Order is not in a trackable state.',
                ]);
            }

            $orderId = $order->id;
        }

        DB::transaction(function () use ($rider, $data, $orderId) {
            // Latest rider location (for fast lookup)
            $rider->update([
                'current_lat'          => $data['lat'],
                'current_lng'          => $data['lng'],
                'location_updated_at'  => now(),
            ]);

            // Append to history
            RiderLocation::create([
                'rider_id'    => $rider->id,
                'order_id'    => $orderId,
                'lat'         => $data['lat'],
                'lng'         => $data['lng'],
                'accuracy_m'  => $data['accuracy'] ?? null,
                'speed_kmh'   => $data['speed'] ?? null,
                'heading_deg' => $data['heading'] ?? null,
                'recorded_at' => now(),
            ]);
        });

        return response()->json([
            'status'      => 'ok',
            'recorded_at' => now()->toIso8601String(),
        ]);
    }

    protected function isTrackable(Order $order): bool
    {
        return in_array($order->status, [
            OrderStatus::Assigned,
            OrderStatus::Accepted,
            OrderStatus::RiderArriving,
            OrderStatus::Arrived,
            OrderStatus::PickedUp,
            OrderStatus::InTransit,
        ], true);
    }
}