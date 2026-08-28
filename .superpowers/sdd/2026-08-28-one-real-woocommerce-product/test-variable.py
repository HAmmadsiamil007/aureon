#!/usr/bin/env python3
"""Variable product integration test — Trifolium Side Table #828."""
import sys
from playwright.sync_api import sync_playwright

URL = "http://localhost:8080/product/trifolium-side-table/"
VIEWPORTS = [(1440, 900), (1024, 768), (768, 1024), (390, 844)]

def run():
    errors = []
    results = {}

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

        for w, h in VIEWPORTS:
            page.set_viewport_size({"width": w, "height": h})
            page.wait_for_timeout(500)

            result = page.evaluate("""() => {
                var pd = window.FermPageData;
                var p = pd ? pd.product : null;
                var section = document.querySelector('[data-component="productPage"]') ||
                              document.querySelector('[data-section-type="product"]');
                var addToCart = section ? section.querySelector('[data-component="addToCart"]') : null;
                var children = addToCart ? addToCart.children.length : 0;
                var button = addToCart ? addToCart.querySelector('[data-button-add-to-cart]') : null;                    var swatches = section ? section.querySelectorAll('[data-color-handle], [data-hex]') : [];
                    if (swatches.length === 0) swatches = document.querySelectorAll('[data-color-handle], [data-hex]');
                var h1 = section ? section.querySelector('h1') : null;

                return {
                    hasProduct: !!p,
                    title: p ? p.title : null,
                    productType: p ? p.product_type : null,
                    price: p ? p.price : null,
                    priceVaries: p ? p.price_varies : null,
                    variants: p ? p.variants.length : 0,
                    options: p ? p.options : [],
                    colors: p ? (p.colors || []).length : 0,
                    colorName: p ? p.color_name : null,
                    selectedVariant: p ? p.selected_variant_id : null,
                    sku: p ? p.sku : null,
                    h1Text: h1 ? h1.textContent.trim() : null,
                    addToCartChildren: children,
                    hasButton: !!button,
                    buttonText: button ? button.textContent.trim() : null,
                    swatchCount: swatches.length,
                };
            }""")

            result["viewport"] = f"{w}x{h}"
            results[f"{w}x{h}"] = result

            # Take screenshot
            page.screenshot(
                path=f".superpowers/sdd/2026-08-28-one-real-woocommerce-product/variable-{w}x{h}.png",
                full_page=False
            )

        # Network analysis
        shopify_calls = [u for u in network_urls if "shopify" in u.lower() and "localhost" not in u]
        clerk_calls = [u for u in network_urls if "clerk" in u.lower()]
        external_ferm = [u for u in network_urls if "fermliving.com" in u]
        google_fonts = [u for u in network_urls if "fonts.googleapis" in u or "fonts.gstatic" in u]
        not_found = [u for u in network_urls if "/404" in u or "favicon" in u.lower()]

        # Console analysis
        real_errors = [e for e in console_errors if "404" not in e and "favicon" not in e.lower()]

        browser.close()

    # Print results
    print("=" * 70)
    print("VARIABLE PRODUCT VERIFICATION — Trifolium Side Table #828")
    print("=" * 70)

    all_pass = True
    for vp, r in results.items():
        print(f"\n--- Viewport: {vp} ---")
        checks = [
            ("FermPageData.product", r.get("hasProduct")),
            ("Title", r.get("title") == "Trifolium Side Table"),
            ("Product type", r.get("productType") == "variable"),
            ("Price", r.get("price") == 89500),
            ("Price varies", r.get("priceVaries") == True),
            ("Variants count", r.get("variants") == 3),
            ("Options", r.get("options") == ["Color"]),
            ("Colors", r.get("colors") == 3),
            ("Color name", r.get("colorName") == "Black"),
            ("Selected variant", r.get("selectedVariant") == 829),
            ("H1", r.get("h1Text") == "Trifolium Side Table"),
            ("addToCart children", r.get("addToCartChildren", 0) >= 2),
            ("Add to Cart button", r.get("hasButton")),
            ("Color swatches", r.get("swatchCount", 0) >= 3),
        ]
        for label, ok in checks:
            status = "PASS" if ok else "FAIL"
            if not ok:
                all_pass = False
            print(f"  [{status}] {label}: {ok}")

    print(f"\n--- Network ---")
    print(f"  Shopify calls: {len(shopify_calls)} {'PASS' if len(shopify_calls) == 0 else 'FAIL'}")
    print(f"  Clerk calls: {len(clerk_calls)} {'PASS' if len(clerk_calls) == 0 else 'FAIL'}")
    print(f"  External Ferm CDN: {len(external_ferm)} {'PASS' if len(external_ferm) == 0 else 'FAIL'}")
    print(f"  Google Fonts: {len(google_fonts)} {'PASS' if len(google_fonts) == 0 else 'FAIL'}")
    print(f"  404s: {len(not_found)} {'PASS' if len(not_found) == 0 else 'FAIL'}")

    print(f"\n--- Console ---")
    print(f"  Unexpected errors: {len(real_errors)} {'PASS' if len(real_errors) == 0 else 'FAIL'}")
    for e in real_errors[:5]:
        print(f"    {e[:120]}")

    print(f"\n{'=' * 70}")
    print(f"RESULT: {'VARIABLE_PRODUCT_INTEGRATION_PASS' if all_pass else 'VARIABLE_PRODUCT_INTEGRATION_BLOCKED'}")
    print(f"{'=' * 70}")

    return 0 if all_pass else 1

if __name__ == "__main__":
    sys.exit(run())
