"""GATE 1+2+3: Routes, Console, Network"""
import json, time, os
from datetime import datetime
from playwright.sync_api import sync_playwright

BASE = "http://localhost:8080"
DIR = os.path.dirname(os.path.abspath(__file__))

with sync_playwright() as p:
    browser = p.chromium.launch(headless=True)
    ctx = browser.new_context(viewport={"width":1440,"height":900})
    page = ctx.new_page()
    
    results = {}
    
    # GATE 1: Routes
    print("=== GATE 1: ROUTE REGRESSION ===")
    routes = {
        "/": "Vineta", "/shop/": "Shop", "/cart/": "Cart",
        "/checkout/": "Checkout", "/my-account/": "My Account",
        "/blog/": "Blog", "/about-us/": "About", "/contact-us/": "Contact",
        "/faq/": "FAQ", "/product/vineta-test-simple-product/": "Vineta",
        "/?s=vineta": "Search",
    }
    route_results = {}
    for path, frag in routes.items():
        try:
            resp = page.goto(BASE+path, wait_until="domcontentloaded", timeout=12000)
            time.sleep(1)
            status = resp.status if resp else 0
            title = page.title()
            body = page.content()[:3000].lower()
            ok = status == 200 and ("vineta" in title.lower() or "vineta" in body)
            route_results[path] = {"status":status,"title":title[:60],"pass":ok}
            print(f"  {'PASS' if ok else 'FAIL'} {path} -> {status} | {title[:50]}")
            if path == "/":
                page.screenshot(path=os.path.join(DIR,"final-acceptance-screenshots","g1-homepage.png"))
        except Exception as e:
            route_results[path] = {"status":0,"error":str(e)[:100],"pass":False}
            print(f"  FAIL {path} -> {e}")
    all_pass = all(r["pass"] for r in route_results.values())
    results["GATE_1"] = {"status":"PASS" if all_pass else "FAIL","details":route_results}
    
    # GATE 2: Console
    print("\n=== GATE 2: CONSOLE ===")
    console_data = {}
    for path in ["/", "/shop/", "/cart/", "/my-account/"]:
        issues = []
        def on_msg(msg):
            if msg.type in ("error",):
                issues.append(msg.text[:150])
        page.on("console", on_msg)
        try:
            page.goto(BASE+path, wait_until="domcontentloaded", timeout=10000)
            time.sleep(1.5)
        except: pass
        page.remove_listener("console", on_msg)
        console_data[path] = issues[:]
        print(f"  {path}: {len(issues)} errors")
        for e in issues[:2]: print(f"    - {e[:80]}")
    total_err = sum(len(v) for v in console_data.values())
    results["GATE_2"] = {"status":"PASS" if total_err==0 else "WARN","total_errors":total_err,"details":console_data}
    
    # GATE 3: Network
    print("\n=== GATE 3: NETWORK ===")
    net_data = {}
    for path in ["/", "/shop/", "/cart/"]:
        failed = []
        def on_resp(resp):
            if resp.status >= 400: failed.append({"url":resp.url[:120],"status":resp.status})
        page.on("response", on_resp)
        try:
            page.goto(BASE+path, wait_until="domcontentloaded", timeout=10000)
            time.sleep(1)
        except: pass
        page.remove_listener("response", on_resp)
        net_data[path] = failed[:]
        print(f"  {path}: {len(failed)} failed")
        for r in failed[:2]: print(f"    - {r['status']} {r['url'][:70]}")
    total_net = sum(len(v) for v in net_data.values())
    results["GATE_3"] = {"status":"PASS" if total_net==0 else "WARN","total_failed":total_net,"details":net_data}
    
    browser.close()

out = os.path.join(DIR,"gate-1-2-3.json")
with open(out,"w") as f: json.dump(results,f,indent=2,default=str)
print(f"\nSaved: {out}")
