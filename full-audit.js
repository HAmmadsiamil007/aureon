const { chromium } = require('playwright');
const fs = require('fs');

const BASE = 'http://localhost:8080';
const RESULTS_DIR = 'test-results';

const PAGES = [
  { name: 'Homepage', url: '/', type: 'home' },
  { name: 'Shop', url: '/shop', type: 'shop' },
  { name: 'Collection: Furniture', url: '/product-category/furniture', type: 'collection' },
  { name: 'Collection: Lighting', url: '/product-category/lighting', type: 'collection' },
  { name: 'Collection: Accessories', url: '/product-category/accessories', type: 'collection' },
  { name: 'Collection: Kids', url: '/product-category/kids', type: 'collection' },
  { name: 'Collection: Kitchen', url: '/product-category/kitchen', type: 'collection' },
  { name: 'Collection: Textiles', url: '/product-category/textiles', type: 'collection' },
  { name: 'Collection: Rugs', url: '/product-category/rugs', type: 'collection' },
  { name: 'Collection: Outdoor', url: '/product-category/outdoor', type: 'collection' },
  { name: 'Collection: Sofas', url: '/product-category/sofas', type: 'collection' },
  { name: 'Collection: New Arrivals', url: '/product-category/new-arrivals', type: 'collection' },
  { name: 'Collection: Bestsellers', url: '/product-category/bestsellers', type: 'collection' },
  { name: 'Collection: Certified', url: '/product-category/certified-products', type: 'collection' },
  { name: 'Collection: Sale', url: '/product-category/sale', type: 'collection' },
  { name: 'Product: Rico Lounge Chair', url: '/product/rico-lounge-chair-raw-boucle-natural', type: 'product' },
  { name: 'Product: Meridian Lamp', url: '/product/meridian-lamp-black', type: 'product' },
  { name: 'Product: Rico Sofa', url: '/product/rico-sofa-2-boucle-off-white', type: 'product' },
  { name: 'Product: Generic Fallback', url: '/product/generic-test-product', type: 'product' },
  { name: 'Blog', url: '/blog', type: 'blog' },
  { name: 'Contact', url: '/contact', type: 'page' },
  { name: 'About', url: '/about-ferm-living', type: 'page' },
  { name: 'Store Locator', url: '/store-locator', type: 'page' },
  { name: 'Cart', url: '/cart', type: 'cart' },
  { name: 'Checkout', url: '/checkout', type: 'checkout' },
  { name: 'Account Login', url: '/my-account', type: 'account' },
  { name: 'Search', url: '/?s=test', type: 'search' },
  { name: '404 Page', url: '/this-page-does-not-exist-xyz', type: '404' },
];

(async () => {
  if (!fs.existsSync(RESULTS_DIR)) fs.mkdirSync(RESULTS_DIR, { recursive: true });

  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({ viewport: { width: 1440, height: 900 } });

  const results = [];

  for (const pg of PAGES) {
    const page = await context.newPage();
    const consoleErrors = [];
    const networkErrors = [];
    const brokenImages = [];

    page.on('console', msg => {
      if (msg.type() === 'error') consoleErrors.push(msg.text());
    });

    page.on('response', response => {
      if (response.status() >= 400) {
        networkErrors.push({ url: response.url(), status: response.status() });
      }
    });

    let httpStatus = 0;
    let title = '';
    let bodyLength = 0;

    try {
      const response = await page.goto(`${BASE}${pg.url}`, { waitUntil: 'domcontentloaded', timeout: 20000 });
      httpStatus = response ? response.status() : 0;
      title = await page.title();

      await page.waitForTimeout(3000);

      const images = await page.$$eval('img', imgs => imgs.map(img => ({
        src: img.src,
        alt: img.alt || '',
        naturalWidth: img.naturalWidth,
        naturalHeight: img.naturalHeight,
        complete: img.complete,
      })));

      for (const img of images) {
        if (img.complete && (img.naturalWidth === 0 || img.naturalHeight === 0)) {
          brokenImages.push({ src: img.src, alt: img.alt });
        }
      }

      bodyLength = await page.evaluate(() => document.body ? document.body.innerText.length : 0);

      const ssName = pg.name.replace(/[^a-zA-Z0-9]/g, '-').toLowerCase();
      await page.screenshot({ path: `${RESULTS_DIR}/${ssName}.png`, fullPage: false, scale: 'css', type: 'png' });

    } catch (err) {
      httpStatus = err.message.includes('Timeout') ? 408 : 500;
      title = 'ERROR: ' + err.message;
    }

    results.push({
      name: pg.name,
      url: pg.url,
      type: pg.type,
      httpStatus,
      title,
      bodyLength,
      status: httpStatus >= 200 && httpStatus < 400 ? 'PASS' : 'FAIL',
      brokenImageCount: brokenImages.length,
      brokenImages: brokenImages.slice(0, 10),
      networkErrorCount: networkErrors.length,
      networkErrors: networkErrors.slice(0, 10),
      consoleErrorCount: consoleErrors.length,
    });

    console.log(`[${httpStatus >= 200 && httpStatus < 400 ? 'PASS' : 'FAIL'}] ${pg.name} (${pg.url}) - HTTP ${httpStatus} - Broken imgs: ${brokenImages.length} - Content: ${bodyLength} chars`);

    await page.close();
  }

  await browser.close();

  fs.writeFileSync(`${RESULTS_DIR}/audit-results.json`, JSON.stringify(results, null, 2));

  console.log('\n=== SUMMARY ===');
  const passed = results.filter(r => r.status === 'PASS').length;
  const failed = results.filter(r => r.status === 'FAIL').length;
  const withBroken = results.filter(r => r.brokenImageCount > 0);
  console.log(`Pages tested: ${results.length}`);
  console.log(`HTTP PASS: ${passed}`);
  console.log(`HTTP FAIL: ${failed}`);
  console.log(`Pages with broken images: ${withBroken.length}`);
  console.log(`Total broken images: ${results.reduce((s, r) => s + r.brokenImageCount, 0)}`);
  
  if (withBroken.length > 0) {
    console.log('\n=== BROKEN IMAGES BY PAGE ===');
    for (const r of withBroken) {
      console.log(`\n${r.name} (${r.url}):`);
      for (const img of r.brokenImages) {
        console.log(`  - ${img.src}`);
      }
    }
  }

  const failPages = results.filter(r => r.status === 'FAIL');
  if (failPages.length > 0) {
    console.log('\n=== FAILED PAGES ===');
    for (const r of failPages) {
      console.log(`  ${r.name} (${r.url}) - HTTP ${r.httpStatus}`);
    }
  }
})();
