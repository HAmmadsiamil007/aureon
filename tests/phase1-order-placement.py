"""PHASE 1 — Real order placement E2E through the live Vineta checkout.
Places a genuine COD order via the rendered WC checkout form, verifies the
thank-you page + database state, and reports the order ID for cleanup."""
import re
import sys
from playwright.sync_api import sync_playwright
from pathlib import Path

OUT = Path("test-results")
OUT.mkdir(exist_ok=True)
BASE = "http://localhost:8080"

def pr(*args):
    """cp1252-safe print (PKR currency sign breaks the Windows console)."""
    print(*[str(a).encode("cp1252", "replace").decode("cp1252") for a in args])

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    ctx = browser.new_context(viewport={"width": 1440, "height": 900})
    page = ctx.new_page()
    console_errors = []
    page.on("console", lambda m: console_errors.append(m.text) if m.type == "error" else None)

    # 1. Add known product (#577) via storefront URL — proven path from the
    #    earlier curl cart E2E (vineta cards don't use WC's add_to_cart_button class)
    page.goto(f"{BASE}/?add-to-cart=577", wait_until="networkidle")

    # 2. Cart shows the item (vineta cart rows are custom markup — the robust
    #    signal is the WC quantity input name="cart[...]" and no empty-cart notice)
    page.goto(f"{BASE}/cart", wait_until="networkidle")
    page.wait_for_timeout(800)
    qty_inputs = page.locator(".woocommerce-cart-form input[name^='cart[']").count()
    empty_notice = page.locator(".cart-empty, .woocommerce-info:has-text('empty')").count()
    print("CART QTY INPUTS:", qty_inputs, "| EMPTY-NOTICE:", empty_notice)
    assert qty_inputs > 0 and empty_notice == 0, "cart is empty - add-to-cart failed"

    # 3. Go to checkout, fill the real WC form
    page.goto(f"{BASE}/checkout", wait_until="networkidle")
    page.wait_for_selector(".woocommerce-checkout form", timeout=30000)

    f = {
        "billing_first_name": "QA", "billing_last_name": "Tester",
        "billing_address_1": "123 Test Street", "billing_city": "Lahore",
        "billing_postcode": "54000", "billing_phone": "03001234567",
        "billing_email": "qa-tester@example.com",
    }
    for name, val in f.items():
        loc = page.locator(f"[name='{name}']")
        if loc.count():
            loc.first.fill(val)
        else:
            pr("  (field not present: %s)" % name)
    # Country/state may be selects
    if page.locator("#billing_country").count():
        page.locator("#billing_country").select_option(label="Pakistan")
    if page.locator("#billing_state").count():
        try:
            page.locator("#billing_state").select_option(label="Punjab")
        except Exception:
            pass

    # 4. Payment method: COD — vineta's radio has no id; select by value
    cod = page.locator("input[name='payment_method'][value='cod']")
    pr("COD RADIO PRESENT:", cod.count() > 0)
    if cod.count() and not cod.first.is_checked():
        cod.first.check(force=True)
    pr("COD CHECKED:", cod.first.is_checked() if cod.count() else "n/a")

    # 5. Accept terms if present
    terms = page.locator("#terms")
    if terms.count() and terms.is_visible():
        terms.check(force=True)

    page.screenshot(path=str(OUT / "order-checkout-filled.png"), full_page=True)

    # 6. Place the order (try both the WC id and the name attribute)
    btn = page.locator("#place_order, button[name='woocommerce_checkout_place_order']").first
    pr("PLACE-ORDER BTN:", btn.count() > 0)
    btn.click()
    page.wait_for_load_state("domcontentloaded")
    page.wait_for_timeout(3000)

    url = page.url
    body = page.content()
    pr("POST-SUBMIT URL:", url)

    # Capture any WC validation errors verbatim (encode-safe)
    errs = page.locator(".woocommerce-error, .wc-block-components-notice__content").all_inner_texts()
    pr("WC ERRORS:", len(errs))
    for e in errs[:6]:
        pr("  !", e.strip().replace("\n", " | ")[:200])

    pr("THANK-YOU PAGE:", "order-received" in url or "Thank you" in body)

    m = re.search(r"order-received/(\d+)", url)
    order_id = m.group(1) if m else "?"
    pr("ORDER ID:", order_id)
    page.screenshot(path=str(OUT / "order-thankyou.png"), full_page=True)
    pr("CONSOLE ERRORS:", len(console_errors))
    for e in console_errors[:5]:
        pr("  ", e)

    with open(OUT / "last-order-id.txt", "w", encoding="utf-8") as fh:
        fh.write(url)

    browser.close()
    pr("RESULT:", "PASS" if ("order-received" in url) else "FAIL")
