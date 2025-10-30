<?php

namespace App\Livewire\Admin\Inventory;

use App\Models\Product;
use App\Models\RestockHistory as RestockHistoryModel;
use App\Models\Warehouse;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Restock History')]
class RestockHistory extends Component
{
    use WithPagination;

    public $search = '';

    public $warehouseFilter = '';

    public $productFilter = '';

    public $date_from = '';

    public $date_to = '';

    public $sortField = 'created_at';

    public $sortDirection = 'desc';

    public $perPage = 10;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingWarehouseFilter()
    {
        $this->resetPage();
    }

    public function updatingProductFilter()
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

    public function getRestockHistoriesProperty()
    {
        $query = RestockHistoryModel::with(['product.primaryImage', 'warehouse', 'performedBy']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('product', function ($productQuery) {
                    $productQuery->where('name', 'like', "%{$this->search}%")
                        ->orWhere('sku', 'like', "%{$this->search}%");
                })
                    ->orWhereHas('warehouse', function ($warehouseQuery) {
                        $warehouseQuery->where('name', 'like', "%{$this->search}%");
                    })
                    ->orWhere('reason', 'like', "%{$this->search}%");
            });
        }

        if ($this->warehouseFilter) {
            $query->where('warehouse_id', $this->warehouseFilter);
        }

        if ($this->productFilter) {
            $query->where('product_id', $this->productFilter);
        }

        if ($this->date_from) {
            $query->whereDate('created_at', '>=', $this->date_from);
        }

        if ($this->date_to) {
            $query->whereDate('created_at', '<=', $this->date_to);
        }

        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate($this->perPage);
    }

    public function getRestockStatsProperty()
    {
        $query = RestockHistoryModel::query();

        if ($this->warehouseFilter) {
            $query->where('warehouse_id', $this->warehouseFilter);
        }

        if ($this->productFilter) {
            $query->where('product_id', $this->productFilter);
        }

        return [
            'totalTransactions' => $query->count(),
            'totalRestockAmount' => $query->sum('quantity'),
        ];
    }

    public function getProductsProperty()
    {
        return Product::query()->orderBy('name')->limit(100)->get();
    }

    public function getWarehousesProperty()
    {
        return Warehouse::query()->orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.admin.inventory.restock-history', [
            'restockHistories' => $this->restockHistories,
            'products' => $this->products,
            'warehouses' => $this->warehouses,
            'restockStats' => $this->restockStats,
        ]);
    }
}
