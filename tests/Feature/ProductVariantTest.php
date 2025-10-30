<?php

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;

test('product can have variants', function () {
    $category = Category::factory()->create();
    $brand = Brand::factory()->create();

    $product = Product::factory()->create([
        'category_id' => $category->id,
        'brand_id' => $brand->id,
    ]);

    $variant = ProductVariant::create([
        'product_id' => $product->id,
        'variant_type' => 'size_color',
        'variant_name' => 'Large Red',
        'size' => 'L',
        'color' => 'Red',
        'variant_sku' => 'TEST-001-V001',
        'price_adjustment' => 5.00,
        'stock_quantity' => 10,
        'is_active' => true,
    ]);

    expect($product->variants)->toHaveCount(1);
    expect($variant->product->id)->toBe($product->id);
    expect($variant->getFinalPrice())->toBe($product->unit_price + 5.00);
    expect($variant->getDisplayName())->toBe('Size: L | Color: Red');
});

test('variant final price calculation works correctly', function () {
    $category = Category::factory()->create();
    $brand = Brand::factory()->create();

    $product = Product::factory()->create([
        'category_id' => $category->id,
        'brand_id' => $brand->id,
        'unit_price' => 100.00,
    ]);

    $variant = ProductVariant::create([
        'product_id' => $product->id,
        'variant_type' => 'size_color',
        'variant_name' => 'Premium Version',
        'variant_sku' => 'TEST-001-V001',
        'price_adjustment' => 25.00,
        'stock_quantity' => 5,
        'is_active' => true,
    ]);

    expect($variant->getFinalPrice())->toBe(125.00);
});

test('variant display name formats correctly', function () {
    $category = Category::factory()->create();
    $brand = Brand::factory()->create();

    $product = Product::factory()->create([
        'category_id' => $category->id,
        'brand_id' => $brand->id,
    ]);

    // Test with size and color
    $variant1 = ProductVariant::create([
        'product_id' => $product->id,
        'variant_name' => 'Medium Blue',
        'size' => 'M',
        'color' => 'Blue',
        'variant_sku' => 'TEST-001-V001',
        'price_adjustment' => 0,
        'stock_quantity' => 10,
    ]);

    // Test with model only
    $variant2 = ProductVariant::create([
        'product_id' => $product->id,
        'variant_name' => 'Pro Model',
        'model' => 'Pro',
        'variant_sku' => 'TEST-001-V002',
        'price_adjustment' => 0,
        'stock_quantity' => 10,
    ]);

    // Test with variant name only
    $variant3 = ProductVariant::create([
        'product_id' => $product->id,
        'variant_name' => 'Standard',
        'variant_sku' => 'TEST-001-V003',
        'price_adjustment' => 0,
        'stock_quantity' => 10,
    ]);

    expect($variant1->getDisplayName())->toBe('Size: M | Color: Blue');
    expect($variant2->getDisplayName())->toBe('Model: Pro');
    expect($variant3->getDisplayName())->toBe('Standard');
});
