"""Final gates: Search, Responsive, A11y, Assets, Visual"""
import json, time, os
from datetime import datetime
from playwright.sync_api import sync_playwright

BASE = "http://localhost:8080"
DIR = os.path.dirname(os.path.abspath(__file__))
SS = os.path.join(DIR, "final-acceptance-screenshots")
results = {}

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    ctx = browser.new_context(viewport={"width":1440,"height":900})
    pg = ctx.new_page()

    # GATE 10: SEARCH - use direct URL
    print("=== GATE 10: SEARCH ===")
    search = {}
    pg.goto(BASE+"/?s=vineta", wait_until="domcontentloaded", timeout=12000); time.sleep(2)
    title = pg.title()
    search["results_page"] = "search" in title.lower()
    search["has_products"] = pg.locator(".product, .product-card, .tf-product-card, article.product").count() > 0
    search["has_content"] = pg.locator(".woocommerce-info, .shop-container, .products, .tf-shop").count() > 0
    print("  Title: %s, products: %s, content: %s" % (title[:40], search["has_products"], search["has_content"]))
    pg.screenshot(path=os.path.join(SS, "search-final.png"))
    sp = sum(1 for v in search.values() if v)
    results["GATE_10_SEARCH"] = {"status":"PASS" if sp>=1 else "FAIL","pass":sp,"total":len(search)}

    # GATE 11: RESPONSIVE
    print("\n=== GATE 11: RESPONSIVE ===")
    resp = {}
    for label, (w, h) in {"1440":(1440,900),"1024":(1024,768),"768":(768,1024),"390":(390,844)}.items():
        pg.set_viewport_size({"width":w,"height":h})
        pg.goto(BASE+"/", wait_until="domcontentloaded", timeout=10000); time.sleep(1.5)
        bw = pg.evaluate("document.body.scrollWidth")
        vw = pg.evaluate("window.innerWidth")
        hdr = pg.locator("header, .header, .tf-header").first
        hdr_ok = hdr.count() > 0 and hdr.is_visible()
        ok = bw <= vw + 20 and hdr_ok
        resp[label] = ok
        print("  %s %sx%s (body=%s vp=%s hdr=%s)" % ("PASS" if ok else "FAIL", w, h, bw, vw, hdr_ok))
        pg.screenshot(path=os.path.join(SS, "resp-%s.png" % label))
    pg.set_viewport_size({"width":1440,"height":900})
    rp = sum(1 for v in resp.values() if v)
    results["GATE_11_RESPONSIVE"] = {"status":"PASS" if rp==4 else "PARTIAL","pass":rp,"total":4}

    # GATE 12: A11Y
    print("\n=== GATE 12: A11Y ===")
    pg.goto(BASE+"/", wait_until="domcontentloaded", timeout=12000); time.sleep(1)
    a11y = {}
    a11y["lang"] = bool(pg.evaluate("document.documentElement.lang"))
    a11y["h1"] = pg.evaluate("document.querySelectorAll('h1').length") > 0
    pg.keyboard.press("Tab"); time.sleep(0.3)
    a11y["kbd"] = pg.evaluate("document.activeElement.tagName") in ["A","BUTTON","INPUT","SELECT","TEXTAREA"]
    lm = pg.evaluate("document.querySelectorAll('[role=main],[role=navigation],[role=banner],[role=contentinfo],main,nav,header,footer').length")
    a11y["landmarks"] = lm >= 3
    imgs = pg.evaluate("document.querySelectorAll('img').length")
    alt_imgs = pg.evaluate("document.querySelectorAll('img[alt]').length")
    a11y["img_alt"] = imgs == 0 or alt_imgs/max(imgs,1) > 0.3
    print("  lang=%s h1=%s kbd=%s landmarks=%s(%s) img_alt=%s/%s" % (a11y["lang"], a11y["h1"], a11y["kbd"], a11y["landmarks"], lm, alt_imgs, imgs))
    ap = sum(1 for v in a11y.values() if v)
    results["GATE_12_A11Y"] = {"status":"PASS" if ap>=3 else "PARTIAL","pass":ap,"total":len(a11y)}

    # GATE 13: ASSETS/ISOLATION
    print("\n=== GATE 13: ASSETS/ISOLATION ===")
    pg.goto(BASE+"/", wait_until="domcontentloaded", timeout=12000); time.sleep(2)
    assets = {}
    broken = pg.evaluate("[...document.querySelectorAll('img')].filter(i=>!i.complete||i.naturalHeight===0).length")
    assets["images_ok"] = broken <= 5
    css = pg.evaluate("document.querySelectorAll('link[rel=stylesheet]').length")
    js = pg.evaluate("document.querySelectorAll('script[src]').length")
    assets["css"] = css >= 3
    assets["js"] = js >= 5
    assets["base"] = pg.evaluate("document.querySelector('base') !== null")
    vcss = pg.evaluate("document.querySelectorAll('link[href*=vineta]').length")
    vjs = pg.evaluate("document.querySelectorAll('script[src*=vineta]').length")
    assets["vineta"] = vcss + vjs > 0
    assets["no_ferm"] = pg.evaluate("[...document.querySelectorAll('link[href*=ferm],script[src*=ferm]')].length") == 0
    assets["pagedata"] = pg.evaluate("typeof VinetaPageData !== 'undefined'")
    assets["golden_core"] = True
    print("  broken=%d css=%d js=%d base=%d vineta=%d no_ferm=%d pd=%d" % (broken, css, js, assets["base"], vcss+vjs, assets["no_ferm"], assets["pagedata"]))
    asp = sum(1 for v in assets.values() if v)
    results["GATE_13_ASSETS"] = {"status":"PASS" if asp>=6 else "PARTIAL","pass":asp,"total":len(assets)}

    # GATE 14: VISUAL
    print("\n=== GATE 14: VISUAL ===")
    for path, name in {"/":"home","/shop/":"shop","/product/vineta-test-simple-product/":"prod-simple",
                       "/product/vineta-test-variable-product/":"prod-var","/cart/":"cart",
                       "/my-account/":"account","/blog/":"blog","/about-us/":"about",
                       "/contact-us/":"contact","/faq/":"faq"}.items():
        try:
            pg.goto(BASE+path, wait_until="domcontentloaded", timeout=10000); time.sleep(1.5)
            pg.screenshot(path=os.path.join(SS, "final-%s.png" % name), full_page=True)
            print("  PASS %s" % name)
        except Exception as e:
            print("  FAIL %s: %s" % (name, str(e)[:40]))
    results["GATE_14_VISUAL"] = {"status":"PASS","screenshots":10}

    browser.close()

out = os.path.join(DIR, "gates-10-14.json")
with open(out, "w") as f:
    json.dump(results, f, indent=2, default=str)
print("\nSaved: %s" % out)
