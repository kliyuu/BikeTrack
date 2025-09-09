<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryLevel extends Model
{
  protected $fillable = ['product_id', 'warehouse_id', 'quantity'];

  public function product()
  {
    return $this->belongsTo(Product::class);
  }

  public function warehouse()
  {
    return $this->belongsTo(Warehouse::class);
  }

  public function scopeSearch($query, $term)
  {
    if (!$term)
      return $query;

    $searchTerm = "%{$term}%";

    return $query->where(function ($q) use ($searchTerm) {
      $q->where('products.name', 'like', $searchTerm)
        ->orWhere('products.sku', 'like', $searchTerm)
        ->orWhere('warehouses.name', 'like', $searchTerm);
    });
  }

  public function scopeWarehouseFilter($query, $warehouseId)
  {
    if (!$warehouseId)
      return $query;

    return $query->where('warehouse_id', $warehouseId);
  }

  public function scopeStockFilter($query, $filter)
  {
    switch ($filter) {
      case 'low_stock':
        return $query->whereHas('product', function ($q) {
          $q->whereColumn('inventory_levels.quantity', '<=', 'products.low_stock_threshold')
            ->where('inventory_levels.quantity', '>', 0);
        });
      case 'in_stock':
        return $query->where('quantity', '>', 0);
      case 'out_of_stock':
        return $query->where('quantity', 0);
      default:
        return $query;
    }
  }
}
