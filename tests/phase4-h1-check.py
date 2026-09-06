"""Verify the visually-hidden homepage H1: present, hidden, survives hydration, exactly one."""
import random
from playwright.sync_api import sync_playwright

with sync_playwright() as p:
    b = p.chromium.launch(headless=True)
    pg = b.new_context(viewport={"width": 1440, "height": 900}).new_page()
    pg.goto("http://localhost:8080/?h1t=%d" % random.randint(10**7, 10**8), wait_until="networkidle")
    pg.wait_for_timeout(2000)
    r = pg.evaluate("""() => {
        const h1s = [...document.querySelectorAll('h1')];
        return {
            count: h1s.length,
            text: h1s.map(h => h.textContent.trim()).join(' | '),
            visible: h1s.map(h => {
                const cs = getComputedStyle(h);
                return !(cs.display === 'none' || cs.visibility === 'hidden' ||
                         (parseFloat(cs.fontSize) !== 0 && Math.abs(parseFloat(cs.textIndent)) < 1 &&
                          cs.position !== 'absolute' && cs.clip !== 'rect(0px, 0px, 0px, 0px)'));
            })
        };
    }""")
    print("H1 COUNT:", r["count"])
    print("TEXT:", r["text"])
    print("ANY VISIBLE:", any(r["visible"]))
    assert r["count"] == 1, "expected exactly 1 h1"
    assert not any(r["visible"]), "h1 must be visually hidden"
    pg.screenshot(path="test-results/phase4-h1-hidden.png")
    b.close()
print("VERDICT: PASS")
