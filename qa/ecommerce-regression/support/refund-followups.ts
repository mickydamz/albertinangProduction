import {existsSync,readFileSync,writeFileSync,mkdirSync,renameSync} from 'node:fs';
import {resolve,dirname} from 'node:path';
import {expect,origin} from './fixtures.js';
import type {PaidOrder} from './lifecycle.js';
export type RefundFollowup={order:PaidOrder;fulfilment:'collection'|'delivery';kind:'cancellation'|'return';createdAt:string;refundId?:string;outcome:'requested'|'pending'|'processed'|'failed';lastCheckedAt?:string;lastObservation?:unknown};
export const followupFile=resolve(origin === 'https://test.albertinang.com' ? 'reports/refund-followups-test.json' : 'reports/refund-followups.json');
export function loadFollowups():RefundFollowup[] {
  if(!existsSync(followupFile))return [];
  const data=JSON.parse(readFileSync(followupFile,'utf8'));
  if(data.origin!==origin||!Array.isArray(data.refunds))throw new Error('Refund follow-up file must contain staging records only.');
  for(const entry of data.refunds)if(!Number.isInteger(entry.order?.id)||entry.order.id<=0||typeof entry.order?.reference!=='string'||!Number.isFinite(entry.order?.totalNgn)||entry.order.totalNgn<=0)throw new Error(`Invalid saved refund order; review ${followupFile}.`);
  return data.refunds;
}
export function saveFollowup(entry:RefundFollowup) {
  const rows=loadFollowups();const index=rows.findIndex(x=>x.order.id===entry.order.id);
  if(index<0)rows.push(entry);else rows[index]=entry;
  mkdirSync(dirname(followupFile),{recursive:true});
  const temporary=followupFile+`.${process.pid}.tmp`;
  writeFileSync(temporary,JSON.stringify({origin,refunds:rows},null,2));renameSync(temporary,followupFile);
}
export function validateGateway(body:any,order:PaidOrder,refundId?:string, legacyOrderStatus=false) {
  expect(body.test_mode).toBe(true);expect(body.order_id).toBe(order.id);
  expect(body.gateway.domain).toBe('test');expect(body.reference).toBe(order.reference);
  expect(String(body.gateway.refund_id)).toMatch(/^\d+$/);
  if(refundId)expect(String(body.gateway.refund_id)).toBe(refundId);
  expect(Number(body.gateway.amount)).toBe(Math.round(order.totalNgn*100));expect(body.gateway.currency).toBe('NGN');
  expect(['pending','processing','processed','failed','needs-attention']).toContain(body.gateway.status);
  if(body.gateway.status!=='processed') {
    expect(body.application.processed_at,'Unprocessed Paystack refund must not have a completion date.').toBeNull();
    expect(body.application.order_status,'Unprocessed Paystack refund must not mark the order refunded.').not.toBe('refunded');
  }
  if(body.application.refund_status==='processed') {
    expect(body.gateway.status,'Application completion must agree with Paystack.').toBe('processed');
    expect(body.application.processed_at).toBeTruthy();expect(legacyOrderStatus ? ['cancelled','completed','delivered','refunded'] : ['cancelled','completed','delivered']).toContain(body.application.order_status);
  }
}
