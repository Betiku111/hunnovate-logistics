<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Outlet extends Model
{
    use HasUuids;

    protected $fillable = [
        'name', 'code', 'phone', 'email', 'address',
        'city', 'state', 'country', 'lat', 'lng', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'lat' => 'decimal:7',
            'lng' => 'decimal:7',
            'is_active' => 'boolean',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
