import {readFileSync} from 'node:fs';
import {test,expect,login} from '../support/fixtures.js';
import {assertStatus,exactReturnRow,type PaidOrder} from '../support/lifecycle.js';
import {step} from '../support/evidence.js';
test.skip(process.env.RUN_REFUND_PROOF_RESUME !== 'true','Optional read-only refund proof diagnostic; run lifecycle.spec.ts for full journeys');
test('E2E-RETURN-001 resume gateway refund proof for this run’s approved return',async({page},info)=>{
 const order=JSON.parse(readFileSync(process.env.RESUME_RETURN_ORDER_FILE || 'reports/refund-proof/order-fixture.json','utf8')) as PaidOrder;
 await info.attach('resumed-own-test-order',{body:Buffer.from(JSON.stringify(order)),contentType:'application/json'});
 await login(page,'ADMIN');
 const row=await exactReturnRow(page,order);
 await expect(row).toContainText(`REGRESSION item damaged on arrival ${order.number}`);
 await expect(row).toContainText(/refunded/i);
 await assertStatus(page,order,'refunded');
 await page.goto(`/admin/orders/${order.id}`,{waitUntil:'domcontentloaded'});
 info.annotations.push({type:'settlement unverified',description:'This diagnostic checks displayed refund evidence only. A pending/processing refund must not be reported as completed settlement.'});
 await step('Refund verification: order exposes Paystack refund identifier',()=>expect(page.getByRole('main')).toContainText(/refund[_ -]?id|refund reference|re_[a-z0-9]+/i));
});
