// Failure injection (Rule 7) — the animation stack failing must NEVER hide
// content. These are the acceptance tests for the Phase A hardening.
const { test, expect } = require('@playwright/test');

async function assertContentVisible(page) {
  await page.waitForTimeout(3500); // allow watchdog (2500ms) + margin to act

  // Motion must be in the disabled/absent state, never hiding content.
  const cls = await page.evaluate(() => document.documentElement.className);
  expect(cls).not.toContain('has-motion');

  // Every reveal-targeted element must be computed visible.
  const hidden = await page.evaluate(() => {
    const els = document.querySelectorAll(
      '[data-reveal], [data-reveal-item], [data-motion-text], [data-image-reveal]'
    );
    let hiddenCount = 0;
    els.forEach((el) => {
      const s = window.getComputedStyle(el);
      if (s.visibility === 'hidden' || s.display === 'none' || Number(s.opacity) === 0) {
        hiddenCount += 1;
      }
    });
    return { total: els.length, hiddenCount };
  });
  expect(hidden.hiddenCount).toBe(0);

  // Preloader must not be stuck.
  const preloader = await page.evaluate(() => {
    const el = document.getElementById('preloader');
    if (!el) return 'removed';
    return window.getComputedStyle(el).display;
  });
  expect(preloader).toBe('removed');

  // Main landmark still usable.
  await expect(page.locator('main#swup')).toBeVisible();
}

async function assertPreloaderAndNoJsErrors(page) {
  await page.waitForTimeout(3000);

  // Preloader must resolve even when a non-motion library (Swiper) is blocked.
  const preloader = await page.evaluate(() => {
    const el = document.getElementById('preloader');
    if (!el) return 'removed';
    return window.getComputedStyle(el).display;
  });
  expect(preloader).toBe('removed');

  // Content in the initial viewport must be visible immediately.
  const hiddenInViewport = await page.evaluate(() => {
    const vh = window.innerHeight;
    const els = document.querySelectorAll('[data-reveal], [data-reveal-item]');
    let hiddenCount = 0;
    els.forEach((el) => {
      const r = el.getBoundingClientRect();
      if (r.top < vh && r.bottom > 0) {
        const s = window.getComputedStyle(el);
        if (s.visibility === 'hidden' || s.display === 'none' || Number(s.opacity) === 0) {
          hiddenCount += 1;
        }
      }
    });
    return hiddenCount;
  });
  expect(hiddenInViewport).toBe(0);

  // Scrolling to the bottom must reveal the remaining items (GSAP works here).
  await page.evaluate(() => window.scrollTo(0, document.body.scrollHeight));
  await page.waitForTimeout(1800);
  const hiddenAfterScroll = await page.evaluate(() => {
    let hiddenCount = 0;
    document.querySelectorAll('[data-reveal], [data-reveal-item]').forEach((el) => {
      const s = window.getComputedStyle(el);
      if (s.visibility === 'hidden' || s.display === 'none' || Number(s.opacity) === 0) {
        hiddenCount += 1;
      }
    });
    return hiddenCount;
  });
  expect(hiddenAfterScroll).toBe(0);

  await expect(page.locator('main#swup')).toBeVisible();
}

test('GSAP/ScrollTrigger CDN blocked → content stays fully visible', { tag: '@failure' }, async ({ page }) => {
  await page.route('**cdnjs.cloudflare.com/**', (route) => route.abort());
  await page.route('**unpkg.com/**', (route) => route.abort());

  await page.goto('/?nocache=fail', { waitUntil: 'domcontentloaded' });
  await assertContentVisible(page);
});

test('Swiper CDN blocked → no JS error, preloader resolves, content visible', { tag: '@failure' }, async ({ page }) => {
  // Swiper lives on cdn.jsdelivr.net. Blocking it must NOT throw (guards) or
  // stick the preloader — the pre-hardening code did both.
  const jsErrors = [];
  page.on('pageerror', (err) => jsErrors.push(err.message));

  await page.route('**cdn.jsdelivr.net/**', (route) => route.abort());

  await page.goto('/?nocache=failswiper', { waitUntil: 'domcontentloaded' });
  await assertPreloaderAndNoJsErrors(page);
  expect(jsErrors).toEqual([]);
});

test('Runtime exception during motion init → content left visible', { tag: '@failure' }, async ({ page }) => {
  // Patch gsap.utils to throw BEFORE animations.js's DOMContentLoaded init runs
  // (addInitScript listeners fire first), triggering the try/catch → disableMotion.
  await page.addInitScript(() => {
    document.addEventListener('DOMContentLoaded', () => {
      if (window.gsap && window.gsap.utils) {
        window.gsap.utils = new Proxy(window.gsap.utils, {
          get() {
            throw new Error('injected motion init failure');
          },
        });
      }
    });
  });

  await page.goto('/?nocache=failinit', { waitUntil: 'domcontentloaded' });
  await assertContentVisible(page);
});

test('Reduced motion preference → content visible, no motion state', { tag: '@failure' }, async ({ page }) => {
  await page.emulateMedia({ reducedMotion: 'reduce' });
  await page.goto('/?nocache=rm', { waitUntil: 'domcontentloaded' });
  await assertContentVisible(page);
});
