<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    public function run(): void
    {
        $brands = [
            'Toyota',
            'BMW',
            'Mercedes-Benz',
            'Audi',
            'Volkswagen',
            'Ford',
            'Honda',
            'Nissan',
            'Hyundai',
            'Kia'
        ];

        foreach ($brands as $brandName) {
            Brand::firstOrCreate(['name' => $brandName]);
        }
    }
}
