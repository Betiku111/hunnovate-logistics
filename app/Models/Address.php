<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id', 'label', 'contact_name', 'contact_phone',
        'line1', 'line2', 'city', 'state', 'country', 'postal_code',
        'lat', 'lng', 'is_default',
    ];

    protected function casts(): array
    {
        return [
            'lat' => 'decimal:7',
            'lng' => 'decimal:7',
            'is_default' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
