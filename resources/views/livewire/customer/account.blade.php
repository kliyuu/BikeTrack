<div class="max-w-7xl">
  <!-- Page Header -->
  <div class="my-6">
    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Account Settings</h1>
    <p class="mt-2 text-gray-600 dark:text-gray-400">Manage your profile, view information, and track your order statistics.</p>
  </div>

  {{-- @dump($orderStats) --}}

  <div class="mx-auto max-w-2xl pb-10 lg:grid lg:max-w-7xl lg:grid-cols-3 lg:grid-rows-[auto_auto_1fr] lg:gap-x-6">
    <div class="md:col-span-2">
      <x-card class="bg-white border border-gray-100 shadow-sm rounded-md">
        <div class="card-body">
          <div x-data="{ selectedTab: 'profile' }" class="w-full">
            <div x-on:keydown.right.prevent="$focus.wrap().next()" x-on:keydown.left.prevent="$focus.wrap().previous()"
              class="flex gap-2 overflow-x-auto border-b border-outline dark:border-outline-dark" role="tablist"
              aria-label="tab options">
              <button x-on:click="selectedTab = 'profile'" x-bind:aria-selected="selectedTab === 'profile'"
                x-bind:tabindex="selectedTab === 'profile' ? '0' : '-1'"
                x-bind:class="selectedTab === 'profile' ?
                    'font-bold text-primary border-b-2 border-primary dark:border-primary-dark dark:text-primary-dark' :
                    'text-on-surface font-medium dark:text-on-surface-dark dark:hover:border-b-outline-dark-strong dark:hover:text-on-surface-dark-strong hover:border-b-2 hover:border-b-outline-strong hover:text-on-surface-strong'"
                class="flex h-min items-center gap-2 px-4 py-2 text-sm cursor-pointer" type="button" role="tab"
                aria-controls="tabpanelProfile">
                <flux:icon name="user" />
                Profile
              </button>

              <button x-on:click="selectedTab = 'password'" x-bind:aria-selected="selectedTab === 'password'"
                x-bind:tabindex="selectedTab === 'password' ? '0' : '-1'"
                x-bind:class="selectedTab === 'password' ?
                    'font-bold text-primary border-b-2 border-primary dark:border-primary-dark dark:text-primary-dark' :
                    'text-on-surface font-medium dark:text-on-surface-dark dark:hover:border-b-outline-dark-strong dark:hover:text-on-surface-dark-strong hover:border-b-2 hover:border-b-outline-strong hover:text-on-surface-strong'"
                class="flex h-min items-center gap-2 px-4 py-2 text-sm cursor-pointer" type="button" role="tab"
                aria-controls="tabpanelPassword">
                <flux:icon name="lock-closed" />
                Password
              </button>

              <button x-on:click="selectedTab = 'billing'" x-bind:aria-selected="selectedTab === 'billing'"
                x-bind:tabindex="selectedTab === 'billing' ? '0' : '-1'"
                x-bind:class="selectedTab === 'billing' ?
                    'font-bold text-primary border-b-2 border-primary dark:border-primary-dark dark:text-primary-dark' :
                    'text-on-surface font-medium dark:text-on-surface-dark dark:hover:border-b-outline-dark-strong dark:hover:text-on-surface-dark-strong hover:border-b-2 hover:border-b-outline-strong hover:text-on-surface-strong'"
                class="flex h-min items-center gap-2 px-4 py-2 text-sm cursor-pointer" type="button" role="tab"
                aria-controls="tabpanelBilling">
                <flux:icon name="credit-card" />
                Billing Details
              </button>
            </div>

            <div class="px-2 py-4 text-on-surface dark:text-on-surface-dark">
              <div x-cloak x-show="selectedTab === 'profile'" id="tabpanelProfile" role="tabpanel" aria-label="profile">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Profile Information</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                  Update your account's profile information and email address.
                </p>

                <form wire:submit.prevent="updateProfile" class="mt-6 space-y-6">
                  <div class="flex flex-col gap-4">
                    <flux:input wire:model="name" label="Name" id="name" type="text"
                      class="w-full md:w-1/2" />
                    <flux:input wire:model="email" label="Email" id="email" type="email"
                      class="w-full md:w-1/2" />
                  </div>

                  <div class="flex items-center gap-4">
                    <div class="flex items-center justify-end">
                      <flux:button variant="primary" type="submit" class="w-full cursor-pointer">
                        Save
                      </flux:button>
                    </div>

                    <x-action-message class="me-3" on="profile-updated">
                      Saved.
                    </x-action-message>
                  </div>
                </form>
              </div>

              <div x-cloak x-show="selectedTab === 'password'" id="tabpanelPassword" role="tabpanel"
                aria-label="password">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Change Password</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                  Update your account's password.
                </p>

                <form wire:submit.prevent="updatePassword" class="mt-6 space-y-6">
                  <div class="flex flex-col gap-4">
                    <flux:input label="Current Password" wire:model="currentPassword" type="password" viewable
                      class="w-full md:w-1/2" />
                    <flux:input label="New Password" wire:model="newPassword" type="password" viewable
                      class="w-full md:w-1/2" />
                    <flux:input label="Confirm New Password" wire:model="confirmPassword" type="password" viewable
                      class="w-full md:w-1/2" />
                  </div>

                  <div class="flex items-center gap-4">
                    <div class="flex items-center justify-end">
                      <flux:button variant="primary" type="submit" class="w-full cursor-pointer">
                        Update Password
                      </flux:button>
                    </div>

                    <x-action-message class="me-3" on="profile-updated">
                      Password Updated.
                    </x-action-message>
                  </div>
                </form>
              </div>

              <div x-cloak x-show="selectedTab === 'billing'" id="tabpanelBilling" role="tabpanel"
                aria-label="billing">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Billing Information</h2>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                  Update your account's billing information.
                </p>

                <form wire:submit.prevent="updateBillingDetails" class="mt-6 space-y-6">
                  <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <flux:input wire:model="companyName" label="Company Name" id="company_name" type="text"
                      class="w-full" />
                    <flux:input wire:model="taxNumber" label="Tax ID" id="tax_id" type="text"
                      class="w-full" />
                    <flux:input wire:model="contactName" label="Contact Name" id="contact_name" type="text"
                      class="w-full" />
                    <flux:input wire:model="contactEmail" label="Contact Email" id="contact_email" type="email"
                      class="w-full" />
                    <flux:input wire:model="contactPhone" label="Contact Phone" id="contact_phone" type="text"
                      class="w-full" />
                  </div>

                  <div class="grid grid-cols-1 gap-4">
                    <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">Billing Address</h3>
                    <flux:input wire:model="billingAddress" id="billing_address" type="text" class="w-full" />
                  </div>

                  <div class="flex items-center gap-4">
                    <div class="flex items-center justify-end">
                      <flux:button variant="primary" type="submit" class="w-full cursor-pointer">
                        Update Billing Information
                      </flux:button>
                    </div>

                    <x-action-message class="me-3" on="profile-updated">
                      Billing Information Updated.
                    </x-action-message>
                  </div>
                </form>
              </div>
            </div>
          </div>
        </div>
      </x-card>
    </div>

    <div class="mt-6 lg:mt-0">
      <x-card class="bg-white border border-gray-100 shadow-sm rounded-md">
        <div class="card-body">
          <div class="flex items-center justify-between mb-6 border-b border-gray-200 pb-4">
            <h2 class="text-base">{{ __('Order Statistics') }}</h2>
          </div>

          <div class="space-y-4">
            <div class="flex flex-col">
              <dl class="space-y-3 w-full">
                <div class="flex justify-between">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">Total Orders</dt>
                  <dd class="text-sm font-semibold text-gray-900 dark:text-white">
                    {{ number_format($orderStats['totalOrders']) }}
                  </dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">Pending Orders</dt>
                  <dd class="text-sm font-semibold text-yellow-600">
                    {{ number_format($orderStats['pendingOrders']) }}
                  </dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">Completed Orders</dt>
                  <dd class="text-sm font-semibold text-green-600">
                    {{ number_format($orderStats['completedOrders']) }}
                  </dd>
                </div>
                <div class="flex justify-between">
                  <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">Total Spent</dt>
                  <dd class="text-sm font-semibold text-gray-900 dark:text-white">
                    ₱ {{ number_format($orderStats['totalSpent'], 2) }}
                  </dd>
                </div>
                @if ($orderStats['lastOrderDate'])
                  <div class="pt-3 border-t border-gray-200">
                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-300">Last Order Date</dt>
                    <dd class="text-sm text-gray-900 dark:text-white">
                      {{ $orderStats['lastOrderDate']->format('M j, Y') }}
                    </dd>
                  </div>
                @endif
              </dl>

              <div class="mt-4">
                <a href="{{ route('client.order-history') }}" class="text-sm text-blue-600 hover:text-blue-500 font-medium">
                  View Order History →
                </a>
              </div>
            </div>
          </div>
        </div>
      </x-card>
    </div>
  </div>
</div>
