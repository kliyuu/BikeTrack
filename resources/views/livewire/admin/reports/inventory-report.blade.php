<div class="max-w-7xl">
  <div class="space-y-6">
    <!-- Header -->
    <div class="sm:flex sm:items-center">
      <div class="sm:flex-auto">
        <h1 class="text-2xl font-semibold leading-6 text-gray-900 dark:text-white">Inventory Report</h1>
        <p class="mt-2 text-sm text-gray-700 dark:text-gray-400">Analyze your inventory levels, stock movements, and warehouse performance.
        </p>
      </div>
      <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
        <flux:button icon="document-arrow-down" wire:click="openExportModal" variant="primary" class="cursor-pointer">
          Export Report
        </flux:button>
      </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
      <div class="bg-white overflow-hidden shadow rounded-lg dark:bg-gray-800">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                <flux:icon name="cube" class="w-5 h-5 text-white"></flux:icon>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Total Products</dt>
                <dd class="text-lg font-medium text-gray-900 dark:text-white">{{ number_format($inventoryStats['total_products']) }}
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg dark:bg-gray-800">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                <flux:icon name="banknotes" class="w-5 h-5 text-white"></flux:icon>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Total Stock Value</dt>
                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                  ₱{{ number_format($inventoryStats['total_stock_value'], 2) }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg dark:bg-gray-800">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                <flux:icon name="exclamation-triangle" class="w-5 h-5 text-white"></flux:icon>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Low Stock Items</dt>
                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                  {{ number_format($inventoryStats['low_stock_count']) }}
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>

      <div class="bg-white overflow-hidden shadow rounded-lg dark:bg-gray-800">
        <div class="p-5">
          <div class="flex items-center">
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-red-500 rounded-md flex items-center justify-center">
                <flux:icon name="exclamation-circle" class="w-5 h-5 text-white"></flux:icon>
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Out of Stock</dt>
                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                  {{ number_format($inventoryStats['out_of_stock_count']) }}</dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl hidden">
      <div class="px-4 py-6 sm:px-8">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-5">
          <div>
            <flux:select wire:model.live="period" id="period" label="Period">
              <flux:select.option value="week">This Week</flux:select.option>
              <flux:select.option value="month">This Month</flux:select.option>
              <flux:select.option value="quarter">This Quarter</flux:select.option>
              <flux:select.option value="year">This Year</flux:select.option>
            </flux:select>
          </div>

          <div>
            <flux:select wire:model.live="warehouseFilter" id="warehouseFilter" label="Warehouse">
              <flux:select.option value="">All Warehouses</flux:select.option>
              @foreach ($warehouses as $warehouse)
                <flux:select.option value="{{ $warehouse->id }}">{{ $warehouse->name }}</flux:select.option>
              @endforeach
            </flux:select>
          </div>

          <div>
            <flux:select wire:model.live="categoryFilter" id="categoryFilter" label="Category">
              <flux:select.option value="">All Categories</flux:select.option>
              @foreach ($categories as $category)
                <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
              @endforeach
            </flux:select>
          </div>

          <div>
            <flux:select wire:model.live="brandFilter" id="brandFilter" label="Brand">
              <flux:select.option value="">All Brands</flux:select.option>
              @foreach ($brands as $brand)
                <flux:select.option value="{{ $brand->id }}">{{ $brand->name }}</flux:select.option>
              @endforeach
            </flux:select>
          </div>

          <div>
            <flux:select wire:model.live="stockFilter" id="stockFilter" label="Stock Status">
              <flux:select.option value="all">All Stock</flux:select.option>
              <flux:select.option value="low">Low Stock</flux:select.option>
              <flux:select.option value="out">Out of Stock</flux:select.option>
              <flux:select.option value="available">Available</flux:select.option>
            </flux:select>
          </div>
        </div>
      </div>
    </div>

    <!-- Warehouse Stock Overview -->
    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl dark:bg-gray-800">
      <div class="px-4 py-6 sm:px-8">
        <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">Warehouse Stock Overview</h3>
        <div class="mt-6 flow-root">
          <div class="overflow-x-auto">
            <div class="inline-block min-w-full py-2 align-middle">
              <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                <thead>
                  <tr>
                    <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-300">Warehouse</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-300">Total Items</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-300">Total Stock</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-300">Stock Value</th>
                    <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-300">Location</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                  @forelse($warehouseStock as $stock)
                    <tr>
                      <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-white">
                        {{ $stock['warehouse']->name }}</td>
                      <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-white">
                        {{ number_format($stock['total_products']) }}</td>
                      <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-white">
                        {{ number_format($stock['total_stock']) }}</td>
                      <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-white">
                        ₱{{ number_format($stock['total_value'], 2) }}</td>
                      <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-white">
                        {{ $stock['warehouse']->location }}</td>
                    </tr>
                  @empty
                    <tr>
                      <td colspan="5" class="px-3 py-8 text-center text-sm text-gray-500 dark:text-white">No warehouse data found.
                      </td>
                    </tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
      <!-- Top Categories -->
      <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl dark:bg-gray-800">
        <div class="px-4 py-6 sm:px-8">
          <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">Top Categories by Product Count</h3>
          <div class="mt-6 flow-root">
            <div class="overflow-x-auto max-h-80">
              <div class="inline-block min-w-full py-2 align-middle">
                <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                  <thead>
                    <tr>
                      <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-300">Category</th>
                      <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-300">Products</th>
                      <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-300">Stock</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    @forelse($topCategories as $category)
                      <tr>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-white">
                          {{ $category->name }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-white">
                          {{ number_format($category->products_count) }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-white">
                          {{ number_format($category->total_stock ?? 0) }}
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="3" class="px-3 py-8 text-center text-sm text-gray-500">No categories found.
                        </td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Top Brands -->
      <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl dark:bg-gray-800">
        <div class="px-4 py-6 sm:px-8">
          <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">Top Brands by Product Count</h3>
          <div class="mt-6 flow-root">
            <div class="overflow-x-auto max-h-80">
              <div class="inline-block min-w-full py-2 align-middle">
                <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                  <thead>
                    <tr>
                      <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-300">Brand</th>
                      <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-300">Products</th>
                      <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-300">Stock</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    @forelse($topBrands as $brand)
                      <tr>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-white">
                          {{ $brand->name }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-white">
                          {{ number_format($brand->products_count) }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-white">
                          {{ number_format($brand->total_stock ?? 0) }}
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="3" class="px-3 py-8 text-center text-sm text-gray-500 dark:text-white">No brands found.</td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Low Stock Products -->
      <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl dark:bg-gray-800">
        <div class="px-4 py-6 sm:px-8">
          <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">Low Stock Alert</h3>
          <div class="mt-6 flow-root">
            <div class="overflow-x-auto max-h-80">
              <div class="inline-block min-w-full py-2 align-middle">
                <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                  <thead>
                    <tr>
                      <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-300">Product</th>
                      <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-300">SKU</th>
                      <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-300">Stock</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    @forelse($lowStockProducts as $product)
                      <tr>
                        <td class="py-4 pl-4 pr-3 text-sm">
                          <div class="flex items-center">
                            <div class="h-8 w-8 flex-shrink-0">
                              <img class="h-8 w-8 rounded-lg object-cover"
                                src='{{ $product->product->primaryImage ? asset("storage/{$product->product->primaryImage->url}") : asset("images/placeholder.png") }}'
                                alt="{{ $product->product->name }}">
                            </div>
                            <div class="ml-3">
                              <div class="font-medium text-gray-900 dark:text-white">{{ $product->product->name }}</div>
                              <div class="text-gray-500 dark:text-white">{{ $product->product->category->name ?? 'N/A' }}</div>
                              <div class="text-xs text-gray-400 dark:text-gray-500">{{ $product->variant_name }}</div>
                            </div>
                          </div>
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-white">{{ $product->product->sku }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                          <span
                            class="inline-flex items-center rounded-full bg-yellow-50 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-inset ring-yellow-600/20">
                            {{ $product->cached_stock }} units
                          </span>
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="3" class="px-3 py-8 text-center text-sm text-gray-500">No low stock products.
                        </td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Out of Stock Products -->
      <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl dark:bg-gray-800">
        <div class="px-4 py-6 sm:px-8">
          <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">Out of Stock Alert</h3>
          <div class="mt-6 flow-root">
            <div class="overflow-x-auto max-h-80">
              <div class="inline-block min-w-full py-2 align-middle">
                <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                  <thead>
                    <tr>
                      <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-300">Product</th>
                      <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-300">SKU</th>
                      <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-300">Status</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                    @forelse($outOfStockProducts as $product)
                      <tr>
                        <td class="py-4 pl-4 pr-3 text-sm">
                          <div class="flex items-center">
                            <div class="h-8 w-8 flex-shrink-0">
                              <img class="h-8 w-8 rounded-lg object-cover"
                                src='{{ $product->product->primaryImage ? asset("storage/{$product->product->primaryImage->url}") : asset("images/placeholder.png") }}'
                                alt="{{ $product->product->name }}">
                            </div>
                            <div class="ml-3">
                              <div class="font-medium text-gray-900 dark:text-white">{{ $product->product->name }}</div>
                              <div class="text-gray-500 dark:text-gray-300">{{ $product->product->category->name ?? 'N/A' }}</div>
                              <div class="text-xs text-gray-400 dark:text-gray-500">{{ $product->variant_name }}</div>
                            </div>
                          </div>
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-white">{{ $product->product->sku }}</td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                          <span
                            class="inline-flex items-center rounded-full bg-red-50 px-2 py-1 text-xs font-medium text-red-800 ring-1 ring-inset ring-red-600/20">
                            Out of Stock
                          </span>
                        </td>
                      </tr>
                    @empty
                      <tr>
                        <td colspan="3" class="px-3 py-8 text-center text-sm text-gray-500 dark:text-white">No out of stock
                          products.</td>
                      </tr>
                    @endforelse
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

  </div>

  <!-- Export Modal -->
  <flux:modal name="export-report" variant="flyout" class="space-y-6" wire:model="showExportModal">
    <div>
      <flux:heading size="lg">Export Inventory Report</flux:heading>
      <flux:subheading>Choose your preferred export format</flux:subheading>
    </div>

    <div class="space-y-4">
      <flux:fieldset>
        <flux:legend>Export Format</flux:legend>
        <flux:radio.group wire:model="exportFormat">
          <flux:radio value="pdf" label="PDF Document" description="Best for printing and sharing" />
          <flux:radio value="csv" label="CSV Spreadsheet" description="Best for Excel and data analysis" />
        </flux:radio.group>
      </flux:fieldset>

      <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
        <div class="flex items-start gap-3">
          <flux:icon name="information-circle" class="w-6 h-6 text-blue-600 dark:text-blue-400"></flux:icon>
          <div class="text-sm text-blue-800 dark:text-blue-300">
            <p class="font-medium mb-1">Export Information</p>
            <ul class="list-disc list-inside space-y-1 text-blue-700 dark:text-blue-400">
              <li>Current filters will be applied to the export</li>
              <li>Report period: {{ $dateFrom }} to {{ $dateTo }}</li>
              @if($warehouseFilter)
                <li>Filtered by warehouse</li>
              @endif
              @if($categoryFilter)
                <li>Filtered by category</li>
              @endif
              @if($brandFilter)
                <li>Filtered by brand</li>
              @endif
            </ul>
          </div>
        </div>
      </div>
    </div>

    <div class="flex gap-2">
      <flux:button icon="document-arrow-down" variant="primary" wire:click="exportReport" class="cursor-pointer">
        Export Now
      </flux:button>

      <flux:modal.close>
        <flux:button variant="ghost" class="cursor-pointer">Cancel</flux:button>
      </flux:modal.close>
    </div>
  </flux:modal>
</div>
