import { test, expect } from '@playwright/test';
import AxeBuilder from '@axe-core/playwright';

/**
 * Runs axe-core against the current page with WCAG 2.x AA tags.
 * Excludes the Herd development toolbar (injected by Laravel Herd in dev mode; not part of the app).
 * Returns the violations array for assertions.
 */
async function runAxe(page) {
    const results = await new AxeBuilder({ page })
        .withTags(['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa', 'wcag22aa'])
        .exclude('#herd-ai-host')
        .analyze();
    return results.violations;
}

/**
 * Formats axe violations for readable test failure messages.
 * @param {import('axe-core').Result[]} violations
 */
function formatViolations(violations) {
    if (violations.length === 0) { return 'No violations'; }
    return violations
        .map(v =>
            `\n[${v.impact?.toUpperCase()}] ${v.id}: ${v.description}\n` +
            v.nodes.slice(0, 3).map(n => `  → ${n.html.slice(0, 120)}`).join('\n'),
        )
        .join('\n');
}

// ─── Portada ────────────────────────────────────────────────────────────────

test.describe('Portada', () => {
    test('ES — sin violaciones axe WCAG 2.2 AA', async ({ page }) => {
        await page.goto('/es');
        await page.waitForLoadState('load');
        const violations = await runAxe(page);
        expect(violations, formatViolations(violations)).toHaveLength(0);
    });

    test('EN — sin violaciones axe WCAG 2.2 AA', async ({ page }) => {
        await page.goto('/en');
        await page.waitForLoadState('load');
        const violations = await runAxe(page);
        expect(violations, formatViolations(violations)).toHaveLength(0);
    });

    test('ES — tiene h1 y title descriptivos', async ({ page }) => {
        await page.goto('/es');
        const h1 = await page.locator('h1').first().textContent();
        expect(h1?.trim().length).toBeGreaterThan(0);
        const title = await page.title();
        expect(title.trim().length).toBeGreaterThan(0);
        expect(title).not.toBe('');
    });

    test('EN — tiene h1 y title descriptivos', async ({ page }) => {
        await page.goto('/en');
        const h1 = await page.locator('h1').first().textContent();
        expect(h1?.trim().length).toBeGreaterThan(0);
        const title = await page.title();
        expect(title.trim().length).toBeGreaterThan(0);
    });

    test('skip link visible al recibir foco', async ({ page }) => {
        await page.goto('/es');
        await page.keyboard.press('Tab');
        const skipLink = page.locator('a[href="#main-content"]');
        await expect(skipLink).toBeVisible();
    });

    test('lang attribute correcto en ES y EN', async ({ page }) => {
        await page.goto('/es');
        const langEs = await page.locator('html').getAttribute('lang');
        expect(langEs).toBe('es');

        await page.goto('/en');
        const langEn = await page.locator('html').getAttribute('lang');
        expect(langEn).toBe('en');
    });
});

// ─── Índice de artículos ────────────────────────────────────────────────────

test.describe('Índice de artículos', () => {
    test('ES — sin violaciones axe WCAG 2.2 AA', async ({ page }) => {
        await page.goto('/es/articulos');
        await page.waitForLoadState('load');
        const violations = await runAxe(page);
        expect(violations, formatViolations(violations)).toHaveLength(0);
    });

    test('EN — sin violaciones axe WCAG 2.2 AA', async ({ page }) => {
        await page.goto('/en/articles');
        await page.waitForLoadState('load');
        const violations = await runAxe(page);
        expect(violations, formatViolations(violations)).toHaveLength(0);
    });

    test('formulario de búsqueda tiene label accesible', async ({ page }) => {
        await page.goto('/es/articulos');
        const input = page.locator('input[type="search"]');
        await expect(input).toBeVisible();
        // label or aria-label must be present
        const ariaLabel = await input.getAttribute('aria-label');
        const id = await input.getAttribute('id');
        const hasLabel = !!ariaLabel || (!!id && await page.locator(`label[for="${id}"]`).count() > 0);
        expect(hasLabel).toBe(true);
    });

    test('búsqueda con query — sin violaciones axe', async ({ page }) => {
        await page.goto('/es/articulos?search=laravel');
        await page.waitForLoadState('load');
        const violations = await runAxe(page);
        expect(violations, formatViolations(violations)).toHaveLength(0);
    });
});

// ─── Detalle de artículo ────────────────────────────────────────────────────

test.describe('Detalle de artículo', () => {
    test('ES — sin violaciones axe WCAG 2.2 AA (si existe contenido)', async ({ page }) => {
        await page.goto('/es/articulos');
        await page.waitForLoadState('load');

        const firstArticleLink = page.locator('article h2 a, article h3 a').first();
        const count = await firstArticleLink.count();
        if (count === 0) {
            test.skip(true, 'No hay artículos publicados — se omite la prueba de detalle');
            return;
        }

        await firstArticleLink.click();
        await page.waitForLoadState('load');
        const violations = await runAxe(page);
        expect(violations, formatViolations(violations)).toHaveLength(0);
    });

    test('EN — sin violaciones axe WCAG 2.2 AA (si existe contenido)', async ({ page }) => {
        await page.goto('/en/articles');
        await page.waitForLoadState('load');

        const firstArticleLink = page.locator('article h2 a, article h3 a').first();
        const count = await firstArticleLink.count();
        if (count === 0) {
            test.skip(true, 'No published articles — skipping detail test');
            return;
        }

        await firstArticleLink.click();
        await page.waitForLoadState('load');
        const violations = await runAxe(page);
        expect(violations, formatViolations(violations)).toHaveLength(0);
    });
});

// ─── Categoría ──────────────────────────────────────────────────────────────

test.describe('Página de categoría', () => {
    test('ES — sin violaciones axe WCAG 2.2 AA (si existe contenido)', async ({ page }) => {
        await page.goto('/es/articulos');
        await page.waitForLoadState('load');

        const categoryLink = page.locator('article a[href*="/categorias/"]').first();
        const count = await categoryLink.count();
        if (count === 0) {
            test.skip(true, 'No hay categorías enlazadas — se omite la prueba');
            return;
        }

        await categoryLink.click();
        await page.waitForLoadState('load');
        const violations = await runAxe(page);
        expect(violations, formatViolations(violations)).toHaveLength(0);
    });
});

// ─── Etiqueta ───────────────────────────────────────────────────────────────

test.describe('Página de etiqueta', () => {
    test('ES — sin violaciones axe WCAG 2.2 AA (si existe contenido)', async ({ page }) => {
        await page.goto('/es/articulos');
        await page.waitForLoadState('load');

        const tagLink = page.locator('article a[href*="/etiquetas/"]').first();
        const count = await tagLink.count();
        if (count === 0) {
            test.skip(true, 'No hay etiquetas enlazadas — se omite la prueba');
            return;
        }

        await tagLink.click();
        await page.waitForLoadState('load');
        const violations = await runAxe(page);
        expect(violations, formatViolations(violations)).toHaveLength(0);
    });
});

// ─── Página 404 ──────────────────────────────────────────────────────────────

test.describe('Página 404', () => {
    test('ES — sin violaciones axe WCAG 2.2 AA', async ({ page }) => {
        await page.goto('/es/articulos/pagina-inexistente-xyz-404');
        await page.waitForLoadState('load');
        const violations = await runAxe(page);
        expect(violations, formatViolations(violations)).toHaveLength(0);
    });

    test('tiene h1 descriptivo', async ({ page }) => {
        await page.goto('/es/articulos/pagina-inexistente-xyz-404');
        const h1 = await page.locator('h1').first().textContent();
        expect(h1?.trim().length).toBeGreaterThan(0);
    });
});
