{{-- Applies the saved (or system) colour scheme before first paint to avoid a flash of the wrong theme --}}
<script>
    (function () {
        var root = document.documentElement;
        var storedTheme = null;
        var storedTextZoom = null;
        var storedColorMode = null;

        try {
            storedTheme = localStorage.getItem('theme');
            storedTextZoom = localStorage.getItem('textZoom');
            storedColorMode = localStorage.getItem('colorMode');
        } catch (error) {}

        var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

        root.classList.add('js');
        root.classList.toggle('dark', storedTheme ? storedTheme === 'dark' : prefersDark);

        if (storedTextZoom) {
            root.setAttribute('data-text-zoom', storedTextZoom);
        }

        if (storedColorMode) {
            root.setAttribute('data-color-mode', storedColorMode);
        }
    })();
</script>
