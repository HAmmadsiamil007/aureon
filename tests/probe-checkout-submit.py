"""Instrument checkout submit: capture wc-ajax/admin-ajax traffic + post-submit DOM state."""
from playwright.sync_api import sync_playwright
from pathlib import Path

OUT = Path("test-results")
BASE = "http://localhost:8080"

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    page = browser.new_context(viewport={"width": 1440, "height": 900}).new_page()

    traffic = []
    def on_response(resp):
        u = resp.url
        if any(s in u for s in ("wc-ajax", "admin-ajax", "checkout")):
            try:
                body = resp.text()[:600]
            except Exception:
                body = "<no body>"
            traffic.append((resp.status, u, body))
    page.on("response", on_response)

    page.goto(f"{BASE}/?add-to-cart=577", wait_until="networkidle")
    page.goto(f"{BASE}/checkout", wait_until="networkidle")
    page.wait_for_selector("form.checkout", timeout=30000)

    for name, val in {
        "billing_first_name": "QA", "billing_last_name": "Tester",
        "billing_address_1": "123 Test Street", "billing_city": "Lahore",
        "billing_postcode": "54000", "billing_phone": "03001234567",
        "billing_email": "qa-tester@example.com",
    }.items():
        loc = page.locator(f"[name='{name}']")
        if loc.count():
            loc.first.fill(val)
    if page.locator("#billing_country").count():
        page.locator("#billing_country").select_option(label="Pakistan")

    # Diagnose silent HTML5 constraint blocking before clicking
    validity = page.evaluate("""() => {
        const form = document.querySelector('form.checkout');
        if (!form) return {form: false};
        const bad = [];
        for (const el of form.querySelectorAll('input, select, textarea')) {
            if (el.willValidate && !el.checkValidity()) {
                bad.push({name: el.name || el.id, msg: el.validationMessage,
                          visible: el.offsetParent !== null, disabled: el.disabled});
            }
        }
        return {form: true, valid: form.checkValidity(), invalid: bad};
    }""")
    print('FORM VALIDITY:', validity)

    page.locator("button[name='woocommerce_checkout_place_order'], #place_order").first.click()
    page.wait_for_timeout(8000)

    print("=== CAPTURED CHECKOUT TRAFFIC ===")
    for status, u, body in traffic:
        print(status, u)
        print("   body:", body.replace("\n", " ")[:400])
        print()

    print("FINAL URL:", page.url)
    notices = page.evaluate("""() => ({
        noticeGroup: (document.querySelector('.woocommerce-NoticeGroup-checkout, .woocommerce-NoticeGroup')||{}).innerText || null,
        errors: [...document.querySelectorAll('.woocommerce-error')].map(e=>e.textContent.trim().slice(0,200)),
        ajaxBlocked: !!document.querySelector('.blockUI')
    })""")
    for k, v in notices.items():
        print(k, ":", v)
    browser.close()
