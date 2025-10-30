<flux:navbar class="mr-2">
  <flux:dropdown position="bottom" align="end">
    <flux:button variant="ghost" square class="relative">
      <flux:icon name="bell" class="size-6" variant="solid" />
      @if ($unreadCount > 0)
        <span class="absolute right-1 top-0 rounded-full bg-danger px-1 leading-4 text-xs font-medium text-on-danger">
          {{ $unreadCount }}
        </span>
      @endif
    </flux:button>

    <flux:menu class="w-80">
      <!-- Header with refresh button -->
      <div class="p-3 border-b bg-gray-50 flex justify-between items-center">
        <h3 class="font-semibold text-gray-900">Notifications</h3>
        <flux:button icon="arrow-path" wire:click="checkStockNotifications" variant="ghost" size="sm" class="text-xs">
          Refresh
        </flux:button>
      </div>

      <div class="max-h-64 overflow-y-auto">
        @forelse ($notifications as $notification)
          <button wire:click="markAsReadAndRedirect({{ $notification['id'] }})"
            class="w-full text-left p-3 not-first:border-t hover:bg-gray-50 flex justify-between items-start {{ str_contains($notification['title'], 'Out of Stock') ? 'border-l-4 border-red-500 bg-red-50' : '' }}
            {{ str_contains($notification['title'], 'Low Stock') ? 'border-l-4 border-yellow-500 bg-yellow-50' : '' }}">
            <div class="flex-1">
              <div class="flex items-center gap-2">
                @if(str_contains($notification['title'], 'Out of Stock'))
                  <flux:icon name="exclamation-triangle" class="size-4 text-red-500" />
                @elseif(str_contains($notification['title'], 'Low Stock'))
                  <flux:icon name="exclamation-triangle" class="size-4 text-yellow-500" />
                @endif
                <div class="font-medium">{{ $notification['title'] }}</div>
              </div>
              <div class="text-sm text-gray-600 mt-1">{{ $notification['message'] }}</div>
              <div class="text-xs text-gray-400 mt-1">
                {{ \Carbon\Carbon::parse($notification['created_at'])->diffForHumans() }}
              </div>
            </div>
            @if (!$notification['is_read'])
              <span class="w-2 h-2 bg-blue-500 rounded-full mt-2 flex-shrink-0"></span>
            @endif
          </button>
        @empty
          <div class="p-3 text-sm text-gray-500 text-center">
            <flux:icon name="bell-slash" class="size-8 mx-auto text-gray-400 mb-2" />
            No notifications
          </div>
        @endforelse
      </div>

      <!-- Footer -->
      @if(count($notifications) > 0)
        <div class="p-3 border-t bg-gray-50 text-center">
          <a href="{{ route('admin.notifications') }}"
             class="text-sm text-blue-600 hover:text-blue-800 font-medium">
            View All Notifications
          </a>
        </div>
      @endif
    </flux:menu>
  </flux:dropdown>
</flux:navbar>
{{-- <div class="relative" x-data="{ open: false }">
  <!-- Bell icon -->
  <button @click="open = !open" class="relative">
    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
        d="M15 17h5l-1.405-1.405M19 13V8a7 7 0 10-14 0v5l-1.405 1.405M5 17h14" />
    </svg>

    @if ($unreadCount > 0)
      <span class="absolute -top-1 -right-1 bg-red-600 text-white text-xs rounded-full px-1.5 py-0.5">
        {{ $unreadCount }}
      </span>
    @endif
  </button>

  <!-- Dropdown -->
  <div x-show="open" x-transition @click.away="open = false"
    class="absolute right-0 mt-2 w-80 bg-white border rounded shadow-lg z-50">
    <div class="p-2 font-semibold border-b">Notifications</div>

    <div class="max-h-64 overflow-y-auto">
      @forelse ($notifications as $notification)
        <button wire:click="markAsReadAndRedirect({{ $notification['id'] }})"
          class="w-full text-left p-3 border-b hover:bg-gray-50 flex justify-between items-start">
          <div>
            <div class="font-medium">{{ $notification['title'] }}</div>
            <div class="text-sm text-gray-600">{{ $notification['message'] }}</div>
            <div class="text-xs text-gray-400">
              {{ \Carbon\Carbon::parse($notification['created_at'])->diffForHumans() }}
            </div>
          </div>
          @if (!$notification['is_read'])
            <span class="w-2 h-2 bg-blue-500 rounded-full mt-2"></span>
          @endif
        </button>
      @empty
        <div class="p-3 text-sm text-gray-500">No notifications</div>
      @endforelse
    </div>
  </div>
</div> --}}
