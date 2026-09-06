"""Probe: what happens to the social list after the bridge hydrates global.social."""
from playwright.sync_api import sync_playwright

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    page = browser.new_page(viewport={"width": 1440, "height": 900})
    page.goto("http://localhost:8080/", wait_until="networkidle")
    page.wait_for_timeout(1500)

    info = page.evaluate("""() => {
        const lists = [...document.querySelectorAll('[data-aureon-slot="global.social"]')];
        const topbar = document.querySelector('.topbar-wraper');
        const snap = document.querySelector('.social-snapchat');
        return {
            socialSlotCount:   lists.length,
            topbarInDom:       !!topbar,
            topbarVisible:     topbar ? getComputedStyle(topbar).display !== 'none' : false,
            topbarDisplay:     topbar ? getComputedStyle(topbar).display : 'n/a',
            topbarHeight:      topbar ? topbar.getBoundingClientRect().height : 0,
            snapchatInDom:     !!snap,
            socialHtmlSample:  lists.length ? lists[0].outerHTML.slice(0, 600) : 'none'
        };
    }""")

    for k, v in info.items():
        print(f"{k}: {v}")
