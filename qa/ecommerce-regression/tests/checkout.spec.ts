import {test,expect} from '../support/fixtures.js';
import {pickupCheckout, checkout} from '../support/checkout.js';
test('PAY-001 payment email is populated from authenticated customer',async({page})=>{
  await pickupCheckout(page);
  await expect(page.getByRole('textbox',{name:'Email address',exact:true})).toHaveValue(process.env.CUSTOMER_EMAIL!);
});
test('PAY-002 Paystack payment choice is reachable without submitting payment',async({page})=>{
  await pickupCheckout(page);
  await page.getByRole('button',{name:'Paystack',exact:false}).click();
  await expect(page.getByRole('button',{name:'Pay with Paystack'})).toBeVisible();

});

test('PAY-003 home delivery address and area reach payment without submitting',async({page})=>{
  await checkout(page,'delivery');
  await expect(page.getByRole('textbox',{name:'Email address',exact:true})).toHaveValue(process.env.CUSTOMER_EMAIL!);
  await page.getByRole('button',{name:'Paystack',exact:false}).click();
  await expect(page.getByRole('button',{name:'Pay with Paystack'})).toBeVisible();
});
