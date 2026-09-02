from playwright.sync_api import sync_playwright
import time
BASE = 'http://localhost:8080'
with sync_playwright() as p:
    b = p.chromium.launch(headless=True)
    pg = b.new_page()
    pages = ['/','/shop/','/product/vineta-test-simple-product/','/product/vineta-test-variable-product/',
             '/cart/','/my-account/','/blog/','/about-us/','/contact-us/','/faq/']
    total_broken = 0
    for path in pages:
        pg.goto(BASE+path, wait_until='domcontentloaded', timeout=12000)
        time.sleep(2)
        broken = pg.evaluate('([...document.querySelectorAll("img")]).filter(i => !i.complete || i.naturalHeight === 0).length')
        total = pg.evaluate('document.querySelectorAll("img").length')
        status = "PASS" if broken == 0 else "FAIL"
        print("%s %s broken=%d/%d" % (status, path, broken, total))
        total_broken += broken
    print("\nTotal broken: %d" % total_broken)
    b.close()
