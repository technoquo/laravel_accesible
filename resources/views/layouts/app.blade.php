<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="@yield('description', __('ui.meta_description'))">
    <title>@yield('title', __('ui.site_name'))</title>

    @isset($canonicalUrl)
        <link rel="canonical" href="{{ $canonicalUrl }}">
    @endisset

    @isset($hreflangUrls)
        @foreach ($hreflangUrls as $lang => $url)
            <link rel="alternate" hreflang="{{ $lang }}" href="{{ $url }}">
        @endforeach
        @if (isset($hreflangUrls['es'], $hreflangUrls['en']))
            <link rel="alternate" hreflang="x-default" href="{{ $hreflangUrls['es'] }}">
        @endif
    @endisset

    <meta property="og:site_name" content="{{ __('ui.site_name') }}">
    <meta property="og:locale" content="{{ app()->getLocale() === 'es' ? 'es_ES' : 'en_US' }}">
    <meta property="og:type" content="@yield('og_type', 'website')">
    <meta property="og:title" content="@yield('title', __('ui.site_name'))">
    <meta property="og:description" content="@yield('description', __('ui.meta_description'))">
    @isset($canonicalUrl)
        <meta property="og:url" content="{{ $canonicalUrl }}">
    @endisset
    @stack('og')

    <x-site-icons />
    <x-theme-init />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body class="min-h-full flex flex-col bg-white text-gray-900 antialiased">

    {{-- Skip link: hidden off-screen, visible on focus --}}
    <a href="#main-content"
       class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-white focus:text-gray-900 focus:font-semibold focus:underline focus:rounded focus:shadow-lg focus:ring-2 focus:ring-gray-900">
        {{ __('ui.skip_to_content') }}
    </a>

    <header role="banner" class="border-b border-gray-200">
        {{-- Below lg the menu collapses behind a disclosure button; theme and accessibility tools stay visible --}}
        <div data-site-header class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-4 flex flex-wrap items-center gap-x-2 gap-y-3 lg:gap-x-8">

            <a href="{{ route('home', ['locale' => app()->getLocale()]) }}"
               class="mr-auto lg:mr-0 rounded-[0.6rem]">
                {{-- The logotype reads "Laravel Accesible" in Spanish in every locale, so the name matches it (WCAG 2.5.3) --}}
                <x-logo class="h-7 sm:h-9" />
                <span class="sr-only"><span lang="es">Laravel Accesible</span>, {{ __('ui.nav_home') }}</span>
            </a>

            <button type="button"
                    data-site-menu-toggle
                    aria-expanded="false"
                    aria-controls="site-menu"
                    class="group lg:hidden inline-flex items-center gap-2 rounded p-2 text-sm font-medium text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                {{-- Bars when closed, cross when open --}}
                <svg class="size-5 group-aria-expanded:hidden" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 6h16M4 12h16M4 18h16"/>
                </svg>
                <svg class="hidden size-5 group-aria-expanded:block" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 6l12 12M18 6L6 18"/>
                </svg>
                <span class="sr-only sm:not-sr-only">{{ __('ui.nav_menu') }}</span>
            </button>

            {{-- Below lg: drawer sliding in from the right, starting under the header (top set in resources/js/app.js) --}}
            <div id="site-menu"
                 data-site-menu
                 class="flex flex-col gap-6 text-base
                        max-lg:fixed max-lg:right-0 max-lg:bottom-0 max-lg:top-(--site-menu-top) max-lg:z-30
                        max-lg:w-72 max-lg:max-w-[85vw] max-lg:overflow-y-auto
                        max-lg:border-l max-lg:border-gray-200 max-lg:bg-white max-lg:p-6 max-lg:shadow-xl
                        max-lg:invisible max-lg:translate-x-full
                        max-lg:transition-[translate,visibility] max-lg:duration-300 max-lg:ease-out motion-reduce:transition-none
                        max-lg:data-[open]:visible max-lg:data-[open]:translate-x-0
                        lg:flex-1 lg:flex-row lg:items-center lg:justify-between lg:gap-4 lg:text-sm">
                <nav aria-label="{{ __('ui.nav_main') }}" class="flex flex-col lg:flex-row lg:items-center gap-1 lg:gap-6">
                    <a href="{{ locale_route('articles.index') }}"
                       class="py-2 lg:py-0 hover:underline underline-offset-2
                              {{ request()->routeIs('*.articles.*') ? 'font-semibold underline decoration-brand-orange decoration-2' : '' }}">
                        {{ __('ui.nav_articles') }}
                    </a>
                    <a href="{{ locale_route('about') }}"
                       @if (request()->routeIs('*.about')) aria-current="page" @endif
                       class="py-2 lg:py-0 hover:underline underline-offset-2
                              {{ request()->routeIs('*.about') ? 'font-semibold underline decoration-brand-orange decoration-2' : '' }}">
                        {{ __('ui.nav_about') }}
                    </a>
                    <a href="{{ locale_route('contact') }}"
                       @if (request()->routeIs('*.contact')) aria-current="page" @endif
                       class="py-2 lg:py-0 hover:underline underline-offset-2
                              {{ request()->routeIs('*.contact') ? 'font-semibold underline decoration-brand-orange decoration-2' : '' }}">
                        {{ __('ui.nav_contact') }}
                    </a>
                </nav>

                <nav aria-label="{{ __('ui.lang_selector') }}" class="flex items-center gap-4 lg:gap-3 py-2 lg:py-0">
                    @foreach (['es' => __('ui.lang_es'), 'en' => __('ui.lang_en')] as $lang => $label)
                        @if (app()->getLocale() === $lang)
                            <span lang="{{ $lang }}"
                                  aria-current="page"
                                  class="font-semibold underline decoration-brand-orange decoration-2 underline-offset-2">
                                {{ $label }}
                            </span>
                        @else
                            <a href="{{ ($alternateUrls ?? [])[$lang] ?? route('home', ['locale' => $lang]) }}"
                               lang="{{ $lang }}"
                               hreflang="{{ $lang }}"
                               class="hover:underline underline-offset-2">
                                {{ $label }}
                            </a>
                        @endif
                    @endforeach
                </nav>
            </div>

            <div class="flex items-center gap-1">
                <x-theme-toggle />
                <x-accessibility-tools />
            </div>

        </div>
    </header>

    <main id="main-content" tabindex="-1" class="flex-1 outline-none">
        @yield('content')
    </main>

    <footer role="contentinfo" class="border-t border-gray-200 mt-auto">
        <div class="mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-6 text-sm text-gray-600 flex flex-wrap items-center justify-between gap-4">
            <p>{{ __('ui.footer_rights', ['year' => date('Y')]) }}</p>
            <div class="flex flex-wrap items-center gap-4">
                <a href="{{ locale_route('about') }}" class="underline underline-offset-2 hover:text-gray-900">
                    {{ __('ui.nav_about') }}
                </a>
                <a href="{{ locale_route('contact') }}" class="underline underline-offset-2 hover:text-gray-900">
                    {{ __('ui.nav_contact') }}
                </a>
            </div>
        </div>
    </footer>

    @livewireScripts
    @stack('scripts')
</body>
</html>
