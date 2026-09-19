const { chromium } = require('playwright');
(async () => {
  const browser = await chromium.launch();
  const page = await (await browser.newContext({ viewport: { width: 1440, height: 900 } })).newPage();
  const collect = { css: new Set(), js: new Set(), homeCss: new Set(), homeJs: new Set(), cartCss: new Set(), cartJs: new Set() };
  const sniff = (sets) => r => {
    const u = r.url();
    if (u.endsWith('.css')) { sets.css.add(u); sets[Object.keys(sets).find(k => sets[k] instanceof Set && k.endsWith('Css'))]?.add(u); }
  };
  // home
  let urls = [];
  page.on('response', r => urls.push(r.url()));
  await page.goto('http://localhost:8080/', { waitUntil: 'networkidle', timeout: 45000 });
  urls.forEach(u => { if (u.match(/\.css(\?|$)/)) collect.homeCss.add(u); if (u.match(/\.js(\?|$)/)) collect.homeJs.add(u); });
  urls = [];
  await page.goto('http://localhost:8080/cart/', { waitUntil: 'networkidle', timeout: 45000 });
  urls.forEach(u => { if (u.match(/\.css(\?|$)/)) collect.cartCss.add(u); if (u.match(/\.js(\?|$)/)) collect.cartJs.add(u); });
  const base = s => s.replace(/\?.*$/, '').replace('http://localhost:8080', '');
  const homeOnlyCss = [...collect.homeCss].filter(u => !collect.cartCss.has(u)).map(base);
  const homeOnlyJs = [...collect.homeJs].filter(u => !collect.cartJs.has(u)).map(base);
  console.log('=== CSS only on home (not cart):', homeOnlyCss.length, '===');
  homeOnlyCss.forEach(u => console.log('  ', u.split('/').slice(-2).join('/')));
  console.log('=== JS only on home (not cart):', homeOnlyJs.length, '===');
  homeOnlyJs.forEach(u => console.log('  ', u.split('/').slice(-2).join('/')));
  console.log('=== CSS on BOTH:', [...collect.homeCss].filter(u => collect.cartCss.has(u)).length, '| JS on BOTH:', [...collect.homeJs].filter(u => collect.cartJs.has(u)).length);
  await browser.close();
})();
