"""Phase 3 — open the pack's #mobileMenu drawer and verify the WP rename inside."""
import random
import sys
from playwright.sync_api import sync_playwright

def pr(*a):
    print(*[str(x).encode("cp1252", "replace").decode("cp1252") for x in a])

with sync_playwright() as p:
    b = p.chromium.launch(headless=True)
    pg = b.new_context(viewport={"width": 390, "height": 844}).new_page()
    pg.goto("http://localhost:8080/?ff=%d" % random.randint(10**8, 10**9),
            wait_until="networkidle")
    pg.wait_for_timeout(1500)

    # The pack ships a #mobileMenu drawer; find its opener
    res = pg.evaluate("""() => {
        const mm = document.querySelector('#mobileMenu');
        const style = mm ? getComputedStyle(mm) : null;
        const openers = [...document.querySelectorAll('button, a, [role="button"]')]
            .filter(e => (e.getAttribute('onclick') || '').includes('mobileMenu')
                      || (e.getAttribute('data-bs-target') || '') === '#mobileMenu'
                      || (e.getAttribute('data-target') || '') === '#mobileMenu');
        return {hasDrawer: !!mm, display: style ? style.display : null,
                openerCount: openers.length,
                openerHtml: openers.length ? openers[0].outerHTML.slice(0, 150) : null};
    }""")
    pr("DRAWER EXISTS:", res["hasDrawer"], "| display:", res["display"])
    pr("OPENERS:", res["openerCount"], res["openerHtml"])

    if res["openerCount"]:
        pg.locator("[data-bs-target='#mobileMenu'], [data-target='#mobileMenu'], [onclick*='mobileMenu']").first.click(force=True)
        pg.wait_for_timeout(1000)
    elif res["hasDrawer"]:
        # Directly unhide via JS (pack may rely on a class the test can't click)
        pg.evaluate("() => { const m = document.querySelector('#mobileMenu'); m.classList.add('show'); m.style.display='block'; }")
        pg.wait_for_timeout(500)

    body = pg.evaluate("() => document.querySelector('#mobileMenu') ? document.querySelector('#mobileMenu').innerText : ''")
    pr("RENAME IN MOBILE DRAWER:", "QA-RENAMED" in body)
    pr("DRAWER SAMPLE:", repr(body[:120]))
    pg.screenshot(path="test-results/phase3-mobile-menu-open.png")
    b.close()
