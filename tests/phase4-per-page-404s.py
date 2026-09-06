"""Probe: which route + DOM element triggers each 404 asset request."""
import random
from playwright.sync_api import sync_playwright

ROUTES = ["/", "/shop", "/checkout", "/my-account?auth=login", "/cart", "/blog", "/nonexistent-page-xyz"]

with sync_playwright() as p:
    b = p.chromium.launch(headless=True)
    pg = b.new_context(viewport={"width": 1440, "height": 900}).new_page()
    for route in ROUTES:
        fails = []
        pg.on("response", lambda r: fails.append((r.status, r.url)) if r.status >= 400 else None)
        pg.goto("http://localhost:8080" + route + ("&" if "?" in route else "?") + "pp=%d" % random.randint(10**7, 10**8), wait_until="networkidle")
        pg.wait_for_timeout(1200)
        if fails:
            print("ROUTE:", route)
            for st, u in fails:
                print("   ", st, u)
                # find who references it
                try:
                    src = pg.evaluate("""(u) => {
                        const needle = u.split('/').slice(-2).join('/');
                        const els = [...document.querySelectorAll('[src],[href]')];
                        const hit = els.find(e => (e.src || e.href || '').includes(needle));
                        return hit ? hit.outerHTML.slice(0, 160) : 'not-in-final-dom (JS/dynamic)';
                    }""", u)
                except Exception as e:
                    src = "eval-error: %s" % e
                print("      ->", src)
        fails.clear()
    b.close()
print("DONE")
