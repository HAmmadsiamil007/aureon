from playwright.sync_api import sync_playwright
import time
BASE = 'http://localhost:8080'
with sync_playwright() as p:
    b = p.chromium.launch(headless=True)
    pg = b.new_page()
    
    # Enable request logging
    requests_log = []
    pg.on("request", lambda req: requests_log.append(req.url) if "cart" in req.url.lower() or "add" in req.url.lower() else None)
    
    pg.goto(BASE + '/product/vineta-test-simple-product/', wait_until='networkidle', timeout=15000)
    time.sleep(3)
    
    # Check if add to cart button exists and is visible
    btn = pg.locator('.btn-submit-total').first
    print("Button count:", pg.locator('.btn-submit-total').count())
    print("Button visible:", btn.is_visible() if btn.count() > 0 else False)
    print("Button text:", btn.text_content().strip()[:30] if btn.count() > 0 else "N/A")
    
    # Try clicking
    if btn.count() > 0 and btn.is_visible():
        btn.click()
        time.sleep(5)
        print("Clicked. Requests made:")
        for r in requests_log:
            if "cart" in r.lower() or "add" in r.lower():
                print("  %s" % r[:100])
        
        count = pg.evaluate("parseInt(document.querySelector('.count-box')?.textContent || '0')")
        print("Cart count: %d" % count)
        
        # Check WC cart via direct API
        result = pg.evaluate("""fetch('/wp-json/wc/store/v1/cart', {credentials: 'include'}).then(r=>r.json()).then(d=>d.items_count||0).catch(e=>-1)""")
        print("WC Store API cart count: %s" % result)
    
    b.close()
