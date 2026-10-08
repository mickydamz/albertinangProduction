import {expect, productName, addProduct, login} from './fixtures.js';
import type {Page} from '@playwright/test';
export type Fulfilment = 'collection' | 'delivery';
export async function checkout(page: Page, fulfilment: Fulfilment = 'collection', beforePayment?: (page: Page) => Promise<void>) {
  await login(page); await addProduct(page); await page.goto('/checkout',{waitUntil:'domcontentloaded'});
  await expect(page.getByRole('main')).toContainText(productName);
  if (fulfilment === 'delivery') {
    await page.getByRole('button',{name:'Home Delivery'}).click();
    await page.locator('#shippingAddressInput').fill('REGRESSION DUMMY — 12 Ogui Road, Enugu');
    await page.getByRole('combobox',{name:'State',exact:true}).selectOption({label:'Enugu State'});
    await page.getByRole('combobox',{name:'Delivery Area',exact:true}).selectOption({label:'Ogui Road'});
    await expect(page.locator('#shippingAddressInput')).toHaveValue(/REGRESSION DUMMY/);
  } else {
  await page.getByRole('button',{name:'Collect in Store'}).click();
  await page.getByRole('button',{name:'Change',exact:true}).click();
  await page.getByText('Enugu',{exact:true}).click();
  await page.getByRole('button',{name:'Confirm Location',exact:true}).click();
  await page.getByRole('heading',{name:'Albertina Nigeria HQ',exact:true}).click();
  }
  if (beforePayment) await beforePayment(page);
  await page.getByRole('button',{name:'Make Payment'}).click();
}

export async function pickupCheckout(page: Page) { return checkout(page, 'collection'); }
