<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Car;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final  class ProcessCarUpdate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        private readonly Car   $car,
        private readonly array $updateData,
        private readonly array $originalData
    )
    {
    }

    public function handle(): void
    {
        $this->car->update($this->updateData);
    }
}
