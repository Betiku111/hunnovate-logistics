<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Created = 'created';
    case WaitingAdmin = 'waiting_admin';
    case Assigned = 'assigned';
    case Accepted = 'accepted';
    case RiderArriving = 'rider_arriving';
    case Arrived = 'arrived';
    case PickedUp = 'picked_up';
    case InTransit = 'in_transit';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Created => 'Created',
            self::WaitingAdmin => 'Waiting for Admin',
            self::Assigned => 'Rider Assigned',
            self::Accepted => 'Rider Accepted',
            self::RiderArriving => 'Rider Arriving',
            self::Arrived => 'Rider Arrived',
            self::PickedUp => 'Picked Up',
            self::InTransit => 'In Transit',
            self::Delivered => 'Delivered',
            self::Cancelled => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Created, self::WaitingAdmin => 'slate',
            self::Assigned, self::Accepted,
            self::RiderArriving, self::Arrived => 'info',
            self::PickedUp, self::InTransit => 'warning',
            self::Delivered => 'success',
            self::Cancelled => 'danger',
        };
    }

    /**
     * Allowed next statuses from this one.
     */
    public function nextStatuses(): array
    {
        return match ($this) {
            self::Created => [self::WaitingAdmin, self::Cancelled],
            self::WaitingAdmin => [self::Assigned, self::Cancelled],
            self::Assigned => [self::Accepted, self::Cancelled],
            self::Accepted => [self::RiderArriving, self::Cancelled],
            self::RiderArriving => [self::Arrived, self::Cancelled],
            self::Arrived => [self::PickedUp, self::Cancelled],
            self::PickedUp => [self::InTransit, self::Cancelled],
            self::InTransit => [self::Delivered, self::Cancelled],
            self::Delivered => [],
            self::Cancelled => [],
        };
    }

    public function canTransitionTo(self $next): bool
    {
        return in_array($next, $this->nextStatuses(), true);
    }

    public function isFinal(): bool
    {
        return in_array($this, [self::Delivered, self::Cancelled], true);
    }
}
