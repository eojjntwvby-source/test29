<?php

declare(strict_types=1);

namespace App\Repositories\Contracts;

use App\Models\Car;
use Illuminate\Database\Eloquent\Collection;

interface CarRepositoryInterface extends BaseRepositoryInterface
{
    public function getForUser(int $userId): Collection;

    public function findForUser(int $carId, int $userId): ?Car;

    public function createForUser(array $data, int $userId): Car;

    public function updateForUser(Car $car, array $data): Car;
}
