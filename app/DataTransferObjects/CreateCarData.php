<?php

declare(strict_types=1);

namespace App\DataTransferObjects;

use App\Enums\MileageUnit;
use App\ValueObjects\Mileage;

final readonly class CreateCarData
{
    public function __construct(
        public int      $brandId,
        public int      $carModelId,
        public ?int     $colorId,
        public ?int     $year,
        public ?Mileage $mileage,
        public ?string  $color,
        public int      $userId
    )
    {
    }

    public static function fromArray(array $data, int $userId): self
    {
        $mileage = null;
        if (isset($data['mileage']) && is_array($data['mileage'])) {
            $mileage = new Mileage(
                (float)$data['mileage']['value'],
                MileageUnit::from($data['mileage']['unit'])
            );
        }

        return new self(
            brandId: $data['brand_id'],
            carModelId: $data['car_model_id'],
            colorId: $data['color_id'] ?? null,
            year: $data['year'] ?? null,
            mileage: $mileage,
            color: $data['color'] ?? null,
            userId: $userId
        );
    }

    public function toArray(): array
    {
        $data = [
            'brand_id' => $this->brandId,
            'car_model_id' => $this->carModelId,
            'color_id' => $this->colorId,
            'year' => $this->year,
            'color' => $this->color,
            'user_id' => $this->userId,
        ];

        if ($this->mileage) {
            $data['mileage_value'] = $this->mileage->value;
            $data['mileage_unit'] = $this->mileage->unit->value;
        }

        return array_filter($data, fn($value) => $value !== null);
    }
}
