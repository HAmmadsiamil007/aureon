from playwright.sync_api import sync_playwright
import time
BASE = 'http://localhost:8080'
with sync_playwright() as p:
    b = p.chromium.launch(headless=True)
    pg = b.new_page()
    
    # Add item to cart first
    pg.goto(BASE + '/product/vineta-test-simple-product/', wait_until='domcontentloaded', timeout=12000)
    time.sleep(2)
    # Try clicking add to cart
    add_btns = pg.locator('.btn-submit-total, .single_add_to_cart_button, button[name="add-to-cart"]')
    if add_btns.count() > 0:
        add_btns.first.click()
        time.sleep(3)
        print("Added to cart")
    
    # Now check checkout
    pg.goto(BASE + '/checkout/', wait_until='domcontentloaded', timeout=12000)
    time.sleep(3)
    url = pg.url
    title = pg.title()
    has_form = pg.locator('form.checkout, #order_review, .woocommerce-checkout, .tf-checkout').count() > 0
    has_place = pg.locator('#placeOrderBtn, #place_order, .place-order').count() > 0
    print("Checkout URL: %s" % url)
    print("Checkout title: %s" % title)
    print("Has form: %s" % has_form)
    print("Has place order: %s" % has_place)
    
    # Check shop page product cards
    pg.goto(BASE + '/shop/', wait_until='domcontentloaded', timeout=12000)
    time.sleep(2)
    products = pg.locator('.product, .product-card, .tf-product-card, .wc-block-grid__product, .product-grid-item').count()
    print("\nShop product cards: %d" % products)
    
    # Check WC cookies
    cookies = pg.context.cookies()
    for c in cookies:
        if 'wc' in c['name'].lower() or 'wordpress' in c['name'].lower():
            print("Cookie: %s = %s..." % (c['name'], c['value'][:30]))
    
    b.close()
