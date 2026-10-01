const { test, expect } = require('@playwright/test');

// Country options are rendered server-side; states load via /api/countries/{id}/states.
// The selects are enhanced with Tom Select, so we drive them via the underlying <select>.

async function selectTom(page, selectId, label) {
    // Set value on the native select and dispatch change so Tom Select + our handler react.
    await page.evaluate(({ selectId, label }) => {
        const sel = document.getElementById(selectId);
        const opt = [...sel.options].find(o => o.textContent.trim() === label);
        if (!opt) throw new Error(`Option "${label}" not found in #${selectId}`);
        // Prefer the Tom Select API when present
        if (sel.tomselect) {
            sel.tomselect.setValue(opt.value);
        } else {
            sel.value = opt.value;
            sel.dispatchEvent(new Event('change', { bubbles: true }));
        }
    }, { selectId, label });
}

test.describe('Registration — country/state dropdowns', () => {

    test.beforeEach(async ({ page }) => {
        await page.goto('/register');
        // Wait until country options are present and Tom Select has initialised
        await page.waitForFunction(() => {
            const sel = document.getElementById('country_id');
            return sel && sel.options.length > 10 && sel.tomselect;
        }, { timeout: 15000 });
    });

    test('Nigeria is default-selected and its 37 states auto-load', async ({ page }) => {
        // Country defaults to Nigeria
        const countryVal = await page.locator('#country_id').inputValue();
        const nigeriaOpt = await page.locator('#country_id option', { hasText: 'Nigeria' }).getAttribute('value');
        expect(countryVal).toBe(nigeriaOpt);

        // States load via fetch — wait for them
        await page.waitForFunction(() =>
            document.getElementById('state_id').options.length > 10, { timeout: 10000 });

        const stateCount = await page.locator('#state_id option:not([value=""])').count();
        console.log('Nigeria states:', stateCount);
        expect(stateCount).toBe(37);
    });

    test('Algeria is selectable and loads its wilayas', async ({ page }) => {
        await selectTom(page, 'country_id', 'Algeria');

        await page.waitForFunction(() =>
            document.getElementById('state_id').options.length > 5, { timeout: 10000 });

        const stateCount = await page.locator('#state_id option:not([value=""])').count();
        console.log('Algeria states:', stateCount);
        expect(stateCount).toBeGreaterThan(10);
    });

    test('switching country replaces the state list', async ({ page }) => {
        // The state field is a Tom Select — its live option list lives in the JS
        // store, not the native <select>.options — so read it from there.
        const tsStateNames = () => page.evaluate(() => {
            const ts = document.getElementById('state_id').tomselect;
            return ts ? Object.values(ts.options).map((o) => o.text) : [];
        });

        // Start on Nigeria (default) — its states are rendered server-side.
        await page.waitForFunction(() => {
            const ts = document.getElementById('state_id').tomselect;
            return ts && Object.values(ts.options).some((o) => /Lagos|Abia/.test(o.text));
        }, { timeout: 10000 });
        expect((await tsStateNames()).some((n) => /Lagos/.test(n))).toBe(true);

        // Switch to United States → the list must be replaced with US states.
        await selectTom(page, 'country_id', 'United States');
        await page.waitForFunction(() => {
            const ts = document.getElementById('state_id').tomselect;
            return ts && Object.values(ts.options).some((o) => o.text === 'California');
        }, { timeout: 10000 });

        const usNames = await tsStateNames();
        console.log('US states:', usNames.length);
        expect(usNames.length).toBeGreaterThanOrEqual(50);
        expect(usNames.some((n) => /Abia|Lagos/.test(n))).toBe(false);
    });

    test('state-less countries are not offered in the dropdown', async ({ page }) => {
        // The register dropdown lists only countries that have states/cities
        // (RegisterController: Country::whereHas('cities')). So a state-less country
        // like Aland Islands is never selectable, and the "empty state list" case
        // simply cannot occur here.
        const alandPresent = await page.evaluate(() =>
            [...document.getElementById('country_id').options]
                .some((o) => o.textContent.trim() === 'Aland Islands')
        );
        expect(alandPresent).toBe(false);
    });

    test('full valid form with Algeria + state passes browser validation', async ({ page }) => {
        await selectTom(page, 'country_id', 'Algeria');
        await page.waitForFunction(() =>
            document.getElementById('state_id').options.length > 5, { timeout: 10000 });

        // Pick the first real state via Tom Select
        const firstStateLabel = await page.locator('#state_id option:not([value=""])').first().textContent();
        await selectTom(page, 'state_id', firstStateLabel.trim());

        await page.fill('#name', 'Playwright User');
        await page.fill('#email', `pw_${Date.now()}@example.com`);
        await page.fill('#password', 'Password123!');
        await page.fill('#password_confirmation', 'Password123!');
        await page.check('#terms_conditions');

        const valid = await page.evaluate(() => document.getElementById('reg-form').checkValidity());
        expect(valid).toBe(true);
        console.log(`Algeria / ${firstStateLabel.trim()} — form valid ✓`);
    });

});

// The registration page also geolocates the visitor (ipwho.is), pre-selects their
// country, and drives the state dropdown to that country's states — the same
// "country change → state change" behaviour, but triggered by IP rather than a
// manual pick. We stub the geo response so the detected country is deterministic.
test.describe('Registration — IP-detected country drives the state dropdown', () => {

    async function stubGeo(page, countryCode) {
        await page.route('https://ipwho.is/**', (route) =>
            route.fulfill({
                status: 200,
                contentType: 'application/json',
                body: JSON.stringify({ success: true, country_code: countryCode }),
            })
        );
    }

    // Wait until the country <select> resolves to the option with this label.
    async function waitForCountry(page, label) {
        await page.waitForFunction((label) => {
            const sel = document.getElementById('country_id');
            if (!sel || !sel.tomselect) return false;
            const opt = [...sel.options].find((o) => o.textContent.trim() === label);
            return opt && sel.value === opt.value;
        }, label, { timeout: 15000 });
    }

    // The state field is a Tom Select — its real option list lives in the JS store,
    // not the native <select>.options — so read state names from there.
    function stateNames(page) {
        return page.evaluate(() => {
            const ts = document.getElementById('state_id').tomselect;
            return ts ? Object.values(ts.options).map((o) => o.text) : [];
        });
    }

    async function waitForStateNames(page, expectedName) {
        await page.waitForFunction((name) => {
            const ts = document.getElementById('state_id').tomselect;
            return ts && Object.values(ts.options).some((o) => o.text === name);
        }, expectedName, { timeout: 12000 });
    }

    test('US IP → country pre-selects United States and its states load', async ({ page }) => {
        await stubGeo(page, 'US');
        await page.goto('/register');

        await waitForCountry(page, 'United States');
        await waitForStateNames(page, 'California'); // a US-specific state must appear

        const names = await stateNames(page);
        expect(names.length).toBeGreaterThanOrEqual(50);
        expect(names).toContain('California');
        // And the old Nigeria list must be gone — the state list truly followed the country.
        expect(names.some((n) => /Abia|Lagos/.test(n))).toBe(false);
    });

    test('Algeria IP → country pre-selects Algeria and its wilayas load', async ({ page }) => {
        await stubGeo(page, 'DZ');
        await page.goto('/register');

        await waitForCountry(page, 'Algeria');
        await waitForStateNames(page, 'Adrar Province'); // an Algerian wilaya

        const names = await stateNames(page);
        expect(names.length).toBeGreaterThan(10);
        expect(names.some((n) => /Abia|Lagos/.test(n))).toBe(false);
    });

});
