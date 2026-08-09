// Shared helpers for the AETHER frontend Playwright suite.
const { expect } = require('@playwright/test');

// Desktop and mobile each render their own header: the desktop `header.header`
// and the slide-out `#mobileHeader` chrome. `.first()` on a combined selector
// would always resolve the DOM-earlier mobile copy (display:none on desktop),
// so assert whichever header the active viewport actually shows.
async function expectHeaderVisible(page) {
  const visible = await page.evaluate(() => {
    const isVisible = (el) =>
      !!el &&
      el.getClientRects().length > 0 &&
      getComputedStyle(el).display !== 'none' &&
      getComputedStyle(el).visibility !== 'hidden';
    return (
      isVisible(document.querySelector('header.header')) ||
      isVisible(document.querySelector('#mobileHeader'))
    );
  });
  expect(visible).toBe(true);
}

module.exports = { expectHeaderVisible };
