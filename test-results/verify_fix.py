"""Verify add-to-cart fix works"""
from playwright.sync_api import sync_playwright
import time

with sync_playwright() as p:
    b = p.chromium.launch(headless=True)
    pg = b.new_page(viewport={"width":1440,"height":900})
    
    # Simple product
    print("=== SIMPLE PRODUCT ===")
    pg.goto("http://localhost:8080/product/vineta-test-simple-product/", wait_until="domcontentloaded", timeout=12000)
    time.sleep(3)
    
    # Click add to cart
    btn = pg.locator(".btn-submit-total, button.single_add_to_cart_button, button[name='add-to-cart']").first
    print("Add button found: %s" % btn.count())
    if btn.count() > 0:
        btn.click()
        time.sleep(3)
    
    # Check WC cart
    cart = pg.evaluate("""fetch("/wp-json/wc/store/v1/cart", {credentials: "same-origin"}).then(r=>r.json()).then(d=>({count: d.items_count, total: d.totals.total_price}))""")
    time.sleep(1)
    print("WC Cart: count=%s total=%s" % (cart.get("count"), cart.get("total")))
    
    # Variable product
    print("\n=== VARIABLE PRODUCT ===")
    pg.goto("http://localhost:8080/product/vineta-test-variable-product/", wait_until="domcontentloaded", timeout=12000)
    time.sleep(3)
    
    # Select Blue + S
    blue = pg.locator("button.btn-variant:has-text('Blue')").first
    s = pg.locator("button.btn-variant:has-text('S')").first
    if blue.count() > 0: blue.click(); time.sleep(0.5)
    if s.count() > 0: s.click(); time.sleep(0.5)
    
    btn2 = pg.locator(".btn-submit-total, button.single_add_to_cart_button").first
    print("Add button found: %s" % btn2.count())
    if btn2.count() > 0:
        btn2.click()
        time.sleep(3)
    
    cart2 = pg.evaluate("""fetch("/wp-json/wc/store/v1/cart", {credentials: "same-origin"}).then(r=>r.json()).then(d=>({count: d.items_count, total: d.totals.total_price, items: d.items.map(i=>({name:i.name, qty:i.quantity, price:i.totals.line_total}))}))""")
    time.sleep(1)
    print("WC Cart: count=%s total=%s" % (cart2.get("count"), cart2.get("total")))
    if cart2.get("items"):
        for item in cart2["items"]:
            print("  - %s x%s = %s" % (item.get("name","?"), item.get("qty","?"), item.get("price","?")))
    
    # Now try checkout
    print("\n=== CHECKOUT ===")
    pg.goto("http://localhost:8080/checkout/", wait_until="domcontentloaded", timeout=15000)
    time.sleep(3)
    print("URL: %s" % pg.url)
    
    has_wc = pg.evaluate("document.querySelector('.woocommerce-checkout') !== null")
    has_form = pg.evaluate("document.querySelector('form.checkout') !== null")
    has_review = pg.evaluate("document.querySelector('#order_review') !== null")
    print("WC checkout: %s, form.checkout: %s, #order_review: %s" % (has_wc, has_form, has_review))
    
    pg.screenshot(path="C:/Users/hamma/Downloads/phantom/wordpress/test-results/final-acceptance-screenshots/verify-checkout.png")
    b.close()
