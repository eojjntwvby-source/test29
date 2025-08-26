<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Color>
 */
class ColorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->safeColorName . '_' . $this->faker->randomNumber(4),
            'hex_code' => $this->faker->hexColor,
            'rgb_code' => implode(',', [$this->faker->numberBetween(0, 255), $this->faker->numberBetween(0, 255), $this->faker->numberBetween(0, 255)]),
        ];
    }
}
