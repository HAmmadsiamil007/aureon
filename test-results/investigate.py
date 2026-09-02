"""Investigate checkout and auth page structure"""
from playwright.sync_api import sync_playwright
import time

with sync_playwright() as p:
    b = p.chromium.launch(headless=True)
    pg = b.new_page(viewport={"width":1440,"height":900})
    
    # Add item to cart first
    pg.goto("http://localhost:8080/product/vineta-test-simple-product/", wait_until="domcontentloaded", timeout=12000)
    time.sleep(2)
    btn = pg.locator("button.single_add_to_cart_button, button[name='add-to-cart']").first
    if btn.count() > 0:
        btn.click()
        time.sleep(2)
    
    # CHECKOUT
    print("=== CHECKOUT ===")
    pg.goto("http://localhost:8080/checkout/", wait_until="domcontentloaded", timeout=12000)
    time.sleep(3)
    print(f"URL: {pg.url}")
    print(f"Title: {pg.title()}")
    forms = pg.evaluate("document.querySelectorAll('form').length")
    print(f"Forms: {forms}")
    checkout_els = pg.evaluate("document.querySelectorAll('#order_review, .checkout, .woocommerce-checkout, #customer_details, .woocommerce-checkout-form').length")
    print(f"Checkout els: {checkout_els}")
    form_details = pg.evaluate("[...document.querySelectorAll('form')].map(f => ({id:f.id, cls:f.className.substring(0,80), action:f.action.substring(0,100)}))")
    for fd in form_details[:5]:
        print(f"  Form: id={fd['id']} class={fd['cls']} action={fd['action']}")
    body_text = pg.evaluate("document.body.innerText.substring(0, 500)")
    print(f"Body: {body_text[:400]}")
    pg.screenshot(path="C:/Users/hamma/Downloads/phantom/wordpress/test-results/final-acceptance-screenshots/investigate-checkout.png")
    
    # AUTH
    print("\n=== AUTH ===")
    pg.goto("http://localhost:8080/my-account/", wait_until="domcontentloaded", timeout=12000)
    time.sleep(2)
    print(f"URL: {pg.url}")
    print(f"Title: {pg.title()}")
    forms2 = pg.evaluate("document.querySelectorAll('form').length")
    print(f"Forms: {forms2}")
    login_els = pg.evaluate("document.querySelectorAll('#customer_login, .woocommerce-form-login, [class*=login], [id*=login], [class*=register]').length")
    print(f"Login/register els: {login_els}")
    form_details2 = pg.evaluate("[...document.querySelectorAll('form')].map(f => ({id:f.id, cls:f.className.substring(0,80), action:f.action.substring(0,100)}))")
    for fd in form_details2[:5]:
        print(f"  Form: id={fd['id']} class={fd['cls']} action={fd['action']}")
    body_text2 = pg.evaluate("document.body.innerText.substring(0, 500)")
    print(f"Body: {body_text2[:400]}")
    pg.screenshot(path="C:/Users/hamma/Downloads/phantom/wordpress/test-results/final-acceptance-screenshots/investigate-auth.png")
    
    # Variable product
    print("\n=== VARIABLE PRODUCT ===")
    pg.goto("http://localhost:8080/product/vineta-test-variable-product/", wait_until="domcontentloaded", timeout=12000)
    time.sleep(2)
    add_btns = pg.evaluate("document.querySelectorAll('button[name=add-to-cart], .single_add_to_cart_button, .variations_form button[type=submit]').length")
    print(f"Add buttons: {add_btns}")
    var_forms = pg.evaluate("document.querySelectorAll('.variations_form, form.cart').length")
    print(f"Variation forms: {var_forms}")
    selects = pg.evaluate("document.querySelectorAll('select, [data-attribute_name]').length")
    print(f"Selects/variation attrs: {selects}")
    body_text3 = pg.evaluate("document.body.innerText.substring(0, 300)")
    print(f"Body: {body_text3[:200]}")
    pg.screenshot(path="C:/Users/hamma/Downloads/phantom/wordpress/test-results/final-acceptance-screenshots/investigate-variable.png")
    
    b.close()
    print("\nDone")
