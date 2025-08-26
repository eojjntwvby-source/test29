<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Car>
 */
class CarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'brand_id' => \App\Models\Brand::factory(),
            'car_model_id' => \App\Models\CarModel::factory(),
            'color_id' => \App\Models\Color::factory(),
            'user_id' => \App\Models\User::factory(),
            'year' => $this->faker->numberBetween(2000, 2024),
            'mileage_value' => $this->faker->randomFloat(2, 0, 200000),
            'mileage_unit' => $this->faker->randomElement(['km', 'mi']),
            'color' => $this->faker->safeColorName, // Legacy field
        ];
    }
}
