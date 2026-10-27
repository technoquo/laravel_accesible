{{-- Accessibility tools: text zoom and colour modes in a disclosure panel; behaviour lives in resources/js/app.js --}}
@php
    $colorModes = [
        'default' => __('ui.a11y_color_default'),
        'invert-brightness' => __('ui.a11y_color_invert_brightness'),
        'invert-colors' => __('ui.a11y_color_invert_colors'),
        'yellow-black' => __('ui.a11y_color_yellow_black'),
        'blue-dye' => __('ui.a11y_color_blue_dye'),
        'grayscale' => __('ui.a11y_color_grayscale'),
        'white-black' => __('ui.a11y_color_white_black'),
    ];
@endphp
<div data-a11y-tools class="relative">
    <button type="button"
            data-a11y-tools-toggle
            aria-expanded="false"
            aria-controls="a11y-tools-panel"
            class="inline-flex items-center gap-2 rounded p-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
        <svg class="size-5" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="12" cy="4" r="2"/>
            <path d="M4 8l8 2 8-2M12 10v5M9 21l3-6 3 6"/>
        </svg>
        <span class="sr-only sm:not-sr-only">{{ __('ui.a11y_tools') }}</span>
    </button>

    <div id="a11y-tools-panel"
         data-a11y-tools-panel
         hidden
         class="absolute right-0 top-full z-40 mt-2 w-72 max-w-[calc(100vw-2rem)] rounded border border-gray-300 bg-white p-4 text-sm text-gray-900 shadow-lg">

        <div role="group" aria-labelledby="a11y-zoom-label">
            <p id="a11y-zoom-label" class="font-semibold">{{ __('ui.a11y_text_zoom') }}</p>
            <div class="mt-2 flex items-center gap-2">
                <button type="button" data-a11y-zoom="decrease"
                        class="rounded border border-gray-300 px-3 py-1 font-semibold hover:bg-gray-100 aria-disabled:opacity-60">
                    <span aria-hidden="true">A−</span>
                    <span class="sr-only">{{ __('ui.a11y_zoom_decrease') }}</span>
                </button>
                <button type="button" data-a11y-zoom="reset"
                        class="rounded border border-gray-300 px-3 py-1 hover:bg-gray-100">
                    {{ __('ui.a11y_zoom_reset') }}
                </button>
                <button type="button" data-a11y-zoom="increase"
                        class="rounded border border-gray-300 px-3 py-1 text-base font-semibold hover:bg-gray-100 aria-disabled:opacity-60">
                    <span aria-hidden="true">A+</span>
                    <span class="sr-only">{{ __('ui.a11y_zoom_increase') }}</span>
                </button>
            </div>
            <p class="mt-2 text-gray-700" aria-live="polite" aria-atomic="true">
                {{ __('ui.a11y_zoom_level') }} <output data-a11y-zoom-value>100%</output>
            </p>
        </div>

        <fieldset class="mt-4">
            <legend class="font-semibold">{{ __('ui.a11y_colors') }}</legend>
            <div class="mt-2 space-y-1">
                @foreach ($colorModes as $mode => $label)
                    <label class="flex cursor-pointer items-center gap-2 rounded px-1 py-1 hover:bg-gray-100">
                        <input type="radio" name="a11y-color-mode" value="{{ $mode }}" data-a11y-color-mode
                               @checked($mode === 'default')
                               class="size-4 accent-current">
                        {{ $label }}
                    </label>
                @endforeach
            </div>
        </fieldset>
    </div>
</div>
