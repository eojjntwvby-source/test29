<?php

declare(strict_types=1);

namespace App\ValueObjects;

use App\Enums\MileageUnit;

final readonly class Mileage
{
    public function __construct(
        public float       $value,
        public MileageUnit $unit
    )
    {
    }

    public function convertTo(MileageUnit $targetUnit): self
    {
        if ($this->unit === $targetUnit) {
            return $this;
        }

        $kmValue = match ($this->unit) {
            MileageUnit::KILOMETERS => $this->value,
            MileageUnit::MILES => $this->value * 1.609344,
        };

        $convertedValue = match ($targetUnit) {
            MileageUnit::KILOMETERS => $kmValue,
            MileageUnit::MILES => $kmValue / 1.609344,
        };

        return new self($convertedValue, $targetUnit);
    }

    public function toKilometers(): self
    {
        return $this->convertTo(MileageUnit::KILOMETERS);
    }

    public function toMiles(): self
    {
        return $this->convertTo(MileageUnit::MILES);
    }

    public function __toString(): string
    {
        return number_format($this->value, 1) . ' ' . $this->unit->getLabel();
    }
}
