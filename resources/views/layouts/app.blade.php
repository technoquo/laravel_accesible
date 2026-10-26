<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('description', __('ui.meta_description'))">
    <title>@yield('title', __('ui.site_name'))</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-full flex flex-col bg-white text-gray-900 antialiased">

    {{-- Skip link: hidden off-screen, visible on focus --}}
    <a href="#main-content"
       class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-white focus:text-gray-900 focus:font-semibold focus:underline focus:rounded focus:shadow-lg focus:ring-2 focus:ring-gray-900">
        {{ __('ui.skip_to_content') }}
    </a>

    <header role="banner" class="border-b border-gray-200">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between gap-4">

            <a href="{{ route('home', ['locale' => app()->getLocale()]) }}"
               class="text-lg font-semibold tracking-tight hover:underline">
                {{ __('ui.site_name') }}
            </a>

            <nav aria-label="{{ __('ui.lang_selector') }}" class="flex items-center gap-3 text-sm">
                @foreach (['es' => __('ui.lang_es'), 'en' => __('ui.lang_en')] as $locale => $label)
                    @if (app()->getLocale() === $locale)
                        <span lang="{{ $locale }}"
                              aria-current="page"
                              class="font-semibold underline underline-offset-2">
                            {{ $label }}
                        </span>
                    @else
                        <a href="{{ route('home', ['locale' => $locale]) }}"
                           lang="{{ $locale }}"
                           hreflang="{{ $locale }}"
                           class="hover:underline underline-offset-2">
                            {{ $label }}
                        </a>
                    @endif
                @endforeach
            </nav>

        </div>
    </header>

    <main id="main-content" tabindex="-1" class="flex-1 outline-none">
        @yield('content')
    </main>

    <footer role="contentinfo" class="border-t border-gray-200 mt-auto">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-6 text-sm text-gray-600">
            {{ __('ui.footer_rights', ['year' => date('Y')]) }}
        </div>
    </footer>

    @livewireScripts
</body>
</html>
