<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
  @include('partials.head')
</head>

<body class="min-h-screen bg-white antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
  <div
    class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0">
    <div class="bg-muted relative hidden h-full flex-col p-10 text-white lg:flex dark:border-e dark:border-neutral-800">
      <div class="absolute inset-0 bg-gray-100">
        <img src="{{ asset('images/hero.jpg') }}" alt="Background Image"
          class="absolute inset-0 h-full w-full object-cover opacity-30" />
      </div>
      <a href="{{ route('shop') }}" class="relative z-20 flex items-center text-lg font-medium" wire:navigate>
        <span class="flex h-10 w-10 items-center justify-center rounded-md">
          {{-- <x-app-logo-icon class="me-2 h-7 fill-current text-blue-600" /> --}}
          <x-app-logo />
        </span>
        <span class="text-blue-600">
          {{ config('app.name', 'Laravel') }}
        </span>
      </a>

      <div class="relative flex items-center justify-center mt-20">
        <span class="flex size-40 items-center justify-center rounded-md">
          {{-- <x-app-logo-icon class="me-2 h-7 fill-current text-blue-600" /> --}}
          <img src="{{ asset('images/bikerzone.jpg') }}" alt="BikeTrack Logo" class="rounded-full" />
        </span>
      </div>

      @php
        [$message, $author] = str(Illuminate\Foundation\Inspiring::quotes()->random())->explode('-');
      @endphp

      <div class="relative z-20 mt-auto">
        <blockquote class="space-y-2">
          <flux:heading size="lg">&ldquo;{{ trim($message) }}&rdquo;</flux:heading>
          <footer>
            <flux:heading>{{ trim($author) }}</flux:heading>
          </footer>
        </blockquote>
      </div>
    </div>
    <div class="w-full lg:p-8">
      <div class="mx-auto flex w-full flex-col justify-center items-center space-y-6 sm:py-6">
        <a href="{{ route('shop') }}" class="z-20 flex flex-col items-center gap-2 font-medium lg:hidden" wire:navigate>
          <span class="flex h-9 w-9 items-center justify-center rounded-md">
            {{-- <x-app-logo-icon class="size-9 fill-current text-black dark:text-white" /> --}}
            <img src="{{ asset('images/bikerzone.jpg') }}" alt="BikeTrack Logo" class="size-9 rounded-full" />
          </span>

          <span class="sr-only">{{ config('app.name', 'Laravel') }}</span>
        </a>
        {{ $slot }}
      </div>
    </div>
  </div>
  @fluxScripts
</body>

</html>
