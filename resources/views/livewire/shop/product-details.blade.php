<div class="max-w-7xl">
  <div class="pt-6">
    <div class="mb-4">
      <flux:breadcrumbs>
        {{-- <flux:breadcrumbs.item href="{{ route('shop') }}">Home</flux:breadcrumbs.item> --}}
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
          @if ($product->primaryImage && $product->primaryImage->url)
            <img src="{{ asset("storage/{$product->primaryImage->url}") }}" alt="{{ $product->name }}"
              class="row-span-3 size-full rounded-lg object-cover" wire:click="openZoom('{{ asset("storage/{$product->primaryImage->url}") }}')" />
          @else
            <img src="{{ asset('images/no-image.svg') }}" alt="{{ $product->name }}"
              class="row-span-3 size-full rounded-lg object-cover" />
          @endif
        </div>

        {{-- <div class="mt-4 grid grid-cols-2 gap-4 lg:gap-6 lg:px-8">
          @foreach ($product->secondaryImages as $image)
            <img src="{{ asset("storage/{$image->url}") }}" alt="{{ $product->name }}"
              class="size-full aspect-3/2 rounded-lg object-cover" />
          @endforeach
        </div> --}}
        <div x-data="{
            slides: {{ $product->secondaryImages->map(
                fn($img) => [
                    'imgSrc' => asset("storage/{$img->url}"),
                    'imgAlt' => $product->name,
                ],
            ) }},
            currentSlideIndex: 1,
            previous() {
                this.currentSlideIndex = this.currentSlideIndex > 1 ?
                    this.currentSlideIndex - 1 :
                    this.slides.length
            },
            next() {
                this.currentSlideIndex = this.currentSlideIndex < this.slides.length ?
                    this.currentSlideIndex + 1 :
                    1
            },
        }" class="relative w-full overflow-hidden lg:px-8 mt-4">
          <!-- previous button -->
          <button type="button"
            class="absolute left-5 top-1/2 z-20 flex rounded-full -translate-y-1/2 items-center justify-center
               bg-gray-200 p-2 text-on-surface transition hover:bg-gray-300
               focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary
               dark:bg-surface-dark/40 dark:text-on-surface-dark dark:hover:bg-surface-dark/60
               dark:focus-visible:outline-primary-dark"
            aria-label="previous slide" x-on:click="previous()">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" fill="none"
              stroke-width="3" class="size-5 md:size-6 pr-0.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
            </svg>
          </button>

          <!-- next button -->
          <button type="button"
            class="absolute right-5 top-1/2 z-20 flex rounded-full -translate-y-1/2 items-center justify-center
               bg-gray-200 p-2 text-on-surface transition hover:bg-gray-300
               focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary
               dark:bg-surface-dark/40 dark:text-on-surface-dark dark:hover:bg-surface-dark/60
               dark:focus-visible:outline-primary-dark"
            aria-label="next slide" x-on:click="next()">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke="currentColor" fill="none"
              stroke-width="3" class="size-5 md:size-6 pl-0.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
            </svg>
          </button>

          <!-- slides -->
          <div class="relative min-h-[50svh] w-full">
            <template x-for="(slide, index) in slides" :key="index">
              <div x-show="currentSlideIndex == index + 1" class="absolute inset-0"
                x-transition.opacity.duration.1000ms>
                <img class="absolute w-full h-full inset-0 object-cover rounded-lg" x-bind:src="slide.imgSrc"
                  x-bind:alt="slide.imgAlt" wire:click="openZoom(slide.imgSrc)" />
              </div>
            </template>
          </div>

          <!-- indicators -->
          <div
            class="absolute bottom-3 md:bottom-5 left-1/2 z-20 flex -translate-x-1/2 gap-3
               bg-surface/75 px-2 py-1 rounded-radius dark:bg-surface-dark/75"
            role="group" aria-label="slides">
            <template x-for="(slide, index) in slides" :key="index">
              <button class="size-2 rounded-full transition"
                :class="currentSlideIndex === index + 1 ?
                    'bg-on-surface dark:bg-on-surface-dark' :
                    'bg-on-surface/50 dark:bg-on-surface-dark/50'"
                x-on:click="currentSlideIndex = index + 1" x-bind:aria-label="'slide ' + (index + 1)"></button>
            </template>
          </div>

        </div>

      </div>

      <!-- Product details -->
      <div class="mt-6 lg:row-span-3 lg:mt-0">
        <h2 class="sr-only">Product information</h2>
        <div class="flex flex-col">
          <div class="flex w-full">
            <h1 class="text-lg font-semibold tracking-tight text-gray-900 dark:text-white">{{ $product->name }}</h1>
            <flux:spacer />
            <p class="text-base tracking-tight text-gray-900 dark:text-white">
              ₱{{ number_format($this->getCurrentPrice(), 2) }}</p>
          </div>
          {{-- @if ($this->getCurrentStock() == 0)
            <p class="text-sm text-red-500">Out of Stock</p>
          @else
            <p class="text-sm text-green-600">
              Stock Available: <span class="font-semibold">{{ $this->getCurrentStock() }}</span>
            </p>
          @endif --}}
        </div>

        <form class="mt-6">
          <!-- Product Variants -->
          @if ($this->activeVariants->count() > 0)
            <div class="py-4">
              <h3 class="font-semibold mb-3">Select Variant:</h3>
              <div class="overflow-y-auto max-h-96 pr-1">
                <div class="grid grid-cols-1 gap-3">
                  @foreach ($this->activeVariants as $variant)
                    <label for="variant-{{ $variant->id }}" class="cursor-pointer">
                      <input type="radio" id="variant-{{ $variant->id }}" name="variant"
                        value="{{ $variant->id }}" wire:click="selectVariant({{ $variant->id }})"
                        @checked($selectedVariantId == $variant->id) class="sr-only">
                      <div
                        class="flex items-center justify-between p-3 border-2 rounded-lg {{ $selectedVariantId == $variant->id ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-200 dark:border-gray-700' }} hover:border-blue-300 transition-colors">
                        <div class="flex-1">
                          <div class="font-medium text-gray-900 dark:text-white">{{ $variant->variant_name }}</div>
                          <div class="text-sm text-gray-600 dark:text-gray-400">{{ $variant->getDisplayName() }}</div>
                          <div class="text-xs text-gray-500 dark:text-gray-500">SKU: {{ $variant->variant_sku }}</div>
                        </div>
                        <div class="text-right">
                          <div class="font-medium text-gray-900 dark:text-white">
                            ₱{{ number_format($variant->getFinalPrice(), 2) }}</div>
                          <div
                            class="text-sm {{ $variant->cached_stock > $variant->low_stock_threshold ? 'text-green-600' : ($variant->cached_stock > 0 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $variant->cached_stock > 0 ? "{$variant->cached_stock} in stock" : 'Out of stock' }}
                          </div>
                        </div>
                      </div>
                    </label>
                  @endforeach
                </div>
              </div>
            </div>
          @endif

          <div class="py-6">
            <!-- Description and details -->
            <div class="space-y-2">
              <h3 class="font-semibold">Description:</h3>

              <div class="space-y-6">
                <p class="text-sm text-gray-900 dark:text-white">
                  {{ $product->description }}
                </p>
              </div>
            </div>

            <!-- Selected Variant Specifications -->
            @if ($selectedVariant && $selectedVariant->specifications)
              <div class="space-y-2 mt-4">
                <h4 class="font-semibold">Specifications:</h4>
                <div class="text-sm text-gray-700 dark:text-gray-300">
                  {{ $selectedVariant->specifications }}
                </div>
              </div>
            @endif
          </div>

          <!-- Add to cart -->
          @if (Auth::check() && Auth::user()->hasAnyRole(['client']))
            <div class="flex gap-2 mt-10">
              <flux:label>Quantity:</flux:label>
              <flux:input type="number" wire:model="quantity" min="1" max="{{ $this->getCurrentStock() }}"
                class="!w-24" />
            </div>

            <div class="mt-6">
              @if ($this->getCurrentStock() > 0)
                <flux:button wire:click="addToCart" variant="primary" icon="shopping-cart"
                  class="w-full cursor-pointer">
                  Add to Cart
                </flux:button>
              @else
                <flux:button variant="primary" icon="shopping-cart" class="w-full cursor-pointer" disabled>
                  Out of Stock
                </flux:button>
              @endif
            </div>
          @elseif (Auth::check() && Auth::user()->hasAnyRole(roles: ['admin', 'staff']))
            <p class="mt-6 text-sm text-gray-500 dark:text-white">Admins and Staff cannot add items to cart.</p>
          @else
            {{-- <p class="mt-6 text-sm text-gray-500 dark:text-white">Please log in to add items to your cart.</p> --}}
            <div class="mt-4 flex gap-2">
              <flux:button variant="primary" size="sm" href="{{ route('login') }}" class="w-full">
                Add to Cart
              </flux:button>
            </div>
          @endif
        </form>
      </div>
    </div>
  </div>

  <x-toast />

  {{-- Modal --}}
  <flux:modal name="image-zoom" class="max-w-xl">
    <div class="flex justify-center items-center p-4">
        <img
           src="{{ $imageSrc }}"
           alt="Zoomed image"
           class="w-auto rounded-lg object-contain"
        />
    </div>
  </flux:modal>
</div>
