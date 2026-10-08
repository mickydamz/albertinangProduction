import {expect} from './fixtures.js';
import {step} from './evidence.js';
import type {Page,TestInfo} from '@playwright/test';
export async function save10MillionBasket(page: Page, info: TestInfo) {
  return step('SAVE10: basket over ₦1 million, capped discount and payable total',async()=>{
    await expect(page.getByRole('spinbutton')).toHaveCount(1);
    const ngn = async(id:string)=>Number(await page.locator(id).getAttribute('data-ngn'));
    await expect.poll(()=>ngn('#summarySubtotal')).toBeGreaterThan(0);
    const unit=(await ngn('#summarySubtotal'))/Number(await page.getByRole('spinbutton').inputValue());
    const quantity=Math.max(2,Math.ceil(1200000/unit));
    await page.getByRole('spinbutton').fill(String(quantity));
    await page.getByRole('spinbutton').press('Tab');
    await expect.poll(()=>ngn('#summarySubtotal')).toBeCloseTo(unit*quantity,2);
    const subtotal=await ngn('#summarySubtotal');
    const before=await ngn('#summaryTotal');
    expect(subtotal).toBeGreaterThan(1000000);
    expect(before).toBeGreaterThan(1000000);
    const pending=page.waitForResponse(r=>new URL(r.url()).pathname==='/api/coupons/validate' && r.request().method()==='POST');
    pending.catch(()=>{});
    await page.locator('#couponInput').fill('SAVE10');
    await page.locator('#couponBtn').click();
    const response=await step('SAVE10: server validates coupon for high-value basket',()=>pending);
    const result=await response.json();
    await info.attach('SAVE10-high-value-validation',{body:Buffer.from(JSON.stringify({quantity,subtotalNgn:subtotal,totalBeforeNgn:before,expectedDiscountNgn:5000,httpStatus:response.status(),request:response.request().postDataJSON(),result},null,2)),contentType:'application/json'});
    if(!result.success) info.annotations.push({type:'issue',description:'SAVE10 validation rejected: '+String(result.message)});
    await step('SAVE10: server accepts coupon for this customer',async()=>{
      expect(response.ok(),JSON.stringify(result)).toBe(true);
      expect(result.success,JSON.stringify(result)).toBe(true);
    });
    expect(result.code).toBe('SAVE10');
    expect(Number(result.discount_ngn),'10% coupon is capped at ₦5,000').toBe(5000);
    await expect(page.locator('#summaryCouponCode')).toHaveText('SAVE10');
    await expect(page.locator('#summaryCouponRow')).toBeVisible();
    await expect.poll(()=>ngn('#summaryTotal')).toBeCloseTo(before-5000,2);
    const total=await ngn('#summaryTotal');
    expect(total).toBeGreaterThan(1000000);
    return {subtotal,total,discount:5000,quantity};
  });
}
