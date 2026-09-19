const { chromium } = require('playwright');
const routes = [
  ['home', '/'], ['shop', '/shop/'], ['category', '/product-category/women/'],
  ['search', '/?s=kurta'], ['blog', '/blog/'], ['blog-single', '/hello-world/'],
  ['about', '/about-us/'], ['contact', '/contact-us/'], ['store-location', '/store-location/'],
  ['faq', '/faq/'], ['privacy', '/privacy-policy/'], ['cart', '/cart/'],
  ['checkout', '/checkout/'], ['account', '/my-account/'], ['404', '/nonexistent-xyz-404/'],
];
const viewports = [['desktop', 1440, 900], ['mobile', 390, 844]];
(async () => {
  const browser = await chromium.launch();
  const report = [];
  for (const [vpName, w, h] of viewports) {
    const ctx = await browser.newContext({ viewport: { width: w, height: h } });
    const page = await ctx.newPage();
    for (const [name, route] of routes) {
      const errors = [];
      const h2 = m => { if (m.type() === 'error') errors.push(m.text().slice(0, 80)); };
      page.on('console', h2);
      try {
        await page.goto('http://localhost:8080' + route, { waitUntil: 'networkidle', timeout: 30000 });
        await page.waitForTimeout(700);
        await page.screenshot({ path: `qa-new/baseline/${name}--${vpName}.png`, fullPage: false });
        report.push({ route: name, viewport: vpName, ok: true, errors: errors.length });
      } catch (e) {
        report.push({ route: name, viewport: vpName, ok: false, error: String(e).slice(0, 80) });
      }
      page.off('console', h2);
    }
    await ctx.close();
  }
  await browser.close();
  require('fs').writeFileSync('qa-new/baseline-report.json', JSON.stringify(report, null, 2));
  const bad = report.filter(r => !r.ok || r.errors > 1);
  console.log('shots:', report.length, '| problem entries:', bad.length);
  bad.forEach(b => console.log(' ', b.route, b.viewport, b.error || (b.errors + ' errors')));
})();
