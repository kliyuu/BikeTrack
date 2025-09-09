{{-- <div>
  <!-- Create/Edit Modal -->
  @if ($showModal)
    <div class="fixed inset-0 z-50 overflow-y-auto transition-all transition-discrete">
      <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
        <div class="fixed inset-0 bg-white/30 backdrop-invert backdrop-opacity-10" wire:click="closeModal"></div>
        <div
          class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl sm:p-6">
          <form wire:prevent.submit="save">
            <h3 class="text-lg font-semibold leading-6 text-gray-900 mb-4">
              {{ $clientId ? 'Edit Client' : 'Add New Client' }}</h3>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
              <div class="sm:col-span-2">
                <label for="name" class="block text-sm font-medium text-gray-700">Company Name</label>
                <input wire:model="name" type="text" id="name"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                @error('name')
                  <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label for="code" class="block text-sm font-medium text-gray-700">Client Code</label>
                <input wire:model="code" type="text" id="code"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                @error('code')
                  <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label for="contact_person" class="block text-sm font-medium text-gray-700">Contact Person</label>
                <input wire:model="contact_person" type="text" id="contact_person"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                @error('contact_person')
                  <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label for="contact_email" class="block text-sm font-medium text-gray-700">Contact Email</label>
                <input wire:model="contact_email" type="email" id="contact_email"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                @error('contact_email')
                  <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label for="contact_phone" class="block text-sm font-medium text-gray-700">Contact Phone</label>
                <input wire:model="contact_phone" type="text" id="contact_phone"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                @error('contact_phone')
                  <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>

              <div class="sm:col-span-2">
                <label for="address" class="block text-sm font-medium text-gray-700">Address</label>
                <textarea wire:model="address" id="address" rows="2"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"></textarea>
                @error('address')
                  <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label for="city" class="block text-sm font-medium text-gray-700">City</label>
                <input wire:model="city" type="text" id="city"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                @error('city')
                  <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label for="state" class="block text-sm font-medium text-gray-700">State/Province</label>
                <input wire:model="state" type="text" id="state"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                @error('state')
                  <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label for="postal_code" class="block text-sm font-medium text-gray-700">Postal Code</label>
                <input wire:model="postal_code" type="text" id="postal_code"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                @error('postal_code')
                  <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label for="country" class="block text-sm font-medium text-gray-700">Country</label>
                <input wire:model="country" type="text" id="country"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                @error('country')
                  <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label for="tax_number" class="block text-sm font-medium text-gray-700">Tax Number</label>
                <input wire:model="tax_number" type="text" id="tax_number"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                @error('tax_number')
                  <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>

              <div>
                <label for="credit_limit" class="block text-sm font-medium text-gray-700">Credit Limit</label>
                <input wire:model="credit_limit" type="number" step="0.01" id="credit_limit"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm">
                @error('credit_limit')
                  <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>

              <div class="sm:col-span-2">
                <label for="payment_terms" class="block text-sm font-medium text-gray-700">Payment Terms</label>
                <input wire:model="payment_terms" type="text" id="payment_terms"
                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm"
                  placeholder="e.g., Net 30 days">
                @error('payment_terms')
                  <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
              </div>

              <div class="sm:col-span-2">
                <div class="flex items-center">
                  <input wire:model="is_active" type="checkbox" id="is_active"
                    class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                  <label for="is_active" class="ml-2 block text-sm text-gray-900">Active</label>
                </div>
              </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
              <button type="button" wire:click="closeModal"
                class="inline-flex justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                Cancel
              </button>
              <button type="submit"
                class="inline-flex justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600">
                {{ $clientId ? 'Update' : 'Create' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  @endif
</div> --}}
<flux:modal :dismissible="false" name="client-modal" class="w-full">
  <div class="space-y-6">
    <div>
      <flux:heading size="lg">{{ $clientId ? "Edit Client's Details" : "Add New Client" }}</flux:heading>
    </div>
    <form method="POST" wire:submit="save" class="mt-6 space-y-6">
      <flux:input wire:model="name" label="Contact Name" placeholder="Client's Contact Name" />
      <flux:input label="Date of birth" type="date" />
      <div class="flex">
        <flux:spacer />
        <flux:button wire:click="closeModal" class="bg-gray-200 hover:bg-gray-300 cursor-pointer mr-2">Cancel</flux:button>
        <flux:button type="submit" variant="primary" class="bg-blue-500 hover:bg-blue-600 cursor-pointer">Save changes</flux:button>
      </div>
    </form>
  </div>
</flux:modal>
