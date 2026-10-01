const { defineConfig } = require('@playwright/test');

module.exports = defineConfig({
    testDir: './tests/playwright',
    timeout: 30000,
    use: {
        baseURL: 'http://127.0.0.1:8001',
        headless: true,
    },
    projects: [{ name: 'chromium', use: { browserName: 'chromium' } }],
});
