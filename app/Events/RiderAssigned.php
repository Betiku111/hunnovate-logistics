<?php

namespace App\Events;

use App\Models\Order;
use App\Models\Rider;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RiderAssigned
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Order $order,
        public Rider $rider,
        public ?User $assignedBy = null,
        public bool $isReassignment = false,
    ) {}
}