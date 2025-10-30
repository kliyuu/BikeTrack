<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Notification;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\StockNotificationService;

test('creates out of stock notification for product variants with zero stock', function () {
    // Create test data
    $user = \App\Models\User::factory()->create();
    $location = \App\Models\Location::create(['floor' => 'A', 'aisle' => '1', 'shelf' => '1']);
    $category = Category::factory()->create();
    $brand = Brand::factory()->create();
    $product = Product::factory()->create([
        'category_id' => $category->id,
        'brand_id' => $brand->id,
        'location_id' => $location->id,
        'is_active' => true,
    ]);

    $variant = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'cached_stock' => 0,
        'low_stock_threshold' => 5,
        'is_active' => true,
    ]);

    // Check for out of stock notifications
    $stockService = app(StockNotificationService::class);
    $stockService->checkOutOfStockProducts();

    // Assert notification was created
    expect(Notification::where('title', 'Product Out of Stock')->count())->toBe(1);

    $notification = Notification::where('title', 'Product Out of Stock')->first();
    expect($notification->message)->toContain($product->name);
    expect($notification->url)->toBe(route('admin.stock-levels'));
    expect($notification->is_read)->toBeFalse();
});

test('creates low stock notification for product variants below threshold', function () {
    // Create test data
    $user = \App\Models\User::factory()->create();
    $location = \App\Models\Location::create(['floor' => 'A', 'aisle' => '1', 'shelf' => '1']);
    $category = Category::factory()->create();
    $brand = Brand::factory()->create();
    $product = Product::factory()->create([
        'category_id' => $category->id,
        'brand_id' => $brand->id,
        'location_id' => $location->id,
        'is_active' => true,
    ]);

    $variant = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'cached_stock' => 3,
        'low_stock_threshold' => 5,
        'is_active' => true,
    ]);

    // Check for low stock notifications
    $stockService = app(StockNotificationService::class);
    $stockService->checkLowStockProducts();

    // Assert notification was created
    expect(Notification::where('title', 'Low Stock Alert')->count())->toBe(1);

    $notification = Notification::where('title', 'Low Stock Alert')->first();
    expect($notification->message)->toContain($product->name);
    expect($notification->message)->toContain('Current: 3');
    expect($notification->message)->toContain('Threshold: 5');
    expect($notification->url)->toBe(route('admin.stock-levels'));
});

test('does not create duplicate notifications for the same product on the same day', function () {
    // Create test data
    $user = \App\Models\User::factory()->create();
    $location = \App\Models\Location::create(['floor' => 'A', 'aisle' => '1', 'shelf' => '1']);
    $category = Category::factory()->create();
    $brand = Brand::factory()->create();
    $product = Product::factory()->create([
        'category_id' => $category->id,
        'brand_id' => $brand->id,
        'location_id' => $location->id,
        'is_active' => true,
    ]);

    $variant = ProductVariant::factory()->create([
        'product_id' => $product->id,
        'cached_stock' => 0,
        'low_stock_threshold' => 5,
        'is_active' => true,
    ]);

    // Check for out of stock notifications twice
    $stockService = app(StockNotificationService::class);
    $stockService->checkOutOfStockProducts();
    $stockService->checkOutOfStockProducts();

    // Assert only one notification was created
    expect(Notification::where('title', 'Product Out of Stock')->count())->toBe(1);
});

test('returns correct stock summary counts', function () {
    // Create test data
    $user = \App\Models\User::factory()->create();
    $location = \App\Models\Location::create(['floor' => 'A', 'aisle' => '1', 'shelf' => '1']);
    $category = Category::factory()->create();
    $brand = Brand::factory()->create();
    $product = Product::factory()->create([
        'category_id' => $category->id,
        'brand_id' => $brand->id,
        'location_id' => $location->id,
        'is_active' => true,
    ]);

    // Out of stock variant
    ProductVariant::factory()->create([
        'product_id' => $product->id,
        'cached_stock' => 0,
        'low_stock_threshold' => 5,
        'is_active' => true,
    ]);

    // Low stock variant
    ProductVariant::factory()->create([
        'product_id' => $product->id,
        'cached_stock' => 2,
        'low_stock_threshold' => 5,
        'is_active' => true,
    ]);

    // Normal stock variant
    ProductVariant::factory()->create([
        'product_id' => $product->id,
        'cached_stock' => 10,
        'low_stock_threshold' => 5,
        'is_active' => true,
    ]);

    $stockService = app(StockNotificationService::class);
    $summary = $stockService->getStockSummary();

    expect($summary['out_of_stock_count'])->toBe(1);
    expect($summary['low_stock_count'])->toBe(1);
});
