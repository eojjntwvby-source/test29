<?php

namespace App\Repositories\Eloquent;

use App\Models\Brand;
use App\Repositories\Contracts\BrandRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class BrandRepository extends BaseRepository implements BrandRepositoryInterface
{
    public function __construct(Brand $model)
    {
        parent::__construct($model);
    }

    public function getAllWithModelsCount(): Collection
    {
        return $this->model->withCount('models')->get();
    }

    public function searchByName(string $name): Collection
    {
        return $this->model->where('name', 'like', '%' . $name . '%')->get();
    }

    public function getAllWithModels(): Collection
    {
        return $this->model->with('models')->get();
    }
}
