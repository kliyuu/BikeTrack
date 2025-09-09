<?php

namespace App\Livewire\Admin\Inventory;

use App\Models\InventoryLevel;
use App\Models\Product;
use App\Models\RestockHistory;
use App\Models\Warehouse;
use App\Services\ProductService;
use Flux\Flux;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Stock Level')]
class StockLevel extends Component
{
  use WithPagination;

  protected ProductService $productService;

  public $search = '';
  public $warehouseFilter = '';
  public $stockFilter = 'all'; // all, low, out, in stock
  public $sortField = 'products.name';
  public $sortDirection = 'asc';
  public $perPage = 10;

  public $stockId = null;
  public $adjustedStock = null;
  public $warehouseId = '';
  public $quantity_change = '';
  public $adjustmentReason = '';

  public function boot(ProductService $productService)
  {
    $this->productService = $productService;
  }

  public function updatingSearch()
  {
    $this->resetPage();
  }

  public function updatingWarehouseFilter()
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

  public function openAdjustmentModal($id = null)
  {
    $this->resetForm();

    $this->validateIdOnly($id);

    $this->stockId = $id;

    $this->adjustedStock = Product::findOrFail($id);

    Flux::modal('adjustment-modal')->show();
  }

  public function saveStockAdjustment()
  {
    $this->validate([
      'warehouseId' => 'required|exists:warehouses,id',
      'quantity_change' => 'required|integer',
      'adjustmentReason' => 'required|string',
    ]);

    $warehouseId = $this->warehouseId;
    $quantityChange = (int) $this->quantity_change;

    $inventoryLevel = InventoryLevel::firstOrCreate(
      [
        'product_id' => $this->stockId,
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
      'product_id' => $this->stockId,
      'warehouse_id' => $warehouseId,
      'quantity_before' => $quantityBefore,
      'quantity_after' => max(0, $quantityAfter),
      'quantity_change' => $quantityChange,
      'reason' => $this->adjustmentReason,
      'performed_by' => Auth::id(),
    ]);

    $this->productService->updateCachedStock($this->adjustedStock);

    $this->dispatch(
      'notify',
      variant: 'success',
      title: 'Stock Adjusted',
      message: 'Stock adjusted successfully.',
    );

    $this->closeModal();
  }

  public function closeModal()
  {
    $this->resetForm();
    Flux::modals()->close();
  }

  public function getInventoryLevelsProperty()
  {
    $query = InventoryLevel::query()->with(['product.brand', 'product.category', 'warehouse'])
      ->join('products', 'inventory_levels.product_id', '=', 'products.id')
      ->join('warehouses', 'inventory_levels.warehouse_id', '=', 'warehouses.id')
      ->select('inventory_levels.*')
      ->search($this->search)
      ->warehouseFilter($this->warehouseFilter)
      ->stockFilter($this->stockFilter);

    $query->orderBy($this->sortField, $this->sortDirection);

    return $query->paginate($this->perPage);
  }

  public function getWarehousesProperty()
  {
    return Warehouse::orderBy('name')->get();
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

  private function resetForm()
  {
    $this->reset([
      'stockId',
      'warehouseId',
      'quantity_change',
      'adjustmentReason'
    ]);

    $this->resetValidation();
  }

  private function validateIdOnly($id)
  {
    validator(
      ['id' => $id],
      ['id' => 'required|exists:inventory_levels,id']
    )->validate();
  }

  public function render()
  {
    return view('livewire.admin.inventory.stock-level', [
      'inventoryLevels' => $this->inventoryLevels,
      'warehouses' => $this->warehouses,
      'stockSummary' => $this->stockSummary
    ]);
  }
}
