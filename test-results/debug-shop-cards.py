from playwright.sync_api import sync_playwright
import time
BASE = 'http://localhost:8080'
with sync_playwright() as p:
    b = p.chromium.launch(headless=True)
    pg = b.new_page()
    pg.goto(BASE + '/shop/', wait_until='domcontentloaded', timeout=12000)
    time.sleep(2)
    # Check what product-related elements exist
    product_els = pg.evaluate("""[...document.querySelectorAll('[class*=product], [class*=card], [data-product-id]')].slice(0,5).map(e => ({
        tag: e.tagName,
        cls: e.className.substring(0,60),
        id: e.id || '',
        dataId: e.dataset.productId || ''
    }))""")
    print("Product-related elements:")
    for el in product_els:
        print("  %s cls='%s' id='%s'" % (el['tag'], el['cls'], el['id']))
    
    # Check for wc product grid
    wc_grid = pg.locator('.wc-block-grid, .products, .product-grid, .shop-container').count()
    print("\nWC grid containers: %d" % wc_grid)
    
    # Check for price elements
    prices = pg.locator('.price, .amount, [class*=price]').count()
    print("Price elements: %d" % prices)
    
    b.close()
