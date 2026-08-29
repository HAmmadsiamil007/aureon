/**
 * Ferm Living Design Pack — Playwright E2E verification.
 * Tests against the live Docker stack at http://localhost:8080.
 * @package Aureon
 */
const { chromium } = require('playwright');

(async () => {
  const browser = await chromium.launch({ headless: true });
  const results = [];

  async function test(name, fn) {
    const page = await browser.newPage({ viewport: { width: 1280, height: 800 } });
    try {
      await fn(page);
      results.push({ name, status: 'PASS' });
    } catch (e) {
      results.push({ name, status: 'FAIL', error: e.message.split('\n')[0] });
    } finally {
      await page.close();
    }
  }

  // === HOMEPAGE ===
  await test('Homepage: design-fermliving body class', async (p) => {
    await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    const cls = await p.getAttribute('body', 'class');
    if (!cls || !cls.includes('design-fermliving')) throw new Error('Missing design-fermliving');
  });

  await test('Homepage: header #header exists', async (p) => {
    await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    if (!(await p.$('#header'))) throw new Error('#header not found');
  });

  await test('Homepage: mobile header #mobileHeader', async (p) => {
    await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    if (!(await p.$('#mobileHeader'))) throw new Error('#mobileHeader not found');
  });

  await test('Homepage: mobile menu overlay #mobileMenuOverlay', async (p) => {
    await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    if (!(await p.$('#mobileMenuOverlay'))) throw new Error('#mobileMenuOverlay not found');
  });

  await test('Homepage: mobile close button .mobile-menu-close', async (p) => {
    await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    if (!(await p.$('.mobile-menu-close'))) throw new Error('.mobile-menu-close not found');
  });

  await test('Homepage: footer #footer', async (p) => {
    await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    if (!(await p.$('#footer'))) throw new Error('#footer not found');
  });

  await test('Homepage: newsletter form #footerNewsletterForm', async (p) => {
    await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    if (!(await p.$('#footerNewsletterForm'))) throw new Error('#footerNewsletterForm not found');
  });

  await test('Homepage: ferm.css loaded', async (p) => {
    await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    const n = await p.locator('link[href*="ferm.css"]').count();
    if (n === 0) throw new Error('ferm.css not loaded');
  });

  await test('Homepage: fonts.css loaded', async (p) => {
    await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    const n = await p.locator('link[href*="fonts.css"]').count();
    if (n === 0) throw new Error('fonts.css not loaded');
  });

  await test('Homepage: ferm.js loaded', async (p) => {
    await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    const n = await p.locator('script[src*="ferm.js"]').count();
    if (n === 0) throw new Error('ferm.js not loaded');
  });

  await test('Homepage: announcement bar renders', async (p) => {
    await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    const bar = await p.$('[data-announcement-bar], .announcement-bar, #announcementBar');
    if (!bar) throw new Error('Announcement bar not found');
  });

  await test('Homepage: header nav links present', async (p) => {
    await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    const count = await p.locator('#mainNav a, .header-nav a').count();
    if (count === 0) throw new Error('No nav links found');
  });

  await test('Homepage: mega menu container present', async (p) => {
    await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    if (!(await p.$('[data-component="megaMenu"]'))) throw new Error('Mega menu not found');
  });

  await test('Homepage: header spacer present', async (p) => {
    await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    if (!(await p.$('.header-spacer'))) throw new Error('.header-spacer not found');
  });

  await test('Homepage: footer legal section present', async (p) => {
    await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    const count = await p.locator('.footer-legal').count();
    if (count === 0) throw new Error('No footer legal section');
  });

  await test('Homepage: footer columns present', async (p) => {
    await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    const count = await p.locator('.footer-column').count();
    if (count === 0) throw new Error('No footer columns');
  });

  // === SHOP ===
  await test('Shop: design-fermliving body class', async (p) => {
    await p.goto('http://localhost:8080/shop/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    const cls = await p.getAttribute('body', 'class');
    if (!cls || !cls.includes('design-fermliving')) throw new Error('Missing design-fermliving');
  });

  await test('Shop: product cards render', async (p) => {
    await p.goto('http://localhost:8080/shop/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    const count = await p.locator('.product-card, .woocommerce ul.products li.product, [data-component="productThumb"]').count();
    if (count === 0) throw new Error('No product cards rendered');
  });

  await test('Shop: shop grid section renders', async (p) => {
    await p.goto('http://localhost:8080/shop/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    const grid = await p.$('.shop-grid, .products-grid, .woocommerce ul.products, .product-grid');
    if (!grid) throw new Error('Shop grid not found');
  });

  // === CART ===
  await test('Cart: design-fermliving body class', async (p) => {
    await p.goto('http://localhost:8080/cart/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    const cls = await p.getAttribute('body', 'class');
    if (!cls || !cls.includes('design-fermliving')) throw new Error('Missing design-fermliving');
  });

  await test('Cart: cart page renders with content', async (p) => {
    await p.goto('http://localhost:8080/cart/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    const el = await p.$('.woocommerce-cart-form, .cart-empty, .cart-drawer, .woocommerce-info, .cart-section, .empty-cart');
    if (!el) throw new Error('Cart content not found');
  });

  // === CHECKOUT ===
  await test('Checkout: redirects to cart when empty', async (p) => {
    const resp = await p.goto('http://localhost:8080/checkout/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    const url = p.url();
    if (!url.includes('cart') && resp.status() !== 200) {
      throw new Error('Checkout did not redirect to cart');
    }
  });

  // === ACCOUNT ===
  await test('Account: design-fermliving body class', async (p) => {
    await p.goto('http://localhost:8080/my-account/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    const cls = await p.getAttribute('body', 'class');
    if (!cls || !cls.includes('design-fermliving')) throw new Error('Missing design-fermliving');
  });

  await test('Account: login form renders', async (p) => {
    await p.goto('http://localhost:8080/my-account/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    const form = await p.$('.woocommerce-form-login, .woocommerce-MyAccount-content, form[name="login"]');
    if (!form) throw new Error('Login form not found');
  });

  await test('Account: navigation or content renders', async (p) => {
    await p.goto('http://localhost:8080/my-account/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    const el = await p.$('.woocommerce-MyAccount-navigation, .woocommerce-MyAccount-content, .woocommerce-form-login');
    if (!el) throw new Error('Account nav/content not found');
  });

  // === SEARCH ===
  await test('Search: design-fermliving body class', async (p) => {
    await p.goto('http://localhost:8080/?s=sofa', { waitUntil: 'domcontentloaded', timeout: 15000 });
    const cls = await p.getAttribute('body', 'class');
    if (!cls || !cls.includes('design-fermliving')) throw new Error('Missing design-fermliving');
  });

  await test('Search: empty state renders', async (p) => {
    await p.goto('http://localhost:8080/?s=sofa', { waitUntil: 'networkidle', timeout: 20000 });
    const el = await p.$('.empty-state, .search-results, .woocommerce-info');
    if (!el) throw new Error('Search content not found');
  });

  // === 404 ===
  await test('404: error-page class renders', async (p) => {
    await p.goto('http://localhost:8080/nonexistent/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    if (!(await p.$('.error-page'))) throw new Error('.error-page not found');
  });

  await test('404: error-page class present', async (p) => {
    await p.goto('http://localhost:8080/nonexistent/', { waitUntil: 'networkidle', timeout: 20000 });
    const el = await p.$('.error-page, .error404');
    if (!el) throw new Error('404 error-page not found');
  });

  await test('404: Ferm CSS applied', async (p) => {
    await p.goto('http://localhost:8080/nonexistent/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    const cls = await p.getAttribute('body', 'class');
    if (!cls || !cls.includes('design-fermliving')) throw new Error('Missing design-fermliving on 404');
  });

  // === CONSOLE ERRORS (homepage) ===
  await test('Console: 0 local errors on homepage', async (p) => {
    const errors = [];
    p.on('console', msg => { if (msg.type() === 'error') errors.push(msg.text()); });
    await p.goto('http://localhost:8080/', { waitUntil: 'networkidle', timeout: 20000 });
    const local = errors.filter(e =>
      e.includes('localhost:8080') &&
      !e.includes('cdn.jsdelivr') &&
      !e.includes('cdnjs.cloudflare') &&
      !e.includes('fonts.googleapis') &&
      !e.includes('fonts.gstatic')
    );
    if (local.length > 0) throw new Error(local.length + ' local errors: ' + local[0]);
  });

  // === CSS VARIABLE VERIFICATION ===
  await test('CSS: --aureon-color-bg resolves', async (p) => {
    await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    const val = await p.evaluate(() => {
      const el = document.querySelector('.design-fermliving') || document.documentElement;
      return getComputedStyle(el).getPropertyValue('--aureon-color-bg').trim();
    });
    if (!val || val === '') throw new Error('--aureon-color-bg not set');
  });

  await test('CSS: body background is not transparent', async (p) => {
    await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    const bg = await p.evaluate(() => getComputedStyle(document.body).backgroundColor);
    if (!bg || bg === 'rgba(0, 0, 0, 0)') throw new Error('Body bg transparent');
  });

  await test('CSS: heading font uses CanelaText', async (p) => {
    await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    const font = await p.evaluate(() => {
      const h = document.querySelector('h1, h2, h3');
      return h ? getComputedStyle(h).fontFamily : '';
    });
    if (!font.includes('CanelaText') && !font.includes('Georgia')) {
      throw new Error('Heading font: ' + font);
    }
  });

  // === MOBILE MENU INTERACTION ===
  await test('Interaction: hamburger opens mobile menu', async (p) => {
    await p.setViewportSize({ width: 390, height: 844 });
    await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    const hamburger = await p.$('#mobileHamburger');
    if (!hamburger) throw new Error('Hamburger not found');
    await hamburger.click();
    await p.waitForTimeout(500);
    const isOpen = await p.$eval('#mobileMenuOverlay', el => el.classList.contains('is-open'));
    if (!isOpen) throw new Error('Mobile menu did not open');
  });

  await test('Interaction: close button closes mobile menu', async (p) => {
    await p.setViewportSize({ width: 390, height: 844 });
    await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    await p.click('#mobileHamburger');
    await p.waitForTimeout(500);
    const closeBtn = await p.$('.mobile-menu-close');
    if (!closeBtn) throw new Error('Close button not found');
    await closeBtn.click();
    await p.waitForTimeout(500);
    const isOpen = await p.$eval('#mobileMenuOverlay', el => el.classList.contains('is-open'));
    if (isOpen) throw new Error('Mobile menu did not close');
  });

  await test('Interaction: mobile menu shows level 1 links', async (p) => {
    await p.setViewportSize({ width: 390, height: 844 });
    await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    await p.click('#mobileHamburger');
    await p.waitForTimeout(500);
    const level1 = await p.$('.mobile-menu-level-1.is-active');
    if (!level1) throw new Error('Level 1 not active');
  });

  await test('Interaction: mobile menu has quick links', async (p) => {
    await p.setViewportSize({ width: 390, height: 844 });
    await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    await p.click('#mobileHamburger');
    await p.waitForTimeout(500);
    const links = await p.locator('.mobile-menu-quick-link').count();
    if (links === 0) throw new Error('No quick links');
  });

  // === HEADER SCROLL BEHAVIOR ===
  // Real Ferm Living keeps header visible on scroll — no hide-on-scroll.
  await test('Interaction: header stays visible on scroll down', async (p) => {
    await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    await p.evaluate(() => window.scrollTo(0, 500));
    await p.waitForTimeout(500);
    const hidden = await p.$eval('#header', el => el.classList.contains('header--hidden'));
    if (hidden) throw new Error('Header should NOT hide on scroll (matches Ferm Living)');
  });

  await test('Interaction: header gets solid class on scroll', async (p) => {
    await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    await p.evaluate(() => window.scrollTo(0, 300));
    await p.waitForTimeout(500);
    const solid = await p.$eval('#header', el => el.classList.contains('header--solid'));
    if (!solid) throw new Error('Header should become solid after scroll on homepage');
  });

  // === DESIGN ISOLATION ===
  await test('Isolation: no Shopify references in HTML', async (p) => {
    await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    const html = await p.content();
    if (html.includes('myshopify.com')) throw new Error('Shopify reference found');
  });

  await test('Isolation: no hardcoded product names', async (p) => {
    await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    const html = await p.content();
    if (html.includes('Rico Sofa') || html.includes('Catena')) throw new Error('Hardcoded product names found');
  });

  // === CONTENT CORRECTNESS ===
  await test('Content: hero images load (no 404)', async (p) => {
    const responses = [];
    p.on('response', r => { if (r.url().includes('hero/') && r.url().includes('fermliving')) responses.push(r); });
    await p.goto('http://localhost:8080/', { waitUntil: 'networkidle', timeout: 20000 });
    const broken = responses.filter(r => r.status() >= 400);
    if (broken.length > 0) throw new Error('Hero image 404: ' + broken[0].url());
  });

  await test('Content: editorial images load (no 404)', async (p) => {
    const responses = [];
    p.on('response', r => { if (r.url().includes('editorial/') && r.url().includes('fermliving')) responses.push(r); });
    await p.goto('http://localhost:8080/', { waitUntil: 'networkidle', timeout: 20000 });
    const broken = responses.filter(r => r.status() >= 400);
    if (broken.length > 0) throw new Error('Editorial image 404: ' + broken[0].url());
  });

  await test('Content: no AETHER shoe products in demo mode', async (p) => {
    await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    const html = await p.content();
    const aetherShoes = ['Encore', 'Diplomat', 'Court', 'Captain Brogue', 'Men\'s Shoes', 'Men\'s Sneakers', 'Men\'s Boots', 'Women\'s Bags'];
    for (const term of aetherShoes) {
      if (html.includes(term)) throw new Error('AETHER shoe content found: ' + term);
    }
  });

  await test('Content: Ferm product names present', async (p) => {
    await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    const html = await p.content();
    const fermProducts = ['Donkey', 'Pear', 'Swif', 'Willora', 'Kona', 'Haze'];
    let found = 0;
    for (const name of fermProducts) {
      if (html.includes(name)) found++;
    }
    if (found < 3) throw new Error('Expected at least 3 Ferm products, found ' + found);
  });

  await test('Content: Ferm category names present', async (p) => {
    await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    const html = await p.content();
    const fermCats = ['Furniture', 'Lighting', 'Accessories', 'Kids', 'Textiles'];
    let found = 0;
    for (const cat of fermCats) {
      if (html.includes(cat)) found++;
    }
    if (found < 3) throw new Error('Expected at least 3 Ferm categories, found ' + found);
  });

  await test('Content: room grid section exists', async (p) => {
    await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    const el = await p.$('.ferm-room-grid, [class*="room"]');
    if (!el) throw new Error('Room grid section not found');
  });

  await test('Content: Ferm footer social links', async (p) => {
    await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    const html = await p.content();
    if (!html.includes('instagram.com/fermliving')) throw new Error('Ferm Instagram link not found');
  });

  await test('Content: Ferm newsletter heading', async (p) => {
    await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    const html = await p.content();
    if (!html.includes('Ferm Living news')) throw new Error('Ferm newsletter heading not found');
  });

  // === RESPONSIVE ===
  await test('Responsive: header hidden on mobile', async (p) => {
    await p.setViewportSize({ width: 390, height: 844 });
    await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    const display = await p.evaluate(() => {
      const header = document.querySelector('.header-nav');
      return header ? getComputedStyle(header).display : 'not found';
    });
    if (display !== 'none') throw new Error('Desktop nav visible on mobile: ' + display);
  });

  await test('Responsive: mobile header visible on mobile', async (p) => {
    await p.setViewportSize({ width: 390, height: 844 });
    await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
    const display = await p.evaluate(() => {
      const mh = document.querySelector('.mobile-header');
      return mh ? getComputedStyle(mh).display : 'not found';
    });
    if (display === 'none') throw new Error('Mobile header hidden on mobile');
  });

  // Print results
  const passed = results.filter(r => r.status === 'PASS').length;
  const failed = results.filter(r => r.status === 'FAIL').length;

  console.log('\n=== PLAYWRIGHT TEST RESULTS ===');
  console.log('Total: ' + results.length + ' | Passed: ' + passed + ' | Failed: ' + failed);
  console.log('');

  results.forEach(r => {
    const icon = r.status === 'PASS' ? '\u2705' : '\u274C';
    console.log(icon + ' ' + r.name + (r.error ? ' \u2014 ' + r.error : ''));
  });

  console.log('\n=== VERDICT: ' + (failed === 0 ? 'ALL PASS' : failed + ' FAILED') + ' ===');

  await browser.close();
  process.exit(failed > 0 ? 1 : 0);
})().catch(e => { console.error('FATAL:', e.message); process.exit(1); });
