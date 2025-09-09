<flux:modal name="product-modal" variant="flyout" class="md:w-xl !p-0">
  <div class="relative px-8 pt-6">
    <flux:heading size="lg" class="pb-6">
      {{ $productId ? 'Edit Product' : 'Add New Product' }}
    </flux:heading>
    <hr class="absolute bottom-0 left-0 w-full border-t-2 border-gray-200 dark:border-gray-700">
  </div>

  <div class="space-y-6 px-8 pb-8">
    <form method="POST" wire:submit="saveProduct" class="mt-6 space-y-6">
      <div class="grid grid-cols-2 gap-4">
        <flux:input wire:model="name" label="Name *" placeholder="Product name" />

        <div class="flex flex-col gap-2">
          <flux:label>SKU</flux:label>
          <flux:input.group>
            <flux:input wire:model="sku" placeholder="Product SKU" />
            <flux:button wire:click="generateSku" class="cursor-pointer hover:text-orange-600">
              <i class="ti ti-arrows-shuffle text-lg"></i>
            </flux:button>
          </flux:input.group>
        </div>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <flux:select wire:model="brand_id" label="Brand *" placeholder="Select brand">
          @foreach ($brands as $brand)
            <flux:select.option value="{{ $brand->id }}" wire:key="{{ $brand->id }}" class="text-gray-800 dark:text-gray-200">
              {{ $brand->name }}
            </flux:select.option>
          @endforeach
        </flux:select>
        <flux:select wire:model="category_id" label="Category *" placeholder="Select category">
          @foreach ($categories as $category)
            <flux:select.option value="{{ $category->id }}" wire:key="{{ $category->id }}" class="text-gray-800 dark:text-gray-200">
              {{ $category->name }}
            </flux:select.option>
          @endforeach
        </flux:select>
      </div>
      <div class="grid grid-cols-2 gap-4">
        <flux:input wire:model="unit_price" label="Price *" placeholder="Product price" type="number" />
        <flux:input wire:model="cached_stock" label="Available Stock" placeholder="Product stock" variant="filled" readonly />
      </div>

      <div class="grid grid-cols-2 gap-4">
        <flux:input wire:model="low_stock_threshold" label="Low Stock Threshold"
          placeholder="Low stock threshold (optional)" type="number" />
        <flux:radio.group wire:model="is_active" label="Status" variant="segmented">
          <flux:radio value="true" label="Active" class="cursor-pointer" />
          <flux:radio value="false" label="Inactive" class="cursor-pointer" />
        </flux:radio.group>
      </div>

      <div class="grid grid-cols-1 gap-4">
        <flux:textarea wire:model="description" label="Description" placeholder="Product description" rows="4" />
      </div>

      @if (!$productId)
        <div class="grid grid-cols-1 gap-4">
          <flux:input wire:model="primaryImage" label="Primary Image *" placeholder="Product image" type="file" />
        </div>
      @endif

      <div class="flex">
        <flux:spacer />
        <flux:button type="button" variant="ghost" wire:click="closeModal" class="mr-2 cursor-pointer">Close
        </flux:button>
        <flux:button type="submit" variant="primary" class="cursor-pointer">Save changes</flux:button>
      </div>
    </form>
  </div>
</flux:modal>
