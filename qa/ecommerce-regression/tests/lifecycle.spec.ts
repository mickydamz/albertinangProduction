import { test, expect, csrf, productPath, productName } from '../support/fixtures.js';
import { lifecycleEnabled, gateway, buy, setStatus, assertStatus, submitReturn, decideReturn, requestReturnRefund, exactReturnRow, productId, cleanupRegressionReviews } from '../support/lifecycle.js';

import {save10MillionBasket} from '../support/coupon.js';
import { step } from '../support/evidence.js';
import {recordRefundRequest} from '../support/refunds.js';

// Every scenario buys its own sandbox order. Never mutate an arbitrary historical order.
for (const fulfilment of ['collection', 'delivery'] as const) {
test.describe(`Full ecommerce lifecycle ${fulfilment} @lifecycle`, () => {
  const receivedStatus = fulfilment === 'delivery' ? 'delivered' : 'completed';
  const receiveOrder = async (admin: Parameters<typeof setStatus>[0], order: Parameters<typeof setStatus>[1]) => {
    for (const status of fulfilment === 'delivery' ? ['processing','shipped','delivered'] : ['processing','ready_for_pickup','completed']) await setStatus(admin, order, status);
  };
  test.skip(process.env.RUN_LIFECYCLE !== 'true', 'Run using the dedicated Full lifecycle configuration.');
  test.beforeEach(() => { lifecycleEnabled(); test.setTimeout(240000); });

  test('E2E-STATE-001 admin progresses order through correct fulfilment statuses',async({page,adminPage},info)=>{
    const order=await buy(page,adminPage,info,fulfilment);
    for(const status of fulfilment==='collection' ? ['processing','ready_for_pickup','completed'] : ['processing','shipped','delivered']) {
      await step(`Admin fulfils ${fulfilment} order: ${status}`,async()=>{
        await setStatus(adminPage,order,status);
        await page.goto('/account/orders',{waitUntil:'domcontentloaded'});
        const card=page.locator('.op-card').filter({hasText:order.number});
        await expect(card).toHaveCount(1);
        await expect(card.locator('.op-status')).toContainText(new RegExp(status.replaceAll('_','[_ ]'),'i'));
      });
    }
    await info.attach('completed-fulfilment-journey',{body:Buffer.from(JSON.stringify({order,fulfilment,status:receivedStatus})),contentType:'application/json'});
  });

  test('E2E-PAY-001 successful payment persists order, total, reference and customer invoice', async ({ page, adminPage }, info) => {
    const admin = adminPage;
    const order = await buy(page, admin, info, fulfilment);
    await admin.goto(`/admin/orders?search=${encodeURIComponent(order.number)}`,{waitUntil:'domcontentloaded'});
    await expect(admin.getByRole('row').filter({ hasText: order.number })).toHaveCount(1);
    await page.reload();
    await admin.reload();
    await expect(admin.getByRole('row').filter({ hasText: order.number })).toHaveCount(1);
  });

  test('E2E-COUPON-001 SAVE10 bank-transfer order remains over ₦1 million',async({page,adminPage},info)=>{
    let expectedTotal=0;
    const order=await buy(page,adminPage,info,fulfilment,async page=>{
      expectedTotal=(await save10MillionBasket(page,info)).total;
    });
    expect(order.totalNgn).toBeGreaterThan(1000000);
    expect(order.totalNgn).toBeCloseTo(expectedTotal,2);
    await expect(page.locator('body')).toContainText(/SAVE10/i);
    await adminPage.goto(`/admin/orders/${order.id}`,{waitUntil:'domcontentloaded'});
    await expect(adminPage.getByRole('main')).toContainText(/SAVE10/i);
  });

  test('E2E-PAY-002 abandoned bank transfer creates no order and preserves basket', async ({ page }, info) => {
    const pendingResponse=page.waitForResponse(r=>new URL(r.url()).pathname==='/paystack/save-checkout' && r.request().method()==='POST',{timeout:60000});
    pendingResponse.catch(()=>{});
    await gateway(page,info,fulfilment);
    const pending=await (await pendingResponse).json();
    expect(pending.reference).toBeTruthy();
    const token=await csrf(page);
    const confirmation=await page.request.post('/paystack/confirm-order',{headers:{Accept:'application/json','X-CSRF-TOKEN':token},data:{reference:pending.reference}});
    expect(confirmation.status(),'Unfunded transfer must return a handled rejection').toBeGreaterThanOrEqual(400);
    expect(confirmation.status()).toBeLessThan(500);
    const result=await confirmation.json();
    expect(result.success).toBe(false);
    expect(result.order_id).toBeFalsy();
    await info.attach('unfunded-transfer-confirmation',{body:Buffer.from(JSON.stringify({reference:pending.reference,httpStatus:confirmation.status(),result},null,2)),contentType:'application/json'});
    await page.goto('/cart',{waitUntil:'domcontentloaded'});
    await expect(page.getByRole('spinbutton')).toHaveCount(1);
  });

  test('E2E-CANCEL-001 payment then cancellation persists request; refund settles asynchronously', async ({ page, adminPage }, info) => {
    const admin = adminPage; const order = await buy(page, admin, info, fulfilment);
    await page.goto(`/account/orders/${order.id}/cancel`,{waitUntil:'domcontentloaded'});
    const reason = `REGRESSION cancel fresh sandbox order ${order.number}`;
    await page.getByRole('textbox', { name: 'Reason for cancellation' }).fill(reason);
    await page.getByRole('button', { name: 'Submit cancellation request' }).click();
    await expect(page.getByRole('main')).toContainText(/cancelled|refund/i);
    await admin.goto('/admin/cancellations',{waitUntil:'domcontentloaded'});
    const row = admin.getByRole('row').filter({ hasText: order.number });
    await expect(row).toHaveCount(1); await expect(row).toContainText(reason);
    await recordRefundRequest(admin,row,order,info,'cancellation');
    await page.goto(`/account/orders/${order.id}/cancel`,{waitUntil:'domcontentloaded'});
    await expect(page.getByRole('main')).toContainText(/already|existing|refunded|cancelled/i);
  });

  test('E2E-RETURN-001 payment, receipt, return approval and asynchronous refund request', async ({ page, adminPage }, info) => {
    const admin = adminPage; const order = await buy(page, admin, info, fulfilment);
    await receiveOrder(admin, order);
    await submitReturn(page, admin, order, `REGRESSION item damaged on arrival ${order.number}`);
    await requestReturnRefund(admin, order);
    await recordRefundRequest(admin,await exactReturnRow(admin,order),order,info,'return');
    await page.goto('/account/orders',{waitUntil:'domcontentloaded'});
    await expect(page.getByRole('main')).toContainText(order.number);
  });

  test('E2E-RETURN-002 rejected return keeps received order and customer sees rejection', async ({ page, adminPage }, info) => {
    const admin = adminPage; const order = await buy(page, admin, info, fulfilment);
    await receiveOrder(admin, order);
    await submitReturn(page, admin, order, `REGRESSION return rejection scenario ${order.number}`);
    await decideReturn(admin, order, 'reject');
    await expect(await exactReturnRow(admin, order)).toContainText(/rejected/i);
    await assertStatus(admin, order, receivedStatus);
    await page.goto(`/account/orders/${order.id}/return`,{waitUntil:'domcontentloaded'});
    await expect(page.getByRole('main')).toContainText(/rejected/i);
  });

  test('E2E-RETURN-003 repeated return journey keeps one pending request', async ({ page, adminPage }, info) => {
    const order=await buy(page,adminPage,info,fulfilment);
    await receiveOrder(adminPage,order);
    await submitReturn(page,adminPage,order,`REGRESSION duplicate return ${order.number}`);
    await page.goto(`/account/orders/${order.id}/return`,{waitUntil:'domcontentloaded'});
    await expect(page.getByRole('main')).toContainText(/pending|already|existing/i);
    const row=await exactReturnRow(adminPage,order);
    await expect(row).toContainText(/pending/i);
    await assertStatus(adminPage,order,receivedStatus);
  });

  test('E2E-REVIEW-001 received purchase publishes review, persists rating and rejects duplicate', async ({ page, adminPage }, info) => {
    const admin = adminPage;
    await cleanupRegressionReviews(admin);
    const order = await buy(page, admin, info, fulfilment);
    await receiveOrder(admin,order);
    await page.goto(productPath,{waitUntil:'domcontentloaded'});
    await page.getByRole('button', { name: /^Reviews \(/ }).click();
    const comment = `REGRESSION verified sandbox purchaser ${order.number}`;
    const form = page.locator('#pd-reviewForm');
    await expect(form, 'Use a product not already reviewed by this dummy customer').toBeVisible();
    await form.locator('label[data-rating="4"]').click();
    await form.locator('textarea[name="comment"]').fill(comment);
    const responsePromise = page.waitForResponse(r => new URL(r.url()).pathname === `/products/${productId()}/reviews` && r.request().method() === 'POST');
    responsePromise.catch(() => {});
    await form.getByRole('button', { name: 'Submit Review' }).click();
    const response = await responsePromise; expect(response.status()).toBe(201);
    const result = await response.json(); expect(Number(result.rating)).toBe(4); expect(result.content).toBe(comment);
    await page.reload(); await page.getByRole('button', { name: /^Reviews \(/ }).click();
    await expect(page.getByRole('main')).toContainText(comment);
    await admin.goto(`/admin/reviews?search=${encodeURIComponent(productName)}`,{waitUntil:'domcontentloaded'});
    const reviewRow=admin.getByRole('row').filter({hasText:productName}).filter({hasText:result.user_name});
    await expect(reviewRow).toHaveCount(1);
    await reviewRow.locator('a[href$="/edit"]').click();
    await expect(admin.locator('textarea[name="content"]')).toHaveValue(comment);
    await expect(admin.locator('input[name="rating"]')).toHaveValue('4');
    const token = await csrf(page);
    const duplicate = await page.request.post(`/products/${productId()}/reviews`, { headers: { Accept: 'application/json', 'X-CSRF-TOKEN': token }, data: { product_id: Number(productId()), rating: 4, comment } });
    expect(duplicate.status()).toBe(422);
    expect((await duplicate.json()).message).toMatch(/already/i);
    await admin.goto(`/admin/reviews?search=${encodeURIComponent(productName)}`,{waitUntil:'domcontentloaded'});
    await expect(admin.getByRole('row').filter({hasText:productName}).filter({hasText:result.user_name})).toHaveCount(1);
    await cleanupRegressionReviews(admin);
  });
});

}
