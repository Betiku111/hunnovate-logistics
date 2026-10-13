<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\VehicleType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'tracking_number', 'customer_id', 'rider_id', 'assigned_by', 'outlet_id',
        'status', 'vehicle_type',
        'pickup_contact_name', 'pickup_contact_phone', 'pickup_address',
        'pickup_city', 'pickup_state', 'pickup_lat', 'pickup_lng',
        'dropoff_contact_name', 'dropoff_contact_phone', 'dropoff_address',
        'dropoff_city', 'dropoff_state', 'dropoff_lat', 'dropoff_lng',
        'package_description', 'package_weight_kg',
        'package_length_cm', 'package_width_cm', 'package_height_cm',
        'is_fragile', 'special_instructions',
        'is_express', 'scheduled_at', 'picked_up_at', 'delivered_at',
        'cancelled_at', 'cancellation_reason',
        'distance_km', 'base_fee', 'distance_fee', 'weight_fee', 'express_fee', 'fragile_fee',
        'total_fee', 'currency', 'payment_status', 'payment_method', 'eta_minutes',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'vehicle_type' => VehicleType::class,
            'payment_status' => PaymentStatus::class,
            'is_fragile' => 'boolean',
            'is_express' => 'boolean',
            'scheduled_at' => 'datetime',
            'picked_up_at' => 'datetime',
            'delivered_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'pickup_lat' => 'decimal:7',
            'pickup_lng' => 'decimal:7',
            'dropoff_lat' => 'decimal:7',
            'dropoff_lng' => 'decimal:7',
            'distance_km' => 'decimal:2',
            'package_weight_kg' => 'decimal:2',
            'base_fee' => 'decimal:2',
            'distance_fee' => 'decimal:2',
            'weight_fee' => 'decimal:2',
            'express_fee' => 'decimal:2',
            'fragile_fee' => 'decimal:2',
            'total_fee' => 'decimal:2',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function rider(): BelongsTo
    {
        return $this->belongsTo(Rider::class);
    }

    public function assigner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function outlet(): BelongsTo
    {
        return $this->belongsTo(Outlet::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class)->orderBy('changed_at');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(RiderAssignment::class);
    }

    public function activeAssignment(): HasOne
    {
        return $this->hasOne(RiderAssignment::class)->where('is_active', true);
    }

    public function locations(): HasMany
    {
        return $this->hasMany(RiderLocation::class);
    }

    public function proofOfDelivery(): HasOne
    {
        return $this->hasOne(ProofOfDelivery::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function earning(): HasOne
    {
        return $this->hasOne(Earning::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }
}
