<div class="max-w-7xl">
  <div class="my-6">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Return Requests</h1>
    <p class="mt-2 text-gray-600 dark:text-gray-400">Track and manage all your return requests in one place.</p>
  </div>

  {{-- @dump($orders) --}}
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Return Details -->
    <div class="lg:col-span-2 space-y-6">
      <!-- Return Items -->
      <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl dark:bg-gray-800">
        <div class="px-6 py-6">
          <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">Return Requests</h3>
          <div class="flow-root">
            @forelse ($returnRequests as $returnRequest)
              <div class="p-4 border-b border-gray-200 dark:border-gray-800 last:border-b-0">
                <div class="flex items-center justify-between">
                  <div class="flex-1">
                    <div class="flex items-center gap-4">
                      <div>
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                          {{ $returnRequest->orderItem->product->name }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                          Order #{{ $returnRequest->orderItem->order->order_number }} • Qty:
                          {{ $returnRequest->quantity }}
                        </div>
                      </div>
                    </div>
                    <div class="mt-2 flex items-center gap-4">
                      <flux:badge size="sm" color="{{ $this->getStatusBadgeColor($returnRequest->status) }}">
                        {{ ucfirst($returnRequest->status) }}
                      </flux:badge>
                      <div class="text-xs text-gray-500 dark:text-gray-400">
                        Requested {{ $returnRequest->requested_at->format('M j, Y') }}
                      </div>
                    </div>
                  </div>
                  <div>
                    <flux:button variant="ghost" size="sm"
                      href="{{ route('client.return-orders.show', $returnRequest) }}">
                      View Details
                    </flux:button>
                  </div>
                </div>
              </div>
            @empty
              <div class="flex justify-center p-4 text-sm text-gray-500 dark:text-gray-400">No return requests found.
              </div>
            @endforelse
          </div>
        </div>
      </div>
    </div>

    <div class="space-y-6">
      <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl dark:bg-gray-800">
        <div class="px-6 py-6">
          <div class="space-y-4 mb-6">
            <flux:label class="text-lg font-semibold text-gray-900 dark:text-white">Create New Return Request</flux:label>
            <div class="mt-2">
              <x-select wire:model="orderItemId">
                <option value="" class="dark:text-gray-600">Please select</option>

                @foreach ($orders as $order)
                  <optgroup label="Order #{{ $order->order_number }}" class="dark:text-gray-600">
                    @foreach ($order->items as $item)
                      <option value="{{ $item->id }}" class="dark:text-gray-600">
                        {{ $item->product->name }} (x{{ $item->quantity }})
                      </option>
                    @endforeach
                  </optgroup>
                @endforeach
              </x-select>
            </div>
            {{-- <flux:select wire:model="orderItemId" label="Order Item">
              <flux:select.option value="" selected disabled>Select an item...</flux:select.option>
              @foreach ($orders as $order)
                <flux:menu.group label="Order #{{ $order->order_number }}">
                  @foreach ($order->items as $item)
                    <flux:select.option value="{{ $item->id }}">
                      {{ $item->product->name }} (x{{ $item->quantity }})
                    </flux:select.option>
                  @endforeach
                </flux:menu.group>
              @endforeach
            </flux:select> --}}

            <flux:input type="number" min="1" wire:model="quantity" label="Quantity" />
            <flux:textarea wire:model="reason" label="Reason (optional)" />
          </div>
          <div>
            <flux:button wire:click="submit" variant="primary">Submit Request</flux:button>
          </div>
        </div>
      </div>

      <div class="mt-8">
        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Recent Orders</h2>
        <div class="bg-white dark:bg-gray-900 shadow sm:rounded-lg divide-y divide-gray-200 dark:divide-gray-800">
          @forelse ($orders as $order)
            <div class="p-4">
              <div class="text-sm text-gray-500 dark:text-gray-400">Order #{{ $order->order_number }}</div>
              <ul class="mt-2 grid gap-2">
                @foreach ($order->items as $item)
                  <li class="flex items-center justify-between text-sm">
                    <div class="text-gray-900 dark:text-white">{{ $item->product->name }}</div>
                    <div class="text-gray-600 dark:text-gray-300">Qty: {{ $item->quantity }}</div>
                  </li>
                @endforeach
              </ul>
            </div>
          @empty
            <div class="flex justify-center p-4 text-sm text-gray-500 dark:text-gray-400">No recent orders.</div>
          @endforelse
        </div>
      </div>
    </div>
  </div>
</div>
