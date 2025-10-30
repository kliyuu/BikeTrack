<div class="max-w-7xl">
  <div class="mb-6">
    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white pt-4">Checkout</h2>
  </div>

  {{-- @dump($cartItems) --}}

  @if ($orderPlaced)
    <!-- Order Confirmation -->
    <div class="text-center">
      <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-12 mb-8">
        <div class="mb-8">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
          </div>
          <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Order Placed Successfully!</h1>
          <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Your order has been received and is being processed
          </p>
        </div>

        <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Order Details</h2>
        <div class="text-center">
          <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $orderNumber }}</p>
          <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Order Number</p>
        </div>
        <div class="mt-6 flex justify-center space-x-4">
          <a href="{{ route('client.order-history') }}"
            class="bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 transition-colors">
            View Orders
          </a>
          <a href="{{ route('shop.catalog') }}"
            class="bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
            Continue Shopping
          </a>
        </div>
      </div>
    </div>
  @else
    <!-- Checkout Form -->
    <x-card class="bg-white border border-gray-100 shadow-sm rounded-md my-6">
      <div class="card-body">
        @if ($cartItems->isEmpty())
          <div class="space-y-4 p-12">
            <div class="flex flex-col justify-center">
              <flux:icon name="shopping-cart" class="size-12 mx-auto text-gray-500" />
              <p class="text-center text-gray-500 dark:text-gray-400">Your cart is empty.</p>
            </div>
            <div class="text-center">
              <flux:button variant="primary" icon:trailing="arrow-right" class="cursor-pointer"
                href="{{ route('shop.catalog') }}">
                Start Shopping
              </flux:button>
            </div>
          </div>
        @else
          <form action="#" class="mx-auto max-w-screen-xl px-4 2xl:px-0">
            <div class="mt-6 sm:mt-8 lg:flex lg:items-start lg:gap-12 xl:gap-16">
              <div class="min-w-0 flex-1 space-y-8">
                <div class="space-y-4">
                  <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Delivery Details</h2>

                  <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <flux:input wire:model="contactName" label="Contact Name" type="text" placeholder="John Doe"
                      required />
                    <flux:input wire:model="contactEmail" label="Contact Email" type="email"
                      placeholder="john.doe@example.com" required />
                    <flux:input wire:model="contactPhone" label="Contact Number" type="tel"
                      placeholder="09123456789" required />

                    <flux:input wire:model="companyName" label="Company Name" type="text" placeholder="My Company" />
                  </div>
                  <div class="grid grid-cols-1 gap-4">
                    <flux:textarea wire:model="billingAddress" label="Billing Address" type="text"
                      placeholder="1234 Main St, City, Country" required />
                  </div>
                </div>

                <div class="space-y-4">
                  <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Payment Method</h3>
                  <div class="flex flex-col gap-4">
                    <flux:radio.group wire:model.live="paymentMethod"
                      class="grid grid-cols-1 md:grid-cols-3 gap-4 w-full">
                      <div>
                        <label for="cod"
                          class="flex items-center justify-between gap-4 rounded border border-gray-300 bg-white p-3 text-sm font-medium shadow-sm transition-colors hover:bg-gray-50 has-checked:border-blue-600 has-checked:ring-1 has-checked:ring-blue-600">
                          <div>
                            <p class="text-gray-700">Cash on Delivery</p>
                            <p id="cod-text" class="mt-1 text-xs font-normal text-gray-500 dark:text-gray-400">
                              Pay with cash upon delivery
                            </p>
                          </div>

                          <flux:radio name="cod" value="cod" id="cod" class="size-5" />
                        </label>
                      </div>

                      <div class="hidden">
                        <label for="card"
                          class="flex items-center justify-between gap-4 rounded border border-gray-300 bg-white p-3 text-sm font-medium shadow-sm transition-colors hover:bg-gray-50 has-checked:border-blue-600 has-checked:ring-1 has-checked:ring-blue-600">
                          <div>
                            <p class="text-gray-700">Credit/Debit Card</p>
                            <p id="credit-card-text" class="mt-1 text-xs font-normal text-gray-500 dark:text-gray-400">
                              Pay with your credit/debit card
                            </p>
                          </div>

                          <flux:radio name="card" value="card" id="card" class="size-5" />
                        </label>
                      </div>

                      <div>
                        <label for="gcash"
                          class="flex items-center justify-between gap-4 rounded border border-gray-300 bg-white p-3 text-sm font-medium shadow-sm transition-colors hover:bg-gray-50 has-checked:border-blue-600 has-checked:ring-1 has-checked:ring-blue-600">
                          <div>
                            <p class="text-gray-700">GCash</p>
                            <p id="gcash-text" class="mt-1 text-xs font-normal text-gray-500 dark:text-gray-400">
                              Pay with your GCash account
                            </p>
                          </div>

                          <flux:radio name="gcash" value="gcash" id="gcash" class="size-5" />
                        </label>
                      </div>
                    </flux:radio.group>

                    @if ($paymentMethod === 'gcash')
                      <div class="mt-4">
                        <div class="flex gap-4">
                          <img src="{{ asset('images/gcash-qr.jpg') }}" alt="GCash QR Code" class="w-48 mb-3">

                          <div class="mt-4">
                            <flux:input label="Upload Proof of Payment" type="file" wire:model="paymentProof"
                              class="w-full text-sm text-gray-600" />
                            <div wire:loading wire:target="paymentProof" class="text-xs text-blue-600 mt-1">
                              Uploading...
                            </div>
                          </div>
                        </div>

                        <p class="text-sm font-medium text-gray-500">Scan QR Code and upload proof of payment</p>
                      </div>
                    @endif
                  </div>
                </div>

                <div class="space-y-4">
                  <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Order Notes</h3>

                  <div class="grid grid-cols-1 gap-4">
                    <flux:textarea wire:model="orderNotes" type="text"
                      placeholder="Add any special instructions or notes for your order." />
                  </div>
                </div>
              </div>

              <div class="mt-6 w-full space-y-4 sm:mt-8 lg:mt-0 lg:max-w-xs xl:max-w-md">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Order Summary</h2>

                @if (session('success'))
                  <div class="rounded-lg bg-green-50 p-4">
                    <div class="flex">
                      <div class="shrink-0">
                        <svg class="h-5 w-5 text-green-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                          fill="none" viewBox="0 0 20 20">
                          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                            stroke-width="2"
                            d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.707-9.293-4 4a1 1 0 0 1-1.414 0l-2-2a1 1 0 1 1 1.414-1.414L9 11.586l3.293-3.293a1 1 0 0 1 1.414 1.414Z" />
                        </svg>
                      </div>
                      <div class="ms-3">
                        <p class="text-sm font-medium text-green-800"> {{ session('success') }} </p>
                      </div>
                    </div>
                  </div>
                @endif

                <div class="flow-root">
                  <ul role="list" class="-my-4 divide-y divide-gray-200 dark:divide-gray-800">
                    @foreach ($cartItems as $item)
                      <li class="flex items-center gap-4 py-4">
                        <img src="{{ asset("storage/{$item->image}") }}" class="h-16 w-16 rounded-lg object-cover"
                          alt="{{ $item->name }}" />

                        <div class="w-full">
                          <div class="flex justify-between">
                            <h3 class="text-sm text-gray-900 dark:text-white"> {{ $item->name }} </h3>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                              ₱ {{ number_format($item->price * $item->quantity, 2) }}
                            </p>
                          </div>

                          <div class="mt-1 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                            <p>{{ $item->price }} each</p>
                          </div>

                          <div class="mt-1 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400">
                            <p>Qty: {{ $item->quantity }}</p>
                          </div>
                        </div>
                      </li>
                    @endforeach
                  </ul>
                </div>

                <div class="mt-6 border-t border-gray-200 pt-4 dark:border-gray-700">
                  <div>
                    <dl class="flex items-center justify-between gap-4 py-3">
                      <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Subtotal</dt>
                      <dd class="text-base font-medium text-gray-900 dark:text-white">
                        ₱ {{ number_format($cartTotal, 2) }}
                      </dd>
                    </dl>

                    <dl class="flex items-center justify-between gap-4 py-3">
                      <dt class="text-base font-normal text-gray-500 dark:text-gray-400">Shipping Fee:</dt>
                      <dd class="text-base font-medium text-green-500">₱ {{ number_format($shippingFee, 2) }}</dd>
                    </dl>

                    <dl class="flex items-center justify-between gap-4 py-3 border-t border-gray-200 pt-3 dark:border-gray-700">
                      <dt class="text-base font-bold text-gray-900 dark:text-white">Total</dt>
                      <dd class="text-base font-bold text-gray-900 dark:text-white">
                        ₱ {{ number_format($grandTotal, 2) }}
                      </dd>
                    </dl>
                  </div>
                </div>

                <div class="space-y-3">
                  <flux:button wire:click="placeOrder" variant="primary" class="w-full cursor-pointer">
                    Place Order
                  </flux:button>
                </div>
              </div>
            </div>
          </form>
        @endif
      </div>
    </x-card>
  @endif
</div>
