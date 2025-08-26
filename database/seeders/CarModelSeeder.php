<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\CarModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CarModelSeeder extends Seeder
{
    public function run(): void
    {
        $modelsData = [
            'Toyota' => ['Camry', 'Corolla', 'RAV4', 'Prius', 'Highlander'],
            'BMW' => ['3 Series', '5 Series', 'X3', 'X5', 'i3'],
            'Mercedes-Benz' => ['C-Class', 'E-Class', 'S-Class', 'GLE', 'GLC'],
            'Audi' => ['A3', 'A4', 'A6', 'Q5', 'Q7'],
            'Volkswagen' => ['Golf', 'Passat', 'Tiguan', 'Polo', 'Jetta'],
            'Ford' => ['Focus', 'Mustang', 'Explorer', 'F-150', 'Escape'],
            'Honda' => ['Civic', 'Accord', 'CR-V', 'Pilot', 'Fit'],
            'Nissan' => ['Altima', 'Sentra', 'Rogue', 'Pathfinder', 'Leaf'],
            'Hyundai' => ['Elantra', 'Sonata', 'Tucson', 'Santa Fe', 'Kona'],
            'Kia' => ['Optima', 'Forte', 'Sorento', 'Sportage', 'Soul']
        ];

        foreach ($modelsData as $brandName => $models) {
            $brand = Brand::where('name', $brandName)->first();

            if ($brand) {
                foreach ($models as $modelName) {
                    CarModel::firstOrCreate([
                        'name' => $modelName,
                        'brand_id' => $brand->id
                    ]);
                }
            }
        }
    }
}
