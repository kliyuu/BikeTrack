<?php

use App\Livewire\Admin\Client\ClientIndex;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Inventory\BrandManager;
use App\Livewire\Admin\Inventory\CategoryManager;
use App\Livewire\Admin\Inventory\ProductManager;
use App\Livewire\Admin\Inventory\ProductView;
use App\Livewire\Admin\Inventory\RestockHistory;
use App\Livewire\Admin\Inventory\StockLevel;
use App\Livewire\Admin\Inventory\WarehouseManager;
use App\Livewire\Admin\Orders\OrderDetails;
use App\Livewire\Admin\Orders\OrderManager;
use App\Livewire\Admin\Reports\InventoryReport;
use App\Livewire\Admin\Reports\SalesReport;
use App\Livewire\Admin\Users\UserManager;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Shop\Index;
use App\Livewire\Shop\ProductCatalog;
use App\Livewire\Shop\ProductDetails;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//   return view('shop');
// })->name('shop');

// Route::redirect('/', 'admin/dashboard')->name('home');

// Storefront route
Route::get('/', Index::class)->name('shop');
Route::get('/shop/catalog', ProductCatalog::class)->name('shop.catalog');
Route::get('/shop/product/{id}', ProductDetails::class)->name('shop.product');

// Dashboard
Route::get('dashboard', function () {
  $user = Auth::user();

  return match ($user->role_id) {
    1 => redirect()->route('admin.dashboard'),
    2 => redirect()->route('admin.dashboard'),
    default => redirect()->route('shop'),
  };
})->middleware(['auth', 'verified'])->name('dashboard');

// Auth and Admin routes
Route::middleware(['auth', 'verified'])->group(function () {
  Route::redirect('settings', 'settings/profile');

  Route::get('settings/profile', Profile::class)->name('settings.profile');
  Route::get('settings/password', Password::class)->name('settings.password');
  Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

  // Admin routes
  Route::prefix('admin')->middleware(['role:admin'])->name('admin.')->group(function () {
    Route::get('dashboard', Dashboard::class)->name('dashboard');

    Route::get('inventory/products', ProductManager::class)->name('products');
    Route::get('inventory/products/show/{id}', ProductView::class)->name('products.show');
    Route::get('inventory/categories', CategoryManager::class)->name('categories');
    Route::get('inventory/brands', BrandManager::class)->name('brands');
    Route::get('inventory/warehouses', WarehouseManager::class)->name('warehouses');
    Route::get('inventory/stock-levels', StockLevel::class)->name('stock-levels');
    Route::get('inventory/restock-history', RestockHistory::class)->name('restock-history');

    Route::get('orders', OrderManager::class)->name('orders');
    Route::get('order-details/{id}', OrderDetails::class)->name('order-details');

    Route::get('inventory-reports', InventoryReport::class)->name('inventory-reports');
    Route::get('sales-reports', SalesReport::class)->name('sales-reports');

    Route::get('user-management', UserManager::class)->name('users');
    Route::get('user-management/create', UserManager::class)->name('users.create');

    Route::get('clients', ClientIndex::class)->name('clients');
  });

  // Client routes
  // Route::middleware(['role:client'])->group(function () {
  //   Route::get('dashboard', function () {
  //     return view('dashboard');
  //   })->name('dashboard');
  // });
});

require __DIR__ . '/auth.php';
