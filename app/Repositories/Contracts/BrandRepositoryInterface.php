<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface BrandRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Get brands with their models count
     */
    public function getAllWithModelsCount(): Collection;

    /**
     * Search brands by name
     */
    public function searchByName(string $name): Collection;

    /**
     * Get brands with their models
     */
    public function getAllWithModels(): Collection;
}
