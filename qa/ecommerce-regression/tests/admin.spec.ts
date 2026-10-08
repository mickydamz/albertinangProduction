import {test,expect,login} from '../support/fixtures.js';
for (const [path,heading] of [
  ['/admin/orders','Orders'],['/admin/returns','Return'],['/admin/cancellations','Cancellation'],['/admin/reviews','Review'],
  ['/admin/products','Product'],['/admin/categories','Categor'],['/admin/brands','Brand'],['/admin/coupons','Coupon'],
  ['/admin/locations','Location'],['/admin/pickup-points','Pickup'],['/admin/shipping','Shipping'],['/admin/audit','Audit'],
  ['/admin/users','User'],['/admin/tickets','Ticket'],['/admin/transactions','Transaction'],
]) {
  test(`ADMIN-READ ${path} @admin`,async({page})=>{
    await login(page,'ADMIN');
    const response=await page.goto(path); expect(response?.status()).toBe(200);
    await expect(page.locator('body')).toContainText(new RegExp(heading,'i'));
    await expect(page.locator('body')).not.toContainText(/Stack trace|Server Error|SQLSTATE/i);
  });
}
