"""Classify all broken images across the site"""
from playwright.sync_api import sync_playwright
import time, json, os

BASE = "http://localhost:8080"
DIR = "C:/Users/hamma/Downloads/phantom/wordpress/test-results"
PACK = "C:/Users/hamma/Downloads/phantom/wordpress/aureon/frontend/designs/vineta"

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    ctx = browser.new_context(viewport={"width": 1440, "height": 900})
    pg = ctx.new_page()

    all_broken = {}
    
    pages_to_check = [
        ("/", "homepage"),
        ("/shop/", "shop"),
        ("/product/vineta-test-simple-product/", "product-simple"),
        ("/product/vineta-test-variable-product/", "product-variable"),
        ("/cart/", "cart"),
        ("/my-account/", "account"),
        ("/blog/", "blog"),
        ("/about-us/", "about"),
        ("/contact-us/", "contact"),
        ("/faq/", "faq"),
    ]
    
    for path, name in pages_to_check:
        pg.goto(BASE + path, wait_until="domcontentloaded", timeout=12000)
        time.sleep(2)
        
        broken = pg.evaluate("""[...document.querySelectorAll('img')].filter(i => !i.complete || i.naturalHeight === 0).map(i => {
            var section = i.closest('section, .hero, .banner, .product-card, .category, footer, .flat-banner, .banner-group');
            return {
                src: i.src,
                alt: (i.alt || '').substring(0, 40),
                parentTag: i.parentElement ? i.parentElement.tagName : '',
                parentCls: (i.parentElement ? i.parentElement.className : '').substring(0, 40),
                sectionCls: section ? section.className.substring(0, 50) : 'none',
                visible: i.offsetParent !== null,
                loading: i.loading || '',
                dataSrc: (i.dataset && i.dataset.src) || '',
                width: i.naturalWidth,
                height: i.naturalHeight
            };
        })""")
        
        if broken:
            all_broken[name] = broken
            print("[%s] %d broken images" % (name, len(broken)))
    
    browser.close()

# Classify by pattern
print("\n=== CLASSIFICATION ===")
all_srcs = []
for page, imgs in all_broken.items():
    for img in imgs:
        src = img["src"].replace("http://localhost:8080/wp-content/frontend/designs/vineta/", "")
        all_srcs.append({
            "src": src,
            "page": page,
            "alt": img["alt"],
            "section": img["sectionCls"],
            "visible": img["visible"],
            "parentTag": img["parentTag"],
            "parentCls": img["parentCls"],
        })

# Group by path pattern
patterns = {}
for item in all_srcs:
    parts = item["src"].split("/")
    if len(parts) >= 2:
        pattern = "/".join(parts[:3])
    else:
        pattern = item["src"]
    if pattern not in patterns:
        patterns[pattern] = []
    patterns[pattern].append(item)

print("\nBroken image groups:")
for pat, items in sorted(patterns.items(), key=lambda x: -len(x[1])):
    print("\n  %s (%d instances)" % (pat, len(items)))
    # Check if any are visible
    visible = [i for i in items if i["visible"]]
    print("    Visible: %d, Hidden: %d" % (len(visible), len(items) - len(visible)))
    # Show which pages
    page_set = set(i["page"] for i in items)
    print("    Pages: %s" % ", ".join(sorted(page_set)))
    # Show first 3 srcs
    for i in items[:3]:
        print("    - %s (alt='%s', section=%s)" % (i["src"][-60:], i["alt"][:25], i["section"][:30]))

# Check if files exist on disk
print("\n=== FILE EXISTENCE CHECK ===")
for pat in sorted(patterns.keys()):
    # Construct possible file path
    test_path = os.path.join(PACK, pat)
    # Try glob
    import glob
    matches = glob.glob(test_path + "*")
    exists = len(matches) > 0
    print("  %s -> exists=%s" % (pat, exists))
    if not exists:
        # Check parent dir
        parent = os.path.dirname(test_path)
        parent_exists = os.path.isdir(parent)
        print("    parent dir exists: %s (%s)" % (parent_exists, parent))

# Save
with open(os.path.join(DIR, "broken-images-classified.json"), "w") as f:
    json.dump({"patterns": {k: len(v) for k, v in patterns.items()}, "all": all_srcs}, f, indent=2)
print("\nSaved to broken-images-classified.json")
