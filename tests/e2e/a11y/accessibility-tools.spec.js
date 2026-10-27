import { test, expect } from '@playwright/test';
import AxeBuilder from '@axe-core/playwright';

// ─── Herramientas de accesibilidad: zoom de texto y modos de color ──────────

test.describe('Herramientas de accesibilidad', () => {
    test.use({ colorScheme: 'light' });

    test('abre y cierra el panel con el teclado y devuelve el foco', async ({ page }) => {
        await page.goto('/es');
        const toggle = page.getByRole('button', { name: 'Accesibilidad' });
        const panel = page.locator('#a11y-tools-panel');

        await expect(toggle).toHaveAttribute('aria-expanded', 'false');
        await expect(panel).toBeHidden();

        await toggle.focus();
        await page.keyboard.press('Enter');
        await expect(toggle).toHaveAttribute('aria-expanded', 'true');
        await expect(panel).toBeVisible();

        await page.getByRole('button', { name: 'Aumentar texto' }).focus();
        await page.keyboard.press('Escape');
        await expect(panel).toBeHidden();
        await expect(toggle).toBeFocused();
    });

    test('aumenta, limita y restablece el tamaño del texto, y lo persiste', async ({ page }) => {
        await page.goto('/es');
        await page.getByRole('button', { name: 'Accesibilidad' }).click();
        const increase = page.getByRole('button', { name: 'Aumentar texto' });
        const decrease = page.getByRole('button', { name: 'Reducir texto' });
        const html = page.locator('html');

        await expect(decrease).toHaveAttribute('aria-disabled', 'true');

        await increase.click();
        await expect(html).toHaveAttribute('data-text-zoom', '125');
        await expect(page.locator('[data-a11y-zoom-value]')).toHaveText('125%');

        for (let step = 0; step < 3; step++) {
            await increase.click();
        }
        await expect(html).toHaveAttribute('data-text-zoom', '200');
        await expect(increase).toHaveAttribute('aria-disabled', 'true');

        await page.reload();
        await expect(page.locator('html')).toHaveAttribute('data-text-zoom', '200');
        expect(await page.evaluate(() => getComputedStyle(document.documentElement).fontSize)).toBe('32px');

        await page.getByRole('button', { name: 'Accesibilidad' }).click();
        await page.getByRole('button', { name: 'Restablecer' }).click();
        await expect(page.locator('html')).not.toHaveAttribute('data-text-zoom');
    });

    test('aplica y persiste un modo de color', async ({ page }) => {
        await page.goto('/en');
        await page.getByRole('button', { name: 'Accessibility' }).click();
        const html = page.locator('html');

        await page.getByRole('radio', { name: 'Yellow on black' }).check();
        await expect(html).toHaveAttribute('data-color-mode', 'yellow-black');
        expect(await page.evaluate(() => getComputedStyle(document.body).backgroundColor)).toBe('rgb(0, 0, 0)');

        await page.reload();
        await expect(page.locator('html')).toHaveAttribute('data-color-mode', 'yellow-black');
        await page.getByRole('button', { name: 'Accessibility' }).click();
        await expect(page.getByRole('radio', { name: 'Yellow on black' })).toBeChecked();

        await page.getByRole('radio', { name: 'Site scheme' }).check();
        await expect(page.locator('html')).not.toHaveAttribute('data-color-mode');
    });

    for (const colorMode of ['yellow-black', 'white-black']) {
        test(`sin violaciones axe con el panel abierto en modo ${colorMode}`, async ({ page }) => {
            await page.addInitScript((mode) => localStorage.setItem('colorMode', mode), colorMode);
            await page.goto('/es');
            await page.getByRole('button', { name: 'Accesibilidad' }).click();

            const results = await new AxeBuilder({ page })
                .withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa'])
                .exclude('#herd-ai-host')
                .analyze();
            expect(results.violations.map((violation) => violation.id)).toEqual([]);
        });
    }
});
