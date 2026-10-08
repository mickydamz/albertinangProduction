import {test,expect,login,origin} from '../support/fixtures.js';
test('@deployment staging refund service is ready and private files are protected', async ({page,adminPage,request})=>{
  await page.goto('/');
  await expect(page.getByRole('link',{name:'Shopping cart',exact:true})).toBeVisible();
  const guest=await request.get('/admin/paystack/refund-test-readiness',{maxRedirects:0});
  expect(guest.status()).toBe(302);
  await login(adminPage,'ADMIN');
  const response=await adminPage.request.get('/admin/paystack/refund-test-readiness');
  expect(response.status()).toBe(200);
  expect(await response.json()).toMatchObject({test_mode:true,refund_status_endpoint:true});
  for(const path of ['/ecommerce/.env','/ecommerce/storage/logs/laravel.log']) {
    const response=await request.get(path);
    expect([403,404],`${origin}${path} must remain inaccessible`).toContain(response.status());
  }
});
