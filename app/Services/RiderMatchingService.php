<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\RiderStatus;
use App\Models\Order;
use App\Models\Rider;
use Illuminate\Support\Collection;

class RiderMatchingService
{
    public function __construct(
        protected DeliveryFeeService $fees,
    ) {}

    /**
     * Return a scored list of riders suitable for the given order.
     *
     * @return Collection<int, array{
     *     rider: Rider,
     *     score: float,
     *     distance_km: ?float,
     *     breakdown: array<string, float>,
     *     is_recommended: bool,
     *     reasons: array<int, string>
     * }>
     */
    public function recommendFor(Order $order, int $limit = 10): Collection
    {
        $riders = Rider::query()
            ->where('status', RiderStatus::Verified->value)
            ->where('is_online', true)
            ->where('is_available', true)
            ->whereHas('vehicles', function ($q) use ($order) {
                $q->where('type', $order->vehicle_type->value)
                  ->where('is_active', true);
            })
            // Exclude riders currently on an active order
            ->whereDoesntHave('orders', function ($q) {
                $q->whereNotIn('status', [OrderStatus::Delivered->value, OrderStatus::Cancelled->value]);
            })
            ->with(['user', 'vehicles'])
            ->get();

        $scored = $riders->map(function (Rider $rider) use ($order) {
            return $this->score($rider, $order);
        })->sortByDesc('score')->values();

        // Mark the top 3 as recommended (if any)
        $scored = $scored->map(function ($item, $idx) {
            $item['is_recommended'] = $idx < 3;
            return $item;
        });

        return $scored->take($limit);
    }

    /**
     * Score a rider for an order. Higher = better match.
     */
    public function score(Rider $rider, Order $order): array
    {
        $reasons = [];

        // Distance from pickup (if both have coords)
        $distanceKm = null;
        if ($order->pickup_lat && $order->pickup_lng && $rider->current_lat && $rider->current_lng) {
            $distanceKm = $this->fees->distanceKm(
                (float) $rider->current_lat,
                (float) $rider->current_lng,
                (float) $order->pickup_lat,
                (float) $order->pickup_lng,
            );
        }

        // Sub-scores (0–100 scale, then weighted)
        $distanceScore = $this->distanceScore($distanceKm);
        $ratingScore   = min(100, ((float) $rider->rating / 5.0) * 100);
        $experienceScore = min(100, ($rider->total_deliveries / 100) * 100);

        $breakdown = [
            'distance'   => round($distanceScore * 0.5, 2),   // 50% weight
            'rating'     => round($ratingScore * 0.3, 2),     // 30% weight
            'experience' => round($experienceScore * 0.2, 2), // 20% weight
        ];

        $total = array_sum($breakdown);

        // Human-readable reasons
        if ($distanceKm !== null) {
            $reasons[] = number_format($distanceKm, 1) . ' km away';
        } else {
            $reasons[] = 'Location unknown';
        }
        if ((float) $rider->rating >= 4.5) {
            $reasons[] = 'Top rated';
        }
        if ($rider->total_deliveries >= 50) {
            $reasons[] = 'Experienced';
        }

        return [
            'rider'          => $rider,
            'score'          => round($total, 2),
            'distance_km'    => $distanceKm,
            'breakdown'      => $breakdown,
            'is_recommended' => false, // set later
            'reasons'        => $reasons,
        ];
    }

    /**
     * Distance → score. 0 km = 100, 20+ km = 0.
     */
    protected function distanceScore(?float $distanceKm): float
    {
        if ($distanceKm === null) {
            return 40.0; // neutral when unknown
        }
        if ($distanceKm <= 0) return 100.0;
        if ($distanceKm >= 20) return 0.0;

        return round(100 - (($distanceKm / 20) * 100), 2);
    }
}