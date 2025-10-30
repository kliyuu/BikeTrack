<?php

namespace App\Livewire\Admin\Reports;

use App\Models\Brand;
use App\Models\Category;
use App\Models\InventoryLevel;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\RestockHistory;
use App\Models\Warehouse;
use App\Services\InventoryReportService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Inventory Report')]
class InventoryReport extends Component
{
    public $dateFrom;

    public $dateTo;

    public $dateRange = [];

    public $warehouseFilter = '';

    public $categoryFilter = '';

    public $brandFilter = '';

    public $stockFilter = 'all'; // all, low, out, available

    public $period = 'month';

    public $showExportModal = false;

    public $exportFormat = 'pdf';

    public function mount()
    {
        $this->setDateRange();
    }

    public function updatedPeriod()
    {
        $this->setDateRange();
    }

    public function getInventoryStatsProperty()
    {
        $baseQuery = InventoryLevel::query();

        if ($this->warehouseFilter) {
            $baseQuery->where('warehouse_id', $this->warehouseFilter);
        }

        $stats = [
            'total_products' => Product::query()->count(),
            'total_stock_value' => 0,
            'low_stock_count' => 0,
            'out_of_stock_count' => 0,
            'total_stock_quantity' => 0,
        ];

        // Calculate stock statistics
        $inventoryLevels = $baseQuery->with(['product', 'productVariant'])
            ->whereHas('product') // Ensure product exists
            ->whereHas('productVariant') // Ensure product variant exists
            ->get();

        foreach ($inventoryLevels as $level) {
            $stats['total_stock_quantity'] += $level->quantity;

            // Check if product and productVariant exist before accessing their properties
            if ($level->product && $level->productVariant) {
                $unitPrice = $level->product->unit_price ?? 0;
                $priceAdjustment = $level->productVariant->price_adjustment ?? 0;
                $stats['total_stock_value'] += $level->quantity * ($unitPrice + $priceAdjustment);

                if ($level->quantity == 0) {
                    $stats['out_of_stock_count']++;
                } elseif ($level->quantity <= ($level->productVariant->low_stock_threshold ?? 0)) {
                    $stats['low_stock_count']++;
                }
            }
        }

        return $stats;
    }

    public function getStockMovementsProperty()
    {
        $query = RestockHistory::with(['product', 'warehouse'])
          // ->selectRaw('DATE(created_at) as date, type, SUM(quantity) as total_quantity')
            ->selectRaw("strftime('%Y-%m-%d', created_at) as date, type, SUM(quantity_after) as total_quantity")
            ->groupBy(['date', 'type']);

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }
        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }
        if ($this->warehouseFilter) {
            $query->where('warehouse_id', $this->warehouseFilter);
        }

        return $query->orderBy('date')->get()->groupBy('date');
    }

    public function getTopCategoriesProperty()
    {
        $query = Category::withCount('products')
            ->orderByDesc('products_count')
            ->limit(10);

        $categories = $query->get();

        // Calculate total stock for each category
        foreach ($categories as $category) {
            $stockQuery = ProductVariant::whereHas('product', function ($query) use ($category) {
                $query->where('category_id', $category->id)->where('is_active', true);
            })->where('is_active', true);

            if ($this->warehouseFilter) {
                $stockQuery->whereHas('inventoryLevels', function ($query) {
                    $query->where('warehouse_id', $this->warehouseFilter);
                });
                $category->total_stock = $stockQuery->with([
                    'inventoryLevels' => function ($query) {
                        $query->where('warehouse_id', $this->warehouseFilter);
                    },
                ])->get()->sum(function ($variant) {
                    return $variant->inventoryLevels->sum('quantity');
                });
            } else {
                $category->total_stock = $stockQuery->sum('cached_stock');
            }
        }

        return $categories;
    }

    public function getTopBrandsProperty()
    {
        $query = Brand::withCount([
            'products' => function ($query) {
                $query->where('is_active', true); // exclude inactive products
            },
        ])
            ->orderByDesc('products_count')
            ->limit(10);

        $brands = $query->get();

        // Calculate total stock for each brand
        foreach ($brands as $brand) {
            $stockQuery = ProductVariant::whereHas('product', function ($query) use ($brand) {
                $query->where('brand_id', $brand->id)->where('is_active', true);
            })->where('is_active', true);

            if ($this->warehouseFilter) {
                $stockQuery->whereHas('inventoryLevels', function ($query) {
                    $query->where('warehouse_id', $this->warehouseFilter);
                });
                $brand->total_stock = $stockQuery->with([
                    'inventoryLevels' => function ($query) {
                        $query->where('warehouse_id', $this->warehouseFilter);
                    },
                ])->get()->sum(function ($variant) {
                    return $variant->inventoryLevels->sum('quantity');
                });
            } else {
                $brand->total_stock = $stockQuery->sum('cached_stock');
            }
        }

        return $brands;
    }

    public function getLowStockProductsProperty()
    {
        $query = ProductVariant::with(['product.category', 'product.brand', 'product.primaryImage'])
            ->whereHas('product', function ($query) {
                $query->where('is_active', true);
            })
            ->where('is_active', true)
            ->where('cached_stock', '>', 0)
            ->whereColumn('cached_stock', '<=', 'low_stock_threshold');

        // Apply warehouse filter if specified
        // if ($this->warehouseFilter) {
        //     $query->whereHas('inventoryLevels', function ($q) {
        //         $q->where('warehouse_id', $this->warehouseFilter)
        //             ->where('quantity', '>', 0);
        //     })->whereHas('inventoryLevels', function ($q) {
        //         $q->where('warehouse_id', $this->warehouseFilter)
        //             ->whereRaw('quantity <= (SELECT low_stock_threshold FROM product_variants WHERE id = inventory_levels.product_variant_id)');
        //     });
        // }

        return $query->orderBy('cached_stock', 'asc')
            ->limit(20)
            ->get();
    }

    public function getOutOfStockProductsProperty()
    {
        $query = ProductVariant::with(['product.category', 'product.brand', 'product.primaryImage'])
            ->whereHas('product', function ($query) {
                $query->where('is_active', true);
            })
            ->where('is_active', true)
            ->where('cached_stock', '=', 0);

        // Apply warehouse filter if specified
        if ($this->warehouseFilter) {
            $query->whereHas('inventoryLevels', function ($q) {
                $q->where('warehouse_id', $this->warehouseFilter)
                    ->where('quantity', '=', 0);
            });
        }

        return $query->orderBy('variant_name')
            ->limit(20)
            ->get();
    }

    public function getWarehouseStockProperty()
    {
        $warehouses = Warehouse::with(['inventoryLevels.product', 'inventoryLevels.productVariant'])
            ->get();

        return $warehouses->map(function ($warehouse) {
            $totalProducts = $warehouse->inventoryLevels->where('product', '!=', null)->count();
            $totalStock = $warehouse->inventoryLevels->sum('quantity');
            $totalValue = $warehouse->inventoryLevels->sum(function ($level) {
                // Check if product and productVariant exist before accessing their properties
                if ($level->product && $level->productVariant) {
                    $unitPrice = $level->product->unit_price ?? 0;
                    $priceAdjustment = $level->productVariant->price_adjustment ?? 0;

                    return $level->quantity * ($unitPrice + $priceAdjustment);
                }

                return 0;
            });

            return [
                'warehouse' => $warehouse,
                'total_products' => $totalProducts,
                'total_stock' => $totalStock,
                'total_value' => $totalValue,
            ];
        });
    }

    public function getWarehousesProperty()
    {
        return Warehouse::query()->orderBy('name')->get();
    }

    public function getCategoriesProperty()
    {
        return Category::query()->orderBy('name')->get();
    }

    public function getBrandsProperty()
    {
        return Brand::query()->orderBy('name')->get();
    }

    public function openExportModal()
    {
        $this->showExportModal = true;
    }

    public function closeExportModal()
    {
        $this->showExportModal = false;
        $this->exportFormat = 'pdf';
    }

    public function exportReport()
    {
        try {
            $this->validate([
                'exportFormat' => 'required|in:pdf,csv',
            ]);

            $reportService = new InventoryReportService;

            $data = $reportService->generateReportData(
                $this->dateFrom,
                $this->dateTo,
                $this->warehouseFilter ?: null,
                $this->categoryFilter ?: null,
                $this->brandFilter ?: null
            );

            $this->closeExportModal();

            if ($this->exportFormat === 'pdf') {
                return $reportService->exportPdf($data);
            }

            return $reportService->exportCsv($data);
        } catch (\Exception $e) {
            $this->closeExportModal();
            session()->flash('error', 'Export failed: '.$e->getMessage());
            logger()->error('Export failed', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }

    private function setDateRange()
    {
        switch ($this->period) {
            case 'today':
                $this->dateFrom = Carbon::today()->toDateString();
                $this->dateTo = Carbon::today()->toDateString();
                $this->dateRange = [$this->dateFrom]; // single date
                break;

            case 'week':
                $start = Carbon::now()->startOfWeek();
                $end = Carbon::now()->endOfWeek();
                $this->dateFrom = $start->toDateString();
                $this->dateTo = $end->toDateString();
                $this->dateRange = collect(CarbonPeriod::create($start, $end))
                    ->map(fn ($date) => $date->toDateString())
                    ->toArray();
                break;

            case 'month':
                $start = Carbon::now()->startOfMonth();
                $end = Carbon::now()->endOfMonth();
                $this->dateFrom = $start->toDateString();
                $this->dateTo = $end->toDateString();
                $this->dateRange = collect(CarbonPeriod::create($start, $end))
                    ->map(fn ($date) => $date->toDateString())
                    ->toArray();
                break;

            case 'quarter':
                $start = Carbon::now()->startOfQuarter();
                $end = Carbon::now()->endOfQuarter();
                $this->dateFrom = $start->toDateString();
                $this->dateTo = $end->toDateString();
                $this->dateRange = collect(range(0, 2))
                    ->map(fn ($i) => $start->copy()->addMonths($i)->format('F'))
                    ->toArray();
                break;

            case 'year':
                $start = Carbon::now()->startOfYear();
                $end = Carbon::now()->endOfYear();
                $this->dateFrom = $start->toDateString();
                $this->dateTo = $end->toDateString();
                $this->dateRange = collect(range(1, 12))
                    ->map(fn ($month) => Carbon::createFromDate($start->year, $month, 1)->format('F'))
                    ->toArray();
                break;
        }
    }

    public function render()
    {
        return view('livewire.admin.reports.inventory-report', [
            'inventoryStats' => $this->inventoryStats,
            'lowStockProducts' => $this->lowStockProducts,
            'outOfStockProducts' => $this->outOfStockProducts,
            'topBrands' => $this->topBrands,
            'topCategories' => $this->topCategories,
            'stockMovements' => $this->stockMovements,
            'warehouseStock' => $this->warehouseStock,
            'warehouses' => $this->warehouses,
            'categories' => $this->categories,
            'brands' => $this->brands,
        ]);
    }
}
