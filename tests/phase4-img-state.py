"""Phase 4 — measure broken <img> elements on key routes (naturalWidth === 0)."""
import random
import sys
from playwright.sync_api import sync_playwright

def pr(*a):
    print(*[str(x).encode("cp1252", "replace").decode("cp1252") for x in a])

with sync_playwright() as p:
    b = p.chromium.launch(headless=True)
    pg = b.new_context(viewport={"width": 1440, "height": 900}).new_page()
    for route in ["/", "/shop", "/product/floral-green-kurta-2"]:
        pg.goto("http://localhost:8080%s?br=%d" % (route, random.randint(10**7, 10**8)),
                wait_until="networkidle")
        pg.wait_for_timeout(2000)
        imgs = pg.evaluate("""() => {
            const out = {total: 0, broken: 0, brokenSamples: [], logoSrc: null, logoOk: null};
            const all = document.querySelectorAll('img');
            out.total = all.length;
            all.forEach(i => {
                if (i.naturalWidth === 0 && i.offsetParent !== null) {
                    out.broken++;
                    if (out.brokenSamples.length < 5) out.brokenSamples.push(i.getAttribute('src'));
                }
            });
            const logo = document.querySelector('img.logo, .logo-header img, header img');
            if (logo) { out.logoSrc = logo.src; out.logoOk = logo.naturalWidth > 0; }
            return out;
        }""")
        pr(f"ROUTE {route}: imgs={imgs['total']} broken={imgs['broken']}")
        for s in imgs["brokenSamples"]:
            pr("   broken:", s[:110])
        pr("   logo:", imgs["logoSrc"], "ok:", imgs["logoOk"])
    b.close()
