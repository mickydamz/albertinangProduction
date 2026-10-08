import { writeFile } from 'node:fs/promises';
import { test as base } from '@playwright/test';
import type { TestInfo, Page } from '@playwright/test';
const timeline = new Map<string, Array<{ step: string; status: string; at: string; error?: string }>>();
export function clean(value: string): string {
  let out = value.replace(/(<meta\b(?=[^>]*\bname=["']csrf-token["'])[^>]*\bcontent=)["'][^"']*["']/gi,'$1"[redacted]"').replace(/(?:pk|sk)_(?:test|live)_[A-Za-z0-9]+/g, '[redacted-key]')
    .replace(/[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,}/gi, '[redacted-email]')
    .replace(/\b(password|authorization|csrf_token|token|secret)\b(["']?\s*[:=]\s*["']?)[^\s,"'<>]+/gi, '$1$2[redacted]');
  for (const name of ['CUSTOMER_PASSWORD','ADMIN_PASSWORD','SECOND_CUSTOMER_PASSWORD']) {
    const secret = process.env[name]; if (secret) out = out.split(secret).join('[redacted-password]');
  }
  return out;
}
export async function step<T>(name: string, action: () => Promise<T>): Promise<T> {
  const info = base.info(); const events = timeline.get(info.testId) || [];
  timeline.set(info.testId, events);
  events.push({step:name,status:'started',at:new Date().toISOString()});
  return base.step(name, async () => {
    try { const result = await action(); events.push({step:name,status:'passed',at:new Date().toISOString()}); return result; }
    catch(error) { events.push({step:name,status:'failed',at:new Date().toISOString(),error:clean(String(error))}); throw error; }
  });
}
export async function captureFailure(info: TestInfo, pages: Page[], network: unknown[], logs: unknown[]) {
  if (info.status === info.expectedStatus || info.status === 'skipped') { timeline.delete(info.testId); return; }
  const events = timeline.get(info.testId) || [];
  const screens = [];
  for (const [index,page] of pages.entries()) {
    if (page.isClosed()) continue;
    const frames = [];
    for (const frame of page.frames()) {
      let text='[frame unavailable]';
      try { text=clean((await frame.locator('body').innerText({timeout:1500})).slice(0,16000)); } catch {}
      frames.push({url:clean(frame.url()),text});
    }
    screens.push({page:index,url:clean(page.url()),frames});
    try { await info.attach(`failure-page-${index}`,{body:await page.screenshot({timeout:3000,mask:[page.locator('input[type="password"]')]}),contentType:'image/png'}); } catch {}
  }
  const last = events.find(x=>x.status==='failed');
  const firstError=info.errors[0];
  const locator=firstError?.message?.match(/Locator:\s+([^\n]+)/)?.[1];
  const source=firstError?.stack?.match(/at (?:[^\n]*?\()?([^()\n]+\.(?:ts|js)):(\d+):(\d+)/)?.slice(1);
  const evidence = {test:info.title,status:info.status,exactStep:last?.step || locator || clean(firstError?.message?.split('\n')[0] || 'Setup or interrupted run'),exactLocator:locator,source,
    errors:info.errors.map(e=>({message:clean(e.message||''),stack:clean(e.stack||'')})),timeline:events,screens,network,logs};
  const details=info.outputPath('failure-details.json');
  await writeFile(details,JSON.stringify(evidence,null,2));
  await info.attach('failure-details',{path:details,contentType:'application/json'});
  await info.attach('failure-summary',{body:Buffer.from(`Test: ${info.title}\nFailed step: ${evidence.exactStep}\n${info.errors.map(e=>clean(e.message||'')).join('\n')}\nPages:\n${screens.map(s=>s.url).join('\n')}`),contentType:'text/plain'});
  timeline.delete(info.testId);
}
