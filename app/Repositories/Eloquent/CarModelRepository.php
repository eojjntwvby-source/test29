<?php

namespace App\Repositories\Eloquent;

use App\Models\CarModel;
use App\Repositories\Contracts\CarModelRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CarModelRepository extends BaseRepository implements CarModelRepositoryInterface
{
    public function __construct(CarModel $model)
    {
        parent::__construct($model);
    }

    public function getByBrand(int $brandId): Collection
    {
        return $this->model->where('brand_id', $brandId)->with('brand')->get();
    }

    public function getAllWithBrand(): Collection
    {
        return $this->model->with('brand')->get();
    }

    public function searchByName(string $name): Collection
    {
        return $this->model->where('name', 'like', '%' . $name . '%')
            ->with('brand')
            ->get();
    }

    public function getAllWithCarsCount(): Collection
    {
        return $this->model->withCount('cars')->with('brand')->get();
    }
}
