import {test,expect,requireWrites} from '../support/fixtures.js';
import {pickupCheckout} from '../support/checkout.js';
test('PAY-003 @sandbox opens verified sandbox gateway only with complete email',async({page},info)=>{
  requireWrites();
  test.skip(process.env.RUN_SANDBOX_GATEWAY !== 'true','Sandbox gateway run is opt-in');
  await pickupCheckout(page);
  // A missing/readonly blank email previously blocked payment in the in-app browser.
  // Never bypass that prerequisite: this assertion must pass before gateway opening.
  await expect(page.getByRole('textbox',{name:'Email address',exact:true})).toHaveValue(process.env.CUSTOMER_EMAIL!);
  await page.getByRole('button',{name:'Paystack',exact:false}).click();
  await info.attach('payment-prerequisites',{body:await page.screenshot(),contentType:'image/png'});
  await page.getByRole('button',{name:'Pay with Paystack'}).click();
  await expect.poll(()=>page.frames().map(f=>f.url()).filter(u=>/paystack/i.test(u)).length).toBeGreaterThan(0);
  const frame=page.frames().find(f=>/checkout.paystack.com/i.test(f.url()));
  expect(frame,'Paystack checkout iframe').toBeTruthy();
  await expect(frame!.locator('body')).toContainText(/test/i);
  await info.attach('sandbox-checkout',{body:Buffer.from(await frame!.locator('body').innerText()),contentType:'text/plain'});
  // Gateway handoff test only. No successful charge/order assertion here.
});
