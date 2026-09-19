const { chromium } = require('playwright');
(async () => {
  const browser = await chromium.launch();
  const ctx = await browser.newContext({ viewport: { width: 1440, height: 900 } });
  const page = await ctx.newPage();
  const errors = [];
  page.on('console', m => { if (m.type() === 'error') errors.push(m.text().slice(0, 120)); });
  await page.goto('http://localhost:8080/shop/', { waitUntil: 'networkidle', timeout: 30000 });
  await page.evaluate(() => { const a = document.querySelector('a[data-vineta-add]'); if (a) a.click(); });
  await page.waitForTimeout(3000);
  await page.goto('http://localhost:8080/cart/', { waitUntil: 'networkidle', timeout: 30000 });
  const res = await page.evaluate(() => {
    const item = document.querySelector('.cart-item-name')?.textContent.trim();
    const totals = Array.from(document.querySelectorAll('.summary-row')).map(e => e.textContent.replace(/\s+/g, ' ').trim());
    const badge = document.querySelector('.nav-cart .count-box')?.textContent.trim();
    return { item, totals: totals.slice(0, 5), badge, errors: undefined };
  });
  console.log('CART SMOKE:', JSON.stringify(res), '| console errors:', errors.length);
  await browser.close();
})();
