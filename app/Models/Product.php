<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
  use SoftDeletes;

  protected $fillable = [
    'name',
    'sku',
    'description',
    'unit_price',
    'category_id',
    'brand_id',
    'cached_stock',
    'low_stock_threshold',
    'is_active',
  ];

  protected $casts = [
    'unit_price' => 'decimal:2',
    'cached_stock' => 'integer',
    'low_stock_threshold' => 'integer',
    'is_active' => 'boolean',
  ];

  /**
   * Event listener for the force deleted model event used to perform post-delete cleanup.
   *
   * When a product is deleted, this method is called and performs the following:
   *  - Deletes the product's folder from the file system.
   *  - Deletes all of the product's images.
   */
  protected static function booted()
  {
    static::forceDeleted(function ($product) {
      Storage::disk('public')->deleteDirectory("products/{$product->id}");

      $product->images()->delete();
    });
  }

  public function category()
  {
    return $this->belongsTo(Category::class);
  }

  public function brand()
  {
    return $this->belongsTo(Brand::class);
  }

  public function images()
  {
    return $this->hasMany(ProductImage::class);
  }

  public function primaryImage()
  {
    return $this->hasOne(ProductImage::class)->where('is_primary', true);
  }

  public function secondaryImages()
  {
    return $this->hasMany(ProductImage::class)->where('is_primary', false);
  }

  public function inventoryLevels()
  {
    return $this->hasMany(InventoryLevel::class);
  }

  public function restockHistories()
  {
    return $this->hasMany(RestockHistory::class);
  }

  public function orderItems()
  {
    return $this->hasMany(OrderItem::class);
  }

  public function variants()
  {
    return $this->hasMany(ProductVariant::class);
  }

  public function getTotalStock(): int
  {
    return $this->inventoryLevels()->sum('quantity');
  }

  public function scopeActive(Builder $query)
  {
    return $query->where('is_active', true);
  }

  public function scopeInactive(Builder $query)
  {
    return $query->where('is_active', false);
  }
}
