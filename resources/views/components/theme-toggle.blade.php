{{-- Light/dark toggle: aria-pressed reflects dark mode; behaviour lives in resources/js/app.js --}}
<button type="button"
        data-theme-toggle
        aria-pressed="false"
        aria-label="{{ __('ui.theme_dark_mode') }}"
        {{ $attributes->merge(['class' => 'inline-flex items-center justify-center rounded p-2 text-gray-700 hover:bg-gray-100 hover:text-gray-900']) }}>
    {{-- Moon: shown in light mode --}}
    <svg class="size-5 dark:hidden" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
    </svg>
    {{-- Sun: shown in dark mode --}}
    <svg class="hidden size-5 dark:block" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <circle cx="12" cy="12" r="4"/>
        <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M4.93 19.07l1.41-1.41M17.66 6.34l1.41-1.41"/>
    </svg>
</button>
