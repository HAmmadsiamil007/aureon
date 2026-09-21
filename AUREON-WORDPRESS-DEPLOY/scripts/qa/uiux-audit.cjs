const { chromium } = require('playwright');
const fs = require('fs');

/**
 * AUREON deep UI/UX audit.
 * For every route x (desktop 1440, mobile 390):
 *  - full-page screenshot
 *  - axe-core accessibility violations
 *  - contrast of visible text (sampled)
 *  - tap-target sizes (mobile)
 *  - images without dimensions (CLS risk), oversized images, alt coverage
 *  - font families/weights in use, custom-property palette
 *  - console errors, failed requests
 */
const ROUTES = [
  ['home', '/'], ['shop', '/shop/'], ['category', '/product-category/women/'],
  ['search', '/?s=kurta'], ['blog', '/blog/'], ['blog-single', '/hello-world/'],
  ['about', '/about-us/'], ['contact', '/contact-us/'], ['store-location', '/store-location/'],
  ['faq', '/faq/'], ['privacy', '/privacy-policy/'], ['cart', '/cart/'],
  ['checkout', '/checkout/'], ['account', '/my-account/'], ['404', '/nonexistent-xyz-404/'],
];
const VIEWPORTS = [['desktop', 1440, 900], ['mobile', 390, 844]];
const BASE = process.env.AUREON_BASE || 'http://localhost:8080';
const OUT = 'qa-new/audit';

(async () => {
  fs.mkdirSync(OUT, { recursive: true });
  // axe-core from CDN, injected per page.
  const axeSrc = await (await fetch('https://cdnjs.cloudflare.com/ajax/libs/axe-core/4.10.2/axe.min.js')).text();

  const browser = await chromium.launch();
  const results = [];

  for (const [vpName, w, h] of VIEWPORTS) {
    const ctx = await browser.newContext({ viewport: { width: w, height: h } });
    const page = await ctx.newPage();
    for (const [name, route] of ROUTES) {
      const consoleErrors = [];
      const failedReq = [];
      const onConsole = m => { if (m.type() === 'error') consoleErrors.push(m.text().slice(0, 120)); };
      const onResp = r => { if (r.status() >= 400) failedReq.push(`${r.status()} ${r.url().split('/').pop().slice(0, 60)}`); };
      page.on('console', onConsole);
      page.on('response', onResp);
      const rec = { route: name, viewport: vpName };

      try {
        await page.goto(BASE + route, { waitUntil: 'networkidle', timeout: 45000 });
        await page.waitForTimeout(900);
        await page.screenshot({ path: `${OUT}/${name}--${vpName}.png`, fullPage: true });

        // Inject axe and run.
        await page.addScriptTag({ content: axeSrc });
        const axe = await page.evaluate(() => {
          const rules = ['critical', 'serious'];
          return window.axe.run(document, {
            resultTypes: ['violations'],
            runOnly: { type: 'tag', values: ['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa'] },
          }).then(r => ({
            violations: r.violations
              .filter(v => rules.includes(v.impact))
              .map(v => ({ id: v.id, impact: v.impact, nodes: v.nodes.length, help: v.help.slice(0, 80) })),
          }));
        });
        rec.axe = axe.violations;

        // Page metrics.
        rec.metrics = await page.evaluate(() => {
          const imgs = [...document.querySelectorAll('img')];
          const noDims = imgs.filter(i => !i.getAttribute('width') || !i.getAttribute('height')).length;
          const noAlt = imgs.filter(i => i.alt === undefined || (i.alt === '' && !i.getAttribute('aria-hidden') && !i.closest('[aria-hidden]'))).length;
          const fonts = new Set();
          document.querySelectorAll('h1,h2,h3,h4,h5,h6,p,a,button,span,div').forEach(el => {
            if (el.offsetParent !== null && el.textContent.trim()) {
              const cs = getComputedStyle(el);
              fonts.add(cs.fontFamily.split(',')[0].replace(/["']/g, '') + '|' + cs.fontWeight);
            }
          });
          const buttons = [...document.querySelectorAll('button, a.btn, [role=button], .btn, a[class*=btn]')];
          const smallTargets = buttons.filter(b => {
            const r = b.getBoundingClientRect();
            return r.width > 0 && r.height > 0 && (r.width < 32 || r.height < 24);
          }).length;
          const textEls = [...document.querySelectorAll('p,span,a,li,h1,h2,h3,h4,h5,h6')].filter(el => {
            if (el.offsetParent === null || !el.textContent.trim() || el.children.length) return false;
            const r = el.getBoundingClientRect();
            return r.height > 0 && r.width > 0 && el.textContent.trim().length > 6;
          });
          // Contrast sampling: first 60 visible leaf text nodes.
          let lowContrast = 0;
          const lum = c => {
            const [r, g, b] = c.match(/\d+/g).map(Number);
            const f = v => { v /= 255; return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4); };
            return 0.2126 * f(r) + 0.7152 * f(g) + 0.0722 * f(b);
          };
          for (const el of textEls.slice(0, 60)) {
            try {
              const cs = getComputedStyle(el);
              if (cs.visibility === 'hidden' || cs.opacity === '0') continue;
              const l1 = lum(cs.color);
              let bg = cs.backgroundColor, node = el;
              while (bg && (bg === 'rgba(0, 0, 0, 0)' || bg === 'transparent') && node.parentElement) {
                node = node.parentElement;
                bg = getComputedStyle(node).backgroundColor;
              }
              if (!bg || bg === 'rgba(0, 0, 0, 0)') continue;
              const ratio = (Math.max(l1, lum(bg)) + 0.05) / (Math.min(l1, lum(bg)) + 0.05);
              const big = parseFloat(cs.fontSize) >= 24 || (parseFloat(cs.fontSize) >= 18.66 && parseInt(cs.fontWeight) >= 700);
              const min = big ? 3 : 4.5;
              if (ratio < min) lowContrast++;
            } catch (e) { /* skip */ }
          }
          const root = getComputedStyle(document.documentElement);
          const cssVars = ['--primary', '--primary-2', '--dark', '--text', '--line', '--surface', '--font-heading'].map(v => v + '=' + (root.getPropertyValue(v) || '').trim()).join(' ');
          const headingCount = document.querySelectorAll('h1').length;
          return { imgs: imgs.length, noDims, noAlt, lowContrast, smallTargets, fonts: [...fonts].slice(0, 14), cssVars, h1Count: headingCount, domNodes: document.querySelectorAll('*').length };
        });
        rec.consoleErrors = consoleErrors;
        rec.failedRequests = failedReq;
      } catch (e) {
        rec.error = String(e).slice(0, 120);
      }
      page.off('console', onConsole);
      page.off('response', onResp);
      results.push(rec);
      const axeN = rec.axe ? rec.axe.reduce((s, v) => s + v.nodes, 0) : '?';
      console.log(`${name}@${vpName}: axe=${axeN} lowContrast=${rec.metrics?.lowContrast ?? '?'} noDims=${rec.metrics?.noDims ?? '?'} err=${(rec.consoleErrors || []).length}`);
    }
    await ctx.close();
  }
  await browser.close();
  fs.writeFileSync(`${OUT}/audit-results.json`, JSON.stringify(results, null, 2));
  console.log('DONE -> ' + OUT + '/audit-results.json');
})();
