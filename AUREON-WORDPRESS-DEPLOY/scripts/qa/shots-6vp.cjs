const { chromium } = require('playwright');

// Phase D visual QA: full route matrix x 6 viewports.
// Viewports per the release standard: 1440, 1280, 1024 (desktop), 768 (tablet), 390, 360 (mobile).
const routes = [
  ['home', '/'], ['shop', '/shop/'], ['category', '/product-category/women/'],
  ['search', '/?s=kurta'], ['blog', '/blog/'], ['blog-single', '/hello-world/'],
  ['about', '/about-us/'], ['contact', '/contact-us/'], ['store-location', '/store-location/'],
  ['faq', '/faq/'], ['privacy', '/privacy-policy/'], ['cart', '/cart/'],
  ['checkout', '/checkout/'], ['account', '/my-account/'], ['404', '/nonexistent-xyz-404/'],
];
const viewports = [
  ['1440', 1440, 900], ['1280', 1280, 800], ['1024', 1024, 768],
  ['768', 768, 1024], ['390', 390, 844], ['360', 360, 800],
];
const BASE = process.env.AUREON_BASE || 'http://localhost:8080';
const OUT = process.env.AUREON_OUT || 'qa-new/phase-d';

(async () => {
  require('fs').mkdirSync(OUT, { recursive: true });
  const browser = await chromium.launch();
  const report = [];
  const overflow = [];
  for (const [vpName, w, h] of viewports) {
    const ctx = await browser.newContext({ viewport: { width: w, height: h } });
    const page = await ctx.newPage();
    for (const [name, route] of routes) {
      const errors = [];
      const h2 = m => { if (m.type() === 'error') errors.push(m.text().slice(0, 80)); };
      page.on('console', h2);
      try {
        await page.goto(BASE + route, { waitUntil: 'networkidle', timeout: 30000 });
        await page.waitForTimeout(700);
        await page.screenshot({ path: `${OUT}/${name}--${vpName}.png`, fullPage: false });
        // Horizontal overflow check (release gate: no horizontal scroll).
        const hasHScroll = await page.evaluate(() =>
          document.documentElement.scrollWidth > document.documentElement.clientWidth + 1
        );
        if (hasHScroll) overflow.push(`${name}@${vpName}`);
        report.push({ route: name, viewport: vpName, ok: true, errors: errors.length, overflow: hasHScroll });
      } catch (e) {
        report.push({ route: name, viewport: vpName, ok: false, error: String(e).slice(0, 100) });
      }
      page.off('console', h2);
    }
    await ctx.close();
  }
  await browser.close();
  require('fs').writeFileSync(`${OUT}/report.json`, JSON.stringify(report, null, 2));
  const bad = report.filter(r => !r.ok || r.errors > 1);
  console.log(`shots: ${report.length} | failed: ${bad.length} | h-overflow: ${overflow.length}`);
  bad.forEach(b => console.log('  BAD', b.route, b.viewport, b.error || (b.errors + ' console errors')));
  overflow.forEach(o => console.log('  OVERFLOW', o));
  process.exit(bad.length || overflow.length ? 1 : 0);
})();
