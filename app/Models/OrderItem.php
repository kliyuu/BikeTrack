<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'variant_details',
        'warehouse_id',
        'quantity',
        'unit_price',
        'line_total',
    ];

    protected $casts = [
        'variant_details' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function returns()
    {
        return $this->hasMany(ReturnItem::class);
    }

    /**
     * Get the display name for this order item including variant info
     */
    public function getDisplayName(): string
    {
        $name = $this->product->name;

        if ($this->productVariant) {
            $name .= ' - '.$this->productVariant->getDisplayName();
        } elseif ($this->variant_details) {
            $details = [];
            if (isset($this->variant_details['size'])) {
                $details[] = "Size: {$this->variant_details['size']}";
            }
            if (isset($this->variant_details['color'])) {
                $details[] = "Color: {$this->variant_details['color']}";
            }
            if (isset($this->variant_details['model'])) {
                $details[] = "Model: {$this->variant_details['model']}";
            }

            if (! empty($details)) {
                $name .= ' - '.implode(' | ', $details);
            }
        }

        return $name;
    }
}
