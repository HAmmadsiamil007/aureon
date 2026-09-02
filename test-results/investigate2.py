"""Investigate buttons, broken images, headings"""
from playwright.sync_api import sync_playwright
import time

with sync_playwright() as p:
    b = p.chromium.launch(headless=True)
    pg = b.new_page(viewport={"width":1440,"height":900})
    
    # Variable product
    pg.goto("http://localhost:8080/product/vineta-test-variable-product/", wait_until="domcontentloaded", timeout=12000)
    time.sleep(2)
    
    btns = pg.evaluate("""[...document.querySelectorAll('button, a.btn, [type=submit]')].map(b => ({
        tag: b.tagName, text: b.textContent.trim().substring(0,40), cls: b.className.substring(0,60), type: b.type
    }))""")
    print("=== BUTTONS ===")
    for btn in btns[:15]:
        print("  %s cls=%s type=%s text=%s" % (btn["tag"], btn["cls"][:40], btn.get("type",""), btn["text"][:30]))
    
    broken = pg.evaluate("""[...document.querySelectorAll('img')].filter(i => !i.complete || i.naturalHeight === 0).map(i => ({
        src: i.src.substring(0,120), alt: i.alt.substring(0,30)
    }))""")
    print("\n=== BROKEN IMAGES ===")
    for img in broken[:10]:
        print("  %s alt=%s" % (img["src"][:100], img["alt"]))
    print("  Total broken: %d" % len(broken))
    
    # Homepage headings
    pg.goto("http://localhost:8080/", wait_until="domcontentloaded", timeout=12000)
    time.sleep(1)
    headings = pg.evaluate("""[...document.querySelectorAll('h1,h2,h3,h4,h5,h6')].slice(0,15).map(h => ({
        tag: h.tagName, text: h.textContent.trim().substring(0,50)
    }))""")
    print("\n=== HEADINGS ===")
    for h in headings:
        print("  %s: %s" % (h["tag"], h["text"]))
    
    # Check nav menu items
    print("\n=== NAV ITEMS ===")
    nav_items = pg.evaluate("""[...document.querySelectorAll('nav a, .main-menu a, .tf-megamenu a')].slice(0,20).map(a => ({
        text: a.textContent.trim().substring(0,30), href: a.href.substring(0,80)
    }))""")
    for item in nav_items[:15]:
        print("  %s -> %s" % (item["text"], item["href"][:60]))
    
    b.close()
    print("\nDone")
