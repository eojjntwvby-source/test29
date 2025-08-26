<?php

declare(strict_types=1);

namespace App\ValueObjects;

use App\Enums\MileageUnit;

final readonly class Mileage implements \JsonSerializable
{
    public function __construct(
        public float       $value,
        public MileageUnit $unit
    )
    {
        if ($value < 0) {
            throw new \InvalidArgumentException('Mileage value cannot be negative');
        }
    }

    public static function kilometers(float $value): self
    {
        return new self($value, MileageUnit::KILOMETERS);
    }

    public static function miles(float $value): self
    {
        return new self($value, MileageUnit::MILES);
    }

    public static function fromArray(array $data): self
    {
        return new self(
            (float)$data['value'],
            MileageUnit::from($data['unit'])
        );
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

    public function toKilometers(): float
    {
        return $this->convertTo(MileageUnit::KILOMETERS)->value;
    }

    public function toMiles(): float
    {
        return $this->convertTo(MileageUnit::MILES)->value;
    }

    public function equals(self $other): bool
    {
        return abs($this->toKilometers() - $other->toKilometers()) < 0.001;
    }

    public function toString(): string
    {
        return number_format($this->value, 2, '.', '') . ' ' . $this->unit->value;
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function jsonSerialize(): array
    {
        return [
            'value' => $this->value,
            'unit' => $this->unit->value,
            'display' => $this->toString(),
            'kilometers' => $this->toKilometers(),
            'miles' => $this->toMiles(),
        ];
    }
}
