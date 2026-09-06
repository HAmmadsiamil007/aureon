"""Deep checkout-JS probe: is WC checkout JS active, and what happens on submit?"""
from playwright.sync_api import sync_playwright

BASE = "http://localhost:8080"

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    page = browser.new_context(viewport={"width": 1440, "height": 900}).new_page()

    msgs = []
    page.on("console", lambda m: msgs.append(f"{m.type}: {m.text[:200]}"))

    page.goto(f"{BASE}/?add-to-cart=577", wait_until="networkidle")
    page.goto(f"{BASE}/checkout", wait_until="networkidle")
    page.wait_for_selector("form.checkout", timeout=30000)

    state = page.evaluate("""() => ({
        wcParams: typeof wc_checkout_params !== 'undefined' ? wc_checkout_params.wc_ajax_url.toString().slice(0,60) : 'UNDEFINED',
        jQueryActive: typeof jQuery !== 'undefined',
        formHasSubmitListener: typeof jQuery !== 'undefined'
            ? !!jQuery._data(document.querySelector('form.checkout'), 'events')
            : 'no jquery data',
        bodyEvents: typeof jQuery !== 'undefined'
            ? Object.keys((jQuery._data(document.body, 'events') || {})).filter(e => /submit|checkout|place/i.test(e))
            : [],
        formId: (document.querySelector('form.checkout')||{}).id || 'no-form-id',
        method: (document.querySelector('form.checkout')||{}).method,
        novalidate: (document.querySelector('form.checkout')||{}).noValidate
    })""")
    for k, v in state.items():
        print(k, ":", v)

    # Try a programmatic requestSubmit (bypasses click-handler quirks)
    result = page.evaluate("""() => {
        const form = document.querySelector('form.checkout');
        try { form.requestSubmit ? form.requestSubmit() : form.submit(); return 'submitted'; }
        catch (e) { return 'error: ' + e.message; }
    }""")
    print("SUBMIT CALL:", result)
    page.wait_for_timeout(8000)
    print("FINAL URL:", page.url)
    print("CONSOLE ({})".format(len(msgs)))
    for m in msgs[-8:]:
        print("  ", m)
    browser.close()
