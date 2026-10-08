import {test,expect,login} from '../support/fixtures.js';
import {setStatus,type PaidOrder} from '../support/lifecycle.js';

test.skip(process.env.RUN_EMAIL_DIAGNOSTIC !== 'true','Opt-in: sends one processing notification for the existing dummy order; inbox receipt must be checked separately.');
test('EMAIL diagnostic: processing notification trigger and restore dummy order',async({adminPage},info)=>{
  test.setTimeout(120000);
  expect(new URL(process.env.BASE_URL || 'https://test.albertinang.com').hostname).toBe('test.albertinang.com');
  await login(adminPage,'ADMIN');
  const order:PaidOrder={id:103,number:'ALB-ENU-339814',reference:'ps_01M3XQ9HD0S3BYECMCJJQP7PPV',totalNgn:55536};
  await adminPage.goto('/admin/orders/103');
  await expect(adminPage.getByRole('main')).toContainText(order.number);
  await expect(adminPage.getByRole('main')).toContainText(process.env.CUSTOMER_EMAIL!);
  await adminPage.goto('/admin/orders/103/edit');
  await expect(adminPage.getByRole('combobox',{name:'Order Status'})).toHaveValue('paid');
  try {
    await setStatus(adminPage,order,'processing');
    await adminPage.screenshot({path:info.outputPath('notification-trigger.png'),fullPage:true});
  } finally {
    await setStatus(adminPage,order,'paid');
  }
  info.annotations.push({type:'INBOX_VERIFICATION_REQUIRED',description:'An order status update alone does not prove SMTP delivery or Gmail receipt.'});
});
