<?php

namespace App\Livewire\Admin\Inventory;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Warehouse;
use App\Services\ProductService;
use Flux\Flux;
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

    public $is_active = true; // 1 for active, 0 for inactive

    public $floor = '';

    public $aisle = '';

    public $shelf = '';

    public $warehouse_location_id = '';

    // Stock modal
    public $adjustingProduct = null;

    public $warehouse_id = '';

    public $quantity_change = '';

    public $reason = 'manual_restock';

    // Barcode scanning
    public $barcodeSearch = '';

    public $item = null;

    protected $listeners = [
        'refreshProduct' => '$refresh',
        'productDeleted' => '$refresh',
        'barcodeScanned' => 'handleBarcodeScanned',
    ];

    public function boot(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function updatingSearch()
    {
        $this->resetPage();
        $this->item = null;
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
            $product = Product::with(['category', 'brand', 'location'])->findOrFail($id);
            $this->fill($product->toArray());

            if ($product->location) {
                $this->floor = $product->location->floor;
                $this->aisle = $product->location->aisle;
                $this->shelf = $product->location->shelf;
            }
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
            'sku' => 'required|string|max:100|unique:products,sku,'.$this->productId,
            'description' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'brand_id' => 'required|exists:brands,id',
            'category_id' => 'required|exists:categories,id',
            'is_active' => 'boolean',
            'primaryImage' => 'nullable|image|max:2048', // 2MB max
            'floor' => 'required|string|max:100',
            'aisle' => 'required|integer|min:1',
            'shelf' => 'required|integer|min:1',
        ]);

        $data = $this->only([
            'name',
            'sku',
            'description',
            'unit_price',
            'category_id',
            'brand_id',
            'is_active',
        ]);

        if ($this->productId) {
            $product = Product::findOrFail($this->productId);
            $product->update($data);

            $product->location()->update([
                'floor' => $this->floor,
                'aisle' => $this->aisle,
                'shelf' => $this->shelf,
            ]);
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

            $product->location()->create([
                'floor' => $this->floor,
                'aisle' => $this->aisle,
                'shelf' => $this->shelf,
            ]);
        }

        $this->dispatch('refreshProduct');
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
        // Redirect to variant management for stock adjustment
        return redirect()->route('admin.products.variants', $id);
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

    // Barcode Scanner
    public function handleBarcodeScanned($barcode)
    {
        $this->barcodeSearch = $barcode;
        $this->search = $barcode;
        $this->resetPage();
    }

    public function getProductsProperty()
    {
        $query = Product::with(['category', 'brand', 'variants.inventoryLevels', 'primaryImage', 'location']);

        if ($this->search) {
            $query->where('name', 'like', "%{$this->search}%")
                ->orWhere('sku', 'like', "%{$this->search}%")
                ->orWhereHas('variants', function ($q) {
                    $q->where('variant_sku', 'like', "%{$this->search}%");
                });

            $this->item = $query->first();
        }

        if ($this->categoryFilter) {
            $query->where('category_id', $this->categoryFilter);
        }

        if ($this->brandFilter) {
            $query->where('brand_id', $this->brandFilter);
        }

        switch ($this->stockFilter) {
            case 'low_stock':
                $query->whereHas('variants', function ($q) {
                    $q->where('cached_stock', '>', 0)
                        ->whereColumn('cached_stock', '<=', 'low_stock_threshold');
                });
                break;
            case 'in_stock':
                $query->whereHas('variants', function ($q) {
                    $q->where('cached_stock', '>', 0);
                });
                break;
            case 'out_of_stock':
                $query->whereDoesntHave('variants', function ($q) {
                    $q->where('cached_stock', '>', 0);
                });
                break;
            default:
                // No filter applied
                break;
        }

        if ($this->sortField === 'category.name') {
            // Join the categories table for sorting
            $query->leftJoin('categories', 'products.category_id', '=', 'categories.id')
                ->select('products.*') // Prevent column ambiguity
                ->orderBy('categories.name', $this->sortDirection);
        } elseif ($this->sortField === 'cached_stock') {
            // Sort by total stock from variants
            $query->withSum('variants', 'cached_stock')
                ->orderBy('variants_sum_cached_stock', $this->sortDirection);
        } else {
            $query->orderBy($this->sortField, $this->sortDirection);
        }

        return $query->paginate($this->perPage);
    }

    public function getStockSummaryProperty()
    {
        $totalProducts = Product::count();

        // Count products based on their variants' stock status
        $lowStock = Product::whereHas('variants', function ($q) {
            $q->where('cached_stock', '>', 0)
                ->whereColumn('cached_stock', '<=', 'low_stock_threshold');
        })->count();

        $inStock = Product::whereHas('variants', function ($q) {
            $q->where('cached_stock', '>', 0);
        })->count();

        $outOfStock = Product::whereDoesntHave('variants', function ($q) {
            $q->where('cached_stock', '>', 0);
        })->count();

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
            $sku = 'BT-'.strtoupper(Str::random(8));
        } while (Product::where('sku', $sku)->exists());

        return $sku;
    }

    public function generateSku()
    {
        $this->sku = $this->makeUniqueSku();
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
            'is_active',
            'primaryImage',
            'warehouse_id',
            'quantity_change',
            'reason',
            'floor',
            'aisle',
            'shelf',
        ]);

        $this->reason = 'manual_restock';

        // Reset the validation state
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.admin.inventory.product-manager', [
            'products' => $this->products,
            'brands' => $this->brands,
            'categories' => $this->categories,
            'warehouses' => $this->warehouses,
            'stockSummary' => $this->stockSummary,
        ]);
    }
}
