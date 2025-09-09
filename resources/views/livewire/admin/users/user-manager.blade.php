<div class="space-y-6">
  {{-- @dump($users) --}}

  <x-card class="space-y-4 bg-white border border-gray-100 shadow-xs rounded-md">
    <div class="card-body">
      <div class="flex items-center justify-between mb-6 border-b border-gray-200 pb-4">
        <h2 class="text-base">{{ __('User Management') }}</h2>
        <flux:button size="sm" variant="primary" icon="plus" :loading="false"
          class="!px-4 bg-blue-600 hover:bg-blue-700 dark:text-white">
          {{ __('Add User') }}
        </flux:button>
      </div>

      <div class="flex mb-6">
        <div class="relative flex items-center">
          <flux:icon name="magnifying-glass" class="absolute left-3 text-gray-400" />
          <input type="text" wire:model.live.debounce.300ms="search" id="search"
            class="block w-full py-2 pl-10 pr-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            placeholder="Search users...">
        </div>
      </div>

      <div class="overflow-x-auto">
        <div
          class="overflow-hidden w-full overflow-x-auto rounded-radius border border-outline dark:border-outline-dark">
          <table class="w-full text-left text-sm text-on-surface dark:text-on-surface-dark">
            <thead
              class="border-b border-outline bg-surface-alt text-sm text-on-surface-strong dark:border-outline-dark dark:bg-surface-dark-alt dark:text-on-surface-dark-strong">
              <tr>
                <th scope="col" class="p-4">ID</th>
                <th scope="col" class="p-4">User</th>
                <th scope="col" class="p-4">Role</th>
                <th scope="col" class="p-4">Status</th>
                <th scope="col" class="p-4 text-center">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-outline dark:divide-outline-dark">
              @forelse ($users as $user)
                <tr>
                  <td class="p-4">{{ $user->id }}</td>
                  <td class="p-4">
                    <div class="flex w-max items-center gap-2">
                      <div class="flex flex-col">
                        <span class="text-neutral-900 dark:text-white">{{ $user->name }}</span>
                        <span
                          class="text-sm text-neutral-600 opacity-85 dark:text-neutral-300">{{ $user->email }}</span>
                      </div>
                    </div>
                  </td>
                  <td class="p-4">{{ ucfirst($user->role->name) ?? 'N/A' }}</td>
                  <td class="p-4">
                    <span
                      class="inline-flex overflow-hidden rounded-radius px-1 py-0.5 text-xs font-medium border-success text-success bg-success/10">
                      {{ ucfirst($user->approval_status) }}
                    </span>
                  </td>
                  <td class="p-4 text-center space-x-1">
                    <flux:tooltip content="View">
                      <flux:button size="xs" variant="primary" icon="eye" icon:variant="outline"
                        class="text-blue-600 bg-blue-100 hover:bg-blue-200 border-0 cursor-pointer" />
                    </flux:tooltip>
                    <flux:tooltip content="Edit">
                      <flux:button size="xs" variant="primary" icon="pencil-square" icon:variant="outline"
                        class="text-green-600 bg-green-100 hover:bg-green-200 border-0 cursor-pointer" />
                    </flux:tooltip>
                    <flux:tooltip content="Delete">
                      <flux:button size="xs" variant="primary" icon="trash" icon:variant="outline"
                        class="text-red-600 bg-red-100 hover:bg-red-200 border-0 cursor-pointer" />
                    </flux:tooltip>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center p-3">No users found.</td>
                </tr>
              @endforelse
            </tbody>
          </table>

          @if ($users->hasPages())
            <div class="mt-6">
              {{ $users->links() }}
            </div>
          @endif
        </div>
      </div>
    </div>
  </x-card>

  <x-toast soundEffect="true" displayDuration="3000" />
</div>
