<flux:modal.trigger name="cart-modal">
  <flux:button variant="ghost" class="relative mr-2">
    <flux:icon name="shopping-cart" class="size-6" variant="solid" />
    <span class="absolute right-1 top-0 rounded-full bg-danger px-1 leading-4 text-xs font-medium text-on-danger">
      {{ $cartItemCount }}
    </span>
  </flux:button>
</flux:modal.trigger>
