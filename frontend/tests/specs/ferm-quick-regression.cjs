/**
 * Ferm Living — Quick Regression (5 pages × 4 viewports)
 * 
 * Checks: HTTP 200, no Shopify calls, no blocking JS errors, image naturalWidth.
 * Fast: ~60 seconds.
 */
const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const BASE = 'http://localhost:8080';
const OUT = path.join(__dirname, '..', 'results', 'ferm-quick');
if (!fs.existsSync(OUT)) fs.mkdirSync(OUT, { recursive: true });

const PAGES = [
  { name: 'Homepage', path: '/' },
  { name: 'Product-Simple', path: '/product/meridian-lamp-black/' },
  { name: 'Product-Variable', path: '/product/rico-sofa-2-boucle-off-white/' },
  { name: 'Collection-Lighting', path: '/product-category/lighting/' },
  { name: 'Cart', path: '/cart/' },
  { name: 'Checkout', path: '/checkout/' },
  { name: 'Contact', path: '/contact/' },
];

const VPS = [
  { name: '1440', w: 1440, h: 900 },
  { name: '1024', w: 1024, h: 768 },
  { name: '768',  w: 768,  h: 1024 },
  { name: '390',  w: 390,  h: 844 },
];

const PROHIBITED = [/myshopify\.com/i, /cdn\.shopify\.com/i, /shop\.app\/checkout/i];
const BAD_CONSOLE = [/Shopify\s+is\s+not\s+defined/i, /Unexpected token/i];

(async () => {
  const browser = await chromium.launch({ headless: true });
  const results = [];
  let total = 0, passed = 0, failed = 0;

  for (const pg of PAGES) {
    for (const vp of VPS) {
      total++;
      const id = `${pg.name}_${vp.name}`;
      const page = await browser.newPage({ viewport: { width: vp.w, height: vp.h } });
      const errors = [];
      const netBad = [];
      const consoleBad = [];

      page.on('console', m => {
        const txt = m.text();
        if (BAD_CONSOLE.some(re => re.test(txt))) consoleBad.push(txt.slice(0, 120));
      });
      page.on('request', r => {
        if (PROHIBITED.some(re => re.test(r.url()))) netBad.push(r.url().slice(0, 120));
      });
      page.on('pageerror', e => errors.push(e.message?.slice(0, 120)));

      let status = 0;
      try {
        const resp = await page.goto(`${BASE}${pg.path}`, { waitUntil: 'domcontentloaded', timeout: 15000 });
        status = resp?.status() || 0;
        await page.waitForTimeout(2000);

        // Check images with naturalWidth > 0
        const imgs = await page.evaluate(() => {
          return Array.from(document.querySelectorAll('img')).map(i => ({
            src: i.src?.slice(0, 80),
            nw: i.naturalWidth,
            nh: i.naturalHeight,
          }));
        });
        const loadedImgs = imgs.filter(i => i.nw > 0).length;

        await page.screenshot({ path: path.join(OUT, `${id}.png`), fullPage: true });

        const ok = status < 400 && netBad.length === 0 && consoleBad.length === 0 && errors.length === 0;
        if (ok) passed++; else failed++;
        results.push({ id, status, imgs: imgs.length, loadedImgs, netBad: netBad.length, consoleBad: consoleBad.length, pageErrors: errors.length, ok });
      } catch (e) {
        failed++;
        results.push({ id, status: 0, error: e.message?.slice(0, 100), ok: false });
      }
      await page.close();
    }
  }

  await browser.close();

  // Summary
  console.log('\n═══ FERM QUICK REGRESSION ═══');
  console.log(`Total: ${total} | Passed: ${passed} | Failed: ${failed}\n`);
  
  const failedOnes = results.filter(r => !r.ok);
  if (failedOnes.length) {
    console.log('FAILURES:');
    failedOnes.forEach(r => console.log(`  ✗ ${r.id} — status=${r.status} netBad=${r.netBad||0} consoleBad=${r.consoleBad||0} errors=${r.pageErrors||0} ${r.error||''}`));
  }
  
  const passedOnes = results.filter(r => r.ok);
  if (passedOnes.length) {
    console.log('\nPASSES:');
    passedOnes.forEach(r => console.log(`  ✓ ${r.id} — status=${r.status} imgs=${r.loadedImgs}/${r.imgs}`));
  }

  fs.writeFileSync(path.join(OUT, 'results.json'), JSON.stringify(results, null, 2));
  console.log(`\nResults: ${path.join(OUT, 'results.json')}`);
  process.exit(failed > 0 ? 1 : 0);
})();
