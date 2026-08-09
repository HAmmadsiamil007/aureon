// Visual regression — committed baselines for key pages.
// Generate/refresh baselines: npx playwright test --grep @visual --update-snapshots
// Any visual difference must be classified EXPECTED or REGRESSION before accepting.
const { test, expect } = require('@playwright/test');

test.describe('visual regression', { tag: '@visual' }, () => {
  for (const route of ['/', '/shop/', '/about/']) {
    test(`snapshot ${route} (desktop)`, async ({ page, isMobile }) => {
      test.skip(isMobile, 'visual baselines are captured on the desktop project only');

      await page.goto(`${route}?nocache=visual`, { waitUntil: 'networkidle' });
      await page.waitForTimeout(2500); // let reveal animations settle into final state

      await expect(page).toHaveScreenshot(`${route.replace(/\//g, '_')}.png`, {
        maxDiffPixelRatio: 0.01,
        animations: 'disabled',
      });
    });
  }
});
