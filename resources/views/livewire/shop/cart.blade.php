<flux:modal name="cart-modal" variant="flyout" class="flex flex-col">
  <div class="space-y-6">
    <div>
      <flux:heading size="lg" class="!font-semibold">Your Cart</flux:heading>
    </div>

    <div class="flex flex-col justify-between overflow-hidden">
      <!-- product -->
      <div class="overflow-y-auto max-h-[500px]">
        @forelse ($cartItems as $item)
          <div class="space-y-6 border-b border-gray-200 py-4 last:border-0">
            <div class="space-y-4">
              <div class="flex items-center gap-4">
                <img src="{{ asset("storage/{$item->image}") }}" alt="{{ $item->name }}"
                  class="size-16 rounded-sm object-cover" />

                <div>
                  <h3 class="text-sm text-gray-900">{{ $item->name }}</h3>

                  <dl class="mt-0.5 space-y-px text-[12px] text-gray-600">
                    <div>
                      <dt class="inline">Price:</dt>
                      <dd class="inline">₱{{ number_format($item->price, 2) }}</dd>
                    </div>
                  </dl>
                </div>

                <div class="flex flex-1 items-center justify-end gap-2">
                  <div>
                    <label for="Line1Qty" class="sr-only"> Quantity </label>

                    <input type="number" min="1" value="{{ $item->quantity }}" id="Line1Qty"
                      class="h-8 w-12 rounded-sm border-gray-200 bg-gray-50 p-0 text-center text-xs text-gray-600 [-moz-appearance:_textfield] focus:outline-hidden [&::-webkit-inner-spin-button]:m-0 [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:m-0 [&::-webkit-outer-spin-button]:appearance-none" />
                  </div>

                  <button wire:click="removeFromCart({{ $item->id }})"
                    class="text-gray-600 transition hover:text-red-600">
                    <span class="sr-only">Remove item</span>
                    <flux:icon name="trash" class="size-4" />
                  </button>
                </div>
              </div>
            </div>
          </div>
        @empty
          <div class="text-center">
            <flux:icon name="shopping-cart" class="size-12 mx-auto text-gray-400" />
            <p class="text-gray-500 dark:text-gray-400">Your cart is empty</p>
          </div>
        @endforelse
      </div>
    </div>
  </div>

  <flux:spacer />

  <div class="space-y-6">
    <div class="flex justify-between">
      <p class="text-sm font-medium text-on-surface-strong dark:text-on-surface-dark-strong">Total</p>
      <p class="text-sm font-medium text-on-surface-strong dark:text-on-surface-dark-strong">
        ₱{{ number_format($cartTotal, 2) }}
      </p>
    </div>
    <div class="flex flex-col gap-4">
      <flux:button variant="primary" icon:trailing="arrow-right" class="w-full cursor-pointer"
        :disabled="$cartItems->isEmpty()">
        Checkout
      </flux:button>
      <a href="{{ route('shop.catalog') }}" class="text-xs text-center font-medium cursor-pointer hover:underline">
        Continue Shopping
      </a>
    </div>
  </div>

  <x-toast />
</flux:modal>
