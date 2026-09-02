from playwright.sync_api import sync_playwright
import time
BASE = 'http://localhost:8080'
with sync_playwright() as p:
    b = p.chromium.launch(headless=True)
    pg = b.new_page()
    pages = ['/','/shop/','/product/vineta-test-simple-product/','/product/vineta-test-variable-product/',
             '/cart/','/my-account/','/blog/','/about-us/','/contact-us/','/faq/']
    for path in pages:
        pg.goto(BASE+path, wait_until='domcontentloaded', timeout=12000)
        time.sleep(1.5)
        h1 = pg.evaluate('document.querySelectorAll("h1").length')
        h1_text = pg.evaluate('document.querySelector("h1") ? document.querySelector("h1").textContent.trim().substring(0,40) : "NONE"')
        print('%s H1=%d text="%s"' % (path, h1, h1_text))
    b.close()
