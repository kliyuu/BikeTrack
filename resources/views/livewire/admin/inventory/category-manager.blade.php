<div class="max-w-7xl">
  <div class="mb-6">
    <div class="flex items-center justify-between">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Category Management</h1>
      </div>
    </div>
  </div>

  <x-card class="bg-white border border-gray-100 shadow-sm rounded-md">
    <div class="card-body">
      <div class="flex items-center justify-between mb-6 border-b border-gray-200 pb-4">
        <h2 class="text-base">{{ __('Categories') }}</h2>
        <flux:button size="sm" variant="primary" icon="plus" wire:click="openCategoryModal" :loading="false"
          class="!px-4 bg-blue-600 hover:bg-blue-700 dark:text-white">
          {{ __('Add Category') }}
        </flux:button>
      </div>

      <div class="overflow-x-auto">
        <div class="pb-6">
          <!-- Search -->
          <div class="flex flex-col gap-2 lg:col-span-2 md:w-1/2">
            <flux:label>Search Category</flux:label>
            <input type="text" wire:model.live.debounce.100ms="search" id="search"
              class="w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
              placeholder="Search by category name ...">
          </div>
        </div>

        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
          <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
              <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                <button wire:click="sortBy('name')"
                  class="flex items-center space-x-1 hover:text-gray-700 dark:hover:text-gray-100">
                  <span>Name</span>
                  @if ($sortField === 'name')
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      @if ($sortDirection === 'asc')
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7">
                        </path>
                      @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                        </path>
                      @endif
                    </svg>
                  @endif
                </button>
              </th>
              <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Description
              </th>
              <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                <button wire:click="sortBy('products_count')"
                  class="flex items-center space-x-1 hover:text-gray-700 dark:hover:text-gray-100">
                  <span>Products</span>
                  @if ($sortField === 'products_count')
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      @if ($sortDirection === 'asc')
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7">
                        </path>
                      @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                        </path>
                      @endif
                    </svg>
                  @endif
                </button>
              </th>
              <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                <button wire:click="sortBy('created_at')"
                  class="flex items-center space-x-1 hover:text-gray-700 dark:hover:text-gray-100">
                  <span>Created</span>
                  @if ($sortField === 'created_at')
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      @if ($sortDirection === 'asc')
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7">
                        </path>
                      @else
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                        </path>
                      @endif
                    </svg>
                  @endif
                </button>
              </th>
              <th scope="col"
                class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                Actions
              </th>
            </tr>
          </thead>

          <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($categories as $category)
              <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10 mr-4">
                      <img
                        src="{{ $category->image ? asset("storage/{$category->image}") : asset('images/no-image.svg') }}"
                        alt="{{ $category->name }}" class="w-10 h-10 rounded-full object-cover">
                    </div>
                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $category->name }}</div>
                  </div>
                </td>
                <td class="px-6 py-4">
                  <div class="text-sm text-gray-900 dark:text-white">
                    {{ Str::limit($category->description, 80) ?: 'No description' }}
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $category->products_count > 0 ? 'bg-green-100 text-green-800 dark:bg-green-800 dark:text-green-100' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                    {{ $category->products_count }} {{ $category->products_count == 1 ? 'product' : 'products' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                  {{ $category->created_at->format('M d, Y') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                  <div class="flex items-center justify-center space-x-2">
                    <flux:tooltip content="Edit">
                      <flux:button wire:click="openCategoryModal({{ $category->id }})" size="xs" variant="primary"
                        icon="pencil-square" icon:variant="outline"
                        class="text-blue-600 bg-blue-100 hover:bg-blue-200 border-0 cursor-pointer" />
                    </flux:tooltip>
                    <flux:tooltip content="Delete">
                      <flux:button wire:click="confirmDelete({{ $category->id }})" size="xs" variant="primary"
                        icon="trash" icon:variant="outline"
                        class="text-red-600 bg-red-100 hover:bg-red-200 border-0 cursor-pointer" />
                    </flux:tooltip>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="px-6 py-12 text-center">
                  <div class="text-gray-500 dark:text-gray-400">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor"
                      viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21l-7-8 7-8m-7 8l-7-8 7 8m0 0v-13a2 2 0 00-2-2H4a2 2 0 00-2 2v13a2 2 0 002 2h1a2 2 0 002-2z">
                      </path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium">No categories found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating your first
                      category.</p>
                    <div class="mt-6">
                      <button wire:click="openCategoryModal"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none transition ease-in-out duration-150">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Add Category
                      </button>
                    </div>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </x-card>

  {{-- Toast Component --}}
  <x-toast />

  {{-- Category Modal --}}
  <flux:modal name="category-modal" class="md:w-1/2">
    <div class="space-y-6">
      <div>
        <flux:heading size="lg">{{ $categoryId ? 'Edit Category' : 'Add New Category' }}</flux:heading>
      </div>

      <div class="flex flex-col">
        <flux:label>Current Image:</flux:label>
        <div class="flex-shrink-0 h-24 w-24 mt-2">
          <img src="{{ $currentImage ? asset("storage/{$currentImage}") : asset('images/no-image.svg') }}"
            alt="{{ $name }}" class="w-20 h-20 rounded-lg object-cover">
        </div>
      </div>

      <form method="POST" wire:submit="saveCategory" class="mt-6 space-y-6">
        <flux:input wire:model="name" label="Category Name" placeholder="Enter category name" />

        <flux:textarea wire:model="description" label="Description" placeholder="Enter description" />

        <flux:field>
          <flux:input wire:model="image" label="Upload Image" type="file" placeholder="Upload image" />
          <flux:text class="text-xs">*Image size must be less than 2MB</flux:text>
        </flux:field>

        <div class="flex">
          <flux:spacer />
          <flux:button wire:click="closeModal" class="bg-gray-200 hover:bg-gray-300 cursor-pointer mr-2">Cancel
          </flux:button>
          <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="image"
            class="bg-blue-500 hover:bg-blue-600 cursor-pointer">
            {{ $categoryId ? 'Update' : 'Save' }}</flux:button>
        </div>
      </form>
    </div>
  </flux:modal>

  {{-- Delete Category Modal --}}
  <flux:modal name="delete-category" class="min-w-[22rem]">
    <div class="space-y-6">
      <div>
        <flux:heading size="lg">Delete category?</flux:heading>
        <flux:text class="mt-2">
          <p>You're about to delete this category.</p>
          <p>This action cannot be reversed.</p>
        </flux:text>
      </div>
      <div class="flex gap-2">
        <flux:spacer />
        <flux:modal.close>
          <flux:button variant="ghost" class="cursor-pointer">Cancel</flux:button>
        </flux:modal.close>
        <flux:button wire:click="deleteCategory" variant="danger" class="cursor-pointer">Delete</flux:button>
      </div>
    </div>
  </flux:modal>
</div>
