<?php

namespace App\Livewire\Admin\Inventory;

use App\Models\InventoryLevel;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\RestockHistory;
use App\Models\Warehouse;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Product Variants')]
class ProductVariantManager extends Component
{
    public $product;

    public $variants = [];

    // Form fields for variant
    public $variantId = null;

    public $variant_type = 'size_color';

    public $variant_name = '';

    public $size = '';

    public $color = '';

    public $model = '';

    public $specifications = '';

    public $variant_sku = '';

    public $price_adjustment = 0.00;

    public $low_stock_threshold = 5;

    public $cached_stock = 0;

    public $is_active = true;

    // Stock adjustment fields
    public $adjustingVariant = null;

    public $warehouse_id = '';

    public $quantity_change = '';

    public $reason = 'manual_restock';

    protected $listeners = [
        'refreshVariants' => 'loadVariants',
    ];

    public function mount($productId)
    {
        $this->product = Product::findOrFail($productId);
        $this->loadVariants();
    }

    public function loadVariants()
    {
        $this->variants = $this->product->variants()
            ->with(['inventoryLevels.warehouse'])
            ->orderBy('variant_name')
            ->get();
    }

    public function openVariantModal($variantId = null)
    {
        $this->resetVariantForm();
        $this->variantId = $variantId;

        if ($variantId) {
            $variant = ProductVariant::findOrFail($variantId);
            $this->fill($variant->toArray());
        } else {
            $this->generateVariantSku();
        }

        Flux::modal('variant-modal')->show();
    }

    public function closeVariantModal()
    {
        $this->resetVariantForm();
        Flux::modals()->close();
    }

    public function saveVariant()
    {
        $this->validate([
            'variant_type' => 'required|string|max:50',
            'variant_name' => 'required|string|max:255',
            'variant_sku' => 'required|string|max:100|unique:product_variants,variant_sku,'.$this->variantId,
            'price_adjustment' => 'required|numeric',
            'low_stock_threshold' => 'required|integer|min:0',
            'cached_stock' => 'required|integer|min:0',
            'is_active' => 'boolean',
            'size' => 'nullable|string|max:50',
            'color' => 'nullable|string|max:50',
            'model' => 'nullable|string|max:100',
            'specifications' => 'nullable|string',
        ]);

        $data = [
            'product_id' => $this->product->id,
            'variant_type' => $this->variant_type,
            'variant_name' => $this->variant_name,
            'size' => $this->size ?: null,
            'color' => $this->color ?: null,
            'model' => $this->model ?: null,
            'specifications' => $this->specifications ?: null,
            'variant_sku' => $this->variant_sku,
            'price_adjustment' => $this->price_adjustment,
            'low_stock_threshold' => $this->low_stock_threshold,
            'cached_stock' => $this->cached_stock,
            'is_active' => $this->is_active,
        ];

        if ($this->variantId) {
            $variant = ProductVariant::findOrFail($this->variantId);
            $variant->update($data);
            $message = 'Variant updated successfully';
        } else {
            ProductVariant::create($data);
            $message = 'Variant created successfully';
        }

        $this->closeVariantModal();
        $this->loadVariants();

        $this->dispatch('notify', [
            'variant' => 'success',
            'title' => 'Success',
            'message' => $message,
        ]);
    }

    public function deleteVariant($variantId)
    {
        $variant = ProductVariant::findOrFail($variantId);

        // Check if variant is used in orders or cart
        if ($variant->orderItems()->exists() || $variant->cartItems()->exists()) {
            $this->dispatch('notify', [
                'variant' => 'error',
                'title' => 'Cannot Delete',
                'message' => 'This variant is used in orders or cart items and cannot be deleted.',
            ]);

            return;
        }

        $variant->delete();
        $this->loadVariants();

        $this->dispatch('notify', [
            'variant' => 'success',
            'title' => 'Deleted',
            'message' => 'Variant deleted successfully',
        ]);
    }

    public function openStockModal($variantId)
    {
        $this->adjustingVariant = ProductVariant::findOrFail($variantId);
        $this->warehouse_id = '';
        $this->quantity_change = '';
        $this->reason = 'manual_restock';

        Flux::modal('stock-adjustment-modal')->show();
    }

    public function closeStockModal()
    {
        $this->adjustingVariant = null;
        $this->warehouse_id = '';
        $this->quantity_change = '';
        $this->reason = 'manual_restock';
        Flux::modals()->close();
    }

    public function saveStockAdjustment()
    {
        $this->validate([
            'warehouse_id' => 'required|exists:warehouses,id',
            'quantity_change' => 'required|integer',
            'reason' => 'required|string',
        ]);

        $warehouseId = $this->warehouse_id;
        $quantityChange = (int) $this->quantity_change;

        $inventoryLevel = InventoryLevel::firstOrCreate(
            [
                'product_id' => $this->adjustingVariant->product_id,
                'product_variant_id' => $this->adjustingVariant->id,
                'warehouse_id' => $warehouseId,
            ],
            ['quantity' => 0, 'reserved_quantity' => 0]
        );

        $quantityBefore = $inventoryLevel->quantity;
        $quantityAfter = max(0, $quantityBefore + $quantityChange);

        $inventoryLevel->update(['quantity' => $quantityAfter]);

        // Create restock history
        RestockHistory::create([
            'product_id' => $this->adjustingVariant->product_id,
            'product_variant_id' => $this->adjustingVariant->id,
            'warehouse_id' => $warehouseId,
            'quantity_before' => $quantityBefore,
            'quantity_after' => $quantityAfter,
            'quantity_change' => $quantityChange,
            'type' => $quantityChange > 0 ? 'in' : 'out',
            'reason' => $this->reason,
            'reference_type' => 'stock_adjustment',
            'reference_id' => null,
            'performed_by' => Auth::id(),
        ]);

        // Update cached stock
        $this->adjustingVariant->updateCachedStock();
        $this->loadVariants();

        $this->dispatch('notify', [
            'variant' => 'success',
            'title' => 'Stock Adjusted',
            'message' => 'Stock adjusted successfully.',
        ]);

        $this->closeStockModal();
    }

    private function resetVariantForm()
    {
        $this->variantId = null;
        $this->variant_type = 'size_color';
        $this->variant_name = '';
        $this->size = '';
        $this->color = '';
        $this->model = '';
        $this->specifications = '';
        $this->variant_sku = '';
        $this->price_adjustment = 0.00;
        $this->low_stock_threshold = 5;
        $this->cached_stock = 0;
        $this->is_active = true;
    }

    public function getWarehousesProperty()
    {
        return Warehouse::query()->orderBy('name')->get();
    }

    private function generateVariantSku()
    {
        $baseSkuCount = ProductVariant::where('product_id', $this->product->id)->count();
        $this->variant_sku = $this->product->sku.'-V'.str_pad($baseSkuCount + 1, 3, '0', STR_PAD_LEFT);
    }

    public function render()
    {
        return view('livewire.admin.inventory.product-variant-manager', [
            'warehouses' => $this->warehouses,
        ]);
    }
}
