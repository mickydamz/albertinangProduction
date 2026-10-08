import {test,expect,login} from '../support/fixtures.js';
import {buy,lifecycleEnabled,setStatus} from '../support/lifecycle.js';

for(const fulfilment of ['collection','delivery'] as const) {
  test(`EMAIL journey ${fulfilment}: sandbox paid through fulfilment`,async({page,adminPage},info)=>{
    test.skip(process.env.RUN_EMAIL_JOURNEYS!=='true','Opt-in: creates two sandbox orders and sends lifecycle notifications.');
    lifecycleEnabled();test.setTimeout(240000);
    expect(new URL(process.env.BASE_URL || 'https://test.albertinang.com').hostname).toBe('test.albertinang.com');
    await login(adminPage,'ADMIN');
    const readiness=await adminPage.request.get('/admin/paystack/refund-test-readiness');
    expect(readiness.status()).toBe(200);
    expect(await readiness.json()).toMatchObject({test_mode:true});
    const order=await buy(page,adminPage,info,fulfilment);
    const statuses=fulfilment==='collection'?['processing','ready_for_pickup','completed']:['processing','shipped','delivered'];
    for(const status of statuses)await setStatus(adminPage,order,status);
    await info.attach('email-journey-order',{body:Buffer.from(JSON.stringify({order,fulfilment,statuses:['paid',...statuses],recipient:process.env.CUSTOMER_EMAIL},null,2)),contentType:'application/json'});
    // Re-saving the terminal status must not send duplicate status/review mail.
    await setStatus(adminPage,order,statuses[statuses.length-1]);
    await adminPage.screenshot({path:info.outputPath('completed-journey.png'),fullPage:true});
    info.annotations.push({type:'INBOX_VERIFICATION_REQUIRED',description:`Check four status emails and one review invitation for ${order.number} in Gmail; this test proves payment and order transitions only.`});
  });
}
