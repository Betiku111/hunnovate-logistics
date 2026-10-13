<?php

namespace App\Services\Order;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Models\Order;
use App\Models\User;

class TransitionPolicy
{
    /**
     * Role → list of allowed (from → to) transitions.
     */
    private const CUSTOMER_ALLOWED = [
        [OrderStatus::Created, OrderStatus::Cancelled],
        [OrderStatus::WaitingAdmin, OrderStatus::Cancelled],
    ];

    private const RIDER_ALLOWED = [
        [OrderStatus::Assigned, OrderStatus::Accepted],
        [OrderStatus::Assigned, OrderStatus::Cancelled],     // rider declines
        [OrderStatus::Accepted, OrderStatus::RiderArriving],
        [OrderStatus::Accepted, OrderStatus::Cancelled],     // rider abandons
        [OrderStatus::RiderArriving, OrderStatus::Arrived],
        [OrderStatus::RiderArriving, OrderStatus::Cancelled],
        [OrderStatus::Arrived, OrderStatus::PickedUp],
        [OrderStatus::PickedUp, OrderStatus::InTransit],
        [OrderStatus::InTransit, OrderStatus::Delivered],
    ];

    /**
     * Admin can trigger any transition that the order's state allows.
     * The only hard rule: admin must provide a reason for `cancelled`.
     */
    public function can(User $actor, Order $order, OrderStatus $to): bool
    {
        $from = $order->status;

        // Admin can always attempt; the order's own rules apply
        if ($actor->isAdmin()) {
            return $from->canTransitionTo($to);
        }

        if ($actor->isCustomer()) {
            return $this->matches(self::CUSTOMER_ALLOWED, $from, $to)
                && $order->customer_id === $actor->id;
        }

        if ($actor->isRider()) {
            // Rider must be the assigned rider
            if ($order->rider?->user_id !== $actor->id) {
                return false;
            }

            return $this->matches(self::RIDER_ALLOWED, $from, $to);
        }

        return false;
    }

    /**
     * Whether the transition requires a note/reason.
     */
    public function requiresReason(OrderStatus $to): bool
    {
        return $to === OrderStatus::Cancelled;
    }

    /**
     * Whether the transition is considered "destructive" (cancel).
     */
    public function isDestructive(OrderStatus $to): bool
    {
        return $to === OrderStatus::Cancelled;
    }

    /**
     * Side-effect timestamps to set on transition.
     *
     * @return array<string, mixed>
     */
    public function sideEffects(OrderStatus $to): array
    {
        return match ($to) {
            OrderStatus::PickedUp  => ['picked_up_at' => now()],
            OrderStatus::Delivered => ['delivered_at' => now()],
            OrderStatus::Cancelled => ['cancelled_at' => now()],
            default                => [],
        };
    }

    /**
     * Whether a rider becomes available again on this transition.
     */
    public function freesRider(OrderStatus $to): bool
    {
        return in_array($to, [OrderStatus::Delivered, OrderStatus::Cancelled], true);
    }

    protected function matches(array $matrix, OrderStatus $from, OrderStatus $to): bool
    {
        foreach ($matrix as [$f, $t]) {
            if ($f === $from && $t === $to) {
                return true;
            }
        }
        return false;
    }
}