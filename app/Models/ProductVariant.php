<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'variant_type',
        'variant_name',
        'variant_attributes',
        'size',
        'color',
        'model',
        'specifications',
        'variant_sku',
        'price_adjustment',
        'low_stock_threshold',
        'cached_stock',
        'is_active',
    ];

    protected $casts = [
        'variant_attributes' => 'array',
        'price_adjustment' => 'decimal:2',
        'low_stock_threshold' => 'integer',
        'cached_stock' => 'integer',
        'is_active' => 'boolean',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function inventoryLevels()
    {
        return $this->hasMany(InventoryLevel::class);
    }

    public function restockHistories()
    {
        return $this->hasMany(RestockHistory::class, 'product_variant_id');
    }

    /**
     * Get the final price for this variant
     */
    public function getFinalPrice(): float
    {
        return $this->product->unit_price + $this->price_adjustment;
    }

    /**
     * Get total stock across all warehouses for this variant
     */
    public function getTotalStock(): int
    {
        return $this->inventoryLevels()->sum('quantity');
    }

    /**
     * Get available stock (total - reserved) across all warehouses
     */
    public function getAvailableStock(): int
    {
        return $this->inventoryLevels->sum(fn ($level) => $level->quantity - $level->reserved_quantity);
    }

    /**
     * Check if variant is low on stock
     */
    public function isLowStock(): bool
    {
        return $this->cached_stock <= $this->low_stock_threshold && $this->cached_stock > 0;
    }

    /**
     * Check if variant is out of stock
     */
    public function isOutOfStock(): bool
    {
        return $this->cached_stock <= 0;
    }

    /**
     * Update cached stock from inventory levels
     */
    public function updateCachedStock(): void
    {
        $this->cached_stock = $this->getAvailableStock();
        $this->save();
    }

    /**
     * Get the variant display name
     */
    public function getDisplayName(): string
    {
        $parts = [];

        if ($this->size) {
            $parts[] = "Size: {$this->size}";
        }

        if ($this->color) {
            $parts[] = "Color: {$this->color}";
        }

        if ($this->model) {
            $parts[] = "Model: {$this->model}";
        }

        if (! empty($parts)) {
            return implode(' | ', $parts);
        }

        return $this->variant_name ?: 'Standard';
    }

    /**
     * Scope for active variants
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
