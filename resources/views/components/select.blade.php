<div class="relative w-full max-w-xl flex-col gap-1 text-on-surface dark:text-on-surface-dark">
  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"
    class="absolute pointer-events-none right-2 top-2 size-5">
    <path fill-rule="evenodd"
      d="M5.22 8.22a.75.75 0 0 1 1.06 0L10 11.94l3.72-3.72a.75.75 0 1 1 1.06 1.06l-4.25 4.25a.75.75 0 0 1-1.06 0L5.22 9.28a.75.75 0 0 1 0-1.06Z"
      clip-rule="evenodd" />
  </svg>
  <select {{ $attributes->merge(['class' => 'w-full appearance-none px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm']) }}>
    {{ $slot }}
  </select>
</div>
