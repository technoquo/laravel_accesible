import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
    testDir: './tests/e2e',
    fullyParallel: false,
    forbidOnly: !!process.env.CI,
    retries: 0,
    reporter: [['html', { open: 'never' }], ['list']],
    use: {
        baseURL: 'https://laravel-accesible.test',
        trace: 'on-first-retry',
        ignoreHTTPSErrors: true,
    },
    projects: [
        {
            name: 'chromium',
            use: { ...devices['Desktop Chrome'] },
        },
        {
            // Repeats the axe audits with the dark theme (system preference, no stored choice)
            name: 'chromium-dark',
            testMatch: /a11y\/(public|admin)\.spec\.js/,
            use: { ...devices['Desktop Chrome'], colorScheme: 'dark' },
        },
    ],
});
