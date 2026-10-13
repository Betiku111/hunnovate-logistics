<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Earning extends Model
{
    protected $fillable = [
        'rider_id', 'order_id', 'gross_amount', 'platform_commission',
        'net_amount', 'currency', 'earned_at', 'is_paid_out', 'paid_out_at',
    ];

    protected function casts(): array
    {
        return [
            'gross_amount' => 'decimal:2',
            'platform_commission' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'earned_at' => 'datetime',
            'is_paid_out' => 'boolean',
            'paid_out_at' => 'datetime',
        ];
    }

    public function rider(): BelongsTo
    {
        return $this->belongsTo(Rider::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
