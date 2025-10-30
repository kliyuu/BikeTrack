<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\InventoryLevel;
use App\Models\Location;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class InventorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Creates products with random variants and inventory levels.
     * Each product gets 1-3 variants with different types (size_color, model, or standard).
     * Each variant gets inventory levels distributed across warehouses with random stock quantities.
     */
    public function run(): void
    {
        $brands = Brand::all();
        $warehouse = Warehouse::all();

        $products = [
            [
                'name' => 'Yamaha Engine Oil',
                'sku' => 'BT-'.strtoupper(Str::random(8)),
                'description' => 'High-quality engine oil for Yamaha motorcycles',
                'unit_price' => 450.00,
                'category_id' => 1, // Engine Parts
                'brand_id' => $brands->where('name', 'Yamaha')->first()->id,
                'is_active' => 1,
            ],
            [
                'name' => 'Generic Motorcycle Tire',
                'sku' => 'BT-'.strtoupper(Str::random(8)),
                'description' => 'Durable tire for various motorcycle models',
                'unit_price' => 1200.00,
                'category_id' => 2, // Tires
                'brand_id' => $brands->where('name', 'Generic')->first()->id,
                'is_active' => 1,
            ],
            [
                'name' => 'Honda Brake Pads',
                'sku' => 'BT-'.strtoupper(Str::random(8)),
                'description' => 'Reliable brake pads for Honda motorcycles',
                'unit_price' => 800.00,
                'category_id' => 3, // Brake Parts
                'brand_id' => $brands->where('name', 'Honda')->first()->id,
                'is_active' => 1,
            ],
            [
                'name' => 'Kawasaki Suspension Kit',
                'sku' => 'BT-'.strtoupper(Str::random(8)),
                'description' => 'Complete suspension kit for Kawasaki motorcycles',
                'unit_price' => 300.00,
                'category_id' => 4, // Suspension
                'brand_id' => $brands->where('name', 'Kawasaki')->first()->id,
                'is_active' => 0,
            ],
        ];

        // Define variant options for different product types
        $variantOptions = [
            'sizes' => ['Small', 'Medium', 'Large', 'XL', '26"', '27"', '28"', '700c'],
            'colors' => ['Red', 'Blue', 'Black', 'White', 'Silver', 'Green', 'Yellow'],
            'models' => ['Standard', 'Premium', 'Sport', 'Touring', 'Racing', 'Classic'],
            'types' => ['size_color', 'model', 'standard'],
        ];

        foreach ($products as $productData) {
            $product = Product::create($productData);

            ProductImage::create([
                'product_id' => $product->id,
                'url' => "https://picsum.photos/seed/{$product->id}/200",
                'alt_text' => "{$product->name} Image",
                'is_primary' => true,
            ]);

            // Create location
            $location = Location::create([
                'floor' => Arr::random(['first', 'second']),
                'aisle' => rand(1, 10),
                'shelf' => rand(1, 5),
            ]);

            $product->location()->associate($location);
            $product->save();

            // Create random variants for each product (1-3 variants)
            $variantCount = rand(1, 3);

            for ($i = 0; $i < $variantCount; $i++) {
                $variantType = Arr::random($variantOptions['types']);
                $variantData = $this->generateVariantData($product, $variantType, $variantOptions, $i + 1);

                $variant = ProductVariant::create($variantData);

                // Create inventory levels for this variant across warehouses
                $totalStock = 0;
                foreach ($warehouse as $wh) {
                    // Not all variants will be in all warehouses (random distribution)
                    if (rand(1, 100) <= 100) { // 100% chance to be in this warehouse
                        $stock = rand(0, 25);
                        $reserved = $stock > 5 ? rand(0, min(5, intval($stock * 0.3))) : 0;

                        InventoryLevel::create([
                            'product_id' => $product->id,
                            'product_variant_id' => $variant->id,
                            'warehouse_id' => $wh->id,
                            'quantity' => $stock,
                            'reserved_quantity' => 0,
                        ]);

                        $totalStock += $stock;
                    }
                }

                // Update variant cached stock
                $variant->update(['cached_stock' => $totalStock]);
            }
        }
    }

    /**
     * Generate variant data based on variant type
     */
    private function generateVariantData(Product $product, string $variantType, array $options, int $index): array
    {
        $baseData = [
            'product_id' => $product->id,
            'variant_type' => $variantType,
            'price_adjustment' => rand(-100, 200) / 10, // Random price adjustment between -10.00 and 20.00
            'low_stock_threshold' => rand(3, 10),
            'cached_stock' => 0, // Will be updated after inventory levels are created
            'is_active' => rand(1, 100) <= 85, // 85% chance to be active
        ];

        switch ($variantType) {
            case 'size_color':
                $size = Arr::random($options['sizes']);
                $color = Arr::random($options['colors']);
                $baseData['variant_name'] = "{$size} {$color}";
                $baseData['size'] = $size;
                $baseData['color'] = $color;
                $baseData['variant_sku'] = $product->sku.'-'.strtoupper(substr($size, 0, 1)).strtoupper(substr($color, 0, 1));
                break;

            case 'model':
                $model = Arr::random($options['models']);
                $baseData['variant_name'] = $model;
                $baseData['model'] = $model;
                $baseData['variant_sku'] = $product->sku.'-'.strtoupper(substr($model, 0, 3));
                break;

            default: // standard
                $baseData['variant_name'] = 'Standard';
                $baseData['variant_sku'] = $product->sku.'-STD';
                if ($index > 1) {
                    $baseData['variant_name'] = "Standard V{$index}";
                    $baseData['variant_sku'] = $product->sku."-STD{$index}";
                }
                break;
        }

        return $baseData;
    }
}
