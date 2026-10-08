import {test,expect,login} from '../support/fixtures.js';
import {buy,lifecycleEnabled,setStatus,submitReturn,requestReturnRefund} from '../support/lifecycle.js';
import {step} from '../support/evidence.js';
import type {Page,TestInfo} from '@playwright/test';
import type {PaidOrder} from '../support/lifecycle.js';
import {saveFollowup,validateGateway,type RefundFollowup} from '../support/refund-followups.js';

async function initiation(admin:Page,entry:RefundFollowup,info:TestInfo) {
  const observations:unknown[]=[];
  try {
    await step('Paystack: verify refund accepted without waiting for completion',async()=>{
      const response=await admin.request.get(`/admin/orders/${entry.order.id}/paystack-refund-status`,{timeout:45000});
      const body=await response.json().catch(()=>({message:'Non-JSON response'}));
      observations.push({at:new Date().toISOString(),http:response.status(),body});
      entry.lastCheckedAt=new Date().toISOString();entry.lastObservation=observations;
      saveFollowup(entry);
      expect(response.status(),'Paystack must return an accepted refund ID. An unknown outcome is not acceptance; use configuration 10 to recheck this saved order, never create another refund.').toBe(200);
      validateGateway(body,entry.order);
      entry.refundId=String(body.gateway.refund_id);
      entry.outcome=['failed','needs-attention'].includes(body.gateway.status)?'failed':body.gateway.status==='processed'&&body.application.refund_status==='processed'?'processed':'pending';
      saveFollowup(entry);
      expect(['pending','processing','processed'],'Paystack must accept this sandbox refund.').toContain(body.gateway.status);
      info.annotations.push({type:entry.outcome==='processed'?'refund processed':'refund accepted; settlement pending',description:`Saved order ${entry.order.number}, refund ${entry.refundId}. Recheck using configuration 10; this test does not wait for processing.`});
    });
  } finally {
    await info.attach('paystack-refund-initiation',{body:Buffer.from(JSON.stringify({entry,observations},null,2)),contentType:'application/json'});
  }
}

for(const fulfilment of ['collection','delivery'] as const) {
  for(const kind of ['cancellation','return'] as const) {
    test(`STAGING-REFUND ${fulfilment} ${kind}: real test bank transfer and refund acceptance`,async({page,adminPage},info)=>{
      test.skip(process.env.RUN_CRITICAL_STAGING!=='true','Choose configuration 08 explicitly.');
      lifecycleEnabled();test.setTimeout(5*60*1000);
      await login(adminPage,'ADMIN');
      await step('Staging prerequisite: admin-only Paystack status endpoint is deployed',async()=>{
        const response=await adminPage.request.get('/admin/paystack/refund-test-readiness');
        expect(response.status(),'Deploy the local refund fix, migration and test-mode verification endpoint to staging before running 08. No payment has been made.').toBe(200);
        expect(await response.json()).toMatchObject({test_mode:true,refund_status_endpoint:true});
      });
      const order=await buy(page,adminPage,info,fulfilment);
      // Check endpoint existence and key mode before creating a refund request.
      const prerequisite=await adminPage.request.get(`/admin/orders/${order.id}/paystack-refund-status`);
      expect(prerequisite.status(),'Deploy the local refund fix/status endpoint; a test key must already be configured on staging.').toBe(202);
      const followup:RefundFollowup={order,fulfilment,kind,createdAt:new Date().toISOString(),outcome:'requested'};
      // Save before the action so an uncertain response never loses the order reference.
      saveFollowup(followup);
      if(kind==='cancellation') {
        // Cancel at the last valid status before receipt, never after fulfilment.
        for(const status of fulfilment==='collection' ? ['processing','ready_for_pickup'] : ['processing']) await setStatus(adminPage,order,status);
        await page.goto(`/account/orders/${order.id}/cancel`,{waitUntil:'domcontentloaded'});
        await page.getByRole('textbox',{name:'Reason for cancellation'}).fill(`REGRESSION delayed staging refund ${order.number}`);
        await page.getByRole('button',{name:'Submit cancellation request'}).click();
      } else {
        for(const status of fulfilment==='collection' ? ['processing','ready_for_pickup','completed'] : ['processing','shipped','delivered']) await setStatus(adminPage,order,status);
        await submitReturn(page,adminPage,order,`REGRESSION delayed staging return refund ${order.number}`);
        await requestReturnRefund(adminPage,order);
      }
      await initiation(adminPage,followup,info);
    });
  }
}
