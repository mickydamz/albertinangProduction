const { test, expect } = require('@playwright/test');

/**
 * Verifies the on-load native-currency behaviour in layouts/simslayout.blade.php:
 *   1. On page load the app geolocates the visitor (ipwho.is) and picks their
 *      currency from COUNTRY_MAP.
 *   2. The header currency selector (globe badge + active dropdown row) flips to
 *      that currency — not just the prices.
 *
 * The geo lookup is a third-party call, so we intercept it and return a fixed
 * country_code to keep the test deterministic. A fresh browser context starts
 * with empty localStorage, so the once-per-browser auto-detect guard is off.
 */

// Stub ipwho.is with a chosen country BEFORE the page loads.
async function stubGeo(page, countryCode) {
    await page.route('https://ipwho.is/**', (route) =>
        route.fulfill({
            status: 200,
            contentType: 'application/json',
            body: JSON.stringify({ success: true, country_code: countryCode }),
        })
    );
}

// Assert the whole selector reflects `code`: the globe badge, the DROPDOWN's
// highlighted/active row (and that it's the ONLY active row), and the mobile pill.
async function expectSelectorCurrency(page, code) {
    // 1. Globe badge text.
    await expect(page.locator('#currencyBtn .currency-btn-label')).toHaveText(code, { timeout: 15000 });

    // 2. The dropdown's active item is exactly this currency — and nothing else.
    await expect(page.locator('#currencyMenuList button.active')).toHaveCount(1);
    await expect(page.locator('#currencyMenuList button.active')).toHaveAttribute('data-code', code);

    // 3. Open the dropdown and confirm the highlighted row is visible to the user.
    await page.click('#currencyBtn');
    await expect(page.locator(`#currencyMenuList button[data-code="${code}"].active`)).toBeVisible();
    await page.click('#currencyBtn'); // close again

    // 4. Mobile pill code label mirrors it too.
    await expect(page.locator('#mobileCurCode')).toHaveText(code);
}

test.describe('Currency auto-detect on load', () => {

    test('UK visitor → selector flips to GBP', async ({ page }) => {
        await stubGeo(page, 'GB');
        await page.goto('/');
        await expectSelectorCurrency(page, 'GBP');
    });

    test('US visitor → selector flips to USD', async ({ page }) => {
        await stubGeo(page, 'US');
        await page.goto('/');
        await expectSelectorCurrency(page, 'USD');
    });

    test('Eurozone visitor (France) → selector flips to EUR', async ({ page }) => {
        await stubGeo(page, 'FR');
        await page.goto('/');
        await expectSelectorCurrency(page, 'EUR');
    });

    test('Nigerian visitor → selector stays NGN', async ({ page }) => {
        await stubGeo(page, 'NG');
        await page.goto('/');
        await expectSelectorCurrency(page, 'NGN');
    });

    test('UK visitor → actual product PRICES convert to GBP (£), not just the selector', async ({ page }) => {
        await stubGeo(page, 'GB');
        await page.goto('/');

        // Selector flips…
        await expect(page.locator('#currencyBtn .currency-btn-label')).toHaveText('GBP', { timeout: 15000 });

        // …and the visible price text on product cards must actually be in £.
        const firstPrice = page.locator('[data-base-price-ngn]').first();
        await expect(firstPrice).toContainText('£', { timeout: 15000 });
        await expect(firstPrice).not.toContainText('₦');
    });

    test('UK visitor → prices convert even when geo resolves BEFORE currency rates load', async ({ page }) => {
        // Simulate the race: /currencies (rates) is slow, ipwho.is is instant.
        await page.route('**/currencies', async (route) => {
            await new Promise((r) => setTimeout(r, 1200));
            return route.continue();
        });
        await stubGeo(page, 'GB');
        await page.goto('/');

        await expect(page.locator('#currencyBtn .currency-btn-label')).toHaveText('GBP', { timeout: 15000 });
        const firstPrice = page.locator('[data-base-price-ngn]').first();
        await expect(firstPrice).toContainText('£', { timeout: 15000 });
    });

    test('Ghanaian visitor → selector flips to GHS', async ({ page }) => {
        await stubGeo(page, 'GH');
        await page.goto('/');
        await expectSelectorCurrency(page, 'GHS');
    });

    test('unmapped country (India) → falls back to USD', async ({ page }) => {
        await stubGeo(page, 'IN');
        await page.goto('/');
        await expectSelectorCurrency(page, 'USD');
    });

    test('geo failure does not disable detection — it retries on the next load', async ({ page }) => {
        // Ad blocker / flaky network: the geo lookup fails on the first load.
        let fail = true;
        await page.route('https://ipwho.is/**', (route) => {
            if (fail) return route.abort();
            return route.fulfill({
                status: 200,
                contentType: 'application/json',
                body: JSON.stringify({ success: true, country_code: 'GB' }),
            });
        });

        // First load: detection fails → no flip, stays on the NGN default.
        await page.goto('/');
        await expectSelectorCurrency(page, 'NGN');

        // Second load: geo now works. The earlier failure must NOT have disabled
        // detection — it runs again and flips to GBP.
        fail = false;
        await page.reload();
        await expectSelectorCurrency(page, 'GBP');
    });

    test('a changed country on reload re-applies the new currency (VPN switch)', async ({ page }) => {
        let country = 'US';
        await page.route('https://ipwho.is/**', (route) =>
            route.fulfill({
                status: 200,
                contentType: 'application/json',
                body: JSON.stringify({ success: true, country_code: country }),
            })
        );

        // Arrive from the US → USD.
        await page.goto('/');
        await expectSelectorCurrency(page, 'USD');

        // Switch VPN to the UK and reload → detection must re-run and flip to GBP.
        country = 'GB';
        await page.reload();
        await expectSelectorCurrency(page, 'GBP');
    });

    test('a manual choice survives reloads while the country is unchanged', async ({ page }) => {
        // First load in Ghana: selector should auto-detect GHS.
        await stubGeo(page, 'GH');
        await page.goto('/');
        await expectSelectorCurrency(page, 'GHS');

        // User manually picks USD.
        await page.click('#currencyBtn');
        await page.click('#currencyMenuList button[data-code="USD"]');
        await expectSelectorCurrency(page, 'USD');

        // Reload still geolocating to Ghana — manual choice must win.
        await page.reload();
        await expectSelectorCurrency(page, 'USD');
    });

    test('a manual choice IS overridden when the country changes (travel / VPN)', async ({ page }) => {
        // In Ghana, auto-detect GHS, then user manually picks USD.
        let country = 'GH';
        await page.route('https://ipwho.is/**', (route) =>
            route.fulfill({
                status: 200,
                contentType: 'application/json',
                body: JSON.stringify({ success: true, country_code: country }),
            })
        );
        await page.goto('/');
        await expectSelectorCurrency(page, 'GHS');

        await page.click('#currencyBtn');
        await page.click('#currencyMenuList button[data-code="USD"]');
        await expectSelectorCurrency(page, 'USD');

        // Now the visitor travels to the UK. The stale manual USD from Ghana must
        // give way to the new location's currency, GBP.
        country = 'GB';
        await page.reload();
        await expectSelectorCurrency(page, 'GBP');

        // And once in the UK, a fresh manual pick sticks across reloads again.
        await page.click('#currencyBtn');
        await page.click('#currencyMenuList button[data-code="EUR"]');
        await expectSelectorCurrency(page, 'EUR');
        await page.reload();
        await expectSelectorCurrency(page, 'EUR');
    });
});
