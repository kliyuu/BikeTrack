<div class="max-w-7xl">
  <div class="space-y-6">
    <div class="flex items-center justify-between mb-6">
      <h1 class="text-2xl font-semibold text-gray-900 dark:text-white">Return Requests</h1>
    </div>

    <x-card class="bg-white border border-gray-100 shadow-sm rounded-md">
      <div class="card-body">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
          <div class="md:col-span-2">
            <flux:input wire:model.live.debounce.300ms="search" placeholder="Search products or orders..." />
          </div>

          <div>
            <flux:select wire:model.live="statusFilter">
              <flux:select.option value="">All statuses</flux:select.option>
              <flux:select.option value="requested">Requested</flux:select.option>
              <flux:select.option value="approved">Approved</flux:select.option>
              <flux:select.option value="rejected">Rejected</flux:select.option>
              <flux:select.option value="received">Received</flux:select.option>
            </flux:select>
          </div>
        </div>
      </div>
    </x-card>

    <div class="bg-white dark:bg-gray-900 shadow sm:rounded-lg overflow-hidden">
      <ul role="list" class="divide-y divide-gray-200 dark:divide-gray-800">
        @forelse($returns as $ret)
          <li class="p-4 sm:p-6">
            <div class="flex items-center justify-between">
              <div>
                <div class="flex items-center gap-2">
                  <span class="text-sm px-2 py-0.5 rounded-full bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300">
                    #{{ $ret->id }}
                  </span>
                  <flux:badge color="{{ $this->getStatusBadgeColor($ret->status) }}">
                    {{ ucfirst($ret->status) }}
                  </flux:badge>
                </div>
                <div class="mt-2 text-sm text-gray-600 dark:text-gray-300">
                  {{-- <div class="font-medium text-gray-900 dark:text-white">
                    <a href="{{ route('admin.returns.show', $ret->id) }}"
                      class="hover:underline">{{ $ret->orderItem->product->name }}</a>
                    <span class="text-gray-500">(SKU: {{ $ret->orderItem->product->sku }})</span>
                  </div> --}}
                  <div>Order: <span class="font-mono">{{ $ret->orderItem->order->order_number }}</span></div>
                  <div>Qty: {{ $ret->quantity }} &middot; Reason: {{ $ret->reason ?: '—' }}</div>
                </div>
              </div>
              <div class="flex gap-2">
                @if ($ret->status === 'requested')
                  <flux:button wire:click="approve({{ $ret->id }})" variant="primary" color="green" class="cursor-pointer">
                    Approve
                  </flux:button>
                  <flux:button wire:click="reject({{ $ret->id }})" variant="danger" class="cursor-pointer">
                    Reject
                  </flux:button>
                @endif
                @if ($ret->status === 'approved')
                  <flux:button wire:click="receive({{ $ret->id }})" variant="primary">Receive</flux:button>
                @endif
              </div>
            </div>
            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
              Requested {{ optional($ret->requested_at ?? $ret->created_at)->diffForHumans() }}
              @if ($ret->processed_at)
                &middot; Processed {{ $ret->processed_at->diffForHumans() }} by
                {{ $ret->processedBy?->name ?? 'System' }}
              @endif
            </div>
          </li>
        @empty
          <li class="p-6 text-center text-sm text-gray-500 dark:text-gray-400">No returns found.</li>
        @endforelse
      </ul>

      @if($returns->hasPages())
        <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-800">{{ $returns->links() }}</div>
      @endif
    </div>
  </div>
</div>
