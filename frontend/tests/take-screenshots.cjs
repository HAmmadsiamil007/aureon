/**
 * Take fresh screenshots of all Ferm Living routes.
 *
 * Desktop: 1440×900
 * Mobile:  390×844 (iPhone 14 Pro)
 */
const { chromium } = require('playwright');
const path = require('path');
const fs = require('fs');

const BASE_URL = 'http://localhost:8080';

const DESKTOP_ROUTES = [
  { name: '01-homepage',      url: '/',                    label: 'Homepage' },
  { name: '02-shop',          url: '/shop/',               label: 'All Products' },
  { name: '03-about',         url: '/about/',              label: 'About' },
  { name: '04-blog',          url: '/blog/',               label: 'Blog' },
  { name: '05-contact',       url: '/contact/',            label: 'Contact' },
  { name: '06-cart',          url: '/cart/',               label: 'Cart' },
  { name: '07-my-account',    url: '/my-account/',         label: 'My Account' },
  { name: '08-shop-product',  url: '/product/donkey-soft-toy/', label: 'Product' },
];

const MOBILE_ROUTES = [...DESKTOP_ROUTES];

const OUTPUT_DIR = path.join(__dirname, 'screenshots-new');

async function main() {
  // Clean/create output dir
  if (fs.existsSync(OUTPUT_DIR)) {
    fs.rmSync(OUTPUT_DIR, { recursive: true });
  }
  fs.mkdirSync(path.join(OUTPUT_DIR, 'desktop'), { recursive: true });
  fs.mkdirSync(path.join(OUTPUT_DIR, 'mobile'), { recursive: true });

  const browser = await chromium.launch({ headless: true });

  // ── Desktop screenshots ──────────────────────────────────
  console.log('Taking desktop screenshots (1440×900)...');
  const desktopCtx = await browser.newContext({
    viewport: { width: 1440, height: 900 },
    deviceScaleFactor: 1,
  });
  const desktopPage = await desktopCtx.newPage();

  for (const route of DESKTOP_ROUTES) {
    const url = BASE_URL + route.url;
    const file = path.join(OUTPUT_DIR, 'desktop', `${route.name}-desktop.png`);
    try {
      await desktopPage.goto(url, { waitUntil: 'networkidle', timeout: 30000 });
      // Wait for fonts and images
      await desktopPage.waitForTimeout(2000);
      await desktopPage.screenshot({ path: file, fullPage: true });
      console.log(`  ✓ ${route.label} → ${route.name}-desktop.png`);
    } catch (err) {
      console.error(`  ✗ ${route.label}: ${err.message}`);
    }
  }
  await desktopCtx.close();

  // ── Mobile screenshots ───────────────────────────────────
  console.log('\nTaking mobile screenshots (390×844)...');
  const mobileCtx = await browser.newContext({
    viewport: { width: 390, height: 844 },
    deviceScaleFactor: 3,
    isMobile: true,
    hasTouch: true,
    userAgent: 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.0 Mobile/15E148 Safari/604.1',
  });
  const mobilePage = await mobileCtx.newPage();

  for (const route of MOBILE_ROUTES) {
    const url = BASE_URL + route.url;
    const file = path.join(OUTPUT_DIR, 'mobile', `${route.name}-mobile.png`);
    try {
      await mobilePage.goto(url, { waitUntil: 'networkidle', timeout: 30000 });
      await mobilePage.waitForTimeout(2000);
      await mobilePage.screenshot({ path: file, fullPage: true });
      console.log(`  ✓ ${route.label} → ${route.name}-mobile.png`);
    } catch (err) {
      console.error(`  ✗ ${route.label}: ${err.message}`);
    }
  }
  await mobileCtx.close();

  await browser.close();
  console.log(`\nDone! Screenshots saved to: ${OUTPUT_DIR}`);
}

main().catch(err => {
  console.error('Fatal error:', err);
  process.exit(1);
});
