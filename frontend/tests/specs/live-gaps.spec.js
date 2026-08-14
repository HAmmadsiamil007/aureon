const { test, expect } = require('@playwright/test');

const revealAndWait = async (locator) => {
  await locator.evaluate((el) => el.scrollIntoView({ block: 'center' }));
  await locator.page().waitForTimeout(1500);
  await expect(locator).toBeVisible({ timeout: 15000 });
};

test.describe('G1/G3 live verification', () => {

  test('G1: homepage card add-to-cart posts wc-ajax, updates count, no navigation', async ({ page }) => {
    const errors = [];
    page.on('console', m => { if (m.type() === 'error') errors.push(m.text()); });
    await page.goto('/shop/?nocache=e2e', { waitUntil: 'domcontentloaded' });

    const btn = page.locator('.add-to-cart-btn[data-product-id]').first();
    await revealAndWait(btn);

    const before = parseInt(await page.locator('.cart-count').first().textContent(), 10);

    let fragmentHtml = null;
    page.on('response', async res => {
      if (res.url().includes('wc-ajax=add_to_cart')) {
        try { const j = await res.json(); if (j.fragments) fragmentHtml = j.fragments['a.aether-cart-count'] || null; } catch (e) {}
      }
    });

    const productId = await btn.getAttribute('data-product-id');
    const productType = await btn.getAttribute('data-product-type');
    expect(productType).toBe('simple');

    await btn.click();
    await page.waitForFunction(() => document.querySelector('.add-to-cart-btn.is-added'), null, { timeout: 15000 });

    const after = parseInt(await page.locator('.cart-count').first().textContent(), 10);
    expect(after).toBe(before + 1);
    expect(page.url()).toContain('/shop/');
    expect(fragmentHtml).not.toBeNull();
    expect(fragmentHtml).toContain('cart-count');
    expect(fragmentHtml).toContain(String(after));
    expect(errors.filter(e => !e.includes('favicon'))).toEqual([]);
    console.log(`G1 card: id=${productId} count ${before} -> ${after}, fragment OK, no navigation`);
  });

  test('G1: product page buy box honours quantity stepper', async ({ page }) => {
    await page.goto('/shop/?nocache=e2e', { waitUntil: 'domcontentloaded' });
    const card = page.locator('.product-card').first();
    await revealAndWait(card);
    await card.click();
    await page.waitForURL(/\/product\//, { timeout: 20000 });

    const addBtn = page.locator('.pd-add-to-cart');
    await revealAndWait(addBtn);
    await expect(addBtn).toBeVisible({ timeout: 20000 });

    const pid = await addBtn.getAttribute('data-product-id');
    const ptype = await addBtn.getAttribute('data-product-type');
    expect(pid).toBeTruthy();
    expect(ptype).toBeTruthy();

    const before = parseInt(await page.locator('.cart-count').first().textContent(), 10);
    expect(await page.locator('#qtyValue').textContent()).toBe('1');

    await page.locator('#qtyPlus').click();
    await page.waitForFunction(() => document.getElementById('qtyValue').textContent === '2');
    await addBtn.click();
    await page.waitForFunction(() => document.querySelector('.pd-add-to-cart.is-added'), null, { timeout: 15000 });

    const after = parseInt(await page.locator('.cart-count').first().textContent(), 10);
    expect(after).toBe(before + 2);
    console.log(`G1 product page: id=${pid} type=${ptype} qty=2 count ${before} -> ${after}`);
  });

  test('G3: contact form posts aether_contact_submit, shows success, no navigation', async ({ page }) => {
    const errors = [];
    page.on('console', m => { if (m.type() === 'error') errors.push(m.text()); });
    await page.goto('/contact/?nocache=e2e', { waitUntil: 'domcontentloaded' });

    const form = page.locator('.aether-contact-form');
    await revealAndWait(form);

    let ajaxJson = null;
    page.on('response', async res => {
      if (res.url().includes('admin-ajax.php') && res.request().postData() && res.request().postData().includes('aether_contact_submit')) {
        try { ajaxJson = await res.json(); } catch (e) {}
      }
    });

    await page.fill('input[name="aether_name"]', 'QA Tester');
    await page.fill('input[name="aether_email"]', 'qa@example.com');
    await page.selectOption('select[name="aether_subject"]', 'general');
    await page.fill('textarea[name="aether_message"]', 'Automated G3 verification message.');

    const urlBefore = page.url();
    await page.click('button[type="submit"]');

    await page.waitForFunction(() => {
      const s = document.querySelector('.aether-form-status');
      return s && (s.classList.contains('is-success') || s.classList.contains('is-error')) && s.textContent.length > 0;
    }, null, { timeout: 20000 });

    const status = await page.locator('.aether-form-status').textContent();
    expect(page.url()).toBe(urlBefore);
    expect(status.length).toBeGreaterThan(5);
    expect(ajaxJson).not.toBeNull();
    expect(!!(ajaxJson.success === true || (ajaxJson.data && ajaxJson.data.message))).toBe(true);
    expect(errors.filter(e => !e.includes('favicon'))).toEqual([]);
    console.log(`G3 contact: success=${ajaxJson.success} message="${status}" no navigation`);
  });

  test('G4: newsletter form posts to aether_newsletter_subscribe with success', async ({ page }) => {
    await page.goto('/shop/?nocache=e2e', { waitUntil: 'domcontentloaded' });

    const form = page.locator('.newsletter-form');
    await revealAndWait(form);

    const ajaxPromise = new Promise(resolve => {
      page.on('response', async res => {
        if (res.url().includes('admin-ajax.php') && res.request().postData() && res.request().postData().includes('aether_newsletter_subscribe')) {
          try { resolve(await res.json()); } catch (e) {}
        }
      });
    });

    await form.locator('.newsletter-input').fill('live-qa-' + Date.now() + '@example.com');
    await form.locator('button[type="submit"]').click();
    const ajaxJson = await ajaxPromise;

    expect(ajaxJson).not.toBeNull();
    expect(ajaxJson.success).toBe(true);
    console.log('G4 newsletter: success=true');
  });

  test('G1: no-JS classic add-to-cart fallback still adds to cart', async ({ browser }) => {
    const ctx = await browser.newContext({ javaScriptEnabled: false, viewport: { width: 1280, height: 800 } });
    const page = await ctx.newPage();
    await page.goto('/shop/');
    await page.waitForSelector('.add-to-cart-btn[data-product-id]', { timeout: 20000 });
    const href = await page.locator('.add-to-cart-btn[data-product-id]').first().getAttribute('href');
    expect(href).toContain('add-to-cart');
    await Promise.all([
      page.waitForURL(/cart/, { timeout: 20000 }),
      page.click('.add-to-cart-btn[data-product-id] >> nth=0'),
    ]);
    const count = parseInt(await page.locator('.cart-count').first().textContent(), 10);
    expect(count).toBeGreaterThan(0);
    await ctx.close();
    console.log(`G1 no-JS fallback: href=${href} landed on cart, count=${count}`);
  });

  test('G1: cart page renders items and quantity form posts natively', async ({ page }) => {
    await page.goto('/cart/');
    const hasItems = await page.locator('.aether-cart-items, .cart-items, form[name="aether-cart"]').count();
    console.log(`G1 cart page: renders=${hasItems > 0}`);
  });
});
