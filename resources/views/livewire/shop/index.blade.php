<div class="max-w-7xl">
  <section class="lg:grid lg:h-screen lg:place-content-center lg:mt-[-3.5rem]">
    <div class="mx-auto md:grid md:grid-cols-2 md:items-center md:gap-4 lg:py-32">
      <div class="max-w-prose text-left">
        <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-6xl">
          BikeTrack - Your B2B E-commerce Platform
        </h1>

        <p class="mt-6 text-lg leading-8 text-gray-600 dark:text-gray-300">
          Browse our complete product catalog, manage your orders, and streamline your business operations.
        </p>

        <div class="mt-4 flex gap-4 sm:mt-6">
          <a class="inline-block rounded border border-indigo-600 bg-indigo-600 px-5 py-3 font-medium text-white shadow-sm transition-colors hover:bg-indigo-700"
            href="{{ route('shop.catalog') }}">
            Start Shopping Now
          </a>
        </div>
      </div>

      <div class="flex items-center justify-center mt-8 md:mt-0">
        <img src="https://picsum.photos/id/237/400" alt="Hero Image">
      </div>
    </div>
  </section>

  <section>
    <div class="mx-auto max-w-7xl">
      <div class="mx-auto max-w-2xl lg:max-w-none pb-16">
        <div class="flex justify-center">
          <h2 class="text-2xl font-bold text-gray-900">Categories</h2>

          <flux:spacer />
          <flux:button variant="ghost" size="sm" icon:trailing="arrow-right" href="{{ route('shop.catalog') }}"
            class="!text-blue-600 hover:!text-blue-700">
            Browse All Categories
          </flux:button>
        </div>

        <div class="mt-6 space-y-12 lg:grid lg:grid-cols-3 lg:space-y-0 lg:gap-x-6">
          @foreach ($categories as $category)
            <div class="group relative">
              <img src="{{ $category->image ? asset("storage/{$category->image}") : asset('images/no-image.svg') }}"
                alt="{{ $category->name }}"
                class="w-full rounded-lg bg-white object-cover group-hover:opacity-75 max-sm:h-80 sm:aspect-2/1 lg:aspect-square" />
              <h3
                class="mt-6 text-balance text-xl font-semibold text-on-surface-strong dark:text-on-surface-dark-strong">
                <a href="{{ route('shop.catalog', ['category' => $category->id]) }}">
                  <span class="absolute inset-0"></span>
                  {{ $category->name }}
                </a>
              </h3>
              <p class="text-sm text-gray-500">{{ $category->description }}</p>
            </div>
          @endforeach
        </div>
      </div>
    </div>
    </>
</div>
