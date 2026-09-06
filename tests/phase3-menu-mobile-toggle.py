"""Phase 3 — mobile menu toggle shows WP-driven rename (encode-safe)."""
import random
import sys
from playwright.sync_api import sync_playwright

def pr(*a):
    print(*[str(x).encode("cp1252", "replace").decode("cp1252") for x in a])

with sync_playwright() as p:
    b = p.chromium.launch(headless=True)
    pg = b.new_context(viewport={"width": 390, "height": 844}).new_page()
    pg.goto("http://localhost:8080/?mt=%d" % random.randint(10**8, 10**9),
            wait_until="networkidle")
    pg.wait_for_timeout(1500)

    # Find any hamburger-ish toggle
    candidates = [".tf-hamburger", ".hamburger", ".offcanvas-toggle",
                  "[class*='hamburger']", ".header-toggle", ".tf-btn-menu"]
    clicked = None
    for sel in candidates:
        loc = pg.locator(sel)
        if loc.count() and loc.first.is_visible():
            loc.first.click(force=True)
            clicked = sel
            break
    pr("TOGGLE USED:", clicked)
    pg.wait_for_timeout(1000)

    state = pg.evaluate("""() => ({
        body: document.body.innerText,
        menus: [...document.querySelectorAll('.offcanvas, .mobile-menu, .tf-mobile-nav, nav, .box-navigation')]
            .map(e => ({cls: e.className.slice(0,50), vis: e.offsetParent !== null, txt: e.innerText.slice(0,120)}))
    })""")
    pr("RENAME in mobile page after toggle:", "QA-RENAMED" in state["body"])
    visible_menus = [m for m in state["menus"] if m["vis"]]
    for m in visible_menus[:3]:
        pr("VISIBLE MENU:", m["cls"], "->", repr(m["txt"][:90]))
    b.close()
