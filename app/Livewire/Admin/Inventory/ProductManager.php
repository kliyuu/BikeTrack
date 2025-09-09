<?php

namespace App\Livewire\Admin\Inventory;

use App\Models\Brand;
use App\Models\Category;
use App\Models\InventoryLevel;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\RestockHistory;
use App\Models\Warehouse;
use App\Services\ProductService;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Title('Products')]
class ProductManager extends Component
{
  use WithFileUploads, WithPagination;

  protected ProductService $productService;

  public $search = '';
  public $categoryFilter = '';
  public $brandFilter = '';
  public $stockFilter = 'all';
  public $sortField = 'name';
  public $sortDirection = 'asc';
  public $perPage = 10;

  // Product modal
  public $productId = null;
  public $name = '';
  public $sku = '';
  public $description = '';
  public $unit_price = 0.00;
  public $category_id = '';
  public $brand_id = '';
  public $primaryImage; // File upload for primary image
  public $cached_stock = 0;
  public $low_stock_threshold = 0;
  public $is_active = true; // 1 for active, 0 for inactive

  // Stock modal
  public $adjustingProduct = null;
  public $warehouse_id = '';
  public $quantity_change = '';
  public $reason = 'manual_restock';

  // Barcode scanning
  public $barcodeSearch = '';

  public function boot(ProductService $productService)
  {
    $this->productService = $productService;
  }

  public function updatingSearch()
  {
    $this->resetPage();
  }

  public function updatingCategoryFilter()
  {
    $this->resetPage();
  }

  public function updatingBrandFilter()
  {
    $this->resetPage();
  }

  public function updatingStockFilter()
  {
    $this->resetPage();
  }

  public function sortBy($field)
  {
    if ($this->sortField === $field) {
      $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
    } else {
      $this->sortField = $field;
      $this->sortDirection = 'asc';
    }

    $this->resetPage();
  }

  public function openProductModal($id = null)
  {
    // Reset the form
    $this->resetForm();

    $this->productId = $id;

    if ($id) {
      $product = Product::with(['category', 'brand'])->findOrFail($id);
      $this->fill($product->toArray());
    } else {
      $this->generateSku();
    }

    // Show the modal
    Flux::modal('product-modal')->show();
  }

  public function closeModal()
  {
    $this->resetForm();
    Flux::modals()->close();
  }

  public function saveProduct()
  {
    $this->is_active = filter_var($this->is_active, FILTER_VALIDATE_BOOLEAN);

    $this->validate([
      'name' => 'required|string|max:255',
      'sku' => 'required|string|max:100|unique:products,sku,' . $this->productId,
      'description' => 'nullable|string',
      'unit_price' => 'required|numeric|min:0',
      'brand_id' => 'required|exists:brands,id',
      'category_id' => 'required|exists:categories,id',
      'low_stock_threshold' => 'nullable|integer|min:0',
      'is_active' => 'boolean',
      'primaryImage' => 'nullable|image|max:2048', // 2MB max
    ]);

    $data = $this->only([
      'name',
      'sku',
      'description',
      'unit_price',
      'category_id',
      'brand_id',
      'low_stock_threshold',
      'is_active'
    ]);

    if ($this->productId) {
      $product = Product::findOrFail($this->productId);
      $product->update($data);
    } else {
      $product = Product::create($data);

      if ($this->primaryImage) {
        ProductImage::create([
          'product_id' => $product->id,
          'url' => $this->primaryImage->store("products/{$product->id}", 'public') ?? null,
          'alt_text' => $this->name,
          'is_primary' => true,
        ]);
      }
    }

    $this->dispatch('refreshProductList');
    $this->dispatch(
      'notify',
      variant: 'success',
      title: 'Product Saved',
      message: $this->productId ? 'Product updated successfully.' : 'Product created successfully.',
    );

    $this->closeModal();
    // Session::flash('message', $this->productId ? 'Product updated successfully.' : 'Product created successfully.');
  }

  public function openStockModal($id)
  {
    $this->resetForm();

    $this->validateOnlyId($id);

    $this->productId = $id;

    $this->adjustingProduct = Product::findOrFail($id);

    Flux::modal('restock-product')->show();
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
        'product_id' => $this->productId,
        'warehouse_id' => $warehouseId,
      ],
      ['quantity' => 0]
    );

    $quantityBefore = $inventoryLevel->quantity;
    $quantityAfter = $quantityBefore + $quantityChange;

    $inventoryLevel->update([
      'quantity' => max(0, $quantityAfter)
    ]);

    // Create a restock history entry
    RestockHistory::create([
      'product_id' => $this->productId,
      'warehouse_id' => $warehouseId,
      'quantity_before' => $quantityBefore,
      'quantity_after' => max(0, $quantityAfter),
      'quantity_change' => $quantityChange,
      'reason' => $this->reason,
      'performed_by' => Auth::id(),
    ]);

    $this->productService->updateCachedStock($this->adjustingProduct);

    $this->dispatch(
      'notify',
      variant: 'success',
      title: 'Stock Adjusted',
      message: 'Stock adjusted successfully.',
    );

    $this->closeModal();
  }

  public function confirmDelete($id)
  {
    $this->validateOnlyId($id);
    $this->productId = $id;

    Flux::modal('delete-product')->show();
  }

  public function deleteProduct()
  {
    $this->validate([
      'productId' => 'required|exists:products,id',
    ]);

    // Soft delete the product
    $product = Product::where('id', $this->productId)->first();
    $product->delete();

    $this->reset(['productId']);

    $this->dispatch(
      'notify',
      variant: 'success',
      title: 'Product Deleted',
      message: 'Product has been deleted.',
    );

    $this->dispatch('productDeleted');
    Flux::modal('delete-product')->close();

    // Session::flash('message', 'Product deleted successfully.');
  }

  private function resetForm()
  {
    // Reset the form and validation
    $this->reset([
      'productId',
      'adjustingProduct',
      'name',
      'sku',
      'description',
      'unit_price',
      'category_id',
      'brand_id',
      'primaryImage',
      'cached_stock',
      'low_stock_threshold',
      'is_active',
      'primaryImage',
      'warehouse_id',
      'quantity_change',
    ]);

    $this->reason = 'manual_restock';

    // Reset the validation state
    $this->resetValidation();
  }

  public function getProductsProperty()
  {
    $query = Product::with(['category', 'brand', 'inventoryLevels', 'primaryImage']);

    if ($this->search) {
      $query->where('name', 'like', "%{$this->search}%");
    }

    if ($this->categoryFilter) {
      $query->where('category_id', $this->categoryFilter);
    }

    if ($this->brandFilter) {
      $query->where('brand_id', $this->brandFilter);
    }

    switch ($this->stockFilter) {
      case 'low_stock':
        $query->whereColumn('cached_stock', '<=', 'low_stock_threshold');
        break;
      case 'in_stock':
        $query->where('cached_stock', '>', 0);
        break;
      case 'out_of_stock':
        $query->where('cached_stock', 0);
        break;
      default:
        // No filter applied
        break;
    }

    return $query->orderBy($this->sortField, $this->sortDirection)
      ->paginate($this->perPage);
  }

  public function getStockSummaryProperty()
  {
    $totalProducts = InventoryLevel::distinct('product_id')->count();
    $lowStock = InventoryLevel::query()
      ->whereHas('product', function ($q) {
        $q->whereColumn('inventory_levels.quantity', '<=', 'products.low_stock_threshold');
      })
      ->count();

    $inStock = InventoryLevel::where('quantity', '>', 0)->count();
    $outOfStock = InventoryLevel::where('quantity', 0)->count();

    return [
      'total_products' => $totalProducts,
      'low_stock' => $lowStock,
      'in_stock' => $inStock,
      'out_of_stock' => $outOfStock,
    ];
  }

  public function getBrandsProperty()
  {
    return Brand::query()->orderBy('name')->get();
  }

  public function getCategoriesProperty()
  {
    return Category::query()->orderBy('name')->get();
  }

  public function getWarehousesProperty()
  {
    return Warehouse::query()->orderBy('name')->get();
  }

  protected function validateOnlyId($id)
  {
    validator(
      ['id' => $id],
      ['id' => 'required|exists:products,id']
    )->validate();
  }

  private function makeUniqueSku()
  {
    do {
      $sku = 'BT-' . strtoupper(Str::random(8));
    } while (Product::where('sku', $sku)->exists());

    return $sku;
  }

  public function generateSku()
  {
    $this->sku = $this->makeUniqueSku();
  }

  public function render()
  {
    return view('livewire.admin.inventory.product-manager', [
      'products' => $this->products,
      'brands' => $this->brands,
      'categories' => $this->categories,
      'warehouses' => $this->warehouses,
      'stockSummary' => $this->stockSummary
    ]);
  }
}
