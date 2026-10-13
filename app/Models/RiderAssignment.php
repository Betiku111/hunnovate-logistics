<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiderAssignment extends Model
{
    protected $fillable = [
        'order_id', 'rider_id', 'assigned_by',
        'assigned_at', 'accepted_at', 'rejected_at', 'completed_at',
        'rejection_reason', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'assigned_at' => 'datetime',
            'accepted_at' => 'datetime',
            'rejected_at' => 'datetime',
            'completed_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function rider(): BelongsTo
    {
        return $this->belongsTo(Rider::class);
    }
}
