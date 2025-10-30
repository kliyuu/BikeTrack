<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'sku',
        'description',
        'unit_price',
        'category_id',
        'brand_id',
        'is_active',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
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
        return $this->hasManyThrough(InventoryLevel::class, ProductVariant::class);
    }

    public function restockHistories()
    {
        return $this->hasManyThrough(RestockHistory::class, ProductVariant::class, 'product_id', 'product_variant_id');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    /**
     * Get total stock across all variants
     */
    public function getTotalStock(): int
    {
        return $this->variants()->sum('cached_stock');
    }

    /**
     * Get available stock across all variants
     */
    public function getAvailableStock(): int
    {
        return $this->variants->sum(fn ($variant) => $variant->getAvailableStock());
    }

    /**
     * Get cached stock (aggregate from all variants)
     */
    public function getCachedStockAttribute(): int
    {
        return $this->variants()->sum('cached_stock');
    }

    /**
     * Check if any variant is low on stock
     */
    public function hasLowStock(): bool
    {
        return $this->variants()->where('cached_stock', '>', 0)
            ->whereColumn('cached_stock', '<=', 'low_stock_threshold')
            ->exists();
    }

    /**
     * Check if all variants are out of stock
     */
    public function isOutOfStock(): bool
    {
        return $this->variants()->sum('cached_stock') <= 0;
    }

    /**
     * Get the lowest stock threshold among variants
     */
    public function getLowStockThresholdAttribute(): int
    {
        return $this->variants()->min('low_stock_threshold') ?? 5;
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
