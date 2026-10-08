import {expect} from './fixtures.js';
import {step} from './evidence.js';
import type {Page,TestInfo,Locator} from '@playwright/test';
import type {PaidOrder} from './lifecycle.js';
/** Application acknowledgement is separate from asynchronous gateway settlement. */
export async function recordRefundRequest(admin:Page,row:Locator,order:PaidOrder,info:TestInfo,kind:'cancellation'|'return') {
  await step('Refund request: application acknowledges request; settlement is separate',async()=>{
    await expect(row).toContainText(/approved|pending|processing|refunded/i);
    const requestText=await row.innerText();
    await admin.goto(`/admin/orders/${order.id}`,{waitUntil:'domcontentloaded'});
    const orderText=await admin.getByRole('main').innerText();
    expect(requestText+' '+orderText,'Refund rejection/manual handling must not pass as initiation').not.toMatch(/automatic refund failed|could not process.*refund|please process.*manually/i);
    const refundId=orderText.match(/(?:refund[_ -]?id|refund reference)\s*[:#]?\s*([A-Za-z0-9_-]+)/i)?.[1];
    await info.attach('refund-follow-up',{body:Buffer.from(JSON.stringify({order,kind,observedAt:new Date().toISOString(),requestText,refundId:refundId||null,applicationAcknowledged:true,gatewaySettlementVerified:false,followUp:'Check gateway processed outcome separately. Pending/processing is not a regression timeout or proof of settlement.'},null,2)),contentType:'application/json'});
    info.annotations.push({type:'refund settlement unverified',description:'Application refund request recorded. Paystack may remain pending/processing; settlement requires a later processed outcome. REFUND-PROOF-001 evidence gap is retained.'});
  });
}
