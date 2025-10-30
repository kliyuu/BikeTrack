<div class="max-w-7xl">
  {{-- @dump($order) --}}
  <div class="mb-4">
    <flux:breadcrumbs>
      <flux:breadcrumbs.item href="{{ route('admin.orders') }}">Orders</flux:breadcrumbs.item>
      <flux:breadcrumbs.item>{{ $order->order_number }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>
  </div>

  <div class="space-y-6">
    <!-- Order Header -->
    <x-card class="bg-white border border-gray-100 shadow-sm rounded-md">
      <div class="card-body">
        <div class="flex items-center justify-between">
          <div>
            <div class="flex gap-2">
              <h1 class="text-2xl font-bold leading-7 text-gray-900 dark:text-white">Order #{{ $order->order_number }}
              </h1>
              <div>
                <flux:badge size="sm" color="{{ $this->getStatusBadgeColor($order->status) }}">
                  {{ ucfirst($order->status) }}
                </flux:badge>
              </div>
            </div>

            <p class="mt-1 text-sm leading-6 text-gray-600 dark:text-gray-400">
              Placed on {{ $order->created_at->format('M j, Y \a\t g:i A') }}
            </p>
          </div>
          <div class="flex items-center space-x-4">
            <div class="flex gap-4">
              <flux:modal.trigger name="update-order-status">
                <flux:button class="cursor-pointer">Update Status</flux:button>
              </flux:modal.trigger>

              {{-- <flux:button wire:click="openNoteModal" class="cursor-pointer">
                Add Note
              </flux:button> --}}
              {{-- <button wire:click="printOrder" type="button"
                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Print
              </button> --}}
            </div>
          </div>
        </div>
      </div>
    </x-card>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
      <!-- Order Information -->
      <div class="lg:col-span-2 space-y-6">
        <!-- Order Items -->
        <x-card class="bg-white border border-gray-100 shadow-sm rounded-md">
          <div class="card-body">
            <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">Order Items</h3>
            <div class="mt-6 flow-root">
              <div class="overflow-x-auto">
                <div class="inline-block min-w-full py-2 align-middle">
                  <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                      <tr>
                        <th
                          class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                          Product
                        </th>
                        <th
                          class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                          SKU
                        </th>
                        <th
                          class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                          Price
                        </th>
                        <th
                          class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                          Quantity
                        </th>
                        <th
                          class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                          Total
                        </th>
                      </tr>
                    </thead>

                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                      @foreach ($order->items as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                          <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <div class="flex items-center">
                              <div class="h-10 w-10 flex-shrink-0">
                                <img class="h-10 w-10 rounded-lg object-cover"
                                  src='{{ asset("storage/{$item->product->primaryImage->url}") }}' alt="{{ $item->product->name }}">
                              </div>
                              <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                  {{ $item->product->name }}
                                </div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                  {{ $item->product->category->name ?? 'N/A' }}
                                </div>
                              </div>
                            </div>
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {{ $item->product->sku }}
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm">
                            ₱{{ number_format($item->unit_price, 2) }}
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm">
                            {{ $item->quantity }}
                          </td>
                          <td class="px-6 py-4 whitespace-nowrap text-sm">
                            ₱{{ number_format($item->line_total, 2) }}
                          </td>
                        </tr>
                      @endforeach
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div>
        </x-card>

        <!-- Order Notes -->
        @if ($order->notes)
          <x-card class="bg-white border border-gray-100 shadow-sm rounded-md">
            <div class="px-4 py-6 sm:px-8">
              <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">Order Notes</h3>
              <div class="mt-4 whitespace-pre-wrap text-sm text-gray-600 dark:text-gray-400">{{ $order->notes }}</div>
            </div>
          </x-card>
        @endif
      </div>

      <!-- Order Summary & Client Info -->
      <div class="space-y-6">
        <!-- Order Summary -->
        <x-card class="bg-white border border-gray-100 shadow-sm rounded-md">
          <div class="card-body">
            <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">Order Summary</h3>
            <dl class="mt-6 space-y-4">
              <div class="flex items-center justify-between">
                <dt class="text-sm text-gray-600 dark:text-gray-100">
                  Subtotal ({{ $orderSummary['total_items'] }} items)
                </dt>
                <dd class="text-sm font-medium text-gray-900 dark:text-white">
                  ₱{{ number_format($orderSummary['subtotal'], 2) }}
                </dd>
              </div>
              @if ($orderSummary['tax_amount'] > 0)
                <div class="flex items-center justify-between">
                  <dt class="text-sm text-gray-600 dark:text-gray-400">Tax</dt>
                  <dd class="text-sm font-medium text-gray-900 dark:text-white">
                    ₱{{ number_format($orderSummary['tax_amount'], 2) }}
                  </dd>
                </div>
              @endif
              @if ($orderSummary['shipping_amount'] > 0)
                <div class="flex items-center justify-between">
                  <dt class="text-sm text-gray-600 dark:text-gray-100">Shipping</dt>
                  <dd class="text-sm font-medium text-gray-900 dark:text-white">
                    ₱{{ number_format($orderSummary['shipping_amount'], 2) }}
                  </dd>
                </div>
              @endif
              <div class="flex items-center justify-between border-t border-gray-200 pt-4">
                <dt class="text-base font-medium text-gray-900 dark:text-white">Total</dt>
                <dd class="text-base font-medium text-gray-900 dark:text-white">
                  ₱{{ number_format($orderSummary['total_amount'], 2) }}
                </dd>
              </div>
            </dl>
          </div>
        </x-card>

        <!-- Client Information -->
        <x-card class="bg-white border border-gray-100 shadow-sm rounded-md">
          <div class="card-body">
            <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">Client Information</h3>
            <dl class="mt-6 space-y-4">
              <div>
                <dt class="text-sm font-semibold text-gray-900 dark:text-white">Company</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                  {{ $order->client->company_name }}
                </dd>
              </div>
              <div>
                <dt class="text-sm font-semibold text-gray-900 dark:text-white">Client Code</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                  {{ $order->client->code }}
                </dd>
              </div>
              <div>
                <dt class="text-sm font-semibold text-gray-900 dark:text-white">Contact Person</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                  {{ $order->client->contact_name ?? 'N/A' }}
                </dd>
              </div>
              <div>
                <dt class="text-sm font-semibold text-gray-900 dark:text-white">Contact Number</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                  {{ $order->client->contact_phone ?? 'N/A' }}
                </dd>
              </div>
              <div>
                <dt class="text-sm font-semibold text-gray-900 dark:text-white">Email</dt>
                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                  {{ $order->client->contact_email ?? 'N/A' }}
                </dd>
              </div>
            </dl>
          </div>
        </x-card>

        <!-- Shipping Address -->
        @if ($order->billing_address)
          <x-card class="bg-white border border-gray-100 shadow-sm rounded-md">
            <div class="card-body">
              <h3 class="text-base font-semibold leading-6 text-gray-900 dark:text-white">Billing Address</h3>
              <div class="mt-4 text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line">
                {{ $order->billing_address }}</div>
            </div>
          </x-card>
        @endif
      </div>
    </div>
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
          {{-- <flux:select.option value="pending">Pending</flux:select.option> --}}
          <option value="pending" {{in_array($this->order?->status, ['confirmed', 'processing', 'shipped', 'delivered', 'cancelled']) ? 'disabled' : '' }}>
            Pending
          </option>
          <flux:select.option value="confirmed">Confirmed</flux:select.option>
          <flux:select.option value="processing">Processing</flux:select.option>
          <flux:select.option value="shipped">Shipped</flux:select.option>
          <flux:select.option value="delivered">Delivered</flux:select.option>
          <option value="cancelled" {{ in_array($this->order?->status, ['confirmed', 'processing', 'shipped', 'delivered']) ? 'disabled' : '' }}>
            Cancelled
          </option>
          {{-- <flux:select.option value="cancelled">Cancelled</flux:select.option> --}}
        </flux:select>
      </div>

      <div class="flex justify-end space-x-3">
        <flux:spacer />

        <flux:modal.close>
          <flux:button variant="ghost" class="cursor-pointer">Cancel</flux:button>
        </flux:modal.close>
        <flux:button wire:click="updateOrderStatus" variant="primary" color="blue">Update Status</flux:button>
      </div>
    </div>
  </flux:modal>
</div>
