const { chromium } = require('@playwright/test');

(async () => {
  const browser = await chromium.launch({ channel: 'chrome', headless: true });
  const page = await browser.newPage({ viewport: { width: 1280, height: 800 } });

  page.on('console', m => {
    if (m.type() === 'log') console.log('[PAGE]', m.text());
  });
  page.on('pageerror', e => console.log('[PAGEERROR]', e.message));

  page.addInitScript(() => {
    const rec = m => console.log('INIT:' + m);
    const origFetch = window.fetch;
    window.fetch = function (input, init) {
      const url = typeof input === 'string' ? input : input.url;
      if (url.includes('wc-ajax') || url.includes('admin-ajax')) {
        rec('FETCH ' + (init && init.method || 'GET') + ' ' + url);
        return origFetch(input, init).then(res => {
          const clone = res.clone();
          clone.json().then(j => rec('FETCH-RESP status=' + res.status + ' keys=' + Object.keys(j).join(',') + ' success=' + j.success + ' error=' + (j.error || '') + ' msg=' + (j.message || '') + ' redirect=' + (j.redirect || ''))).catch(() => rec('FETCH-RESP non-json status=' + res.status));
          return res;
        });
      }
      return origFetch(input, init);
    };
    const origAdd = document.addEventListener.bind(document);
    document.addEventListener = function (type, fn, opts) {
      const s = typeof fn === 'function' ? fn.toString().slice(0, 200).replace(/\s+/g, ' ') : String(fn);
      if (type === 'click') rec('REG click src=' + s.slice(0, 160));
      if (type === 'click' && s.includes('add-to-cart-btn')) {
        rec('ATTACH a2c: ' + s.slice(0, 80));
        const wrapped = (e) => {
          rec('A2C FIRED target=' + e.target.tagName + ' on=' + location.pathname);
          try { fn.call(this, e); setTimeout(() => rec('A2C post defaultPrevented=' + e.defaultPrevented), 0); }
          catch (err) { rec('A2C THREW: ' + err); }
        };
        return origAdd(type, wrapped, opts);
      }
      return origAdd(type, fn, opts);
    };
    const origPD = Event.prototype.preventDefault;
    Event.prototype.preventDefault = function () {
      if (this.type === 'click') rec('PREVENT stack=' + (new Error().stack || '').split('\n').slice(2, 8).map(l => l.trim().slice(0, 100)).join(' ^ '));
      return origPD.call(this);
    };
    const origPS = Event.prototype.stopPropagation;
    Event.prototype.stopPropagation = function () {
      if (this.type === 'click') rec('STOPPROP stack=' + (new Error().stack || '').split('\n').slice(2, 4).map(l => l.trim().slice(0, 60)).join(' ^ '));
      return origPS.call(this);
    };
    const origSIP = Event.prototype.stopImmediatePropagation;
    Event.prototype.stopImmediatePropagation = function () {
      if (this.type === 'click') rec('STOPIMM stack=' + (new Error().stack || '').split('\n').slice(2, 4).map(l => l.trim().slice(0, 60)).join(' ^ '));
      return origSIP.call(this);
    };
    const loc = window.location;
    let cur = loc.href;
    const desc = Object.getOwnPropertyDescriptor(Location.prototype, 'href');
    Object.defineProperty(Location.prototype, 'href', {
      get() { return desc.get.call(this); },
      set(v) {
        rec('NAV location.href= ' + v + ' stack=' + (new Error().stack || '').split('\n').slice(2, 4).map(l => l.trim().slice(0, 60)).join(' ^ '));
        return desc.set.call(this, v);
      }
    });
    const ps = history.pushState.bind(history);
    history.pushState = (s, t, u) => { rec('NAV pushState ' + u); return ps(s, t, u); };
    const rs = history.replaceState.bind(history);
    history.replaceState = (s, t, u) => { rec('NAV replaceState ' + u); return rs(s, t, u); };
    for (const evt of ['mousedown', 'mouseup', 'click']) {
      document.addEventListener(evt, e => rec('DOC-' + evt + ' target=' + e.target.tagName + '.' + String(e.target.className).slice(0, 15)), true);
    }
    rec('BOOT ' + location.pathname);
  });

  await page.goto('http://localhost:8080/shop/?nocache=node1', { waitUntil: 'domcontentloaded' });
  await page.waitForTimeout(3000);
  await page.evaluate(() => document.querySelector('.add-to-cart-btn').scrollIntoView({ block: 'center' }));
  await page.waitForTimeout(1500);

  const btn = page.locator('.add-to-cart-btn[data-product-id]').first();
  await btn.hover();
  await page.waitForTimeout(800);
  const b = await btn.boundingBox();
  const hit = await page.evaluate(({ x, y }) => {
    const el = document.elementFromPoint(x, y);
    const anchor = document.querySelector('.add-to-cart-btn[data-product-id]');
    return { ok: el === anchor || anchor.contains(el), elTag: el.tagName };
  }, { x: b.x + b.width / 2, y: b.y + b.height / 2 });
  console.log('HIT:', JSON.stringify(hit));
  await page.mouse.click(b.x + b.width / 2, b.y + b.height / 2);
  await page.waitForTimeout(3000);
  console.log('FINAL URL:', page.url());
  await browser.close();
})();