<div class="max-w-7xl">
  <div class="mb-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Stock Level</h1>
      </div>
    </div>
  </div>

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
            placeholder="Search by SKU, name, or warehouse...">
        </div>
        <div class="flex flex-col gap-2 md:ml-4">
          <flux:label>Warehouses</flux:label>
          <x-select wire:model.live="brandFilter">
            <option value="">All Warehouses</option>
            @foreach ($warehouses as $warehouse)
              <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
            @endforeach
          </x-select>
        </div>

        <div class="flex flex-col gap-2 md:ml-4">
          <flux:label>Stock Level</flux:label>
          <x-select wire:model.live="stockFilter" id="stockFilter"
            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
            <option value="all">All Stock</option>
            <option value="in_stock">In Stock</option>
            <option value="low_stock">Low Stock</option>
            <option value="out_of_stock">Out of Stock</option>
          </x-select>
        </div>
      </div>
    </div>
  </div>

  <x-card class="bg-white border border-gray-100 shadow-sm rounded-md">
    <div class="card-body">
      <div class="flex items-center justify-between mb-6 border-b border-gray-200 pb-4">
        <h2 class="text-base">{{ __('Inventory Levels') }}</h2>
        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $inventoryLevels->total() }} total</span>
      </div>

      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
              <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                <button wire:click="sortBy('products.name')"
                  class="flex items-center hover:text-gray-700 dark:hover:text-gray-200 cursor-pointer">
                  PRODUCT
                  @if ($sortField === 'products.name')
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
                SKU
              </th>
              <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                <button wire:click="sortBy('warehouses.name')"
                  class="flex items-center hover:text-gray-700 dark:hover:text-gray-200 cursor-pointer">
                  WAREHOUSE
                  @if ($sortField === 'warehouses.name')
                    @if ($sortDirection === 'asc')
                      <flux:icon name="chevron-up" class="ml-1 w-3 h-3" />
                    @else
                      <flux:icon name="chevron-down" class="ml-1 w-3 h-3" />
                    @endif
                  @endif
                </button>
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                <button wire:click="sortBy('inventory_levels.quantity')"
                  class="flex items-center hover:text-gray-700 dark:hover:text-gray-200 cursor-pointer">
                  CURRENT STOCK
                  @if ($sortField === 'inventory_levels.quantity')
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
                Status
              </th>
              <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Category
              </th>
              <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Last Updated
              </th>
              <th scope="col"
                class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>

          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse ($inventoryLevels as $inventory)
              <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="px-6 py-4">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                      <img class="h-10 w-10 rounded-lg object-cover"
                        src="{{ asset("storage/{$inventory->product->primaryImage->url}") }}"
                        alt="{{ $inventory->product->name }}">
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $inventory->product->name }}
                      </div>
                      <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $inventory->product->brand->name ?? 'No Brand' }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900 dark:text-white font-mono">{{ $inventory->product->sku }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900 dark:text-white">{{ $inventory->warehouse->name }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-semibold text-gray-900 dark:text-white">
                    {{ number_format($inventory->quantity) }}
                  </div>
                  <div class="text-xs text-gray-500 dark:text-gray-400">units</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  @if ($inventory->quantity <= 0)
                    <flux:badge color="red" variant="pill" size="sm">Out of Stock</flux:badge>
                  @elseif($inventory->quantity <= $inventory->product->low_stock_threshold)
                    <flux:badge color="yellow" variant="pill" size="sm">Low Stock</flux:badge>
                  @else
                    <flux:badge color="green" variant="pill" size="sm">In Stock</flux:badge>
                  @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900 dark:text-white">
                    {{ $inventory->product->category->name ?? 'No Category' }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900 dark:text-white">
                    {{ $inventory->updated_at->format('M j, Y') }}
                  </div>
                  <div class="text-xs text-gray-500 dark:text-gray-400">
                    {{ $inventory->updated_at->format('g:i A') }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <button wire:click="openAdjustmentModal({{ $inventory->id }})"
                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 cursor-pointer">
                    Adjust Stock
                  </button>
                </td>
              </tr>
            @empty
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    {{-- Pagination --}}
    <div class="mt-4">
      {{ $inventoryLevels->links() }}
    </div>
  </x-card>

  {{-- Toast Component --}}
  <x-toast />

  {{-- Adjust Stock Modal --}}
  <flux:modal name="adjustment-modal" class="md:w-xl !p-0">
    <div class="relative px-8 pt-8">
      <flux:heading size="lg" class="pb-6 text-lg font-medium text-gray-900 dark:text-white">
        Adjust Stock: {{ $adjustedStock?->name }}
      </flux:heading>
      <hr class="absolute bottom-0 left-0 w-full border-t-2 border-gray-200 dark:border-gray-700">
    </div>

    <div class="space-y-6 px-8 pb-8">
      <form method="POST" wire:submit="saveStockAdjustment" class="mt-6 space-y-6">
        <div class="grid grid-cols-1 gap-4">
          <flux:select wire:model="warehouseId" label="Warehouse *" placeholder="Select Warehouse">
            @foreach ($warehouses as $warehouse)
              <flux:select.option value="{{ $warehouse->id }}" class="text-gray-900 dark:text-white">
                {{ $warehouse->name }}
              </flux:select.option>
            @endforeach
          </flux:select>
        </div>

        <div class="grid grid-cols-1 gap-4">
          <flux:input wire:model="quantity_change" label="Quantity Change *"
            placeholder="Use negative numbers to decrease stock" type="number" />
        </div>

        <div class="grid grid-cols-1 gap-4">
          <flux:select wire:model="adjustmentReason" label="Reason *" placeholder="Select reason for restock">
            <flux:select.option value="manual_restock">Manual Adjustment</flux:select.option>
            <flux:select.option value="purchase_receipt">Purchase Receipt</flux:select.option>
            <flux:select.option value="return">Return</flux:select.option>
            <flux:select.option value="spoilage">Spoilage/Damage</flux:select.option>
            <flux:select.option value="inventory_count">Inventory Count</flux:select.option>
          </flux:select>
        </div>

        <div class="flex">
          <flux:spacer />
          <flux:button type="button" variant="ghost" wire:click="closeModal" class="mr-2 cursor-pointer">
            Cancel
          </flux:button>
          <flux:button type="submit" variant="primary" class="cursor-pointer">Adjust Stock</flux:button>
        </div>
      </form>
    </div>
  </flux:modal>
</div>
