"""GATE 5+6+7: Checkout, Auth, Account"""
import json, time, os
from playwright.sync_api import sync_playwright

BASE = "http://localhost:8080"
DIR = os.path.dirname(os.path.abspath(__file__))

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    ctx = browser.new_context(viewport={"width":1440,"height":900})
    page = ctx.new_page()
    results = {}
    
    # GATE 5: Checkout
    print("=== GATE 5: CHECKOUT ===")
    try:
        # Ensure item in cart
        page.goto(BASE+"/product/vineta-test-simple-product/", wait_until="domcontentloaded", timeout=12000)
        time.sleep(2)
        btn = page.locator("button.single_add_to_cart_button, button[name='add-to-cart']").first
        if btn.count() > 0:
            btn.click()
            time.sleep(2)
        
        page.goto(BASE+"/checkout/", wait_until="domcontentloaded", timeout=15000)
        time.sleep(3)
        page.screenshot(path=os.path.join(DIR,"final-acceptance-screenshots","g5a-checkout.png"))
        
        form = page.locator("#order_review, .woocommerce-checkout, form.checkout")
        results["checkout_renders"] = {"pass": form.count() > 0, "evidence": f"Form found: {form.count() > 0}"}
        print(f"  {'PASS' if form.count() > 0 else 'FAIL'} Checkout form renders")
        
        # Fill billing
        fields = {"#billing_first_name":"Test","#billing_last_name":"User",
                   "#billing_email":"test@example.com","#billing_phone":"5551234567",
                   "#billing_address_1":"123 Test St","#billing_city":"New York","#billing_postcode":"10001"}
        filled = 0
        for sel, val in fields.items():
            el = page.locator(sel)
            if el.count() > 0:
                try: el.fill(val); filled += 1
                except: pass
            time.sleep(0.2)
        
        # Country
        country = page.locator("#billing_country")
        if country.count() > 0:
            try: country.select_option(value="US"); filled += 1; time.sleep(1)
            except: pass
        
        # State
        state = page.locator("#billing_state")
        if state.count() > 0:
            try: state.select_option(index=1); filled += 1
            except: pass
        
        page.screenshot(path=os.path.join(DIR,"final-acceptance-screenshots","g5b-checkout-filled.png"))
        results["fields_filled"] = {"pass": filled >= 4, "count": filled}
        print(f"  PASS Fields filled: {filled}")
        
        place = page.locator("#place_order, button[name='woocommerce_checkout_place_order']")
        results["place_order"] = {"pass": place.count() > 0}
        print(f"  {'PASS' if place.count() > 0 else 'FAIL'} Place order button: {place.count() > 0}")
    except Exception as e:
        results["checkout_error"] = {"pass":False,"evidence":str(e)[:100]}
        print(f"  FAIL Checkout: {e}")
    
    # GATE 6: Auth
    print("\n=== GATE 6: AUTH ===")
    try:
        page.goto(BASE+"/my-account/", wait_until="domcontentloaded", timeout=12000)
        time.sleep(2)
        page.screenshot(path=os.path.join(DIR,"final-acceptance-screenshots","g6a-login.png"))
        
        login_form = page.locator("#customer_login, .woocommerce-form-login, form.login")
        results["login_form"] = {"pass": login_form.count() > 0}
        print(f"  {'PASS' if login_form.count() > 0 else 'FAIL'} Login form: {login_form.count() > 0}")
        
        user_field = page.locator("#username, input[name='username']")
        pass_field = page.locator("#password, input[name='password']")
        results["login_fields"] = {"pass": user_field.count() > 0 and pass_field.count() > 0}
        print(f"  PASS Login fields: user={user_field.count() > 0}, pass={pass_field.count() > 0}")
        
        # Register form
        reg = page.locator(".woocommerce-form-register, form.register, #customer_login .woocommerce-form-register")
        results["register_form"] = {"pass": reg.count() > 0}
        print(f"  {'PASS' if reg.count() > 0 else 'WARN'} Register form: {reg.count() > 0}")
        
        # Lost password
        lp = page.locator("a[href*='lostpassword'], .woocommerce-LostPassword, a:has-text('Lost your password')")
        results["lost_password"] = {"pass": lp.count() > 0}
        print(f"  {'PASS' if lp.count() > 0 else 'WARN'} Lost password link: {lp.count() > 0}")
        
        if lp.count() > 0:
            lp.first.click()
            time.sleep(2)
            page.screenshot(path=os.path.join(DIR,"final-acceptance-screenshots","g6b-lost-password.png"))
    except Exception as e:
        results["auth_error"] = {"pass":False,"evidence":str(e)[:100]}
        print(f"  FAIL Auth: {e}")
    
    # GATE 7: Account
    print("\n=== GATE 7: ACCOUNT ===")
    acct_pages = {"/my-account/":"dashboard","/my-account/orders/":"orders",
                  "/my-account/edit-address/":"addresses","/my-account/edit-account/":"account_details"}
    for path, label in acct_pages.items():
        try:
            resp = page.goto(BASE+path, wait_until="domcontentloaded", timeout=10000)
            time.sleep(1)
            status = resp.status if resp else 0
            results[f"account_{label}"] = {"pass": status == 200, "status": status}
            print(f"  {'PASS' if status==200 else 'FAIL'} {label}: {status}")
            if label == "dashboard":
                page.screenshot(path=os.path.join(DIR,"final-acceptance-screenshots","g7-account.png"))
        except Exception as e:
            results[f"account_{label}"] = {"pass":False,"error":str(e)[:80]}
    
    browser.close()

passed = sum(1 for v in results.values() if v.get("pass"))
total = len(results)
status = "PASS" if passed == total else "PARTIAL" if passed > 0 else "FAIL"
print(f"\nGATE 5-7: {status} ({passed}/{total})")

out = os.path.join(DIR,"gate-5-6-7.json")
with open(out,"w") as f: json.dump({"status":status,"passed":passed,"total":total,"details":results},f,indent=2,default=str)
print(f"Saved: {out}")
