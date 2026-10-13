<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    /**
     * Return tracking data for an order.
     * Public for tracking number lookup. Authenticated users can access via order ID.
     */
    public function show(Request $request, Order $order): JsonResponse
    {
        // Authorization: customer owns it, rider assigned to it, or admin
        $user = $request->user();
        if ($user) {
            $canView = $user->isAdmin()
                || $order->customer_id === $user->id
                || $order->rider?->user_id === $user->id;

            if (! $canView) {
                abort(403);
            }
        }

        $order->load(['rider.user']);

        $rider = $order->rider;

        return response()->json([
            'order' => [
                'id'              => $order->id,
                'tracking_number' => $order->tracking_number,
                'status'          => $order->status->value,
                'status_label'    => $order->status->label(),
                'status_color'    => $order->status->color(),
                'eta_minutes'     => $order->eta_minutes,
                'distance_km'     => (float) ($order->distance_km ?? 0),
                'pickup'          => [
                    'lat'     => $order->pickup_lat ? (float) $order->pickup_lat : null,
                    'lng'     => $order->pickup_lng ? (float) $order->pickup_lng : null,
                    'address' => $order->pickup_address,
                    'city'    => $order->pickup_city,
                ],
                'dropoff' => [
                    'lat'     => $order->dropoff_lat ? (float) $order->dropoff_lat : null,
                    'lng'     => $order->dropoff_lng ? (float) $order->dropoff_lng : null,
                    'address' => $order->dropoff_address,
                    'city'    => $order->dropoff_city,
                ],
            ],
            'rider' => $rider ? [
                'id'                  => $rider->id,
                'name'                => $rider->user->name,
                'avatar_url'          => $rider->profile_photo_path
                    ? \Storage::url($rider->profile_photo_path)
                    : null,
                'rating'              => (float) $rider->rating,
                'lat'                 => $rider->current_lat ? (float) $rider->current_lat : null,
                'lng'                 => $rider->current_lng ? (float) $rider->current_lng : null,
                'location_updated_at' => $rider->location_updated_at?->toIso8601String(),
            ] : null,
            'server_time' => now()->toIso8601String(),
        ]);
    }
}