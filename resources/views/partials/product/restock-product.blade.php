<flux:modal name="restock-product" variant="flyout" class="md:w-xl !p-0">
  <div class="relative px-8 pt-8">
    <flux:heading size="lg" class="pb-6 text-lg font-medium text-gray-900 dark:text-white">
      Adjust Stock: {{ $adjustingProduct?->name }}
    </flux:heading>
    <hr class="absolute bottom-0 left-0 w-full border-t-2 border-gray-200 dark:border-gray-700">
  </div>

  <div class="px-8 pb-8">
    <div class="flex gap-2 pt-4">
      <div class="text-sm font-semibold">Last Updated:</div>
      <div class="flex gap-2">
        <div class="text-sm text-gray-500 dark:text-white">
          {{ $adjustingProduct?->updated_at->format('M j, Y') }}
        </div>
        <div class="text-sm text-gray-500 dark:text-gray-400">
          {{ $adjustingProduct?->updated_at->format('g:i A') }}
        </div>
      </div>
    </div>

    <form method="POST" wire:submit="saveStockAdjustment" class="mt-6 space-y-6">
      <div class="grid grid-cols-1 gap-4">
        <flux:select wire:model="warehouse_id" label="Warehouse *" placeholder="Select Warehouse">
          @foreach ($warehouses as $warehouse)
            <flux:select.option value="{{ $warehouse->id }}" class="text-gray-900 dark:text-white">
              {{ $warehouse->name }}
            </flux:select.option>
          @endforeach
        </flux:select>
      </div>

      <div class="grid grid-cols-1 gap-4">
        <flux:input wire:model="quantity_change" label="Quantity Change *"
          placeholder="Use negative numbers to decrease stock" type="number" />
      </div>

      <div class="grid grid-cols-1 gap-4">
        <flux:select wire:model="reason" label="Reason *" placeholder="Select reason for restock">
          <flux:select.option value="manual_restock">Manual Adjustment</flux:select.option>
          <flux:select.option value="purchase_receipt">Purchase Receipt</flux:select.option>
          <flux:select.option value="return">Return</flux:select.option>
          <flux:select.option value="spoilage">Spoilage/Damage</flux:select.option>
          <flux:select.option value="inventory_count">Inventory Count</flux:select.option>
        </flux:select>
      </div>

      <div class="flex">
        <flux:spacer />
        <flux:button type="button" variant="ghost" wire:click="closeModal" class="mr-2 cursor-pointer">
          Cancel
        </flux:button>
        <flux:button type="submit" variant="primary" class="cursor-pointer">Adjust Stock</flux:button>
      </div>
    </form>
  </div>
</flux:modal>
