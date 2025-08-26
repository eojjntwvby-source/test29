<?php

declare(strict_types=1);

namespace App\Enums;

enum MileageUnit: string
{
    case KILOMETERS = 'km';
    case MILES = 'mi';

    public function getLabel(): string
    {
        return match ($this) {
            self::KILOMETERS => 'Kilometers',
            self::MILES => 'Miles',
        };
    }

    public function getConversionFactor(): float
    {
        return match ($this) {
            self::KILOMETERS => 1.0,
            self::MILES => 1.609344, // 1 mile = 1.609344 km
        };
    }

    public static function fromString(string $unit): self
    {
        return match (strtolower($unit)) {
            'km', 'kilometers' => self::KILOMETERS,
            'mi', 'miles' => self::MILES,
            default => throw new \InvalidArgumentException("Invalid mileage unit: {$unit}"),
        };
    }
}
