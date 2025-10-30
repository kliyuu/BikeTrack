<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProductVariant>
 */
class ProductVariantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'variant_type' => $this->faker->randomElement(['size', 'color', 'model']),
            'variant_name' => $this->faker->word(),
            'variant_attributes' => [],
            'size' => $this->faker->randomElement(['XS', 'S', 'M', 'L', 'XL', null]),
            'color' => $this->faker->randomElement(['Red', 'Blue', 'Green', 'Black', 'White', null]),
            'model' => $this->faker->randomElement(['Model A', 'Model B', 'Model C', null]),
            'specifications' => $this->faker->sentence(),
            'variant_sku' => 'VAR-'.$this->faker->unique()->regexify('[A-Z0-9]{8}'),
            'price_adjustment' => $this->faker->randomFloat(2, -100, 100),
            'low_stock_threshold' => $this->faker->numberBetween(5, 20),
            'cached_stock' => $this->faker->numberBetween(0, 100),
            'is_active' => true,
        ];
    }
}
