# Ferm Living — Template Contract

**Date:** 2026-08-26
**Purpose:** Define every dynamic data slot, route, DOM hook, and commerce interface for the 15 standalone Ferm Living templates.

---

## 1. Template Families & Routes

| # | Family | Template File | Route Pattern | Dynamic |
|---|--------|--------------|---------------|---------|
| 1 | Homepage | `index.html` | `/` | YES |
| 2 | Product (Sofa) | `products/rico-sofa-2-boucle-off-white.html` | `/products/{handle}` | YES |
| 3 | Product (Chair) | `products/rico-lounge-chair-raw-boucle-natural.html` | `/products/{handle}` | YES |
| 4 | Product (Lamp) | `products/meridian-lamp-black.html` | `/products/{handle}` | YES |
| 5 | Collection (Furniture) | `collections/furniture.html` | `/collections/{slug}` | YES |
| 6 | Collection (Lighting) | `collections/lighting.html` | `/collections/{slug}` | YES |
| 7 | Collection (Accessories) | `collections/accessories.html` | `/collections/{slug}` | YES |
| 8 | Blog Listing | `blogs/stories.html` | `/blog` | YES |
| 9 | Contact | `pages/contact.html` | `/contact` | YES |
| 10 | About | `pages/about-ferm-living.html` | `/about` | YES |
| 11 | Store Locator | `pages/store-locator.html` | `/store-locator` | YES |
| 12 | Cart | `cart.html` | `/cart` | YES |
| 13 | Checkout | `checkout.html` | `/checkout` | REDIRECT |
| 14 | Account | `account.html` | `/account` | REDIRECT |
| 15 | Account Login | `account/login.html` | `/account/login` | YES |

---

## 2. Global Shell (All Pages)

Every page shares the same header/footer/cart drawer. These dynamic slots must be present on ALL pages.

### 2A. USP Bar

```html
<div class="usp-header" data-component="uspHeader" data-speed="4000" data-usp-length="[COUNT]">
  <div data-usp-current-index="1"></div>
  <div data-usp-item data-usp-index="0"><a href="[URL]"><p>[TEXT]</p></a></div>
  <div data-usp-item data-usp-index="1">...</div>
</div>
```

| Slot | Type | WordPress Source |
|------|------|------------------|
| `[COUNT]` | int | ACF options page (USP items count) |
| `[URL]` | string | ACF link field per USP item |
| `[TEXT]` | string | ACF text field per USP item |

### 2B. Header Navigation

```html
<nav data-component="header" data-template="[TEMPLATE_TYPE]">
  <a data-header-link href="[URL]">[LABEL]</a>
  <!-- Mega menu triggered by data-megamenu -->
</nav>
```

| Slot | Type | WordPress Source |
|------|------|------------------|
| `[TEMPLATE_TYPE]` | string | Body class / ACF template name |
| `[URL]` | string | `wp_nav_menu()` |
| `[LABEL]` | string | Menu item title |

### 2C. Cart Drawer (All Pages)

```html
<div id="cart-drawer" data-cart-drawer-count-number="[COUNT]">
  <div data-cart-drawer-content>[RENDERED BY JS]</div>
  <div data-cart-drawer-footer>
    <div data-cart-total-price>[TOTAL_CENTS]</div>
  </div>
</div>
```

| Slot | Type | WordPress Source |
|------|------|------------------|
| `[COUNT]` | int | `WC()->cart->get_cart_contents_count()` |
| `[TOTAL_CENTS]` | int | `WC()->cart->get_cart_contents_total() * 100` |

### 2D. Footer

| Slot | Type | WordPress Source |
|------|------|------------------|
| Newsletter form | HTML | WPForms or Mailchimp for WP |
| Footer nav links | HTML | `wp_nav_menu('footer')` |
| Social links | HTML | ACF options page |
| Copyright | string | Hardcoded + `date('Y')` |
| Payment icons | HTML | WooCommerce gateways |

---

## 3. Homepage (`index.html`)

### Dynamic Data Slots

| Slot | DOM Hook | WordPress Source |
|------|----------|------------------|
| Hero image (mobile) | `<img class="md:hidden">` | ACF image field |
| Hero image (desktop) | `<img class="md:block hidden">` | ACF image field |
| Hero headline | heading element | ACF text field |
| Hero CTA link | `<a>` | ACF link field |
| Hero CTA text | `<a>` | ACF text field |
| Featured collections grid | section wrapper | `WP_Query` product_cat |
| Featured collection image | `<img>` | Term thumbnail |
| Featured collection title | `<h3>` | Term name |
| Featured collection link | `<a>` | Term link |
| Product grid items | `data-component="productCard"` | `WC_Product_Query` |
| Product card image | `<img>` | `$product->get_image()` |
| Product card title | text node | `$product->get_name()` |
| Product card price | `data-price` | `$product->get_price()` (cents) |
| Product card link | `<a href>` | `$product->get_permalink()` |
| Product card color swatches | color options | `$product->get_children()` |
| Blog post cards | article elements | `WP_Query('post_type=post')` |
| Blog post image | `<img>` | `the_post_thumbnail()` |
| Blog post title | heading | `the_title()` |
| Blog post link | `<a>` | `the_permalink()` |

---

## 4. Product Page (`products/*.html`)

### Dynamic Data Slots

| Slot | DOM Hook | WordPress Source |
|------|----------|------------------|
| Product title | `<h1>` | `$product->get_name()` |
| Short description | text near price | `$product->get_short_description()` |
| Long description | tab content | `$product->get_description()` or ACF |
| Price (regular) | `data-variant-price` | `$product->get_price()` (cents) |
| Price (formatted) | visible text | `wc_price()` |
| SKU | hidden/meta | `$product->get_sku()` |
| Main image | first `<img>` | `$product->get_image()` |
| Gallery images | gallery thumbs | `$product->get_gallery_image_ids()` |
| Variant selector | `<select>` or swatches | `WC_Product_Variable::get_children()` |
| Variant ID | `data-variant-id` | `$variation->get_id()` |
| Variant price | `data-variant-price` | `$variation->get_price()` (cents) |
| Variant available | `data-variant-available` | `$variation->is_in_stock()` |
| Variant inventory | `data-variant-inventory` | `$variation->get_stock_quantity()` |
| Add to cart form | `<form>` | WC cart handler |
| Product ID | `data-product-id` | `$product->get_id()` |
| Product handle | `data-product-handle` | `$product->get_slug()` |
| Cross-sell products | `data-products` | ACF relationship or WC upsells |
| JSON-LD Product | `<script type="application/ld+json">` | `WC_Structured_Data` |

### Required JSON-LD (Product)

```json
{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "[TITLE]",
  "description": "[SHORT_DESC]",
  "image": "[IMAGE_URL]",
  "sku": "[SKU]",
  "brand": {"@type": "Brand", "name": "ferm LIVING"},
  "offers": {
    "@type": "Offer",
    "price": "[PRICE_CENTS/100]",
    "priceCurrency": "EUR",
    "availability": "https://schema.org/[InStock/OutOfStock]"
  }
}
```

---

## 5. Collection Page (`collections/*.html`)

### Dynamic Data Slots

| Slot | DOM Hook | WordPress Source |
|------|----------|------------------|
| Collection title | `<h1>` | `single_term_title()` |
| Collection description | text block | `$term->description` |
| Collection image | hero `<img>` | `$term->thumbnail` |
| Product count | text node | `$term->count` |
| Product grid items | `data-component="productCard"` | `WC_Product_Query` |
| Sort dropdown | `<select>` | Custom sort |
| Filter options | checkboxes/forms | WooCommerce layered nav |
| Pagination | page links | `previous_posts_link()` / `next_posts_link()` |

---

## 6. Blog Listing (`blogs/stories.html`)

### Dynamic Data Slots

| Slot | DOM Hook | WordPress Source |
|------|----------|------------------|
| Blog title | `<h1>` | `single_post_title()` |
| Blog description | text block | ACF or hardcoded |
| Post cards | article elements | `WP_Query('post_type=post')` |
| Post image | `<img>` | `the_post_thumbnail()` |
| Post title | heading | `the_title()` |
| Post excerpt | text | `the_excerpt()` |
| Post date | text | `the_date()` |
| Post link | `<a>` | `the_permalink()` |
| Pagination | page links | `previous_posts_link()` / `next_posts_link()` |

---

## 7. Static Pages (Contact, About, Store Locator)

### Contact Page

| Slot | DOM Hook | WordPress Source |
|------|----------|------------------|
| Page title | `<h1>` | `the_title()` |
| Page content | content area | `the_content()` |
| Contact form | `<form action="/contact">` | WPForms / CF7 |
| Store locations | map pins | ACF repeater + Google Maps |
| FAQ items | accordion | ACF flexible content |

### About Page

| Slot | DOM Hook | WordPress Source |
|------|----------|------------------|
| Page title | `<h1>` | `the_title()` |
| Hero image + text | ACF FC | ACF flexible content |
| Story sections | ACF FC | ACF flexible content |
| Team members | ACF repeater | ACF repeater |

---

## 8. Cart Page (`cart.html`)

### Dynamic Data Slots

| Slot | DOM Hook | WordPress Source |
|------|----------|------------------|
| Line items | cart item rows | `WC()->cart->get_cart()` |
| Item image | `<img>` | `$cart_item['data']->get_image()` |
| Item title | text | `$cart_item['data']->get_name()` |
| Item variant | text | `$cart_item['variation_id']` |
| Item price | text | `$cart_item['data']->get_price()` |
| Item quantity | `<input type="number">` | `$cart_item['quantity']` |
| Item total | text | `$cart_item['line_total']` |
| Item remove | button/link | `wc_get_cart_remove_url()` |
| Cart subtotal | text | `WC()->cart->get_cart_subtotal()` |
| Cart total | text | `WC()->cart->get_total()` |
| Proceed to checkout | `<a>` | `wc_get_checkout_url()` |

---

## 9. Checkout & Account

**Checkout** (`checkout.html`) — Redirects to Shopify hosted checkout. Rebuild using WooCommerce checkout template. No frozen HTML to port.

**Account** (`account.html`) — Redirects to login. Use WordPress/WC account pages.

**Account Login** (`account/login.html`) — Login form with password recovery. Map to `wp_login_url()`.

---

## 10. Commerce API Contract

### Cart Endpoints (Shim)

| Shopify Endpoint | WooCommerce Shim | Response Format |
|-----------------|------------------|-----------------|
| `GET /cart.js` | `GET /wp-json/ferm-cart/v1/cart` | `{items_count, total_price, items:[...]}` |
| `POST /cart/add.js` | `POST /wp-json/ferm-cart/v1/add` | `{sections:{"cart-drawer":"<html>","main-cart":"<html>"}}` |
| `POST /cart/update.js` | `POST /wp-json/ferm-cart/v1/update` | Same as above |
| `POST /cart/change.js` | `POST /wp-json/ferm-cart/v1/change` | Same as above |

### Money Format

```json
{
  "active": "EUR",
  "rate": "1.0",
  "format": "EUR {{amount_with_comma_separator}}"
}
```

Map to: `wp_localize_script('ferm-app', 'Shopify', {money_format: get_woocommerce_currency_symbol()})`

### Section Rendering Response

The app.js expects HTML fragments in `sections` object. WooCommerce bridge must render PHP templates and return as JSON:

```php
// WooCommerce bridge endpoint
function ferm_cart_add_handler() {
    $cart = WC()->cart;
    $cart->add_to_cart($_POST['id'], $_POST['quantity']);

    // Render cart drawer template
    ob_start();
    include FERM_TEMPLATE_PATH . '/cart-drawer.php';
    $cart_drawer_html = ob_get_clean();

    // Render main cart template
    ob_start();
    include FERM_TEMPLATE_PATH . '/main-cart.php';
    $main_cart_html = ob_get_clean();

    wp_send_json_success([
        'sections' => [
            'cart-drawer' => $cart_drawer_html,
            'main-cart' => $main_cart_html,
        ]
    ]);
}
```

---

## 11. CustomEvent Contracts

### Events the JS Produces (OUT)

| Event | Payload | Consumer |
|-------|---------|----------|
| `cart:open` | none | Cart drawer component |
| `cart:update` | none | Cart count, drawer |
| `cart:error` | `{detail: string}` | Toast notification |
| `variant:change` | `{detail: {variant, productId}}` | Product page, cart |

### Events the JS Consumes (IN)

| Event | Payload | Producer |
|-------|---------|----------|
| `variant:change` | `{detail: {variant, productId}}` | Variant selector |
| `shopify:section:load` | `{detail: {container}}` | WordPress AJAX |

---

## 12. Window Globals Contract

```javascript
// Required window globals for standalone mode
window.Shopify = {
  routes: { root: '/' },
  currency: { active: 'EUR', rate: '1.0' },
  money_format: 'EUR {{amount_with_comma_separator}}',
  formatMoney: function(cents, format) { /* returns formatted string */ }
};

window.__MONEY_FORMAT__ = 'EUR {{amount_with_comma_separator}}';

window.shop = {
  klaviyoCompanyId: 'Wz7REr',
  campaign: { threshold: 0 }
};

// Stub third-party globals
window._swat = { /* empty */ };
window.SwymCallbacks = [];
window.dataLayer = window.dataLayer || [];
```

---

## 13. Verification Checklist

For each template, verify:
- [ ] All `data-*` attributes present as specified
- [ ] All `<script type="application/ld+json">` blocks valid
- [ ] All `<img>` tags have valid `src` pointing to local assets
- [ ] All `<a>` tags have valid `href` (relative or absolute)
- [ ] All `data-component` values match component registry
- [ ] All `data-variant-*` attributes have values (product pages)
- [ ] All `data-money-format` attributes present
- [ ] `window.Shopify` shim loaded before app.js
- [ ] `fonts.ferm-open-source.css` loaded (not original fonts.css)
- [ ] No references to `cdn.shopify.com` for JS/CSS
- [ ] No references to third-party CDN domains
