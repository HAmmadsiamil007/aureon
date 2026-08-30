# FRONTEND API USAGE

> **Status:** COMPLETE (baseline) · **Date:** 2026-08-08 · **Closure:** 2026-08-09 (Phase B guards added)
> **Purpose:** exhaustively document the WP/WC surface the frontend consumes, per layer. Adapters are the only WP/WC boundary; components must stay clean.

---

## 1. WordPress core APIs (used in adapters/views only)

| API | File | Purpose |
|---|---|---|
| `get_bloginfo('name' / 'description')` | `adapter-site`, `adapter-shell`, `adapter-auth`, `adapter-coming-soon` | brand/tagline |
| `home_url()` | shell/contact/faq/cart/shop adapters, sections | canonical URLs |
| `get_permalink()` | `adapter-menu`, `adapter-wc-products`, `adapter-blog`, `adapter-article`, `adapter-wishlist` | item URLs |
| `get_page_by_path()` | `adapter-shell` (wishlist) | wishlist page |
| `wp_get_nav_menu_items()` | `adapter-menu` | primary menu tree |
| `WP_Query` | `adapter-blog`, `adapter-faq`, `adapter-testimonials`, `adapter-team`, `adapter-wishlist`, `adapter-wc-products` | content queries |
| `get_posts()` | `adapter-wc-categories` (category image), `adapter-wishlist` | helper queries |
| `get_terms()` | `adapter-wc-categories`, `adapter-wc-filter` | product categories |
| `get_term_link()` | `adapter-wc-categories` | term URLs (WP_Error guarded) |
| `wp_count_terms()` | `adapter-wc-categories` | "view all" logic |
| `get_option()` | `adapter-wc-categories` (default_product_cat), `adapter-contact` (admin_email), `adapter-auth` (WC registration flag) | config |
| `get_post_meta()` | `adapter-wc-products` (image alt), `adapter-product` | meta |
| `get_the_post_thumbnail_url()` | product/blog/wishlist/categories adapters | images |
| `wp_get_attachment_image_url()` | `adapter-wc-categories`, `viewmodel.php` | image URLs |
| `wp_login_url()` | `adapter-shell`, `adapter-menu` | auth fallback |
| `esc_url_raw()` / `sanitize_text_field()` | all adapters + viewmodel | normalization |
| `wp_strip_all_tags()` | `adapter-wc-products`, `adapter-blog` | excerpt/price cleanup |
| `wp_parse_args()` | all adapters | contract merging |
| `is_user_logged_in()` | `inc/frontend.php` (theme, read-only) | aetherAjax context |
| `paginate_links()` (indirect) | `adapter-blog` | pagination data |

## 2. WooCommerce APIs

| API | File | Purpose |
|---|---|---|
| `wc_get_page_permalink('shop' / 'myaccount')` | shell, auth, account, cart, order, wishlist, menu, wc-* adapters | canonical page URLs |
| `wc_get_cart_url()` / `wc_get_checkout_url()` | shell, cart, product | cart/checkout URLs |
| `wc_get_product()` | `adapter-wc-products`, `adapter-product`, `adapter-wishlist`, `adapter-order` | product objects |
| `wc_get_product_ids_on_sale()` | `adapter-wc-products`, `adapter-wc-filter` | sale filter (guarded) |
| `wc_get_related_products()` | `adapter-wc-products` | related engine (guarded) |
| `wc_get_product_id_by_sku()` | `adapter-wc-categories` | fallback images (guarded) |
| `wc_price()` | `adapter-wc-products`, `adapter-cart` | formatted prices |
| `wc_placeholder_img_src()` | `adapter-wc-categories` | missing-image fallback |
| `WC()->cart` | `adapter-cart`, `adapter-shell` | cart items/count/totals |
| `wc_get_cart_remove_url()` | `adapter-cart` | remove links |
| `wc_get_orders()` | `adapter-account` | customer orders |
| `wc_get_customer_order_count()` | `adapter-account` | stats |
| `wc_get_order()` | `adapter-order` | order confirmation |
| `wc_get_endpoint_url()` / `wc_get_account_endpoint_url()` | `adapter-account`, `adapter-order` | account nav |
| `woocommerce_page_title()` | `adapter-shop-hero` | shop title |
| `wc_get_page_id('shop')` | `adapter-shop-hero` | title fallback |
| `get_woocommerce_currency()` | theme `aether-seo` (read-only) | schema.org |

## 3. Theme settings APIs (allowed)

| API | Consumers |
|---|---|
| `aureon_get_option()` | all adapters, viewmodel behavior, composer, sections, tokens |
| `aureon_option_defaults` filter | `tokens.php` `aether_frontend_defaults()` |
| `aureon_color_option_defaults` filter | `tokens.php` color bridge |
| `aureon_font_option_defaults` filter | `tokens.php` font bridge |

## 4. REST / AJAX surfaces consumed

| Endpoint | Owner | Consumer |
|---|---|---|
| `admin-ajax.php` (aether_nonce) | theme `aether-ajax`, `aether-newsletter` | `main.js` (wishlist, newsletter) |
| `rest_url('aether/v1/newsletter/subscribe')` | theme `aether-newsletter` | `main.js` newsletter fallback |
| `aetherAjax.shopUrl` / `searchUrl` | theme `inc/frontend.php` localize | `main.js` search overlay + product-card routing |

## 5. Guarded-region summary

| Layer | WP/WC calls | Policy |
|---|---|---|
| Adapters | yes | the only sanctioned boundary; `function_exists` guards added (Phase B closed 2026-08-09) |
| Views (composer/viewmodel) | `aureon_get_option`, image helpers | settings + media only |
| Sections | `home_url()` URL fallbacks only | presentation fallback; prefer `$sectionData` |
| Components | **zero** | grep gate in `tests/verify.sh` |
| Theme templates | `aether_render_*` + `aureon_get_option` gating | read-only |

## 6. Security posture of the surface

- All URL output `esc_url` / `esc_url_raw`; text `esc_html`; attributes `esc_attr`.
- `price` HTML passthrough in `card/product.php` is the single documented exception (WC-generated markup, phpcs-tagged).
- `$_SERVER['REQUEST_URI']` read in `section-shop-grid.php` is phpcs-tagged; Phase B hardens to WC canonical base.
- No raw SQL, no `eval`, no shell execution anywhere in `frontend/` (verified).
- AJAX endpoints nonce-checked (theme-side, read-only).
