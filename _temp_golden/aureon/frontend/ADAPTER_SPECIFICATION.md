# ADAPTER_SPECIFICATION

**Phase:** 17 — Frontend Integration Framework (Step 4: Integration Layer)
**Date:** 2026-08-06
**Status:** Spec complete — implementation pending approval

---

## 1. Purpose

Adapters are the **only layer** that touches WordPress APIs. They translate WP/WooCommerce data into the normalized `$componentData` arrays that the component renderer consumes. Components never call WP functions — this is the hard architectural boundary.

```
WP/WC  →  Adapters  →  ViewModels (normalized arrays)  →  Renderer  →  Components  →  HTML
```

---

## 2. Adapter Registry

### 2.1 Core Adapters (theme-level, `aureon/theme/inc/adapters/`)

| Adapter | Input (WP API) | Output ($componentData) | Consumed By |
|---|---|---|---|
| `adapter-site.php` | `get_bloginfo`, `get_custom_logo`, `get_theme_mod` | `site` (name, tagline, logo, url) | shell/header, shell/footer |
| `adapter-menu.php` | `wp_get_nav_menu_items` | `menu` (tree: label, url, children, current) | nav/menu, nav/mobile-menu |
| `adapter-footer.php` | `dynamic_sidebar`, `get_theme_mod` | `footer` (widgets, social, copyright) | shell/footer |
| `adapter-search.php` | `get_search_form`, `home_url` | `search` (action, placeholder) | nav/search |
| `adapter-options.php` | `get_option` + plugin settings | `options` (theme options, toggles) | all (via registry) |
| `adapter-announcement.php` | `get_theme_mod` | `announcement` (text, url) | shell/announcement |

### 2.2 Content Adapters (`adapter-content.php`)

| Adapter | Input | Output | Consumed By |
|---|---|---|---|
| `page_title` | `get_the_title`, breadcrumbs | `page_title` (title, description, crumb) | hero/page-title |
| `section_header` | Customizer / post meta | `section_label`, `section_title`, `section_subtitle`, `section_cta` | section/header |
| `faq_items` | ACF/Custom post type | `faq` (q, a) | block/accordion |
| `team_members` | CPT or Customizer repeater | `team_member` (name, role, bio, avatar) | card/team |
| `testimonials` | CPT `aether_testimonial` | `review` (score, text, author, avatar) | card/review |
| `story_block` | Theme mods | `story_quote`, `brand_logo` | block/story |

### 2.3 Blog Adapters

| Adapter | Input | Output | Consumed By |
|---|---|---|---|
| `blog_posts` | `WP_Query` | `blog_post` (title, excerpt, date, category, url, image) | card/blog-card |
| `article_meta` | `get_the_*` | `article_meta`, `article_author`, `article_date`, `article_read_time` | article/meta |
| `article_body` | `the_content` | `article_body` | article/body |
| `pagination` | `paginate_links` | `pagination` (pages, current, urls) | block/pagination |

### 2.4 WooCommerce Adapters (plugin-level, `aureon/plugin/woocommerce/adapters/`)

| Adapter | Input (WC API) | Output | Consumed By |
|---|---|---|---|
| `wc_products` | `WC_Query` / `wc_get_products` | `product` (id, name, price, rating, badge, image, url) | card/product, card/product-slider |
| `wc_product_single` | `wc_get_product($id)` | `product` + gallery, variants, stock, description | product/gallery, product/meta, product/tabs |
| `wc_categories` | `get_terms('product_cat')` | `category` (name, count, image, url) | card/category |
| `wc_cart` | `WC()->cart` | `cart_item` (name, variant, price, total, qty) | cart/table, card/cart-item |
| `wc_cart_totals` | `WC()->cart->get_totals()` | totals (subtotal, shipping, total) | checkout/summary |
| `wc_checkout_fields` | `WC()->checkout->get_checkout_fields()` | `checkout` (billing, shipping, payment) | checkout/form |
| `wc_order` | `wc_get_order($id)` | `order` (number, items, totals, status) | order/success, card/order-item |
| `wc_wishlist` | `wc_get_wishlist` / plugin | `wishlist` (products) | wishlist grid |
| `wc_auth` | `is_user_logged_in`, `wp_login_form`, Google OAuth state | `auth` (form fields, nonce, action) | form/login, form/register |
| `wc_rating_breakdown` | `wc_get_rating_counts` | `rating` (score, count, bars) | product/rating |

### 2.5 Behavior Adapters

| Adapter | Output | Consumed By |
|---|---|---|
| `fx_reveal` | `data-reveal-item`, `data-reveal-group` | fx/* |
| `fx_tilt` | `data-tilt` | fx/tilt |
| `fx_parallax` | `data-parallax-section`, `data-parallax` | fx/parallax |
| `fx_motion_text` | `data-motion-text="words|lines"` | fx/motion-text |
| `fx_swiper` | `data-swiper` (breakpoints, loop, autoplay) | hero/slider, card/product-slider |

---

## 3. Adapter Contract (all adapters)

```php
/**
 * Every adapter:
 * 1. Is a plain function `aether_adapter_{name}( $context = array() )`.
 * 2. Accepts optional context (WP objects, args) — NEVER accepts raw $_GET/$_POST.
 * 3. Returns a normalizable array or WP_Error.
 * 4. Applies `aether_{name}_data` filter for extension points.
 * 5. Escapes nothing — the renderer escapes everything.
 */
function aether_adapter_wc_products( $context = array() ) {
    $defaults = array( 'limit' => 12, 'cat' => '', 'orderby' => 'popularity' );
    $args = wp_parse_args( $context, $defaults );
    $data = array( 'products' => array() );
    // ... WP_Query / wc_get_products ...
    return apply_filters( 'aether_wc_products_data', $data, $context );
}
```

### Escaping policy
- Adapters: raw values only (sanitize inputs, never escape outputs).
- ViewModels: normalize + type-cast.
- Renderer/Components: single-escape boundary (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post` where rich content).

---

## 4. ViewModel Normalization Rules

1. All keys snake_case, prefixed by phantom contract name (`section_title`, `product_price`).
2. Prices: `wc_price()` applied in adapter (raw → formatted + raw_cents).
3. Dates: `get_the_date()` formatted at adapter, ISO string also retained.
4. Images: `id`, `url`, `alt`, `sizes` (thumbnail, medium, large).
5. URLs: absolute `home_url()`-anchored.
6. Missing values → `''` (components render gracefully, `data-phantom` key omitted if empty).

---

## 5. Existing Hooks to Leverage (verified in Aureon core)

The framework builds on the existing theme hooks — **no new hook system is needed**:

| Existing Hook | Adapter | Purpose |
|---|---|---|
| `aureon_menu_bar_items` | nav/mini-cart, nav/search | header actions (already used by WC module `aureon_wc_do_cart_menu_item`) |
| `aureon_secondary_menu_bar_items` | secondary cart item | — |
| `aureon_inside_navigation` | — | nav injection points |
| `aureon_before/after_header_content` | — | header sections |
| `aureon_before/after_footer_widgets` | — | footer sections |
| `aureon_after_logo` / `aureon_before_logo` | shell/header | logo extras |
| `aureon_credits` / `aureon_before_copyright` | shell/footer | copyright area |
| `aureon_back_to_top_icon` / `aureon_back_to_top_start_scroll` | shell/back-to-top | behavior |
| `loop_shop_columns` / `loop_shop_per_page` | wc_products | shop grid config |
| `woocommerce_before/after_shop_loop` | shop-grid | section wrappers (already used) |
| `aureon_color_option_defaults` / `aureon_font_option_defaults` | wc tokens | WC color/font defaults (already used) |

---

## 6. REST Fallback (phantom-data.js AJAX layer)

`class-rest.php` already registers `aureon/v{version}` namespace. Target endpoint additions:

```
GET  /aureon/v1/frontend/page-data   →  { page: 'shop', sections: { ... } }
GET  /aureon/v1/frontend/products    →  { products: [...] }   (lazy product cards)
POST /aureon/v1/frontend/newsletter  →  { ok: true }          (replace demo form JS)
POST /aureon/v1/frontend/contact     →  { ok: true }          (replace contact-form.php)
```

- Nonces via `wp_rest` (REST `X-WP-Nonce` header) — fixes phantom-bridge design.
- Server-side rendering remains primary; REST used for lazy-load and form submits.
- `phantom-data.js` is refactored into `aether-frontend.js` with the fixed `init()`.

---

## 7. Security Requirements

1. All form posts require nonce + capability check (existing `update_settings_permission` pattern reused).
2. Newsletter/contact endpoints rate-limited + honeypot field.
3. Google OAuth (login.html) — token exchanged server-side only; never expose API keys in frontend JS.
4. Adapters never output user-supplied HTML unescaped; rich content whitelisted via `wp_kses_post`.
5. WooCommerce adapters respect `wc_get_loop_prop` and product visibility.