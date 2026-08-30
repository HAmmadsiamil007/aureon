# Core-to-Ferm Integration Map

**Date:** 2026-08-26
**Purpose:** Map every dynamic data point from AUREON/WooCommerce to its Ferm Living presentation location.

---

## 1. Architecture Principle

```
AUREON/WooCommerce (canonical data)
        ↓
    Adapters (ONLY WP/WC touchpoint)
        ↓
    Normalized data arrays
        ↓
    Pack templates (presentation)
        ↓
    Ferm HTML/CSS/JS (visual output)
```

**Rule:** Ferm JS never calls WordPress/WooCommerce directly. All data flows through the adapter layer.

---

## 2. Product Data Flow

### 2.1 Product Card (Collection/Grid)

| Ferm DOM Element | Dynamic Field | AUREON Source | Integration Method |
|-----------------|---------------|---------------|-------------------|
| `.product-card__title` | Product name | `wc_get_product()->get_name()` | Server render via adapter |
| `.product-card__price` | Formatted price | `wc_get_product()->get_price_html()` | Server render via adapter |
| `.product-card__compare-price` | Original price (sale) | `wc_get_product()->get_regular_price()` | Server render via adapter |
| `.product-card__media img[src]` | Product image | `wp_get_attachment_url()` | Server render via adapter |
| `.product-card__media img[alt]` | Image alt | `get_post_meta(alt)` | Server render via adapter |
| `.product-card__url[href]` | Product URL | `get_permalink()` | Server render via adapter |
| `.product-card__badge` | Sale/New badge | `is_on_sale()` / `get_date_created()` | Server render via adapter |
| `.product-card__swatches[data-value]` | Color variants | `get_available_variations()` | Server render via adapter |
| `.product-card__add-to-cart[data-product-id]` | Variant ID | `wc_get_product()->get_id()` | Server render via adapter |
| `.product-card__wishlist[data-product-id]` | Product ID | `wc_get_product()->get_id()` | Server render via adapter |

**Adapter:** `adapter-wc-products.php`
**Pack override:** `components/cards/product.php`

### 2.2 Product Page (Single Product)

| Ferm DOM Element | Dynamic Field | AUREON Source | Integration Method |
|-----------------|---------------|---------------|-------------------|
| `.product-info__title` | Product name | `wc_get_product()->get_name()` | Server render |
| `.product-info__current-price` | Current price | `wc_get_product()->get_price_html()` | Server render |
| `.product-info__compare-price` | Regular price | `wc_get_product()->get_regular_price()` | Server render |
| `.product-info__description` | Short description | `wc_get_product()->get_short_description()` | Server render |
| `.product-gallery__main img[src]` | Featured image | `wp_get_attachment_url()` | Server render |
| `.product-gallery__thumb[data-image]` | Gallery images | `wc_get_product()->get_gallery_image_ids()` | Server render |
| `.variant-option[data-value]` | Variant options | `get_available_variations()` | Server render |
| `.variant-option[data-option-id]` | Variant ID | `variation['variation_id']` | Server render |
| `input[name="id"][value]` | Selected variant | `wc_get_product()->get_id()` | Server render |
| `.product-info__add-to-cart` | Add to cart URL | `wc_get_cart_add_url()` | Server render |
| `.product-info__accordion details` | Product details | `wc_get_product()->get_description()` | Server render |
| `.product-related .product-card` | Related products | `wc_get_related_products()` | Server render |

**Adapter:** `adapter-product.php`
**Pack override:** `components/product/info.php`, `components/product/gallery.php`

---

## 3. Cart Data Flow

| Ferm DOM Element | Dynamic Field | AUREON Source | Integration Method |
|-----------------|---------------|---------------|-------------------|
| `.cart-count` | Item count | `WC()->cart->get_cart_contents_count()` | Server render + JS update |
| `.cart-item__image` | Item image | `wp_get_attachment_url()` | Server render |
| `.cart-item__title` | Item name | `wc_get_product()->get_name()` | Server render |
| `.cart-item__variant` | Variant info | `variation_data` | Server render |
| `.cart-item__price` | Line price | `$cart_item['line_total']` | Server render |
| `.cart-item__quantity input` | Quantity | `$cart_item['quantity']` | Server render |
| `.cart-summary__subtotal` | Subtotal | `WC()->cart->get_cart_subtotal()` | Server render |
| `.cart-summary__shipping` | Shipping | `WC()->cart->get_shipping_total()` | Server render |
| `.cart-summary__total` | Total | `WC()->cart->get_total('edit')` | Server render |
| `.cart-item__remove[data-key]` | Cart item key | `$cart_item_key` | Server render |

**Adapter:** `adapter-cart.php`
**Pack override:** `sections/section-cart.php`

### Cart JS Bridge (Shopify API → WooCommerce)

```javascript
// Ferm cart.js calls Shopify API:
POST /cart/add.js
POST /cart/change.js
GET  /cart.js

// Bridge shims these to WooCommerce:
// /cart.js → WC REST API or AJAX fragment
// /cart/add.js → WC ?add-to-cart= or AJAX
// /cart/change.js → WC update-cart
```

---

## 4. Navigation Data Flow

| Ferm DOM Element | Dynamic Field | AUREON Source | Integration Method |
|-----------------|---------------|---------------|-------------------|
| `.site-header__nav a[href]` | Menu URLs | `wp_get_nav_menu_items('primary')` | Server render |
| `.site-header__nav a[text]` | Menu labels | `WP_Post::post_title` | Server render |
| `.mega-menu__column a[href]` | Submenu URLs | `WP_Post::post_parent` hierarchy | Server render |
| `.mega-menu__featured img[src]` | Mega menu images | Customizer repeater | Server render |
| `.mega-menu__featured a[href]` | Mega menu links | Customizer repeater | Server render |
| `.mobile-chrome a[href]` | Mobile menu URLs | `wp_get_nav_menu_items('primary')` | Server render |
| `.site-footer a[href]` | Footer menu URLs | `wp_get_nav_menu_items('footer')` | Server render |

**Adapters:** `adapter-menu.php`, `adapter-shell.php`
**Pack override:** `components/shell/header.php`, `components/shell/mobile-chrome.php`, `components/shell/footer.php`

---

## 5. Category/Collection Data Flow

| Ferm DOM Element | Dynamic Field | AUREON Source | Integration Method |
|-----------------|---------------|---------------|-------------------|
| `.category-grid__item img[src]` | Category image | `get_term_meta('thumbnail')` | Server render |
| `.category-grid__item h3` | Category name | `get_term('product_cat')->name` | Server render |
| `.category-grid__item a[href]` | Category URL | `get_term_link('product_cat')` | Server render |
| `.collection-hero h1` | Collection name | `get_queried_object()->name` | Server render |
| `.collection-hero p` | Collection description | `get_queried_object()->description` | Server render |
| `.filter-bar select[data-filter]` | Filter options | `get_terms('product_cat')` | Server render |
| `.collection-pagination a[href]` | Page URLs | `paginate_links()` | Server render |

**Adapters:** `adapter-wc-categories.php`, `adapter-wc-filter.php`
**Pack override:** `sections/section-categories.php`

---

## 6. Search Data Flow

| Ferm DOM Element | Dynamic Field | AUREON Source | Integration Method |
|-----------------|---------------|---------------|-------------------|
| `.search-overlay input` | Search input | User input | Client JS |
| `.search-results .product-card` | Search results | `WP_Query('s')` | Server render |
| `.search-suggestions a[href]` | Autocomplete | `WC()->get_endpoint_url('search')` | Client bridge |

### Search Bridge

Ferm uses Shopify predictive search (`/search/suggest.json`). Bridge replaces with:
- WordPress `WP_Query` for server-rendered results
- WC AJAX search endpoint for live suggestions

**Adapter:** WordPress search (via `search.php` template)
**Pack override:** Search overlay component

---

## 7. Customer/Account Data Flow

| Ferm DOM Element | Dynamic Field | AUREON Source | Integration Method |
|-----------------|---------------|---------------|-------------------|
| `.site-header__account[href]` | Account URL | `wc_get_account_endpoint_url('dashboard')` | Server render |
| `.account-orders__list` | Order history | `wc_get_orders(['customer' => $user_id])` | Server render |
| `.account-profile__name` | Customer name | `wp_get_current_user()->display_name` | Server render |
| `.account-profile__email` | Customer email | `wp_get_current_user()->user_email` | Server render |
| `.account-addresses` | Saved addresses | `WC()->customer->get_addresses()` | Server render |

**Adapters:** `adapter-auth.php`, `adapter-account.php`

---

## 8. Wishlist Data Flow

| Ferm DOM Element | Dynamic Field | AUREON Source | Integration Method |
|-----------------|---------------|---------------|-------------------|
| `.product-card__wishlist.active` | Wishlist state | `get_user_meta('aether_wishlist')` | Server render |
| `.wishlist-page .product-card` | Wishlist items | `aether_adapter_wishlist()` | Server render |

**Adapter:** `adapter-wishlist.php` (uses `aether_wishlist` user meta)
**Note:** Ferm uses Swym for wishlist. AUREON uses built-in user meta. Bridge: ignore Swym, use AUREON wishlist.

---

## 9. Blog/Article Data Flow

| Ferm DOM Element | Dynamic Field | AUREON Source | Integration Method |
|-----------------|---------------|---------------|-------------------|
| `.blog-card__title` | Post title | `get_the_title()` | Server render |
| `.blog-card__excerpt` | Post excerpt | `get_the_excerpt()` | Server render |
| `.blog-card__image img[src]` | Featured image | `wp_get_attachment_url()` | Server render |
| `.blog-card__date` | Publish date | `get_the_date()` | Server render |
| `.blog-card__author` | Author name | `get_the_author()` | Server render |
| `.blog-card__category` | Category | `get_the_category()` | Server render |
| `.blog-card__url[href]` | Post URL | `get_permalink()` | Server render |
| `.article-body` | Post content | `get_the_content()` | Server render |
| `.article-meta__tags` | Tags | `get_the_tags()` | Server render |
| `.blog-related .blog-card` | Related posts | `WP_Query('post')` | Server render |

**Adapters:** `adapter-blog.php`, `adapter-article.php`

---

## 10. Forms Data Flow

### Contact Form

| Ferm DOM Element | Dynamic Field | AUREON Source | Integration Method |
|-----------------|---------------|---------------|-------------------|
| `.contact-form input[name="name"]` | Form field | User input | Client → WC AJAX |
| `.contact-form input[name="email"]` | Form field | User input | Client → WC AJAX |
| `.contact-form textarea[name="message"]` | Form field | User input | Client → WC AJAX |
| `.contact-info__address` | Business address | Customizer option | Server render |
| `.contact-info__phone` | Phone number | Customizer option | Server render |
| `.contact-info__email` | Email address | Customizer option | Server render |

**Adapter:** `adapter-contact.php`
**Note:** AUREON has rate-limited contact AJAX handler. Use existing `aether-ajax.php`.

### Newsletter Form

| Ferm DOM Element | Dynamic Field | AUREON Source | Integration Method |
|-----------------|---------------|---------------|-------------------|
| `.newsletter__form input[name="email"]` | Email | User input | Client → WC AJAX |
| `.newsletter__form button[type="submit"]` | Submit | — | Client JS |

**Adapter:** `adapter-options.php`
**Note:** AUREON has newsletter DB + subscribe handler. Use existing `aether-newsletter.php`.

---

## 11. Homepage Composition

### Section Sequence

| Order | Section ID | Ferm Section | AUREON Section | Adapter |
|-------|-----------|--------------|----------------|---------|
| 1 | `hero` | Hero Split | hero-slider | adapter-hero.php |
| 2 | `categories` | Category Grid | categories | adapter-wc-categories.php |
| 3 | `bestsellers` | Product Grid | bestsellers | adapter-wc-products.php |
| 4 | `editorial` | Editorial Split | — (pack custom) | — |
| 5 | `rooms` | Room Grid | — (pack custom) | — |
| 6 | `newsletter` | Newsletter | newsletter | adapter-options.php |

**Pack control:** `composer.php` hooks `aether_frontpage_sections` to set this sequence.

---

## 12. Asset Loading

### CSS

| Asset | Source | Load Method |
|-------|--------|-------------|
| Tailwind utilities | Pack CSS | `manifest.json` → `assets.css` |
| Component styles | Pack CSS | `manifest.json` → `assets.css` |
| Font imports | Pack CSS | `manifest.json` → `assets.css` |
| Platform Bootstrap | CDN | `views/assets.php` (platform handle) |
| Platform FA | CDN | `views/assets.php` (platform handle) |

### JS

| Asset | Source | Load Method |
|-------|--------|-------------|
| Platform animations.js | Engine base | `views/assets.php` (platform handle) |
| Platform main.js | Engine base | `views/assets.php` (platform handle) |
| Pack ferm.js | Pack | `manifest.json` → `assets.js` |
| Embla Carousel | CDN/npm | Pack JS or platform |
| PhotoSwipe | CDN/npm | Pack JS or platform |

### Fonts

| Font | Source | Load Method |
|------|--------|-------------|
| CanelaText-Regular | Pack assets | CSS @font-face |
| KHTeka-Regular | Pack assets | CSS @font-face |
| KHTeka-Medium | Pack assets | CSS @font-face |

---

## 13. Known Integration Gaps

| Gap | Issue | Resolution |
|-----|-------|------------|
| `aether_pack_url()` | Undefined but called by pack | Add to `design.php` or define in pack |
| Cart page DOM | Missing from crawl | Reconstruct from Ferm cart-page.js behavior |
| Language selector | Single-store handling | AUREON has no built-in; add to header adapter |
| Tailwind utilities | Missing from shipped CSS | Use prettified superset from frozen source |
| Font licensing | CanelaText/KHTeka commercial | Confirm licensing before self-hosting |
| Platform contract JS | animations.js, main.js | Verify no clash with Ferm presentation JS |
| Shopify cart API | Bridge needed | Create shim endpoints: /cart.js, /cart/add.js |
| Swym wishlist | Not compatible | Use AUREON built-in wishlist instead |
| Klaviyo | Not compatible | Use AUREON newsletter handler |
| InstantClick | PWA transitions | Evaluate if needed or skip |
| Embla vs Swiper | Different carousel lib | Platform loads Swiper; pack can use Embla if isolated |

---

## 14. Integration Boundary Summary

```
┌─────────────────────────────────────────────────┐
│              AUREON CANONICAL LAYER              │
│  WordPress core + WooCommerce + Customizer       │
│  23 adapters → normalized data arrays            │
│  Security, SEO, analytics, performance           │
└──────────────────────┬──────────────────────────┘
                       │
              ┌────────┴────────┐
              │  ADAPTER LAYER  │
              │  (ONLY touch    │
              │   point)        │
              └────────┬────────┘
                       │
┌──────────────────────┴──────────────────────────┐
│              FERM PRESENTATION LAYER             │
│  Original HTML/CSS/JS from frozen source         │
│  Pack templates shadow engine components         │
│  CSS animations (no GSAP/Lenis needed)           │
│  Embla carousel, PhotoSwipe lightbox             │
└─────────────────────────────────────────────────┘
```

**The bridge is thin:** Adapters normalize data. Templates render it. JS enhances presentation. Commerce flows through existing WC endpoints.
