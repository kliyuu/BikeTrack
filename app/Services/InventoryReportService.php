<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Brand;
use App\Models\Category;
use App\Models\InventoryLevel;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Warehouse;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;

class InventoryReportService
{
    /**
     * Generate inventory report data
     */
    public function generateReportData(
        string $dateFrom,
        string $dateTo,
        ?string $warehouseFilter = null,
        ?string $categoryFilter = null,
        ?string $brandFilter = null
    ): array {
        return [
            'inventoryStats' => $this->getInventoryStats($warehouseFilter),
            'warehouseStock' => $this->getWarehouseStock(),
            'topCategories' => $this->getTopCategories($warehouseFilter),
            'topBrands' => $this->getTopBrands($warehouseFilter),
            'lowStockProducts' => $this->getLowStockProducts(),
            'outOfStockProducts' => $this->getOutOfStockProducts(),
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'warehouseFilter' => $warehouseFilter,
            'categoryFilter' => $categoryFilter,
            'brandFilter' => $brandFilter,
            'warehouses' => Warehouse::orderBy('name')->get(),
            'categories' => Category::orderBy('name')->get(),
            'brands' => Brand::orderBy('name')->get(),
        ];
    }

    /**
     * Export report as PDF
     */
    public function exportPdf(array $data)
    {
        $pdf = Pdf::loadView('exports.inventory-report-pdf', $data);
        $pdf->setPaper('a4', 'portrait');

        $filename = 'inventory-report-'.now()->format('Y-m-d-His').'.pdf';

        $content = $pdf->output();

        return response()->streamDownload(
            fn () => print ($content),
            $filename,
            ['Content-Type' => 'application/pdf']
        );
    }

    /**
     * Export report as CSV
     */
    public function exportCsv(array $data)
    {
        $filename = 'inventory-report-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function () use ($data) {
            $handle = fopen('php://output', 'w');

            // Add BOM for UTF-8 Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // Report Header
            fputcsv($handle, ['INVENTORY REPORT']);
            fputcsv($handle, ['Generated:', now()->format('F d, Y - h:i A')]);
            fputcsv($handle, ['Period:', $data['dateFrom'].' to '.$data['dateTo']]);

            if ($data['warehouseFilter']) {
                $warehouse = $data['warehouses']->find($data['warehouseFilter']);
                fputcsv($handle, ['Warehouse:', $warehouse?->name ?? 'All']);
            }
            if ($data['categoryFilter']) {
                $category = $data['categories']->find($data['categoryFilter']);
                fputcsv($handle, ['Category:', $category?->name ?? 'All']);
            }
            if ($data['brandFilter']) {
                $brand = $data['brands']->find($data['brandFilter']);
                fputcsv($handle, ['Brand:', $brand?->name ?? 'All']);
            }

            fputcsv($handle, []); // Empty line

            // Summary Statistics
            fputcsv($handle, ['SUMMARY STATISTICS']);
            fputcsv($handle, ['Metric', 'Value']);
            fputcsv($handle, ['Total Products', number_format($data['inventoryStats']['total_products'])]);
            fputcsv($handle, ['Total Stock Value', '₱'.number_format($data['inventoryStats']['total_stock_value'], 2)]);
            fputcsv($handle, ['Total Stock Quantity', number_format($data['inventoryStats']['total_stock_quantity'])]);
            fputcsv($handle, ['Low Stock Items', number_format($data['inventoryStats']['low_stock_count'])]);
            fputcsv($handle, ['Out of Stock Items', number_format($data['inventoryStats']['out_of_stock_count'])]);
            fputcsv($handle, []); // Empty line

            // Warehouse Stock Overview
            if ($data['warehouseStock']->count() > 0) {
                fputcsv($handle, ['WAREHOUSE STOCK OVERVIEW']);
                fputcsv($handle, ['Warehouse', 'Total Products', 'Total Stock', 'Total Value']);

                foreach ($data['warehouseStock'] as $stock) {
                    fputcsv($handle, [
                        $stock['warehouse']->name,
                        number_format($stock['total_products']),
                        number_format($stock['total_stock']),
                        '₱'.number_format($stock['total_value'], 2),
                    ]);
                }
                fputcsv($handle, []); // Empty line
            }

            // Top Categories
            if ($data['topCategories']->count() > 0) {
                fputcsv($handle, ['TOP CATEGORIES BY PRODUCT COUNT']);
                fputcsv($handle, ['Category', 'Product Count', 'Total Stock']);

                foreach ($data['topCategories'] as $category) {
                    fputcsv($handle, [
                        $category->name,
                        number_format($category->products_count),
                        number_format($category->products_sum_cached_stock ?? 0),
                    ]);
                }
                fputcsv($handle, []); // Empty line
            }

            // Top Brands
            if ($data['topBrands']->count() > 0) {
                fputcsv($handle, ['TOP BRANDS BY PRODUCT COUNT']);
                fputcsv($handle, ['Brand', 'Product Count', 'Total Stock']);

                foreach ($data['topBrands'] as $brand) {
                    fputcsv($handle, [
                        $brand->name,
                        number_format($brand->products_count),
                        number_format($brand->products_sum_cached_stock ?? 0),
                    ]);
                }
                fputcsv($handle, []); // Empty line
            }

            // Low Stock Products
            if ($data['lowStockProducts']->count() > 0) {
                fputcsv($handle, ['LOW STOCK PRODUCTS']);
                fputcsv($handle, ['SKU', 'Product Name', 'Category', 'Brand', 'Current Stock', 'Threshold', 'Status']);

                foreach ($data['lowStockProducts'] as $product) {
                    fputcsv($handle, [
                        $product->sku,
                        $product->name,
                        $product->category->name ?? 'N/A',
                        $product->brand->name ?? 'N/A',
                        number_format($product->cached_stock),
                        number_format($product->low_stock_threshold),
                        'Low Stock',
                    ]);
                }
                fputcsv($handle, []); // Empty line
            }

            // Out of Stock Products
            if ($data['outOfStockProducts']->count() > 0) {
                fputcsv($handle, ['OUT OF STOCK PRODUCTS']);
                fputcsv($handle, ['SKU', 'Product Name', 'Category', 'Brand', 'Status']);

                foreach ($data['outOfStockProducts'] as $product) {
                    fputcsv($handle, [
                        $product->sku,
                        $product->name,
                        $product->category->name ?? 'N/A',
                        $product->brand->name ?? 'N/A',
                        'Out of Stock',
                    ]);
                }
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Get inventory statistics
     */
    private function getInventoryStats(?string $warehouseFilter): array
    {
        $baseQuery = InventoryLevel::query();

        if ($warehouseFilter) {
            $baseQuery->where('warehouse_id', $warehouseFilter);
        }

        $stats = [
            'total_products' => Product::query()->count(),
            'total_stock_value' => 0,
            'low_stock_count' => 0,
            'out_of_stock_count' => 0,
            'total_stock_quantity' => 0,
        ];

        $inventoryLevels = $baseQuery->with('product', 'productVariant')
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

    /**
     * Get warehouse stock overview
     */
    private function getWarehouseStock(): Collection
    {
        $warehouses = Warehouse::with(['inventoryLevels.product', 'inventoryLevels.productVariant'])->get();

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

    /**
     * Get top categories
     */
    private function getTopCategories(?string $warehouseFilter): Collection
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

            if ($warehouseFilter) {
                $stockQuery->whereHas('inventoryLevels', function ($query) use ($warehouseFilter) {
                    $query->where('warehouse_id', $warehouseFilter);
                });
                $category->total_stock = $stockQuery->with(['inventoryLevels' => function ($query) use ($warehouseFilter) {
                    $query->where('warehouse_id', $warehouseFilter);
                }])->get()->sum(function ($variant) {
                    return $variant->inventoryLevels->sum('quantity');
                });
            } else {
                $category->total_stock = $stockQuery->sum('cached_stock');
            }
        }

        return $categories;
    }

    /**
     * Get top brands
     */
    private function getTopBrands(?string $warehouseFilter): Collection
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

            if ($warehouseFilter) {
                $stockQuery->whereHas('inventoryLevels', function ($query) use ($warehouseFilter) {
                    $query->where('warehouse_id', $warehouseFilter);
                });
                $brand->total_stock = $stockQuery->with(['inventoryLevels' => function ($query) use ($warehouseFilter) {
                    $query->where('warehouse_id', $warehouseFilter);
                }])->get()->sum(function ($variant) {
                    return $variant->inventoryLevels->sum('quantity');
                });
            } else {
                $brand->total_stock = $stockQuery->sum('cached_stock');
            }
        }

        return $brands;
    }

    /**
     * Get low stock products
     */
    private function getLowStockProducts(): Collection
    {
        return ProductVariant::with(['product.category', 'product.brand'])
            ->whereHas('product', function ($query) {
                $query->where('is_active', true);
            })
            ->where('is_active', true)
            ->whereColumn('cached_stock', '>', '0')
            ->whereColumn('cached_stock', '<=', 'low_stock_threshold')
            ->orderBy('cached_stock', 'asc')
            ->limit(20)
            ->get();
    }

    /**
     * Get out of stock products
     */
    private function getOutOfStockProducts(): Collection
    {
        return ProductVariant::with(['product.category', 'product.brand'])
            ->whereHas('product', function ($query) {
                $query->where('is_active', true);
            })
            ->where('is_active', true)
            ->where('cached_stock', '=', 0)
            ->orderBy('variant_name')
            ->limit(20)
            ->get();
    }
}
