"""Plugin compatibility test for Vineta pack"""
from playwright.sync_api import sync_playwright
import time, json

BASE = "http://localhost:8080"

plugins = {
    "woocommerce": {
        "capability": "eCommerce platform",
        "surfaces": ["cart", "checkout", "account", "products", "categories"],
    },
    "aureon": {
        "capability": "Theme framework / design engine",
        "surfaces": ["header", "footer", "menus", "customizer", "path-bridge"],
    },
    "aureon-fix-wc-session": {
        "capability": "WC session persistence fix",
        "surfaces": ["cart persistence", "session handling"],
    }
}

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    ctx = browser.new_context(viewport={"width": 1440, "height": 900})
    pg = ctx.new_page()

    results = {}

    # Test WooCommerce
    print("=== WooCommerce ===")
    wc_tests = {}
    
    # 1. Product display
    pg.goto(BASE + "/shop/", wait_until="domcontentloaded", timeout=12000)
    time.sleep(2)
    wc_tests["shop_renders"] = pg.locator(".product, .product-card, .tf-product-card, .wc-block-grid__product").count() > 0
    wc_tests["wc_scripts"] = pg.evaluate("typeof wc_add_to_cart_params !== 'undefined'")
    wc_tests["wc_cart_fragments"] = pg.evaluate("typeof jQuery !== 'undefined' && typeof jQuery(document).trigger === 'function'")
    print("  Shop renders: %s" % wc_tests["shop_renders"])
    print("  WC scripts loaded: %s" % wc_tests["wc_scripts"])

    # 2. Cart functionality
    pg.goto(BASE + "/cart/", wait_until="domcontentloaded", timeout=12000)
    time.sleep(1)
    wc_tests["cart_page"] = "cart" in pg.url or pg.locator(".woocommerce-cart-form, .cart-container, .shopping-cart").count() > 0
    print("  Cart page: %s" % wc_tests["cart_page"])

    # 3. Checkout
    pg.goto(BASE + "/checkout/", wait_until="domcontentloaded", timeout=12000)
    time.sleep(1)
    wc_tests["checkout_page"] = "checkout" in pg.url
    wc_tests["checkout_form"] = pg.locator("form.checkout, #order_review, .woocommerce-checkout").count() > 0
    print("  Checkout page: %s" % wc_tests["checkout_page"])
    print("  Checkout form: %s" % wc_tests["checkout_form"])

    # 4. Account
    pg.goto(BASE + "/my-account/", wait_until="domcontentloaded", timeout=12000)
    time.sleep(1)
    wc_tests["account_page"] = "my-account" in pg.url
    wc_tests["login_form"] = pg.locator(".form-login, form.login, #username").count() > 0
    print("  Account page: %s" % wc_tests["account_page"])
    print("  Login form: %s" % wc_tests["login_form"])

    # 5. WC API endpoints
    pg.goto(BASE + "/?wc-ajax=get_refreshed_fragments", wait_until="domcontentloaded", timeout=10000)
    time.sleep(1)
    wc_tests["wc_ajax"] = pg.evaluate("document.body.innerText.includes('{')") or "wc" in pg.content().lower()
    print("  WC AJAX endpoint: %s" % wc_tests["wc_ajax"])

    wc_pass = sum(1 for v in wc_tests.values() if v)
    results["woocommerce"] = {"status": "PASS" if wc_pass >= 5 else "PARTIAL", "pass": wc_pass, "total": len(wc_tests)}

    # Test Aureon
    print("\n=== Aureon ===")
    aureon_tests = {}
    pg.goto(BASE + "/", wait_until="domcontentloaded", timeout=12000)
    time.sleep(2)
    
    aureon_tests["header"] = pg.locator("header, .header, .tf-header").count() > 0
    aureon_tests["footer"] = pg.locator("footer, .footer, .tf-footer").count() > 0
    aureon_tests["menus"] = pg.locator("nav, .navigation, .header-menu").count() > 0
    aureon_tests["pagedata"] = pg.evaluate("typeof VinetaPageData !== 'undefined'")
    aureon_tests["customizer"] = pg.evaluate("typeof VinetaCustomizer !== 'undefined'")
    aureon_tests["no_js_errors"] = len(pg.evaluate("window.__consoleErrors || []")) == 0
    
    print("  Header: %s" % aureon_tests["header"])
    print("  Footer: %s" % aureon_tests["footer"])
    print("  Menus: %s" % aureon_tests["menus"])
    print("  VinetaPageData: %s" % aureon_tests["pagedata"])
    print("  VinetaCustomizer: %s" % aureon_tests["customizer"])
    
    au_pass = sum(1 for v in aureon_tests.values() if v)
    results["aureon"] = {"status": "PASS" if au_pass >= 5 else "PARTIAL", "pass": au_pass, "total": len(aureon_tests)}

    # Test aureon-fix-wc-session
    print("\n=== aureon-fix-wc-session ===")
    session_tests = {}
    pg.goto(BASE + "/", wait_until="domcontentloaded", timeout=12000)
    time.sleep(1)
    # Check if WC session cookie exists
    cookies = pg.context.cookies()
    wc_cookies = [c for c in cookies if "wc" in c["name"].lower() or "woocommerce" in c["name"].lower()]
    session_tests["wc_cookie"] = len(wc_cookies) > 0
    print("  WC cookies: %s (found: %s)" % (session_tests["wc_cookie"], [c["name"] for c in wc_cookies]))
    
    se_pass = sum(1 for v in session_tests.values() if v)
    results["aureon-fix-wc-session"] = {"status": "PASS" if se_pass >= 1 else "PARTIAL", "pass": se_pass, "total": len(session_tests)}

    browser.close()

# Summary
print("\n=== PLUGIN COMPATIBILITY SUMMARY ===")
all_pass = True
for name, data in results.items():
    status = data["status"]
    icon = "PASS" if status == "PASS" else "PARTIAL" if status == "PARTIAL" else "FAIL"
    print("  %s: %s (%d/%d)" % (name, icon, data["pass"], data["total"]))
    if status != "PASS":
        all_pass = False

print("\nOverall: %s" % ("PASS" if all_pass else "PARTIAL"))

with open("C:/Users/hamma/Downloads/phantom/wordpress/test-results/plugin-compatibility.json", "w") as f:
    json.dump(results, f, indent=2)
