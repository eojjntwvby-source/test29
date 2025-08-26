<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface CarModelRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get models by brand
     */
    public function getByBrand(int $brandId): Collection;

    /**
     * Get models with brand information
     */
    public function getAllWithBrand(): Collection;

    /**
     * Search models by name
     */
    public function searchByName(string $name): Collection;

    /**
     * Get models with cars count
     */
    public function getAllWithCarsCount(): Collection;
}
