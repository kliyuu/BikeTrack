<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
  @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
  <flux:header container sticky class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />
    {{-- <flux:brand href="#" logo="https://fluxui.dev/img/demo/logo.png" name="Acme Inc."
      class="max-lg:hidden dark:hidden" />
    <flux:brand href="#" logo="https://fluxui.dev/img/demo/dark-mode-logo.png" name="Acme Inc."
      class="max-lg:hidden! hidden dark:flex" /> --}}

    <flux:spacer />

    <flux:navbar>
      <flux:dropdown x-data align="end">
        <flux:button variant="subtle" square class="group" aria-label="Preferred color scheme">
          <flux:icon.sun x-show="$flux.appearance === 'light'" variant="mini" class="text-zinc-500 dark:text-white" />
          <flux:icon.moon x-show="$flux.appearance === 'dark'" variant="mini" class="text-zinc-500 dark:text-white" />
          <flux:icon.moon x-show="$flux.appearance === 'system' && $flux.dark" variant="mini" />
          <flux:icon.sun x-show="$flux.appearance === 'system' && ! $flux.dark" variant="mini" />
        </flux:button>
        <flux:menu>
          <flux:menu.item icon="sun" x-on:click="$flux.appearance = 'light'">Light</flux:menu.item>
          <flux:menu.item icon="moon" x-on:click="$flux.appearance = 'dark'">Dark</flux:menu.item>
          <flux:menu.item icon="computer-desktop" x-on:click="$flux.appearance = 'system'">System</flux:menu.item>
        </flux:menu>
      </flux:dropdown>
    </flux:navbar>

    <livewire:notifications.admin />

    <flux:dropdown position="top" align="start">
      <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />
      <flux:menu>
        <flux:menu.radio.group>
          <div class="p-0 text-sm font-normal">
            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
              <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                <span
                  class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                  {{ auth()->user()->initials() }}
                </span>
              </span>

              <div class="grid flex-1 text-start text-sm leading-tight">
                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
              </div>
            </div>
          </div>
        </flux:menu.radio.group>

        <flux:menu.separator />

        <flux:menu.radio.group>
          <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}
          </flux:menu.item>
        </flux:menu.radio.group>

        <flux:menu.separator />

        <form method="POST" action="{{ route('logout') }}" class="w-full">
          @csrf
          <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
            {{ __('Log Out') }}
          </flux:menu.item>
        </form>
      </flux:menu>
    </flux:dropdown>
  </flux:header>

  <flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
    <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

    <a href="{{ route('shop.catalog') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate>
      <x-app-logo />
    </a>

    <flux:navlist variant="outline" class="mt-4">
      {{-- Dashboard --}}
      <flux:navlist.item icon="squares-2x2" :href="route('admin.dashboard')"
        :current="request()->routeIs('admin.dashboard')" wire:navigate>{{ __('Dashboard') }}</flux:navlist.item>

      {{-- Inventory --}}
      <flux:navlist.group :heading="__('INVENTORY')" class="grid">
        <flux:navlist.item icon="cube" :href="route('admin.products')"
          :current="request()->routeIs('admin.products')" wire:navigate>{{ __('Products') }}</flux:navlist.item>
        <flux:navlist.item icon="tag" :href="route('admin.brands')" :current="request()->routeIs('admin.brands')"
          wire:navigate>{{ __('Brands') }}</flux:navlist.item>
        <flux:navlist.item icon="rectangle-group" :href="route('admin.categories')"
          :current="request()->routeIs('admin.categories')" wire:navigate>{{ __('Categories') }}</flux:navlist.item>
        <flux:navlist.item icon="building-storefront" :href="route('admin.warehouses')"
          :current="request()->routeIs('admin.warehouses')" wire:navigate>{{ __('Warehouses') }}</flux:navlist.item>
        {{-- <flux:navlist.item icon="chart-bar" :href="route('admin.stock-levels')"
          :current="request()->routeIs('admin.stock-levels')" wire:navigate>{{ __('Stock Levels') }}</flux:navlist.item> --}}
        <flux:navlist.item icon="clock" :href="route('admin.restock-history')"
          :current="request()->routeIs('admin.restock-history')" wire:navigate>{{ __('Restock History') }}
        </flux:navlist.item>
      </flux:navlist.group>

      {{-- Orders --}}
      <flux:navlist.group :heading="__('ORDERS')" class="grid">
        <flux:navlist.item icon="shopping-cart" :href="route('admin.orders')"
          :current="request()->routeIs('admin.orders')" wire:navigate>{{ __('All Orders') }}</flux:navlist.item>
        <flux:navlist.item icon="arrow-path-rounded-square" :href="route('admin.returns')"
          :current="request()->routeIs('admin.returns')" wire:navigate>{{ __('Returns') }}</flux:navlist.item>
      </flux:navlist.group>

      {{-- Reports --}}
      <flux:navlist.group :heading="__('REPORTS')" class="grid">
        <flux:navlist.item icon="presentation-chart-line" :href="route('admin.sales-reports')"
          :current="request()->routeIs('admin.sales-reports')" wire:navigate>{{ __('Sales Reports') }}
        </flux:navlist.item>
        <flux:navlist.item icon="rectangle-stack" :href="route('admin.inventory-reports')"
          :current="request()->routeIs('admin.inventory-reports')" wire:navigate>{{ __('Inventory Reports') }}
        </flux:navlist.item>
      </flux:navlist.group>

      {{-- User Management --}}
      <flux:navlist.group :heading="__('USER MANAGEMENT')" class="grid">
        <flux:navlist.item icon="users" :href="route('admin.users')" :current="request()->routeIs('admin.users')"
          wire:navigate>{{ __('Users') }}</flux:navlist.item>
        <flux:navlist.item icon="user-group" :href="route('admin.clients')"
          :current="request()->routeIs('admin.clients')" wire:navigate>{{ __('B2B Clients') }}</flux:navlist.item>
      </flux:navlist.group>
    </flux:navlist>

    <flux:spacer />

    {{-- <flux:navlist variant="outline">
                <flux:navlist.item icon="folder-git-2" href="https://github.com/laravel/livewire-starter-kit" target="_blank">
                {{ __('Repository') }}
                </flux:navlist.item>

                <flux:navlist.item icon="book-open-text" href="https://laravel.com/docs/starter-kits#livewire" target="_blank">
                {{ __('Documentation') }}
                </flux:navlist.item>
            </flux:navlist> --}}

    <!-- Desktop User Menu -->
    <flux:dropdown class="hidden lg:block" position="bottom" align="start">
      <flux:profile :name="auth()->user()->name" :initials="auth()->user()->initials()"
        icon:trailing="chevrons-up-down" />

      <flux:menu class="w-[220px]">
        <flux:menu.radio.group>
          <div class="p-0 text-sm font-normal">
            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
              <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                <span
                  class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                  {{ auth()->user()->initials() }}
                </span>
              </span>

              <div class="grid flex-1 text-start text-sm leading-tight">
                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
              </div>
            </div>
          </div>
        </flux:menu.radio.group>

        <flux:menu.separator />

        <flux:menu.radio.group>
          <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}
          </flux:menu.item>
        </flux:menu.radio.group>

        <flux:menu.separator />

        <form method="POST" action="{{ route('logout') }}" class="w-full">
          @csrf
          <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
            {{ __('Log Out') }}
          </flux:menu.item>
        </form>
      </flux:menu>
    </flux:dropdown>
  </flux:sidebar>

  <!-- Mobile User Menu -->
  <flux:header class="lg:hidden">
    <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

    <flux:spacer />

    <flux:dropdown position="top" align="end">
      <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

      <flux:menu>
        <flux:menu.radio.group>
          <div class="p-0 text-sm font-normal">
            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
              <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                <span
                  class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                  {{ auth()->user()->initials() }}
                </span>
              </span>

              <div class="grid flex-1 text-start text-sm leading-tight">
                <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                <span class="truncate text-xs">{{ auth()->user()->email }}</span>
              </div>
            </div>
          </div>
        </flux:menu.radio.group>

        <flux:menu.separator />

        <flux:menu.radio.group>
          <flux:menu.item :href="route('settings.profile')" icon="cog" wire:navigate>{{ __('Settings') }}
          </flux:menu.item>
        </flux:menu.radio.group>

        <flux:menu.separator />

        <form method="POST" action="{{ route('logout') }}" class="w-full">
          @csrf
          <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
            class="w-full cursor-pointer">
            {{ __('Log Out') }}
          </flux:menu.item>
        </form>
      </flux:menu>
    </flux:dropdown>
  </flux:header>

  {{ $slot }}

  @fluxScripts
  @stack('scripts')
</body>

</html>
