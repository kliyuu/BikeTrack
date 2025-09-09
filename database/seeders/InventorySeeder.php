<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\InventoryLevel;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Warehouse;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InventorySeeder extends Seeder
{
  /**
   * Run the database seeds.
   */
  public function run(): void
  {
    $brands = Brand::all();
    $warehouse = Warehouse::all();

    $products = [
      [
        'name' => 'Yamaha Engine Oil',
        'sku' => 'BT-' . strtoupper(Str::random(8)),
        'description' => 'High-quality engine oil for Yamaha motorcycles',
        'unit_price' => 450.00,
        'category_id' => 1, // Engine Parts
        'brand_id' => $brands->where('name', 'Yamaha')->first()->id,
        'is_active' => 1
      ],
      [
        'name' => 'Generic Motorcycle Tire',
        'sku' => 'BT-' . strtoupper(Str::random(8)),
        'description' => 'Durable tire for various motorcycle models',
        'unit_price' => 1200.00,
        'category_id' => 2, // Tires
        'brand_id' => $brands->where('name', 'Generic')->first()->id,
        'is_active' => 1
      ],
      [
        'name' => 'Honda Brake Pads',
        'sku' => 'BT-' . strtoupper(Str::random(8)),
        'description' => 'Reliable brake pads for Honda motorcycles',
        'unit_price' => 800.00,
        'category_id' => 3, // Brake Parts
        'brand_id' => $brands->where('name', 'Honda')->first()->id,
        'is_active' => 1
      ],
      [
        'name' => 'Kawasaki Suspension Kit',
        'sku' => 'BT-' . strtoupper(Str::random(8)),
        'description' => 'Complete suspension kit for Kawasaki motorcycles',
        'unit_price' => 300.00,
        'category_id' => 4, // Suspension
        'brand_id' => $brands->where('name', 'Kawasaki')->first()->id,
        'low_stock_threshold' => 5,
        'is_active' => 0
      ]
    ];

    foreach ($products as $productData) {
      $product = Product::create($productData);

      ProductImage::create([
        'product_id' => $product->id,
        'url' => "https://picsum.photos/seed/{$product->id}/200",
        'alt_text' => "{$product->name} Image",
        'is_primary' => true,
      ]);

      // Create inventory levels
      $stock = rand(2, 15);

      InventoryLevel::create([
        'product_id' => $product->id,
        'warehouse_id' => $warehouse[0]->id,
        'quantity' => $stock,
      ]);

      $product->update(['cached_stock' => $stock]);
    }
  }
}
