<div class="max-w-7xl">
  <div class="mb-8">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Admin Dashboard</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Overview of your inventory management system</p>
      </div>
      <flux:button wire:click="refreshStats" variant="primary" icon="arrow-path"
        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150 cursor-pointer">
        Refresh
      </flux:button>
    </div>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 lg:gap-8 gap-6 mb-8">
    <!-- Today's Sales -->
    <div class="bg-rose-500 dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
      <div class="p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="flex items-center justify-center w-12 h-12 p-3 bg-white dark:bg-purple-900 rounded-full">
              <i class="ti ti-currency-peso text-purple-600 dark:text-purple-400 text-3xl"></i>
            </div>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-50 dark:text-gray-400 uppercase tracking-wider">Today's Sales</p>
            <p class="text-2xl font-semibold text-gray-50 dark:text-white">P{{ number_format($totalSalesToday, 2) }}
            </p>
          </div>
        </div>
      </div>
    </div>

    <!-- Orders Today -->
    <div class="bg-green-500 dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
      <div class="p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="flex items-center justify-center w-12 h-12 p-3 bg-white dark:bg-green-900 rounded-full">
              <i class="ti ti-shopping-bag text-green-600 dark:text-green-400 text-3xl"></i>
            </div>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-50 dark:text-gray-400 uppercase tracking-wider">Orders Today</p>
            <p class="text-2xl font-semibold text-gray-50 dark:text-white">{{ $ordersToday }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Low Stock Alert -->
    <div class="bg-orange-400 dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
      <div class="p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="flex items-center justify-center w-12 h-12 p-3 bg-white dark:bg-yellow-900 rounded-full">
              <i class="ti ti-alert-triangle text-yellow-600 dark:text-yellow-400 text-3xl"></i>
            </div>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-50 dark:text-gray-400 uppercase tracking-wider">Low Stock</p>
            <p class="text-2xl font-semibold text-gray-50 dark:text-white">{{ $lowStockCount }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Total Products -->
    <div class="bg-blue-600 dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
      <div class="p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="flex items-center justify-center w-12 h-12 p-3 bg-white dark:bg-blue-900 rounded-full">
              <i class="ti ti-package text-blue-600 dark:text-blue-400 text-3xl"></i>
            </div>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-gray-50 dark:text-gray-400 uppercase tracking-wider">Total Products</p>
            <p class="text-2xl font-semibold text-gray-50 dark:text-white">{{ number_format($totalProducts) }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    <!-- Stock Alerts -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Stock Alerts</h3>
      </div>
      <div class="p-6">
        @if ($stockAlerts->count() > 0)
          <div class="space-y-3">
            @foreach ($stockAlerts as $product)
              <div class="flex items-center justify-between p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg">
                <div>
                  <p class="font-medium text-gray-900 dark:text-white">{{ $product->name }}</p>
                  <p class="text-sm text-gray-500 dark:text-gray-400">SKU: {{ $product->sku }}</p>
                </div>
                <div class="text-right">
                  <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">{{ $product->cached_stock }} left
                  </p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">{{ $product->category->name ?? 'No Category' }}
                  </p>
                </div>
              </div>
            @endforeach
          </div>
          <div class="mt-4">
            <a href="{{ route('admin.products') }}"
              class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">View all
              products →</a>
          </div>
        @else
          <p class="text-gray-500 dark:text-gray-400">No low stock alerts</p>
        @endif
      </div>
    </div>

    <!-- Recent Orders -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Orders</h3>
      </div>
      <div class="p-6">
        @if ($recentOrders->count() > 0)
          <div class="space-y-3">
            @foreach ($recentOrders as $order)
              <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <div>
                  <p class="font-medium text-gray-900 dark:text-white">{{ $order->order_number }}</p>
                  <p class="text-sm text-gray-500 dark:text-gray-400">{{ $order->client->name ?? 'N/A' }}</p>
                </div>
                <div class="text-right">
                  <p class="text-sm font-medium text-gray-900 dark:text-white">
                    ${{ number_format($order->total_amount, 2) }}
                  </p>
                  @if ($order->status === 'pending')
                    <flux:badge color="yellow">Pending</flux:badge>
                  @elseif($order->status === 'confirmed')
                    <flux:badge color="blue">Confirmed</flux:badge>
                  @elseif($order->status === 'shipped')
                    <flux:badge color="purple">Shipped</flux:badge>
                  @elseif($order->status === 'delivered')
                    <flux:badge color="green">Delivered</flux:badge>
                  @else
                    <flux:badge color="red">Cancelled</flux:badge>
                  @endif
                </div>
              </div>
            @endforeach
          </div>
          <div class="mt-4">
            <span class="text-sm text-gray-500 dark:text-gray-400">Order management coming soon</span>
          </div>
        @else
          <p class="text-gray-500 dark:text-gray-400">No recent orders</p>
        @endif
      </div>
    </div>

    <!-- Top Products -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Top Selling Products (30 days)</h3>
      </div>
      <div class="p-6">
        @if ($topProducts->count() > 0)
          <div class="space-y-3">
            @foreach ($topProducts as $product)
              <div class="flex items-center justify-between">
                <div>
                  <p class="font-medium text-gray-900 dark:text-white">{{ $product->name }}</p>
                  <p class="text-sm text-gray-500 dark:text-gray-400">{{ $product->category->name ?? 'No Category' }}
                  </p>
                </div>
                <div class="text-right">
                  <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $product->order_items_count }} sold
                  </p>
                  <p class="text-xs text-gray-500 dark:text-gray-400">₱{{ number_format($product->unit_price, 2) }}
                  </p>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <p class="text-gray-500 dark:text-gray-400">No sales data available</p>
        @endif
      </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
      <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Quick Actions</h3>
      </div>
      <div class="p-6">
        <div class="grid grid-cols-2 gap-4">
          <a href="{{ route('admin.products') }}"
            class="flex items-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
            <flux:icon name="cube" class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-3"></flux:icon>
            <span class="text-sm font-medium text-blue-900 dark:text-blue-100">Manage Products</span>
          </a>

          <a href="#"
            class="flex items-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
            <flux:icon name="shopping-bag" class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-3"></flux:icon>
            <span class="text-sm font-medium text-blue-900 dark:text-blue-100">B2B Catalog</span>
          </a>

          <a href="{{ route('admin.orders') }}"
            class="flex items-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
            <flux:icon name="shopping-cart" class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-3"></flux:icon>
            <span class="text-sm font-medium text-blue-900 dark:text-blue-100">Orders</span>
          </a>

          <a href="{{ route('admin.sales-reports') }}"
            class="flex items-center p-3 bg-blue-50 dark:bg-blue-900/20 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors">
            <flux:icon name="chart-pie" class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-3"></flux:icon>
            <span class="text-sm font-medium text-blue-900 dark:text-blue-100">Sales Reports</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
