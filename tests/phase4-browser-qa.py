"""PHASE 4 — Full browser QA: console, network, responsive overflow, a11y basics.

Runs the key routes at 1440/1024/768/390, records console errors, failed
requests, horizontal overflow, and per-page a11y essentials (h1 count,
images without alt, inputs without labels).
"""
import json
import random
import sys
from pathlib import Path

from playwright.sync_api import sync_playwright

def pr(*a):
    print(*[str(x).encode("cp1252", "replace").decode("cp1252") for x in a])

OUT = Path("test-results")
OUT.mkdir(exist_ok=True)
BASE = "http://localhost:8080"
BUST = random.randint(10**7, 10**8)

ROUTES = [
    "/", "/shop", "/product/floral-green-kurta-2", "/product-category/women",
    "/cart", "/checkout", "/blog", "/my-account?auth=login",
    "/?s=kurta&post_type=product", "/nonexistent-page-xyz",
]
VIEWPORTS = [(1440, 900), (1024, 768), (768, 1024), (390, 844)]

report = {}

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)

    for vp in VIEWPORTS:
        ctx = browser.new_context(viewport={"width": vp[0], "height": vp[1]})
        page = ctx.new_page()
        console_errors = []
        failed_requests = []
        page.on("console", lambda m: console_errors.append(m.text[:200]) if m.type == "error" else None)
        page.on("requestfailed", lambda r: failed_requests.append(r.url))
        page.on("response", lambda r: failed_requests.append(f"{r.status} {r.url}") if r.status >= 400 else None)

        vp_report = {}
        for route in ROUTES:
            url = route + ("&" if "?" in route else "?") + f"qa={BUST}"
            try:
                page.goto(BASE + url, wait_until="networkidle", timeout=45000)
            except Exception as e:
                vp_report[route] = {"error": str(e)[:120]}
                continue
            page.wait_for_timeout(700)

            metrics = page.evaluate("""() => ({
                overflowX: document.documentElement.scrollWidth > document.documentElement.clientWidth + 2,
                overflowBy: document.documentElement.scrollWidth - document.documentElement.clientWidth,
                h1: document.querySelectorAll('h1').length,
                imgsNoAlt: [...document.querySelectorAll('img')].filter(i => i.offsetParent !== null && !i.hasAttribute('alt')).length,
                inputsNoLabel: [...document.querySelectorAll('input:not([type=hidden])')]
                    .filter(i => i.offsetParent !== null
                        && !i.labels?.length && !i.getAttribute('aria-label')
                        && !i.getAttribute('aria-labelledby')).length,
                title: document.title.slice(0, 60)
            })""")
            vp_report[route] = metrics

        report[f"{vp[0]}x{vp[1]}"] = {
            "console_errors": console_errors[:10],
            "failed_requests": failed_requests[:10],
            "routes": vp_report,
        }
        ctx.close()

    browser.close()

(OUT / "phase4-browser-qa.json").write_text(json.dumps(report, indent=2), encoding="utf-8")

# Console/404 summary across all viewports (collected per-context above)
print("=== PHASE 4 SUMMARY ===")
total_overflow = 0
total_h1_missing = 0
for vp, data in report.items():
    ce = len(data["console_errors"])
    fr = [f for f in data["failed_requests"]]
    pr(f"VIEWPORT {vp}: console_errors={ce} failed_requests={len(fr)}")
    for f in fr[:4]:
        pr("   req:", f[:130])
    for e in data["console_errors"][:3]:
        pr("   con:", e[:130])
    for route, m in data["routes"].items():
        if m.get("overflowX"):
            total_overflow += 1
            pr(f"   OVERFLOW {vp} {route}: by {m.get('overflowBy')}px")
        if vp == "1440x900" and m.get("h1", 1) == 0 and route not in ("/cart", "/checkout"):
            total_h1_missing += 1
            pr(f"   H1 MISSING: {route}")

pr("")
pr("TOTAL overflow findings:", total_overflow, "| H1-missing findings:", total_h1_missing)
pr("FULL REPORT -> test-results/phase4-browser-qa.json")
