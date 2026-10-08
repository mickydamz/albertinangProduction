import {test,expect,login} from '../support/fixtures.js';
import fs from 'node:fs';
import {fileURLToPath} from 'node:url';
type Group={label:string;items:{route:string;label:string}[]};
const groups:Group[]=JSON.parse(fs.readFileSync(fileURLToPath(new URL('../support/admin-navigation.json',import.meta.url)),'utf8'));
const items=groups.flatMap(group=>group.items);

test('ADMIN workspace: all current destinations, active navigation, dashboard data',async({adminPage},info)=>{
  test.setTimeout(240000);
  await login(adminPage,'ADMIN');
  await adminPage.goto('/admin');
  const nav=adminPage.getByRole('navigation',{name:'Administration'});
  const destinations=await nav.locator('a[href]').evaluateAll(es=>es.map(e=>({label:e.textContent?.trim(),url:e.getAttribute('href')})));
  expect(destinations).toHaveLength(items.length + 1); // overview plus the current directory
  const results=[];
  for(const item of items){
    const target=destinations.find(d=>d.label===item.label);
    expect(target,`Missing menu destination: ${item.label}`).toBeTruthy();
    const response=await adminPage.goto(target!.url!);
    results.push({label:item.label,url:target!.url,status:response?.status()});
    expect(response?.status(),`${item.label} must load`).toBe(200);
    await expect(adminPage.getByRole('navigation',{name:'Administration'}).getByRole('link',{name:item.label,exact:true})).toHaveAttribute('aria-current','page');
  }
  await adminPage.goto('/admin');
  for(const chart of ['revenue','user','transaction','product','order-status']){
    const response=await adminPage.request.get(`/admin/data/${chart}-analytics`);
    expect(response.status(),`${chart} dashboard data`).toBe(200);
    const payload=await response.json(); expect(Array.isArray(payload.labels)).toBe(true);expect(Array.isArray(payload.datasets)).toBe(true);
  }
  await info.attach('menu-destinations',{body:Buffer.from(JSON.stringify(results,null,2)),contentType:'application/json'});
});

test('ADMIN workspace: desktop search, products, empty results and shortcuts',async({adminPage},info)=>{
  await adminPage.setViewportSize({width:1440,height:1000});
  await login(adminPage,'ADMIN');await adminPage.goto('/admin');
  await expect(adminPage.getByRole('heading',{name:'Your store workspace'})).toBeVisible();
  const search=adminPage.getByRole('searchbox',{name:'Find an admin page'});
  await search.fill('refund');await expect(adminPage.locator('#adminNavResults')).toHaveText('3 pages found');
  await expect(adminPage.getByRole('navigation',{name:'Administration'}).getByRole('link',{name:'Returns',exact:true})).toBeVisible();
  await search.fill('unfindable-page-xyz');await expect(adminPage.locator('#adminNavEmpty')).toBeVisible();
  await search.fill('products');
  await adminPage.getByRole('navigation',{name:'Administration'}).getByRole('link',{name:'Products',exact:true}).click();
  await expect(adminPage.getByRole('main')).toContainText('Products');
  await expect(adminPage.getByRole('link',{name:/Add.*Product/})).toBeVisible();
  await adminPage.screenshot({path:info.outputPath('products-desktop.png'),fullPage:true});
});

test('ADMIN workspace: mobile drawer, search, keyboard and no page overflow',async({adminPage},info)=>{
  await adminPage.setViewportSize({width:390,height:844});
  await login(adminPage,'ADMIN');await adminPage.goto('/admin');
  const open=adminPage.locator('#alHamburger');
  await open.click();await expect(open).toHaveAttribute('aria-expanded','true');
  const search=adminPage.getByRole('searchbox',{name:'Find an admin page'});await expect(search).toBeFocused();
  await search.fill('products');
  await expect(adminPage.getByRole('navigation',{name:'Administration'}).getByRole('link',{name:'Products',exact:true})).toBeVisible();
  await adminPage.screenshot({path:info.outputPath('menu-mobile.png'),fullPage:true});
  await search.press('Escape');await expect(open).toHaveAttribute('aria-expanded','false');await expect(open).toBeFocused();
  const width=await adminPage.evaluate(()=>({scroll:document.documentElement.scrollWidth,width:innerWidth}));expect(width.scroll).toBeLessThanOrEqual(width.width);
  await open.click();await adminPage.getByRole('button',{name:'Close navigation',exact:true}).click();await expect(open).toHaveAttribute('aria-expanded','false');
});

test('ADMIN workspace: guests cannot access admin pages',async({page})=>{
  for(const path of ['/admin','/admin/products','/admin/paystack-transactions']){
    await page.goto(path);await expect(page).toHaveURL(/\/login(?:\?|$)/);
  }
});


test('ADMIN workspace: desktop menu hides, remembers preference and restores',async({adminPage},info)=>{
  await adminPage.setViewportSize({width:1440,height:1000});
  await login(adminPage,'ADMIN');await adminPage.goto('/admin');
  const toggle=adminPage.locator('#alHamburger');
  await expect(toggle).toHaveAttribute('aria-expanded','true');
  await toggle.click();
  await expect(toggle).toHaveAttribute('aria-label','Open navigation');
  await expect(adminPage.locator('#alDrawer')).toBeHidden();
  await expect(adminPage.locator('.al-content')).toHaveCSS('margin-left','0px');
  await adminPage.reload();
  await expect(adminPage.locator('#alDrawer')).toBeHidden();
  await adminPage.screenshot({path:info.outputPath('sidebar-hidden.png'),fullPage:true});
  await adminPage.setViewportSize({width:390,height:844});
  await toggle.click();
  await expect(toggle).toHaveAttribute('aria-expanded','true');
  await expect(adminPage.getByRole('searchbox',{name:'Find an admin page'})).toBeFocused();
  await adminPage.getByRole('searchbox',{name:'Find an admin page'}).press('Escape');
  await adminPage.setViewportSize({width:1440,height:1000});
  await expect(adminPage.locator('#alDrawer')).toBeHidden();
  await toggle.click();
  await expect(adminPage.locator('#alDrawer')).toBeVisible();
  await expect(adminPage.locator('.al-content')).toHaveCSS('margin-left','264px');
  await adminPage.reload();
  await expect(toggle).toHaveAttribute('aria-expanded','true');
});
