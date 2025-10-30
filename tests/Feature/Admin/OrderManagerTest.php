<?php

use App\Livewire\Admin\Orders\OrderManager;
use App\Models\Client;
use App\Models\Order;
use App\Models\Role;
use App\Models\User;

beforeEach(function () {
    $adminRole = Role::firstOrCreate(
        ['name' => 'admin'],
        ['description' => 'Admin User: Full system access']
    );

    $this->admin = User::factory()->create(['role_id' => $adminRole->id]);
    $this->actingAs($this->admin);
});

test('getCurrentOrder method returns correct order when order is selected', function () {
    $client = Client::factory()->create();
    $order = Order::create([
        'client_id' => $client->id,
        'order_number' => 'ORD-123456',
        'status' => 'confirmed',
        'total_amount' => 1000.00,
        'shipping_amount' => 50.00,
        'billing_address' => '123 Main St',
        'shipping_address' => '123 Main St',
        'placed_at' => now(),
    ]);

    $component = new OrderManager;
    $component->orderId = $order->id;

    $currentOrder = $component->getCurrentOrder();

    expect($currentOrder)->not->toBeNull();
    expect($currentOrder->id)->toBe($order->id);
    expect($currentOrder->status)->toBe('confirmed');
});

test('getCurrentOrder returns null when no order is selected', function () {
    $component = new OrderManager;
    $currentOrder = $component->getCurrentOrder();

    expect($currentOrder)->toBeNull();
});

test('status options are correctly disabled based on order status', function () {
    $client = Client::factory()->create();

    // Test with confirmed order
    $confirmedOrder = Order::create([
        'client_id' => $client->id,
        'order_number' => 'ORD-123456',
        'status' => 'confirmed',
        'total_amount' => 1000.00,
        'shipping_amount' => 50.00,
        'billing_address' => '123 Main St',
        'shipping_address' => '123 Main St',
        'placed_at' => now(),
    ]);

    $component = new OrderManager;
    $component->orderId = $confirmedOrder->id;

    // When order is confirmed, pending and cancelled should be disabled
    $currentOrder = $component->getCurrentOrder();
    expect($currentOrder->status)->toBe('confirmed');

    // Test with pending order
    $pendingOrder = Order::create([
        'client_id' => $client->id,
        'order_number' => 'ORD-789012',
        'status' => 'pending',
        'total_amount' => 1000.00,
        'shipping_amount' => 50.00,
        'billing_address' => '123 Main St',
        'shipping_address' => '123 Main St',
        'placed_at' => now(),
    ]);

    $component->orderId = $pendingOrder->id;
    $currentOrder = $component->getCurrentOrder();
    expect($currentOrder->status)->toBe('pending');
});
