const { chromium } = require('playwright');
const routes = [
  ['home', '/'], ['shop', '/shop/'], ['category', '/product-category/women/'],
  ['search', '/?s=kurta'], ['blog', '/blog/'], ['blog-single', '/hello-world/'],
  ['cart', '/cart/'], ['checkout', '/checkout/'], ['account', '/my-account/'], ['404', '/nonexistent-xyz-404/'],
];
(async () => {
  const browser = await chromium.launch();
  const ctx = await browser.newContext({ viewport: { width: 1440, height: 900 } });
  const page = await ctx.newPage();
  const results = {};
  for (const [name, route] of routes) {
    const reqs = [];
    const handler = r => {
      const t = r.resourceType();
      reqs.push({ type: t, url: r.url(), bytes: 0 });
    };
    const respHandler = async r => {
      try { const b = await r.body(); const e = reqs.find(x => x.url === r.url()); if (e) e.bytes = b.length; } catch (e) {}
    };
    page.on('request', handler);
    page.on('response', respHandler);
    await page.goto('http://localhost:8080' + route, { waitUntil: 'networkidle', timeout: 45000 });
    await page.waitForTimeout(500);
    page.off('request', handler);
    page.off('response', respHandler);
    const byType = {};
    let total = 0;
    for (const r of reqs) {
      byType[r.type] = byType[r.type] || { count: 0, bytes: 0 };
      byType[r.type].count++; byType[r.type].bytes += r.bytes; total += r.bytes;
    }
    results[name] = { route, requests: reqs.length, totalBytes: total, byType };
  }
  require('fs').writeFileSync('qa-new/perf-BEFORE.json', JSON.stringify(results, null, 2));
  for (const [k, v] of Object.entries(results)) {
    const mb = (v.totalBytes / 1048576).toFixed(2);
    const types = Object.entries(v.byType).map(([t, d]) => `${t}:${d.count}/${(d.bytes / 1048576).toFixed(2)}MB`).join(' ');
    console.log(k.padEnd(12), String(v.requests).padStart(3), 'reqs', mb + 'MB |', types);
  }
  await browser.close();
})();
