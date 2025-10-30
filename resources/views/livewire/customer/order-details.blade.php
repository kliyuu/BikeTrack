<div class="max-w-7xl">
  {{-- @dump($user) --}}

  <!-- Header -->
  <div class="my-8">
    <div class="flex items-center justify-between">
      <div class="space-y-1.5">
        <div class="flex gap-4 items-center">
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Order #{{ $order->order_number }}</h1>
          <div>
            <flux:badge size="sm" color="{{ $statusBadgeColor }}">
              {{ ucfirst($order->status) }}
            </flux:badge>
          </div>
        </div>
        <p class="text-sm text-gray-600 dark:text-gray-400">
          Placed on {{ $order->created_at->format('F j, Y \a\t g:i A') }}
        </p>
      </div>

      <div class="flex items-center space-x-4">
        <div class="flex space-x-2">
          {{-- <flux:button wire:click="downloadInvoice">Download Invoice</flux:button> --}}

          @if ($order->status === 'delivered')
            <flux:button wire:click="markAsReceived" variant="primary" color="blue" class="cursor-pointer">
              Order Received
            </flux:button>
          @endif
        </div>
      </div>
    </div>
  </div>

  <!-- Auto-completion Notice -->
  @if ($order->status === 'delivered' && $order->delivered_at)
    @php
      $hoursRemaining = 24 - now()->diffInHours($order->delivered_at);
    @endphp
    @if ($hoursRemaining > 0)
      <flux:callout variant="info" class="mb-6">
        <strong>Order Delivered:</strong> This order will be automatically marked as completed in approximately
        {{ number_format($hoursRemaining, 0) }} {{ Str::plural('hour', $hoursRemaining) }} if not confirmed manually.
      </flux:callout>
    @endif
  @endif

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Order Details -->
    <div class="lg:col-span-2 space-y-6">
      <!-- Order Items -->
      <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl dark:bg-gray-800">
        <div class="px-6 py-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-6 dark:text-white">Order Items</h3>
          <div class="flow-root">
            <div class="overflow-x-auto">
              <div class="inline-block min-w-full py-2 align-middle">
                <table class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                  <thead>
                    <tr>
                      <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-white">Product
                      </th>
                      <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">SKU</th>
                      <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-white">Price</th>
                      <th class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900 dark:text-white">Quantity
                      </th>
                      <th class="px-3 py-3.5 text-center text-sm font-semibold text-gray-900 dark:text-white">Total</th>
                    </tr>
                  </thead>
                  <tbody class="divide-y divide-gray-200 dark:divide-gray-700 dark:bg-gray-800">
                    @foreach ($order->items as $item)
                      <tr>
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm">
                          <div class="flex items-center">
                            <div class="h-12 w-12 flex-shrink-0">
                              @if ($item->product && $item->product->primaryImage)
                                <img class="h-12 w-12 rounded-lg object-cover"
                                  src='{{ asset("storage/{$item->product->primaryImage->url}") }}'
                                  alt="{{ $item->product->name }}">
                              @else
                                <div
                                  class="h-12 w-12 rounded-lg bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                  <flux:icon name="photo" class="w-6 h-6 text-gray-400" />
                                </div>
                              @endif
                            </div>
                            <div class="ml-4">
                              <div class="font-medium text-gray-900 dark:text-white">
                                {{ $item->product->name ?? 'Product Unavailable' }}</div>
                              <div class="text-gray-500 dark:text-gray-400">
                                {{ $item->product->category->name ?? 'N/A' }}</div>
                            </div>
                          </div>
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-200">
                          {{ $item->product->sku ?? 'N/A' }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900 dark:text-white">
                          ₱ {{ number_format($item->unit_price, 2) }}
                        </td>
                        <td class="text-right whitespace-nowrap px-3 py-4 text-sm text-gray-900 dark:text-white">
                          {{ $item->quantity }}
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm font-semibold text-gray-900 dark:text-white">
                          ₱ {{ number_format($item->line_total, 2) }}
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="space-y-6">
      <!-- Order Summary -->
      <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl dark:bg-gray-800">
        <div class="px-6 py-6">
          <h3 class="text-lg font-semibold text-gray-900 mb-6 dark:text-white">Order Summary</h3>
          <dl class="space-y-4">
            <div class="flex items-center justify-between">
              <dt class="text-sm text-gray-600 dark:text-gray-400">
                Subtotal ({{ $orderSummary['total_items'] }} items)
              </dt>
              <dd class="text-sm font-medium text-gray-900 dark:text-white">
                ₱ {{ number_format($orderSummary['subtotal'], 2) }}
              </dd>
            </div>
            @if ($orderSummary['tax_amount'] > 0)
              <div class="flex items-center justify-between">
                <dt class="text-sm text-gray-600 dark:text-gray-400">Tax</dt>
                <dd class="text-sm font-medium text-gray-900 dark:text-white">
                  ₱ {{ number_format($orderSummary['tax_amount'], 2) }}
                </dd>
              </div>
            @endif
            @if ($orderSummary['shipping_amount'] > 0)
              <div class="flex items-center justify-between">
                <dt class="text-sm text-gray-600 dark:text-gray-400">Shipping Fee</dt>
                <dd class="text-sm font-medium text-gray-900 dark:text-white">
                  ₱ {{ number_format($orderSummary['shipping_amount'], 2) }}
                </dd>
              </div>
            @endif
            <div class="flex items-center justify-between border-t border-gray-200 pt-4">
              <dt class="text-lg font-semibold text-gray-900 dark:text-white">Total</dt>
              <dd class="text-lg font-semibold text-gray-900 dark:text-white">
                ₱ {{ number_format($orderSummary['total_amount'], 2) }}
              </dd>
            </div>
          </dl>
        </div>
      </div>

      <!-- Order Timeline -->
      @if ($order->delivered_at || $order->shipped_at || $order->cancelled_at)
        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl dark:bg-gray-800">
          <div class="px-6 py-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 dark:text-white">Order Timeline</h3>
            <dl class="space-y-3 text-sm">
              @if ($order->shipped_at)
                <div class="flex items-center justify-between">
                  <dt class="text-gray-600 dark:text-gray-400 flex items-center gap-2">
                    <flux:icon name="truck" class="w-4 h-4" />
                    Shipped
                  </dt>
                  <dd class="text-gray-900 dark:text-white">
                    {{ $order->shipped_at->format('M j, Y g:i A') }}
                  </dd>
                </div>
              @endif
              @if ($order->delivered_at)
                <div class="flex items-center justify-between">
                  <dt class="text-gray-600 dark:text-gray-400 flex items-center gap-2">
                    <flux:icon name="check-circle" class="w-4 h-4" />
                    Delivered
                  </dt>
                  <dd class="text-gray-900 dark:text-white">
                    {{ $order->delivered_at->format('M j, Y g:i A') }}
                  </dd>
                </div>
              @endif
              @if ($order->cancelled_at)
                <div class="flex items-center justify-between">
                  <dt class="text-gray-600 dark:text-gray-400 flex items-center gap-2">
                    <flux:icon name="x-circle" class="w-4 h-4" />
                    Cancelled
                  </dt>
                  <dd class="text-gray-900 dark:text-white">
                    {{ $order->cancelled_at->format('M j, Y g:i A') }}
                  </dd>
                </div>
              @endif
            </dl>
          </div>
        </div>
      @endif

      <!-- Shipping Information -->
      @if ($order->billing_address)
        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl dark:bg-gray-800">
          <div class="px-6 py-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4 dark:text-white">Billing Address</h3>
            <div class="text-sm text-gray-600 whitespace-pre-line dark:text-gray-400">{{ $order->billing_address }}
            </div>
          </div>
        </div>
      @endif
    </div>
  </div>

  <!-- Navigation -->
  <div class="my-8 flex justify-between">
    <div class="flex space-x-3">
      <flux:button icon="arrow-left" href="{{ route('client.order-history') }}">
        Back to Orders
      </flux:button>

      @if ($order->status !== 'cancelled')
        <flux:button icon="shopping-cart" variant="primary" color="blue" href="{{ route('shop.catalog') }}"
          class="cursor-pointer">
          Continue Shopping
        </flux:button>
      @endif
    </div>

    <div class="flex space-x-3">
      {{-- <button wire:click="printOrder"
        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
        Print Order
      </button> --}}
    </div>
  </div>
</div>
