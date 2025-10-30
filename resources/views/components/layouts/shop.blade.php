<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />

  <title>{{ $title ?? null }} | {{ config('app.name') }}</title>

  <link rel="icon" href="/images/bikerzone.jpg" type="image/jpg">
  <link rel="apple-touch-icon" href="/apple-touch-icon.png">

  <link rel="preconnect" href="https://fonts.bunny.net">
  <link href="https://fonts.bunny.net/css?family=inter:400,500,600&display=swap" rel="stylesheet" />
  {{-- <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" /> --}}

  <link href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" rel="stylesheet" />

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  @fluxAppearance
</head>

<body class="relative min-h-screen bg-gray-50 dark:bg-zinc-800">
  @include('partials.shop.header')

  <flux:container class="relative !py-0">
    {{ $slot }}
  </flux:container>

  @if (!Route::is('shop.checkout'))
    <livewire:shop.cart />
    @if (Auth::check() && Auth::user()->cartItems()->count() > 0)
      <x-cart-button />
    @endif
  @endif


  @include('partials.shop.footer')
  @fluxScripts
</body>

</html>
