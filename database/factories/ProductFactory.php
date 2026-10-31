<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sku' => 'SKU-'.fake()->unique()->numerify('#####'),
            'name' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'quantity' => fake()->numberBetween(10, 100),
            'unit' => 'Adet',
            'low_stock_threshold' => 5,
        ];
    }
}
