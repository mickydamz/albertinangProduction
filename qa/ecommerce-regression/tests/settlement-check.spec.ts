import {test,expect,login,origin} from '../support/fixtures.js';
import {loadFollowups,saveFollowup,validateGateway,followupFile} from '../support/refund-followups.js';
import {step} from '../support/evidence.js';
import type {Page,TestInfo} from '@playwright/test';
const refunds=loadFollowups();
let nextStatusCheck=0;
const delay=(ms:number)=>new Promise(resolve=>setTimeout(resolve,ms));
async function pacedStatus(adminPage:Page,path:string,info:TestInfo) {
  await delay(Math.max(0,nextStatusCheck-Date.now()));
  nextStatusCheck=Date.now()+6500; // Admin endpoint allows 10 requests per minute.
  let response=await adminPage.request.get(path,{timeout:45000});
  if(response.status()===429) {
    const value=response.headers()['retry-after'];
    const seconds=Number(value);
    const waitMs=Number.isFinite(seconds)&&seconds>0 ? seconds*1000 : Math.max(0,Date.parse(value||'')-Date.now())||60000;
    info.annotations.push({type:'RATE_LIMIT_RETRY',description:`HTTP 429; retrying this read-only check after ${Math.ceil(waitMs/1000)} seconds.`});
    await delay(Math.min(waitMs+1000,65000));
    nextStatusCheck=Date.now()+6500;
    response=await adminPage.request.get(path,{timeout:45000});
  }
  return response;
}
if(!refunds.length) {
  test('SETTLEMENT prerequisite: saved refunds from configuration 08',async({adminPage},info)=>{
    test.skip(process.env.RUN_SETTLEMENT_CHECK!=='true','Choose configuration 10 explicitly.');
    await step('Verify staging refund service before checking saved records',async()=>{
      await login(adminPage,'ADMIN');
      const response=await adminPage.request.get('/admin/paystack/refund-test-readiness');
      expect(response.status(),'The staging refund verification service must be available.').toBe(200);
      expect(await response.json()).toMatchObject({test_mode:true,refund_status_endpoint:true});
    });
    const reason=`NO REFUNDS TO CHECK on ${origin}. Run configuration 08 on this site first. Saved records: ${followupFile}. No payment or refund was created; settlement is not verified.`;
    await info.attach('settlement-prerequisite',{body:Buffer.from(JSON.stringify({origin,followupFile,savedRefunds:0,status:'not-run',reason},null,2)),contentType:'application/json'});
    info.annotations.push({type:'NO_SAVED_REFUNDS',description:reason});
    test.skip(true,reason);
  });
}
for(const entry of refunds) {
  test(`SETTLEMENT ${entry.fulfilment} ${entry.kind} order ${entry.order.number}`,async({adminPage},info)=>{
    test.skip(process.env.RUN_SETTLEMENT_CHECK!=='true','Choose configuration 10 explicitly.');
    test.setTimeout(180000);await login(adminPage,'ADMIN');
    await step('Read saved refund status with rate-limit pacing; do not create a payment or refund',async()=>{
      const savedRefundId=entry.refundId;
      const response=await pacedStatus(adminPage,`/admin/orders/${entry.order.id}/paystack-refund-status`,info);
      const body=await response.json().catch(()=>({message:'Non-JSON response'}));
      const observation={at:new Date().toISOString(),http:response.status(),body};
      entry.lastCheckedAt=observation.at;entry.lastObservation=observation;
      saveFollowup(entry);
      await info.attach('paystack-settlement-observation',{body:Buffer.from(JSON.stringify({entry,observation},null,2)),contentType:'application/json'});
      expect([200,202,429,502,503],'Staging refund endpoint must be deployed, admin authenticated and test mode configured.').toContain(response.status());
      if(response.status()!==200) {
        info.annotations.push({type:'UNRESOLVED',description:`No confirmed gateway status: HTTP ${response.status()}${response.status()===429 ? " (server rate limit)" : ""}. Recheck later; do not issue another refund.`});
        test.skip(true,'UNRESOLVED: gateway status unavailable; no settlement claimed.');return;
      }
      const legacy = new Date(entry.createdAt) < new Date('2026-10-05T00:00:00Z') && body.application?.order_status==='refunded';
      validateGateway(body,entry.order,savedRefundId,legacy);
      if(legacy) info.annotations.push({type:'LEGACY_ORDER_STATUS',description:'Historical refund from before the separate order/refund workflow retains its existing refunded order status.'});
      entry.refundId=String(body.gateway.refund_id);
      if(['failed','needs-attention'].includes(body.gateway.status)) {
        entry.outcome='failed';saveFollowup(entry);
        throw new Error(`Paystack refund ${entry.refundId}: ${body.gateway.status}. Admin investigation required; this check does not retry refunds.`);
      }
      if(body.gateway.status!=='processed'||body.application.refund_status!=='processed') {
        entry.outcome='pending';saveFollowup(entry);
        info.annotations.push({type:'PENDING',description:`Paystack=${body.gateway.status}; application=${body.application.refund_status}. Recheck later. This is not a settlement pass.`});
        test.skip(true,'PENDING: awaiting Paystack processing or application reconciliation.');return;
      }
      entry.outcome='processed';saveFollowup(entry);
      expect(body.application.processed_at).toBeTruthy();expect(body.application.order_status).toBe(legacy ? 'refunded' : entry.kind==='cancellation' ? 'cancelled' : entry.fulfilment==='collection' ? 'completed' : 'delivered');
      info.annotations.push({type:'PROCESSED',description:`Confirmed actual Paystack sandbox result for refund ${entry.refundId}; customer bank receipt is not independently verified.`});
    });
  });
}
