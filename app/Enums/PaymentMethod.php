<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Paystack = 'paystack';
    case Flutterwave = 'flutterwave';
    case Wallet = 'wallet';
    case Cash = 'cash';

    public function label(): string
    {
        return match ($this) {
            self::Paystack => 'Paystack',
            self::Flutterwave => 'Flutterwave',
            self::Wallet => 'Wallet',
            self::Cash => 'Cash on Delivery',
        };
    }
}
