"""Phase 3 — find the real mobile toggle from the pack markup and verify rename inside drawer."""
import random
import sys
from playwright.sync_api import sync_playwright

def pr(*a):
    print(*[str(x).encode("cp1252", "replace").decode("cp1252") for x in a])

with sync_playwright() as p:
    b = p.chromium.launch(headless=True)
    pg = b.new_context(viewport={"width": 390, "height": 844}).new_page()
    pg.goto("http://localhost:8080/?deep=%d" % random.randint(10**8, 10**9),
            wait_until="networkidle")
    pg.wait_for_timeout(1500)

    # Discover toggle buttons from the live header
    info = pg.evaluate("""() => {
        const btns = [...document.querySelectorAll('header button, header .icon-menu, [class*="menu-bar"], .tf-hamburger, .mobile-menu-wrap')];
        return btns.map(e => ({tag: e.tagName, cls: (e.className||'').toString().slice(0,60), vis: e.offsetParent !== null})).slice(0, 8);
    }""")
    for i in info:
        pr("CANDIDATE:", i["tag"], "|", i["cls"], "| visible:", i["vis"])

    # Click the icon-menu element (vineta's hamburger icon)
    burger = pg.locator("header .icon-menu, .icon-menu").first
    if burger.count():
        burger.click(force=True)
        pg.wait_for_timeout(1200)
        body = pg.evaluate("() => document.body.innerText")
        pr("RENAME VISIBLE AFTER BURGER CLICK:", "QA-RENAMED" in body)
        pg.screenshot(path="test-results/phase3-mobile-menu-open.png")
    else:
        pr("no burger found")
    b.close()
