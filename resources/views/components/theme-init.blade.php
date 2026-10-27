{{-- Applies the saved (or system) colour scheme before first paint to avoid a flash of the wrong theme --}}
<script>
    (function () {
        var root = document.documentElement;
        var storedTheme = null;

        try {
            storedTheme = localStorage.getItem('theme');
        } catch (error) {}

        var prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

        root.classList.add('js');
        root.classList.toggle('dark', storedTheme ? storedTheme === 'dark' : prefersDark);
    })();
</script>
