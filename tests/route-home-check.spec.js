const { test, expect } = require('@playwright/test');

test('homepage renders Vineta store home, not blog', async ({ page }) => {
  await page.goto('http://localhost:8080/', { waitUntil: 'networkidle' });

  const finalUrl = page.url();
  const title = await page.title();
  console.log('FINAL URL :', finalUrl);
  console.log('TITLE     :', title);

  // Detect any late JS redirect (wait a moment and re-check)
  await page.waitForTimeout(3000);
  console.log('URL AFTER 3s:', page.url());
  console.log('TITLE AFTER 3s:', await page.title());

  // Hero should exist on the store home
  const heroCount = await page.locator('[class*="hero"], [id*="hero"], .swiper').count();
  console.log('HERO/SWIPER ELEMENTS:', heroCount);

  await page.screenshot({ path: 'test-results/home-check.png', fullPage: false });

  expect(page.url()).toBe('http://localhost:8080/');
  expect(title).not.toContain('Blog');
});
