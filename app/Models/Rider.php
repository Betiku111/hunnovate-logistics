<?php

namespace App\Models;

use App\Enums\RiderStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rider extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id', 'status', 'home_address', 'city', 'state',
        'emergency_contact_name', 'emergency_contact_phone',
        'government_id_path', 'rider_license_path', 'profile_photo_path',
        'verified_at', 'verified_by', 'rejection_reason',
        'rating', 'total_deliveries', 'cancelled_deliveries',
        'is_online', 'is_available', 'current_lat', 'current_lng', 'location_updated_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => RiderStatus::class,
            'verified_at' => 'datetime',
            'location_updated_at' => 'datetime',
            'rating' => 'decimal:2',
            'is_online' => 'boolean',
            'is_available' => 'boolean',
            'current_lat' => 'decimal:7',
            'current_lng' => 'decimal:7',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function vehicles(): HasMany
    {
        return $this->hasMany(Vehicle::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function earnings(): HasMany
    {
        return $this->hasMany(Earning::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
