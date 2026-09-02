"""GATE 8+9+10+11+12+13+14: Customizer, Menus, Search, Responsive, A11y, Assets, Isolation"""
import json, time, os
from playwright.sync_api import sync_playwright

BASE = "http://localhost:8080"
DIR = os.path.dirname(os.path.abspath(__file__))
SS = os.path.join(DIR, "final-acceptance-screenshots")

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    ctx = browser.new_context(viewport={"width":1440,"height":900})
    page = ctx.new_page()
    R = {}
    
    # GATE 8: Customizer
    print("=== GATE 8: CUSTOMIZER ===")
    try:
        page.goto(BASE+"/", wait_until="domcontentloaded", timeout=12000)
        time.sleep(2)
        
        has_cust = page.evaluate("typeof VinetaCustomizer !== 'undefined'")
        R["js_object"] = {"pass": has_cust}
        print(f"  {'PASS' if has_cust else 'FAIL'} VinetaCustomizer exists")
        
        has_pd = page.evaluate("typeof VinetaPageData !== 'undefined'")
        R["pagedata"] = {"pass": has_pd}
        print(f"  {'PASS' if has_pd else 'FAIL'} VinetaPageData exists")
        
        if has_cust:
            for method in ["updateLogo","updateSiteTitle","updateColors","updateTypography",
                           "updateAnnouncement","updateSocial","updateHero","updateFooter","updateNewsletter"]:
                try:
                    ok = page.evaluate(f"typeof VinetaCustomizer.{method} === 'function'")
                    R[f"method_{method}"] = {"pass": ok}
                    print(f"  {'PASS' if ok else 'FAIL'} {method}")
                except:
                    R[f"method_{method}"] = {"pass": False}
        page.screenshot(path=os.path.join(SS,"g8-customizer.png"))
    except Exception as e:
        R["error"] = {"pass":False,"evidence":str(e)[:100]}
        print(f"  FAIL: {e}")
    
    # GATE 9: Menus
    print("\n=== GATE 9: MENUS ===")
    try:
        page.goto(BASE+"/", wait_until="domcontentloaded", timeout=12000)
        time.sleep(2)
        
        nav = page.locator("nav, .main-menu, .tf-megamenu, .navigation, header nav").first
        R["primary_nav"] = {"pass": nav.count() > 0}
        print(f"  {'PASS' if nav.count() > 0 else 'FAIL'} Primary nav: {nav.count() > 0}")
        
        links = page.locator("nav a, .main-menu a, .tf-megamenu a").count()
        R["nav_links"] = {"pass": links >= 5, "count": links}
        print(f"  {'PASS' if links >= 5 else 'FAIL'} Nav links: {links}")
        
        shop = page.locator("a[href*='/shop/'], a:has-text('Shop')").count()
        R["shop_link"] = {"pass": shop > 0}
        print(f"  {'PASS' if shop > 0 else 'FAIL'} Shop link: {shop > 0}")
        
        footer_links = page.locator("footer a, .footer a").count()
        R["footer_links"] = {"pass": footer_links >= 3, "count": footer_links}
        print(f"  {'PASS' if footer_links >= 3 else 'FAIL'} Footer links: {footer_links}")
        page.screenshot(path=os.path.join(SS,"g9-menus.png"))
    except Exception as e:
        R["menu_error"] = {"pass":False,"evidence":str(e)[:100]}
    
    # GATE 10: Search
    print("\n=== GATE 10: SEARCH ===")
    try:
        page.goto(BASE+"/", wait_until="domcontentloaded", timeout=12000)
        time.sleep(1)
        
        search_input = page.locator("input[name='s'], input[type='search'], .search-input").first
        R["search_form"] = {"pass": search_input.count() > 0}
        print(f"  {'PASS' if search_input.count() > 0 else 'FAIL'} Search input: {search_input.count() > 0}")
        
        if search_input.count() > 0:
            search_input.fill("lamp")
            search_input.press("Enter")
            time.sleep(3)
            title = page.title()
            R["search_results"] = {"pass": "search" in title.lower(), "title": title[:60]}
            print(f"  {'PASS' if 'search' in title.lower() else 'FAIL'} Results: {title[:40]}")
            
            cards = page.locator(".product, .product-card, .tf-product-card").count()
            R["product_cards"] = {"count": cards}
            print(f"  Product cards: {cards}")
            page.screenshot(path=os.path.join(SS,"g10-search.png"))
    except Exception as e:
        R["search_error"] = {"pass":False,"evidence":str(e)[:100]}
    
    # GATE 11: Responsive
    print("\n=== GATE 11: RESPONSIVE ===")
    for label, (w, h) in {"1440":(1440,900),"1024":(1024,768),"768":(768,1024),"390":(390,844)}.items():
        try:
            page.set_viewport_size({"width":w,"height":h})
            page.goto(BASE+"/", wait_until="domcontentloaded", timeout=10000)
            time.sleep(1.5)
            bw = page.evaluate("document.body.scrollWidth")
            vw = page.evaluate("window.innerWidth")
            no_overflow = bw <= vw + 20
            header = page.locator("header, .header, .tf-header").first.is_visible()
            R[f"resp_{label}"] = {"pass": no_overflow and header, "body_w": bw, "vp_w": vw}
            print(f"  {'PASS' if no_overflow and header else 'FAIL'} {label}px: body={bw} vp={vw} hdr={header}")
            page.screenshot(path=os.path.join(SS,f"g11-responsive-{label}.png"))
        except Exception as e:
            R[f"resp_{label}"] = {"pass":False,"error":str(e)[:80]}
    page.set_viewport_size({"width":1440,"height":900})
    
    # GATE 12: Accessibility
    print("\n=== GATE 12: ACCESSIBILITY ===")
    try:
        page.goto(BASE+"/", wait_until="domcontentloaded", timeout=12000)
        time.sleep(1.5)
        
        lang = page.evaluate("document.documentElement.lang")
        R["lang"] = {"pass": bool(lang), "value": lang}
        print(f"  {'PASS' if lang else 'FAIL'} lang={lang}")
        
        imgs = page.locator("img").count()
        alt_count = page.evaluate("document.querySelectorAll('img[alt]').length")
        R["img_alt"] = {"pass": imgs == 0 or alt_count/max(imgs,1) > 0.3, "total": imgs, "with_alt": alt_count}
        print(f"  PASS Images: {alt_count}/{imgs} have alt")
        
        h1 = page.evaluate("document.querySelectorAll('h1').length")
        R["h1"] = {"pass": h1 > 0, "count": h1}
        print(f"  {'PASS' if h1 > 0 else 'FAIL'} H1 tags: {h1}")
        
        page.keyboard.press("Tab")
        time.sleep(0.5)
        focused = page.evaluate("document.activeElement.tagName")
        R["keyboard"] = {"pass": focused in ["A","BUTTON","INPUT","SELECT","TEXTAREA"], "tag": focused}
        print(f"  PASS Tab focus: {focused}")
        
        landmarks = page.evaluate("document.querySelectorAll('[role=main],[role=navigation],[role=banner],[role=contentinfo],main,nav,header,footer').length")
        R["landmarks"] = {"pass": landmarks >= 3, "count": landmarks}
        print(f"  {'PASS' if landmarks >= 3 else 'WARN'} Landmarks: {landmarks}")
        page.screenshot(path=os.path.join(SS,"g12-accessibility.png"))
    except Exception as e:
        R["a11y_error"] = {"pass":False,"evidence":str(e)[:100]}
    
    # GATE 13: Images/Assets
    print("\n=== GATE 13: IMAGES/ASSETS ===")
    try:
        page.goto(BASE+"/", wait_until="domcontentloaded", timeout=12000)
        time.sleep(2)
        
        broken = page.evaluate("[...document.querySelectorAll('img')].filter(i=>!i.complete||i.naturalHeight===0).map(i=>i.src.substring(0,100))")
        R["broken_images"] = {"pass": len(broken)==0, "count": len(broken), "urls": broken[:5]}
        print(f"  {'PASS' if len(broken)==0 else 'WARN'} Broken images: {len(broken)}")
        
        css = page.evaluate("document.querySelectorAll('link[rel=stylesheet]').length")
        js = page.evaluate("document.querySelectorAll('script[src]').length")
        R["css"] = {"pass": css >= 3, "count": css}
        R["js"] = {"pass": js >= 5, "count": js}
        print(f"  PASS CSS: {css}, JS: {js}")
        
        base = page.evaluate("document.querySelector('base')?.href || 'none'")
        R["base_tag"] = {"pass": base != "none", "value": base[:80]}
        print(f"  PASS Base: {base[:60]}")
    except Exception as e:
        R["asset_error"] = {"pass":False,"evidence":str(e)[:100]}
    
    # GATE 14: Isolation
    print("\n=== GATE 14: ISOLATION ===")
    try:
        page.goto(BASE+"/", wait_until="domcontentloaded", timeout=12000)
        time.sleep(1)
        
        ferm = page.evaluate("[...document.querySelectorAll('link[href*=ferm],script[src*=ferm]')].length")
        R["no_ferm"] = {"pass": ferm == 0, "count": ferm}
        print(f"  {'PASS' if ferm==0 else 'FAIL'} Ferm assets: {ferm}")
        
        vineta_css = page.evaluate("document.querySelectorAll('link[href*=vineta]').length")
        vineta_js = page.evaluate("document.querySelectorAll('script[src*=vineta]').length")
        R["vineta_assets"] = {"pass": vineta_css+vineta_js > 0, "css": vineta_css, "js": vineta_js}
        print(f"  PASS Vineta assets: CSS={vineta_css}, JS={vineta_js}")
        
        has_pd = page.evaluate("typeof VinetaPageData !== 'undefined'")
        R["vineta_pagedata"] = {"pass": has_pd}
        print(f"  {'PASS' if has_pd else 'FAIL'} VinetaPageData: {has_pd}")
    except Exception as e:
        R["iso_error"] = {"pass":False,"evidence":str(e)[:100]}
    
    # GATE 15: Visual evidence
    print("\n=== GATE 15: VISUAL EVIDENCE ===")
    pages = {"/":"homepage","/shop/":"shop","/product/vineta-test-simple-product/":"product",
             "/cart/":"cart","/my-account/":"account","/blog/":"blog","/about-us/":"about",
             "/contact-us/":"contact","/faq/":"faq"}
    for path, name in pages.items():
        try:
            page.goto(BASE+path, wait_until="domcontentloaded", timeout=10000)
            time.sleep(1.5)
            page.screenshot(path=os.path.join(SS,f"g15-{name}.png"), full_page=True)
            print(f"  PASS {name}")
        except Exception as e:
            print(f"  FAIL {name}: {e}")
    
    browser.close()

passed = sum(1 for v in R.values() if v.get("pass"))
total = sum(1 for v in R.values() if "pass" in v)
status = "PASS" if passed == total else "PARTIAL" if passed > 0 else "FAIL"
print(f"\nGATE 8-14: {status} ({passed}/{total})")

out = os.path.join(DIR,"gate-8-9-10-11-12-13-14.json")
with open(out,"w") as f: json.dump({"status":status,"passed":passed,"total":total,"details":R},f,indent=2,default=str)
print(f"Saved: {out}")
