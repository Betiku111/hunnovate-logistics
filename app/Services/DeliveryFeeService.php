<?php

namespace App\Services;

use App\Enums\VehicleType;

class DeliveryFeeService
{
    /**
     * Base fees per vehicle type (NGN).
     */
    private const BASE_FEE = [
        VehicleType::Motorcycle->value => 500,
        VehicleType::Car->value => 1000,
        VehicleType::Truck->value => 3000,
    ];

    /**
     * Per-km fee per vehicle type.
     */
    private const PER_KM = [
        VehicleType::Motorcycle->value => 100,
        VehicleType::Car->value => 150,
        VehicleType::Truck->value => 250,
    ];

    /**
     * Weight surcharge threshold and rate per kg over threshold.
     */
    private const WEIGHT_FREE_KG = [
        VehicleType::Motorcycle->value => 5,
        VehicleType::Car->value => 20,
        VehicleType::Truck->value => 200,
    ];

    private const WEIGHT_PER_KG = [
        VehicleType::Motorcycle->value => 20,
        VehicleType::Car->value => 15,
        VehicleType::Truck->value => 10,
    ];

    /**
     * Express surcharge as multiplier of base fee.
     */
    private const EXPRESS_MULTIPLIER = 0.5;

    /**
     * Fragile handling flat fee.
     */
    private const FRAGILE_FEE = 500;

    /**
     * Currency (all fees in this currency).
     */
    private const CURRENCY = 'NGN';

    /**
     * Calculate the full fee breakdown.
     *
     * @return array{
     *     currency: string,
     *     distance_km: float,
     *     base_fee: float,
     *     distance_fee: float,
     *     weight_fee: float,
     *     express_fee: float,
     *     fragile_fee: float,
     *     total_fee: float,
     *     breakdown: array<string, array{label: string, amount: float}>
     * }
     */
    public function calculate(
        float $distanceKm,
        float $weightKg,
        VehicleType $vehicle,
        bool $isExpress = false,
        bool $isFragile = false
    ): array {
        $baseFee = (float) self::BASE_FEE[$vehicle->value];
        $perKm = (float) self::PER_KM[$vehicle->value];
        $freeKg = (float) self::WEIGHT_FREE_KG[$vehicle->value];
        $perKg = (float) self::WEIGHT_PER_KG[$vehicle->value];

        $distanceFee = round($distanceKm * $perKm, 2);

        $billableKg = max(0, $weightKg - $freeKg);
        $weightFee = round($billableKg * $perKg, 2);

        $expressFee = $isExpress ? round($baseFee * self::EXPRESS_MULTIPLIER, 2) : 0.0;
        $fragileFee = $isFragile ? (float) self::FRAGILE_FEE : 0.0;

        $total = $baseFee + $distanceFee + $weightFee + $expressFee + $fragileFee;

        return [
            'currency' => self::CURRENCY,
            'distance_km' => round($distanceKm, 2),
            'base_fee' => $baseFee,
            'distance_fee' => $distanceFee,
            'weight_fee' => $weightFee,
            'express_fee' => $expressFee,
            'fragile_fee' => $fragileFee,
            'total_fee' => round($total, 2),
            'breakdown' => [
                'base' => ['label' => 'Base fee',        'amount' => $baseFee],
                'distance' => ['label' => 'Distance charge', 'amount' => $distanceFee],
                'weight' => ['label' => 'Weight surcharge', 'amount' => $weightFee],
                'express' => ['label' => 'Express delivery', 'amount' => $expressFee],
                'fragile' => ['label' => 'Fragile handling', 'amount' => $fragileFee],
            ],
        ];
    }

    /**
     * Haversine distance between two coordinates in km.
     */
    public function distanceKm(
        ?float $fromLat,
        ?float $fromLng,
        ?float $toLat,
        ?float $toLng
    ): float {
        if ($fromLat === null || $fromLng === null || $toLat === null || $toLng === null) {
            // Fallback: assume 5 km minimum for cities when coords unavailable
            return 5.0;
        }

        $earthRadius = 6371;

        $latFrom = deg2rad($fromLat);
        $lngFrom = deg2rad($fromLng);
        $latTo = deg2rad($toLat);
        $lngTo = deg2rad($toLng);

        $dLat = $latTo - $latFrom;
        $dLng = $lngTo - $lngFrom;

        $a = sin($dLat / 2) ** 2
           + cos($latFrom) * cos($latTo) * sin($dLng / 2) ** 2;

        $c = 2 * asin(sqrt($a));

        return round($earthRadius * $c, 2);
    }

    /**
     * Recommend a vehicle type based on weight.
     */
    public function recommendVehicle(float $weightKg): VehicleType
    {
        if ($weightKg <= VehicleType::Motorcycle->maxWeightKg()) {
            return VehicleType::Motorcycle;
        }
        if ($weightKg <= VehicleType::Car->maxWeightKg()) {
            return VehicleType::Car;
        }

        return VehicleType::Truck;
    }

    /**
     * Recommended ETA in minutes, based on distance and vehicle.
     * A rough heuristic — refined in STEP 13 with real routing.
     */
    public function estimateEtaMinutes(float $distanceKm, VehicleType $vehicle): int
    {
        $avgSpeed = match ($vehicle) {
            VehicleType::Motorcycle => 35.0,
            VehicleType::Car => 30.0,
            VehicleType::Truck => 25.0,
        };

        // Add 10 minutes for pickup/handoff overhead
        return (int) ceil(($distanceKm / $avgSpeed) * 60) + 10;
    }
}
