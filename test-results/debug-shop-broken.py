from playwright.sync_api import sync_playwright
import time
BASE = 'http://localhost:8080'
with sync_playwright() as p:
    b = p.chromium.launch(headless=True)
    pg = b.new_page()
    pg.goto(BASE+'/shop/', wait_until='domcontentloaded', timeout=12000)
    time.sleep(2)
    broken = pg.evaluate("""([...document.querySelectorAll('img')]).filter(i => !i.complete || i.naturalHeight === 0).map(i => {
        var section = i.closest('section, .hero, .banner, footer');
        return {
            src: i.src.replace('http://localhost:8080/wp-content/frontend/designs/vineta/', ''),
            alt: (i.alt || '').substring(0, 40),
            section: section ? section.className.substring(0, 50) : 'none',
            parentCls: (i.parentElement ? i.parentElement.className : '').substring(0, 40)
        };
    })""")
    for img in broken:
        print("BROKEN: %s (alt='%s', section=%s)" % (img['src'][-70:], img['alt'], img['section'][:30]))
    b.close()
