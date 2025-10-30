<?php

use App\Livewire\Admin\Client\ClientIndex;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Inventory\BrandManager;
use App\Livewire\Admin\Inventory\CategoryManager;
use App\Livewire\Admin\Inventory\ProductManager;
use App\Livewire\Admin\Inventory\ProductVariantManager;
use App\Livewire\Admin\Inventory\ProductView;
use App\Livewire\Admin\Inventory\RestockHistory;
use App\Livewire\Admin\Inventory\StockLevel;
use App\Livewire\Admin\Inventory\WarehouseManager;
use App\Livewire\Admin\Orders\OrderDetails;
use App\Livewire\Admin\Orders\OrderManager;
use App\Livewire\Admin\Orders\ReturnManager;
use App\Livewire\Admin\Reports\InventoryReport;
use App\Livewire\Admin\Reports\SalesReport;
use App\Livewire\Admin\Users\UserManager;
use App\Livewire\Customer\Account;
use App\Livewire\Customer\OrderDetails as CustomerOrderDetails;
use App\Livewire\Customer\OrderHistory;
use App\Livewire\Customer\ReturnRequestDetails;
use App\Livewire\Customer\ReturnRequests;
use App\Livewire\Notifications\AdminIndex as NotificationsAdminIndex;
use App\Livewire\Notifications\ClientIndex as NotificationsClientIndex;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Shop\Checkout;
use App\Livewire\Shop\Index;
use App\Livewire\Shop\ProductCatalog;
use App\Livewire\Shop\ProductDetails;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//   return view('shop');
// })->name('shop');

// Route::redirect('/', 'admin/dashboard')->name('home');

// Storefront routes (shop) | unauthenticated users can access
Route::get('/', ProductCatalog::class)->name('shop');
Route::get('/shop/catalog', ProductCatalog::class)->name('shop.catalog');
Route::get('/shop/product/{id}', ProductDetails::class)->name('shop.product');
Route::get('/shop/checkout', Checkout::class)->name('shop.checkout');

// Dashboard
Route::get('dashboard', function () {
    $user = Auth::user();

    return match ($user->role_id) {
        1 => redirect()->route('admin.dashboard'), // Admin
        2 => redirect()->route('admin.dashboard'), // Staff
        3 => redirect()->route('shop.catalog'), // Client
        default => redirect()->route('shop'), // Guest
    };
})->middleware(['auth', 'verified'])->name('dashboard');

// Authenticated and verified user routes
Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    // Admin routes
    Route::prefix('admin')->middleware(['role:admin,staff'])->name('admin.')->group(function () {
        Route::get('dashboard', Dashboard::class)->name('dashboard');

        Route::get('inventory/products', ProductManager::class)->name('products');
        Route::get('inventory/products/show/{id}', ProductView::class)->name('products.show');
        Route::get('inventory/products/{productId}/variants', ProductVariantManager::class)->name('products.variants');
        Route::get('inventory/categories', CategoryManager::class)->name('categories');
        Route::get('inventory/brands', BrandManager::class)->name('brands');
        Route::get('inventory/warehouses', WarehouseManager::class)->name('warehouses');
        Route::get('inventory/stock-levels', StockLevel::class)->name('stock-levels');
        Route::get('inventory/restock-history', RestockHistory::class)->name('restock-history');

        Route::get('orders', OrderManager::class)->name('orders');
        Route::get('order-details/{order}', OrderDetails::class)->name('order-details');
        Route::get('returns', ReturnManager::class)->name('returns');

        Route::get('inventory-reports', InventoryReport::class)->name('inventory-reports');
        Route::get('sales-reports', SalesReport::class)->name('sales-reports');

        Route::get('user-management', UserManager::class)->name('users');
        Route::get('user-management/create', UserManager::class)->name('users.create');

        Route::get('clients', ClientIndex::class)->name('clients');

        // Notifications routes
        Route::get('notifications', NotificationsAdminIndex::class)->name('notifications');
    });

    // Client routes
    Route::prefix('account')->middleware(['role:client'])->name('client.')->group(function () {
        Route::get('/', Index::class)->name('shop');
        Route::get('account-info', Account::class)->name('account-info');
        Route::get('order-history', OrderHistory::class)->name('order-history');
        Route::get('order-details/{id}', CustomerOrderDetails::class)->name('order-details');
        Route::get('return-orders', ReturnRequests::class)->name('return-orders');
        Route::get('return-orders/{returnRequest}', ReturnRequestDetails::class)->name('return-orders.show');

        Route::get('notifications', NotificationsClientIndex::class)->name('notifications');
    });
});

require __DIR__.'/auth.php';
