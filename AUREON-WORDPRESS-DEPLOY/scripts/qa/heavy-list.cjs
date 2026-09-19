const { chromium } = require('playwright');
(async () => {
  const browser = await chromium.launch();
  const page = await (await browser.newContext({ viewport: { width: 1440, height: 900 } })).newPage();
  const assets = [];
  const rh = async r => {
    const u = r.url();
    if (u.match(/\.(css|js)(\?|$)/)) {
      try { const b = await r.body(); assets.push({ url: u.replace('http://localhost:8080', ''), type: u.endsWith('.css') ? 'css' : 'js', kb: Math.round(b.length / 1024) }); } catch (e) {}
    }
  };
  page.on('response', rh);
  await page.goto('http://localhost:8080/', { waitUntil: 'networkidle', timeout: 45000 });
  page.off('response', rh);
  assets.sort((a, b) => b.kb - a.kb);
  console.log('=== HOME: top assets ===');
  assets.slice(0, 22).forEach(a => console.log(String(a.kb).padStart(6) + 'KB', a.type, a.url.split('/').slice(-2).join('/')));
  console.log('TOTAL css:', assets.filter(a => a.type === 'css').reduce((s, a) => s + a.kb, 0), 'KB | js:', assets.filter(a => a.type === 'js').reduce((s, a) => s + a.kb, 0), 'KB');
  await browser.close();
})();
