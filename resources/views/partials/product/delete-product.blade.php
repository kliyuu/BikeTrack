<flux:modal name="delete-product" class="min-w-[22rem]">
  <div class="space-y-6">
    <div>
      <flux:heading size="lg">Delete Product?</flux:heading>
      <flux:text class="mt-2">
        <p>You're about to delete this product.</p>
        <p>Confirm to delete.</p>
      </flux:text>
    </div>
    <div class="flex gap-2">
      <flux:spacer />
      <flux:modal.close>
        <flux:button variant="ghost" class="cursor-pointer">Cancel</flux:button>
      </flux:modal.close>
      <flux:button variant="danger" wire:click="deleteProduct" class="cursor-pointer">Delete</flux:button>
    </div>
  </div>
</flux:modal>
