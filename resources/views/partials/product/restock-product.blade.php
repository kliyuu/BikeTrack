<!-- Stock Adjustment Modal -->
<flux:modal name="stock-adjustment-modal" class="md:w-lg">
  <div class="space-y-6">
    <div>
      <flux:heading size="lg">
        Adjust Stock: {{ $adjustingVariant?->variant_name }}
      </flux:heading>
      <p class="text-sm text-gray-600 dark:text-gray-400">
        Current Stock: {{ $adjustingVariant?->cached_stock }} units
      </p>
    </div>

    <form wire:submit="saveStockAdjustment" class="space-y-6">
      <!-- Warehouse Selection -->
      <flux:field>
        <flux:label>Warehouse</flux:label>
        <flux:select wire:model="warehouse_id" placeholder="Select Warehouse">
          @foreach ($warehouses as $warehouse)
            <flux:select.option value="{{ $warehouse->id }}">{{ $warehouse->name }}</flux:select.option>
          @endforeach
        </flux:select>
        <flux:error name="warehouse_id" />
      </flux:field>

      <!-- Quantity Change -->
      <flux:field>
        <flux:label>Quantity Change</flux:label>
        <flux:input type="number" wire:model="quantity_change" placeholder="Use negative numbers to decrease stock" />
        <flux:description>
          Enter positive numbers to add stock, negative to remove stock
        </flux:description>
        <flux:error name="quantity_change" />
      </flux:field>

      <!-- Reason -->
      <flux:field>
        <flux:label>Reason</flux:label>
        <flux:select wire:model="reason" placeholder="Select reason">
          <flux:select.option value="manual_restock">Manual Adjustment</flux:select.option>
          <flux:select.option value="purchase_receipt">Purchase Receipt</flux:select.option>
          <flux:select.option value="return">Return</flux:select.option>
          <flux:select.option value="spoilage">Spoilage/Damage</flux:select.option>
          <flux:select.option value="inventory_count">Inventory Count</flux:select.option>
        </flux:select>
        <flux:error name="reason" />
      </flux:field>

      <!-- Actions -->
      <div class="flex justify-end space-x-3">
        <flux:button type="button" wire:click="closeStockModal" variant="ghost">
          Cancel
        </flux:button>
        <flux:button type="submit" variant="primary">
          Adjust Stock
        </flux:button>
      </div>
    </form>
  </div>
</flux:modal>
