<?php

namespace App\Enums;

enum VehicleType: string
{
    case Motorcycle = 'motorcycle';
    case Car = 'car';
    case Truck = 'truck';

    public function label(): string
    {
        return match ($this) {
            self::Motorcycle => 'Motorcycle',
            self::Car => 'Car',
            self::Truck => 'Truck',
        };
    }

    public function maxWeightKg(): int
    {
        return match ($this) {
            self::Motorcycle => 20,
            self::Car => 200,
            self::Truck => 5000,
        };
    }

    public function recommendedFor(float $weightKg): bool
    {
        return $weightKg <= $this->maxWeightKg();
    }
}
