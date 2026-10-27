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

/**
 * Accessibility tools: text zoom and colour modes (components/accessibility-tools.blade.php).
 * Like the theme, the saved choices are applied in <head> by theme-init; this module drives the
 * disclosure panel, updates the root attributes and persists the visitor's choices.
 */
const textZoomStorageKey = 'textZoom';
const colorModeStorageKey = 'colorMode';
const textZoomLevels = [100, 125, 150, 175, 200];
const defaultColorMode = 'default';

function storePreference(key, value, defaultValue) {
    try {
        if (value === defaultValue) {
            localStorage.removeItem(key);
        } else {
            localStorage.setItem(key, value);
        }
    } catch {
        // Storage may be unavailable (private mode); the choice then lasts for this page only.
    }
}

function currentTextZoom() {
    const textZoom = Number(root.getAttribute('data-text-zoom'));

    return textZoomLevels.includes(textZoom) ? textZoom : textZoomLevels[0];
}

function applyTextZoom(textZoom) {
    if (textZoom === textZoomLevels[0]) {
        root.removeAttribute('data-text-zoom');
    } else {
        root.setAttribute('data-text-zoom', String(textZoom));
    }

    const levelIndex = textZoomLevels.indexOf(textZoom);

    document.querySelectorAll('[data-a11y-tools]').forEach((tools) => {
        tools.querySelector('[data-a11y-zoom-value]').textContent = `${textZoom}%`;
        tools.querySelector('[data-a11y-zoom="decrease"]').setAttribute('aria-disabled', String(levelIndex === 0));
        tools.querySelector('[data-a11y-zoom="increase"]')
            .setAttribute('aria-disabled', String(levelIndex === textZoomLevels.length - 1));
    });
}

function applyColorMode(colorMode) {
    if (colorMode === defaultColorMode) {
        root.removeAttribute('data-color-mode');
    } else {
        root.setAttribute('data-color-mode', colorMode);
    }

    document.querySelectorAll('[data-a11y-color-mode]').forEach((radio) => {
        radio.checked = radio.value === colorMode;
    });
}

function setPanelOpen(tools, isOpen) {
    tools.querySelector('[data-a11y-tools-toggle]').setAttribute('aria-expanded', String(isOpen));
    tools.querySelector('[data-a11y-tools-panel]').hidden = !isOpen;
}

document.querySelectorAll('[data-a11y-tools]').forEach((tools) => {
    const toggle = tools.querySelector('[data-a11y-tools-toggle]');

    toggle.addEventListener('click', () => {
        setPanelOpen(tools, toggle.getAttribute('aria-expanded') !== 'true');
    });

    tools.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') {
            setPanelOpen(tools, false);
            toggle.focus();
        }
    });

    tools.addEventListener('focusout', (event) => {
        if (event.relatedTarget && !tools.contains(event.relatedTarget)) {
            setPanelOpen(tools, false);
        }
    });

    tools.querySelectorAll('[data-a11y-zoom]').forEach((button) => {
        button.addEventListener('click', () => {
            if (button.getAttribute('aria-disabled') === 'true') {
                return;
            }

            const levelIndex = textZoomLevels.indexOf(currentTextZoom());
            const textZoom = {
                decrease: textZoomLevels[Math.max(levelIndex - 1, 0)],
                increase: textZoomLevels[Math.min(levelIndex + 1, textZoomLevels.length - 1)],
                reset: textZoomLevels[0],
            }[button.dataset.a11yZoom];

            applyTextZoom(textZoom);
            storePreference(textZoomStorageKey, String(textZoom), String(textZoomLevels[0]));
        });
    });

    tools.querySelectorAll('[data-a11y-color-mode]').forEach((radio) => {
        radio.addEventListener('change', () => {
            applyColorMode(radio.value);
            storePreference(colorModeStorageKey, radio.value, defaultColorMode);
        });
    });
});

document.addEventListener('click', (event) => {
    document.querySelectorAll('[data-a11y-tools]').forEach((tools) => {
        if (!tools.contains(event.target)) {
            setPanelOpen(tools, false);
        }
    });
});

const storedColorMode = root.getAttribute('data-color-mode');
const isKnownColorMode = [...document.querySelectorAll('[data-a11y-color-mode]')]
    .some((radio) => radio.value === storedColorMode);

applyTextZoom(currentTextZoom());
applyColorMode(isKnownColorMode ? storedColorMode : defaultColorMode);

/**
 * Site menu (layouts/app.blade.php): below the lg breakpoint the main navigation and language selector
 * live in a drawer that slides in from the right, under the header, so the menu, theme and accessibility
 * buttons stay usable while it is open. From lg up the menu is inline and the toggle is hidden by CSS.
 */
function placeSiteMenu(header, menu) {
    const headerBottom = Math.max(header.getBoundingClientRect().bottom, 0);

    menu.style.setProperty('--site-menu-top', `${headerBottom}px`);
}

function setSiteMenuOpen(header, isOpen) {
    const toggle = header.querySelector('[data-site-menu-toggle]');
    const menu = header.querySelector('[data-site-menu]');

    if (isOpen) {
        placeSiteMenu(header, menu);
    }

    toggle.setAttribute('aria-expanded', String(isOpen));
    menu.toggleAttribute('data-open', isOpen);
}

document.querySelectorAll('[data-site-header]').forEach((header) => {
    const toggle = header.querySelector('[data-site-menu-toggle]');
    const menu = header.querySelector('[data-site-menu]');

    if (!toggle || !menu) {
        return;
    }

    const isOpen = () => toggle.getAttribute('aria-expanded') === 'true';

    placeSiteMenu(header, menu);

    toggle.addEventListener('click', () => {
        setSiteMenuOpen(header, !isOpen());
    });

    document.addEventListener('keydown', (event) => {
        if (event.key !== 'Escape' || !isOpen()) {
            return;
        }

        const focusWasInMenu = menu.contains(document.activeElement);

        setSiteMenuOpen(header, false);

        if (focusWasInMenu) {
            toggle.focus();
        }
    });

    document.addEventListener('click', (event) => {
        if (isOpen() && !menu.contains(event.target) && !toggle.contains(event.target)) {
            setSiteMenuOpen(header, false);
        }
    });

    window.addEventListener('scroll', () => {
        if (isOpen()) {
            placeSiteMenu(header, menu);
        }
    }, { passive: true });

    window.addEventListener('resize', () => placeSiteMenu(header, menu));
});
