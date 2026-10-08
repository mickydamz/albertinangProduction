import { test, expect, productPath, productName, addProduct } from '../support/fixtures.js';
test('WEB-001 @smoke storefront exposes product journeys', async ({ page }) => {
  await page.goto('/');
  await expect(page.getByRole('link', { name: 'Shopping cart' })).toBeVisible();
  await expect(page.getByRole('link', { name: productName, exact: true }).first()).toBeVisible();
});
test('WEB-002 storefront never links customers into production', async ({ page }) => {
  await page.goto('/');
  const links = await page.locator('a[href]').evaluateAll(els => els.map(e => ({text:e.textContent,href:(e as HTMLAnchorElement).href})).filter(e => /^https?:\/\/(www\.)?albertinang\.com(?:\/|$)/.test(e.href)));
  expect(links, 'Staging links to production').toEqual([]);
});
for (const path of ['/about','/contact','/faq','/privacy','/terms','/store-locator','/store-locations','/blog']) {
  test(`WEB-PAGE ${path} has meaningful content`, async ({ page }) => {
    const response = await page.goto(path);
    expect(response?.status()).toBe(200);
    await expect(page.getByRole('main')).toBeVisible();
    expect((await page.getByRole('main').innerText()).trim().length).toBeGreaterThan(40);
    await expect(page.getByRole('main')).not.toContainText(/Server Error|Stack trace|404\s+Not Found/i);
  });
}
test('CAT-001 @smoke product details, description and reviews render', async ({ page }) => {
  await page.goto(productPath);
  await expect(page.getByRole('heading', {level:1})).toHaveText(productName);
  await expect(page.getByRole('button',{name:'Decrease quantity',exact:true})).toBeDisabled();
  await page.getByRole('button',{name:'Description',exact:true}).click();
  await expect(page.getByRole('main')).toContainText('Hisense');
  await page.getByRole('button',{name:/^Reviews \(/}).click();
  await expect(page.getByRole('main')).toContainText(/review/i);
});
test('CART-001 @smoke add, increment, decrement, persist and remove', async ({ page }) => {
  await addProduct(page);
  const qty = page.getByRole('spinbutton',{name:`Quantity for ${productName}`});
  await expect(qty).toHaveValue('1');
  await page.getByRole('button',{name:'Increase',exact:true}).click();
  await expect(qty).toHaveValue('2');
  await page.reload();
  await expect(qty).toHaveValue('2');
  await page.getByRole('button',{name:'Decrease',exact:true}).click();
  await expect(qty).toHaveValue('1');
  page.once('dialog', dialog => dialog.accept());
  await page.getByRole('button',{name:`Remove ${productName}`,exact:true}).click();
  await expect(page.getByRole('main')).toContainText(/empty/i);
});
test('CART-002 duplicate adds consolidate into one cart line', async ({ page }) => {
  await addProduct(page); await addProduct(page);
  await expect(page.getByRole('spinbutton',{name:`Quantity for ${productName}`})).toHaveValue('2');
  await expect(page.getByRole('spinbutton')).toHaveCount(1);
});
test('CART-003 guest checkout requires sign in', async ({ page }) => {
  await addProduct(page);
  await page.getByRole('button',{name:'Proceed to Checkout'}).click();
  await expect(page).toHaveURL(/\/login/);
  await expect(page.getByRole('textbox',{name:'Email Address'})).toBeVisible();
});
test('CAT-002 search returns matching product', async ({ page }, info) => {
  await page.goto('/');
  if(info.project.name==='mobile') await page.locator('#mobileSearchBtn').click();
  const form=page.locator(info.project.name==='mobile' ? '#mobileSearchForm' : '#desktopSearchForm');
  await form.getByRole('textbox',{name:'Search products',exact:true}).fill('SD-165');
  await form.getByRole('button',{name:'Search',exact:false}).click();
  await expect(page.getByRole('main')).toContainText('SD-165');
});
test('CAT-003 unknown search has empty state', async ({ page }, info) => {
  await page.goto('/');
  if(info.project.name==='mobile') await page.locator('#mobileSearchBtn').click();
  const form=page.locator(info.project.name==='mobile' ? '#mobileSearchForm' : '#desktopSearchForm');
  await form.getByRole('textbox',{name:'Search products',exact:true}).fill('ZZZ_REGRESSION_NO_SUCH_PRODUCT_84721');
  await form.getByRole('button',{name:'Search',exact:false}).click();
  await expect(page.getByRole('main')).toContainText(/no products|no results|not found|couldn.t find/i);
});
