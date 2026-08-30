# PHASE 06 — WC BINDING MATRIX COMPLETION

> **Phase:** 6 · **Date:** 2026-08-14 · **Method:** static (templates + adapters) + live HTTP probes against `localhost:8080`
> **Scope:** cart / checkout / order-received / my-account (5-branch) / wishlist — the five commerce surfaces that were only baseline-verified in `docs/WOO_FRONTEND_BINDING_MATRIX.md`
> **Result:** ALL FIVE surfaces verified end-to-end; 3 findings (1 MED, 2 LOW); no change required

---

## 1. Route → template → section (verified this phase)

| Route | Template | Sections / components | Adapter → component | Live check |
|---|---|---|---|---|
| `/cart/` | `aureon/theme/cart.php` | `cart` + `newsletter` (gated `aether_section_newsletter`) | `aether_adapter_cart` → `cart/items` + `cart/summary` | ✅ 200, real item rendered |
| `/checkout/` | `aureon/theme/checkout/form-checkout.php` | `checkout` | `aether_adapter_cart(context=checkout)` + `WC()->checkout` fields | ✅ 200 (cart non-empty) / 302→/cart/ (empty) |
| `/checkout/order-received/{id}/` | `aureon/theme/woocommerce/checkout/thankyou.php` | `order-confirmation` + `newsletter` | `aether_adapter_order` → `order/confirmation` | static (needs real order; adapter guarded) |
| `/my-account/*` | `aureon/theme/myaccount/my-account.php` | page-banner + 1 of 5 branches (below) | `aether_adapter_account` / `aether_adapter_account_orders` | ✅ 200 logged-out |
| `/wishlist/` | `aureon/theme/page-wishlist.php` | `wishlist` + `newsletter` | `aether_adapter_wishlist` (user meta) | ✅ 200 |

## 2. My Account — 5-branch decision table (my-account.php)

| # | Condition (line) | Rendered | Data source |
|---|---|---|---|
| 1 | `is_logged_in && endpoint === ''` (:37) | AETHER `account/profile` component only | `aether_adapter_account()` (WC counts + endpoint URLs) |
| 2 | `is_logged_in && endpoint === 'orders'` (:51) | `woocommerce_account_navigation()` + AETHER `account/orders` | `aether_adapter_account_orders()` → `wc_get_orders()` |
| 3 | `is_logged_in` (other endpoints) (:80) | stock WC content (`woocommerce_account_navigation` + `woocommerce_account_content`) framed in `.aether-wc` | WC native |
| 4 | `!is_logged_in && endpoint === 'lost-password'` (:101) | stock WC form, 6-col centered frame | WC native |
| 5 | `else` (logged out) (:122) | custom AETHER login + register forms posting to `WC_Form_Handler`; register block only when `woocommerce_enable_myaccount_registration === 'yes'` (:125) | WC nonces + handlers |

Live: `/my-account/` logged-out → branch 5 (login form present, register hidden — option off, matches :125 conditional).

## 3. Commerce data path verification

- **Cart** (`adapter-cart.php`): `WC()->cart->get_cart()` → item rows (name, `pa_color`/`pa_size` variant text, qty, `wc_price`, remove URL, permalink) + `get_cart_subtotal`/`get_shipping_total`/`get_total_tax`/`get_total('edit')`. Empty guard `WC()->cart->is_empty()` at :65. Qty +/- JS posts the real `woocommerce-cart-form` with the `update_cart` marker WC_Form_Handler expects (inline script in `section-cart.php`).
- **Checkout** (`section-checkout.php`): custom-rendered billing/shipping/terms fields via `aether_checkout_render_field()` (:36 — reads `WC()->checkout->get_value($key)`), `wp_nonce_field('woocommerce-process_checkout')`, posts to `wc_get_checkout_url()` — flow passes through `WC_Form_Handler::checkout_action()`. Totals sidebar via `checkout/order-items` fed by `adapter-cart(context=checkout)`.
- **Order received**: `wc_get_order($order_id)`; account-page context swaps track URL to orders endpoint (:44-45).
- **My Account**: `wc_get_customer_order_count`, `wc_get_orders`, `wc_get_account_endpoint_url`, `wc_get_endpoint_url`.
- **Wishlist**: user meta + `wc_get_product`, `wc_get_product_id_by_sku`; AJAX toggle via `aether-ajax.php` (read-only, nopriv → login redirect).

Live evidence (2026-08-14, `Invoke-WebRequest` sessions):
- `?add-to-cart=526` → `/cart/` shows real row "Chambray Workshirt | Vintage Indigo / One size · $281.00 · Total $281.00 · Subtotal $281.00 · Shipping Free" + Proceed to Checkout.
- `/checkout/` (non-empty cart): 200 with `aether-checkout-form`, `billing_email`, `place_order`, Subtotal summary. Empty cart: 302 → `/cart/` (WC rule preserved).
- Catalog now 500+ products (demo import); earlier "6 products" baseline is superseded — adapter scales, no fixed-cap bugs found.

## 4. Findings

| ID | Sev | Finding |
|---|---|---|
| F6-1 | MED | **WC state changes still handled by the plugin's JS/CSS** (`aureon_wc_scripts`): cart update/remove, checkout AJAX (update order review), and account navigation rely on plugin-registered scripts (`aureon-woocommerce-js` — the ONLY plugin asset confirmed live). Theme templates are section-composition, but WC's own JS is the engine. Works today (verified 200 + totals), but is a single-point dependency — any plugin-neutralization change (Phase 12-15) must preserve `aureon-woocommerce-*` enqueues or re-implement these interactions in `frontend/`. |
| F6-2 | LOW | **Checkout field rendering is hand-rolled** (`aether_checkout_render_field`): 6 field types handled; WC can register custom types (e.g. payment-method extras, `checkout/order-pay` fields) that will fall through to default text rendering — cosmetic risk only, form still posts intact. |
| F6-3 | LOW | `adapter-cart.php:121` shipping label: totals ≤ 0 display "Free" — also true when shipping methods are NOT configured (not just free shipping), which can mislabel an unconfigured store. Cosmetic. |

## 5. Matrix delta (updates to `docs/WOO_FRONTEND_BINDING_MATRIX.md`)

- Section 1: my-account row — replaced "page-banner + account/profile + account/orders" with the 5-branch table (Section 2 above).
- Section 2: cart row now "✅ verified with real item" (was empty-state only); checkout row documented as custom-rendered fields (W3 in old matrix said "native WC forms… no premium-styled override" — **superseded**: fields ARE premium-styled via `aether_checkout_render_field`, though the form itself remains WC-compatible).
- Section 3: added "Cart update/remove" + "Checkout order-review refresh" state rows (JS-mediated, plugin-owned).

## 6. Verdict

All five commerce surfaces bind real WC data through adapters with zero unguarded calls (grep-verified); demo shapes only for order-empty and gated by `aether_demo_content`. No theme/frontend change required in this phase. F6-1 feeds the Phase 12-15 change-gate analysis; F6-2/F6-3 are cosmetic, defer.