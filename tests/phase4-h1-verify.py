"""Homepage H1 final check: exactly one, hidden by any standard visually-hidden mechanism."""
import random
from playwright.sync_api import sync_playwright

with sync_playwright() as p:
    b = p.chromium.launch(headless=True)
    pg = b.new_context(viewport={"width": 1440, "height": 900}).new_page()
    pg.goto("http://localhost:8080/?h1f=%d" % random.randint(10**7, 10**8), wait_until="networkidle")
    pg.wait_for_timeout(1500)
    r = pg.evaluate("""() => {
        const h1s = [...document.querySelectorAll('h1')];
        const isHidden = (h) => {
            const cs = getComputedStyle(h);
            return cs.display === 'none' || cs.visibility === 'hidden' ||
                   cs.clip === 'rect(0px, 0px, 0px, 0px)' ||
                   cs.clipPath.includes('inset(50%)') ||
                   (parseInt(cs.width) <= 1 && parseInt(cs.height) <= 1 && cs.overflow === 'hidden');
        };
        return {count: h1s.length, allHidden: h1s.every(isHidden),
                text: h1s.map(h => h.textContent.trim()).join(' | ')};
    }""")
    print("COUNT:", r["count"], "| ALL HIDDEN:", r["allHidden"], "| TEXT:", r["text"])
    assert r["count"] == 1 and r["allHidden"]
    b.close()
print("VERDICT: PASS")
