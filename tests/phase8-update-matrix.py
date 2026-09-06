"""Phase 8 — append final-validation evidence (V-01..V-08) to the acceptance matrix.

Adds today's local-runtime gates as a new evidence block:
- V-01 real order placement E2E (orders created, thank-you reached, DB verified)
- V-02 checkout defects fixed (wc_checkout_params blob, blockui, place_order name, nonce action)
- V-03 Customizer announcement round-trip (set -> server payload -> live DOM marquee -> restore)
- V-04 menu round-trip desktop + mobile drawer (rename -> live nav -> restore)
- V-05 browser QA sweep 4 viewports (0 overflow, 0 app JS errors, 0 asset 404s)
- V-06 homepage H1 added (hidden, single, hydration-safe)
- V-07 snapchat icon fix (bridge + svg mask, all templates)
- V-08 pack-relative image paths absolutized (ferm rewriter + standalone header)

Honesty rules: production gates stay BLOCKED; nothing is inflated.
"""
import json
from datetime import date

PATH = "test-results/VINETA-FINAL-PRODUCTION-ACCEPTANCE-MATRIX.json"

with open(PATH, encoding="utf-8") as f:
    d = json.load(f)

existing = {t["id"] for t in d["tests"]}

NEW = [
    {
        "id": "V-01", "category": "local-runtime-e2e", "route": "/checkout -> /checkout/order-received",
        "setup": "COD enabled, guest checkout on; real checkout POST via rendered WC form (Playwright)",
        "expected": "order created (wc-processing, correct totals/line items/billing), thank-you page reached in browser, order visible in DB",
        "actual": "orders #681-#684 created: wc-processing, PKR 7,000.00, COD, billing from form, line item 'Floral Green Kurta'; browser landed on order-received/682?key=wc_order_...; QA orders cleaned after evidence capture",
        "status": "PASS",
        "evidence": "tests/phase1-order-placement.py output; DB wp_posts shop_order_place_holder + lookup tables; screenshots test-results/phase1-thankyou*.png",
        "date": "2026-09-06",
    },
    {
        "id": "V-02", "category": "local-runtime-fix", "route": "/checkout",
        "setup": "diagnosed checkout JS bailing: wc_checkout_params blob absent while script printed",
        "expected": "checkout AJAX fires, nonce verifies, order processing runs",
        "actual": "root cause: theme re-registered 'wc-checkout' handle wiping WC's localize blob; also wrong native-submit name and hyphen nonce action; fixed with self-healing registration + jquery-blockui + woocommerce_checkout_place_order + woocommerce-process_checkout",
        "status": "PASS",
        "evidence": "commit 9874eb4 (form-checkout.php); live script pipeline jquery-core->migrate->blockui->wc-checkout-js-extra->wc-checkout-js; probe-checkout-js.py before/after",
        "date": "2026-09-06",
    },
    {
        "id": "V-03", "category": "local-runtime-customizer", "route": "/ (announcement marquee)",
        "setup": "wp-load PHP runner writing through WP options API into aureon_settings bucket (same storage the Customizer uses)",
        "expected": "set -> save -> server payload carries value -> live DOM marquee shows it -> reset -> original restored",
        "actual": "server payload carried QA text; browser DOM marquee rendered it after hydration; restored to null and re-verified",
        "status": "PASS",
        "evidence": "qa-roundtrip.php snapshot/set/restore JSON outputs; Playwright DOM check (announcement element text == QA value)",
        "date": "2026-09-06",
    },
    {
        "id": "V-04", "category": "local-runtime-menus", "route": "/ (nav + mobile drawer)",
        "setup": "renamed real WP nav menu item 671 ('New In' -> QA-RENAMED-<ts>) via wp_update_nav_menu_object",
        "expected": "rename visible in desktop nav AND mobile drawer; restore returns original",
        "actual": "QA-RENAMED-1788688658 rendered in desktop nav and mobile drawer post-hydration; menu restored; screenshots captured",
        "status": "PASS",
        "evidence": "tests/phase3-menu-check.py, phase3-menu-mobile-final.py output; test-results/phase3-menu-mobile.png",
        "date": "2026-09-06",
    },
    {
        "id": "V-05", "category": "local-browser-qa", "route": "10 routes x 4 viewports (1440/1024/768/390)",
        "setup": "Playwright sweep: console errors, failed requests, horizontal overflow, h1/alt/label checks",
        "expected": "0 horizontal overflow, 0 application JS errors, 0 required-asset 404s, h1 present on all routes",
        "actual": "overflow 0 across all viewports; console errors 0 (only intentional 404-document log); asset 404s 0 after fixes; h1 present on all routes",
        "status": "PASS",
        "evidence": "test-results/phase4-browser-qa.json (final run); tests/phase4-per-page-404s.py attribution",
        "date": "2026-09-06",
    },
    {
        "id": "V-06", "category": "a11y-fix", "route": "/",
        "setup": "homepage had zero h1 (headings started at h2)",
        "expected": "single visually-hidden h1, hidden by CSS, surviving JS hydration",
        "actual": "h1.visually-hidden added to pack index.html before hero; computed style confirms clip rect(0,0,0,0) 1px/1px overflow hidden; count=1 post-hydration",
        "status": "PASS",
        "evidence": "tests/phase4-h1-verify.py (COUNT:1 ALL HIDDEN:True); test-results/phase4-h1-hidden.png",
        "date": "2026-09-06",
    },
    {
        "id": "V-07", "category": "frontend-fix", "route": "all 58 templates (topbar/footer social)",
        "setup": "snapchat anchor rendered raw text (font codepoint \\e96a absent; band-aid wiped <i>)",
        "expected": "snapchat renders as icon identical in behavior to fb/ig/x siblings",
        "actual": "bridge band-aid replaced with sibling-consistent <i class='icon icon-snapchat'>; svg mask via currentColor; 0 console errors; verified on /, /shop, /404",
        "status": "PASS",
        "evidence": "commits aa4fd49 + 38de786; test-results/snapchat-fix-header.png, -footer.png",
        "date": "2026-09-06",
    },
    {
        "id": "V-08", "category": "asset-path-fix", "route": "deep routes + standalone auth pages",
        "setup": "pack-relative images/ paths 404ed at depth-2 routes and on standalone login/register/account (logo)",
        "expected": "all pack images resolve on every route; zero required 404s",
        "actual": "ferm-page.php rewriter extended (img src images/ + srcset) and standalone-header rewriter extended (images/ next to cdn/); final sweep shows 0 asset 404s",
        "status": "PASS",
        "evidence": "php -l clean; curl checks: auth logo absolute; phase4 final JSON failed_requests only contains intentional 404 document",
        "date": "2026-09-06",
    },
]

added = 0
for t in NEW:
    if t["id"] not in existing:
        d["tests"].append(t)
        added += 1

d["localRuntimeSummary"] = {
    "PASS": 10 + added,   # previous 10 LR-block passes + V-block (all PASS)
    "FAIL": 0,
    "BLOCKED": 1,
    "total": 11 + added,
    "note": "Local Docker runtime evidence (2026-09-06 final validation). V-01..V-08 = full local validation executed against the corrected RC-mounted runtime. Production gates remain BLOCKED until staging/production + SMTP + sandbox exist.",
}

with open(PATH, "w", encoding="utf-8") as f:
    json.dump(d, f, indent=2, ensure_ascii=False)

print("added:", added, "| total tests:", len(d["tests"]))
print("localRuntimeSummary:", json.dumps(d["localRuntimeSummary"]))
c = {}
for t in d["tests"]:
    c[t["status"]] = c.get(t["status"], 0) + 1
print("all 56 tests by status:", c)
