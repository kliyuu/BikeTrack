<div class="max-w-7xl">
  <div class="pt-6">
    <div class="mb-4">
      <flux:breadcrumbs>
        <flux:breadcrumbs.item href="{{ route('shop') }}">Home</flux:breadcrumbs.item>
        <flux:breadcrumbs.item href="{{ route('shop.catalog') }}">Product Catalog</flux:breadcrumbs.item>
        <flux:breadcrumbs.item>{{ $product->name }}</flux:breadcrumbs.item>
      </flux:breadcrumbs>
    </div>

    {{-- @dump($cartItems) --}}

    <!-- Product info -->
    <div
      class="mx-auto max-w-2xl px-4 pt-10 pb-16 sm:px-6 lg:grid lg:max-w-7xl lg:grid-cols-3 lg:grid-rows-[auto_auto_1fr] lg:gap-x-8 lg:px-8 lg:pt-16 lg:pb-24">
      <div class="lg:col-span-2 lg:border-r lg:border-gray-200 lg:pr-8">
        <div class="mx-auto max-w-2xl lg:grid lg:max-w-7xl lg:grid-cols-1 lg:gap-8 lg:px-8">
          <img src="{{ asset("storage/{$product->primaryImage->url}") }}" alt="{{ $product->name }}"
            class="row-span-3 size-full rounded-lg object-cover" />
        </div>

        <div class="mt-4 grid grid-cols-2 gap-4 lg:gap-6 lg:px-8">
          @foreach ($product->secondaryImages as $image)
            <img src="{{ asset("storage/{$image->url}") }}" alt="{{ $product->name }}"
              class="size-full aspect-3/2 rounded-lg object-cover" />
          @endforeach
        </div>
      </div>

      <!-- Product details -->
      <div class="mt-6 lg:row-span-3 lg:mt-0">
        <h2 class="sr-only">Product information</h2>
        <div class="flex flex-col">
          <div class="flex w-full">
            <h1 class="text-base font-semibold tracking-tight text-gray-900 dark:text-white">{{ $product->name }}</h1>
            <flux:spacer />
            <p class="text-base tracking-tight text-gray-900 dark:text-white">₱{{ number_format($product->unit_price, 2) }}</p>
          </div>
          @if ($product->cached_stock == 0)
            <p class="text-sm text-red-500">Out of Stock</p>
          @else
            <p class="text-sm text-green-600">
              Stock Available: <span class="font-semibold">{{ $product->cached_stock }}</span>
            </p>
          @endif
        </div>

        <form class="mt-6">
          <div class="py-6">
            <!-- Description and details -->
            <div>
              <h3 class="sr-only">Description</h3>

              <div class="space-y-6">
                <p class="text-base text-gray-900 dark:text-white">
                  {{ $product->description }}
                </p>
              </div>
            </div>
          </div>

          <!-- Add to cart -->
          @if (Auth::check() && Auth::user()->hasAnyRole(['client']))
            <div class="flex gap-2 mt-10">
              <flux:label>Quantity:</flux:label>
              <flux:input type="number" wire:model="quantity" min="1" max="{{ $product->cached_stock }}"
                class="!w-24" />
            </div>

            <div class="mt-4">
              <flux:button wire:click="addToCart({{ $product->id }})" variant="primary" icon="shopping-cart"
                class="w-full cursor-pointer">
                Add to Cart
              </flux:button>
            </div>
          @elseif (Auth::check() && Auth::user()->hasAnyRole(roles: ['admin', 'staff']))
            <p class="mt-6 text-sm text-gray-500 dark:text-white">Admins and Staff cannot add items to cart.</p>
          @else
            <p class="mt-6 text-sm text-gray-500 dark:text-white">Please log in to add items to your cart.</p>
          @endif
        </form>
      </div>
    </div>
  </div>

  <x-toast />
</div>
