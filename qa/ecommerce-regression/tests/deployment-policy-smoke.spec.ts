import {test,expect,login,csrf} from '../support/fixtures.js';
test('DEPLOY unlimited quantity and refund readiness',async({page,adminPage})=>{
 await login(page);
 const token=await csrf(page);
 const response=await page.request.post('/paystack/save-checkout',{headers:{'X-CSRF-TOKEN':token,'Accept':'application/json'},data:{customer_email:process.env.CUSTOMER_EMAIL,items:[{product_id:1213,quantity:150}],fulfillment:{method:'pickup'}}});
 expect(response.status()).toBe(200);
 const result=await response.json(); expect(result.saved).toBe(true); expect(result.reference).toBeTruthy(); expect(result.total_ngn).toBeGreaterThan(1000000);
 await login(adminPage,'ADMIN');
 const ready=await adminPage.request.get('/admin/paystack/refund-test-readiness');
 expect(ready.status()).toBe(200); expect(await ready.json()).toMatchObject({test_mode:true,refund_status_endpoint:true});
});
