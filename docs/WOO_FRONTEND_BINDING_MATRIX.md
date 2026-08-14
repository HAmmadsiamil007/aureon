# WOOCOMMERCE → FRONTEND BINDING MATRIX

> **Status:** COMPLETE (baseline) · **Date:** 2026-08-08 · **Closure:** 2026-08-09 (W1/W2 resolved — zero unguarded WC calls)
> **Source:** `frontend/adapters/*` (WC boundary), `aureon/theme/{archive-product,single-product,cart,checkout,myaccount,woocommerce}*.php` routing.
> **Rule:** WC owns commerce data/business logic; adapters map to ViewModels; components render. No WC logic recreated in frontend.

---

## 1. Route → template → section binding

| Route | Template (theme) | Sections | Adapter |
|---|---|---|---|
| `/shop/` + `/product-category/*` + `/product-tag/*` | `archive-product.php` | shop-hero → shop-filter → shop-grid → newsletter | `shop-hero`, `wc-filter`, `wc-products` |
| `/product/{slug}/` | `single-product.php` | product → related → newsletter | `product`, `wc-products(related_to)` |
| `/cart/` | `cart.php` | cart → newsletter | `cart` |
| `/checkout/` | `checkout/form-checkout.php` | checkout | WC native (wrapped in shell) |
| `/checkout/order-received/{id}/` | `woocommerce/checkout/thankyou.php` | order-confirmation → newsletter | `order` |
| `/my-account/*` | `myaccount/my-account.php` | page-banner + 1 of 5 branches (dashboard → `account/profile`; orders → nav + `account/orders`; other endpoints → stock WC framed; lost-password → stock form; logged-out → custom login/register) | `account` |
| `/wishlist/` | `page-wishlist.php` | wishlist → newsletter | `wishlist` |

## 2. Commerce surface binding

| Surface | Adapter fn | WC API / source | ViewModel → Component | Verified |
|---|---|---|---|---|
| Product cards (home bestsellers) | `aether_adapter_wc_products` | `WP_Query` + `meta_key=total_sales`, `wc_get_product` | ProductViewModel → `card/product` | ✅ 4 real products |
| Product cards (shop grid) | same (paged/tax/on_sale/orderby_shop) | `WP_Query`, `wc_get_product_ids_on_sale` | → `card/product` (shop layout) | ✅ 6 real products |
| Related products | same (related_to) | `wc_get_related_products` | → `product/related` | ✅ 4 excluding self |
| Category grid | `aether_adapter_wc_categories` | `get_terms('product_cat')`, `wp_count_terms`, term image → product image → placeholder | CategoryViewModel → `card/category` | ✅ |
| Shop filter | `aether_adapter_wc_filter` | `get_terms`, `wc_get_product_ids_on_sale` | → `section/filter-bar` | ✅ (hides when empty) |
| Shop hero title | `aether_adapter_shop_hero` | `woocommerce_page_title`, `wc_get_page_id` | → `hero/page-title` | ✅ |
| Single product | `aether_adapter_product` | `wc_get_product`, gallery ids, `pa_color`/`pa_size` terms, review comments, rating counts | SingleProductViewModel → `product/*` (9 components) | ✅ |
| Cart page | `aether_adapter_cart` | `WC()->cart`, `wc_get_cart_remove_url`, totals | CartViewModel → `cart/items` + `cart/summary` | ✅ (2026-08-14: real item + totals verified; qty/remove via JS posting `woocommerce-cart-form` with `update_cart` marker — plugin-owned JS) |
| Header/mobile cart count | `aether_adapter_header/mobile` | `WC()->cart->get_cart_contents_count()` | → `shell/header`, `shell/mobile-chrome` | ✅ |
| Add to cart | adapters | classic `?add-to-cart={id}` | `product/info`, `card/product` CTAs | ✅ (WC handles) |
| Order confirmation | `aether_adapter_order` | `wc_get_order` | OrderViewModel → `order/confirmation` | ✅ |
| My Account | `aether_adapter_account` | `wc_get_customer_order_count`, `wc_get_orders`, `wc_get_endpoint_url`, `wc_get_account_endpoint_url` | AccountViewModel → `account/profile` + `account/orders` | ✅ |
| Wishlist | `aether_adapter_wishlist` | user meta + `wc_get_product`, `wc_get_product_id_by_sku` | → `card/wishlist` | ✅ |
| Wishlist AJAX | `inc/aether-ajax.php` (theme, read-only) | user meta toggle + count | `main.js` buttons | ✅ (nopriv → login redirect) |
| Pricing/currency | adapters | `wc_price`, `get_woocommerce_currency` | price fields | ✅ |

## 3. WC state coverage

| State | Where | Behavior |
|---|---|---|
| Empty cart | `section-cart` | premium empty state + Continue Shopping |
| Cart update/remove | `section-cart` inline JS + plugin `aureon-woocommerce-js` | posts real cart form with `update_cart` marker; swaps fresh form + summary |
| Checkout order-review refresh | plugin `aureon-woocommerce-js` | WC checkout JS (AJAX order review) |
| Empty shop (no products) | `adapter_wc_products` | demo fallback, gated by `aether_demo_content` (G2 closed) |
| Empty categories | `adapter_wc_categories` | curated SKU fallback, gated by `aether_demo_content` (G2 closed) |
| No sale products | `adapter_wc_filter` | Sale button hidden |
| Product without image | `adapter-wc-categories` / product | `wc_placeholder_img_src` fallback |
| Product on sale | `adapter_wc_products` | badge "Sale" + old_price_plain strikethrough |
| New product (<30d) | same | badge "New" |
| Featured product | same | badge "Featured" |
| Empty order | `adapter_order` | demo shape for styleguide (G2) |
| Checkout with empty cart | `checkout/form-checkout.php` route | WC 302 → /cart/ (verified) |

## 4. WC data coverage gaps

| Gap | Detail | Fix (frontend) |
|---|---|---|
| W1 | `adapter-wc-products.php` `wc_get_product_ids_on_sale()`/`wc_get_related_products()` | ✅ **RESOLVED (2026-08-09)** — all call sites `function_exists`-guarded; plus top-of-function WC guards in `adapter-wc-products`/`adapter-product`/`adapter-wishlist` |
| W2 | `adapter-wc-categories.php` `wc_get_product_id_by_sku()` in fallback | ✅ **RESOLVED** — guarded at baseline |
| W3 | Checkout/account use native WC forms inside AETHER shell (no premium-styled override for checkout fields) | intentional (design decision) — revisit only with user approval |
| W4 | `cards/product` CTA buttons navigate to product page (not true add-to-cart) | intentional routing decision (Stage 13); a real AJAX add-to-cart is a feature request — deferred |
| W5 | Quick view modal (`commerce/quick-view`) depends on `aether-ajax` theme endpoint | read-only dependency; frontend consumes |

## 5. Non-WC fallback behavior (WC not active)

Every WC adapter call is guarded by `function_exists('wc_...')`/`class_exists('WooCommerce')` with graceful `home_url('/shop/')` fallbacks. **Zero unguarded WC calls remain** (grep-verified 2026-08-09); adapters loaded globally cannot fatal without WC (early-return empty states).
