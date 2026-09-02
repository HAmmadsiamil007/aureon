from playwright.sync_api import sync_playwright
import time
p = sync_playwright().start()
b = p.chromium.launch(headless=True)
pg = b.new_page()

# Check shop page
pg.goto('http://localhost:8080/shop/', wait_until='domcontentloaded', timeout=12000)
time.sleep(2)
url = pg.url
title = pg.title()
h1s = pg.evaluate('([...document.querySelectorAll("h1")]).map(h => h.textContent.trim().substring(0,50))')
h4s = pg.evaluate('([...document.querySelectorAll("h4")]).slice(0,3).map(h => h.textContent.trim().substring(0,50))')
page_title = pg.evaluate('document.querySelector(".tf-page-title") ? document.querySelector(".tf-page-title").innerHTML.substring(0,300) : "NO SECTION"')
print("SHOP URL:", url)
print("SHOP TITLE:", title)
print("H1s:", h1s)
print("H4s:", h4s)
print("PAGE_TITLE:", page_title[:200])

# Check about page
pg.goto('http://localhost:8080/about-us/', wait_until='domcontentloaded', timeout=12000)
time.sleep(2)
h1s2 = pg.evaluate('([...document.querySelectorAll("h1")]).map(h => ({text: h.textContent.trim().substring(0,50), cls: h.className}))')
sr = pg.evaluate('document.querySelector(".sr-only") ? document.querySelector(".sr-only").outerHTML.substring(0,200) : "NO SR-ONLY"')
print()
print("ABOUT H1s:", h1s2)
print("SR-ONLY:", sr)

b.close()
p.stop()
