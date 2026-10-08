import { test, expect, origin } from '../support/fixtures.js';
for (const path of ['/currencies','/api/locations','/categories']) {
  test(`API-READ ${path} returns JSON data`, async ({ request }) => {
    const response = await request.get(path, {maxRedirects:0});
    expect(response.status()).toBe(200);
    expect(response.headers()['content-type']).toContain('application/json');
    const data = await response.json();
    expect(data).not.toBeNull();
    expect(typeof data).toBe('object');
    expect(JSON.stringify(data)).not.toMatch(/password|secret_key|sk_live_/i);
  });
}
for (const path of ['/admin','/admin/orders','/admin/returns','/admin/cancellations','/admin/reviews','/account/orders','/checkout']) {
  test(`SEC-GUEST ${path} rejects unauthenticated access`, async ({ request }) => {
    const r = await request.get(path, {maxRedirects:0});
    expect(r.status()).toBe(302);
    expect(new URL(r.headers().location,origin).pathname).toBe('/login');
  });
}
test('API-404 nonexistent route returns 404, not a successful error page', async ({ request }) => {
  const r = await request.get('/regression-route-does-not-exist-84721',{maxRedirects:0}); expect(r.status()).toBe(404);
});

test('API-PICKUP location returns associated pickup points',async({request})=>{
  const locations=await request.get('/api/locations'); expect(locations.status()).toBe(200);
  const data=await locations.json(); expect(Array.isArray(data)).toBe(true);
  const location=data.find((x:{name:string})=>x.name==='Enugu'); expect(location).toBeTruthy();
  const r=await request.get('/api/pickup-points',{params:{location_id:location.id}});
  expect(r.status()).toBe(200); const points=await r.json(); expect(Array.isArray(points)).toBe(true);
  expect(points.length).toBeGreaterThan(0);
  for(const point of points) expect(Number(point.location_id)).toBe(Number(location.id));
});
test('API-PICKUP missing location is rejected with validation',async({request})=>{
  const r=await request.get('/api/pickup-points',{headers:{Accept:'application/json'}});
  expect(r.status()).toBe(422); expect((await r.json()).errors).toHaveProperty('location_id');
});
