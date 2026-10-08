import { captureFailure, clean } from './evidence.js';
import { test as base, expect, type Page } from '@playwright/test';
export const origin = new URL(process.env.BASE_URL || 'https://test.albertinang.com').origin;
export const productPath = process.env.RUN_LIFECYCLE === 'true' ? (process.env.LIFECYCLE_PRODUCT_PATH || '/product/1213') : (process.env.PRODUCT_PATH || '/product/1335');
export const productName = process.env.RUN_LIFECYCLE === 'true' ? (process.env.LIFECYCLE_PRODUCT_NAME || 'Hisense Soundbar 30W 2.0CH (HS204) HISAUD204') : (process.env.PRODUCT_NAME || 'Hisense SD-165 Showcase ICE CREAM FREEZER 165L');
export const test = base.extend<{ guard: void; adminPage: Page; failureEvidence: void }>({
  failureEvidence: [async ({ browser, page, adminPage }, use, info) => {
    const pending: Promise<void>[] = [];
    const pages: Page[] = []; const network: unknown[] = []; const logs: unknown[] = [];
    const watch = (page: Page) => {
      if (pages.includes(page)) return; pages.push(page);
      page.on('pageerror', error => { logs.push({kind:'pageerror',page:clean(page.url()),message:clean(error.message)}); });
      page.on('console', msg => { if(msg.type()==='error') logs.push({kind:'console',page:clean(page.url()),message:clean(msg.text()).slice(0,1000)}); });
      page.on('requestfailed', req => { network.push({kind:'requestfailed',method:req.method(),url:clean(req.url()),error:req.failure()?.errorText}); });
      page.on('response', res => {
        if (res.request().method() === 'POST' || res.status()>=400 || /\/(orders\/paystack|reviews|cancel|return)(?:\?|$)/.test(new URL(res.url()).pathname)) {
          const entry: Record<string,unknown> = {kind:'response',method:res.request().method(),url:clean(res.url()),status:res.status()};
          network.push(entry);
          if (/api\.paystack\.co\/checkout\/request_inline|standard\.paystack\.co\/charge\/|\/paystack\/(confirm-order|save-checkout)$/.test(res.url())) {
            pending.push(res.json().then(data=>{
              entry.outcome=Object.fromEntries(['status','message','success','order_id','order_number','reference'].filter(key=>data[key]!==undefined).map(key=>[key,clean(String(data[key]))]));
            }).catch(()=>{}));
          }
        }
      });
    };
    watch(page); watch(adminPage);
    const timer = setInterval(() => { for(const context of browser.contexts()) for(const page of context.pages()) watch(page); },100);
    try { await use(); } finally {
      clearInterval(timer);
      await Promise.allSettled(pending);
      await captureFailure(info,pages,network.slice(-80),logs.slice(-40));
    }
  }, {auto:true}],
  adminPage: async ({ browser }, use) => {
    const context = await browser.newContext({ baseURL: origin });
    await context.route('**/*', route => {
      const host = new URL(route.request().url()).hostname;
      return ['albertinang.com', 'www.albertinang.com'].includes(host) ? route.abort('blockedbyclient') : route.continue();
    });
    try { await use(await context.newPage()); } finally { await context.close(); }
  },
  guard: [async ({ context }, use) => {
    await context.route('**/*', async route => {
      const u = new URL(route.request().url());
      if (['albertinang.com','www.albertinang.com'].includes(u.hostname)) return route.abort('blockedbyclient');
      return route.continue();
    });
    await use();
  }, { auto: true }],
});
export { expect };
export async function login(page: Page, role = 'CUSTOMER') {
  const email = process.env[`${role}_EMAIL`], password = process.env[`${role}_PASSWORD`];
  test.skip(!email || !password, `${role} credentials not configured`);
  await page.goto('/login',{waitUntil:'domcontentloaded'});
  if(new URL(page.url()).pathname !== '/login') {
    await page.goto('/',{waitUntil:'domcontentloaded'});
    await expect(page.getByRole('button',{name:'My Account',exact:true})).toBeVisible();
    return;
  }
  await page.getByRole('textbox', { name: 'Email Address' }).fill(email!);
  await page.getByRole('textbox', { name: 'Password', exact: true }).fill(password!);
  await page.getByRole('button', { name: 'Sign In', exact: true }).click();
  await expect(page).not.toHaveURL(/\/login(?:\?|$)/);
  await page.goto('/',{waitUntil:'domcontentloaded'});
  await expect(page.getByRole('button', { name: 'My Account', exact: true })).toBeVisible();
}
export async function addProduct(page: Page) {
  await page.goto(productPath,{waitUntil:'domcontentloaded'});
  await expect(page.getByRole('heading', { level: 1 })).toHaveText(productName);
  await page.getByRole('button', { name: 'Add to Cart' }).click();
  await page.goto('/cart',{waitUntil:'domcontentloaded'});
  await expect(page.getByRole('main').getByRole('link', { name: productName, exact: true })).toBeVisible();
}
export async function csrf(page: Page) {
  const token = await page.locator('head meta[name="csrf-token"]').first().getAttribute('content');
  if (!token) throw new Error('Missing CSRF token');
  return token;
}
export function requireWrites() {
  test.skip(process.env.ALLOW_TEST_WRITES !== 'true' || process.env.PAYMENT_TEST_MODE !== 'true', 'Enable test writes and confirm gateway test mode in .env');
}
