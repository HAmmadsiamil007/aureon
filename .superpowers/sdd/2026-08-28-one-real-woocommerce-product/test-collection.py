#!/usr/bin/env python3
"""Collection/Archive integration test — Accessories category."""
import sys
from playwright.sync_api import sync_playwright

URL = "http://localhost:8080/product-category/accessories/"
VIEWPORTS = [(1440, 900), (1024, 768), (768, 1024), (390, 844)]

def run():
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        ctx = browser.new_context(viewport={"width": 1440, "height": 900})
        page = ctx.new_page()

        console_errors = []
        page.on("console", lambda msg: console_errors.append(msg.text) if msg.type == "error" else None)

        network_urls = []
        page.on("request", lambda req: network_urls.append(req.url))

        page.goto(URL, wait_until="domcontentloaded", timeout=30000)
        page.wait_for_timeout(3000)

        results = {}
        for w, h in VIEWPORTS:
            page.set_viewport_size({"width": w, "height": h})
            page.wait_for_timeout(500)

            result = page.evaluate("""() => {
                var pd = window.FermPageData;
                var c = pd ? pd.collection : null;
                var section = document.querySelector('[data-component="collectionTemplate"]');
                var h1 = section ? section.querySelector('h1') : null;
                var thumbs = section ? section.querySelectorAll('[data-component="productThumb"]') : [];
                var productIds = [];
                thumbs.forEach(function(t) {
                    var idEl = t.querySelector('[data-product-id]');
                    if (idEl) productIds.push(idEl.getAttribute('data-product-id'));
                });

                // Check first product link
                var firstLink = thumbs.length > 0 ? thumbs[0].querySelector('a[href]') : null;
                var firstHref = firstLink ? firstLink.getAttribute('href') : null;
                var isWpLink = firstHref ? !firstHref.endsWith('.html') : false;

                // Check first product image
                var firstImg = thumbs.length > 0 ? thumbs[0].querySelector('img') : null;
                var hasRealImg = firstImg ? (firstImg.src.indexOf('localhost') > -1) : false;

                return {
                    hasCollection: !!c,
                    title: c ? c.title : null,
                    productCount: c ? c.product_count : 0,
                    thumbsRendered: thumbs.length,
                    productIds: productIds.slice(0, 10),
                    firstHref: firstHref,
                    isWpLink: isWpLink,
                    hasRealImg: hasRealImg,
                    h1Text: h1 ? h1.textContent.trim() : null,
                };
            }""")

            result["viewport"] = f"{w}x{h}"
            results[f"{w}x{h}"] = result

            page.screenshot(
                path=f".superpowers/sdd/2026-08-28-one-real-woocommerce-product/collection-{w}x{h}.png",
                full_page=False
            )

        # Network analysis
        shopify_calls = [u for u in network_urls if "shopify" in u.lower() and "localhost" not in u]
        clerk_calls = [u for u in network_urls if "clerk" in u.lower()]
        external_ferm = [u for u in network_urls if "fermliving.com" in u]
        google_fonts = [u for u in network_urls if "fonts.googleapis" in u or "fonts.gstatic" in u]

        # Console analysis
        real_errors = [e for e in console_errors if "404" not in e and "favicon" not in e.lower()]

        # Regression: check products #834 and #828
        page.set_viewport_size({"width": 1440, "height": 900})
        regression_results = {}

        for slug, expected in [("meridian-lamp-black", "Meridian Lamp Black"), ("trifolium-side-table", "Trifolium Side Table")]:
            page.goto(f"http://localhost:8080/product/{slug}/", wait_until="domcontentloaded", timeout=30000)
            page.wait_for_timeout(2000)
            reg = page.evaluate("""(slug) => {
                var pd = window.FermPageData;
                var p = pd ? pd.product : null;
                var section = document.querySelector('[data-component="productPage"]');
                var addToCart = section ? section.querySelector('[data-component="addToCart"]') : null;
                return {
                    hasProduct: !!p,
                    title: p ? p.title : null,
                    type: p ? p.product_type : null,
                    addToCartChildren: addToCart ? addToCart.children.length : 0,
                };
            }""", slug)
            regression_results[slug] = reg

        browser.close()

    # Print results
    print("=" * 70)
    print("COLLECTION INTEGRATION — Accessories Category")
    print("=" * 70)

    all_pass = True
    for vp, r in results.items():
        print(f"\n--- Viewport: {vp} ---")
        checks = [
            ("FermPageData.collection", r.get("hasCollection")),
            ("Title", r.get("title") == "Accessories"),
            ("Product count > 0", r.get("productCount", 0) > 0),
            ("Thumbs rendered > 0", r.get("thumbsRendered", 0) > 0),
            ("H1", r.get("h1Text") == "Accessories"),
            ("Product links are WP URLs", r.get("isWpLink")),
            ("Real product images", r.get("hasRealImg")),
        ]
        for label, ok in checks:
            status = "PASS" if ok else "FAIL"
            if not ok: all_pass = False
            print(f"  [{status}] {label}: {ok}")

    print(f"\n--- Network ---")
    print(f"  Shopify calls: {len(shopify_calls)} {'PASS' if len(shopify_calls) == 0 else 'FAIL'}")
    print(f"  Clerk calls: {len(clerk_calls)} {'PASS' if len(clerk_calls) == 0 else 'FAIL'}")
    print(f"  External Ferm CDN: {len(external_ferm)} {'PASS' if len(external_ferm) == 0 else 'FAIL'}")
    print(f"  Google Fonts: {len(google_fonts)} {'PASS' if len(google_fonts) == 0 else 'FAIL'}")

    print(f"\n--- Console ---")
    print(f"  Unexpected errors: {len(real_errors)} {'PASS' if len(real_errors) == 0 else 'FAIL'}")
    for e in real_errors[:5]:
        print(f"    {e[:120]}")

    print(f"\n--- Regression ---")
    for slug, reg in regression_results.items():
        ok = reg.get("hasProduct") and reg.get("addToCartChildren", 0) >= 2
        status = "PASS" if ok else "FAIL"
        if not ok: all_pass = False
        print(f"  [{status}] {slug}: title={reg.get('title')}, children={reg.get('addToCartChildren')}")

    print(f"\n{'=' * 70}")
    print(f"RESULT: {'COLLECTION_INTEGRATION_PASS' if all_pass else 'COLLECTION_INTEGRATION_BLOCKED'}")
    print(f"{'=' * 70}")

    return 0 if all_pass else 1

if __name__ == "__main__":
    sys.exit(run())
