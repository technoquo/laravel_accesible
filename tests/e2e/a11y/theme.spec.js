import { test, expect } from '@playwright/test';

// ─── Selector de tema claro/oscuro ──────────────────────────────────────────

test.describe('Selector de tema', () => {
    test.use({ colorScheme: 'light' });

    test('alterna modo oscuro, actualiza aria-pressed y persiste al recargar', async ({ page }) => {
        await page.goto('/es');
        const toggle = page.getByRole('button', { name: 'Modo oscuro' });
        const html = page.locator('html');

        await expect(toggle).toHaveAttribute('aria-pressed', 'false');
        await expect(html).not.toHaveClass(/\bdark\b/);

        await toggle.click();
        await expect(toggle).toHaveAttribute('aria-pressed', 'true');
        await expect(html).toHaveClass(/\bdark\b/);

        await page.reload();
        await expect(page.locator('html')).toHaveClass(/\bdark\b/);
        await expect(page.getByRole('button', { name: 'Modo oscuro' })).toHaveAttribute('aria-pressed', 'true');
    });

    test('se activa con el teclado', async ({ page }) => {
        await page.goto('/en');
        const toggle = page.getByRole('button', { name: 'Dark mode' });

        await toggle.focus();
        await page.keyboard.press('Enter');
        await expect(toggle).toHaveAttribute('aria-pressed', 'true');

        await page.keyboard.press('Space');
        await expect(toggle).toHaveAttribute('aria-pressed', 'false');
    });

    test('respeta prefers-color-scheme cuando no hay preferencia guardada', async ({ browser }) => {
        const context = await browser.newContext({ colorScheme: 'dark', ignoreHTTPSErrors: true });
        const page = await context.newPage();
        await page.goto('/es');

        await expect(page.locator('html')).toHaveClass(/\bdark\b/);
        await expect(page.getByRole('button', { name: 'Modo oscuro' })).toHaveAttribute('aria-pressed', 'true');
        await context.close();
    });
});
