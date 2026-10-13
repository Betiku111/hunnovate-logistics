<?php

namespace App\Enums;

enum UserRole: string
{
    case Customer = 'customer';
    case Rider = 'rider';
    case Admin = 'admin';

    public function label(): string
    {
        return match ($this) {
            self::Customer => 'Customer',
            self::Rider => 'Rider',
            self::Admin => 'Administrator',
        };
    }
}
