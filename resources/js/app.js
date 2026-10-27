/**
 * Light/dark theme toggle.
 * The initial class is set inline in <head> (components/theme-init.blade.php) to avoid a flash;
 * this module keeps the toggle buttons in sync and persists the visitor's explicit choice.
 */
const themeStorageKey = 'theme';
const root = document.documentElement;
const systemDarkQuery = window.matchMedia('(prefers-color-scheme: dark)');

function hasStoredTheme() {
    try {
        return localStorage.getItem(themeStorageKey) !== null;
    } catch {
        return false;
    }
}

function applyTheme(isDark) {
    root.classList.toggle('dark', isDark);

    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
        button.setAttribute('aria-pressed', String(isDark));
    });
}

function storeTheme(isDark) {
    try {
        localStorage.setItem(themeStorageKey, isDark ? 'dark' : 'light');
    } catch {
        // Storage may be unavailable (private mode); the choice then lasts for this page only.
    }
}

document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
    button.addEventListener('click', () => {
        const isDark = !root.classList.contains('dark');

        applyTheme(isDark);
        storeTheme(isDark);
    });
});

systemDarkQuery.addEventListener('change', (event) => {
    if (!hasStoredTheme()) {
        applyTheme(event.matches);
    }
});

applyTheme(root.classList.contains('dark'));
