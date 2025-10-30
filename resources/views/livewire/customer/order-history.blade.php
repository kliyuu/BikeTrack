<div class="max-w-7xl space-y-6">
  <div class="my-6">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Order History</h1>
    <p class="mt-2 text-gray-600 dark:text-gray-400">Track and manage all your orders in one place.</p>
  </div>

  {{-- @dump($orders) --}}

  <!-- Order Statistics Cards -->
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <div class="bg-white overflow-hidden shadow rounded-lg dark:bg-gray-800">
      <div class="p-5">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
              <flux:icon name="document-text" class="w-5 h-5 text-white" />
            </div>
          </div>
          <div class="ml-5 w-0 flex-1">
            <dl>
              <dt class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Total Orders</dt>
              <dd class="text-lg font-medium text-gray-900 dark:text-white">
                {{ number_format($orderStats['totalOrders']) }}
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
              <flux:icon name="banknotes" class="w-5 h-5 text-white" />
            </div>
          </div>
          <div class="ml-5 w-0 flex-1">
            <dl>
              <dt class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Total Spent</dt>
              <dd class="text-lg font-medium text-gray-900 dark:text-white">
                ₱ {{ number_format($orderStats['totalSpent'], 2) }}
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
            <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
              <flux:icon name="clock" class="w-5 h-5 text-white" />
            </div>
          </div>
          <div class="ml-5 w-0 flex-1">
            <dl>
              <dt class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Pending</dt>
              <dd class="text-lg font-medium text-gray-900 dark:text-white">
                {{ number_format($orderStats['pendingOrders']) }}
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
            <div class="w-8 h-8 bg-green-600 rounded-md flex items-center justify-center">
              <flux:icon name="check" class="w-5 h-5 text-white" />
            </div>
          </div>
          <div class="ml-5 w-0 flex-1">
            <dl>
              <dt class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Delivered</dt>
              <dd class="text-lg font-medium text-gray-900 dark:text-white">
                {{ number_format($orderStats['completedOrders']) }}
              </dd>
            </dl>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Filters -->
  <x-card class="bg-white border border-gray-100 shadow-sm sm:rounded-lg">
    <div class="card-body">
      <div class="grid grid-cols-1 gap-4 sm:grid-cols-5 py-6">
        <div class="lg:col-span-2">
          <flux:input label="Search" wire:model.live.debounce.300ms="search" placeholder="Search order number..." />
        </div>

        <div>
          <flux:select label="Status" wire:model.live="statusFilter">
            <flux:select.option value="">All Status</flux:select.option>
            <flux:select.option value="pending">Pending</flux:select.option>
            <flux:select.option value="confirmed">Confirmed</flux:select.option>
            <flux:select.option value="processing">Processing</flux:select.option>
            <flux:select.option value="shipped">Shipped</flux:select.option>
            <flux:select.option value="delivered">Delivered</flux:select.option>
            <flux:select.option value="cancelled">Cancelled</flux:select.option>
          </flux:select>
        </div>

        <div>
          <flux:input label="From Date" type="date" wire:model.live="dateFrom" />
        </div>

        <div>
          <flux:input label="To Date" type="date" wire:model.live="dateTo" />
        </div>
      </div>
    </div>
  </x-card>

  <!-- Orders Table -->
  <div class="bg-white border border-gray-100 shadow-sm overflow-hidden sm:rounded-lg mb-6 dark:bg-gray-800 dark:border-gray-700">
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-800">
          <tr>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              <button wire:click="sortBy('order_number')"
                class="flex items-center space-x-1 hover:text-gray-700 dark:hover:text-gray-300">
                <span class="uppercase">Order Number</span>
                @if ($sortField === 'order_number')
                  @if ($sortDirection === 'asc')
                    <flux:icon name="chevron-up" class="ml-1 w-3 h-3" />
                  @else
                    <flux:icon name="chevron-down" class="ml-1 w-3 h-3" />
                  @endif
                @else
                  <flux:icon name="chevron-up-down" class="ml-1 size-4" />
                @endif
              </button>
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">
              <button wire:click="sortBy('created_at')"
                class="flex items-center space-x-1 hover:text-gray-700 dark:hover:text-gray-300">
                <span class="uppercase">Date</span>
                @if ($sortField === 'created_at')
                  @if ($sortDirection === 'asc')
                    <flux:icon name="chevron-up" class="ml-1 w-3 h-3" />
                  @else
                    <flux:icon name="chevron-down" class="ml-1 w-3 h-3" />
                  @endif
                @else
                  <flux:icon name="chevron-up-down" class="ml-1 size-4" />
                @endif
              </button>
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">
              <span class="uppercase">Status</span>
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">
              <span class="uppercase">Items</span>
            </th>
            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 tracking-wider">
              <span class="uppercase">Total</span>
            </th>
            <th scope="col" class="px-6 py-3 text-center text-xs font-medium text-gray-500 tracking-wider">
              <span class="uppercase">Actions</span>
            </th>
          </tr>
        </thead>

        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
          @forelse($orders as $order)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                {{ $order->order_number }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-white">
                {{ $order->created_at->format('M j, Y') }}
                <div class="text-xs text-gray-400">{{ $order->created_at->format('g:i A') }}</div>
              </td>
              <td class="px-6 py-4 whitespace-nowrap">
                <flux:badge color="{{ $this->getStatusBadgeColor($order->status) }}" class="text-xs font-medium">
                  {{ ucfirst($order->status) }}
                </flux:badge>
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-white">
                {{ $order->items->count() }} {{ Str::plural('item', $order->items->count()) }}
                @if ($order->items->count() > 0 && $order->items->first()->product)
                  <div class="text-xs text-gray-400 mt-1">
                    {{ $order->items->first()->product->name }}
                    @if ($order->items->count() > 1)
                      + {{ $order->items->count() - 1 }} more
                    @endif
                  </div>
                @endif
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                ₱ {{ number_format($order->total_amount, 2) }}
              </td>
              <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                <a href="{{ route('client.order-details', $order->id) }}"
                  class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-600">
                  View Details
                </a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="px-6 py-12 text-center">
                <div class="flex flex-col items-center">
                  <flux:icon name="clipboard-document-list" class="w-12 h-12 text-gray-400 mb-4" />
                  <h3 class="text-lg font-medium text-gray-900 mb-2 dark:text-white">No orders found</h3>
                  <p class="text-gray-500 mb-4 dark:text-white">You haven't placed any orders yet or no orders match your current
                    filters.</p>
                  <a href="{{ route('shop.catalog') }}"
                    class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700">
                    Browse Products
                  </a>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    @if ($orders->hasPages())
      <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
        {{ $orders->links() }}
      </div>
    @endif
  </div>
</div>
