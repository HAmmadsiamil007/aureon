// Route coverage — every AETHER page renders inside the premium shell
// without fatal JS errors, with header/footer + main landmark present.
const { test, expect } = require('@playwright/test');
const { expectHeaderVisible } = require('./helpers');

const ROUTES = [
  '/', '/shop/', '/cart/', '/my-account/', '/blog/',
  '/about/', '/contact/', '/faq/', '/team/', '/wishlist/',
  '/login/', '/register/', '/coming-soon/', '/no-such-page/',
];

// A console error that is expected: the intentional 404 route requests a
// missing resource (browser logs "Failed to load resource").
const EXPECTED_RESOURCE_ERROR = 'Failed to load resource';

test.describe('route coverage', () => {
  for (const route of ROUTES) {
    test(`GET ${route} renders premium shell without JS errors`, async ({ page }) => {
      const jsErrors = [];
      page.on('pageerror', (err) => jsErrors.push(err.message));

      const res = await page.goto(`${route}?nocache=e2e`, { waitUntil: 'domcontentloaded' });
      expect(res.status()).toBeLessThan(500);

      await page.waitForTimeout(1500);

      // AETHER shell chrome.
      await expect(page.locator('main#swup')).toBeVisible();
      await expectHeaderVisible(page);
      // The footer is reveal-animated (data-reveal="fade-up") and sits below
      // the fold on short pages (/my-account/, /login/…) — scroll it into view
      // so its ScrollTrigger fires before asserting visibility.
      await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight));
      await page.waitForTimeout(1200);
      await expect(page.locator('footer.footer')).toBeVisible({ timeout: 10000 });

      // No uncaught JS exceptions.
      expect(jsErrors).toEqual([]);
    });
  }

  test('GET /product/* resolves a real product page from the shop grid', async ({ page }) => {
    await page.goto('/shop/?nocache=e2e', { waitUntil: 'domcontentloaded' });
    // Product links are absolute (WP home_url) — match on the containing path
    // scoped to the card, so the nav/shop links are excluded.
    const firstProduct = page.locator('.product-card a[href*="/product/"]').first();
    // Cards are reveal-animated (data-reveal-item): force-scroll the card to
    // viewport center so its ScrollTrigger fires, then wait for the entrance
    // animation to settle before asserting visibility.
    await firstProduct.evaluate((el) => el.closest('.product-card').scrollIntoView({ block: 'center' }));
    await page.waitForTimeout(1500);
    await expect(firstProduct).toBeVisible({ timeout: 15000 });
    const href = await firstProduct.getAttribute('href');

    const jsErrors = [];
    page.on('pageerror', (err) => jsErrors.push(err.message));
    const res = await page.goto(href + '?nocache=e2e', { waitUntil: 'domcontentloaded' });
    expect(res.status()).toBe(200);
    await page.waitForTimeout(1500);

    await expect(page.locator('main#swup')).toBeVisible();
    await expect(page.locator('.pd-hero, .pd-gallery-swiper, .pd-info').first()).toBeVisible();
    expect(jsErrors).toEqual([]);
  });

  test('checkout redirects to cart when cart is empty', async ({ page }) => {
    const res = await page.goto('/checkout/?nocache=e2e', { waitUntil: 'domcontentloaded' });
    // WooCommerce standard: empty cart → redirect to /cart/.
    expect([200, 302, 301]).toContain(res.status());
    await page.waitForTimeout(1200);
    const url = page.url();
    expect(url.includes('/cart/')).toBe(true);
  });
});
