<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProofOfDelivery extends Model
{
    protected $table = 'proof_of_delivery';

    protected $fillable = [
        'order_id', 'rider_id', 'photo_path', 'signature_path',
        'recipient_name', 'note', 'lat', 'lng', 'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'lat' => 'decimal:7',
            'lng' => 'decimal:7',
            'delivered_at' => 'datetime',
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
