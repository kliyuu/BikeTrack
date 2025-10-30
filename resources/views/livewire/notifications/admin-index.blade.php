<div class="max-w-7xl space-y-6">
  <div class="flex items-center justify-between">
    <div>
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Admin Notifications</h1>
      <p class="mt-2 text-gray-600 dark:text-gray-400">Manage and view all system notifications</p>
    </div>
  </div>

  <!-- Stats Summary -->
  <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
    <div class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 rounded-lg p-6">
      <div class="flex items-center gap-4">
        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 dark:bg-blue-900">
          <flux:icon name="bell" class="text-blue-600 dark:text-blue-400" />
        </div>
        <div>
          <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Notifications</p>
          <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $notifications->total() }}</p>
        </div>
      </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 rounded-lg p-6">
      <div class="flex items-center gap-4">
        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-green-100 dark:bg-green-900">
          <flux:icon name="check-circle" class="text-green-600 dark:text-green-400" />
        </div>
        <div>
          <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Read</p>
          <p class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ \App\Models\Notification::where('client_id', null)->where('is_read', true)->count() }}
          </p>
        </div>
      </div>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 rounded-lg p-6">
      <div class="flex items-center gap-4">
        <div class="flex items-center justify-center w-12 h-12 rounded-full bg-orange-100 dark:bg-orange-900">
          <flux:icon name="bell-alert" class="text-orange-600 dark:text-orange-400" />
        </div>
        <div>
          <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Unread</p>
          <p class="text-2xl font-bold text-gray-900 dark:text-white">
            {{ \App\Models\Notification::where('client_id', null)->where('is_read', false)->count() }}
          </p>
        </div>
      </div>
    </div>
  </div>

  <!-- Notifications List -->
  <div class="bg-white dark:bg-gray-800 shadow-sm ring-1 ring-gray-900/5 rounded-xl">
    <div class="flex p-4 border-b border-gray-200 dark:border-gray-700 items-center justify-between">
      <div class="flex gap-2">
        <button wire:click="setFilter('all')"
          class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $filter === 'all' ? 'bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
          All Notifications
        </button>
        <button wire:click="setFilter('unread')"
          class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $filter === 'unread' ? 'bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
          Unread
        </button>
        <button wire:click="setFilter('read')"
          class="px-4 py-2 text-sm font-medium rounded-md transition-colors {{ $filter === 'read' ? 'bg-blue-600 text-white' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
          Read
        </button>
      </div>

      <div class="flex items-center gap-3">
        <flux:button wire:click="markAllAsRead" variant="ghost" size="sm" icon="check-circle">
          Mark All Read
        </flux:button>
        <flux:button wire:click="deleteAllRead" variant="danger" size="sm" icon="trash">
          Delete All Read
        </flux:button>
      </div>
    </div>

    <div class="divide-y divide-gray-200 dark:divide-gray-700">
      @forelse ($notifications as $notification)
        <div
          class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors {{ !$notification->is_read ? 'bg-blue-50/50 dark:bg-blue-900/10' : '' }}">
          <div class="flex items-start justify-between gap-4">
            <!-- Notification Content -->
            <div class="flex-1 min-w-0">
              <div class="flex items-start gap-4">
                <!-- Icon -->
                <div class="flex-shrink-0">
                  <div
                    class="flex items-center justify-center w-10 h-10 rounded-full {{ !$notification->is_read ? 'bg-blue-100 dark:bg-blue-900' : 'bg-gray-100 dark:bg-gray-700' }}">
                    @if (str_contains(strtolower($notification->title), 'stock'))
                      <flux:icon name="cube"
                        class="text-xl {{ !$notification->is_read ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400' }}" />
                    @elseif(str_contains(strtolower($notification->title), 'order'))
                      <flux:icon name="shopping-bag"
                        class="text-xl {{ !$notification->is_read ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400' }}" />
                    @elseif(str_contains(strtolower($notification->title), 'user'))
                      <flux:icon name="user-circle"
                        class="text-xl {{ !$notification->is_read ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400' }}" />
                    @elseif(str_contains(strtolower($notification->title), 'payment'))
                      <flux:icon name="credit-card"
                        class="text-xl {{ !$notification->is_read ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400' }}" />
                    @elseif(str_contains(strtolower($notification->title), 'return'))
                      <flux:icon name="arrow-uturn-left"
                        class="text-xl {{ !$notification->is_read ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400' }}" />
                    @else
                      <flux:icon name="bell"
                        class="text-xl {{ !$notification->is_read ? 'text-blue-600 dark:text-blue-400' : 'text-gray-600 dark:text-gray-400' }}" />
                    @endif
                  </div>
                </div>

                <!-- Content -->
                <div class="flex-1 min-w-0">
                  <div class="flex items-start gap-2">
                    <h3
                      class="text-sm font-semibold {{ !$notification->is_read ? 'text-gray-900 dark:text-white' : 'text-gray-700 dark:text-gray-300' }}">
                      {{ $notification->title }}
                    </h3>
                    @if (!$notification->is_read)
                      <flux:badge size="sm" color="green">New</flux:badge>
                    @endif
                  </div>

                  <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    {{ $notification->message }}
                  </p>

                  <div class="mt-2 flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                    <span class="flex items-center gap-1">
                      <i class="ti ti-clock text-sm"></i>
                      {{ $notification->created_at->diffForHumans() }}
                    </span>
                    <span>{{ $notification->created_at->format('M j, Y g:i A') }}</span>
                  </div>

                  @if ($notification->url)
                    <div class="mt-3">
                      <flux:button wire:click="navigateToUrl({{ $notification->id }}, '{{ $notification->url }}')"
                        variant="ghost" size="sm" class="!text-blue-600 !dark:text-blue-400 cursor-pointer">
                        View Details →
                      </flux:button>
                    </div>
                  @endif
                </div>
              </div>
            </div>

            <!-- Actions -->
            <div class="flex items-start gap-2">
              @if (!$notification->is_read)
                <flux:tooltip content="Mark as Read">
                  <flux:button icon="check" wire:click="markAsRead({{ $notification->id }})" variant="ghost"
                    size="sm" title="Mark as read">
                  </flux:button>
                </flux:tooltip>
              @endif

              <flux:tooltip content="Delete Notification">
                <flux:button icon="trash" square wire:click="deleteNotification({{ $notification->id }})"
                  variant="ghost" size="sm" title="Delete notification" class="!text-red-600 !hover:text-red-700">
                </flux:button>
              </flux:tooltip>
            </div>
          </div>
        </div>
      @empty
        <div class="p-12 text-center">
          <div class="flex justify-center mb-4">
            <div class="flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700">
              <i class="ti ti-bell-off text-3xl text-gray-400 dark:text-gray-500"></i>
            </div>
          </div>
          <h3 class="text-lg font-medium text-gray-900 dark:text-white">No notifications found</h3>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            @if ($filter === 'unread')
              You don't have any unread notifications.
            @elseif($filter === 'read')
              You don't have any read notifications.
            @else
              You don't have any notifications yet.
            @endif
          </p>
        </div>
      @endforelse
    </div>

    <!-- Pagination -->
    @if ($notifications->hasPages())
      <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
        {{ $notifications->links() }}
      </div>
    @endif
  </div>

  {{-- Toast --}}
  <x-toast />
</div>
