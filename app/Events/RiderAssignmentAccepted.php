<?php

namespace App\Events;

use App\Models\Order;
use App\Models\Rider;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RiderAssignmentAccepted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Order $order,
        public Rider $rider,
    ) {}
}