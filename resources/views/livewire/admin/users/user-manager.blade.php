<div class="space-y-6">
  {{-- @dump($users) --}}

  <x-card class="space-y-4 bg-white border border-gray-100 shadow-xs rounded-md">
    <div class="card-body">
      <div class="flex items-center justify-between mb-6 border-b border-gray-200 pb-4">
        <h2 class="text-base">{{ __('User Management') }}</h2>
        <flux:button wire:click="openUserModal" size="sm" variant="primary" color="blue" icon="plus"
          :loading="false" class="cursor-pointer">
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
                    <flux:badge size="sm" color="{{ $this->getStatusBadgeColor($user->approval_status) }}">
                      {{ ucfirst($user->approval_status) }}
                    </flux:badge>
                  </td>
                  <td class="p-4 text-center space-x-1">
                    {{-- <flux:tooltip content="View">
                      <flux:button size="xs" variant="primary" icon="eye" icon:variant="outline"
                        class="text-blue-600 bg-blue-100 hover:bg-blue-200 border-0 cursor-pointer" />
                    </flux:tooltip> --}}
                    <flux:tooltip content="Edit">
                      <flux:button wire:click="openUserModal({{ $user->id }})" size="xs" variant="primary"
                        icon="pencil-square" icon:variant="outline"
                        class="text-green-600 bg-green-100 hover:bg-green-200 border-0 cursor-pointer" />
                    </flux:tooltip>
                    <flux:tooltip content="Delete">
                      <flux:button wire:click="confirmDelete({{ $user->id }})" size="xs" variant="primary"
                        icon="trash" icon:variant="outline"
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

  {{-- Toast Notifications --}}
  <x-toast soundEffect="true" displayDuration="3000" />

  <flux:modal name="user-modal" class="md:w-1/2">
    <div class="space-y-6">
      <div>
        <flux:heading size="lg">{{ $userId ? 'Update User' : 'Add New User' }}</flux:heading>
        <flux:text class="mt-2">Make changes to your personal details.</flux:text>
      </div>

      <form method="POST" wire:submit.prevent="saveUser">
        <div class="space-y-4">
          <flux:input wire:model="name" label="Name" placeholder="Enter name" />
          <flux:input wire:model="email" label="Email" type="email" placeholder="Enter email" />
          <flux:select wire:model="role_id" label="Role">
            <flux:select.option value="" selected disabled>Select Role</flux:select.option>
            <flux:select.option value="1">Admin</flux:select.option>
            <flux:select.option value="2">Staff</flux:select.option>
          </flux:select>
          <flux:input wire:model="password" label="Password" type="password" placeholder="Enter password" viewable/>
          <flux:input wire:model="password_confirmation" label="Confirm Password" type="password"
            placeholder="Confirm password" viewable/>

          @if ($userId)
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <flux:radio.group wire:model="approval_status" label="Status" variant="segmented">
                <flux:radio value="active" label="Active" class="cursor-pointer" />
                <flux:radio value="inactive" label="Inactive" class="cursor-pointer" />
              </flux:radio.group>
            </div>
          @endif

          <div class="flex">
            <flux:spacer />
            <flux:button type="button" variant="ghost" wire:click="closeModal" class="mr-2 cursor-pointer">
              Cancel
            </flux:button>
            <flux:button type="submit" variant="primary" color="blue" class="cursor-pointer">
              {{ $userId ? 'Update User' : 'Save User' }}
            </flux:button>
          </div>
        </div>
      </form>
    </div>
  </flux:modal>

  {{-- Delete User Modal --}}
  <flux:modal name="delete-user" class="min-w-[22rem]">
    <div class="space-y-6">
      <div>
        <flux:heading size="lg">Delete User</flux:heading>
        <flux:text class="mt-2">
          Are you sure you want to delete the user named <span class="font-semibold">{{ $user->name }}</span>?
        </flux:text>
      </div>

      <div class="flex">
        <flux:spacer />
        <flux:button type="button" variant="ghost" wire:click="closeModal" class="mr-2 cursor-pointer">
          Cancel
        </flux:button>
        <flux:button type="button" variant="danger" wire:click="deleteUser" class="cursor-pointer">
          Delete User
        </flux:button>
      </div>
    </div>
  </flux:modal>
</div>
