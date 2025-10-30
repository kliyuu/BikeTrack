<flux:navbar>
  <flux:dropdown position="bottom" align="end">
    <flux:button variant="ghost" square class="relative">
      <flux:icon name="bell" class="size-6" variant="solid" />
      @if ($unreadCount > 0)
        <span class="absolute right-1 top-0 rounded-full bg-danger px-1 leading-4 text-xs font-medium text-on-danger">
          {{ $unreadCount }}
        </span>
      @endif
    </flux:button>

    <flux:menu>
      <div class="max-h-64 overflow-y-auto">
        @forelse ($notifications as $notification)
          <button wire:click="markAsReadAndRedirect({{ $notification['id'] }})"
            class="w-full text-left p-3 not-first:border-t hover:bg-gray-50 flex justify-between items-start">
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
    </flux:menu>
  </flux:dropdown>
</flux:navbar>
