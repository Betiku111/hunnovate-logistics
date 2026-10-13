<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    use HasUuids;

    protected $fillable = [
        'user_id', 'company_name', 'default_address', 'city', 'state',
        'country', 'total_spent', 'total_orders',
    ];

    protected function casts(): array
    {
        return [
            'total_spent' => 'decimal:2',
            'total_orders' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
