<?php

use App\Models\Client;
use App\Models\Order;
use Illuminate\Support\Facades\Artisan;

test('orders delivered more than 24 hours ago are automatically marked as completed', function () {
    $client = Client::factory()->create();

    // Create an order that was delivered 25 hours ago
    $oldDeliveredOrder = Order::factory()->create([
        'client_id' => $client->id,
        'status' => 'delivered',
        'delivered_at' => now()->subHours(25),
    ]);

    // Create an order that was delivered 23 hours ago (should not be auto-completed yet)
    $recentDeliveredOrder = Order::factory()->create([
        'client_id' => $client->id,
        'status' => 'delivered',
        'delivered_at' => now()->subHours(23),
    ]);

    // Create an order that is already completed
    $completedOrder = Order::factory()->create([
        'client_id' => $client->id,
        'status' => 'completed',
        'delivered_at' => now()->subHours(30),
    ]);

    // Run the command
    Artisan::call('orders:auto-complete');

    // Refresh the orders from database
    $oldDeliveredOrder->refresh();
    $recentDeliveredOrder->refresh();
    $completedOrder->refresh();

    // Assert the old delivered order is now completed
    expect($oldDeliveredOrder->status)->toBe('completed');

    // Assert the recent delivered order is still delivered
    expect($recentDeliveredOrder->status)->toBe('delivered');

    // Assert the already completed order remains completed
    expect($completedOrder->status)->toBe('completed');
});

test('orders without delivered_at timestamp are not auto-completed', function () {
    $client = Client::factory()->create();

    // Create a delivered order without delivered_at timestamp
    $order = Order::factory()->create([
        'client_id' => $client->id,
        'status' => 'delivered',
        'delivered_at' => null,
    ]);

    Artisan::call('orders:auto-complete');

    $order->refresh();

    // Should remain delivered since there's no delivered_at timestamp
    expect($order->status)->toBe('delivered');
});

test('delivered_at timestamp is automatically set when order status changes to delivered', function () {
    $client = Client::factory()->create();

    $order = Order::factory()->create([
        'client_id' => $client->id,
        'status' => 'shipped',
        'delivered_at' => null,
    ]);

    // Update the order status to delivered
    $order->status = 'delivered';
    $order->save();

    // delivered_at should now be set
    expect($order->delivered_at)->not->toBeNull();
    expect($order->delivered_at->isToday())->toBeTrue();
});

test('shipped_at timestamp is automatically set when order status changes to shipped', function () {
    $client = Client::factory()->create();

    $order = Order::factory()->create([
        'client_id' => $client->id,
        'status' => 'confirmed',
        'shipped_at' => null,
    ]);

    // Update the order status to shipped
    $order->status = 'shipped';
    $order->save();

    // shipped_at should now be set
    expect($order->shipped_at)->not->toBeNull();
    expect($order->shipped_at->isToday())->toBeTrue();
});
