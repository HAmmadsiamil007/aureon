"""Dump computed styles + matched stylesheets for the homepage H1."""
import random
from playwright.sync_api import sync_playwright

with sync_playwright() as p:
    b = p.chromium.launch(headless=True)
    pg = b.new_context(viewport={"width": 1440, "height": 900}).new_page()
    pg.goto("http://localhost:8080/?h1s=%d" % random.randint(10**7, 10**8), wait_until="networkidle")
    pg.wait_for_timeout(1500)
    r = pg.evaluate("""() => {
        const h = document.querySelector('h1');
        if (!h) return {found: false};
        const cs = getComputedStyle(h);
        const sheets = [...document.styleSheets].map(s => (s.href || 'inline').split('/').slice(-1)[0]);
        return {
            found: true,
            cls: h.className,
            pos: cs.position, clip: cs.clip, clipPath: cs.clipPath,
            w: cs.width, h: cs.height, margin: cs.margin,
            overflow: cs.overflow,
            sheetsWithVisuallyHidden: sheets.filter(s => s.toLowerCase().includes('bootstrap') || s.includes('styles'))
        };
    }""")
    import json
    print(json.dumps(r, indent=1))
    b.close()
