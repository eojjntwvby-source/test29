<?php

declare(strict_types=1);

namespace App\Repositories\Eloquent;

use App\Models\Car;
use App\Repositories\Contracts\CarRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

final class CarRepository extends BaseRepository implements CarRepositoryInterface
{
    public function __construct(Car $model)
    {
        parent::__construct($model);
    }

    public function getForUser(int $userId): Collection
    {
        return $this->model->where('user_id', $userId)
            ->with(['brand', 'carModel', 'colorRelation'])
            ->get();
    }

    public function findForUser(int $carId, int $userId): ?Car
    {
        return $this->model->where('id', $carId)
            ->where('user_id', $userId)
            ->with(['brand', 'carModel', 'colorRelation'])
            ->first();
    }

    public function createForUser(array $data, int $userId): Car
    {
        $data['user_id'] = $userId;
        return $this->create($data);
    }

    public function updateForUser(Car $car, array $data): Car
    {
        return $this->update($car, $data);
    }
}