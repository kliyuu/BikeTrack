<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Client>
 */
class ClientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper($this->faker->unique()->bothify('CL-###-???')),
            'company_name' => $this->faker->company(),
            'contact_name' => $this->faker->name(),
            'contact_email' => $this->faker->unique()->safeEmail(),
            'contact_phone' => $this->faker->phoneNumber(),
            'tax_number' => $this->faker->optional()->numerify('###-###-###'),
            'billing_address' => $this->faker->address(),
            'shipping_address' => $this->faker->address(),
            'status' => $this->faker->randomElement(['active', 'inactive']),
            'payment_terms' => $this->faker->optional()->randomElement(['Net 30', 'Net 60', 'COD']),
            'payment_method' => $this->faker->optional()->randomElement(['cod', 'card', 'gcash']),
        ];
    }
}
