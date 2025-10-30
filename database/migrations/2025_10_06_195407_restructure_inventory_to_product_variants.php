<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, add stock management fields to product_variants
        Schema::table('product_variants', function (Blueprint $table) {
            $table->integer('low_stock_threshold')->default(5)->after('stock_quantity');
            $table->integer('cached_stock')->default(0)->after('low_stock_threshold');
        });

        // Create a backup of current inventory levels
        DB::statement('CREATE TEMPORARY TABLE temp_inventory_levels AS SELECT * FROM inventory_levels');

        // Add product_variant_id to inventory_levels
        Schema::table('inventory_levels', function (Blueprint $table) {
            $table->foreignId('product_variant_id')->nullable()->after('product_id')->constrained('product_variants')->onDelete('cascade');
            $table->index(['product_variant_id', 'warehouse_id']);
        });

        // Migrate existing inventory data to default variants
        // First, create default variants for products that don't have any
        $productsWithoutVariants = DB::table('products')
            ->leftJoin('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->whereNull('product_variants.id')
            ->select('products.*')
            ->get();

        foreach ($productsWithoutVariants as $product) {
            // Create a default variant for this product
            $variantId = DB::table('product_variants')->insertGetId([
                'product_id' => $product->id,
                'variant_type' => 'standard',
                'variant_name' => 'Standard',
                'variant_sku' => $product->sku.'-STD',
                'price_adjustment' => 0,
                'stock_quantity' => $product->cached_stock ?? 0,
                'low_stock_threshold' => $product->low_stock_threshold ?? 5,
                'cached_stock' => $product->cached_stock ?? 0,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update inventory levels to reference the new default variant
            DB::table('inventory_levels')
                ->where('product_id', $product->id)
                ->update(['product_variant_id' => $variantId]);
        }

        // For existing products with variants, distribute stock evenly or assign to first variant
        $productsWithVariants = DB::table('products')
            ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->select('products.id as product_id', 'products.cached_stock', 'products.low_stock_threshold')
            ->groupBy('products.id', 'products.cached_stock', 'products.low_stock_threshold')
            ->get();

        foreach ($productsWithVariants as $product) {
            $variants = DB::table('product_variants')
                ->where('product_id', $product->product_id)
                ->get();

            if ($variants->count() > 0) {
                // Update the first variant with existing stock data
                $firstVariant = $variants->first();
                DB::table('product_variants')
                    ->where('id', $firstVariant->id)
                    ->update([
                        'low_stock_threshold' => $product->low_stock_threshold ?? 5,
                        'cached_stock' => $product->cached_stock ?? 0,
                    ]);

                // Update inventory levels to reference the first variant
                DB::table('inventory_levels')
                    ->where('product_id', $product->product_id)
                    ->whereNull('product_variant_id')
                    ->update(['product_variant_id' => $firstVariant->id]);
            }
        }

        // Make product_variant_id required and remove product_id dependency
        Schema::table('inventory_levels', function (Blueprint $table) {
            $table->foreignId('product_variant_id')->nullable(false)->change();
        });

        // Finally, remove stock-related fields from products table
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['cached_stock', 'low_stock_threshold']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add back stock fields to products table
        Schema::table('products', function (Blueprint $table) {
            $table->integer('cached_stock')->default(0)->comment('Cached stock quantity for performance');
            $table->integer('low_stock_threshold')->default(0)->comment('Threshold for low stock alerts');
        });

        // Migrate stock data back to products from variants
        $products = DB::table('products')->get();
        foreach ($products as $product) {
            $totalStock = DB::table('product_variants')
                ->where('product_id', $product->id)
                ->sum('cached_stock');

            $minThreshold = DB::table('product_variants')
                ->where('product_id', $product->id)
                ->min('low_stock_threshold');

            DB::table('products')
                ->where('id', $product->id)
                ->update([
                    'cached_stock' => $totalStock ?? 0,
                    'low_stock_threshold' => $minThreshold ?? 5,
                ]);
        }

        // Remove variant-specific inventory management
        Schema::table('inventory_levels', function (Blueprint $table) {
            $table->dropForeign(['product_variant_id']);
            $table->dropIndex(['product_variant_id', 'warehouse_id']);
            $table->dropColumn('product_variant_id');
        });

        // Remove stock fields from product_variants
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn(['low_stock_threshold', 'cached_stock']);
        });
    }
};
