"""Debug checkout button and auth form"""
from playwright.sync_api import sync_playwright
import time

BASE = "http://localhost:8080"

with sync_playwright() as p:
    b = p.chromium.launch(headless=True)
    pg = b.new_page(viewport={"width":1440,"height":900})
    
    # Add item
    pg.goto(BASE+"/product/vineta-test-simple-product/", wait_until="domcontentloaded", timeout=12000)
    time.sleep(2)
    pg.locator(".btn-submit-total").first.click()
    time.sleep(2)
    
    # Checkout
    pg.goto(BASE+"/checkout/", wait_until="domcontentloaded", timeout=15000)
    time.sleep(4)
    
    print("=== CHECKOUT BUTTONS ===")
    btns = pg.evaluate("""[...document.querySelectorAll('button, input[type=submit], a.btn')].map(b=>({
        tag: b.tagName, text: b.textContent.trim().substring(0,50),
        cls: b.className.substring(0,80), type: b.type, name: b.name || '',
        id: b.id
    }))""")
    for btn in btns[:20]:
        print("  %s id=%s cls=%s name=%s text=%s" % (btn["tag"], btn["id"], btn["cls"][:50], btn["name"], btn["text"][:30]))
    
    # Check order review
    review = pg.evaluate("""({
        order_review: document.querySelector('#order_review') !== null,
        order_review_heading: document.querySelector('#order_review_heading') !== null,
        payment: document.querySelector('#payment') !== null,
        place_order: document.querySelector('#place_order') !== null,
        submit_order: document.querySelector('button[name=woocommerce_checkout_place_order]') !== null,
    })""")
    print("\nOrder review elements: %s" % str(review))
    
    pg.screenshot(path="C:/Users/hamma/Downloads/phantom/wordpress/test-results/final-acceptance-screenshots/checkout-buttons.png")
    
    # Auth page
    print("\n=== AUTH FORMS ===")
    pg.goto(BASE+"/my-account/", wait_until="domcontentloaded", timeout=12000)
    time.sleep(2)
    
    forms = pg.evaluate("""[...document.querySelectorAll('form')].map(f=>({
        id: f.id, cls: f.className.substring(0,80), action: f.action.substring(0,100),
        childCount: f.children.length,
        html: f.innerHTML.substring(0,200)
    }))""")
    for f in forms[:8]:
        print("  id=%s cls=%s action=%s" % (f["id"], f["cls"][:50], f["action"][:80]))
        print("    html: %s" % f["html"][:150])
    
    # Check all inputs on page
    inputs = pg.evaluate("""[...document.querySelectorAll('input')].map(i=>({
        name: i.name, type: i.type, id: i.id, placeholder: i.placeholder,
        cls: i.className.substring(0,40)
    }))""")
    print("\nAll inputs:")
    for inp in inputs[:15]:
        print("  name=%s type=%s id=%s placeholder=%s" % (inp["name"], inp["type"], inp["id"], inp["placeholder"][:20]))
    
    pg.screenshot(path="C:/Users/hamma/Downloads/phantom/wordpress/test-results/final-acceptance-screenshots/auth-forms.png")
    b.close()
