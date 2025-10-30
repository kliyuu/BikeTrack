<div class="max-w-7xl space-y-6">
  <div class="sm:flex sm:items-center">
    <div class="sm:flex-auto">
      <h1 class="text-2xl font-semibold leading-6 text-gray-900 dark:text-white">All Orders</h1>
      <p class="mt-2 text-sm text-gray-700 dark:text-gray-400">Manage and track all customer orders.</p>
    </div>
  </div>

  <div class="space-y-6">
    <!-- Order Statistics Cards -->
    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mb-6">
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
                  {{ number_format($orderStats['total']) }}
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
                <dt class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Pending Orders</dt>
                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                  {{ number_format($orderStats['pending']) }}
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
                <flux:icon name="check" class="w-5 h-5 text-white" />
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Delivered Orders</dt>
                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                  {{ number_format($orderStats['delivered']) }}
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
              <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                <flux:icon name="currency-dollar" class="w-5 h-5 text-white" />
              </div>
            </div>
            <div class="ml-5 w-0 flex-1">
              <dl>
                <dt class="text-sm font-medium text-gray-500 truncate dark:text-gray-400">Total Revenue</dt>
                <dd class="text-lg font-medium text-gray-900 dark:text-white">
                  ₱{{ number_format($orderStats['total_revenue'], 2) }}
                </dd>
              </dl>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Filters -->
    <x-card class="bg-white border border-gray-100 shadow-sm rounded-md">
      <div class="card-body">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-5 py-6">
          <div class="lg:col-span-2">
            <flux:input label="Search" wire:model.live.debounce.300ms="search"
              placeholder="Search order number, clients..." />
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
    <x-card class="bg-white border border-gray-100 shadow-sm rounded-md">
      <div class="card-body">
        <div class="overflow-x-auto">
          <div class="inline-block min-w-full py-2 align-middle">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Order #
                  </th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Client</th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Status
                  </th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Total
                  </th>
                  <th scope="col"
                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Date
                  </th>
                  <th scope="col"
                    class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    Actions
                  </th>
                </tr>
              </thead>

              <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($orders as $order)
                  <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <a href="{{ route('admin.order-details', $order->id) }}"
                        class="text-sm text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200">
                        #{{ $order->order_number }}
                      </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div>
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                          {{ $order->client->company_name }}
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $order->client->code }}</div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <flux:badge size="sm" color="{{ $this->getStatusBadgeColor($order->status) }}">
                        {{ ucfirst($order->status) }}
                      </flux:badge>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900 dark:text-white">
                        ₱{{ number_format($order->total_amount, 2) }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="text-sm text-gray-900 dark:text-white">
                        {{ $order->created_at->format('M j, Y') }}
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div class="flex justify-center space-x-2">
                        <flux:dropdown>
                          <flux:button size="xs" variant="primary" icon="ellipsis-vertical"
                            icon:variant="outline"
                            class="text-gray-600 bg-gray-100 hover:bg-gray-200 border-0 cursor-pointer" />

                          <flux:navmenu>
                            <flux:navmenu.item icon="eye" href="{{ route('admin.order-details', $order->id) }}">
                              View Details
                            </flux:navmenu.item>
                            <flux:navmenu.item icon="pencil-square"
                              wire:click.prevent="openStatusModal({{ $order->id }})" class="cursor-pointer">
                              Update Status
                            </flux:navmenu.item>
                          </flux:navmenu>
                        </flux:dropdown>
                        {{-- <a href="{{ route('admin.order-details', $order->id) }}"
                          class="text-sm text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-200">
                          View
                        </a>
                        <button type="button" wire:click.stop="openStatusModal({{ $order->id }})"
                          class="text-sm text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 cursor-pointer"
                          aria-haspopup="dialog" aria-controls="status-modal">
                          Update Status
                        </button> --}}
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="px-6 py-4 whitespace-nowrap">
                      <div class="flex flex-col items-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                          viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                          </path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No orders found</h3>
                        <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter criteria.</p>
                      </div>
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
          {{ $orders->links() }}
        </div>
      </div>
    </x-card>
  </div>

  <!-- Toast Notifications -->
  <x-toast />

  <!-- Update Status Modal -->
  <flux:modal name="update-order-status" class="md:w-96">
    <div class="space-y-6">
      <div>
        <flux:heading size="lg">Update Order Status</flux:heading>
      </div>

      <div>
        <flux:select label="Status" wire:model="orderStatus">
          <option value="pending" {{in_array($this->getCurrentOrder()?->status, ['confirmed', 'processing', 'shipped', 'delivered', 'cancelled']) ? 'disabled' : '' }}>
            Pending
          </option>
          <flux:select.option value="confirmed">Confirmed</flux:select.option>
          <flux:select.option value="processing">Processing</flux:select.option>
          <flux:select.option value="shipped">Shipped</flux:select.option>
          <flux:select.option value="delivered">Delivered</flux:select.option>
          <option value="cancelled" {{ in_array($this->getCurrentOrder()?->status, ['confirmed', 'processing', 'shipped', 'delivered']) ? 'disabled' : '' }}>
            Cancelled
          </option>
        </flux:select>
      </div>

      <div class="flex justify-end space-x-3">
        <flux:spacer />

        <flux:modal.close>
          <flux:button class="cursor-pointer">Cancel</flux:button>
        </flux:modal.close>
        <flux:button wire:click="updateOrderStatus" variant="primary" color="blue" class="cursor-pointer">
          Update Status
        </flux:button>
      </div>
    </div>
  </flux:modal>
</div>
