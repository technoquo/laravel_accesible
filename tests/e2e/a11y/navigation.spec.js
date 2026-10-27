import { test, expect } from '@playwright/test';
import AxeBuilder from '@axe-core/playwright';

/**
 * True when the page scrolls horizontally (WCAG 1.4.10 reflow).
 */
async function hasHorizontalScroll(page) {
    return page.evaluate(() => document.documentElement.scrollWidth > document.documentElement.clientWidth);
}

// ─── Menú responsive: hamburguesa en móvil y tablet ─────────────────────────

test.describe('Menú responsive', () => {
    test.use({ colorScheme: 'light' });

    for (const viewport of [{ width: 320, height: 640 }, { width: 375, height: 812 }, { width: 768, height: 1024 }]) {
        test(`${viewport.width}px — menú colapsado, abre con teclado y cierra con Escape`, async ({ page }) => {
            await page.setViewportSize(viewport);
            await page.goto('/es');

            const toggle = page.getByRole('button', { name: 'Menú' });
            const articlesLink = page.getByRole('navigation', { name: 'Navegación principal' })
                .getByRole('link', { name: 'Artículos' });

            await expect(toggle).toHaveAttribute('aria-expanded', 'false');
            await expect(articlesLink).toBeHidden();
            await expect(page.getByRole('button', { name: 'Accesibilidad' })).toBeVisible();
            await expect(page.getByRole('button', { name: 'Modo oscuro' })).toBeVisible();

            await toggle.focus();
            await page.keyboard.press('Enter');
            await expect(toggle).toHaveAttribute('aria-expanded', 'true');
            await expect(articlesLink).toBeVisible();
            await expect(page.getByRole('link', { name: 'Inglés' })).toBeVisible();

            await page.keyboard.press('Tab');
            await expect(articlesLink).toBeFocused();
            await page.keyboard.press('Escape');
            await expect(articlesLink).toBeHidden();
            await expect(toggle).toBeFocused();
        });

        test(`${viewport.width}px — sin scroll horizontal y sin solapes con la herramienta de accesibilidad`, async ({ page }) => {
            await page.setViewportSize(viewport);
            await page.goto('/es');
            await page.getByRole('button', { name: 'Menú' }).click();

            expect(await hasHorizontalScroll(page)).toBe(false);

            const toggleBox = await page.getByRole('button', { name: 'Menú' }).boundingBox();
            const toolsBox = await page.getByRole('button', { name: 'Accesibilidad' }).boundingBox();
            const themeBox = await page.getByRole('button', { name: 'Modo oscuro' }).boundingBox();
            expect(toggleBox.x + toggleBox.width).toBeLessThanOrEqual(themeBox.x);
            expect(themeBox.x + themeBox.width).toBeLessThanOrEqual(toolsBox.x);

            const drawer = page.locator('#site-menu');
            await expect(drawer).toBeInViewport({ ratio: 1 });
            const drawerBox = await drawer.boundingBox();
            expect(drawerBox.y).toBeGreaterThanOrEqual(toolsBox.y + toolsBox.height);
            expect(Math.round(drawerBox.x + drawerBox.width)).toBe(viewport.width);

            await page.getByRole('button', { name: 'Accesibilidad' }).click();
            const panelBox = await page.locator('#a11y-tools-panel').boundingBox();
            expect(panelBox.x).toBeGreaterThanOrEqual(0);
            expect(panelBox.x + panelBox.width).toBeLessThanOrEqual(viewport.width);
            expect(await hasHorizontalScroll(page)).toBe(false);
        });
    }

    test('375px — el panel lateral se cierra al pulsar fuera', async ({ page }) => {
        await page.setViewportSize({ width: 375, height: 812 });
        await page.goto('/es');
        const toggle = page.getByRole('button', { name: 'Menú' });

        await toggle.click();
        await expect(page.locator('#site-menu')).toBeVisible();

        await page.locator('main').click({ position: { x: 10, y: 10 } });
        await expect(toggle).toHaveAttribute('aria-expanded', 'false');
        await expect(page.locator('#site-menu')).toBeHidden();
    });

    test('320px con texto al 200 % — sin scroll horizontal', async ({ page }) => {
        await page.setViewportSize({ width: 320, height: 640 });
        await page.goto('/es');
        await page.evaluate(() => document.documentElement.setAttribute('data-text-zoom', '200'));
        await page.getByRole('button', { name: 'Menú' }).click();

        expect(await hasHorizontalScroll(page)).toBe(false);
    });

    test('375px con menú abierto — sin violaciones axe WCAG 2.2 AA', async ({ page }) => {
        await page.setViewportSize({ width: 375, height: 812 });
        await page.goto('/es');
        await page.getByRole('button', { name: 'Menú' }).click();

        const results = await new AxeBuilder({ page })
            .withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa'])
            .exclude('#herd-ai-host')
            .analyze();
        expect(results.violations).toHaveLength(0);
    });

    test('escritorio — menú siempre visible y sin botón hamburguesa', async ({ page }) => {
        await page.setViewportSize({ width: 1280, height: 800 });
        await page.goto('/es');

        await expect(page.getByRole('button', { name: 'Menú' })).toBeHidden();
        await expect(page.getByRole('navigation', { name: 'Navegación principal' })
            .getByRole('link', { name: 'Artículos' })).toBeVisible();
        expect(await hasHorizontalScroll(page)).toBe(false);
    });
});
