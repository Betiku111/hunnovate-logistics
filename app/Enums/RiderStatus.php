<?php

namespace App\Enums;

enum RiderStatus: string
{
    case Pending = 'pending';
    case Verified = 'verified';
    case Rejected = 'rejected';
    case Suspended = 'suspended';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Pending Verification',
            self::Verified => 'Verified',
            self::Rejected => 'Rejected',
            self::Suspended => 'Suspended',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Verified => 'success',
            self::Rejected => 'danger',
            self::Suspended => 'slate',
        };
    }

    public function canReceiveAssignments(): bool
    {
        return $this === self::Verified;
    }
}
