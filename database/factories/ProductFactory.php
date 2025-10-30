<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(3, true),
            'sku' => 'BT-'.strtoupper($this->faker->bothify('??######')),
            'description' => $this->faker->paragraph(),
            'unit_price' => $this->faker->randomFloat(2, 10, 1000),
            'category_id' => Category::factory(),
            'brand_id' => Brand::factory(),
            'location_id' => \App\Models\Location::first()?->id ?? 1,
            'is_active' => true,
        ];
    }
}
