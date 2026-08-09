// Interaction coverage — the dynamic interactions the frontend owns.
const { test, expect } = require('@playwright/test');

test.describe('interactions', () => {
  test('preloader is removed after page load', async ({ page }) => {
    await page.goto('/?nocache=e2e', { waitUntil: 'domcontentloaded' });
    await page.waitForTimeout(2500);
    await expect(page.locator('#preloader')).toHaveCount(0);
  });

  test('search overlay opens and routes Enter to the WP search', async ({ page }) => {
    test.setTimeout(120000);
    await page.goto('/?nocache=e2e', { waitUntil: 'domcontentloaded' });
    // Desktop: header search icons (only the active viewport copy is visible).
    // Mobile: the search trigger lives inside the slide-out drawer and opens
    // the same overlay (wired in main.js).
    const isMobileViewport = await page.evaluate(() => window.innerWidth <= 768);
    if (isMobileViewport) {
      await page.locator('#mobileHamburger').click();
      await page.locator('.mobile-search').click();
    } else {
      await page.locator('[aria-label="Search"]:visible').first().click();
    }
    const input = page.locator('.search-input');
    await expect(input).toBeVisible();
    await input.fill('sneaker');
    await input.press('Enter');
    await page.waitForTimeout(1500);
    expect(page.url()).toContain('?s=sneaker');
    await expect(page.locator('main#swup')).toBeVisible();
  });

  test('product card click navigates to the real product permalink', async ({ page }) => {
    await page.goto('/shop/?nocache=e2e', { waitUntil: 'domcontentloaded' });
    const card = page.locator('.product-card').first();
    // Cards are reveal-animated (data-reveal-item): force-scroll the element to
    // viewport center so the ScrollTrigger (top 82%) reliably fires, then wait
    // for the entrance animation to settle. (scrollIntoViewIfNeeded can skip
    // the scroll when the element is only partially in view.)
    await card.evaluate((el) => el.scrollIntoView({ block: 'center' }));
    await page.waitForTimeout(1500);
    await expect(card).toBeVisible({ timeout: 15000 });
    await card.click();
    await page.waitForTimeout(1500);
    expect(page.url()).toMatch(/\/product\//);
  });

  test('mobile drawer opens and closes', async ({ page }) => {
    // The `isMobile` Playwright fixture is only defined for device-profile
    // projects; this config uses viewport projects, so detect by width.
    const isMobileViewport = await page.evaluate(() => window.innerWidth <= 768);
    test.skip(!isMobileViewport, 'mobile drawer is a mobile-only interaction');
    await page.goto('/?nocache=e2e', { waitUntil: 'domcontentloaded' });
    await page.locator('#mobileHamburger').click();
    await expect(page.locator('#mobileMenuOverlay')).toHaveClass(/active/);
    await page.locator('#mobileMenuClose').click();
    await expect(page.locator('#mobileMenuOverlay')).not.toHaveClass(/active/);
  });

  test('FAQ accordion toggles the active item', async ({ page }) => {
    await page.goto('/?nocache=e2e', { waitUntil: 'domcontentloaded' });
    // The first FAQ item renders open by default (section-faq passes 'open'
    // for the first item), so clicking it would *close* it. Target the first
    // closed item and assert it becomes the active one.
    // NOTE: a live locator `.faq-item:not(.active)` re-resolves after the
    // click changes the DOM (the previously-open first item becomes closed),
    // which would point the assertion at the WRONG element. Pin the element
    // handle once, then assert class + aria-expanded on that pinned element.
    const handle = await page.locator('.faq-item:not(.active)').first().elementHandle();
    if (!handle) {
      test.skip(true, 'no closable FAQ items rendered on this store');
      return;
    }
    await handle.evaluate((el) => {
      el.querySelector('.faq-question').scrollIntoView({ block: 'center' });
    });
    await page.waitForTimeout(1200);
    await handle.evaluate((el) => el.querySelector('.faq-question').click());
    await page.waitForFunction(
      (el) => el.classList.contains('active'),
      handle
    );
    await handle.evaluate((el) => {
      const btn = el.querySelector('.faq-question');
      if (btn.getAttribute('aria-expanded') !== 'true') {
        throw new Error('aria-expanded not set to true after click');
      }
    });
  });

  test('announcement marquee renders items', async ({ page }) => {
    await page.goto('/?nocache=e2e', { waitUntil: 'domcontentloaded' });
    // Desktop renders the full-width marquee (announcement-item); mobile
    // hides it by design and renders the mobile-announcement slider instead.
    const isMobileViewport = await page.evaluate(() => window.innerWidth <= 768);
    if (isMobileViewport) {
      await expect(page.locator('.mobile-announcement').first()).toBeVisible();
      await expect(page.locator('.mobile-announcement-text.active').first()).toBeVisible();
    } else {
      await expect(page.locator('.announcement-item').first()).toBeVisible();
    }
  });
});
