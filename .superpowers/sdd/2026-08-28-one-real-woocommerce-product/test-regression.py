#!/usr/bin/env python3
"""Regression test: simple product #834 + variable product add-to-cart."""
import sys
from playwright.sync_api import sync_playwright

def run():
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True)
        ctx = browser.new_context(viewport={"width": 1440, "height": 900})

        # --- REGRESSION: Simple Product #834 ---
        print("=" * 60)
        print("REGRESSION: Simple Product #834 (Meridian Lamp Black)")
        print("=" * 60)
        page = ctx.new_page()
        page.goto("http://localhost:8080/product/meridian-lamp-black/", wait_until="domcontentloaded", timeout=30000)
        page.wait_for_timeout(3000)

        r834 = page.evaluate("""() => {
            var pd = window.FermPageData;
            var p = pd ? pd.product : null;
            var section = document.querySelector('[data-component="productPage"]') ||
                          document.querySelector('[data-section-type="product"]');
            var addToCart = section ? section.querySelector('[data-component="addToCart"]') : null;
            var children = addToCart ? addToCart.children.length : 0;
            var button = addToCart ? addToCart.querySelector('[data-button-add-to-cart]') : null;

            return {
                hasProduct: !!p,
                title: p ? p.title : null,
                productType: p ? p.product_type : null,
                price: p ? p.price : null,
                sku: p ? p.sku : null,
                variants: p ? p.variants.length : 0,
                h1Text: section ? (section.querySelector('h1') || {}).textContent : null,
                addToCartChildren: children,
                hasButton: !!button,
            };
        }""")

        checks_834 = [
            ("FermPageData.product", r834.get("hasProduct")),
            ("Title", r834.get("title") == "Meridian Lamp Black"),
            ("Product type", r834.get("productType") == "simple"),
            ("Price", r834.get("price") == 18900),
            ("SKU", r834.get("sku") == "FL-LAMP-MER-001"),
            ("Variants", r834.get("variants") == 0),
            ("H1", r834.get("h1Text") and "Meridian" in r834.get("h1Text")),
            ("addToCart children", r834.get("addToCartChildren", 0) >= 2),
            ("Add to Cart button", r834.get("hasButton")),
        ]
        all_pass = True
        for label, ok in checks_834:
            status = "PASS" if ok else "FAIL"
            if not ok: all_pass = False
            print(f"  [{status}] {label}")

        # --- VARIABLE PRODUCT: Add-to-Cart ---
        print("\n" + "=" * 60)
        print("VARIABLE PRODUCT: Add-to-Cart #828 (Trifolium)")
        print("=" * 60)
        page2 = ctx.new_page()

        # Clear cart first
        page2.goto("http://localhost:8080/?wc-ajax=cart_trash_all", wait_until="domcontentloaded", timeout=15000)
        page2.wait_for_timeout(500)

        page2.goto("http://localhost:8080/product/trifolium-side-table/", wait_until="domcontentloaded", timeout=30000)
        page2.wait_for_timeout(3000)

        # Click the Green swatch (should change price to 92500)
        greenClicked = page2.evaluate("""() => {
            var section = document.querySelector('[data-component="productPage"]');
            var swatches = section ? section.querySelectorAll('[data-color-handle]') : [];
            for (var i = 0; i < swatches.length; i++) {
                var handle = swatches[i].getAttribute('data-color-handle');
                if (handle === 'green' || handle === 'Green') {
                    swatches[i].click();
                    return {clicked: handle, variantId: document.querySelector('[data-variant-id]') ? document.querySelector('[data-variant-id]').getAttribute('data-variant-id') : null};
                }
            }
            return {clicked: null, error: "Green swatch not found"};
        }""")
        page2.wait_for_timeout(500)

        print(f"  Green swatch click: {greenClicked}")

        # Check if variant ID and price updated
        afterGreen = page2.evaluate("""() => {
            var pd = window.FermPageData;
            var section = document.querySelector('[data-component="productPage"]');
            var variantId = section ? (section.querySelector('[data-variant-id]') || {}).getAttribute('data-variant-id') : null;
            var priceEl = section ? (section.querySelector('[data-component="price"]') || {}).textContent.trim() : null;
            return {variantId: variantId, price: priceEl};
        }""")
        print(f"  After Green click: variantId={afterGreen.get('variantId')}, price={afterGreen.get('price')}")

        # Click Add to Cart
        addResult = page2.evaluate("""() => {
            var section = document.querySelector('[data-component="productPage"]');
            var button = section ? section.querySelector('[data-button-add-to-cart]') : null;
            if (button) {
                button.click();
                return "clicked";
            }
            return "button not found";
        }""")
        print(f"  Add to Cart: {addResult}")
        page2.wait_for_timeout(2000)

        # Check cart state
        cartState = page2.evaluate("""() => {
            if (window.FermCart) {
                return {
                    itemCount: FermCart.count || 0,
                    items: (FermCart.items || []).map(function(i) {
                        return {id: i.id || i.product_id, qty: i.quantity, name: i.title || i.name};
                    }),
                };
            }
            return {error: "FermCart not found"};
        }""")
        print(f"  Cart state: {cartState}")

        # Check cart count in header
        cartCount = page2.evaluate("""() => {
            var el = document.querySelector('[data-cart-count]');
            return el ? el.textContent.trim() : "N/A";
        }""")
        print(f"  Header cart count: {cartCount}")

        # Network
        network_urls = []
        page2.on("request", lambda req: network_urls.append(req.url))
        page2.reload(wait_until="domcontentloaded", timeout=30000)
        page2.wait_for_timeout(3000)

        shopify_calls = [u for u in network_urls if "shopify" in u.lower() and "localhost" not in u]
        print(f"\n  Network: Shopify={len(shopify_calls)}")

        # Take screenshots
        page2.screenshot(path=".superpowers/sdd/2026-08-28-one-real-woocommerce-product/variable-828-green-1440.png", full_page=False)
        page2.set_viewport_size({"width": 390, "height": 844})
        page2.wait_for_timeout(500)
        page2.screenshot(path=".superpowers/sdd/2026-08-28-one-real-woocommerce-product/variable-828-green-390.png", full_page=False)

        browser.close()

    print(f"\n{'=' * 60}")
    print(f"RESULT: {'PASS' if all_pass else 'BLOCKED'}")
    print(f"{'=' * 60}")
    return 0 if all_pass else 1

if __name__ == "__main__":
    sys.exit(run())
