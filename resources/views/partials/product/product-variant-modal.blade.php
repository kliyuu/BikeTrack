<!-- Variant Modal -->
<flux:modal name="variant-modal" variant="flyout" class="md:w-xl">
  <div class="space-y-6">
    <div>
      <flux:heading size="lg">
        {{ $variantId ? 'Edit Variant' : 'Add New Variant' }}
      </flux:heading>
      <flux:subheading>
        Configure the variant details for {{ $product->name }}
      </flux:subheading>
    </div>

    <div class="flex flex-col gap-4">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Variant Type -->
        <flux:field>
          <flux:label>Variant Type</flux:label>
          <flux:select wire:model="variant_type">
            <flux:select.option value="size_color">Size & Color</flux:select.option>
            <flux:select.option value="model">Model</flux:select.option>
            <flux:select.option value="specification">Specification</flux:select.option>
            <flux:select.option value="custom">Custom</flux:select.option>
          </flux:select>
          <flux:error name="variant_type" />
        </flux:field>

        <!-- Variant Name -->
        <flux:field>
          <flux:label>Variant Name</flux:label>
          <flux:input wire:model="variant_name" placeholder="e.g., Large Red, Model X, etc." />
          <flux:error name="variant_name" />
        </flux:field>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Size -->
        <flux:field>
          <flux:label>Size</flux:label>
          <flux:input wire:model="size" placeholder="e.g., S, M, L, XL, 26&quot;, etc." />
          <flux:error name="size" />
        </flux:field>

        <!-- Color -->
        <flux:field>
          <flux:label>Color</flux:label>
          <flux:input wire:model="color" placeholder="e.g., Red, Blue, Black, etc." />
          <flux:error name="color" />
        </flux:field>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Model -->
        <flux:field>
          <flux:label>Model</flux:label>
          <flux:input wire:model="model" placeholder="e.g., Model X, Version 2.0, etc." />
          <flux:error name="model" />
        </flux:field>

        <!-- Variant SKU -->
        <flux:field>
          <flux:label>Variant SKU</flux:label>
          <flux:input wire:model="variant_sku" placeholder="Unique SKU for this variant" />
          <flux:error name="variant_sku" />
        </flux:field>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Price Adjustment -->
        <flux:field>
          <flux:label>Price Adjustment</flux:label>
          <flux:input type="number" step="0.01" wire:model="price_adjustment" placeholder="0.00" />
          <flux:description class="!text-xs !mt-0">
            *Enter positive value to increase price, negative to decrease
          </flux:description>
          <flux:error name="price_adjustment" />
        </flux:field>

        <!-- Cached Stock -->
        <flux:field>
          <flux:label>Current Stock</flux:label>
          <flux:input type="number" wire:model="cached_stock" placeholder="0" variant="filled" readonly />
          <flux:description class="!text-xs !mt-0">
            *Current available stock across all warehouses
          </flux:description>
          <flux:error name="cached_stock" />
        </flux:field>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Low Stock Threshold -->
        <flux:field>
          <flux:label>Low Stock Threshold</flux:label>
          <flux:input type="number" wire:model="low_stock_threshold" placeholder="5" />
          <flux:error name="low_stock_threshold" />
          <flux:description class="!text-xs !mt-0">*Alert when stock falls below this level</flux:description>
        </flux:field>
      </div>

      <!-- Specifications -->
      <flux:field>
        <flux:label>Specifications</flux:label>
        <flux:textarea wire:model="specifications" placeholder="Additional specifications for this variant..."
          rows="3" />
        <flux:error name="specifications" />
      </flux:field>

      <!-- Is Active -->
      <flux:field variant="inline">
        <flux:checkbox wire:model="is_active" />
        <flux:label>Active (variant will be available for purchase)</flux:label>

        <flux:error name="is_active" />
      </flux:field>

      <!-- Actions -->
      <div class="flex justify-end space-x-3">
        <flux:button wire:click="closeVariantModal" variant="ghost">
          Cancel
        </flux:button>
        <flux:button wire:click="saveVariant" variant="primary">
          {{ $variantId ? 'Update Variant' : 'Create Variant' }}
        </flux:button>
      </div>
    </div>
</flux:modal>
