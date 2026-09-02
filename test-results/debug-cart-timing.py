from playwright.sync_api import sync_playwright
import time
BASE = 'http://localhost:8080'
with sync_playwright() as p:
    b = p.chromium.launch(headless=True)
    pg = b.new_page()
    
    # Add to cart with proper wait
    pg.goto(BASE + '/product/vineta-test-simple-product/', wait_until='networkidle', timeout=15000)
    time.sleep(3)
    
    btn = pg.locator('.btn-submit-total, .single_add_to_cart_button').first
    if btn.count() > 0:
        btn.click()
        # Wait for AJAX to complete
        time.sleep(5)
        count = pg.evaluate("parseInt(document.querySelector('.count-box')?.textContent || '0')")
        print("Cart count after add: %d" % count)
        
        # Check WC cart via API
        pg.goto(BASE + '/cart/', wait_until='domcontentloaded', timeout=12000)
        time.sleep(3)
        cart_rows = pg.locator('.cart_item, .woocommerce-cart-form__cart-item, .cart-row').count()
        print("Cart rows: %d" % cart_rows)
        
        # Check via WC AJAX
        result = pg.evaluate("""fetch('/?wc-ajax=get_refreshed_fragments').then(r=>r.json()).then(d=>{
            var count = 0;
            if (d && d.fragments && d.fragments['span.count-box']) {
                var m = d.fragments['span.count-box'].match(/\\d+/);
                if (m) count = parseInt(m[0]);
            }
            return count;
        }).catch(e => -1)""")
        print("WC AJAX fragment count: %s" % result)
    
    b.close()
