<div class="max-w-7xl">
  <div class="mb-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Restock Histories</h1>
      </div>
    </div>
  </div>

  <!-- Statistics Cards -->
  <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
      <div class="p-5">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <flux:icon name="clipboard-document-list" class="w-6 h-6 text-blue-600 dark:text-blue-400"></flux:icon>
          </div>
          <div class="ml-5 w-0 flex-1">
            <dl>
              <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Transactions</dt>
              <dd class="text-lg font-medium text-gray-900 dark:text-white">
                {{ number_format($restockStats['totalTransactions']) }}</dd>
            </dl>
          </div>
        </div>
      </div>
    </div>

    {{-- <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
      <div class="p-5">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12">
              </path>
            </svg>
          </div>
          <div class="ml-5 w-0 flex-1">
            <dl>
              <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Stock In</dt>
              <dd class="text-lg font-medium text-green-600 dark:text-green-400">
                {{ number_format($restockStats['stock_in']) }}</dd>
            </dl>
          </div>
        </div>
      </div>
    </div>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
      <div class="p-5">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <svg class="h-6 w-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6">
              </path>
            </svg>
          </div>
          <div class="ml-5 w-0 flex-1">
            <dl>
              <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Stock Out</dt>
              <dd class="text-lg font-medium text-red-600 dark:text-red-400">
                {{ number_format($restockStats['stock_out']) }}</dd>
            </dl>
          </div>
        </div>
      </div>
    </div>

    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
      <div class="p-5">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <svg class="h-6 w-6 text-{{ $restockStats['net_change'] >= 0 ? 'green' : 'red' }}-400" fill="none"
              stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6">
              </path>
            </svg>
          </div>
          <div class="ml-5 w-0 flex-1">
            <dl>
              <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Net Change</dt>
              <dd
                class="text-lg font-medium text-{{ $restockStats['net_change'] >= 0 ? 'green' : 'red' }}-600 dark:text-{{ $restockStats['net_change'] >= 0 ? 'green' : 'red' }}-400">
                {{ $restockStats['net_change'] >= 0 ? '+' : '' }}{{ number_format($restockStats['net_change']) }}
              </dd>
            </dl>
          </div>
        </div>
      </div>
    </div> --}}
  </div>

  <!-- Search and Filters -->
  <x-card class="bg-white border border-gray-100 shadow-sm rounded-md mb-6">
    <div class="space-y-2">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-4">
        <div class="flex flex-col gap-2 lg:col-span-2">
          <flux:label>Search Products</flux:label>
          <input type="text" wire:model.live.debounce.100ms="search" id="search"
            class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            placeholder="Search by SKU, name, or barcode...">
        </div>

        <div class="flex flex-col gap-2 md:ml-4">
          <flux:label>Products</flux:label>
          <flux:select wire:model.live="brandFilter" placeholder="All Products">
            @foreach ($products as $product)
              <flux:select.option value="{{ $product->id }}" class="text-gray-700">{{ $product->name }}</flux:select.option>
            @endforeach
          </flux:select>
        </div>
      </div>
    </div>
  </x-card>

  <x-card class="bg-white border border-gray-100 shadow-sm rounded-md">
    <div class="card-body">
      <div class="flex items-center justify-between mb-6 border-b border-gray-200 pb-4">
        <h2 class="text-base">{{ __('Histories') }}</h2>
      </div>
    </div>

    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-700">
          <tr>
            <th scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
              <button wire:click="sortBy('created_at')"
                class="flex items-center hover:text-gray-700 dark:hover:text-gray-200">
                Date & Time
                @if ($sortField === 'created_at')
                  <svg class="ml-1 w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    @if ($sortDirection === 'asc')
                      <path fill-rule="evenodd"
                        d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                        clip-rule="evenodd" />
                    @else
                      <path fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                    @endif
                  </svg>
                @endif
              </button>
            </th>
            <th scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
              Product</th>
            <th scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
              Warehouse</th>
            <th scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
              <button wire:click="sortBy('quantity')"
                class="flex items-center hover:text-gray-700 dark:hover:text-gray-200">
                Quantity
                @if ($sortField === 'quantity')
                  <svg class="ml-1 w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                    @if ($sortDirection === 'asc')
                      <path fill-rule="evenodd"
                        d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z"
                        clip-rule="evenodd" />
                    @else
                      <path fill-rule="evenodd"
                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                    @endif
                  </svg>
                @endif
              </button>
            </th>
            <th scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
              Reason</th>
            <th scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
              Performed By</th>
            <th scope="col"
              class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
              Reference</th>
          </tr>
        </thead>
        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
          @forelse($restockHistories as $history)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900 dark:text-white">{{ $history->created_at->format('M j, Y') }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $history->created_at->format('g:i A') }}</div>
              </td>
              <td class="px-6 py-4">
                <div class="flex items-center">
                  <div class="flex-shrink-0 h-8 w-8">
                    <img class="h-8 w-8 rounded object-cover" src='{{ asset("storage/{$history->product->primaryImage->url}") }}'
                      alt="{{ $history->product->name }}">
                  </div>
                  <div class="ml-3">
                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $history->product->name }}</div>
                    <div class="text-xs text-gray-500 dark:text-gray-400">{{ $history->product->sku }}</div>
                  </div>
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900 dark:text-white">{{ $history->warehouse->name }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $history->warehouse->code ?? 'N/A' }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900 dark:text-white">
                  {{ $history->type === 'in' ? '+' : '+' }}{{ number_format($history->quantity_change) }}
                </div>
                <div class="text-xs text-gray-500 dark:text-gray-400">units</div>
              </td>
              <td class="px-6 py-4">
                <div class="text-sm text-gray-900 dark:text-white">
                  {{ ucwords(str_replace('_', ' ', $history->reason)) ?: 'No reason provided' }}
                </div>
                @if ($history->reference_type)
                  <div class="text-xs text-gray-500 dark:text-gray-400">
                    {{ ucfirst(str_replace('_', ' ', $history->reference_type)) }}</div>
                @endif
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm text-gray-900 dark:text-white">{{ $history->performedBy->name ?? 'System' }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $history->performedBy->email ?? 'N/A' }}
                </div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                @if ($history->reference_type && $history->reference_id)
                  <div class="text-xs text-gray-500 dark:text-gray-400">
                    {{ ucfirst(str_replace('_', ' ', $history->reference_type)) }}
                  </div>
                  <div class="text-xs text-gray-400 dark:text-gray-500">#{{ $history->reference_id }}</div>
                @else
                  <div class="text-xs text-gray-400 dark:text-gray-500">No reference</div>
                @endif
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="px-6 py-12 text-center">
                <div class="text-gray-500 dark:text-gray-400">
                  <flux:icon name="square-3-stack-3d" class="mx-auto h-12 w-12 mb-4" />
                  <p class="text-sm">No restock history found</p>
                  <p class="text-xs mt-1">Try adjusting your search or filters</p>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </x-card>
</div>
