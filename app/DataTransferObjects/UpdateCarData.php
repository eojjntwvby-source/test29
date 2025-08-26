<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use App\Enums\MileageUnit;
use App\ValueObjects\Mileage;

final readonly class UpdateCarData
{
    public function __construct(
        public ?int     $brandId = null,
        public ?int     $carModelId = null,
        public ?int     $colorId = null,
        public ?int     $year = null,
        public ?Mileage $mileage = null,
        public ?string  $color = null
    )
    {
    }

    public static function fromArray(array $data): self
    {
        $mileage = null;
        if (isset($data['mileage']) && is_array($data['mileage'])) {
            $mileage = new Mileage(
                (float)$data['mileage']['value'],
                MileageUnit::from($data['mileage']['unit'])
            );
        }

        return new self(
            brandId: $data['brand_id'] ?? null,
            carModelId: $data['car_model_id'] ?? null,
            colorId: $data['color_id'] ?? null,
            year: $data['year'] ?? null,
            mileage: $mileage,
            color: $data['color'] ?? null
        );
    }

    public function hasChanges(): bool
    {
        return $this->brandId !== null
            || $this->carModelId !== null
            || $this->colorId !== null
            || $this->year !== null
            || $this->mileage !== null
            || $this->color !== null;
    }

    public function toArray(): array
    {
        $data = [
            'brand_id' => $this->brandId,
            'car_model_id' => $this->carModelId,
            'color_id' => $this->colorId,
            'year' => $this->year,
            'color' => $this->color,
        ];

        if ($this->mileage) {
            $data['mileage_value'] = $this->mileage->value;
            $data['mileage_unit'] = $this->mileage->unit->value;
        }

        return array_filter($data, fn($value) => $value !== null);
    }
}
