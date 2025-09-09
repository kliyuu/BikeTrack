<flux:header container sticky class="bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
  <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

  <flux:brand href="#" name="BikeTrack">
    <x-slot name="logo">
      <div class="size-10 rounded shrink-0 bg-accent text-accent-foreground flex items-center justify-center">
        <i class="font-serif font-bold">BT</i>
      </div>
    </x-slot>
  </flux:brand>

  <flux:navbar class="-mb-px max-lg:hidden">
    <flux:navbar.item icon="home" href="{{ route('shop') }}" :current="request()->routeIs('shop')">
      Home
    </flux:navbar.item>
    <flux:navbar.item icon="archive-box" href="{{ route('shop.catalog') }}"
      :current="request()->routeIs('shop.catalog')">
      Catalog
    </flux:navbar.item>
  </flux:navbar>

  <flux:spacer />

  {{-- <flux:dropdown x-data align="end">
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
  </flux:dropdown> --}}
  @if (Auth::check() && Auth::user()->hasAnyRole(['admin', 'staff']))
    <flux:button variant="primary" size="sm" color="blue" href="{{ route('admin.dashboard') }}">
      Dashboard
    </flux:button>
  @elseif(Auth::check() && Auth::user()->hasAnyRole(['client']))
    <livewire:shop.cart-icon />

    <flux:dropdown position="top" align="center">
      <flux:profile avatar="https://fluxui.dev/img/demo/user.png" />

      <flux:menu>
        <flux:menu.radio.group>
          <flux:menu.radio checked>{{ Auth::user()->name }}</flux:menu.radio>
        </flux:menu.radio.group>

        <flux:menu.separator />

        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="cursor-pointer">
            Logout</flux:menu.item>
        </form>
      </flux:menu>
    </flux:dropdown>
  @else
    <flux:button variant="primary" size="sm" color="blue" href="{{ route('login') }}">
      Login
    </flux:button>
  @endif
</flux:header>

<flux:sidebar stashable sticky
  class="lg:hidden bg-zinc-50 dark:bg-zinc-900 border rtl:border-r-0 rtl:border-l border-zinc-200 dark:border-zinc-700">
  <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

  <flux:brand href="#" logo="https://fluxui.dev/img/demo/logo.png" name="Acme Inc." class="px-2 dark:hidden" />
  <flux:brand href="#" logo="https://fluxui.dev/img/demo/dark-mode-logo.png" name="Acme Inc."
    class="px-2 hidden dark:flex" />

  <flux:navlist variant="outline">
    <flux:navlist.item icon="home" href="{{ route('shop') }}" :current="request()->routeIs('shop')">
      Home
    </flux:navlist.item>
  </flux:navlist>

  <flux:spacer />

  <flux:navlist variant="outline">
    <flux:navlist.item icon="cog-6-tooth" href="#">Settings</flux:navlist.item>
    <flux:navlist.item icon="information-circle" href="#">Help</flux:navlist.item>
  </flux:navlist>
</flux:sidebar>
