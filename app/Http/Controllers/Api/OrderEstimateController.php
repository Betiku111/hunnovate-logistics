<?php

namespace App\Http\Controllers\Api;

use App\Enums\VehicleType;
use App\Http\Controllers\Controller;
use App\Http\Requests\EstimateFeeRequest;
use App\Services\DeliveryFeeService;
use Illuminate\Http\JsonResponse;

class OrderEstimateController extends Controller
{
    public function fee(EstimateFeeRequest $request, DeliveryFeeService $service): JsonResponse
    {
        $data = $request->validated();
        $vehicle = VehicleType::from($data['vehicle_type']);

        $distance = $service->distanceKm(
            $data['pickup_lat'] ?? null,
            $data['pickup_lng'] ?? null,
            $data['dropoff_lat'] ?? null,
            $data['dropoff_lng'] ?? null,
        );

        $fee = $service->calculate(
            distanceKm: $distance,
            weightKg: (float) $data['package_weight_kg'],
            vehicle: $vehicle,
            isExpress: (bool) ($data['is_express'] ?? false),
            isFragile: (bool) ($data['is_fragile'] ?? false),
        );

        $fee['eta_minutes'] = $service->estimateEtaMinutes($distance, $vehicle);
        $fee['recommended_vehicle'] = $service->recommendVehicle((float) $data['package_weight_kg'])->value;

        return response()->json($fee);
    }
}
