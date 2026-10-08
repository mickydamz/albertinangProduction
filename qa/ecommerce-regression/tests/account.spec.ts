import { test, expect, login, addProduct, productName } from '../support/fixtures.js';
test('AUTH-001 @smoke valid login opens account', async ({ page }) => {
  await login(page); await page.goto('/account');
  await expect(page).not.toHaveURL(/\/login/);
  await expect(page.getByRole('main')).toContainText(/account/i);
});
test('AUTH-002 blank login has required field validation', async ({ page }) => {
  await page.goto('/login');
  await page.getByRole('button',{name:'Sign In',exact:true}).click();
  await expect(page).toHaveURL(/\/login/);
  expect(await page.getByRole('textbox',{name:'Email Address'}).evaluate((e: HTMLInputElement)=>e.validity.valueMissing)).toBe(true);
});
test('CHECKOUT-001 @smoke guest basket survives login and reaches checkout', async ({ page }) => {
  await addProduct(page); await login(page); await page.goto('/checkout');
  await expect(page.getByRole('main')).toContainText(productName);
  await expect(page.getByRole('button',{name:'Collect in Store'})).toBeVisible();
  await expect(page.getByRole('button',{name:'Home Delivery'})).toBeVisible();
});
test('CHECKOUT-002 invalid coupon does not produce discount', async ({ page }) => {
  await login(page); await addProduct(page); await page.goto('/checkout');
  await expect(page.getByRole('main')).toContainText(productName);
  await page.getByRole('textbox',{name:'Have a coupon?'}).fill('REGRESSION_INVALID_84721');
  await page.getByRole('button',{name:'Apply',exact:true}).click();
  await expect(page.getByRole('main')).toContainText(/invalid|not found|does not exist/i);
});
test('ORDER-001 order history requires authentication', async ({ page }) => {
  await page.goto('/account/orders'); await expect(page).toHaveURL(/\/login/);
});
test('ORDER-002 authenticated order history renders and reloads', async ({ page },info) => {
  await login(page); const response = await page.goto('/account/orders');
  expect(response?.status()).toBe(200);
  await expect(page.getByRole('main')).toContainText(/orders/i);
  await expect(page.getByRole('main')).toContainText(/ALB-/);
  const reloaded=await page.reload();
  expect(reloaded?.status(),'Order history must render even when refund attempts are absent').toBe(200);
  await expect(page.getByRole('main')).toContainText(/ALB-/);
  await page.screenshot({path:info.outputPath('my-orders-working.png'),fullPage:true});
});
