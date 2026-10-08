import {test,expect,login} from '../support/fixtures.js';
import {setStatus,type PaidOrder} from '../support/lifecycle.js';
test.skip(process.env.RUN_BOUNDARY_DEPLOYMENT !== 'true','Explicit deployment diagnostic using an existing dummy order only');
test('BOUNDARY deployment: collection cancellation ends at completion; returns begin then',async({page,adminPage},info)=>{
 test.setTimeout(120000);
 await login(adminPage,'ADMIN');await login(page);
 const order:PaidOrder={id:103,number:'ALB-ENU-339814',reference:'ps_01M3XQ9HD0S3BYECMCJJQP7PPV',totalNgn:55536};
 await adminPage.goto('/admin/orders/103');
 await expect(adminPage.getByRole('main')).toContainText(order.number);
 await adminPage.goto('/admin/orders/103/edit');
 const original=await adminPage.getByRole('combobox',{name:'Order Status'}).inputValue();
 expect(original,'Only the previously created diagnostic paid order may be used').toBe('paid');
 const history=async()=>{
  for(let n=1;n<=20;n++){const r=await page.goto(`/account/orders?page=${n}`);expect(r?.status()).toBe(200);if((await page.getByRole('main').innerText()).includes(order.number))return r;}
  throw new Error('Diagnostic order was not found in order history');
 };
 try {
  await setStatus(adminPage,order,'ready_for_pickup');
  let response=await history();expect(response?.status()).toBe(200);
  let cancel=page.locator('a[href$="/account/orders/103/cancel"]');await expect(cancel).toBeVisible();
  await expect(page.locator('a[href$="/account/orders/103/return"]')).toHaveCount(0);
  const token=await page.locator('meta[name="csrf-token"]').getAttribute('content');
  const earlyReturn=await page.request.post('/account/orders/103/return',{form:{_token:token!,reason:'REGRESSION invalid return before collection completed'},maxRedirects:0});
  expect(earlyReturn.status()).toBe(302);
  await page.goto('/account/orders/103/return');await expect(page.getByRole('button',{name:'Submit return request'})).toBeVisible();
  await setStatus(adminPage,order,'completed');
  await history();
  await expect(page.locator('a[href$="/account/orders/103/cancel"]')).toHaveCount(0);
  await expect(page.locator('a[href$="/account/orders/103/return"]')).toBeVisible();
  const csrf=await page.locator('meta[name="csrf-token"]').getAttribute('content');
  const rejected=await page.request.post('/account/orders/103/cancel',{form:{_token:csrf!,reason:'REGRESSION cancellation after completed collection must be rejected'},maxRedirects:0});
  expect(rejected.status()).toBe(302);
  await adminPage.goto('/admin/orders/103/edit');await expect(adminPage.getByRole('combobox',{name:'Order Status'})).toHaveValue('completed');
  await page.screenshot({path:info.outputPath('collection-boundary.png'),fullPage:true});
 } finally {await setStatus(adminPage,order,original);}
});
