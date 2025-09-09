<div>
  <main class="mx-auto max-w-7xl">
    <div class="flex items-baseline justify-between border-b border-gray-200 py-6">
      <h1 class="text-4xl font-bold tracking-tight text-gray-900">Product Catalog</h1>

      <div class="flex items-center">
        <flux:dropdown align="end">
          <flux:button icon:trailing="chevron-down" variant="ghost">Sort by</flux:button>
          <flux:menu>
            <flux:menu.radio.group wire:model.live="sortField">
              <flux:menu.radio value="name">Name</flux:menu.radio>
              <flux:menu.radio value="newest">Newest</flux:menu.radio>
              <flux:menu.radio value="price_asc">Price: Low to High</flux:menu.radio>
              <flux:menu.radio value="price_desc">Price: High to Low</flux:menu.radio>
            </flux:menu.radio.group>
          </flux:menu>
        </flux:dropdown>

        <button type="button" class="-m-2 ml-5 p-2 text-gray-400 hover:text-gray-500 sm:ml-7">
          <span class="sr-only">View grid</span>
          <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="size-5">
            <path
              d="M4.25 2A2.25 2.25 0 0 0 2 4.25v2.5A2.25 2.25 0 0 0 4.25 9h2.5A2.25 2.25 0 0 0 9 6.75v-2.5A2.25 2.25 0 0 0 6.75 2h-2.5Zm0 9A2.25 2.25 0 0 0 2 13.25v2.5A2.25 2.25 0 0 0 4.25 18h2.5A2.25 2.25 0 0 0 9 15.75v-2.5A2.25 2.25 0 0 0 6.75 11h-2.5Zm9-9A2.25 2.25 0 0 0 11 4.25v2.5A2.25 2.25 0 0 0 13.25 9h2.5A2.25 2.25 0 0 0 18 6.75v-2.5A2.25 2.25 0 0 0 15.75 2h-2.5Zm0 9A2.25 2.25 0 0 0 11 13.25v2.5A2.25 2.25 0 0 0 13.25 18h2.5A2.25 2.25 0 0 0 18 15.75v-2.5A2.25 2.25 0 0 0 15.75 11h-2.5Z"
              clip-rule="evenodd" fill-rule="evenodd" />
          </svg>
        </button>
        <button type="button" command="show-modal" commandfor="mobile-filters"
          class="-m-2 ml-4 p-2 text-gray-400 hover:text-gray-500 sm:ml-6 lg:hidden">
          <span class="sr-only">Filters</span>
          <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="size-5">
            <path
              d="M2.628 1.601C5.028 1.206 7.49 1 10 1s4.973.206 7.372.601a.75.75 0 0 1 .628.74v2.288a2.25 2.25 0 0 1-.659 1.59l-4.682 4.683a2.25 2.25 0 0 0-.659 1.59v3.037c0 .684-.31 1.33-.844 1.757l-1.937 1.55A.75.75 0 0 1 8 18.25v-5.757a2.25 2.25 0 0 0-.659-1.591L2.659 6.22A2.25 2.25 0 0 1 2 4.629V2.34a.75.75 0 0 1 .628-.74Z"
              clip-rule="evenodd" fill-rule="evenodd" />
          </svg>
        </button>
      </div>
    </div>

    <section aria-labelledby="products-heading" class="pt-6 pb-24">
      <h2 id="products-heading" class="sr-only">Products</h2>

      <div class="grid grid-cols-1 gap-x-8 gap-y-10 lg:grid-cols-4">
        <!-- Filters -->
        <div class="hidden lg:block border-r border-gray-200">
          <div class="flex border-b border-gray-200 mr-6">
            <flux:fieldset class="mb-6">
              <flux:legend>Category</flux:legend>

              <flux:radio.group wire:model.live="categoryFilter">
                <flux:radio value="" label="All Categories" class="cursor-pointer" />
                @foreach ($categories as $category)
                  <flux:radio value="{{ $category->id }}" label="{{ $category->name }}" class="cursor-pointer" />
                @endforeach
              </flux:radio.group>
            </flux:fieldset>
          </div>

          <div class="divide-y divide-outline overflow-hidden text-black border-b border-gray-200 mr-6">
            <div x-data="{ isExpanded: false }">
              <button id="controlsAccordionItemOne" type="button"
                class="flex w-full items-center justify-between gap-4 bg-surface-alt py-4 pr-4 text-left text-base font-medium underline-offset-2 hover:bg-surface-alt/75 focus-visible:bg-surface-alt/75 focus-visible:underline focus-visible:outline-hidden dark:bg-surface-dark-alt dark:hover:bg-surface-dark-alt/75 dark:focus-visible:bg-surface-dark-alt/75 cursor-pointer"
                aria-controls="accordionItemOne" x-on:click="isExpanded = ! isExpanded"
                x-bind:class="isExpanded ? 'text-on-surface-strong dark:text-on-surface-dark-strong font-medium' :
                    'text-black'"
                x-bind:aria-expanded="isExpanded ? 'true' : 'false'">
                Brand
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke-width="2"
                  stroke="currentColor" class="size-5 shrink-0 transition" aria-hidden="true"
                  x-bind:class="isExpanded ? 'rotate-180' : ''">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
              </button>
              <div x-cloak x-show="isExpanded" id="accordionItemOne" role="region"
                aria-labelledby="controlsAccordionItemOne" x-collapse>
                <flux:fieldset class="mb-6">
                  <flux:checkbox.group wire:model.live="brandFilter">
                    @foreach ($brands as $brand)
                      <flux:checkbox value="{{ $brand->id }}" label="{{ $brand->name }}" class="cursor-pointer" />
                    @endforeach
                  </flux:checkbox.group>
                </flux:fieldset>
              </div>
            </div>
          </div>

        </div>

        <!-- Product grid -->
        <div class="lg:col-span-3">
          <div class="grid grid-cols-1 gap-x-8 gap-y-10 sm:grid-cols-2 lg:grid-cols-3">
            @forelse ($products as $product)
              <article
                class="group flex rounded-radius max-w-sm flex-col overflow-hidden border border-outline bg-surface-alt text-on-surface dark:border-outline-dark dark:bg-surface-dark-alt dark:text-on-surface-dark">
                <!-- Image -->
                <div class="h-32 md:h-48 overflow-hidden">
                  <img src="{{ asset("storage/{$product->primaryImage->url}") }}"
                    class="object-cover transition duration-700 ease-out group-hover:scale-105"
                    alt="{{ $product->name }}" />
                </div>
                <!-- Content -->
                <div class="flex flex-col gap-4 p-6">
                  <!-- Header -->
                  <div class="flex flex-col md:flex-row gap-4 md:gap-12 justify-between">
                    <!-- Title & Rating -->
                    <div class="flex flex-col">
                      <span class="text-sm">
                        <span class="sr-only">Price</span>₱{{ number_format($product->unit_price, 2) }}
                      </span>
                      <h3 class="text-lg font-bold text-on-surface-strong dark:text-on-surface-dark-strong"
                        aria-describedby="productDescription">{{ $product->name }}</h3>
                    </div>
                  </div>
                  <p id="productDescription" class="mb-2 text-pretty text-sm">
                    {{ Str::limit($product->description, 80) }}
                  </p>
                  <!-- Button -->
                  <flux:button variant="primary" icon="eye" class="cursor-pointer"
                    href="{{ route('shop.product', $product->id) }}">
                    View Details
                  </flux:button>
                </div>
              </article>
            @empty
              <div class="col-span-3 text-center">
                <p class="text-lg text-gray-500">No products found.</p>
              </div>
            @endforelse
          </div>
        </div>
      </div>
    </section>
  </main>
</div>
