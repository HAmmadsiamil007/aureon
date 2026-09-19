const { chromium } = require('playwright');
(async () => {
  const routes = ['/', '/shop/', '/?s=kurta', '/blog/', '/hello-world/', '/about-us/', '/contact-us/', '/privacy-policy/', '/cart/', '/my-account/', '/nonexistent-xyz-404/'];
  const browser = await chromium.launch();
  const ctx = await browser.newContext({ viewport: { width: 1440, height: 900 } });
  const page = await ctx.newPage();
  let total = 0;
  for (const r of routes) {
    const errors = [];
    const handler = (msg) => { if (msg.type() === 'error') errors.push(msg.text().slice(0, 120)); };
    page.on('console', handler);
    await page.goto('http://localhost:8080' + r, { waitUntil: 'networkidle', timeout: 30000 });
    await page.waitForTimeout(800);
    page.off('console', handler);
    const status = errors.length === 0 ? 'CLEAN' : errors.length + ' ERR';
    console.log(status.padEnd(9), r, errors.slice(0, 2).join(' | '));
    total += errors.length;
  }
  await browser.close();
  console.log(total === 0 ? 'ALL ROUTES CLEAN' : 'TOTAL ERRORS: ' + total);
})();
