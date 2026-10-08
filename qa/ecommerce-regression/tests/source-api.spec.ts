import {test,expect,login,csrf,productPath} from '../support/fixtures.js';
import {step} from '../support/evidence.js';
const productId=Number(productPath.split('/').pop());
const invalidCouponCases=[
  ['missing code',{subtotal_ngn:10000},'code'],
  ['negative subtotal',{code:'SAVE10',subtotal_ngn:-1},'subtotal_ngn'],
  ['non-numeric subtotal',{code:'SAVE10',subtotal_ngn:'bad'},'subtotal_ngn'],
] as const;
for(const [label,data,field] of invalidCouponCases) test(`COUPON-VALIDATION ${label}`,async({page})=>{
  await login(page); const token=await csrf(page);
  const r=await page.request.post('/api/coupons/validate',{headers:{Accept:'application/json','X-CSRF-TOKEN':token},data});
  expect(r.status()).toBe(422); expect((await r.json()).errors).toHaveProperty([field]);
});
for(const [label,ids] of [['not an array','bad'],['unknown product',[2147483647]]] as const) test(`INSTALL-VALIDATION ${label}`,async({page})=>{
  await page.goto('/');const token=await csrf(page);
  const r=await page.request.post('/api/installation-options',{headers:{Accept:'application/json','X-CSRF-TOKEN':token},data:{product_ids:ids}});
  expect(r.status()).toBe(422);expect((await r.json()).errors).toBeTruthy();
});
test('INSTALL-READ requested product returns an options array',async({page})=>{
  await page.goto('/'); const token=await csrf(page);
  const r=await page.request.post('/api/installation-options',{headers:{Accept:'application/json','X-CSRF-TOKEN':token},data:{product_ids:[productId]}});
  expect(r.status()).toBe(200);expect(Array.isArray((await r.json())[String(productId)])).toBe(true);
});
test('DELIVERY-API states and state-filtered locations are consistent',async({request})=>{
  const r=await request.get('/api/states');expect(r.status()).toBe(200);
  const states=await r.json();expect(states.length).toBeGreaterThan(0);
  const results=await Promise.all(states.map(async(s:{id:number})=>{const r=await request.get('/api/locations',{params:{state_id:s.id}});expect(r.status()).toBe(200);return r.json();}));
  const ids=results.flat().map((x:{id:number})=>x.id);expect(new Set(ids).size).toBe(ids.length);
  for(const location of results.flat()) {expect(Number(location.shipping_cost)).toBeGreaterThanOrEqual(0);expect(Number(location.truck_shipping_cost)).toBeGreaterThanOrEqual(0);}
});
test('DELIVERY-API pickup-only locations each have a physical pickup point',async({request})=>{
  const r=await request.get('/api/locations?for_pickup=1');expect(r.status()).toBe(200);
  const locations=await r.json();expect(locations.length).toBeGreaterThan(0);
  for(const l of locations){const points=await request.get('/api/pickup-points',{params:{location_id:l.id}});expect(points.status()).toBe(200);expect((await points.json()).length).toBeGreaterThan(0);}
});
test('DELIVERY-API truck metadata and thresholds are valid',async({request})=>{
  const r=await request.get('/api/cart/truck-check',{params:{ids:productId}});expect(r.status()).toBe(200);
  const data=await r.json();expect(data.products[String(productId)]).toBeTruthy();
  expect(Number(data.products[String(productId)].inferred_weight_kg)).toBeGreaterThanOrEqual(0);
  expect(Number(data.thresholds.weight_kg)).toBeGreaterThan(0);expect(Number(data.thresholds.order_value_ngn)).toBeGreaterThan(0);
});
test('CAT-SUGGEST suggestions link to real products and unknown search is empty',async({request})=>{
  const r=await request.get('/search-suggestions',{params:{query:'Hisense'}});expect(r.status()).toBe(200);
  const suggestions=await r.json();expect(Array.isArray(suggestions)).toBe(true);expect(suggestions.some((s:{type:string})=>s.type==='product')).toBe(true);
  for(const s of suggestions) {expect(['product','category']).toContain(s.type);expect(s.name).toBeTruthy();if(s.type==='product')expect(Number(s.id)).toBeGreaterThan(0);}
  const empty=await request.get('/search-suggestions',{params:{query:'REGRESSION_NO_MATCH_8472199'}});expect(empty.status()).toBe(200);expect(await empty.json()).toEqual([]);
});
test('GEO-API countries contain Nigeria and unknown country has no states',async({request})=>{
  const r=await request.get('/api/geo/countries');expect(r.status()).toBe(200);expect((await r.json()).some((c:{name:string})=>c.name==='Nigeria')).toBe(true);
  const empty=await request.get('/api/geo/states?country=REGRESSION_NO_COUNTRY');expect(empty.status()).toBe(200);expect(await empty.json()).toEqual([]);
});
for(const path of ['/tickets','/tickets/create','/chat','/account/two-factor','/account/change-password','/manager/products']) test(`ACCESS-GUEST ${path}`,async({request})=>{
  const r=await request.get(path,{maxRedirects:0});expect(r.status()).toBe(302);expect(new URL(r.headers().location,'https://testing.albertinang.com').pathname).toBe('/login');
});
test('INVOICE-SEC unsigned guest invoice is forbidden',async({request})=>{
  const r=await request.get('/guest/orders/2147483647/invoice/download',{maxRedirects:0});expect([403,404]).toContain(r.status());expect(r.headers()['content-type']).not.toContain('application/pdf');
});
const reviewCases=[['rating zero',{rating:0,comment:'REGRESSION invalid review'},'rating'],['rating above five',{rating:6,comment:'REGRESSION invalid review'},'rating'],['comment too short',{rating:4,comment:'short'},'comment'],['comment too long',{rating:4,comment:'x'.repeat(1001)},'comment']] as const;
for(const [label,values,field] of reviewCases) test(`REVIEW-VALIDATION ${label}`,async({page})=>{
  await login(page);const token=await csrf(page);
  const r=await step(`Review server rejects ${label}`,()=>page.request.post(`/products/${productId}/reviews`,{headers:{Accept:'application/json','X-CSRF-TOKEN':token},data:{product_id:productId,...values}}));
  expect(r.status()).toBe(422);expect((await r.json()).errors).toHaveProperty([field]);
});
const checkoutCases=[['empty basket',{items:[]},'items'],['quantity zero',{items:[{product_id:productId,quantity:0}]},'items.0.quantity'],['invalid method',{items:[{product_id:productId,quantity:1}],fulfillment:{method:'teleport'}},'fulfillment.method']] as const;
for(const [label,data,field] of checkoutCases) test(`CHECKOUT-SERVER ${label} is rejected before payment`,async({page})=>{
  await login(page);const token=await csrf(page);
  const r=await step(`Checkout server rejects ${label}`,()=>page.request.post('/paystack/save-checkout',{headers:{Accept:'application/json','X-CSRF-TOKEN':token},data}));
  expect(r.status()).toBe(422);expect((await r.json()).errors).toHaveProperty([field]);
});
test('PAYMENT-VALIDATION missing reference cannot confirm an order',async({page})=>{
  await login(page);const token=await csrf(page);const r=await page.request.post('/paystack/confirm-order',{headers:{Accept:'application/json','X-CSRF-TOKEN':token},data:{}});
  expect(r.status()).toBe(422);expect((await r.json()).errors).toHaveProperty('reference');
});

test('ACCESS removed supplier feature is unavailable to guests',async({request})=>{
 const response=await request.get('/supplier/dashboard',{maxRedirects:0});expect(response.status()).toBe(404);
});
