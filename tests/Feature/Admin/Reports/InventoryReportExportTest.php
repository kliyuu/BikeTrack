<?php

declare(strict_types=1);

use App\Livewire\Admin\Reports\InventoryReport;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Models\Warehouse;
use Livewire\Livewire;

beforeEach(function () {
    $this->adminRole = Role::create(['name' => 'admin', 'description' => 'Administrator']);
    $this->admin = User::factory()->create(['role_id' => $this->adminRole->id]);

    $this->category = Category::create([
        'name' => 'Test Category',
        'slug' => 'test-category',
        'description' => 'Test',
    ]);
    $this->brand = Brand::create([
        'name' => 'Test Brand',
        'slug' => 'test-brand',
        'description' => 'Test',
    ]);
    $this->warehouse = Warehouse::create([
        'name' => 'Main Warehouse',
        'location' => 'Test Location',
        'capacity' => 1000,
    ]);

    $this->actingAs($this->admin);

    // Helper function to create products
    $this->createProduct = function ($count = 1, $categoryId = null, $brandId = null) {
        $products = [];
        for ($i = 1; $i <= $count; $i++) {
            $products[] = Product::create([
                'name' => 'Product '.$i,
                'sku' => 'SKU-'.uniqid(),
                'category_id' => $categoryId ?? $this->category->id,
                'brand_id' => $brandId ?? $this->brand->id,
                'unit_price' => 100,
                'cached_stock' => 10,
                'low_stock_threshold' => 5,
                'is_active' => true,
            ]);
        }

        return $count === 1 ? $products[0] : $products;
    };
});

test('admin can view inventory report page', function () {
    $this->get(route('admin.inventory-reports'))
        ->assertSuccessful()
        ->assertSeeLivewire(InventoryReport::class);
});

test('export modal opens when export button is clicked', function () {
    Livewire::test(InventoryReport::class)
        ->call('openExportModal')
        ->assertSet('showExportModal', true);
});

test('export modal closes when close method is called', function () {
    Livewire::test(InventoryReport::class)
        ->set('showExportModal', true)
        ->call('closeExportModal')
        ->assertSet('showExportModal', false)
        ->assertSet('exportFormat', 'pdf');
});

test('export format defaults to pdf', function () {
    Livewire::test(InventoryReport::class)
        ->assertSet('exportFormat', 'pdf');
});

test('export format can be changed to csv', function () {
    Livewire::test(InventoryReport::class)
        ->set('exportFormat', 'csv')
        ->assertSet('exportFormat', 'csv');
});

test('export report validates export format', function () {
    Livewire::test(InventoryReport::class)
        ->set('exportFormat', 'invalid')
        ->call('exportReport')
        ->assertHasErrors(['exportFormat']);
});

test('export report as pdf returns download response', function () {
    ($this->createProduct)(5);

    $response = Livewire::test(InventoryReport::class)
        ->set('exportFormat', 'pdf')
        ->call('exportReport')
        ->assertHasNoErrors();

    expect($response->effects['returns'])->toBeInstanceOf(Symfony\Component\HttpFoundation\StreamedResponse::class);
});

test('export report as csv returns streamed response', function () {
    ($this->createProduct)(5);

    $response = Livewire::test(InventoryReport::class)
        ->set('exportFormat', 'csv')
        ->call('exportReport')
        ->assertHasNoErrors();

    expect($response->effects['returns'])->toBeInstanceOf(Symfony\Component\HttpFoundation\StreamedResponse::class);
});

test('export report closes modal after export', function () {
    ($this->createProduct)(3);

    Livewire::test(InventoryReport::class)
        ->set('showExportModal', true)
        ->set('exportFormat', 'pdf')
        ->call('exportReport')
        ->assertSet('showExportModal', false);
});

test('export report respects warehouse filter', function () {
    $warehouse1 = Warehouse::create([
        'name' => 'Warehouse 1',
        'location' => 'Location 1',
        'capacity' => 500,
    ]);

    ($this->createProduct)(3);

    Livewire::test(InventoryReport::class)
        ->set('warehouseFilter', (string) $warehouse1->id)
        ->set('exportFormat', 'pdf')
        ->call('exportReport')
        ->assertHasNoErrors();
});

test('export report respects category filter', function () {
    $category = Category::create([
        'name' => 'Bikes',
        'slug' => 'bikes',
        'description' => 'Bikes',
    ]);

    ($this->createProduct)(3, $category->id);

    Livewire::test(InventoryReport::class)
        ->set('categoryFilter', (string) $category->id)
        ->set('exportFormat', 'csv')
        ->call('exportReport')
        ->assertHasNoErrors();
});

test('export report respects brand filter', function () {
    $brand = Brand::create([
        'name' => 'Premium Brand',
        'slug' => 'premium-brand',
        'description' => 'Premium',
    ]);

    ($this->createProduct)(3, null, $brand->id);

    Livewire::test(InventoryReport::class)
        ->set('brandFilter', (string) $brand->id)
        ->set('exportFormat', 'pdf')
        ->call('exportReport')
        ->assertHasNoErrors();
});

test('inventory report service generates correct data structure', function () {
    ($this->createProduct)(5);

    $service = new \App\Services\InventoryReportService;
    $data = $service->generateReportData(
        now()->startOfMonth()->toDateString(),
        now()->endOfMonth()->toDateString(),
        null,
        null,
        null
    );

    expect($data)->toHaveKeys([
        'inventoryStats',
        'warehouseStock',
        'topCategories',
        'topBrands',
        'lowStockProducts',
        'outOfStockProducts',
        'dateFrom',
        'dateTo',
        'warehouseFilter',
        'categoryFilter',
        'brandFilter',
        'warehouses',
        'categories',
        'brands',
    ]);

    expect($data['inventoryStats'])->toHaveKeys([
        'total_products',
        'total_stock_value',
        'low_stock_count',
        'out_of_stock_count',
        'total_stock_quantity',
    ]);
});
