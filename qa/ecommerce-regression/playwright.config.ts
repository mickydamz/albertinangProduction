import { defineConfig, devices } from '@playwright/test';
import 'dotenv/config';
const baseURL = process.env.BASE_URL || 'https://test.albertinang.com';
if (!['https://test.albertinang.com', 'https://testing.albertinang.com'].includes(new URL(baseURL).origin)) throw new Error('This suite is restricted to the test and testing staging sites.');
const reports = process.env.REPORT_DIR || 'reports';
export default defineConfig({
  testDir: './tests', timeout: 60000, expect: { timeout: 12000 },
  fullyParallel: false, workers: 1, retries: 0, forbidOnly: !!process.env.CI,
  outputDir: `${reports}/artifacts`,
  reporter: [['list'], ['./support/failure-reporter.ts'], ['html', { outputFolder: `${reports}/html`, open: 'never' }], ['json', { outputFile: `${reports}/results.json` }], ['junit', { outputFile: `${reports}/junit.xml` }]],
  use: { baseURL, ignoreHTTPSErrors: false, screenshot: 'only-on-failure', trace: 'retain-on-failure', video: 'retain-on-failure', actionTimeout: 15000, navigationTimeout: 45000 },
  projects: [
    { name: 'desktop', use: { ...devices['Desktop Chrome'] } },
    { name: 'mobile', testIgnore: /admin|lifecycle|api|payment-sandbox/, use: { ...devices['iPhone 13'], defaultBrowserType: 'chromium' } },
    { name: 'firefox', testIgnore: /admin|lifecycle|api|payment-sandbox/, use: { ...devices['Desktop Firefox'] } },
  ],
});
