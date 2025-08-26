<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Car;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class ProcessCarCreation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly array $carData,
        private readonly int   $userId
    )
    {
    }

    public function handle(): void
    {
        Car::create(array_merge($this->carData, ['user_id' => $this->userId]));
    }
}
