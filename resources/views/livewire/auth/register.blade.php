<div class="flex flex-col gap-6 w-full">
  <x-auth-header :title="__('Create an account')" :description="__('Enter your details below to create your account')" />

  <!-- Session Status -->
  <x-auth-session-status class="text-center" :status="session('status')" />

  <form method="POST" wire:submit="register" class="flex flex-col gap-6">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
      <!-- Name -->
      <flux:input wire:model="name" :label="__('Name')" type="text" required autofocus autocomplete="name"
        :placeholder="__('Full name')" />

      <!-- Email Address -->
      <flux:input wire:model="email" :label="__('Email address')" type="email" required autocomplete="email"
        placeholder="email@example.com" />

      <!-- Password -->
      <flux:input wire:model="password" :label="__('Password')" type="password" required autocomplete="new-password"
        :placeholder="__('Password')" viewable />

      <!-- Confirm Password -->
      <flux:input wire:model="password_confirmation" :label="__('Confirm password')" type="password" required
        autocomplete="new-password" :placeholder="__('Confirm password')" viewable />
    </div>

    <div class="flex flex-col gap-4">
      <flux:heading size="lg" level="1">Company Information</flux:heading>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        <!-- Company Name -->
        <flux:input wire:model="company_name" :label="__('Company Name')" type="text" required
          :placeholder="__('Your Company Name')" />

        <!-- Tax ID -->
        <flux:input wire:model="tax_number" :label="__('Company Tax ID')" type="text" required
          :placeholder="__('Your Company Tax ID')" />

        <!-- Company Email -->
        {{-- <flux:input wire:model="contact_email" :label="__('Company Email')" type="email" required
          :placeholder="__('Your Company Email')" /> --}}

        <!-- Company Phone -->
        <flux:input wire:model="contact_phone" :label="__('Company Phone')" type="text" required
          :placeholder="__('Your Company Phone')" />

        <!-- Contact Person -->
        {{-- <flux:input wire:model="contact_name" :label="__('Contact Person')" type="text" required
          :placeholder="__('Contact Person Name')" /> --}}
      </div>

      <!-- Billing Address -->
      <flux:textarea wire:model="billing_address" :label="__('Billing Address')" type="text" required
        :placeholder="__('Your Billing Address')" />
    </div>

    <!-- Terms of Service Acceptance -->
    <div class="flex items-start space-x-3">
      <flux:checkbox wire:model="accept_tos" id="accept_tos" required />
      <label for="accept_tos" class="text-sm text-zinc-600 dark:text-zinc-400">
        I agree to the
        <flux:modal.trigger name="terms-and-conditions">
          <a href="javascript:void(0)" class="text-blue-700 hover:text-blue-500 hover:underline">Terms of Service</a>
        </flux:modal.trigger>
        and Privacy Policy.
      </label>
    </div>

    <div class="flex items-center justify-center">
      <flux:button type="submit" variant="primary">
        {{ __('Create account') }}
      </flux:button>
    </div>
  </form>

  <div class="space-x-1 rtl:space-x-reverse text-center text-sm text-zinc-600 dark:text-zinc-400">
    <span>{{ __('Already have an account?') }}</span>
    <flux:link :href="route('login')" wire:navigate>{{ __('Log in') }}</flux:link>
  </div>

  {{-- <div class="text-xs text-center text-zinc-600 dark:text-zinc-400">
    Read our
    <flux:modal.trigger name="terms-and-conditions">
      <a href="javascript:void(0)" class="text-blue-700 hover:text-blue-500 hover:underline">Terms of Service</a>.
    </flux:modal.trigger>
  </div> --}}

  @include('partials.tos.terms-and-conditions')
</div>
