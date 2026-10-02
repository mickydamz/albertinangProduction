const { defineConfig } = require('@playwright/test');

module.exports = defineConfig({
    testDir: './tests/playwright',
    globalSetup: require.resolve('./tests/playwright/global-setup'),
    webServer: {
        command: 'php artisan serve --env=testing --host=127.0.0.1 --port=8001',
        url: 'http://127.0.0.1:8001',
        reuseExistingServer: !process.env.CI,
        timeout: 60000,
    },
    timeout: 30000,
    use: {
        baseURL: 'http://127.0.0.1:8001',
        headless: true,
    },
    projects: [{ name: 'chromium', use: { browserName: 'chromium' } }],
});
