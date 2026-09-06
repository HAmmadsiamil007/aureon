"""Verify the Snapchat icon fix renders the SVG ghost instead of raw text.
Part of the Phase-4 browser QA evidence chain (L-12 in the acceptance matrix)."""
from playwright.sync_api import sync_playwright
from pathlib import Path

OUT = Path("test-results")
OUT.mkdir(exist_ok=True)

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    page = browser.new_page(viewport={"width": 1440, "height": 900})
    errors = []
    page.on("console", lambda m: errors.append(m.text) if m.type == "error" else None)

    page.goto("http://localhost:8080/", wait_until="networkidle")

    icon = page.locator("a.social-snapchat i.icon-snapchat")

    # 1. The ::before pseudo-element must now be a mask box, not visible text
    metrics = icon.evaluate("""el => {
        const s = getComputedStyle(el, '::before');
        const r = el.getBoundingClientRect();
        return {
            content:        s.content,
            maskImage:      s.maskImage || s.webkitMaskImage,
            visibleWidth:   r.width,
            visibleHeight:  r.height,
            elementText:    (el.textContent || '').trim()
        };
    }""")

    print("SNAPCHAT ICON ::before METRICS")
    for k, v in metrics.items():
        print(f"  {k}: {v}")

    passed = (
        metrics["content"].strip().strip('"') == ""          # no ligature text
        and "snapchat.svg" in metrics["maskImage"]            # SVG mask applied
        and metrics["visibleWidth"] > 0                       # box actually renders
        and metrics["visibleHeight"] > 0
    )

    # 2. Screenshots: header topbar zone + footer social zone
    page.screenshot(path=str(OUT / "snapchat-fix-header.png"),
                    clip={"x": 0, "y": 0, "width": 1440, "height": 60})
    footer = page.locator("footer").first
    footer.scroll_into_view_if_needed()
    page.wait_for_timeout(400)
    footer.screenshot(path=str(OUT / "snapchat-fix-footer.png"))

    print(f"CONSOLE ERRORS: {len(errors)}")
    for e in errors[:5]:
        print(f"  {e}")

    print("VERDICT:", "PASS - SVG ghost renders, no raw text" if passed else "FAIL")
    raise SystemExit(0 if passed else 1)
