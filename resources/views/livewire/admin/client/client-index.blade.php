<div class="max-w-7xl">
  <div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-semibold">Client Management</h2>
    {{-- <flux:button variant="primary" icon="plus" wire:click="$dispatch('openClientModal')"
      class="!px-4 bg-blue-600 hover:bg-blue-700">
      {{ __('Add Client') }}
    </flux:button> --}}
  </div>

  @if (session()->has('message'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 2000)" x-transition.opacity.duration.500ms
      class="bg-green-100 text-green-800 p-2 mb-3 rounded">
      {{ session('message') }}
    </div>
  @endif

  <x-card class="space-y-4 bg-white border border-gray-100 shadow-sm rounded-lg">
    <div class="card-body">
      <div class="flex mb-6">
        <div class="relative flex items-center">
          <flux:icon name="magnifying-glass" class="absolute left-3 text-gray-400" />
          <input type="text" wire:model.live.debounce.300ms="search" id="search"
            class="block w-full py-2 pl-10 pr-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            placeholder="Search users...">
        </div>
      </div>

      <div class="overflow-hidden w-full overflow-x-auto rounded-radius border border-outline dark:border-outline-dark">
        <table class="w-full text-left text-sm text-on-surface dark:text-on-surface-dark">
          <thead
            class="border-b border-outline bg-surface-alt text-sm text-on-surface-strong dark:border-outline-dark dark:bg-surface-dark-alt dark:text-on-surface-dark-strong">
            <tr>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                <button wire:click="sortBy('company_name')"
                  class="flex items-center hover:text-gray-700 dark:hover:text-gray-200 cursor-pointer">
                  COMPANY
                  @if ($sortField === 'company_name')
                    @if ($sortDirection === 'asc')
                      <flux:icon name="chevron-up" class="ml-1 w-3 h-3" />
                    @else
                      <flux:icon name="chevron-down" class="ml-1 w-3 h-3" />
                    @endif
                  @endif
                </button>
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                <button wire:click="sortBy('contact_name')"
                  class="flex items-center hover:text-gray-700 dark:hover:text-gray-200 cursor-pointer">
                  CONTACT NAME
                  @if ($sortField === 'contact_name')
                    @if ($sortDirection === 'asc')
                      <flux:icon name="chevron-up" class="ml-1 w-3 h-3" />
                    @else
                      <flux:icon name="chevron-down" class="ml-1 w-3 h-3" />
                    @endif
                  @endif
                </button>
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                CONTACT EMAIL
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                CONTACT NUMBER
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                TAX ID
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                STATUS
              </th>
              <th
                class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider text-center">
                ACTIONS
              </th>
            </tr>
          </thead>

          <tbody class="divide-y divide-outline dark:divide-outline-dark">
            @forelse($clients as $client)
              <tr class="*:text-gray-900 *:first:font-medium">
                <td class="px-6 py-4 whitespace-nowrap dark:text-white">
                  {{ $client->company_name ?? '-' }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap dark:text-white">
                  <div class="flex flex-col">
                    {{ $client->user->name }}
                    <div class="text-xs text-gray-500 dark:text-gray-400">
                      Username: {{ $client->user->email }}
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap dark:text-white">{{ $client->user->email }}</td>
                <td class="px-6 py-4 whitespace-nowrap dark:text-white">{{ $client->contact_phone }}</td>
                <td class="px-6 py-4 whitespace-nowrap dark:text-white">{{ $client->tax_number }}</td>
                <td class="px-6 py-4 whitespace-nowrap dark:text-white">
                  {{ ucfirst($client->status) }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap space-x-2 text-center">
                  @if ($client->status === 'pending')
                    <button wire:click="approve({{ $client->id }})"
                      class="text-green-500 rounded hover:text-green-900 cursor-pointer">Approve
                    </button>
                    <button wire:click="reject({{ $client->id }})"
                      class="text-red-500 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 rounded cursor-pointer">Reject</button>
                  @else
                    {{-- <button wire:click="$dispatch('openClientModal', {id: {{ $client->id }}})"
                      class="text-blue-500 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 rounded cursor-pointer">
                      Edit
                    </button> --}}
                    <button wire:click="delete({{ $client->id }})"
                      class="text-red-500 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 rounded cursor-pointer">Delete</button>
                  @endif
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="5" class="text-center p-3">No clients found.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>

    <div class="mt-4">
      {{ $clients->links() }}
    </div>
  </x-card>

  <div class="mt-4">
    <livewire:admin.client.client-form />
  </div>

  <flux:modal name="delete-client" class="min-w-[22rem]">
    <div class="space-y-6">
      <div>
        <flux:heading size="lg">Delete client?</flux:heading>
        <flux:text class="mt-2">
          <p>You're about to delete this client.</p>
          <p>This action cannot be reversed.</p>
        </flux:text>
      </div>
      <div class="flex gap-2">
        <flux:spacer />
        <flux:modal.close>
          <flux:button variant="ghost">Cancel</flux:button>
        </flux:modal.close>
        <flux:button type="submit" variant="danger">Delete</flux:button>
      </div>
    </div>
  </flux:modal>

  {{-- <x-toast /> --}}
</div>
