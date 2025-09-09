<div class="max-w-7xl">
  <div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-semibold">{{ __('Client Management') }}</h2>
    <flux:button variant="primary" icon="plus" wire:click="$dispatch('openClientModal')"
      class="!px-4 bg-blue-600 hover:bg-blue-700">
      {{ __('Add Client') }}
    </flux:button>
  </div>

  @if (session()->has('message'))
    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 2000)" x-transition.opacity.duration.500ms
      class="bg-green-100 text-green-800 p-2 mb-3 rounded">
      {{ session('message') }}
    </div>
  @endif

  <x-card class="space-y-4 bg-white border border-gray-100 shadow-sm rounded-lg">
    <div class="card-body">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-700">
            <tr class="*:font-medium *:text-gray-900">
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                <button wire:click="sortBy('contact_name')"
                  class="flex items-center hover:text-gray-700 dark:hover:text-gray-200 cursor-pointer">
                  NAME
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
                Email
              </th>
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
                Status
              </th>
              <th
                class="px-6 py-3 text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider text-center">
                Actions
              </th>
            </tr>
          </thead>

          <tbody class="divide-y divide-gray-200">
            @forelse($clients as $client)
              <tr class="*:text-gray-900 *:first:font-medium">
                <td class="px-6 py-4 whitespace-nowrap">{{ $client->contact_name }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $client->contact_email }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ $client->company_name ?? '-' }}</td>
                <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst($client->status) }}</td>
                <td class="px-6 py-4 whitespace-nowrap space-x-2 text-center">
                  @if ($client->status === 'pending')
                    <button wire:click="approve({{ $client->id }})"
                      class="text-green-500 rounded hover:text-green-900 cursor-pointer">Approve
                    </button>
                    <button wire:click="reject({{ $client->id }})"
                      class="text-red-500 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 rounded cursor-pointer">Reject</button>
                  @else
                    <button wire:click="$dispatch('openClientModal', {id: {{ $client->id }}})"
                      class="text-blue-500 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 rounded cursor-pointer">Edit</button>
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
  </x-card>

  <div class="mt-4">
    {{ $clients->links() }}
  </div>

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
</div>
