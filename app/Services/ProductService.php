<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Warehouse;

class ProductService
{
  /**
   * Create a new class instance.
   */
  public function updateCachedStock(Product $product): void
  {
    $product->update(['cached_stock' => $product->getTotalStock()]);
  }

  public function generateLocationCode(Product $product, int $warehouseId): ?string
  {
    $warehouse = Warehouse::find($warehouseId);
    if (!$warehouse) {
      return null;
    }

    // Example format: WH{id}-U1-S01
    return "WH{$warehouse->id}-U1-S01";
  }
}
