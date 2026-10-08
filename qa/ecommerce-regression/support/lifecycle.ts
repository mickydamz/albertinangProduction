import { expect, test, login, origin, productPath, productName } from './fixtures.js';
import { step, clean } from './evidence.js';
import { checkout as prepareCheckout, type Fulfilment } from './checkout.js';
import type { Page, Frame, TestInfo, Locator } from '@playwright/test';

export type PaidOrder = { id: number; number: string; reference: string; totalNgn: number };
export function lifecycleEnabled() {
  test.skip(process.env.RUN_LIFECYCLE !== 'true', 'Choose Full lifecycle tests to create fresh sandbox orders.');
  if (process.env.ALLOW_TEST_WRITES !== 'true' || process.env.PAYMENT_TEST_MODE !== 'true')
    throw new Error('Lifecycle requires ALLOW_TEST_WRITES=true and PAYMENT_TEST_MODE=true.');
}
export async function gateway(page: Page, info: TestInfo, fulfilment: Fulfilment = 'collection', beforePayment?: (page: Page) => Promise<void>): Promise<Frame> {
  await step(`Checkout: sign in, add product and select ${fulfilment}`,()=>prepareCheckout(page, fulfilment, beforePayment));
  // Do not bypass the blank read-only email condition previously reported.
  await step('Payment prerequisite: authenticated email is populated',async()=>{
    await expect(page.getByRole('textbox', { name: 'Email address', exact: true })).toHaveValue(process.env.CUSTOMER_EMAIL!);
  });
  await page.getByRole('button', { name: 'Paystack', exact: false }).click();
  await page.getByRole('button', { name: 'Pay with Paystack' }).click();
  await expect.poll(() => page.frames().filter(f => /^https:\/\/checkout\.paystack\.com\//.test(f.url())).length).toBeGreaterThan(0);
  const frame = page.frames().find(f => /^https:\/\/checkout\.paystack\.com\//.test(f.url()))!;
  await step('Paystack: verify sandbox marker before selecting transfer',async()=>{
    await expect.poll(async () => {
      const text = await frame.locator('body').textContent() || '';
      if (/could not start this transaction/i.test(text)) throw new Error('Paystack initialization failed: '+clean(text));
      return /Use any of the options below to test the payment flow|test mode|Test\s*Cancel Payment/i.test(text)
        || await frame.getByText('Test', {exact:true}).isVisible();
    }, {message:'Paystack must expose its sandbox simulator or test-mode marker'}).toBe(true);
  });
  await info.attach('sandbox-before-charge', { body: await page.screenshot(), contentType: 'image/png' });
  await step('Paystack: select bank transfer',async()=>{
    const mobileTransfer=frame.getByText('Pay with Transfer',{exact:true});
    if(await mobileTransfer.isVisible()) await mobileTransfer.click();
    else await frame.getByText('Transfer',{exact:true}).click();
  });
  await step('Paystack transfer: verify sandbox account and amount',async()=>{
    await expect(frame.locator('body')).toContainText('Test Bank');
    await expect(frame.getByRole('button',{name:"I've sent the money",exact:true})).toBeVisible();
  });
  return frame;
}
export async function submitTransfer(frame: Frame) {
  return step('Paystack test-mode bank transfer: request gateway confirmation',async()=>{
    const text=await frame.locator('body').innerText();
    if(process.env.PAYMENT_TEST_MODE !== 'true' || !text.includes('Test Bank')) throw new Error('Direct transfer confirmation is restricted to Paystack Test Bank in test mode');
    // Requests sandbox confirmation; buy() requires the server-verified
    // payment response and a persisted paid order before passing.
    await frame.getByRole('button',{name:"I've sent the money",exact:true}).click();
  });
}
export async function buy(page: Page, admin: Page, info: TestInfo, fulfilment: Fulfilment = 'collection', beforePayment?: (page: Page) => Promise<void>): Promise<PaidOrder> {
  const checkoutSaved=page.waitForResponse(r=>new URL(r.url()).origin===origin && new URL(r.url()).pathname==='/paystack/save-checkout' && r.request().method()==='POST',{timeout:60000});
  checkoutSaved.catch(()=>{});
  const frame = await gateway(page, info, fulfilment, beforePayment);
  const checkoutResponse=await step('Checkout: server saves pending payment reference',()=>checkoutSaved);
  expect(checkoutResponse.ok()).toBe(true);
  const checkout=await checkoutResponse.json();
  expect(checkout.saved).toBe(true);
  await info.attach('fulfilment-checkout', {body:Buffer.from(JSON.stringify({fulfilment, pendingCheckout:checkout, submittedCheckout:checkoutResponse.request().postDataJSON()},null,2)),contentType:'application/json'});
  const saved = page.waitForResponse(r => new URL(r.url()).origin === origin && new URL(r.url()).pathname === '/paystack/confirm-order' && r.request().method() === 'POST', { timeout: 90000 });
  // Consume the promise if the transfer action fails; avoid unhandled timeout rejections.
  saved.catch(() => {});
  await submitTransfer(frame);
  const response = await step('Payment outcome: gateway accepts charge and server saves order',()=>Promise.race([saved, frame.getByText(/Your transaction was declined|insufficient funds|payment failed/i).first().waitFor({state:'visible',timeout:90000}).then(async()=>{ throw new Error('Paystack rejected the sandbox charge: '+clean(await frame.locator('body').innerText())); }).catch(error=>{ if(frame.isDetached()) return saved; throw error; })]));
  const data = await response.json();
  await info.attach('payment-order-confirmation',{body:Buffer.from(clean(JSON.stringify({httpStatus:response.status(),pendingReference:checkout.reference,submittedReference:response.request().postDataJSON()?.reference,result:data},null,2))),contentType:'application/json'});
  expect(response.ok(), 'Payment must persist an order successfully: '+JSON.stringify(data)).toBe(true);
  expect(data.success).toBe(true);
  const payload = response.request().postDataJSON();
  const order: PaidOrder = { id: Number(data.order_id), number: data.order_number, reference: data.reference, totalNgn: Number(checkout.total_ngn) };
  expect(order.id).toBeGreaterThan(0); expect(order.number).toMatch(/^ALB-/); expect(order.reference).toBeTruthy();
  expect(order.reference).toBe(payload.reference); expect(order.reference).toBe(checkout.reference); expect(order.totalNgn).toBeGreaterThan(0);
  await info.attach('created-order', { body: Buffer.from(JSON.stringify(order, null, 2)), contentType: 'application/json' });
  await login(admin, 'ADMIN');
  await step('Admin: saved order is paid',()=>assertStatus(admin,order,'paid'));
  await admin.goto(`/admin/orders/${order.id}`,{waitUntil:'domcontentloaded'});
  await expect(admin.getByRole('main')).toContainText(order.reference);
  await expect(admin.getByRole('main')).toContainText(order.number);
  await expect(admin.getByRole('main')).toContainText(order.totalNgn.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
  await step('Customer invoice: saved order renders successfully and survives reload',async()=>{
    const invoice=await page.goto(`/account/orders/${order.id}/invoice`,{waitUntil:'domcontentloaded'});
    expect(invoice?.status(),'Customer invoice must return HTTP 200').toBe(200);
    await expect(page.locator('body')).toContainText(order.number);
    const reloaded=await page.reload();
    expect(reloaded?.status(),'Customer invoice reload must return HTTP 200').toBe(200);
    await expect(page.locator('body')).toContainText(order.number);
  });
  return order;
}
export async function assertStatus(admin: Page, order: PaidOrder, status: string) {
  return step('Verify persisted order status',async()=>{
  const r = await admin.goto(`/admin/orders/${order.id}/edit`,{waitUntil:'domcontentloaded'}); expect(r?.status()).toBe(200);
  await expect(admin.getByRole('combobox', { name: 'Order Status' })).toHaveValue(status);
  });
}
export async function setStatus(admin: Page, order: PaidOrder, status: string) {
  return step('Admin change order status',async()=>{
  await admin.goto(`/admin/orders/${order.id}/edit`,{waitUntil:'domcontentloaded'});
  await admin.getByRole('combobox', { name: 'Order Status' }).selectOption(status);
  await admin.getByRole('button', { name: 'Save Changes' }).click();
  await assertStatus(admin, order, status);
  });
}
export async function submitReturn(page: Page, admin: Page, order: PaidOrder, reason: string) {
  return step('Customer submit return and verify pending record',async()=>{
  await page.goto(`/account/orders/${order.id}/return`,{waitUntil:'domcontentloaded'});
  await page.getByRole('textbox', { name: 'Reason for return' }).fill(reason);
  await page.getByRole('button', { name: 'Submit return request' }).click();
  await admin.goto('/admin/returns',{waitUntil:'domcontentloaded'});
  const row = admin.getByRole('row').filter({ hasText: order.number });
  await expect(row).toHaveCount(1); await expect(row).toContainText(reason); await expect(row).toContainText(/pending/i);
  });
}
export async function decideReturn(admin: Page, order: PaidOrder, action: 'approve' | 'receive' | 'inspect' | 'refund' | 'reject') {
  return step(`Admin return action: ${action}`, async () => {
    const row = await exactReturnRow(admin, order);
    await row.getByRole('button', { name: /Review/ }).click();
    const form = admin.locator('.modal.show');
    await expect(form).toHaveCount(1);
    const select = form.getByRole('combobox', { name: 'Next action', exact: true });
    await expect(select).toHaveCount(1);
    await select.selectOption(action);
    if (action === 'approve') {
      await form.locator('textarea[name="return_instructions"]').fill('REGRESSION: return the dummy goods to the staging collection desk with the order reference.');
    }
    if (action === 'inspect' || action === 'reject') {
      await form.locator('textarea[name="admin_notes"]').fill(action === 'inspect'
        ? 'REGRESSION: dummy goods received and inspection passed.'
        : 'REGRESSION: dummy return rejected with a customer explanation.');
    }
    const saved = admin.waitForResponse(r => /^\/admin\/returns\/\d+\/review$/.test(new URL(r.url()).pathname)
      && ['POST', 'PATCH'].includes(r.request().method()), { timeout: 15000 });
    saved.catch(() => {});
    await form.getByRole('button', { name: /Save Decision/i }).click();
    const response = await step(`Return ${action}: server accepts submitted action`, () => saved);
    expect(response.status()).toBeLessThan(400);
    const stageLabels = { approve: 'Return approved', receive: 'Goods received', inspect: 'Inspection completed', refund: 'Return refund requested', reject: 'Return rejected' };
    // A redirect alone is not proof that validation passed or the stage persisted.
    await expect(await exactReturnRow(admin, order)).toContainText(stageLabels[action]);
  });
}
export async function requestReturnRefund(admin: Page, order: PaidOrder) {
  for (const action of ['approve', 'receive', 'inspect', 'refund'] as const) {
    await decideReturn(admin, order, action);
  }
}
export async function exactReturnRow(admin: Page, order: PaidOrder): Promise<Locator> {
  await admin.goto('/admin/returns',{waitUntil:'domcontentloaded'});
  const row = admin.getByRole('row').filter({ hasText: order.number });
  await expect(row).toHaveCount(1); return row;
}
export function productId() {
  const id = productPath.match(/^\/product\/(\d+)$/)?.[1];
  if (!id) throw new Error('Lifecycle reviews need PRODUCT_PATH=/product/<numeric ID>.');
  return id;
}

export async function cleanupRegressionReviews(admin: Page) {
  return step('Review fixture: remove only earlier regression reviews for this product',async()=>{
    await login(admin,'ADMIN');
    await admin.goto(`/admin/reviews?search=${encodeURIComponent(productName)}`,{waitUntil:'domcontentloaded'});
    const rows=admin.getByRole('row').filter({hasText:productName});
    const edits=await rows.locator('a[href$="/edit"]').evaluateAll(xs=>xs.map(x=>x.getAttribute('href')!));
    for(const edit of edits) {
      await admin.goto(edit,{waitUntil:'domcontentloaded'});
      const content=await admin.locator('textarea[name="content"]').inputValue();
      // Never remove a customer review without our test-specific marker.
      if(!/^REGRESSION verified sandbox purchaser ALB-/.test(content)) continue;
      const id=new URL(edit).pathname.match(/\/reviews\/(\d+)\/edit$/)?.[1];
      if(!id) throw new Error('Unexpected regression review edit route');
      await admin.goto(`/admin/reviews?search=${encodeURIComponent(productName)}`,{waitUntil:'domcontentloaded'});
      const form=admin.locator(`form[action$="/admin/reviews/${id}"]`);
      await expect(form).toHaveCount(1);
      admin.once('dialog',dialog=>dialog.accept());
      await form.locator('button[type="submit"]').click();
      await admin.goto(`/admin/reviews?search=${encodeURIComponent(productName)}`,{waitUntil:'domcontentloaded'});
      await expect(admin.locator(`form[action$="/admin/reviews/${id}"]`)).toHaveCount(0);
    }
  });
}
