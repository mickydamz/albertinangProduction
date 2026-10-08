import {test,expect,login} from '../support/fixtures.js';

test('ADMIN coupon: SAVE10 saves unchanged and retains all settings',async({adminPage},info)=>{
  await login(adminPage,'ADMIN');
  await adminPage.goto('/admin/coupons/6/edit');
  const read=()=>adminPage.locator('form').filter({has:adminPage.locator('input[name="code"]')}).evaluate(form=>Object.fromEntries(new FormData(form as HTMLFormElement)));
  const before=await read();
  expect(before.code).toBe('SAVE10');
  const response=adminPage.waitForResponse(r=>r.request().method()==='POST' && new URL(r.url()).pathname==='/admin/coupons/6');
  await adminPage.getByRole('button',{name:'Save changes',exact:true}).click();
  expect((await response).status()).toBe(302);
  await expect(adminPage).toHaveURL(/\/admin\/coupons$/);
  await expect(adminPage.getByText('Coupon SAVE10 updated successfully.',{exact:true})).toBeVisible();
  await adminPage.screenshot({path:info.outputPath('coupon-save-success.png'),fullPage:true});
  await adminPage.goto('/admin/coupons/6/edit');
  const after=await read();
  for(const name of ['code','discount_type','value','max_discount_amount','min_order_amount','max_uses','multi_use','expires_at','is_active']) expect(after[name],name).toBe(before[name]);
});
