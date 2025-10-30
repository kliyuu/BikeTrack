<div class="space-y-6">
  <div class="mb-4">
    <flux:breadcrumbs>
      <flux:breadcrumbs.item href="{{ route('admin.products') }}">Products</flux:breadcrumbs.item>
      <flux:breadcrumbs.item>{{ $product->name }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>
  </div>

  <!-- Header -->
  <div class="flex items-center justify-between">
    <div>
      <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
        Product Variants for: {{ $product->name }}
      </h3>
      <p class="text-sm text-gray-600 dark:text-gray-400">
        Base SKU: {{ $product->sku }} | Base Price: ₱{{ number_format($product->unit_price, 2) }}
      </p>
    </div>
    <flux:button icon="plus" wire:click="openVariantModal()" variant="primary">
      Add Variant
    </flux:button>
  </div>

  <!-- Variants List -->
  <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
    @if ($variants->count() > 0)
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Variant Details
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                SKU
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Price
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Stock
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                By Warehouse
              </th>
              <th
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Status
              </th>
              <th
                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>
          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @foreach ($variants as $variant)
              <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                  <div>
                    <div class="text-sm font-medium text-gray-900 dark:text-white">
                      {{ $variant->variant_name }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                      {{ $variant->getDisplayName() }}
                    </div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                  {{ $variant->variant_sku }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                  ₱{{ number_format($variant->getFinalPrice(), 2) }}
                  @if ($variant->price_adjustment != 0)
                    <span class="text-xs text-gray-500 dark:text-gray-400">
                      ({{ $variant->price_adjustment > 0 ? '+' : '' }}₱{{ number_format($variant->price_adjustment, 2) }})
                    </span>
                  @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  @php
                    $stockClass = match (true) {
                        $variant->cached_stock <= 0 => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-200',
                        $variant->cached_stock <= $variant->low_stock_threshold
                            => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-200',
                        default => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-200',
                    };
                  @endphp
                  <span class="inline-flex px-2 py-1 text-xs rounded-full {{ $stockClass }}">
                    {{ $variant->cached_stock }} units
                  </span>
                  <div class="text-xs text-gray-500 mt-1">
                    Threshold: {{ $variant->low_stock_threshold }}
                  </div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-xs text-gray-500 dark:text-gray-400 space-y-1">
                    @forelse ($variant->inventoryLevels as $level)
                      <div class="flex justify-between items-center">
                        <span class="truncate">{{ $level->warehouse->name }}:</span>
                        <span class="font-medium ml-2">{{ $level->quantity - $level->reserved_quantity }}</span>
                      </div>
                      @if ($level->reserved_quantity > 0)
                        <div class="text-xs text-amber-600 dark:text-amber-400 pl-2">
                          Reserved: {{ $level->reserved_quantity }}
                        </div>
                      @endif
                    @empty
                      <div class="text-gray-400 italic">No warehouse inventory</div>
                    @endforelse
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <flux:badge size="sm" color="{{ $variant->is_active ? 'green' : 'red' }}">
                    {{ $variant->is_active ? 'Active' : 'Inactive' }}
                  </flux:badge>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                  <flux:dropdown>
                    <flux:button size="xs" variant="primary" icon="ellipsis-vertical" icon:variant="outline"
                      class="text-gray-600 bg-gray-100 hover:bg-gray-200 border-0 cursor-pointer" />

                    <flux:navmenu>
                      <flux:navmenu.item icon="plus-circle" wire:click="openStockModal({{ $variant->id }})" class="cursor-pointer">
                        Stock
                      </flux:navmenu.item>
                      <flux:navmenu.item icon="pencil-square" wire:click="openVariantModal({{ $variant->id }})" class="cursor-pointer">
                        Edit
                      </flux:navmenu.item>
                      <flux:navmenu.item variant="danger" icon="trash" wire:click="deleteVariant({{ $variant->id }})" class="cursor-pointer text-red-600">
                        Delete
                      </flux:navmenu.item>
                    </flux:navmenu>
                  </flux:dropdown>
                  {{-- <flux:button icon="plus-circle" wire:click="openStockModal({{ $variant->id }})" variant="ghost"
                    size="sm" title="Adjust Stock">
                    Stock
                  </flux:button>
                  <flux:button icon="pencil-square" wire:click="openVariantModal({{ $variant->id }})" variant="ghost"
                    size="sm">
                    Edit
                  </flux:button>
                  <flux:button icon="trash" wire:click="deleteVariant({{ $variant->id }})" variant="danger"
                    size="sm">
                    Delete
                  </flux:button> --}}
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    @else
      <div class="text-center py-12">
        <flux:icon.cube class="mx-auto h-12 w-12 text-gray-400" />
        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No variants</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
          Get started by creating a product variant.
        </p>
        <div class="mt-6">
          <flux:button wire:click="openVariantModal()" variant="primary">
            <flux:icon.plus class="size-4" />
            Add Variant
          </flux:button>
        </div>
      </div>
    @endif
  </div>

  <!-- Variant Modal -->
  @include('partials.product.product-variant-modal')

  <!-- Stock Adjustment Modal -->
  @include('partials.product.restock-product')
</div>
