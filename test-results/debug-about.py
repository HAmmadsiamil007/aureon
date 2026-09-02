from playwright.sync_api import sync_playwright
import time
p = sync_playwright().start()
b = p.chromium.launch(headless=True)
pg = b.new_page()

# Check about page in detail
pg.goto('http://localhost:8080/about-us/', wait_until='networkidle', timeout=15000)
time.sleep(3)
url = pg.url
title = pg.title()
h1_all = pg.evaluate('([...document.querySelectorAll("h1")]).map(h => ({tag: h.tagName, text: h.textContent.trim().substring(0,60), cls: h.className, vis: h.offsetParent !== null}))')
sr = pg.evaluate('([...document.querySelectorAll(".sr-only")]).map(e => e.outerHTML.substring(0,150))')
print('URL:', url)
print('TITLE:', title)
print('ALL H1s:', h1_all)
print('SR-ONLY elements:', sr[:3])

# Check contact page
pg.goto('http://localhost:8080/contact-us/', wait_until='networkidle', timeout=15000)
time.sleep(2)
h1_c = pg.evaluate('([...document.querySelectorAll("h1")]).map(h => ({tag: h.tagName, text: h.textContent.trim().substring(0,60), cls: h.className}))')
print()
print('CONTACT H1s:', h1_c)

b.close()
p.stop()
