<div class="max-w-7xl">
  <div class="mb-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Warehouse Management</h1>
      </div>
    </div>
  </div>

  <x-card class="bg-white border border-gray-100 shadow-sm rounded-md">
    <div class="card-body">
      <div class="flex items-center justify-between mb-6 border-b border-gray-200 pb-4">
        <h2 class="text-base">{{ __('Warehouses') }}</h2>
        <flux:button size="sm" variant="primary" icon="plus" wire:click="openWarehouseModal"
          :loading="false" class="!px-4 bg-blue-600 hover:bg-blue-700 dark:text-white">
          {{ __('Add Warehouse') }}
        </flux:button>
      </div>

      <div class="overflow-x-auto">
        <div class="pb-6">
          <!-- Search -->
          <div class="flex flex-col gap-2 lg:col-span-2 md:w-1/2">
            <flux:label>Search Warehouse</flux:label>
            <input type="text" wire:model.live.debounce.100ms="search" id="search"
              class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              placeholder="Search by warehouse name or location ...">
          </div>
        </div>

        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
              <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Warehouse
              </th>
              <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Location
              </th>
              <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Contact
              </th>
              <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Inventory Items
              </th>
              <th scope="col"
                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>

          <tbody class="bg-white divide-y divide-gray-200 dark:divide-gray-700">
            @forelse ($warehouses as $warehouse)
              <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $warehouse->name }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $warehouse->location }}</div>
                </td>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  @if ($warehouse->contact_person)
                    <div class="text-sm text-gray-900 dark:text-white">{{ $warehouse->contact_person }}</div>
                  @endif
                  @if ($warehouse->contact_phone)
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $warehouse->contact_phone }}</div>
                  @endif
                  @if ($warehouse->contact_email)
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $warehouse->contact_email }}</div>
                  @endif
                  @if (!$warehouse->contact_person && !$warehouse->contact_phone && !$warehouse->contact_email)
                    <div class="text-sm text-gray-400 dark:text-gray-500">No contact info</div>
                  @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    class="inline-flex px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-200">
                    {{ $warehouse->inventory_levels_count }}
                    {{ Str::plural('item', $warehouse->inventory_levels_count) }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <flux:tooltip content="Edit">
                    <flux:button wire:click="openWarehouseModal({{ $warehouse->id }})" size="xs" variant="primary"
                      icon="pencil-square" icon:variant="outline"
                      class="text-blue-600 bg-blue-100 hover:bg-blue-200 border-0 cursor-pointer" />
                  </flux:tooltip>
                  @if ($warehouses->total() > 1)
                    <flux:tooltip content="Delete">
                      <flux:button wire:click="confirmDelete({{ $warehouse->id }})" size="xs" variant="primary"
                        icon="trash" icon:variant="outline"
                        class="text-red-600 bg-red-100 hover:bg-red-200 border-0 cursor-pointer" />
                    </flux:tooltip>
                  @endif
                </td>
              </tr>
            @empty
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </x-card>

  {{-- Toast Component --}}
  <x-toast />

  {{-- Warehouse Modal --}}
  <flux:modal name="warehouse-modal" class="md:w-1/2">
    <div class="space-y-6">
      <div>
        <flux:heading size="lg">{{ $warehouseId ? 'Edit Warehouse' : 'Add New Warehouse' }}</flux:heading>
      </div>

      <form method="POST" wire:submit="saveWarehouse" class="mt-6 space-y-6">
        <flux:input wire:model="name" label="Warehouse Name" placeholder="Enter warehouse name" />
        <flux:input wire:model="location" label="Location" placeholder="Enter location" />
        <flux:textarea wire:model="description" label="Description" placeholder="Enter description" />

        <div class="flex flex-col gap-4">
          <div class="text-lg">Contact Information</div>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <flux:input wire:model="contact_person" label="Contact Person" placeholder="Enter contact person" />
            <flux:input wire:model="contact_number" label="Contact Phone" placeholder="Enter contact phone" />
          </div>
          <div class="grid grid-cols-1">
            <flux:input wire:model="contact_email" label="Contact Email" placeholder="Enter contact email" />
          </div>
        </div>

        <div class="flex">
          <flux:spacer />
          <flux:button wire:click="closeModal" class="bg-gray-200 hover:bg-gray-300 cursor-pointer mr-2">Cancel
          </flux:button>
          <flux:button type="submit" variant="primary" class="bg-blue-500 hover:bg-blue-600 cursor-pointer">
            {{ $warehouseId ? 'Update' : 'Save' }}
          </flux:button>
        </div>
      </form>
    </div>
  </flux:modal>

  {{-- Delete Warehouse Modal --}}
  <flux:modal name="delete-warehouse" class="min-w-[22rem]">
    <div class="space-y-6">
      <div>
        <flux:heading size="lg">Delete warehouse?</flux:heading>
        <flux:text class="mt-2">
          <p>You're about to delete this warehouse.</p>
          <p>This action cannot be reversed.</p>
        </flux:text>
      </div>
      <div class="flex gap-2">
        <flux:spacer />
        <flux:modal.close>
          <flux:button variant="ghost" class="cursor-pointer">Cancel</flux:button>
        </flux:modal.close>
        <flux:button wire:click="deleteWarehouse" variant="danger" class="cursor-pointer">Delete</flux:button>
      </div>
    </div>
  </flux:modal>
</div>
