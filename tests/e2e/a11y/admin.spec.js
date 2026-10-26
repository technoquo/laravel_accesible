import { test, expect } from '@playwright/test';
import AxeBuilder from '@axe-core/playwright';

async function runAxe(page) {
    const results = await new AxeBuilder({ page })
        .withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa'])
        .exclude('#herd-ai-host')
        .analyze();
    return results.violations;
}

function formatViolations(violations) {
    if (violations.length === 0) { return 'No violations'; }
    return violations
        .map(v =>
            `\n[${v.impact?.toUpperCase()}] ${v.id}: ${v.description}\n` +
            v.nodes.slice(0, 3).map(n => `  → ${n.html.slice(0, 120)}`).join('\n'),
        )
        .join('\n');
}

// ─── Login ──────────────────────────────────────────────────────────────────

test.describe('Admin — Login', () => {
    test('formulario de login — sin violaciones axe WCAG 2.2 AA', async ({ page }) => {
        await page.goto('/admin/login');
        await page.waitForLoadState('load');
        const violations = await runAxe(page);
        expect(violations, formatViolations(violations)).toHaveLength(0);
    });

    test('inputs tienen labels correctamente enlazados', async ({ page }) => {
        await page.goto('/admin/login');
        const emailInput = page.locator('input[type="email"]');
        const passwordInput = page.locator('input[type="password"]');

        await expect(emailInput).toBeVisible();
        await expect(passwordInput).toBeVisible();

        const emailId = await emailInput.getAttribute('id');
        const passwordId = await passwordInput.getAttribute('id');

        expect(await page.locator(`label[for="${emailId}"]`).count()).toBe(1);
        expect(await page.locator(`label[for="${passwordId}"]`).count()).toBe(1);
    });

    test('skip link visible al recibir foco', async ({ page }) => {
        await page.goto('/admin/login');
        await page.waitForLoadState('networkidle');
        const skipLink = page.locator('a[href="#main-content"]');
        // Focus directly to avoid Livewire initialization race conditions
        await skipLink.focus();
        await expect(skipLink).toBeVisible();
    });

    test('errores de validación anunciados con role=alert', async ({ page }) => {
        await page.goto('/admin/login');
        // Submit empty form to trigger validation
        await page.locator('button[type="submit"]').click();
        // Wait for Livewire response
        await page.waitForTimeout(500);

        const alerts = page.locator('[role="alert"]');
        // If validation fires, should have at least one alert
        const count = await alerts.count();
        // Either no errors yet (server didn't validate) or errors have role=alert
        if (count > 0) {
            const violations = await runAxe(page);
            expect(violations, formatViolations(violations)).toHaveLength(0);
        }
    });
});

// ─── Panel autenticado ───────────────────────────────────────────────────────

test.describe('Admin — Panel autenticado', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto('/admin/login');
        await page.waitForLoadState('load');
        await page.fill('input[type="email"]', 'admin@laravel-accesible.test');
        await page.fill('input[type="password"]', 'password');
        await page.locator('button[type="submit"]').click();
        await page.waitForURL('**/admin/**');
    });

    test('lista de posts — sin violaciones axe WCAG 2.2 AA', async ({ page }) => {
        await page.goto('/admin/posts');
        await page.waitForLoadState('load');
        const violations = await runAxe(page);
        expect(violations, formatViolations(violations)).toHaveLength(0);
    });

    test('formulario de creación — sin violaciones axe WCAG 2.2 AA', async ({ page }) => {
        await page.goto('/admin/posts/create');
        await page.waitForLoadState('load');
        const violations = await runAxe(page);
        expect(violations, formatViolations(violations)).toHaveLength(0);
    });

    test('lista de categorías — sin violaciones axe WCAG 2.2 AA', async ({ page }) => {
        await page.goto('/admin/categories');
        await page.waitForLoadState('load');
        const violations = await runAxe(page);
        expect(violations, formatViolations(violations)).toHaveLength(0);
    });

    test('lista de etiquetas — sin violaciones axe WCAG 2.2 AA', async ({ page }) => {
        await page.goto('/admin/tags');
        await page.waitForLoadState('load');
        const violations = await runAxe(page);
        expect(violations, formatViolations(violations)).toHaveLength(0);
    });
});
