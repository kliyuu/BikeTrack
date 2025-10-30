<?php

namespace Database\Factories;

use App\Models\Client;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $titles = [
            'Low Stock Alert',
            'Out of Stock Alert',
            'New Order Placed',
            'Order Shipped',
            'Payment Received',
            'Return Request Received',
            'New User Registration',
        ];

        $messages = [
            'Product stock is running low and needs restocking.',
            'Product is out of stock. Please restock immediately.',
            'A new order has been placed and requires processing.',
            'An order has been shipped successfully.',
            'Payment has been received for an order.',
            'A return request has been submitted for an order.',
            'A new user has registered on the platform.',
        ];

        $index = array_rand($titles);

        return [
            'user_id' => 1, // Default to admin user
            'client_id' => $this->faker->boolean(30) ? Client::factory() : null,
            'title' => $titles[$index],
            'message' => $messages[$index],
            'url' => $this->faker->boolean(70) ? $this->faker->randomElement([
                '/admin/orders',
                '/admin/inventory',
                '/admin/users',
                '/admin/return-requests',
            ]) : null,
            'is_read' => $this->faker->boolean(40),
        ];
    }

    /**
     * Indicate that the notification is unread.
     */
    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => false,
        ]);
    }

    /**
     * Indicate that the notification is read.
     */
    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_read' => true,
        ]);
    }

    /**
     * Indicate that the notification is for admin (no client).
     */
    public function forAdmin(): static
    {
        return $this->state(fn (array $attributes) => [
            'client_id' => null,
        ]);
    }

    /**
     * Indicate that the notification is for a client.
     */
    public function forClient(?int $clientId = null): static
    {
        return $this->state(fn (array $attributes) => [
            'client_id' => $clientId ?? Client::factory(),
        ]);
    }
}
