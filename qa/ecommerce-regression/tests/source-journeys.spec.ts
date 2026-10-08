import {test,expect,login,addProduct,csrf,requireWrites} from '../support/fixtures.js';
import {step} from '../support/evidence.js';
const amount=async(page:import('@playwright/test').Page,id:string)=>Number(await page.locator(id).getAttribute('data-ngn'));
async function start(page:import('@playwright/test').Page){await login(page);await addProduct(page);await page.goto('/checkout',{waitUntil:'domcontentloaded'});await expect.poll(()=>amount(page,'#summarySubtotal')).toBeGreaterThan(0);}
async function delivery(page:import('@playwright/test').Page){
  await page.getByRole('button',{name:'Home Delivery'}).click();
  await page.locator('#shippingAddressInput').fill('REGRESSION DUMMY 12 Ogui Road, Enugu');
  await page.getByRole('combobox',{name:'State',exact:true}).selectOption({label:'Enugu State'});
  await page.getByRole('combobox',{name:'Delivery Area',exact:true}).selectOption({label:'Ogui Road'});
}
test('DELIVERY-UI truck fee matches selected area and totals survive refresh',async({page},info)=>{
  await start(page);await delivery(page);
  const selected=page.locator('#deliveryLocationSelect option:checked');
  const expected=Number(await selected.getAttribute('data-truck-fee'));
  expect(expected).toBeGreaterThan(0);
  await step('Shipping: truck product uses selected area truck fee',()=>expect.poll(()=>amount(page,'#summaryShipping')).toBe(expected));
  const subtotal=await amount(page,'#summarySubtotal'),installation=await amount(page,'#summaryInstallation');
  await expect.poll(()=>amount(page,'#summaryTotal')).toBeCloseTo(subtotal+installation+expected,2);
  await info.attach('delivery-pricing',{body:Buffer.from(JSON.stringify({subtotal,installation,shipping:expected,total:subtotal+installation+expected})),contentType:'application/json'});
  await page.reload({waitUntil:'domcontentloaded'});
  await expect(page.locator('#shippingAddressInput')).toHaveValue('REGRESSION DUMMY 12 Ogui Road, Enugu');
  await expect(page.locator('#deliveryLocationSelect option:checked')).toHaveText('Ogui Road');
  await expect.poll(()=>amount(page,'#summaryTotal')).toBeCloseTo(subtotal+installation+expected,2);
});
test('DELIVERY-UI switching to collection removes shipping and back restores fee',async({page})=>{
  await start(page);await delivery(page);const fee=Number(await page.locator('#deliveryLocationSelect option:checked').getAttribute('data-truck-fee'));
  await expect.poll(()=>amount(page,'#summaryShipping')).toBe(fee);
  await page.getByRole('button',{name:'Collect in Store'}).click();
  await expect.poll(()=>amount(page,'#summaryShipping')).toBe(0);
  await expect.poll(()=>amount(page,'#summaryTotal')).toBeCloseTo((await amount(page,'#summarySubtotal'))+(await amount(page,'#summaryInstallation')),2);
  await page.getByRole('button',{name:'Home Delivery'}).click();await expect.poll(()=>amount(page,'#summaryShipping')).toBe(fee);
});
test('DELIVERY-UI missing address blocks payment',async({page})=>{
  await start(page);await delivery(page);await page.locator('#shippingAddressInput').fill('');
  await page.getByRole('button',{name:/enter.*address/i}).click();
  await expect(page.getByRole('heading',{name:'Delivery Address Required'})).toBeVisible();
  await expect(page.getByRole('button',{name:'Pay with Paystack'})).toBeHidden();
});
for(const [label,data,field] of [['blank ticket',{subject:'',description:'',priority:'medium'},'subject'],['invalid priority',{subject:'REGRESSION invalid ticket',description:'Dummy negative validation',priority:'urgent'},'priority']] as const) test(`SUPPORT-VALIDATION ${label}`,async({page})=>{
  await login(page);const token=await csrf(page);const r=await page.request.post('/tickets',{headers:{Accept:'application/json','X-CSRF-TOKEN':token},data});expect(r.status()).toBe(422);expect((await r.json()).errors).toHaveProperty(field);
});
test('ACCOUNT-VALIDATION mismatched new passwords cannot change login',async({page})=>{
  await login(page);const token=await csrf(page);
  const r=await page.request.post('/account/change-password',{headers:{Accept:'application/json','X-CSRF-TOKEN':token},data:{current_password:'REGRESSION deliberately incorrect',password:'RegressionDummyOnly123',password_confirmation:'DifferentDummyOnly123'}});
  expect(r.status()).toBe(422);expect((await r.json()).errors).toHaveProperty('password');
});
test('SUPPORT-E2E dummy ticket creation, reply, edit and admin persistence',async({page,adminPage},info)=>{
  requireWrites();test.setTimeout(120000);await login(page);
  const subject=`REGRESSION DUMMY support ${Date.now()}`;
  await step('Support: customer creates dummy ticket',async()=>{
    await page.goto('/tickets/create',{waitUntil:'domcontentloaded'});
    await page.locator('input[name="subject"]').fill(subject);
    await page.locator('textarea[name="description"]').fill('REGRESSION DUMMY support journey. No real customer issue.');

    await page.locator('form[action$="/tickets"] button[type="submit"]').click();
    await expect(page.getByRole('main')).toContainText(subject);
  });
  const href=await page.getByRole('main').locator('a[href]').filter({hasText:subject}).first().getAttribute('href');
  expect(href).toBeTruthy();await page.goto(href!,{waitUntil:'domcontentloaded'});
  const id=new URL(page.url()).pathname.match(/\/tickets\/(\d+)$/)?.[1];expect(id).toBeTruthy();
  await info.attach('created-dummy-ticket',{body:Buffer.from(JSON.stringify({id,subject})),contentType:'application/json'});
  const message='REGRESSION DUMMY customer reply persisted';
  await step('Support: reply persists after refresh',async()=>{
    await page.locator('textarea[name="message"]').fill(message);await page.getByRole('button',{name:'Send Reply'}).click();
    await page.reload({waitUntil:'domcontentloaded'});await expect(page.getByRole('main')).toContainText(message);
  });
  await login(adminPage,'ADMIN');await adminPage.goto(`/admin/tickets/${id}`,{waitUntil:'domcontentloaded'});
  await expect(adminPage.locator('body')).toContainText(subject);await expect(adminPage.locator('body')).toContainText(message);
  await step('Support: edited description persists',async()=>{
    const response=await page.goto(`/tickets/${id}/edit`,{waitUntil:'domcontentloaded'});
    const errorHeading=await page.getByRole('heading').allTextContents();
    await info.attach('ticket-edit-http',{body:Buffer.from(JSON.stringify({id,status:response?.status(),headings:errorHeading})),contentType:'application/json'});
    expect(response?.status(),errorHeading.join(' | ')).toBe(200);
    await page.locator('textarea[name="description"]').fill('REGRESSION DUMMY edited description');
    await page.getByRole('button',{name:'Save Changes'}).click();
    await page.goto(`/tickets/${id}`,{waitUntil:'domcontentloaded'});await expect(page.getByRole('main')).toContainText('REGRESSION DUMMY edited description');
  });
});
