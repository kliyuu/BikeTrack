<?php

namespace App\Services;

use App\Models\InventoryLevel;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\RestockHistory;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductService
{
    /**
     * Create a new class instance.
     */
    public function updateCachedStock(ProductVariant $productVariant): void
    {
        $productVariant->update(['cached_stock' => $productVariant->getAvailableStock()]);
    }

    // Reserve stock (at order placement)
    public function reserveStock(ProductVariant $productVariant, int $warehouseId, int $quantity)
    {
        try {
            DB::transaction(function () use ($productVariant, $warehouseId, $quantity) {
                $inventoryLevel = InventoryLevel::query()
                    ->where('product_variant_id', $productVariant->id)
                    ->where('warehouse_id', $warehouseId)
                    ->lockForUpdate()
                    ->first();

                if (! $inventoryLevel || ($inventoryLevel->quantity - $inventoryLevel->reserved_quantity < $quantity)) {
                    throw new \Exception("Insufficient stock to reserve for product {$productVariant->name} in warehouse ID {$warehouseId}");
                }

                $inventoryLevel->increment('reserved_quantity', $quantity);

                $this->updateCachedStock($productVariant);
            });
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    public function confirmStock(ProductVariant $productVariant, int $warehouseId, int $quantity, $orderNumber = null)
    {
        DB::transaction(function () use ($productVariant, $warehouseId, $quantity, $orderNumber) {
            $inventoryLevel = InventoryLevel::query()
                ->where('product_variant_id', $productVariant->id)
                ->where('warehouse_id', $warehouseId)
                ->lockForUpdate()
                ->first();

            if (! $inventoryLevel || ($inventoryLevel->reserved_quantity < $quantity)) {
                throw new \Exception("Not enough reserved stock to confirm for {$productVariant->variant_name}");
            }

            // Calculate the quantity change
            $quantityBefore = $inventoryLevel->quantity;
            $quantityAfter = max(0, $quantityBefore - $quantity);

            // Update the inventory levels
            $inventoryLevel->decrement('reserved_quantity', $quantity);
            $inventoryLevel->decrement('quantity', $quantity);

            // Restock history entry
            RestockHistory::create([
                'product_id' => $productVariant->product_id,
                'product_variant_id' => $productVariant->id,
                'warehouse_id' => $warehouseId,
                'quantity_before' => $quantityBefore,
                'quantity_after' => $quantityAfter,
                'quantity_change' => $quantity,
                'type' => 'out',
                'reason' => 'order_fulfillment',
                'reference_type' => 'order',
                'reference_id' => $orderNumber,
                'performed_by' => Auth::id(),
            ]);

            $this->updateCachedStock($productVariant);
        });
    }

    // Release reserved stock (at order cancellation)
    public function releaseReservedStock(ProductVariant $productVariant, int $warehouseId, int $quantity)
    {
        DB::transaction(function () use ($productVariant, $warehouseId, $quantity) {
            $inventoryLevel = InventoryLevel::query()
                ->where('product_variant_id', $productVariant->id)
                ->where('warehouse_id', $warehouseId)
                ->lockForUpdate()
                ->first();

            if (! $inventoryLevel || ($inventoryLevel->reserved_quantity < $quantity)) {
                throw new \Exception("Cannot release more stock than reserved for {$productVariant->variant_name}");
            }

            $inventoryLevel->decrement('reserved_quantity', $quantity);

            $this->updateCachedStock($productVariant);
        });
    }

    public function generateLocationCode(Product $product, int $warehouseId): ?string
    {
        $warehouse = Warehouse::find($warehouseId);
        if (! $warehouse) {
            return null;
        }

        // Example format: WH{id}-U1-S01
        return "WH{$warehouse->id}-U1-S01";
    }
}
