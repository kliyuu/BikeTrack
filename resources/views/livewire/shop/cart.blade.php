<flux:modal name="cart-modal" variant="flyout" class="flex flex-col max-w-lg">
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
                  <h3 class="text-sm text-gray-900 dark:text-white">{{ $item->name }}</h3>

                  <dl class="mt-0.5 space-y-px text-[12px] text-gray-600">
                    <div>
                      <dt class="inline dark:text-gray-400">Price:</dt>
                      <dd class="inline dark:text-gray-400">₱{{ number_format($item->price, 2) }}</dd>
                    </div>
                  </dl>
                </div>

                <div class="flex flex-1 items-center justify-end gap-2">
                  <div>
                    {{-- <input size="sm" wire:change="updateCartQuantity({{ $item->id }}, $event.target.value)" type="number" min="1" max="{{ $item->available_stock }}" value="{{ $item->quantity }}"
                      class="h-8 w-12 rounded-sm border-gray-200 bg-gray-50 p-0 text-center text-xs text-gray-600 [-moz-appearance:_textfield] focus:outline-hidden [&::-webkit-inner-spin-button]:m-0 [&::-webkit-inner-spin-button]:appearance-none [&::-webkit-outer-spin-button]:m-0 [&::-webkit-outer-spin-button]:appearance-none" /> --}}
                    <div x-data="{
                        quantity: {{ $item->quantity }},
                        minQuantity: 1,
                        maxQuantity: @js($item->available_stock),
                        updateLivewire() {
                            $wire.updateCartQuantity({{ $item->id }}, this.quantity);
                        }
                    }" x-init="$watch('quantity', value => updateLivewire())">
                      <div class="flex items-center">
                        <button @click="quantity = Math.max(minQuantity, quantity - 1)"
                          :disabled="quantity <= minQuantity"
                          class="flex items-center justify-center h-8 w-6 text-gray-600 transition hover:opacity-75 dark:text-gray-200 dark:hover:opacity-75">
                          <flux:icon name="minus" class="size-4" />
                        </button>

                        <input type="number" x-model.number="quantity" :min="minQuantity" :max="maxQuantity"
                          class="h-8 w-10 rounded border border-outline bg-gray-50 p-0 text-center text-xs text-gray-600">

                        <button @click="quantity = Math.min(maxQuantity, quantity + 1)"
                          :disabled="quantity >= maxQuantity"
                          class="flex items-center justify-center h-8 w-6 text-gray-600 transition hover:opacity-75 dark:text-gray-200 dark:hover:opacity-75">
                          <flux:icon name="plus" class="size-4" />
                        </button>
                      </div>
                    </div>

                  </div>

                  <button wire:click="removeFromCart({{ $item->id }})"
                    class="text-gray-600 transition hover:text-red-600 dark:text-gray-400">
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
    <div>
      <flux:heading class="!font-semibold mb-4">Payment Details</flux:heading>
      <div class="space-y-2">
        <div class="flex justify-between">
          <p class="text-sm text-black dark:text-white">Items Subtotal</p>
          <p class="text-sm text-black dark:text-white">
            ₱{{ number_format($cartTotal, 2) }}
          </p>
        </div>

        <div class="flex justify-between">
          <p class="text-sm text-black dark:text-white">Shipping Fee</p>
          <p class="text-sm text-black dark:text-white">
            ₱{{ number_format($shippingFee, 2) }}
          </p>
        </div>
      </div>

      <div class="flex justify-between mt-4 pt-4 border-t border-gray-200">
        <p class="text-sm text-black dark:text-white">Total Payment</p>
        <p class="text-sm font-semibold text-black dark:text-white">
          ₱{{ number_format($cartTotal + $shippingFee, 2) }}
        </p>
      </div>
    </div>

    <div class="flex flex-col gap-4">
      <flux:button variant="primary" icon:trailing="arrow-right" class="w-full cursor-pointer"
        href="{{ route('shop.checkout') }}" :disabled="$cartItems->isEmpty()">
        Checkout
      </flux:button>
      <a href="{{ route('shop.catalog') }}" class="text-xs text-center font-medium cursor-pointer hover:underline">
        Continue Shopping
      </a>
    </div>
  </div>

  <x-toast />
</flux:modal>
