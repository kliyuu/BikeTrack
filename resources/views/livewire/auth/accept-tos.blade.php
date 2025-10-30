<div class="flex flex-col gap-6 sm:w-[500px]">
  <x-auth-header :title="__('Accept Terms of Service')" :description="__('Please read and accept our Terms of Service to continue using BikeTrack')" />

  <!-- Session Status -->
  <x-auth-session-status class="text-center" :status="session('status')" />

  <!-- Session Errors -->
  @if (session('error'))
    <div class="text-red-600 text-sm text-center">
      {{ session('error') }}
    </div>
  @endif

  <div class="flex flex-col gap-6">
    <!-- Terms of Service Content -->
    <div
      class="max-h-96 overflow-y-auto border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 bg-zinc-50 dark:bg-zinc-900">
      @include('partials.tos.terms-and-conditions-content')
    </div>

    <form wire:submit.prevent="acceptTerms" class="space-y-6">
      <!-- Terms of Service Acceptance -->
      <div class="flex items-start space-x-3">
        <flux:checkbox wire:model="accept_tos" id="accept_tos" />
        <flux:label>
          I have read and agree to the Terms of Service and Privacy Policy.
        </flux:label>

        <flux:error name="accept_tos" />
      </div>

      <div class="flex items-center justify-center">
        <flux:button type="submit" variant="primary">
          Accept and Continue
        </flux:button>
      </div>
    </form>

    <!-- Separate logout form -->
    <div class="flex items-center justify-center">
      <form method="POST" action="{{ route('logout') }}">
        @csrf
        <flux:button type="submit" variant="ghost">
          Logout
        </flux:button>
      </form>
    </div>
  </div>
</div>
