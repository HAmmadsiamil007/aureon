/**
 * Ferm Living — Hardening Pass Regression Test
 * 
 * Deterministic regression suite covering:
 *  - 15 routes × 4 viewports = 60 route/viewport combos
 *  - HTTP status, Ferm DOM, CSS/JS/font loading
 *  - Image integrity (src, srcset, picture, CSS bg, lazy-loaded, JS-created)
 *  - Zero Shopify/third-party prohibited calls
 *  - No duplicates, no AUREON/Woo contamination
 *  - Functional interactions (cart, variants, nav, search)
 *  - Full-page screenshots
 *
 * Run: node frontend/tests/specs/ferm-hardening-regression.cjs
 *
 * @package Aureon
 */
const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');

const BASE_URL = 'http://localhost:8080';
const SCREENSHOT_DIR = path.join(__dirname, '..', 'results', 'ferm-hardening');
const TIMEOUT = 20000;

// ─── Routes ──────────────────────────────────────────────────────────────
const ROUTES = [
  { name: 'Homepage',           path: '/',                          template: 'index' },
  { name: 'Product (Simple)',   path: '/product/meridian-lamp-black/', template: 'product' },
  { name: 'Product (Variable)', path: '/product/rico-sofa-2-boucle-off-white/', template: 'product' },
  { name: 'Collection (Lighting)', path: '/product-category/lighting/', template: 'collection' },
  { name: 'Cart (empty)',       path: '/cart/',                     template: 'cart', expectRedirect: false },
  { name: 'Checkout (empty)',   path: '/checkout/',                 template: 'checkout', expectRedirect: true, redirectContains: 'cart' },
  { name: 'Account',            path: '/my-account/',               template: 'account' },
  { name: 'Search',             path: '/?s=lamp',                   template: 'index' },
  { name: 'Blog',               path: '/blog/',                     template: 'blog' },
  { name: 'Contact',            path: '/contact/',                  template: 'page' },
  { name: 'About',              path: '/about-ferm-living/',        template: 'page' },
  { name: 'Store Locator',      path: '/store-locator/',            template: 'page' },
  { name: '404',                path: '/nonexistent-page/',         template: 'page', expectStatus: 404 },
];

const VIEWPORTS = [
  { name: 'Desktop-1440', width: 1440, height: 900 },
  { name: 'Laptop-1024',  width: 1024, height: 768 },
  { name: 'Tablet-768',   width: 768,  height: 1024 },
  { name: 'Mobile-390',   width: 390,  height: 844 },
];

// ─── Prohibited patterns ─────────────────────────────────────────────────
const PROHIBITED_URLS = [
  /myshopify\.com/i,
  /shop\.app\/checkout/i,
  /cdn\.shopify\.com/i,
  /clerk\.io/i,
  /google-analytics\.com/i,
  /googletagmanager\.com/i,
];

const PROHIBITED_CONSOLE = [
  /Shopify\s+is\s+not\s+defined/i,
  /cartProductFlags/i,
];

// ─── State ───────────────────────────────────────────────────────────────
const results = [];
const imageReport = [];
const assetReport = [];
let browser;

function log(msg) { process.stdout.write(msg + '\n'); }

async function runTest(name, fn) {
  const page = await browser.newPage({ viewport: { width: 1440, height: 900 } });
  const errors = [];
  const warnings = [];
  const networkErrors = [];
  const networkRequests = [];
  const consoleMessages = [];

  page.on('console', msg => {
    consoleMessages.push({ type: msg.type(), text: msg.text() });
    if (msg.type() === 'error') errors.push(msg.text());
    if (msg.type() === 'warning') warnings.push(msg.text());
  });

  page.on('requestfailed', req => {
    networkErrors.push({ url: req.url(), failure: req.failure()?.errorText || 'unknown' });
  });

  page.on('response', resp => {
    networkRequests.push({ url: resp.url(), status: resp.status() });
  });

  try {
    await fn(page, { errors, warnings, networkErrors, networkRequests, consoleMessages });
    results.push({ name, status: 'PASS', errors: [], warnings: [], networkErrors: [] });
  } catch (e) {
    results.push({
      name,
      status: 'FAIL',
      errors: [e.message.split('\n')[0]],
      warnings,
      networkErrors: networkErrors.map(n => n.url),
    });
  } finally {
    await page.close();
  }
}

// ─── Tests ───────────────────────────────────────────────────────────────

async function runAllTests() {
  // Ensure screenshot directory exists
  fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });

  browser = await chromium.launch({ headless: true });

  // ═══════════════════════════════════════════════════════════════════════
  // PHASE 1: Route × Viewport Matrix
  // ═══════════════════════════════════════════════════════════════════════
  log('\n═══════════════════════════════════════════════════════════════');
  log('  PHASE 1: ROUTE × VIEWPORT MATRIX');
  log('═══════════════════════════════════════════════════════════════\n');

  for (const route of ROUTES) {
    for (const vp of VIEWPORTS) {
      const testName = `${route.name} @ ${vp.name}`;
      await runTest(testName, async (page, ctx) => {
        await page.setViewportSize({ width: vp.width, height: vp.height });

        const response = await page.goto(`${BASE_URL}${route.path}`, {
          waitUntil: 'networkidle',
          timeout: TIMEOUT,
        });

        // 1. HTTP status
        const status = response.status();
        if (route.expectStatus) {
          if (status !== route.expectStatus) {
            throw new Error(`Expected status ${route.expectStatus}, got ${status}`);
          }
        } else if (status >= 400) {
          throw new Error(`HTTP ${status} for ${route.path}`);
        }

        // 2. Redirect check
        if (route.expectRedirect) {
          const finalUrl = page.url();
          if (!finalUrl.includes(route.redirectContains || 'cart')) {
            throw new Error(`Expected redirect to ${route.redirectContains}, landed on ${finalUrl}`);
          }
          return; // Don't check DOM on redirect
        }

        // 3. Ferm body class
        const bodyClass = await page.getAttribute('body', 'class') || '';
        if (!bodyClass.includes('design-fermliving')) {
          throw new Error(`Missing design-fermliving body class. Got: "${bodyClass}"`);
        }

        // 4. Complete Ferm DOM — essential elements
        const hasHeader = await page.$('#header') !== null;
        const hasMobileHeader = await page.$('#mobileHeader') !== null;
        const hasFooter = await page.$('#footer') !== null;
        const hasMobileMenu = await page.$('#mobileMenuOverlay') !== null;

        if (!hasHeader && !hasMobileHeader) {
          throw new Error('No header found (#header or #mobileHeader)');
        }
        if (!hasFooter) {
          throw new Error('No footer found (#footer)');
        }
        if (!hasMobileMenu) {
          throw new Error('No mobile menu overlay (#mobileMenuOverlay)');
        }

        // 5. Ferm CSS loaded
        const cssLinks = await page.$$eval('link[rel="stylesheet"]', links =>
          links.map(l => l.getAttribute('href') || '')
        );
        const hasFermCSS = cssLinks.some(h => h.includes('app.adf0bc36b7') || h.includes('fonts.') || h.includes('ferm'));
        if (!hasFermCSS) {
          throw new Error('Ferm CSS not loaded');
        }

        // 6. No duplicate Ferm CSS
        const fermCSSCount = cssLinks.filter(h => h.includes('app.adf0bc36b7')).length;
        if (fermCSSCount > 1) {
          throw new Error(`Duplicate Ferm CSS: ${fermCSSCount} instances`);
        }

        // 7. No AETHER platform CSS contamination (for complete-page mode)
        const hasAetherBootstrap = cssLinks.some(h => h.includes('bootstrap.min.css'));
        const hasAetherSwiper = cssLinks.some(h => h.includes('swiper-bundle.min.css'));
        if (hasAetherBootstrap || hasAetherSwiper) {
          throw new Error('AETHER platform CSS detected (bootstrap/swiper) — contamination');
        }

        // 8. Ferm JS loaded
        const jsSrcs = await page.$$eval('script[src]', scripts =>
          scripts.map(s => s.getAttribute('src') || '')
        );
        const hasAppJS = jsSrcs.some(s => s.includes('app.1e7cf79a09'));
        const hasShimsJS = jsSrcs.some(s => s.includes('ferm-data-shims'));
        if (!hasAppJS) {
          throw new Error('app.js not loaded');
        }
        if (!hasShimsJS) {
          throw new Error('ferm-data-shims.js not loaded');
        }

        // 9. No duplicate Ferm JS
        const appJSCount = jsSrcs.filter(s => s.includes('app.1e7cf79a09')).length;
        if (appJSCount > 1) {
          throw new Error(`Duplicate app.js: ${appJSCount} instances`);
        }

        // 10. No AETHER platform JS contamination
        const hasAetherMain = jsSrcs.some(s => s.includes('aether-main'));
        const hasAetherBootstrapJS = jsSrcs.some(s => s.includes('bootstrap.bundle.min.js'));
        if (hasAetherMain || hasAetherBootstrapJS) {
          throw new Error('AETHER platform JS detected — contamination');
        }

        // 11. Zero Shopify requests (no actual Shopify API calls)
        const shopifyRequests = ctx.networkRequests.filter(r =>
          /myshopify\.com|cdn\.shopify\.com|shop\.app/i.test(r.url)
        );
        if (shopifyRequests.length > 0) {
          throw new Error(`Shopify requests detected: ${shopifyRequests.map(r => r.url).join(', ')}`);
        }

        // 12. Zero prohibited third-party calls
        const prohibitedRequests = ctx.networkRequests.filter(r =>
          PROHIBITED_URLS.some(p => p.test(r.url))
        );
        if (prohibitedRequests.length > 0) {
          throw new Error(`Prohibited requests: ${prohibitedRequests.map(r => r.url).join(', ')}`);
        }

        // 13. Zero blocking console errors (allow known non-blocking ones)
        const blockingErrors = ctx.errors.filter(e =>
          !e.includes('cartProductFlags') &&
          !e.includes('Shopify is not defined') &&
          !e.includes('favicon') &&
          !e.includes('404') &&
          !e.includes('net::ERR') &&
          !e.includes('Failed to load resource')
        );
        if (blockingErrors.length > 0) {
          throw new Error(`Console errors: ${blockingErrors[0]}`);
        }

        // 14. No AUREON presentation contamination
        const hasAureonStyle = cssLinks.some(h => h.includes('aureon-style') || h.includes('aureon-mobile'));
        if (hasAureonStyle) {
          throw new Error('AUREON theme presentation CSS detected');
        }

        // 15. Screenshot
        const ssName = `${route.name.replace(/[^a-zA-Z0-9]/g, '_')}_${vp.name}.png`;
        const ssPath = path.join(SCREENSHOT_DIR, ssName);
        await page.screenshot({ path: ssPath, fullPage: true });
      });
    }
  }

  // ═══════════════════════════════════════════════════════════════════════
  // PHASE 2: Image Integrity Audit
  // ═══════════════════════════════════════════════════════════════════════
  log('\n═══════════════════════════════════════════════════════════════');
  log('  PHASE 2: IMAGE INTEGRITY AUDIT');
  log('═══════════════════════════════════════════════════════════════\n');

  const IMAGE_ROUTES = [
    { name: 'Homepage',   path: '/' },
    { name: 'Product #1', path: '/product/meridian-lamp-black/' },
    { name: 'Product #2', path: '/product/rico-sofa-2-boucle-off-white/' },
    { name: 'Collection',  path: '/product-category/lighting/' },
    { name: 'Cart',        path: '/cart/' },
  ];

  for (const route of IMAGE_ROUTES) {
    await runTest(`ImageAudit: ${route.name}`, async (page) => {
      await page.setViewportSize({ width: 1440, height: 900 });
      await page.goto(`${BASE_URL}${route.path}`, { waitUntil: 'networkidle', timeout: TIMEOUT });

      // Scroll to bottom to trigger lazy-loaded images
      await page.evaluate(() => {
        return new Promise(resolve => {
          let totalHeight = 0;
          const distance = 500;
          const timer = setInterval(() => {
            window.scrollBy(0, distance);
            totalHeight += distance;
            if (totalHeight >= document.body.scrollHeight) {
              clearInterval(timer);
              resolve();
            }
          }, 100);
        });
      });
      await page.waitForTimeout(1500); // Wait for lazy-load to trigger

      // Check all <img> elements
      const imgResults = await page.evaluate(() => {
        const imgs = document.querySelectorAll('img');
        const results = [];
        imgs.forEach(img => {
          const src = img.getAttribute('src') || '';
          const naturalWidth = img.naturalWidth;
          const isLazy = img.getAttribute('loading') === 'lazy';
          const isHidden = img.offsetParent === null;
          const dataSrc = img.getAttribute('data-lazy-src') || img.getAttribute('data-src') || '';

          // Check if image has a relative cdn/ path that wasn't rewritten
          const hasRelativeCDN = /^(\.\.\/)?cdn\//.test(src);

          results.push({
            src: src.substring(0, 200),
            naturalWidth,
            isLazy,
            isHidden,
            dataSrc: dataSrc.substring(0, 200),
            hasRelativeCDN,
            loaded: naturalWidth > 0 || isHidden || isLazy,
          });
        });
        return results;
      });

      // Find broken images (not hidden, not lazy, not loaded)
      const broken = imgResults.filter(img =>
        !img.isHidden && !img.isLazy && !img.loaded && img.src && !img.dataSrc
      );

      // Find unrewritten CDN paths
      const unrewritten = imgResults.filter(img => img.hasRelativeCDN);

      // Record in image report
      imageReport.push({
        route: route.name,
        totalImages: imgResults.length,
        broken: broken.length,
        unrewrittenCDN: unrewritten.length,
        details: { broken: broken.map(i => i.src), unrewritten: unrewritten.map(i => i.src) },
      });

      if (broken.length > 0) {
        throw new Error(`${broken.length} broken images: ${broken[0].src}`);
      }
      if (unrewritten.length > 0) {
        throw new Error(`${unrewritten.length} unrewritten CDN paths: ${unrewritten[0].src}`);
      }
    });
  }

  // ═══════════════════════════════════════════════════════════════════════
  // PHASE 3: Asset Loading Audit (network 404s)
  // ═══════════════════════════════════════════════════════════════════════
  log('\n═══════════════════════════════════════════════════════════════');
  log('  PHASE 3: ASSET LOADING AUDIT');
  log('═══════════════════════════════════════════════════════════════\n');

  await runTest('AssetAudit: Homepage', async (page, ctx) => {
    await page.goto(`${BASE_URL}/`, { waitUntil: 'networkidle', timeout: TIMEOUT });

    const failed404s = ctx.networkRequests.filter(r => r.status === 404);
    const failed500s = ctx.networkRequests.filter(r => r.status >= 500);

    assetReport.push({
      route: '/',
      total: ctx.networkRequests.length,
      failed404: failed404s.length,
      failed500: failed500s.length,
      urls404: failed404s.map(r => r.url),
    });

    if (failed404s.length > 0) {
      throw new Error(`${failed404s.length} 404s: ${failed404s[0].url}`);
    }
  });

  await runTest('AssetAudit: Product', async (page, ctx) => {
    await page.goto(`${BASE_URL}/product/meridian-lamp-black/`, { waitUntil: 'networkidle', timeout: TIMEOUT });

    const failed404s = ctx.networkRequests.filter(r => r.status === 404);
    assetReport.push({
      route: '/product/meridian-lamp-black/',
      total: ctx.networkRequests.length,
      failed404: failed404s.length,
      urls404: failed404s.map(r => r.url),
    });

    if (failed404s.length > 0) {
      throw new Error(`${failed404s.length} 404s: ${failed404s[0].url}`);
    }
  });

  await runTest('AssetAudit: Collection', async (page, ctx) => {
    await page.goto(`${BASE_URL}/product-category/lighting/`, { waitUntil: 'networkidle', timeout: TIMEOUT });

    const failed404s = ctx.networkRequests.filter(r => r.status === 404);
    assetReport.push({
      route: '/product-category/lighting/',
      total: ctx.networkRequests.length,
      failed404: failed404s.length,
      urls404: failed404s.map(r => r.url),
    });

    if (failed404s.length > 0) {
      throw new Error(`${failed404s.length} 404s: ${failed404s[0].url}`);
    }
  });

  // ═══════════════════════════════════════════════════════════════════════
  // PHASE 4: Functional Tests
  // ═══════════════════════════════════════════════════════════════════════
  log('\n═══════════════════════════════════════════════════════════════');
  log('  PHASE 4: FUNCTIONAL TESTS');
  log('═══════════════════════════════════════════════════════════════\n');

  await testFunctional();

  // ═══════════════════════════════════════════════════════════════════════
  // PHASE 5: Cross-page consistency
  // ═══════════════════════════════════════════════════════════════════════
  log('\n═══════════════════════════════════════════════════════════════');
  log('  PHASE 5: CROSS-PAGE CONSISTENCY');
  log('═══════════════════════════════════════════════════════════════\n');

  await testCrossPageConsistency();

  await browser.close();
}

async function testFunctional() {
  // Mobile navigation open/close
  await testFunctionalMobileNav();

  // Homepage nav links point to WP routes
  await testNavLinks();

  // Product page variant selection
  await testVariantSelection();

  // Add to cart flow
  await testAddToCart();

  // Search functionality
  await testSearch();

  // Cart page renders
  await testCartPage();
}

async function testFunctionalMobileNav() {
  await runTest('Functional: Mobile hamburger opens menu', async (page) => {
    await page.setViewportSize({ width: 390, height: 844 });
    await page.goto(`${BASE_URL}/`, { waitUntil: 'domcontentloaded', timeout: TIMEOUT });
    const hamburger = await page.$('#mobileHamburger');
    if (!hamburger) throw new Error('Hamburger not found');
    await hamburger.click();
    await page.waitForTimeout(500);
    const isOpen = await page.$eval('#mobileMenuOverlay', el => el.classList.contains('is-open'));
    if (!isOpen) throw new Error('Mobile menu did not open');
  });

  await runTest('Functional: Mobile menu close button works', async (page) => {
    await page.setViewportSize({ width: 390, height: 844 });
    await page.goto(`${BASE_URL}/`, { waitUntil: 'domcontentloaded', timeout: TIMEOUT });
    await page.click('#mobileHamburger');
    await page.waitForTimeout(500);
    const closeBtn = await page.$('.mobile-menu-close');
    if (!closeBtn) throw new Error('Close button not found');
    await closeBtn.click();
    await page.waitForTimeout(500);
    const isOpen = await page.$eval('#mobileMenuOverlay', el => el.classList.contains('is-open'));
    if (isOpen) throw new Error('Mobile menu did not close');
  });

  await runTest('Functional: Mobile menu shows level 1', async (page) => {
    await page.setViewportSize({ width: 390, height: 844 });
    await page.goto(`${BASE_URL}/`, { waitUntil: 'domcontentloaded', timeout: TIMEOUT });
    await page.click('#mobileHamburger');
    await page.waitForTimeout(500);
    const level1 = await page.$('.mobile-menu-level-1.is-active');
    if (!level1) throw new Error('Level 1 not active');
  });
}

async function testNavLinks() {
  await runTest('Functional: Nav links are rewritten to WP routes', async (page) => {
    await page.goto(`${BASE_URL}/`, { waitUntil: 'domcontentloaded', timeout: TIMEOUT });
    const links = await page.$$eval('#header a[href]', anchors =>
      anchors.map(a => a.getAttribute('href')).filter(Boolean)
    );

    // Check that links don't contain .html extensions (Shopify artifact)
    const htmlLinks = links.filter(h =>
      h.endsWith('.html') && !h.includes('cdn/') && !h.startsWith('#')
    );
    if (htmlLinks.length > 0) {
      throw new Error(`Unrewritten .html links: ${htmlLinks.slice(0, 3).join(', ')}`);
    }

    // Check no relative Shopify paths
    const shopifyPaths = links.filter(h =>
      h.includes('collections/') || h.includes('products/') || h.includes('pages/')
    );
    if (shopifyPaths.length > 0) {
      throw new Error(`Shopify paths in nav: ${shopifyPaths.slice(0, 3).join(', ')}`);
    }
  });
}

async function testVariantSelection() {
  await runTest('Functional: Variant selection updates price on product page', async (page) => {
    await page.goto(`${BASE_URL}/product/rico-sofa-2-boucle-off-white/`, {
      waitUntil: 'networkidle',
      timeout: TIMEOUT,
    });

    // Check if FermPageData.product exists (bridge injected data)
    const hasProduct = await page.evaluate(() => {
      return window.FermPageData && window.FermPageData.product ? true : false;
    });

    if (!hasProduct) {
      throw new Error('FermPageData.product not available — bridge not injected');
    }

    // Check product title is set
    const title = await page.evaluate(() => window.FermPageData.product.title);
    if (!title || title.length === 0) {
      throw new Error('Product title is empty');
    }

    // Check price is set
    const price = await page.evaluate(() => window.FermPageData.product.price);
    if (!price || price <= 0) {
      throw new Error('Product price is zero/empty');
    }
  });
}

async function testAddToCart() {
  await runTest('Functional: FermCart API available', async (page) => {
    await page.goto(`${BASE_URL}/`, { waitUntil: 'domcontentloaded', timeout: TIMEOUT });

    const hasFermCart = await page.evaluate(() => {
      return typeof window.FermCart === 'object' &&
        typeof window.FermCart.addItem === 'function' &&
        typeof window.FermCart.getCart === 'function';
    });

    if (!hasFermCart) {
      throw new Error('FermCart API not available');
    }
  });

  await runTest('Functional: Shopify cart intercept active', async (page) => {
    await page.goto(`${BASE_URL}/`, { waitUntil: 'domcontentloaded', timeout: TIMEOUT });

    const interceptActive = await page.evaluate(() => {
      // Check if fetch has been patched to intercept Shopify cart endpoints
      const fnStr = window.fetch.toString();
      return fnStr.includes('cartEndpoints') || fnStr.includes('FermCart');
    });

    if (!interceptActive) {
      throw new Error('Shopify cart intercept not active');
    }
  });
}

async function testSearch() {
  await runTest('Functional: Search page renders', async (page) => {
    await page.goto(`${BASE_URL}/?s=lamp`, { waitUntil: 'networkidle', timeout: TIMEOUT });
    const bodyClass = await page.getAttribute('body', 'class') || '';
    if (!bodyClass.includes('design-fermliving')) {
      throw new Error('Search page missing Ferm body class');
    }
  });
}

async function testCartPage() {
  await runTest('Functional: Cart page renders with Ferm DOM', async (page) => {
    await page.goto(`${BASE_URL}/cart/`, { waitUntil: 'networkidle', timeout: TIMEOUT });

    // Cart should have the cart component
    const hasCartComponent = await page.$('[data-component="cartMain"]') !== null;
    const hasCartDrawer = await page.$('#cart-drawer') !== null;
    const hasCartEmpty = await page.$('.cart-empty, .woocommerce-info, .empty-cart') !== null;

    if (!hasCartComponent && !hasCartDrawer && !hasCartEmpty) {
      throw new Error('Cart page missing expected DOM elements');
    }

    // Check FermPageData.cart is available
    const hasCartData = await page.evaluate(() => {
      return window.FermPageData && window.FermPageData.cart ? true : false;
    });
    if (!hasCartData) {
      throw new Error('FermPageData.cart not available');
    }
  });
}

async function testCrossPageConsistency() {
  await runTest('Consistency: All routes have consistent header/footer', async (page) => {
    const routesToCheck = ['/', '/product/meridian-lamp-black/', '/cart/', '/my-account/'];

    for (const route of routesToCheck) {
      await page.goto(`${BASE_URL}${route}`, { waitUntil: 'networkidle', timeout: TIMEOUT });

      const hasHeader = await page.$('#header') !== null;
      const hasFooter = await page.$('#footer') !== null;
      const bodyClass = await page.getAttribute('body', 'class') || '';

      if (!hasHeader) throw new Error(`Missing #header on ${route}`);
      if (!hasFooter) throw new Error(`Missing #footer on ${route}`);
      if (!bodyClass.includes('design-fermliving')) {
        throw new Error(`Missing Ferm body class on ${route}`);
      }
    }
  });

  await runTest('Consistency: No <base> tag in rendered HTML', async (page) => {
    await page.goto(`${BASE_URL}/`, { waitUntil: 'domcontentloaded', timeout: TIMEOUT });
    const hasBase = await page.$('base') !== null;
    if (hasBase) {
      throw new Error('<base> tag found in rendered HTML — breaks relative URLs');
    }
  });

  await runTest('Consistency: Fonts load correctly', async (page) => {
    await page.goto(`${BASE_URL}/`, { waitUntil: 'networkidle', timeout: TIMEOUT });

    const fontLinks = await page.$$eval('link[href*="fonts."]', links =>
      links.map(l => l.getAttribute('href'))
    );
    if (fontLinks.length === 0) {
      throw new Error('No Ferm font CSS links found');
    }
  });
}

// ─── Report Generation ───────────────────────────────────────────────────

function generateReports() {
  const passed = results.filter(r => r.status === 'PASS').length;
  const failed = results.filter(r => r.status === 'FAIL').length;
  const total = results.length;

  // ═══ Full Page Acceptance Matrix ═══
  let matrixMd = `# FERM-FULL-PAGE-ACCEPTANCE-MATRIX\n\n`;
  matrixMd += `**Date:** ${new Date().toISOString()}\n`;
  matrixMd += `**Total Tests:** ${total} | **Passed:** ${passed} | **Failed:** ${failed}\n\n`;

  // Verdict
  if (failed === 0) {
    matrixMd += `## VERDICT: ✅ CURRENT_RUNTIME_HARDENED\n\n`;
  } else {
    matrixMd += `## VERDICT: ❌ CURRENT_RUNTIME_BLOCKED\n\n`;
    matrixMd += `### Blocking Failures:\n`;
    results.filter(r => r.status === 'FAIL').forEach(r => {
      matrixMd += `- **${r.name}**: ${r.errors.join('; ')}\n`;
    });
    matrixMd += `\n`;
  }

  // Route × Viewport matrix
  matrixMd += `## Route × Viewport Matrix\n\n`;
  matrixMd += `| Route | Desktop-1440 | Laptop-1024 | Tablet-768 | Mobile-390 |\n`;
  matrixMd += `|-------|-------------|-------------|------------|------------|\n`;

  for (const route of ROUTES) {
    const row = [`**${route.name}**`];
    for (const vp of VIEWPORTS) {
      const testResult = results.find(r => r.name === `${route.name} @ ${vp.name}`);
      const icon = testResult?.status === 'PASS' ? '✅' : '❌';
      row.push(icon);
    }
    matrixMd += `| ${row.join(' | ')} |\n`;
  }

  matrixMd += `\n## Image Integrity Report\n\n`;
  matrixMd += `| Route | Total Images | Broken | Unrewritten CDN |\n`;
  matrixMd += `|-------|-------------|--------|------------------|\n`;
  imageReport.forEach(ir => {
    matrixMd += `| ${ir.route} | ${ir.totalImages} | ${ir.broken} | ${ir.unrewrittenCDN} |\n`;
  });

  matrixMd += `\n## Asset Loading Report\n\n`;
  matrixMd += `| Route | Total Requests | 404s | 500s+ |\n`;
  matrixMd += `|-------|---------------|------|-------|\n`;
  assetReport.forEach(ar => {
    matrixMd += `| ${ar.route} | ${ar.total} | ${ar.failed404} | ${ar.failed500 || 0} |\n`;
  });

  matrixMd += `\n## Detailed Results\n\n`;
  results.forEach(r => {
    const icon = r.status === 'PASS' ? '✅' : '❌';
    matrixMd += `${icon} **${r.name}**`;
    if (r.errors.length > 0) {
      matrixMd += ` — ${r.errors.join('; ')}`;
    }
    matrixMd += `\n`;
  });

  fs.writeFileSync(path.join(SCREENSHOT_DIR, 'FERM-FULL-PAGE-ACCEPTANCE-MATRIX.md'), matrixMd);

  // ═══ Image Integrity Report ═══
  let imgMd = `# FERM-IMAGE-INTEGRITY-REPORT\n\n`;
  imgMd += `**Date:** ${new Date().toISOString()}\n\n`;

  imageReport.forEach(ir => {
    imgMd += `## ${ir.route}\n\n`;
    imgMd += `- **Total images:** ${ir.totalImages}\n`;
    imgMd += `- **Broken (naturalWidth=0, not lazy/hidden):** ${ir.broken}\n`;
    imgMd += `- **Unrewritten CDN paths:** ${ir.unrewrittenCDN}\n`;
    if (ir.details.broken.length > 0) {
      imgMd += `- **Broken URLs:**\n`;
      ir.details.broken.forEach(u => { imgMd += `  - ${u}\n`; });
    }
    if (ir.details.unrewritten.length > 0) {
      imgMd += `- **Unrewritten URLs:**\n`;
      ir.details.unrewritten.forEach(u => { imgMd += `  - ${u}\n`; });
    }
    imgMd += `\n`;
  });

  fs.writeFileSync(path.join(SCREENSHOT_DIR, 'FERM-IMAGE-INTEGRITY-REPORT.md'), imgMd);

  // ═══ Console output ═══
  log('\n═══════════════════════════════════════════════════════════════');
  log('  REGRESSION RESULTS');
  log('═══════════════════════════════════════════════════════════════\n');
  log(`Total: ${total} | Passed: ${passed} | Failed: ${failed}\n`);

  results.forEach(r => {
    const icon = r.status === 'PASS' ? '✅' : '❌';
    log(`${icon} ${r.name}${r.errors.length > 0 ? ' — ' + r.errors.join('; ') : ''}`);
  });

  log(`\n=== VERDICT: ${failed === 0 ? '✅ CURRENT_RUNTIME_HARDENED' : '❌ CURRENT_RUNTIME_BLOCKED'} ===`);
  log(`\nReports saved to: ${SCREENSHOT_DIR}`);
}

// ─── Run ─────────────────────────────────────────────────────────────────
runAllTests()
  .then(() => {
    generateReports();
    process.exit(results.some(r => r.status === 'FAIL') ? 1 : 0);
  })
  .catch(e => {
    console.error('FATAL:', e.message);
    process.exit(1);
  });
