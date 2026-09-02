"""Debug checkout redirect"""
from playwright.sync_api import sync_playwright
import time

with sync_playwright() as p:
    b = p.chromium.launch(headless=True)
    pg = b.new_page(viewport={"width":1440,"height":900})
    
    # Add item
    pg.goto("http://localhost:8080/product/vineta-test-simple-product/", wait_until="domcontentloaded", timeout=12000)
    time.sleep(2)
    btn = pg.locator("button.single_add_to_cart_button, .btn-submit-total").first
    if btn.count() > 0:
        btn.click()
        time.sleep(3)
    
    # Check WC cart API
    cart_data = pg.evaluate("""fetch("/wp-json/wc/store/v1/cart", {credentials: "same-origin"})
        .then(r=>r.json())
        .then(d=>({items_count: d.items_count, items: d.items ? d.items.length : 0, total: d.totals}))""")
    time.sleep(1)
    print("WC Cart: %s" % str(cart_data))
    
    # Navigate to checkout
    resp = pg.goto("http://localhost:8080/checkout/", wait_until="domcontentloaded", timeout=15000)
    time.sleep(3)
    print("Final URL: %s" % pg.url)
    print("Status: %s" % (resp.status if resp else "N/A"))
    
    # Check checkout elements
    has_wc = pg.evaluate("document.querySelector('.woocommerce-checkout') !== null")
    has_form = pg.evaluate("document.querySelector('form.checkout') !== null")
    has_review = pg.evaluate("document.querySelector('#order_review') !== null")
    has_customer = pg.evaluate("document.querySelector('#customer_details') !== null")
    print("WC checkout: %s, form.checkout: %s, #order_review: %s, #customer_details: %s" % (has_wc, has_form, has_review, has_customer))
    
    # Check the cart form on the page
    cart_box = pg.evaluate("document.querySelector('.checkout-cart-box') !== null")
    print("checkout-cart-box: %s" % cart_box)
    
    # Try submitting the checkout-cart-box form
    form = pg.locator("form.checkout-cart-box").first
    if form.count() > 0:
        action = form.get_attribute("action")
        print("Cart checkout form action: %s" % action)
        # Try to submit it
        form.evaluate("f => f.submit()")
        time.sleep(3)
        print("After submit URL: %s" % pg.url)
    
    pg.screenshot(path="C:/Users/hamma/Downloads/phantom/wordpress/test-results/final-acceptance-screenshots/debug-checkout.png")
    b.close()
