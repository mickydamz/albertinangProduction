import {test,expect,login} from '../support/fixtures.js';
import {checkout} from '../support/checkout.js';
import {save10MillionBasket} from '../support/coupon.js';
test('COUPON-001 SAVE10 applies capped discount to basket over ₦1 million',async({page,adminPage},info)=>{
  test.setTimeout(120000);
  await login(adminPage,'ADMIN');
  await adminPage.goto('/admin/coupons?search=SAVE10',{waitUntil:'domcontentloaded'});
  const row=adminPage.getByRole('row').filter({hasText:'SAVE10'});
  await expect(row).toHaveCount(1);
  await info.attach('SAVE10-current-admin-settings',{body:Buffer.from(await row.innerText()),contentType:'text/plain'});
  await checkout(page,'collection',async page=>{
    const values=await save10MillionBasket(page,info);
    await page.locator('#couponBtn').click();
    await expect(page.locator('#summaryCouponRow')).toBeHidden();
    await expect.poll(async()=>Number(await page.locator('#summaryTotal').getAttribute('data-ngn'))).toBeCloseTo(values.total+values.discount,2);
    await save10MillionBasket(page,info);
  });
  await expect(page.getByRole('textbox',{name:'Email address',exact:true})).toHaveValue(process.env.CUSTOMER_EMAIL!);
});
