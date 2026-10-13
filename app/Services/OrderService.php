<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\RiderStatus;
use App\Enums\UserRole;
use App\Enums\VehicleType;
use App\Events\RiderAssigned;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\Rider;
use App\Models\RiderAssignment;
use App\Models\User;
use App\Services\Order\StatusTransitionService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    public function __construct(
        protected OrderNumberService $numbers,
        protected DeliveryFeeService $fees,
        protected StatusTransitionService $transitions,
    ) {}

    /**
     * Create a new order inside a DB transaction.
     * Fee is recalculated server-side from validated input.
     */
    public function create(User $customer, array $data): Order
    {
        return DB::transaction(function () use ($customer, $data) {
            $vehicle = VehicleType::from($data['vehicle_type']);

            $distance = $this->fees->distanceKm(
                $data['pickup_lat'] ?? null,
                $data['pickup_lng'] ?? null,
                $data['dropoff_lat'] ?? null,
                $data['dropoff_lng'] ?? null,
            );

            $fee = $this->fees->calculate(
                distanceKm: $distance,
                weightKg: (float) $data['package_weight_kg'],
                vehicle: $vehicle,
                isExpress: (bool) ($data['is_express'] ?? false),
                isFragile: (bool) ($data['is_fragile'] ?? false),
            );

            $eta = $this->fees->estimateEtaMinutes($distance, $vehicle);

            $order = Order::create([
                'tracking_number' => $this->numbers->generate(),
                'customer_id' => $customer->id,

                'status' => OrderStatus::Created->value,
                'vehicle_type' => $vehicle->value,

                // Pickup
                'pickup_contact_name' => $data['pickup_contact_name'],
                'pickup_contact_phone' => $data['pickup_contact_phone'],
                'pickup_address' => $data['pickup_address'],
                'pickup_city' => $data['pickup_city'],
                'pickup_state' => $data['pickup_state'],
                'pickup_lat' => $data['pickup_lat'] ?? null,
                'pickup_lng' => $data['pickup_lng'] ?? null,

                // Dropoff
                'dropoff_contact_name' => $data['dropoff_contact_name'],
                'dropoff_contact_phone' => $data['dropoff_contact_phone'],
                'dropoff_address' => $data['dropoff_address'],
                'dropoff_city' => $data['dropoff_city'],
                'dropoff_state' => $data['dropoff_state'],
                'dropoff_lat' => $data['dropoff_lat'] ?? null,
                'dropoff_lng' => $data['dropoff_lng'] ?? null,

                // Package
                'package_description' => $data['package_description'] ?? null,
                'package_weight_kg' => $data['package_weight_kg'],
                'package_length_cm' => $data['package_length_cm'] ?? null,
                'package_width_cm' => $data['package_width_cm'] ?? null,
                'package_height_cm' => $data['package_height_cm'] ?? null,
                'is_fragile' => (bool) ($data['is_fragile'] ?? false),
                'special_instructions' => $data['special_instructions'] ?? null,

                // Options
                'is_express' => (bool) ($data['is_express'] ?? false),
                'scheduled_at' => $data['scheduled_at'] ?? null,

                // Fees (authoritative)
                'distance_km' => $fee['distance_km'],
                'base_fee' => $fee['base_fee'],
                'distance_fee' => $fee['distance_fee'],
                'weight_fee' => $fee['weight_fee'],
                'express_fee' => $fee['express_fee'],
                'fragile_fee' => $fee['fragile_fee'],
                'total_fee' => $fee['total_fee'],
                'currency' => $fee['currency'],

                // Payment
                'payment_status' => PaymentStatus::Pending->value,
                'payment_method' => $data['payment_method'] ?? null,

                'eta_minutes' => $eta,
            ]);

            $this->recordStatus(
                order: $order,
                to: OrderStatus::Created,
                from: null,
                actor: $customer,
                note: 'Order created by customer.',
            );

            // Move to waiting_admin immediately
            $this->transitions->transition(
                order: $order,
                to: OrderStatus::WaitingAdmin,
                actor: $customer,
                note: 'Order submitted for processing.',
                systemOverride: true,
            );

            return $order->fresh();
        });
    }

    /**
     * Delegate to the dedicated StatusTransitionService.
     */
    public function transition(
        Order $order,
        OrderStatus $to,
        ?User $actor = null,
        ?string $note = null,
        ?float $lat = null,
        ?float $lng = null,
    ): Order {
        return $this->transitions->transition($order, $to, $actor, $note, $lat, $lng);
    }

    /**
     * Assign a verified rider to an order waiting for admin assignment.
     */
    public function assignRider(Order $order, Rider $rider, User $assignedBy, ?string $note = null): Order
    {
        if ($order->status !== OrderStatus::WaitingAdmin) {
            throw ValidationException::withMessages([
                'rider_id' => "Order must be in 'Waiting for Admin' state to assign a rider.",
            ]);
        }

        if ($order->rider_id !== null) {
            throw ValidationException::withMessages([
                'rider_id' => 'Order already has a rider assigned.',
            ]);
        }

        if ($rider->status !== RiderStatus::Verified) {
            throw ValidationException::withMessages([
                'rider_id' => 'Selected rider is not verified.',
            ]);
        }

        return DB::transaction(function () use ($order, $rider, $assignedBy, $note) {
            $order->update([
                'rider_id' => $rider->id,
                'assigned_by' => $assignedBy->id,
            ]);

            RiderAssignment::create([
                'order_id' => $order->id,
                'rider_id' => $rider->id,
                'assigned_by' => $assignedBy->id,
                'assigned_at' => now(),
                'is_active' => true,
            ]);

            $this->transition(
                order: $order,
                to: OrderStatus::Assigned,
                actor: $assignedBy,
                note: $note ?? "Rider {$rider->user->name} assigned by admin.",
            );

            $rider->update(['is_available' => false]);

            event(new RiderAssigned($order, $rider, $assignedBy, false));

            return $order->fresh();
        });
    }

    /**
     * Reassign an order before the current rider accepts it.
     */
    public function reassignRider(Order $order, Rider $newRider, User $assignedBy, ?string $note = null): Order
    {
        if ($order->status !== OrderStatus::Assigned) {
            throw ValidationException::withMessages([
                'rider_id' => 'Order can only be reassigned while in "Rider Assigned" state.',
            ]);
        }

        if ($order->rider_id === $newRider->id) {
            throw ValidationException::withMessages([
                'rider_id' => 'Order is already assigned to this rider.',
            ]);
        }

        if ($newRider->status !== RiderStatus::Verified) {
            throw ValidationException::withMessages([
                'rider_id' => 'Selected rider is not verified.',
            ]);
        }

        return DB::transaction(function () use ($order, $newRider, $assignedBy, $note) {
            $oldRider = $order->rider;

            RiderAssignment::where('order_id', $order->id)
                ->where('is_active', true)
                ->update(['is_active' => false]);

            if ($oldRider) {
                $oldRider->update(['is_available' => true]);
            }

            $order->update([
                'rider_id' => $newRider->id,
                'assigned_by' => $assignedBy->id,
            ]);

            RiderAssignment::create([
                'order_id' => $order->id,
                'rider_id' => $newRider->id,
                'assigned_by' => $assignedBy->id,
                'assigned_at' => now(),
                'is_active' => true,
            ]);

            $this->recordStatus(
                order: $order,
                to: OrderStatus::Assigned,
                from: OrderStatus::Assigned,
                actor: $assignedBy,
                note: $note ?? "Reassigned to {$newRider->user->name} (previously {$oldRider?->user->name}).",
            );

            $newRider->update(['is_available' => false]);

            event(new RiderAssigned($order, $newRider, $assignedBy, true));

            return $order->fresh();
        });
    }

    /**
     * Append a status history row.
     */
    protected function recordStatus(
        Order $order,
        OrderStatus $to,
        ?OrderStatus $from,
        ?User $actor,
        ?string $note = null,
        ?float $lat = null,
        ?float $lng = null,
    ): void {
        OrderStatusHistory::create([
            'order_id' => $order->id,
            'from_status' => $from?->value,
            'to_status' => $to->value,
            'changed_by' => $actor?->id,
            'changed_by_role' => $actor?->role instanceof UserRole
                ? $actor->role->value
                : null,
            'notes' => $note,
            'lat' => $lat,
            'lng' => $lng,
            'changed_at' => now(),
        ]);
    }
}
