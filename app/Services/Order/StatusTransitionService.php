<?php

namespace App\Services\Order;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Events\OrderStatusChanged;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StatusTransitionService
{
    public function __construct(
        protected TransitionPolicy $policy,
    ) {}

    /**
     * Perform a status transition with full audit.
     *
     * @throws ValidationException when the transition is illegal.
     */
    public function transition(
        Order $order,
        OrderStatus $to,
        ?User $actor = null,
        ?string $note = null,
        ?float $lat = null,
        ?float $lng = null,
        bool $systemOverride = false,
    ): Order {
        $from = $order->status;

        // No-op
        if ($from === $to) {
            return $order;
        }

        // Legal transition check (order-level)
        if (! $from->canTransitionTo($to)) {
            throw ValidationException::withMessages([
                'status' => "Cannot move order from {$from->label()} to {$to->label()}.",
            ]);
        }

        // Actor permission check (skip if system-triggered)
        if (! $systemOverride && $actor !== null) {
            if (! $this->policy->can($actor, $order, $to)) {
                throw ValidationException::withMessages([
                    'status' => "You are not permitted to move this order to {$to->label()}.",
                ]);
            }
        }

        // Reason requirement
        if ($this->policy->requiresReason($to) && blank($note)) {
            throw ValidationException::withMessages([
                'note' => "A reason is required for {$to->label()}.",
            ]);
        }

        return DB::transaction(function () use ($order, $from, $to, $actor, $note, $lat, $lng) {
            // Apply side effects + status in a single update
            $order->update(array_merge(
                ['status' => $to->value],
                $this->policy->sideEffects($to),
            ));

            // Free the rider if terminal
            if ($this->policy->freesRider($to) && $order->rider) {
                $order->rider->update(['is_available' => true]);
            }

            // Audit trail
            OrderStatusHistory::create([
                'order_id'        => $order->id,
                'from_status'     => $from->value,
                'to_status'       => $to->value,
                'changed_by'      => $actor?->id,
                'changed_by_role' => $actor?->role instanceof UserRole
                    ? $actor->role->value
                    : null,
                'notes'           => $note,
                'lat'             => $lat,
                'lng'             => $lng,
                'changed_at'      => now(),
            ]);

            // Increment rider's delivered counter on delivery
            if ($to === OrderStatus::Delivered && $order->rider) {
                $order->rider->increment('total_deliveries');
            }

            event(new OrderStatusChanged($order->fresh(), $from, $to, $actor, $note));

            return $order->fresh();
        });
    }
}