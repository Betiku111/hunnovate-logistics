<?php

namespace App\Models;

use App\Enums\UserRole;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, HasUuids, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'avatar_path',
        'is_active',
        'theme_preference',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
            'is_active' => 'boolean',
        ];
    }

    // ---------- Relations ----------

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class);
    }

    public function rider(): HasOne
    {
        return $this->hasOne(Rider::class);
    }

    public function admin(): HasOne
    {
        return $this->hasOne(Admin::class);
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    /**
     * Orders placed by this user (when acting as a customer).
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    // ---------- Role helpers ----------

    public function isCustomer(): bool
    {
        return $this->role === UserRole::Customer;
    }

    public function isRider(): bool
    {
        return $this->role === UserRole::Rider;
    }

    public function isAdmin(): bool
    {
        return $this->role === UserRole::Admin;
    }
}
