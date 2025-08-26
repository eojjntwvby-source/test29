<?php

declare(strict_types=1);

namespace App\Providers;

use App\Repositories\Contracts\CarRepositoryInterface;
use App\Repositories\Eloquent\CarRepository;
use Illuminate\Support\ServiceProvider;

final class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(CarRepositoryInterface::class, CarRepository::class);
    }

    public function boot(): void
    {
        //
    }
}