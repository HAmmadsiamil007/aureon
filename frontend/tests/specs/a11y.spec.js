// Accessibility regression — axe scans + core AETHER a11y contracts.
const { test, expect } = require('@playwright/test');
const AxeBuilder = require('@axe-core/playwright').default;
const { expectHeaderVisible } = require('./helpers');

const A11Y_PAGES = ['/', '/contact/', '/my-account/'];

test.describe('accessibility', { tag: '@a11y' }, () => {
  // axe scans on the full DOM can be slow on heavy pages; give them room.
  test.setTimeout(120000);

  for (const route of A11Y_PAGES) {
    test(`axe scan: ${route} has no critical/serious violations`, async ({ page }) => {
      await page.goto(`${route}?nocache=a11y`, { waitUntil: 'domcontentloaded' });
      await page.waitForTimeout(1500);

      const results = await new AxeBuilder({ page })
        .withTags(['wcag2a', 'wcag2aa'])
        .analyze();

      const serious = results.violations.filter((v) =>
        ['critical', 'serious'].includes(v.impact)
      );
      expect(serious).toEqual([]);
    });
  }

  test('skip link is present and is the first focusable element', async ({ page }) => {
    await page.goto('/?nocache=a11y', { waitUntil: 'domcontentloaded' });
    const skip = page.locator('a.skip-link, [href="#main"]').first();
    await expect(skip).toBeVisible();
    const firstTab = await page.evaluate(() => {
      const links = [...document.querySelectorAll('a[href], button')];
      return links[0] ? links[0].getAttribute('href') || links[0].tagName : '';
    });
    expect(firstTab).toBe('#main');
  });

  test('main landmark and header/footer landmarks exist', async ({ page }) => {
    await page.goto('/?nocache=a11y', { waitUntil: 'domcontentloaded' });
    await expect(page.locator('main#swup')).toBeVisible();
    // Desktop/mobile each show their own header — assert whichever is visible.
    await expectHeaderVisible(page);
    // Footer is reveal-animated below the fold — scroll it into view first.
    await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight));
    await page.waitForTimeout(1200);
    await expect(page.locator('footer.footer')).toBeVisible({ timeout: 10000 });
  });

  test('images have alt attributes', async ({ page }) => {
    await page.goto('/?nocache=a11y', { waitUntil: 'domcontentloaded' });
    const missingAlt = await page.evaluate(() => {
      const imgs = [...document.querySelectorAll('main img')];
      return imgs.filter((img) => !img.hasAttribute('alt')).length;
    });
    expect(missingAlt).toBe(0);
  });
});
