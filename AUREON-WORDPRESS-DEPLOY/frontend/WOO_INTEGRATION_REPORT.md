# WOO_INTEGRATION_REPORT

**Phase:** 17 — Frontend Integration Framework (Step 8: WooCommerce)
**Date:** 2026-08-06
**Status:** Complete — strategy from static template + existing Aureon WC module

---

## 1. Objective

Bring the AETHER commerce pages (shop, product-detail, cart, checkout, wishlist, auth, account, thank-you) into the Aureon theme as **WooCommerce template overrides**, data-driven through the WC adapters (see ADAPTER_SPECIFICATION §2.4). No static commerce markup is imported wholesale.

---

## 2. Existing WC Module in Aureon (verified)

The plugin already ships `aureon/plugin/woocommerce/` with:

- `woocommerce.php` — module bootstrap
- `functions/functions.php` — hooks: `aureon_wc_defaults()`, `aureon_wc_color_defaults`, `aureon_wc_typography_defaults`, `aureon_wc_navigation_class`, `aureon_wc_post_class`, `aureon_wc_before/after_shop_loop`, `aureon_wc_scripts`, `aureon_wc_sidebar_layout`, `loop_shop_columns`/`per_page`, `aureon_wc_menu_cart` (nav cart via `wp_nav_menu_items`), `aureon_wc_do_cart_menu_item` (on `aureon_menu_bar_items`), checkout layout/footer filters.
- `fields/woocommerce-colors.php` — WC color settings.
- `functions/customizer/` — WC customizer settings + JS.
- `functions/css/woocommerce.css` + `woocommerce-mobile.css` — existing WC styles.
- `functions/js/woocommerce.js` — existing behavior.

**Impact:** The framework **extends** this module (adds `adapters/` + new template overrides + AETHER styling), it does not replace it.

---

## 3. Page-by-Page Integration

### 3.1 Shop (`shop.html` → archive-product)

| Static element | WC source | Template hook / strategy |
|---|---|---|
| `page-hero` + `page_title`/`page_description` | Shop page title/description (`woocommerce_show_page_title` already disabled — keep) | `archive-product.php` header + `aureon_wc_before_shop_loop` (existing) |
| `filter-bar` | WC layered nav + catalog ordering | Reuse `woocommerce_catalog_ordering` (existing) + `aureon` filter bar wrapper |
| `shop-grid-section` + `card/product` ×12 | `WC_Query` loop | `loop_shop_columns` (existing) + product card component inside `woocommerce_product_loop_start/end` |
| product card hover actions | add-to-cart button, wishlist toggle | Existing `aureon_wc_image_wrapper_open/close` + new `aether` card actions on `woocommerce_after_shop_loop_item` |
| `pagination` | `woocommerce_pagination` | Existing + block/pagination |
| badges (sale/new) | `woocommerce_show_product_loop_sale_flash` (already hooked) | Card component renders `product_badge` |

### 3.2 Single Product (`product-detail.html` → single-product)

| Static element | WC source | Strategy |
|---|---|---|
| `pd-hero`: gallery | `woocommerce_product_images` | `aether` gallery component + `data-image-zoom` + Swiper thumbnails |
| gallery thumbnails | `woocommerce_product_thumbnails` | component |
| `product/meta`: price | `woocommerce_template_single_price` | component |
| variant/size/color | WC attributes + `woocommerce_variation_add_to_cart` | adapter `wc_product_single` |
| qty stepper | `woocommerce_quantity_input` | component (AETHER stepper styling) |
| sticky add-to-cart | `woocommerce_single_variation` + JS | `sticky_product_name/price` bound |
| `pd-specs` tabs | WC tabs (`woocommerce_product_tabs`) | description/reviews hooks |
| `pd-reviews` | `woocommerce_product_reviews` | `card/review` + `product/rating` (rating breakdown) |
| `pd-related` | `woocommerce_output_related_products` | product-slider component + Swiper |

### 3.3 Cart (`cart.html` → cart)

| Static element | WC source |
|---|---|
| cart table + `card/cart-item` | `woocommerce_cart` form + cart items loop |
| quantity steppers | `woocommerce_quantity_input` |
| summary (`checkout/summary`) | `woocommerce_cart_totals` |
| update/apply coupon | existing WC JS (`woocommerce.js` untouched) + `aether-cart.js` additions (mini-cart count sync) |

### 3.4 Checkout (`checkout.html` → checkout)

| Static element | WC source |
|---|---|
| `checkout-section` + form | `woocommerce_checkout` + `WC()->checkout->get_checkout_fields()` via `wc_checkout_fields` adapter |
| order summary | `woocommerce_order_review` (order-review table) |
| payment methods | `woocommerce_checkout_payment` |
| trusted badges row | component (static content, Customizer-editable) |

### 3.5 Wishlist (`wishlist.html`)

- **Decision:** if Wishlist plugin active (`YITH`/`TI WooCommerce Wishlist`) → adapter maps `wc_wishlist`; else custom endpoint via `aether` `WC()` extension (session-based) or disable section with Customizer toggle.
- `card/product` reuse + remove action.

### 3.6 Auth (login/join → myaccount)

| Static element | Strategy |
|---|---|
| login/register forms | `woocommerce_login_form` / `woocommerce_register_form` (or `wp_login_form`) via `wc_auth` adapter |
| Google OAuth button | **Requires server-side token exchange** — config via Customizer (client id/secret), never hardcode. v1: hide unless configured. |
| password strength bar | existing WC JS + `aether-auth.js` enhancement |

### 3.7 Account dashboard (`account.html`)

- `myaccount.php` default layout (nav + content) — AETHER styling via CSS; overview widgets bound via `wc_order`/`wc_wishlist` adapters.

### 3.8 Order received (`thank-you.html` → thankyou)

| Static element | WC source |
|---|---|
| `order-confirmation` | `woocommerce_thankyou` (order number, status) via `wc_order` adapter |
| `order-summary` items | `woocommerce_order_details_table` |

---

## 4. Mini-Cart & Header Cart (existing hook reuse)

- `aureon_menu_bar_items` + `aureon_wc_do_cart_menu_item` already inject the cart button — replace its markup with `nav/mini-cart` component (frag count via `WC()->cart->get_cart_contents_count()`).
- Mini-cart drawer: `woocommerce_mini_cart` fragment target; `aether-cart.js` handles open/close + `wc_fragments_refreshed` event sync (reuse `woocommerce.js` fragments — already present).

---

## 5. WC Data Adapters (new, in plugin `woocommerce/adapters/`)

Full contracts in ADAPTER_SPECIFICATION §2.4 (`wc_products`, `wc_product_single`, `wc_categories`, `wc_cart`, `wc_cart_totals`, `wc_checkout_fields`, `wc_order`, `wc_wishlist`, `wc_auth`, `wc_rating_breakdown`).

**Notable decisions:**
- Prices formatted at adapter via `wc_price()` → raw cents retained.
- Gallery: `get_gallery_image_ids()` → component slices (main + 4 thumbs).
- Variants: `WC_Product_Variable::get_available_variations()` → `{id, attributes, price, stock}` array (JSON-serialized into `data-variations` for `aether-gallery.js`).
- Ratings: `get_rating_counts()` → bars array.

---

## 6. WC CSS/JS Strategy

- Keep existing `woocommerce.css`/`woocommerce-mobile.css` + `woocommerce.js` (regression-safe).
- Add `aether-wc.css` (component styles, tokenized via `--aureon-frontend-*`) — load only on WC pages (`is_woocommerce() || is_cart() || is_checkout() || is_account_page()`).
- `aether-wc.js` for card actions (add-to-cart AJAX via `wc_add_to_cart_params` — existing hookpoint, extend only).
- Ensure `.shop-page`-style body classes: `woocommerce` adds its own; body_class additions via `aureon_wc_post_class` (existing) extension.

---

## 7. Risks & Edge Cases

1. **Variable product JS** — WC's `add-to-cart-variation` script must load; AETHER size-stepper markup must use WC's `variations_form` classes to avoid breaking it. **Plan:** keep WC markup class skeleton, AETHER styling on top.
2. **Checkout fields** — full-bleed `checkout-form-wrap` needs `woocommerce_checkout` nonce fields; keep form `id="customer_details"`.
3. **Fragment caching** — mini-cart must use `woocommerce_after_mini_cart` hooks, not hardcoded markup.
4. **Order received without session** — `wc_order` adapter must handle `is_wc_endpoint_url('order-received')` + `$_GET['key']` validation via `wc_get_order( wc_get_order_id_by_order_key() )`.
5. **Third-party plugins** (payment gateways inject into checkout payment block — leave `woocommerce_checkout_payment` output untouched).
6. **Google OAuth** — firebase-auth.js uses modular v11; **module path bug** (`../assets/js/` vs `./assets/js/`) fixed in bundle; server-side token verification only.

---

## 8. Completion Gates

- [ ] Shop renders product grid with AETHER card styling, no WC default theme leakage
- [ ] Single product: gallery zoom, variant switch, qty, sticky add-to-cart work
- [ ] Cart + mini-cart counts sync via fragments
- [ ] Checkout submits an order end-to-end (BACS test gateway)
- [ ] Order received shows order items
- [ ] Wishlist works or is gracefully disabled
- [ ] `wp` + `wc` core tests pass; no PHP errors in debug.log