<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = Brand::all();

        $products = [
            [
                'name' => 'Yamaha Engine Oil',
                'sku' => 'YEO-001',
                'description' => 'High-quality engine oil for Yamaha motorcycles',
                'unit_price' => 450.00,
                'category_id' => 1, // Engine Parts
                'brand_id' => $brands->where('name', 'Yamaha')->first()->id,
                'is_active' => 1,
            ],
            [
                'name' => 'Generic Motorcycle Tire',
                'sku' => 'GMT-001',
                'description' => 'Durable tire for various motorcycle models',
                'unit_price' => 1200.00,
                'category_id' => 2, // Tires
                'brand_id' => $brands->where('name', 'Generic')->first()->id,
                'is_active' => 1,
            ],
            [
                'name' => 'Honda Brake Pads',
                'sku' => 'HBP-001',
                'description' => 'Reliable brake pads for Honda motorcycles',
                'unit_price' => 800.00,
                'category_id' => 3, // Brake Parts
                'brand_id' => $brands->where('name', 'Honda')->first()->id,
                'is_active' => 1,
            ],
            [
                'name' => 'Kawasaki Suspension Kit',
                'sku' => 'KCL-001',
                'description' => 'Complete suspension kit for Kawasaki motorcycles',
                'unit_price' => 300.00,
                'category_id' => 4, // Suspension
                'brand_id' => $brands->where('name', 'Kawasaki')->first()->id,
                'is_active' => 0,
            ],
        ];

        foreach ($products as $productData) {
            $product = Product::create($productData);

            ProductImage::create([
                'product_id' => $product->id,
                'url' => '/images/products/placeholder-'.strtolower(str_replace(' ', '-', $product->name)).'.jpg',
                'alt_text' => "{$product->name} Image",
                'is_primary' => true,
            ]);
        }
    }
}
