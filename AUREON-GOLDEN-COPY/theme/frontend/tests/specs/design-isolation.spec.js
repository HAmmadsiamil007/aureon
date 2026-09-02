// Design pack isolation gate (M7).
//
// Asserts the active design's asset pipeline is isolated:
//   - exactly one design system is enqueued (Luxury css XOR pack css)
//   - no duplicate handles/URLs
//   - the aetherAjax contract is present on every route
//   - wp-login (core UI) carries NO aether design assets
//
// Run: DESIGN_SLUG=lumen npx playwright test --project=desktop design-isolation.spec.js
// Default (no env): asserts the Luxury baseline.
const { test, expect } = require('@playwright/test');

const DESIGN = process.env.DESIGN_SLUG || 'luxury';

test.describe('design isolation', () => {
  test('active design assets are the only design system on the frontend', async ({ page }) => {
    await page.goto('/?nocache=iso', { waitUntil: 'networkidle' });

    const styles = await page.evaluate(() =>
      Array.from(document.querySelectorAll('link[rel="stylesheet"]'))
        .map((l) => l.href)
        .filter((h) => /frontend\/assets\/css|frontend\/designs\//.test(h))
    );
    const scripts = await page.evaluate(() =>
      Array.from(document.querySelectorAll('script[src]'))
        .map((s) => s.src)
        .filter((s) => /frontend\/assets\/js|frontend\/designs\//.test(s))
    );

    const luxuryCss = styles.filter((h) => h.includes('/frontend/assets/css/'));
    const packCss = styles.filter((h) => h.includes('/frontend/designs/'));

    if (DESIGN === 'luxury') {
      expect(luxuryCss.length).toBeGreaterThan(0);
      expect(packCss.length).toBe(0);
    } else {
      expect(packCss.length).toBeGreaterThan(0);
      expect(luxuryCss.length).toBe(0);
    }

    // No duplicate asset URLs (isolation: one design system only).
    const all = [...styles, ...scripts];
    expect(new Set(all).size).toBe(all.length);

    // aetherAjax contract is present regardless of design.
    const hasContract = await page.evaluate(() => typeof window.aetherAjax !== 'undefined');
    expect(hasContract).toBe(true);
  });

  test('wp-login (core UI) carries no aether design assets', async ({ page }) => {
    await page.goto('/wp-login.php', { waitUntil: 'domcontentloaded' });
    const leaks = await page.evaluate(() =>
      Array.from(document.querySelectorAll('link[rel="stylesheet"], script[src]'))
        .map((el) => el.href || el.src)
        .filter((h) => /frontend\/assets|frontend\/designs/.test(h))
    );
    expect(leaks).toEqual([]);
  });

  test(`body carries the ${DESIGN} design class`, async ({ page }) => {
    await page.goto('/?nocache=iso', { waitUntil: 'networkidle' });
    const bodyClass = await page.evaluate(() => document.body.className);
    expect(bodyClass).toContain(`design-${DESIGN}`);
  });
});
