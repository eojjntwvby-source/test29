<?php

namespace Database\Seeders;

use App\Models\Color;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    public function run(): void
    {
        $colors = [
            ['name' => 'Black', 'hex_code' => '#000000', 'rgb_code' => '0,0,0'],
            ['name' => 'White', 'hex_code' => '#FFFFFF', 'rgb_code' => '255,255,255'],
            ['name' => 'Silver', 'hex_code' => '#C0C0C0', 'rgb_code' => '192,192,192'],
            ['name' => 'Gray', 'hex_code' => '#808080', 'rgb_code' => '128,128,128'],
            ['name' => 'Red', 'hex_code' => '#FF0000', 'rgb_code' => '255,0,0'],
            ['name' => 'Blue', 'hex_code' => '#0000FF', 'rgb_code' => '0,0,255'],
            ['name' => 'Green', 'hex_code' => '#008000', 'rgb_code' => '0,128,0'],
            ['name' => 'Yellow', 'hex_code' => '#FFFF00', 'rgb_code' => '255,255,0'],
            ['name' => 'Orange', 'hex_code' => '#FFA500', 'rgb_code' => '255,165,0'],
            ['name' => 'Purple', 'hex_code' => '#800080', 'rgb_code' => '128,0,128'],
            ['name' => 'Brown', 'hex_code' => '#A52A2A', 'rgb_code' => '165,42,42'],
            ['name' => 'Beige', 'hex_code' => '#F5F5DC', 'rgb_code' => '245,245,220'],
            ['name' => 'Gold', 'hex_code' => '#FFD700', 'rgb_code' => '255,215,0'],
            ['name' => 'Navy Blue', 'hex_code' => '#000080', 'rgb_code' => '0,0,128'],
            ['name' => 'Dark Green', 'hex_code' => '#006400', 'rgb_code' => '0,100,0'],
            ['name' => 'Maroon', 'hex_code' => '#800000', 'rgb_code' => '128,0,0'],
            ['name' => 'Pink', 'hex_code' => '#FFC0CB', 'rgb_code' => '255,192,203'],
            ['name' => 'Turquoise', 'hex_code' => '#40E0D0', 'rgb_code' => '64,224,208'],
            ['name' => 'Charcoal', 'hex_code' => '#36454F', 'rgb_code' => '54,69,79'],
            ['name' => 'Pearl White', 'hex_code' => '#F8F6F0', 'rgb_code' => '248,246,240'],
        ];

        foreach ($colors as $color) {
            Color::firstOrCreate(
                ['name' => $color['name']],
                $color
            );
        }
    }
}
