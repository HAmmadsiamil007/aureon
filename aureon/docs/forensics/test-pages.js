const { chromium } = require('playwright');

const BASE = 'http://localhost:8080';

const ROUTES = [
  // Homepage
  { path: '/', name: 'Homepage', family: 'HOME' },

  // Shop / All Products
  { path: '/product-category/furniture/', name: 'Furniture Category', family: 'CATEGORY' },
  { path: '/product-category/lighting/', name: 'Lighting Category', family: 'CATEGORY' },
  { path: '/product-category/accessories/', name: 'Accessories Category', family: 'CATEGORY' },
  { path: '/product-category/kids/', name: 'Kids Category', family: 'CATEGORY' },
  { path: '/product-category/kitchen/', name: 'Kitchen Category', family: 'CATEGORY' },
  { path: '/product-category/textiles/', name: 'Textiles Category', family: 'CATEGORY' },
  { path: '/product-category/rugs/', name: 'Rugs Category', family: 'CATEGORY' },
  { path: '/product-category/outdoor/', name: 'Outdoor Category', family: 'CATEGORY' },
  { path: '/product-category/sofas/', name: 'Sofas Category', family: 'CATEGORY' },

  // Collections
  { path: '/product-category/new-arrivals/', name: 'New Arrivals', family: 'COLLECTION' },
  { path: '/product-category/bestsellers/', name: 'Bestsellers', family: 'COLLECTION' },
  { path: '/product-category/certified-products/', name: 'Certified', family: 'COLLECTION' },
  { path: '/product-category/sale/', name: 'Sale', family: 'COLLECTION' },

  // Product Detail — Real WC products (frozen HTML)
  { path: '/product/meridian-lamp-black/', name: 'Meridian Lamp Black', family: 'PRODUCT' },
  { path: '/product/rico-lounge-chair-raw-boucle-natural/', name: 'Rico Lounge Chair', family: 'PRODUCT' },
  { path: '/product/rico-sofa-2-boucle-off-white/', name: 'Rico Sofa 2', family: 'PRODUCT' },

  // Product Detail — Demo products (generic template)
  { path: '/product/pear-braided-storage/', name: 'Pear Braided Storage (demo)', family: 'PRODUCT' },
  { path: '/product/boda-dining-chair-red-brown/', name: 'Boda Dining Chair Red Brown (demo)', family: 'PRODUCT' },
  { path: '/product/arum-portable-lamp-cashmere/', name: 'Arum Portable Lamp Cashmere (demo)', family: 'PRODUCT' },
  { path: '/product/haze-wall-cabinet-35-x-100-cashmere/', name: 'Haze Wall Cabinet (demo)', family: 'PRODUCT' },
  { path: '/product/dapple-chair-dark-chocolate/', name: 'Dapple Chair (demo)', family: 'PRODUCT' },
  { path: '/product/mist-box-pearl/', name: 'Mist Box Pearl (demo)', family: 'PRODUCT' },

  // Static pages
  { path: '/about-ferm-living/', name: 'About', family: 'ABOUT' },
  { path: '/contact/', name: 'Contact', family: 'CONTACT' },
  { path: '/store-locator/', name: 'Store Locator', family: 'STORE_LOCATOR' },

  // Blog
  { path: '/blog/', name: 'Blog/Stories', family: 'BLOG' },

  // Cart / Checkout / Account
  { path: '/cart/', name: 'Cart', family: 'CART' },
  { path: '/checkout/', name: 'Checkout', family: 'CHECKOUT' },
  { path: '/my-account/', name: 'Account', family: 'ACCOUNT' },

  // 404
  { path: '/product/nonexistent-product-xyz/', name: 'Nonexistent Product (404)', family: '404' },
];

(async () => {
  const browser = await chromium.launch({ headless: true });
  const context = await browser.newContext({ viewport: { width: 1440, height: 900 } });

  const results = [];

  for (const route of ROUTES) {
    const page = await context.newPage();
    const url = BASE + route.path;
    const result = {
      name: route.name,
      path: route.path,
      family: route.family,
      url: url,
      status: null,
      title: null,
      bodyText: '',
      imageCount: 0,
      brokenImages: [],
      hasProductData: false,
      hasHeroSection: false,
      hasCollectionTemplate: false,
      hasProductPage: false,
      consoleErrors: [],
      networkErrors: [],
      loadTime: 0,
    };

    // Collect console errors
    page.on('console', msg => {
      if (msg.type() === 'error') {
        result.consoleErrors.push(msg.text());
      }
    });

    // Collect network errors
    page.on('requestfailed', request => {
      result.networkErrors.push(`${request.failure().errorText}: ${request.url()}`);
    });

    const startTime = Date.now();
    try {
      const response = await page.goto(url, { waitUntil: 'networkidle', timeout: 30000 });
      result.status = response ? response.status() : null;
      result.loadTime = Date.now() - startTime;
      result.title = await page.title();

      // Get body text (first 500 chars)
      result.bodyText = await page.evaluate(() => {
        return document.body ? document.body.innerText.substring(0, 500) : '';
      });

      // Count images
      const imageData = await page.evaluate(() => {
        const imgs = document.querySelectorAll('img');
        const broken = [];
        let loaded = 0;
        imgs.forEach(img => {
          if (img.complete && img.naturalWidth === 0 && img.src && !img.src.startsWith('data:')) {
            broken.push(img.src.substring(0, 120));
          } else if (img.src && !img.src.startsWith('data:')) {
            loaded++;
          }
        });
        return { total: imgs.length, loaded, broken };
      });
      result.imageCount = imageData.total;
      result.brokenImages = imageData.broken;

      // Check for FermPageData
      result.hasProductData = await page.evaluate(() => {
        return !!(window.FermPageData && window.FermPageData.product);
      });

      // Check page structure
      result.hasHeroSection = await page.evaluate(() => {
        return !!(document.querySelector('[data-component="collectionTemplate"]') ||
                   document.querySelector('[data-component="productPage"]') ||
                   document.querySelector('.hero') ||
                   document.querySelector('[data-hero]'));
      });

      result.hasCollectionTemplate = await page.evaluate(() => {
        return !!document.querySelector('[data-component="collectionTemplate"]');
      });

      result.hasProductPage = await page.evaluate(() => {
        return !!(
          document.querySelector('[data-component="productPage"]') ||
          document.querySelector('[data-component="addToCart"]') ||
          document.querySelector('[data-product-id]')
        );
      });

      // Check if correct page content is shown
      const isCorrectPage = await page.evaluate((expectedFamily) => {
        const body = document.body;
        if (!body) return false;
        const text = body.innerText || '';

        switch (expectedFamily) {
          case 'HOME':
            return text.includes('Ferm Living') || text.includes('Space to feel');
          case 'CATEGORY':
          case 'COLLECTION':
            return text.includes('Sort by') || text.includes('product') || text.includes('Product');
          case 'PRODUCT':
            return text.includes('Add to Cart') || text.includes('EUR') || text.includes('€');
          case 'ABOUT':
            return text.includes('About') || text.includes('Ferm Living');
          case 'CONTACT':
            return text.includes('Contact') || text.includes('Get in Touch');
          case 'BLOG':
            return text.includes('Stories') || text.includes('Blog') || text.includes('Journal');
          case 'CART':
            return text.includes('Cart') || text.includes('cart');
          case 'CHECKOUT':
            return text.includes('Checkout') || text.includes('checkout') || text.includes('Payment');
          case 'ACCOUNT':
            return text.includes('Login') || text.includes('Account') || text.includes('Username');
          case '404':
            return text.includes('Lost') || text.includes('404') || text.includes('not found') || text.includes('Contact');
          default:
            return true;
        }
      }, route.family);

      result.isCorrectPage = isCorrectPage;

    } catch (err) {
      result.status = 'ERROR';
      result.loadTime = Date.now() - startTime;
      result.consoleErrors.push(err.message);
    }

    results.push(result);
    await page.close();
  }

  // Print results
  console.log('\n' + '='.repeat(120));
  console.log('FERM LIVING — COMPLETE PAGE AUDIT REPORT');
  console.log('='.repeat(120));
  console.log(`Total routes tested: ${results.length}`);
  console.log(`Date: ${new Date().toISOString()}`);
  console.log('='.repeat(120));

  let passCount = 0;
  let failCount = 0;
  let warnCount = 0;

  for (const r of results) {
    const statusIcon = r.status === 200 ? '✅' : r.status === 'ERROR' ? '❌' : '⚠️';
    const pageIcon = r.isCorrectPage ? '✅' : '❌';
    const imageIcon = r.brokenImages.length === 0 ? '✅' : '❌';

    let verdict = 'PASS';
    if (r.status !== 200 || !r.isCorrectPage || r.brokenImages.length > 0) {
      verdict = 'FAIL';
      failCount++;
    } else {
      passCount++;
    }

    console.log(`\n${statusIcon} ${r.name} (${r.path})`);
    console.log(`   Status: ${r.status} | Load: ${r.loadTime}ms | Title: ${(r.title || '').substring(0, 60)}`);
    console.log(`   Correct page: ${pageIcon} | Images: ${r.imageCount} loaded, ${r.brokenImages.length} broken ${imageIcon}`);
    console.log(`   FermPageData.product: ${r.hasProductData ? 'YES' : 'no'} | Collection template: ${r.hasCollectionTemplate ? 'YES' : 'no'} | Product page: ${r.hasProductPage ? 'YES' : 'no'}`);

    if (r.brokenImages.length > 0) {
      console.log(`   BROKEN IMAGES:`);
      r.brokenImages.forEach(img => console.log(`     - ${img}`));
    }

    if (r.consoleErrors.length > 0) {
      console.log(`   CONSOLE ERRORS: ${r.consoleErrors.length}`);
      r.consoleErrors.slice(0, 3).forEach(e => console.log(`     - ${e.substring(0, 120)}`));
    }

    if (r.networkErrors.length > 0) {
      console.log(`   NETWORK ERRORS: ${r.networkErrors.length}`);
      r.networkErrors.slice(0, 3).forEach(e => console.log(`     - ${e.substring(0, 120)}`));
    }

    // Body text preview
    const bodyPreview = r.bodyText.replace(/\n/g, ' ').substring(0, 150);
    console.log(`   Content: "${bodyPreview}..."`);
  }

  console.log('\n' + '='.repeat(120));
  console.log('SUMMARY');
  console.log('='.repeat(120));
  console.log(`PASS: ${passCount} | FAIL: ${failCount} | Total: ${results.length}`);

  // List all failures
  const failures = results.filter(r => r.status !== 200 || !r.isCorrectPage || r.brokenImages.length > 0);
  if (failures.length > 0) {
    console.log('\nFAILURES:');
    failures.forEach(f => {
      const issues = [];
      if (f.status !== 200) issues.push(`HTTP ${f.status}`);
      if (!f.isCorrectPage) issues.push('wrong page content');
      if (f.brokenImages.length > 0) issues.push(`${f.brokenImages.length} broken images`);
      if (f.networkErrors.length > 0) issues.push(`${f.networkErrors.length} network errors`);
      console.log(`  ❌ ${f.name} (${f.path}): ${issues.join(', ')}`);
    });
  }

  console.log('='.repeat(120));

  await browser.close();
})();
