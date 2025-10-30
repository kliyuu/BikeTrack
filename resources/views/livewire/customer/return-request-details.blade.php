<div class="max-w-7xl">
  <div class="py-6">
    <div class="mb-4">
      <flux:breadcrumbs>
        <flux:breadcrumbs.item href="{{ route('shop') }}">Home</flux:breadcrumbs.item>
        <flux:breadcrumbs.item href="{{ route('client.return-orders') }}">Return Orders</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ $returnRequest->orderItem->product->name }}</flux:breadcrumbs.item>
      </flux:breadcrumbs>
    </div>

    <div class="bg-white dark:bg-gray-900 shadow-md sm:rounded-lg">
      <div class="p-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Product Information -->
          <div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Product Information</h3>
            <dl class="space-y-3">
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Product</dt>
                <dd class="text-sm text-gray-900 dark:text-white">{{ $returnRequest->orderItem->product->name }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">SKU</dt>
                <dd class="text-sm text-gray-900 dark:text-white">{{ $returnRequest->orderItem->product->sku }}</dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Original Order</dt>
                <dd class="text-sm text-gray-900 dark:text-white">
                  <a href="{{ route('client.order-details', $returnRequest->orderItem->order) }}"
                    class="text-blue-600 hover:text-blue-500">
                    Order #{{ $returnRequest->orderItem->order->order_number }}
                  </a>
                </dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Quantity Returned</dt>
                <dd class="text-sm text-gray-900 dark:text-white">{{ $returnRequest->quantity }} of
                  {{ $returnRequest->orderItem->quantity }}</dd>
              </div>
            </dl>
          </div>

          <!-- Return Information -->
          <div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Return Information</h3>
            <dl class="space-y-3">
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                <dd class="text-sm">
                  <flux:badge size="sm" color="{{ $this->getStatusBadgeColor($returnRequest->status) }}">
                    {{ ucfirst($returnRequest->status) }}
                  </flux:badge>
                </dd>
              </div>
              <div>
                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Request Date</dt>
                <dd class="text-sm text-gray-900 dark:text-white">
                  {{ $returnRequest->requested_at->format('F j, Y \a\t g:i A') }}</dd>
              </div>
              @if ($returnRequest->processed_at)
                <div>
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Processed Date</dt>
                  <dd class="text-sm text-gray-900 dark:text-white">
                    {{ $returnRequest->processed_at->format('F j, Y \a\t g:i A') }}</dd>
                </div>
              @endif
              @if ($returnRequest->processedBy)
                <div>
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Processed By</dt>
                  <dd class="text-sm text-gray-900 dark:text-white">{{ $returnRequest->processedBy->name }}</dd>
                </div>
              @endif
            </dl>
          </div>
        </div>

        <!-- Reason Section -->
        @if ($returnRequest->reason)
          <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Reason for Return</h3>
            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $returnRequest->reason }}</p>
          </div>
        @endif

        <!-- Admin Notes Section -->
        @if ($returnRequest->notes)
          <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-6">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Notes</h3>
            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $returnRequest->notes }}</p>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
