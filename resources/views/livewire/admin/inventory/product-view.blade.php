<div class="max-w-7xl">
  <div class="mb-4">
    <flux:breadcrumbs>
      <flux:breadcrumbs.item href="{{ route('admin.products') }}">Products</flux:breadcrumbs.item>
      <flux:breadcrumbs.item>{{ $product->name }}</flux:breadcrumbs.item>
    </flux:breadcrumbs>
  </div>
  {{-- @dump($product->inventoryLevels->first()->warehouse->name) --}}

  <x-card class="bg-white border border-gray-100 shadow-sm rounded-md">
    <div x-data="{ selectedTab: 'groups' }" class="w-full">
      <div x-on:keydown.right.prevent="$focus.wrap().next()" x-on:keydown.left.prevent="$focus.wrap().previous()"
        class="flex gap-2 overflow-x-auto border-b border-outline dark:border-outline-dark" role="tablist"
        aria-label="tab options">
        <button x-on:click="selectedTab = 'groups'" x-bind:aria-selected="selectedTab === 'groups'"
          x-bind:tabindex="selectedTab === 'groups' ? '0' : '-1'"
          x-bind:class="selectedTab === 'groups' ?
              'font-bold text-primary border-b-2 border-primary dark:border-primary-dark dark:text-primary-dark' :
              'text-on-surface font-medium dark:text-on-surface-dark dark:hover:border-b-outline-dark-strong dark:hover:text-on-surface-dark-strong hover:border-b-2 hover:border-b-outline-strong hover:text-on-surface-strong'"
          class="flex h-min items-center gap-2 px-4 py-2 text-sm cursor-pointer" type="button" role="tab"
          aria-controls="tabpanelGroups">
          <flux:icon name="information-circle" />
          Information
        </button>
        <button x-on:click="selectedTab = 'images'" x-bind:aria-selected="selectedTab === 'images'"
          x-bind:tabindex="selectedTab === 'images' ? '0' : '-1'"
          x-bind:class="selectedTab === 'images' ?
              'font-bold text-primary border-b-2 border-primary dark:border-primary-dark dark:text-primary-dark' :
              'text-on-surface font-medium dark:text-on-surface-dark dark:hover:border-b-outline-dark-strong dark:hover:text-on-surface-dark-strong hover:border-b-2 hover:border-b-outline-strong hover:text-on-surface-strong'"
          class="flex h-min items-center gap-2 px-4 py-2 text-sm cursor-pointer" type="button" role="tab"
          aria-controls="tabpanelImages">
          <flux:icon name="photo" />
          Images
        </button>
      </div>

      <div class="px-2 py-4 text-on-surface dark:text-on-surface-dark">
        <div x-cloak x-show="selectedTab === 'groups'" id="tabpanelGroups" role="tabpanel" aria-label="groups">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="w-full space-y-2">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <h3 class="text-sm font-semibold">SKU</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $product->sku }}</p>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <h3 class="text-sm font-semibold">Name</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $product->name }}</p>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <h3 class="text-sm font-semibold">Category</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $product->category->name }}</p>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <h3 class="text-sm font-semibold">Brand</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $product->brand->name }}</p>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <h3 class="text-sm font-semibold">Price</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ number_format($product->unit_price, 2) }}</p>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <h3 class="text-sm font-semibold">Available Stocks</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $product->cached_stock }}</p>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <h3 class="text-sm font-semibold">Low Stock Threshold</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $product->low_stock_threshold }}</p>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <h3 class="text-sm font-semibold">Status</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $product->is_active ? 'Active' : 'Inactive' }}
                </p>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                <h3 class="text-sm font-semibold">Created At</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $product->created_at->format('M d, Y') }}</p>
              </div>

              <div class="grid grid-cols-1 gap-2 mt-2">
                <h3 class="text-sm font-semibold">Barcode</h3>
                <div>{!! DNS1D::getBarcodeHTML($product->sku, 'C128') !!}</div>
              </div>
            </div>

            <div class="w-full space-y-4">
              @if ($product->primaryImage)
                <img src='{{ asset("storage/{$product->primaryImage->url}") }}'
                  alt="{{ $product->primaryImage->alt_text }}" class="w-auto max-h-48">
              @else
                <img src="{{ asset('images/no-image.svg') }}" alt="No Image" class="w-36">
              @endif
              <div>
                <flux:input type="file" wire:model="newPrimaryImage" label="Upload Primary Image"
                  accept="image/png, image/jpeg, image/jpg" />
              </div>
            </div>
          </div>

          <div class="w-full mt-4">
            <h3 class="text-sm font-semibold">Description</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $product->description }}</p>
          </div>

          <div class="w-full mt-8">
            <flux:button variant="danger" icon="trash" class="mt-4 cursor-pointer"
              wire:click="confirmForceDelete({{ $product->id }})">
              Permanently Delete Product
            </flux:button>
          </div>
        </div>

        <div x-cloak x-show="selectedTab === 'images'" id="tabpanelImages" role="tabpanel" aria-label="images">
          <div class="flex flex-col gap-4">
            <div class="flex flex-col gap-4">
              <div class="py-6">
                @if ($product->secondaryImages->count() >= 4)
                  <p class="text-sm text-red-500 mb-2">You have reached the maximum number of secondary images (4).
                    Please delete an existing image before uploading a new one.</p>
                @else
                  <flux:input type="file" wire:model="newImage" label="Upload Image"
                    accept="image/png, image/jpeg, image/jpg" />
                @endif
              </div>

              <div class="grid grid-cols-2 gap-4">
                @foreach ($product->secondaryImages as $image)
                  <div class="relative">
                    <img src='{{ asset("storage/{$image->url}") }}' alt="{{ $image->alt_text }}"
                      class="w-full h-64 object-cover rounded-md">
                    <flux:button wire:click="deleteImage({{ $image->id }})" variant="danger" size="sm"
                      class="!absolute top-1 right-1 cursor-pointer">
                      <flux:icon name="trash" class="size-4" />
                    </flux:button>
                  </div>
                @endforeach
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
  </x-card>

  <!-- Full-screen loading blocker -->
  <div wire:loading wire:target="newPrimaryImage"
    class="fixed inset-0 bg-black/60 flex flex-col items-center justify-center z-50 w-screen h-screen">

    <div class="flex items-center justify-center w-full h-full">
      <!-- Info spinner -->
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"
        class="size-5 fill-info motion-safe:animate-spin dark:fill-info">
        <path d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z" opacity=".25" />
        <path
          d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z" />
      </svg>
    </div>
  </div>

  <div wire:loading wire:target="newImage"
    class="fixed inset-0 bg-black/60 flex flex-col items-center justify-center z-50 w-screen h-screen">

    <div class="flex items-center justify-center w-full h-full">
      <!-- Info spinner -->
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true"
        class="size-5 fill-info motion-safe:animate-spin dark:fill-info">
        <path d="M12,1A11,11,0,1,0,23,12,11,11,0,0,0,12,1Zm0,19a8,8,0,1,1,8-8A8,8,0,0,1,12,20Z" opacity=".25" />
        <path
          d="M10.14,1.16a11,11,0,0,0-9,8.92A1.59,1.59,0,0,0,2.46,12,1.52,1.52,0,0,0,4.11,10.7a8,8,0,0,1,6.66-6.61A1.42,1.42,0,0,0,12,2.69h0A1.57,1.57,0,0,0,10.14,1.16Z" />
      </svg>
    </div>
  </div>

  <flux:modal name="confirm-force-delete" class="min-w-[22rem]">
    <div class="space-y-6">
      <div>
        <flux:heading size="lg">Permanently Delete Product?</flux:heading>
        <flux:text class="mt-2">
          <p>You're about to delete this product permanently.</p>
          <p>This action cannot be reversed.</p>
        </flux:text>
      </div>
      <div class="flex gap-2">
        <flux:spacer />
        <flux:modal.close>
          <flux:button variant="ghost" class="cursor-pointer">Cancel</flux:button>
        </flux:modal.close>
        <flux:button variant="danger" wire:click="forceDeleteProduct" class="cursor-pointer">Force Delete
        </flux:button>
      </div>
    </div>
  </flux:modal>

  <x-toast />
</div>
