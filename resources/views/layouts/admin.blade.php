<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Panel') — Laravel Accesible</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-full flex flex-col bg-gray-50 text-gray-900 antialiased">

    <a href="#main-content"
       class="sr-only focus:not-sr-only focus:fixed focus:top-4 focus:left-4 focus:z-50 focus:px-4 focus:py-2 focus:bg-white focus:text-gray-900 focus:font-semibold focus:underline focus:rounded focus:shadow-lg focus:ring-2 focus:ring-gray-900">
        Ir al contenido principal
    </a>

    @auth
        <header role="banner" class="bg-white border-b border-gray-200">
            <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between gap-4">
                <nav aria-label="Navegación del panel" class="flex items-center gap-6 text-sm font-medium">
                    <a href="{{ route('admin.posts.index') }}"
                       class="hover:underline focus:outline-none focus:ring-2 focus:ring-gray-900 rounded">
                        Posts
                    </a>
                    <a href="{{ route('admin.categories.index') }}"
                       class="hover:underline focus:outline-none focus:ring-2 focus:ring-gray-900 rounded">
                        Categorías
                    </a>
                    <a href="{{ route('admin.tags.index') }}"
                       class="hover:underline focus:outline-none focus:ring-2 focus:ring-gray-900 rounded">
                        Etiquetas
                    </a>
                </nav>
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button type="submit"
                        class="text-sm text-gray-600 hover:text-gray-900 hover:underline focus:outline-none focus:ring-2 focus:ring-gray-900 rounded">
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </header>
    @endauth

    <main id="main-content" tabindex="-1" class="flex-1 outline-none py-8">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>

    @livewireScripts
</body>
</html>
