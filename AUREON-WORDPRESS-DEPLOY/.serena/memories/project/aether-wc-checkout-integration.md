# AETHER → WooCommerce Checkout Integration (2026-08-07, WORKING)

## Status: ✅ Checkout flow works end-to-end (cart → checkout → order → thank-you)

The AETHER static frontend (`C:\Users\hamma\Downloads\wordpress\frontend\`) is being
converted into WooCommerce templates served by the theme's `template_include` filter.
This memory covers the checkout portion only.

## Environment
- Container `aureon_wp` (wordpress:latest, PHP 8.3), http://localhost:8080
- WooCommerce 11.0.0 active; permalink `/%postname%/`; checkout page id 25, slug `checkout`
- Host source mirrors container at `/var/www/html/wp-content/` (frontend/, themes/aureon, plugins/)
- **`docker cp` DOES work for single files** (used for section-checkout.php + main.js this
  session) — the old "docker cp is broken" lesson applies to bulk dir deploys (use tar.gz pipe).

## Template routing (aureon/theme/inc/frontend.php:164-183)
`aureon_aether_wc_page_templates` (priority 99 on template_include):
- `is_cart()` → theme cart template
- `is_checkout() && !is_wc_endpoint_url('order-received') && !is_wc_endpoint_url('order-pay')`
  → theme checkout template (endpoints keep WC STOCK flow → thank-you page renders natively)
- `is_account_page()` → theme account template
- No `thankyou.php` override exists — stock WC renders at `/checkout/order-received/{id}/`

## Fixes applied (deployed + verified live)
### 1. section-checkout.php payment markup (frontend/sections/section-checkout.php)
WooCommerce checkout.js (WC 8.x+ `checkout.min.js`) requires:
- radio: `input[name="payment_method"]` with **id `payment_method_<gateway-id>`**
  (e.g. `payment_method_cheque`)
- wrapper: gateways inside `<div class="payment_methods">`
- box: `div.payment_box` with **class `payment_method_<gateway-id>`** (matches `e("div.payment_box."+id)`)

Without these → console error `Syntax error, unrecognized expression: .` and dead gateways.
All three were missing; now fixed (radio id line ~137, box class line ~142, wrapper ~131-151).

### 2. main.js Place Order hijack removed (frontend/assets/js/main.js)
ROOT CAUSE of failed orders: static-demo handler at old line 287-294 attached to
`#placeOrderBtn` calling `e.preventDefault()` + `window.location.href = 'thank-you.html'`
→ blocked WC AJAX submit AND redirected to nonexistent page (404). Replaced with comment
`// --- Checkout Place Order (WordPress build: WooCommerce AJAX handles order submission)`.
**File is ReadOnly on disk** — the edit/write tools fail with "Access denied"/FileSystem.writeFile;
must clear attribute first: `(Get-Item $p).IsReadOnly = $false`, then patch via PowerShell
line-index approach (box-drawing `───` in comments breaks naive `Contains` matches).

### 3. Verified end-to-end (Playwright, 2026-08-07)
- Checkout page 0 console errors; gateway toggle works (only selected payment_box visible)
- Filled billing: test@aether.test / Ava Chen / 123 Mono Ave / San Francisco / 94107
- Place Order → WC AJAX → redirect `/checkout/order-received/61/?key=wc_order_Y8ogknlsNiS1t`
- Order #61 created, status **wc-on-hold** (Direct bank transfer/BACS), $129.00
- Thank-you renders stock WC content (order number/date/total/payment/line items/billing)
  inside AETHER theme layout, 0 console errors
- Order verified in DB via `/tmp/wc-diag.php` (prints permalink, checkout page, order-received
  URL pattern, last order status)

## Remaining / open items
- **Static demo behaviors still in main.js** (and other frontend JS) — must audit + neutralize:
  - `.product-card` click → `product-detail.html` (breaks WP product links)
  - newsletter form handlers, gallery/slider behaviors tied to static pages
  - any other `.html` navigation (lenis scroll is fine; phantom-bridge/firebase-auth may be dead)
- **Cart page** (section-cart.php): verify add-to-cart/update/remove works via WC AJAX; check
  aether-cart.js static handlers similarly
- **Shop/product pages**: section-shop-grid/hero, section-product/related/reviews — verify WC
  query wiring (WC 11 template loader note in frontend.php comments)
- **Account page** (section-account?): login/register via WC + firebase-auth.js conflict check
- mu-plugins gap: `mu-plugins/aureon-fix-wc-session.php` absent from disk + container (see
  mem:aureon-docker-deployment)
- Checkout order-pay endpoint & guest checkout: untested
- Static `thank-you.html` page: still exists in frontend/ source — WC flow ignores it; consider
  removing or leaving as fallback

## Useful commands
- Deploy single file: `docker cp "C:\...\file" aureon_wp:/var/www/html/wp-content/frontend/...`
- Verify served JS truth (not browser cache): `docker exec aureon_wp grep -n "<string>" /var/www/html/wp-content/frontend/assets/js/main.js`
- Diag: `docker exec aureon_wp php /tmp/wc-diag.php`
- Browser test URL (cache-bust): `http://localhost:8080/checkout/?nocache=<rand>`
