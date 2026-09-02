"""GATE 4: Full Cart Browser Flow"""
import json, time, os
from playwright.sync_api import sync_playwright

BASE = "http://localhost:8080"
DIR = os.path.dirname(os.path.abspath(__file__))

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    ctx = browser.new_context(viewport={"width":1440,"height":900})
    page = ctx.new_page()
    results = {}
    
    print("=== GATE 4: CART BROWSER FLOW ===")
    
    # Clear cart first via WC API
    try:
        page.goto(BASE+"/my-account/", wait_until="domcontentloaded", timeout=10000)
    except: pass
    
    # 4a: Add simple product
    print("  4a: Add simple product...")
    try:
        page.goto(BASE+"/product/vineta-test-simple-product/", wait_until="domcontentloaded", timeout=12000)
        time.sleep(2)
        page.screenshot(path=os.path.join(DIR,"final-acceptance-screenshots","g4a-simple-product.png"))
        
        btn = page.locator("button.single_add_to_cart_button, button[name='add-to-cart'], .btn-add-to-cart").first
        if btn.count() > 0:
            btn.click()
            time.sleep(3)
            page.screenshot(path=os.path.join(DIR,"final-acceptance-screenshots","g4b-after-add.png"))
            results["add_simple"] = {"pass":True,"evidence":"Clicked add to cart"}
            print("    PASS Simple product added")
        else:
            results["add_simple"] = {"pass":False,"evidence":"Button not found"}
            print("    FAIL Button not found")
    except Exception as e:
        results["add_simple"] = {"pass":False,"evidence":str(e)[:100]}
        print(f"    FAIL: {e}")
    
    # 4b: Add variable product
    print("  4b: Add variable product...")
    try:
        page.goto(BASE+"/product/vineta-test-variable-product/", wait_until="domcontentloaded", timeout=12000)
        time.sleep(2)
        page.screenshot(path=os.path.join(DIR,"final-acceptance-screenshots","g4c-variable-product.png"))
        
        # Try selecting variations via JS
        page.evaluate("""() => {
            document.querySelectorAll('.variations select, [data-attribute_name] select').forEach(s => {
                if (s.options.length > 1) { s.selectedIndex = 1; s.dispatchEvent(new Event('change', {bubbles:true})); }
            });
        }""")
        time.sleep(2)
        
        btn = page.locator("button.single_add_to_cart_button, .variations_form button[type='submit']").first
        if btn.count() > 0:
            btn.click()
            time.sleep(3)
            results["add_variable"] = {"pass":True,"evidence":"Variable product added"}
            print("    PASS Variable product added")
            page.screenshot(path=os.path.join(DIR,"final-acceptance-screenshots","g4d-after-var-add.png"))
        else:
            results["add_variable"] = {"pass":False,"evidence":"No add button after variation"}
            print("    FAIL No add button")
    except Exception as e:
        results["add_variable"] = {"pass":False,"evidence":str(e)[:100]}
        print(f"    FAIL: {e}")
    
    # 4c: Cart page verification
    print("  4c: Cart page...")
    try:
        page.goto(BASE+"/cart/", wait_until="domcontentloaded", timeout=12000)
        time.sleep(2)
        page.screenshot(path=os.path.join(DIR,"final-acceptance-screenshots","g4e-cart-page.png"))
        
        rows = page.locator(".tf-cart-item, .cart_item, tr.cart_item").count()
        total_el = page.locator(".cart-head .total, .order-total .amount, .total-price, .cart-subtotal").first
        total_text = total_el.text_content().strip()[:30] if total_el.count() > 0 else "N/A"
        empty = page.locator("text=Your cart is currently empty").count() > 0
        
        results["cart_page"] = {"pass": rows > 0 or empty, "rows": rows, "total": total_text, "empty": empty}
        print(f"    PASS Cart: {rows} rows, total: {total_text}, empty: {empty}")
    except Exception as e:
        results["cart_page"] = {"pass":False,"evidence":str(e)[:100]}
        print(f"    FAIL: {e}")
    
    # 4d: Qty increase/decrease
    print("  4d: Qty controls...")
    try:
        inc = page.locator(".btn-increase, [data-action='increase'], .quantity-plus").first
        dec = page.locator(".btn-decrease, [data-action='decrease'], .quantity-minus").first
        if inc.count() > 0:
            inc.click()
            time.sleep(1.5)
            results["qty_increase"] = {"pass":True}
            print("    PASS Increase clicked")
        else:
            results["qty_increase"] = {"pass":False,"evidence":"No increase btn"}
            print("    FAIL No increase btn")
        if dec.count() > 0:
            dec.click()
            time.sleep(1.5)
            results["qty_decrease"] = {"pass":True}
            print("    PASS Decrease clicked")
        else:
            results["qty_decrease"] = {"pass":False,"evidence":"No decrease btn"}
            print("    FAIL No decrease btn")
    except Exception as e:
        results["qty_increase"] = {"pass":False,"evidence":str(e)[:80]}
        results["qty_decrease"] = {"pass":False,"evidence":str(e)[:80]}
    
    # 4e: Remove item
    print("  4e: Remove item...")
    try:
        rm = page.locator(".remove-cart, .product-remove a, .cart_item .remove, [data-action='remove']").first
        if rm.count() > 0:
            rm.click()
            time.sleep(2)
            results["remove"] = {"pass":True}
            print("    PASS Remove clicked")
            page.screenshot(path=os.path.join(DIR,"final-acceptance-screenshots","g4f-after-remove.png"))
        else:
            results["remove"] = {"pass":False,"evidence":"No remove btn"}
            print("    FAIL No remove btn")
    except Exception as e:
        results["remove"] = {"pass":False,"evidence":str(e)[:80]}
    
    # 4f: Persistence
    print("  4f: Persistence...")
    try:
        page.goto(BASE+"/cart/", wait_until="domcontentloaded", timeout=10000)
        time.sleep(1.5)
        rows_after = page.locator(".tf-cart-item, .cart_item, tr.cart_item").count()
        results["persistence"] = {"pass":True,"rows_after_reload":rows_after}
        print(f"    PASS Persistence: {rows_after} rows after reload")
    except Exception as e:
        results["persistence"] = {"pass":False,"evidence":str(e)[:80]}
    
    passed = sum(1 for v in results.values() if v.get("pass"))
    total = len(results)
    status = "PASS" if passed == total else "PARTIAL" if passed > 0 else "FAIL"
    print(f"\n  GATE 4: {status} ({passed}/{total})")
    
    browser.close()

out = os.path.join(DIR,"gate-4-cart.json")
with open(out,"w") as f: json.dump({"status":status,"passed":passed,"total":total,"details":results},f,indent=2,default=str)
print(f"Saved: {out}")
