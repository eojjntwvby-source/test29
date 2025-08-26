<?php

declare(strict_types=1);

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class CarResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'brand' => new BrandResource($this->whenLoaded('brand')),
            'car_model' => new CarModelResource($this->whenLoaded('carModel')),
            'color' => $this->whenLoaded('colorRelation', function () {
                return new ColorResource($this->colorRelation);
            }),
            'year' => $this->year,
            'mileage' => $this->mileage_value ? [
                'value' => $this->mileage_value,
                'unit' => $this->mileage_unit?->value,
                'display' => number_format($this->mileage_value, 1) . ' ' . ($this->mileage_unit?->getLabel() ?? 'unknown')
            ] : null,
            'legacy_color' => $this->color, // Legacy field
            'user_id' => $this->user_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}