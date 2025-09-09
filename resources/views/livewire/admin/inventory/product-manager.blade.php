<div class="max-w-7xl">
  <div class="mb-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Product Management</h1>
      </div>
    </div>
  </div>

  {{-- @if (session()->has('message'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 2000)" x-transition.opacity.duration.500ms
      class="bg-green-100 text-green-800 p-2 mb-3 rounded">
      {{ session('message') }}
    </div>
  @endif --}}

  <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-blue-500 dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
      <div class="p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="flex items-center justify-center w-12 h-12 p-3 bg-white dark:bg-blue-900 rounded-full">
              <flux:icon name="cube" class="w-6 h-6 text-blue-600 dark:text-blue-400"></flux:icon>
            </div>
          </div>
          <div class="ml-4">
            <dl>
              <dt class="text-sm font-medium text-gray-50 dark:text-gray-400 truncate">Total Products</dt>
              <dd class="text-lg font-medium text-gray-50 dark:text-white">
                {{ number_format($stockSummary['total_products']) }}
              </dd>
            </dl>
          </div>
        </div>
      </div>
    </div>

    <div class="bg-green-500 dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
      <div class="p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="flex items-center justify-center w-12 h-12 p-3 bg-white dark:bg-green-900 rounded-full">
              <flux:icon name="check-circle" class="w-6 h-6 text-green-600 dark:text-green-400"></flux:icon>
            </div>
          </div>
          <div class="ml-4">
            <dl>
              <dt class="text-sm font-medium text-gray-50 dark:text-gray-400 truncate">In Stock</dt>
              <dd class="text-lg font-medium text-gray-50 dark:text-white">
                {{ number_format($stockSummary['in_stock']) }}
              </dd>
            </dl>
          </div>
        </div>
      </div>
    </div>

    <div class="bg-yellow-500 dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
      <div class="p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="flex items-center justify-center w-12 h-12 p-3 bg-white dark:bg-yellow-900 rounded-full">
              <flux:icon name="exclamation-triangle" class="w-6 h-6 text-yellow-600 dark:text-yellow-400"></flux:icon>
            </div>
          </div>
          <div class="ml-4">
            <dl>
              <dt class="text-sm font-medium text-gray-50 dark:text-gray-400 truncate">Low Stock</dt>
              <dd class="text-lg font-medium text-gray-50 dark:text-white">
                {{ number_format($stockSummary['low_stock']) }}
              </dd>
            </dl>
          </div>
        </div>
      </div>
    </div>

    <div class="bg-red-500 dark:bg-gray-800 overflow-hidden shadow-sm rounded-lg">
      <div class="p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="flex items-center justify-center w-12 h-12 p-3 bg-white dark:bg-red-900 rounded-full">
              <flux:icon name="exclamation-circle" class="w-6 h-6 text-red-600 dark:text-red-400"></flux:icon>
            </div>
          </div>
          <div class="ml-4">
            <dl>
              <dt class="text-sm font-medium text-gray-50 dark:text-gray-400 truncate">Out of Stock</dt>
              <dd class="text-lg font-medium text-gray-50 dark:text-white">
                {{ number_format($stockSummary['out_of_stock']) }}
              </dd>
            </dl>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="bg-white dark:bg-gray-800 shadow-sm rounded-md mb-6">
    <div class="p-6 space-y-2">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 space-y-4">
        <div class="flex flex-col gap-2 lg:col-span-2">
          <flux:label>Search Products</flux:label>
          <input type="text" wire:model.live.debounce.100ms="search" id="search"
            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            placeholder="Search by SKU, name, or barcode...">
        </div>
        <div class="flex flex-col gap-2 md:ml-4">
          <flux:label>Brands</flux:label>
          <x-select wire:model.live="brandFilter">
            <option value="">All Brands</option>
            @foreach ($brands as $brand)
              <option value="{{ $brand->id }}">{{ $brand->name }}</option>
            @endforeach
          </x-select>
        </div>

        <div class="flex flex-col gap-2 md:ml-4">
          <flux:label>Categories</flux:label>
          <x-select wire:model.live="categoryFilter">
            <option value="">All Categories</option>
            @foreach ($categories as $category)
              <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
          </x-select>
        </div>

        <div class="flex flex-col gap-2 md:ml-4">
          <flux:label>Stocks</flux:label>
          <x-select wire:model.live="stockFilter" id="stockFilter"
            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            <option value="all">All Stock</option>
            <option value="in_stock">In Stock</option>
            <option value="low_stock">Low Stock</option>
            <option value="out_of_stock">Out of Stock</option>
          </x-select>
        </div>
      </div>

      <!-- Barcode Scanner -->
      <div>
        <label for="barcodeSearch" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Barcode
          Scanner</label>
        <div class="flex">
          <input wire:model.live="barcodeSearch" type="text" id="barcodeSearch"
            placeholder="Scan or type barcode/SKU..."
            class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-l-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-1 focus:ring-blue-500">
          <button type="button"
            class="px-4 py-2 bg-gray-100 dark:bg-gray-600 border border-l-0 border-gray-300 dark:border-gray-600 rounded-r-md hover:bg-gray-200 dark:hover:bg-gray-500">
            <flux:icon name="qr-code" class="w-5 h-5 text-gray-600 dark:text-gray-300" />
          </button>
        </div>
      </div>
    </div>
  </div>

  <x-card class="bg-white border border-gray-100 shadow-sm rounded-md">
    <div class="card-body">
      <div class="flex items-center justify-between mb-6 border-b border-gray-200 pb-4">
        <h2 class="text-base">{{ __('Products') }}</h2>
        <flux:button size="sm" variant="primary" icon="plus" wire:click="openProductModal"
          :loading="false" class="!px-4 bg-blue-600 hover:bg-blue-700 dark:text-white">
          {{ __('Add Product') }}
        </flux:button>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
              <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                <button wire:click="sortBy('name')"
                  class="flex items-center hover:text-gray-700 dark:hover:text-gray-200 cursor-pointer">
                  PRODUCT
                  @if ($sortField === 'name')
                    @if ($sortDirection === 'asc')
                      <flux:icon name="chevron-up" class="ml-1 w-3 h-3" />
                    @else
                      <flux:icon name="chevron-down" class="ml-1 w-3 h-3" />
                    @endif
                  @endif
                </button>
              </th>
              <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                <button wire:click="sortBy('sku')"
                  class="flex items-center hover:text-gray-700 dark:hover:text-gray-200 cursor-pointer">
                  SKU
                  @if ($sortField === 'sku')
                    @if ($sortDirection === 'asc')
                      <flux:icon name="chevron-up" class="ml-1 w-3 h-3" />
                    @else
                      <flux:icon name="chevron-down" class="ml-1 w-3 h-3" />
                    @endif
                  @endif
                </button>
              </th>
              <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Category</th>
              <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                <button type="button" wire:click="sortBy('unit_price')"
                  class="flex items-center hover:text-gray-700 dark:hover:text-gray-200 cursor-pointer">
                  PRICE
                  @if ($sortField === 'unit_price')
                    @if ($sortDirection === 'asc')
                      <flux:icon name="chevron-up" class="ml-1 w-3 h-3" />
                    @else
                      <flux:icon name="chevron-down" class="ml-1 w-3 h-3" />
                    @endif
                  @endif
                </button>
              </th>
              <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Stock</th>
              <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Warehouses</th>
              <th scope="col"
                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Status
              </th>
              <th scope="col"
                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($products as $product)
              <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="px-6 py-4">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                      @if ($product->primaryImage && Storage::disk('public')->exists($product->primaryImage->url))
                        <img class="h-10 w-10 rounded-lg object-cover"
                          src="{{ asset("storage/{$product->primaryImage->url}") }}" alt="{{ $product->name }}">
                      @elseif ($product->primaryImage && !Storage::disk('public')->exists($product->primaryImage->url))
                        <img class="h-10 w-10 rounded-lg object-cover"
                          src="{{ $product->primaryImage->url }}" alt="{{ $product->name }}">
                      @else
                        <img class="h-10 w-10 rounded-lg object-cover"
                          src="{{ asset('images/no-image.svg') }}" alt="{{ $product->name }}">
                      @endif
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $product->name }}</div>
                      <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $product->brand->name ?? 'No Brand' }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900 dark:text-white font-mono">{{ $product->sku }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900 dark:text-white">{{ $product->category->name ?? 'No Category' }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900 dark:text-white font-medium">
                    ₱{{ number_format($product->unit_price, 2) }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  @php
                    $stockClass = match (true) {
                        $product->cached_stock <= 0 => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-200',
                        $product->cached_stock <= $product->low_stock_threshold
                            => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-200',
                        default => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-200',
                    };
                  @endphp
                  <span class="inline-flex px-2 py-1 text-xs rounded-full {{ $stockClass }}">
                    {{ $product->cached_stock }} units
                  </span>
                </td>
                <td class="px-6 py-4">
                  <div class="text-xs text-gray-500 dark:text-gray-400 space-y-1">
                    @foreach ($product->inventoryLevels as $level)
                      <div>{{ $level->warehouse->name }}: {{ $level->quantity }}</div>
                    @endforeach
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->is_active ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-200' }}">
                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-1">
                  <flux:tooltip content="View">
                    <flux:button href="{{ route('admin.products.show', $product->id) }}" size="xs"
                      variant="primary" icon="eye" icon:variant="outline"
                      class="text-orange-600 bg-orange-100 hover:bg-orange-200 border-0 cursor-pointer"></flux:button>
                  </flux:tooltip>
                  <flux:tooltip content="Edit">
                    <flux:button wire:click="openProductModal({{ $product->id }})" size="xs" variant="primary"
                      icon="pencil-square" icon:variant="outline"
                      class="text-blue-600 bg-blue-100 hover:bg-blue-200 border-0 cursor-pointer" />
                  </flux:tooltip>
                  <flux:tooltip content="Stock">
                    <flux:button wire:click="openStockModal({{ $product->id }})" size="xs" variant="primary"
                      icon="plus-circle" icon:variant="outline"
                      class="text-green-600 bg-green-100 hover:bg-green-200 border-0 cursor-pointer" />
                  </flux:tooltip>
                  <flux:tooltip content="Delete">
                    <flux:button wire:click="confirmDelete({{ $product->id }})" size="xs" variant="primary"
                      icon="trash" icon:variant="outline"
                      class="text-red-600 bg-red-100 hover:bg-red-200 border-0 cursor-pointer" />
                  </flux:tooltip>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="8" class="px-6 py-12 text-center">
                  <div class="text-gray-500 dark:text-gray-400">
                    <svg class="mx-auto h-12 w-12 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                    <p class="text-sm">No products found</p>
                    <p class="text-xs mt-1">Try adjusting your search or filters</p>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>

        @if ($products->hasPages())
          <div class="mt-6">
            {{ $products->links() }}
          </div>
        @endif
      </div>
    </div>
  </x-card>

  {{-- Toasts --}}
  <x-toast displayDuration="3000" soundEffect="true" />

  {{-- Modals --}}
  @include('partials.product.product-modal')
  @include('partials.product.restock-product')
  @include('partials.product.delete-product')
</div>
