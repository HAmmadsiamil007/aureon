"""Investigate H1, broken images, plugins - simplified"""
from playwright.sync_api import sync_playwright
import time, json

BASE = "http://localhost:8080"
DIR = "C:/Users/hamma/Downloads/phantom/wordpress/test-results"

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    ctx = browser.new_context(viewport={"width":1440,"height":900})
    pg = ctx.new_page()

    # === H1 ===
    print("=== H1 INVESTIGATION ===")
    pages = ["/","/shop/","/product/vineta-test-simple-product/","/product/vineta-test-variable-product/",
             "/cart/","/my-account/","/blog/","/about-us/","/contact-us/","/faq/"]
    h1_data = {}
    for path in pages:
        pg.goto(BASE+path, wait_until="domcontentloaded", timeout=12000)
        time.sleep(1.5)
        info = pg.evaluate("""(() => {
            const hs = [...document.querySelectorAll('h1,h2,h3,h4')].slice(0,6);
            return {
                h1Count: document.querySelectorAll('h1').length,
                headings: hs.map(h => h.tagName + ': ' + h.textContent.trim().substring(0,50))
            };
        })()""")
        h1_data[path] = info
        print("  %s H1=%s" % (path, info["h1Count"]))
        for h in info["headings"][:3]:
            print("    %s" % h)
    
    # === BROKEN IMAGES - all pages ===
    print("\n=== BROKEN IMAGES ===")
    all_broken = {}
    for path in ["/", "/shop/", "/product/vineta-test-simple-product/", "/product/vineta-test-variable-product/", "/cart/"]:
        pg.goto(BASE+path, wait_until="domcontentloaded", timeout=12000)
        time.sleep(1.5)
        imgs = pg.evaluate("""[...document.querySelectorAll('img')].filter(i => !i.complete || i.naturalHeight === 0).map(i => i.src)""")
        if imgs:
            all_broken[path] = imgs
            print("  %s: %d broken" % (path, len(imgs)))
    
    # Get ALL image srcs on homepage
    pg.goto(BASE+"/", wait_until="domcontentloaded", timeout=12000)
    time.sleep(2)
    all_imgs = pg.evaluate("""[...document.querySelectorAll('img')].map(i => ({src: i.src, alt: i.alt||'', loading: i.loading||''}))""")
    print("\n  Total images on homepage: %d" % len(all_imgs))
    
    # Classify broken by path pattern
    broken_srcs = all_broken.get("/", [])
    patterns = {}
    for src in broken_srcs:
        short = src.replace("http://localhost:8080/wp-content/frontend/designs/vineta/", "")
        parts = short.split("/")
        pattern = "/".join(parts[:3]) if len(parts) >= 3 else short
        patterns[pattern] = patterns.get(pattern, 0) + 1
    
    print("\n  Broken image patterns:")
    for pat, count in sorted(patterns.items(), key=lambda x: -x[1]):
        print("    %s: %d" % (pat, count))
    
    # === PLUGINS ===
    print("\n=== PLUGINS ===")
    pg.goto(BASE+"/", wait_until="domcontentloaded", timeout=10000)
    time.sleep(1)
    plugins = pg.evaluate("""(() => {
        const r = {};
        r.wc = typeof wc_add_to_cart_params !== 'undefined';
        r.wc_blocks = typeof wc !== 'undefined' && typeof wc.blocks !== 'undefined';
        r.wp = typeof wp !== 'undefined';
        r.jQuery = typeof jQuery !== 'undefined';
        // Check for plugin scripts
        const scripts = [...document.querySelectorAll('script[src]')].map(s => s.src);
        r.yoast = scripts.some(s => s.includes('yoast'));
        r.elementor = scripts.some(s => s.includes('elementor'));
        r.cf7 = scripts.some(s => s.includes('contact-form'));
        r.mailchimp = scripts.some(s => s.includes('mc4wp') || s.includes('mailchimp'));
        r.analytics = scripts.some(s => s.includes('google-analytics') || s.includes('gtag') || s.includes('gtm'));
        r.tawk = scripts.some(s => s.includes('tawk'));
        r.freshdesk = scripts.some(s => s.includes('fresh'));
        r.caching = scripts.some(s => s.includes('wp-rocket') || s.includes('w3-total') || s.includes('litespeed'));
        r.seo = scripts.some(s => s.includes('yoast') || s.includes('rank-math') || s.includes('all-in-one-seo'));
        r.lang = document.documentElement.lang;
        // WC API check
        r.wc_cart_hash = document.querySelector('input[name="woocommerce-cart-hash"]') !== null;
        r.wc_meta = document.querySelectorAll('meta[property="product:price"]').length;
        return r;
    })()""")
    print("  %s" % json.dumps(plugins, indent=4))
    
    # Save
    with open(DIR+"/hardening-data.json", "w") as f:
        json.dump({"h1": h1_data, "broken_by_page": {k: len(v) for k,v in all_broken.items()}, "broken_srcs": broken_srcs[:30], "plugins": plugins}, f, indent=2, default=str)
    
    browser.close()
    print("\nDone")
