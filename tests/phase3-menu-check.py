"""Phase 3 — menu live round-trip verification (WP-driven menu contract)."""
import random
from playwright.sync_api import sync_playwright

with sync_playwright() as p:
    b = p.chromium.launch(headless=True)
    pg = b.new_context(viewport={"width": 1440, "height": 900}).new_page()
    pg.goto("http://localhost:8080/?m=%d" % random.randint(10**8, 10**9),
            wait_until="networkidle")
    pg.wait_for_timeout(2000)

    result = pg.evaluate("""() => {
        const slot = document.querySelector('[data-aureon-slot="global.navigation"]');
        const nav = document.querySelector('.box-navigation');
        return {
            slotExists: !!slot,
            slotText: slot ? slot.innerText : null,
            navText: nav ? nav.innerText : null,
            slotHtmlHead: slot ? slot.innerHTML.slice(0, 500) : null
        };
    }""")
    print("slotExists:", result["slotExists"])
    print("slotText:", repr((result["slotText"] or "")[:150]))
    print("navText:", repr((result["navText"] or "")[:150]))
    print("slotHtmlHead:", (result["slotHtmlHead"] or "")[:400])
    print("RENAME VISIBLE:", "QA-RENAMED" in (result["slotText"] or "") or "QA-RENAMED" in (result["navText"] or ""))
    b.close()
