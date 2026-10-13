<?php

namespace App\Models;

use App\Enums\VehicleType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'rider_id', 'type', 'brand', 'model', 'color',
        'plate_number', 'registration_number', 'capacity_kg', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'type' => VehicleType::class,
            'is_active' => 'boolean',
            'capacity_kg' => 'integer',
        ];
    }

    public function rider(): BelongsTo
    {
        return $this->belongsTo(Rider::class);
    }
}
