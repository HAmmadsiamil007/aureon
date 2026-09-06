"""Phase 3 — mobile + footer menu verification before restore."""
import random
from playwright.sync_api import sync_playwright

with sync_playwright() as p:
    b = p.chromium.launch(headless=True)
    pg = b.new_context(viewport={"width": 390, "height": 844}).new_page()
    pg.goto("http://localhost:8080/?mob=%d" % random.randint(10**8, 10**9),
            wait_until="networkidle")
    pg.wait_for_timeout(2000)

    res = pg.evaluate("""() => {
        const all = document.body.innerText;
        const footer = document.querySelector('footer');
        return {
            mobileHasRename: all.includes('QA-RENAMED'),
            mobileHasOld: all.includes('New In'),
            footerText: footer ? footer.innerText.slice(0, 300) : 'NO FOOTER',
            footerHasRename: footer ? footer.innerText.includes('QA-RENAMED') : false
        };
    }""")
    print("MOBILE shows rename:", res["mobileHasRename"], "| old label gone:", not res["mobileHasOld"])
    print("FOOTER (primary location not expected there) has rename:", res["footerHasRename"])
    print("FOOTER SAMPLE:", repr(res["footerText"][:150]))

    # Mobile menu toggle if present
    toggle = pg.locator(".tf-hamburger, .hamburger, [class*='hamburger']").first
    if toggle.count():
        toggle.click(force=True)
        pg.wait_for_timeout(800)
        opened = pg.evaluate("() => document.body.innerText.includes('QA-RENAMED')")
        print("MOBILE MENU after toggle shows rename:", opened)
    b.close()
