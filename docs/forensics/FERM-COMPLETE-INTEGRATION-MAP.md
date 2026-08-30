# Ferm Living — Complete Integration Map

**Source:** Frozen Ferm frontend → AUREON WordPress/WooCommerce
**Date:** 2026-08-26
**Architecture:** COPY COMPLETE FRONTEND + THIN AUREON INTEGRATION

---

## 1. Integration Architecture

```
GOLDEN AUREON CORE
       ↓
THIN INTEGRATION BRIDGE (bridge.js + PHP templates)
       ↓
COMPLETE FERM PRESENTATION (HTML/CSS/JS/fonts/images)
```

### What Stays from Frozen Source
- ALL HTML structure (DOM, classes, IDs, data attributes)
- ALL CSS (compiled Tailwind + component styles)
- ALL JS (app.js bundle + product.js + customer.js + speedblitz.js + cart-page.js)
- ALL fonts (Canela + KHTeka or Fraunces/Inter)
- ALL images (referenced subset)
- ALL animations/keyframes
- ALL responsive behavior
- ALL data attributes

### What Gets Bridged
- Shopify cart API → WooCommerce cart API
- Shopify global object → JS shim
- Shopify section rendering → Custom WP bridge
- Shopify Liquid data → PHP adapter output
- Shopify customer → WooCommerce customer
- Shopify search → WordPress search

---

## 2. File Change Map

### 2a. Files to PRESERVE (AUREON core — do not modify)

| File | Action | Reason |
|------|--------|--------|
| `frontend/views/loader.php` | PRESERVE | Engine kernel |
| `frontend/views/design.php` | PRESERVE | Pack resolution |
| `frontend/views/registry.php` | PRESERVE | Section registry |
| `frontend/views/renderer.php` | PRESERVE | Component renderer |
| `frontend/views/composer.php` | PRESERVE | Shell composition |
| `frontend/views/viewmodel.php` | PRESERVE | Data normalization |
| `frontend/views/assets.php` | MODIFY (minimal) | Add pack-first asset loading |
| `frontend/adapters/*.php` | PRESERVE | All 23 adapters |
| `frontend/sections/*.php` | PRESERVE | Base sections |
| `frontend/manifest/components.php` | PRESERVE | Component manifest |
| `frontend/tokens/tokens.php` | PRESERVE | Token defaults |
| `aureon/theme/functions.php` | PRESERVE | Theme setup |
| `aureon/theme/inc/frontend.php` | MODIFY (minimal) | Add luxury pack asset handling |
| `aureon/theme/header.php` | PRESERVE | Opens document |
| `aureon/theme/footer.php` | PRESERVE | Closes document |
| `aureon/theme/front-page.php` | PRESERVE | Homepage routing |
| `aureon/theme/inc/aether-cart.php` | PRESERVE | WC cart fragments |

### 2b. Files to ARCHIVE (old Ferm rebuild — no longer needed)

| File | Action | Reason |
|------|--------|--------|
| `frontend/designs/fermliving/css/ferm.css` | ARCHIVE | Replaced by frozen compiled CSS |
| `frontend/designs/fermliving/css/ferm-shell.css` | ARCHIVE | Replaced by frozen compiled CSS |
| `frontend/designs/fermliving/css/ferm-homepage.css` | ARCHIVE | Replaced by frozen compiled CSS |
| `frontend/designs/fermliving/css/ferm-product.css` | ARCHIVE | Replaced by frozen compiled CSS |
| `frontend/designs/fermliving/css/ferm-commerce.css` | ARCHIVE | Replaced by frozen compiled CSS |
| `frontend/designs/fermliving/css/ferm-archive.css` | ARCHIVE | Replaced by frozen compiled CSS |
| `frontend/designs/fermliving/css/ferm-content.css` | ARCHIVE | Replaced by frozen compiled CSS |
| `frontend/designs/fermliving/css/frozen-original.css` | ARCHIVE | Reference file, no longer needed |
| `frontend/designs/fermliving/js/ferm.js` | ARCHIVE | Replaced by frozen app.js |
| `frontend/designs/fermliving/js/ferm-shell.js` | ARCHIVE | Replaced by frozen app.js |
| `frontend/designs/fermliving/js/ferm-product.js` | ARCHIVE | Replaced by frozen product.js |
| `frontend/designs/fermliving/js/ferm-commerce.js` | ARCHIVE | Replaced by frozen cart-page.js |
| `frontend/designs/fermliving/js/ferm-archive.js` | ARCHIVE | Folded into frozen app.js |
| `frontend/designs/fermliving/js/ferm-homepage.js` | ARCHIVE | Folded into frozen app.js |
| `frontend/designs/fermliving/js/ferm-content.js` | ARCHIVE | Folded into frozen app.js |
| `frontend/designs/fermliving/components/shell/*.php` | ARCHIVE | Replaced by frozen HTML shell |
| `frontend/designs/fermliving/components/cards/*.php` | ARCHIVE | Replaced by frozen HTML cards |
| `frontend/designs/fermliving/components/product/*.php` | ARCHIVE | Replaced by frozen HTML product |
| `frontend/designs/fermliving/components/content/*.php` | ARCHIVE | Replaced by frozen HTML content |

### 2c. Files to CREATE (frozen source copy + bridge)

| File | Action | Source |
|------|--------|--------|
| `frontend/designs/fermliving/css/ferm-compiled.css` | **COPY** | `cdn/shop/t/164/assets/app.prettified.css` |
| `frontend/designs/fermliving/fonts/fonts.css` | **COPY** | `cdn/shop/t/164/assets/fonts.fd2d67c5ce.css` |
| `frontend/designs/fermliving/fonts/CanelaText-Regular.woff2` | **COPY** | Frozen source |
| `frontend/designs/fermliving/fonts/CanelaText-Regular.woff` | **COPY** | Frozen source |
| `frontend/designs/fermliving/fonts/KHTeka-Regular.woff2` | **COPY** | Frozen source |
| `frontend/designs/fermliving/fonts/KHTeka-Regular.woff` | **COPY** | Frozen source |
| `frontend/designs/fermliving/fonts/KHTeka-RegularItalic.woff2` | **COPY** | Frozen source |
| `frontend/designs/fermliving/fonts/KHTeka-RegularItalic.woff` | **COPY** | Frozen source |
| `frontend/designs/fermliving/fonts/KHTeka-Medium.woff2` | **COPY** | Frozen source |
| `frontend/designs/fermliving/fonts/KHTeka-Medium.woff` | **COPY** | Frozen source |
| `frontend/designs/fermliving/fonts/KHTeka-MediumItalic.woff2` | **COPY** | Frozen source |
| `frontend/designs/fermliving/fonts/KHTeka-MediumItalic.woff` | **COPY** | Frozen source |
| `frontend/designs/fermliving/js/app.js` | **COPY** | `cdn/shop/t/164/assets/app.1e7cf79a09.js` |
| `frontend/designs/fermliving/js/product.js` | **COPY** | `cdn/shop/t/164/assets/product.fa97565a5f.js` |
| `frontend/designs/fermliving/js/customer.js` | **COPY** | `cdn/shop/t/164/assets/customer.5de68fbefc.js` |
| `frontend/designs/fermliving/js/speedblitz.js` | **COPY** | `cdn/shop/t/164/assets/speedblitz.min.95accfb9a4.js` |
| `frontend/designs/fermliving/js/cart-page.js` | **COPY** | `cdn/shop/t/164/assets/cart-page.4c84950b1c.js` |
| `frontend/designs/fermliving/js/bridge.js` | **CREATE** | Shopify-to-WooCommerce bridge |
| `frontend/designs/fermliving/templates/page-home.php` | **CREATE** | Frozen homepage HTML + PHP data injection |
| `frontend/designs/fermliving/templates/page-product.php` | **REWRITE** | Frozen PDP HTML + PHP data injection |
| `frontend/designs/fermliving/templates/archive-product.php` | **REWRITE** | Frozen CLP HTML + PHP data injection |
| `frontend/designs/fermliving/templates/page-blog.php` | **REWRITE** | Frozen blog HTML + PHP data injection |
| `frontend/designs/fermliving/templates/page-article.php` | **REWRITE** | Frozen article HTML + PHP data injection |
| `frontend/designs/fermliving/templates/page-cart.php` | **REWRITE** | Frozen cart HTML + PHP data injection |
| `frontend/designs/fermliving/templates/page-checkout.php` | **REWRITE** | Frozen checkout HTML + PHP data injection |
| `frontend/designs/fermliving/templates/page-account.php` | **REWRITE** | Frozen account HTML + PHP data injection |
| `frontend/designs/fermliving/templates/page-about.php` | **REWRITE** | Frozen about HTML + PHP data injection |
| `frontend/designs/fermliving/templates/page-contact.php` | **REWRITE** | Frozen contact HTML + PHP data injection |
| `frontend/designs/fermliving/templates/page-search.php` | **REWRITE** | Frozen search HTML + PHP data injection |
| `frontend/designs/fermliving/templates/page-404.php` | **REWRITE** | Frozen 404 HTML + PHP data injection |

### 2d. Files to MODIFY (minimal changes to AUREON core)

| File | Change | Scope |
|------|--------|-------|
| `frontend/views/assets.php` | Add luxury pack CSS/JS enqueuing path | ~20 lines |
| `aureon/theme/inc/frontend.php` | Add luxury pack asset loading in `aether_enqueue_luxury_assets()` | ~15 lines |
| `frontend/designs/fermliving/manifest.json` | Update to reference frozen source assets | ~30 lines |
| `frontend/designs/fermliving/composer.php` | Simplify to frozen-source data injection | ~50 lines |
| `frontend/designs/fermliving/tokens.php` | Keep for fallback data, reduce scope | ~30 lines |

---

## 3. Bridge Layer Specification

### 3a. `js/bridge.js` — Shopify-to-WooCommerce API Bridge

**Purpose:** Make the frozen `app.js` work with WooCommerce by shimming Shopify APIs.

**Lines:** ~150

#### What It Provides

```javascript
// 1. Shopify Global Shim
window.Shopify = {
  shop: '',
  locale: document.documentElement.lang || 'en',
  currency: { active: 'EUR', rate: '1.0' },
  routes: { root: '/' },
  formatMoney: function(cents) {
    // Bridge to WooCommerce price formatting
    // Uses wp_localize_script data
  }
};

// 2. Cart API Bridge
// Intercepts fetch() calls to /cart/*.js
// Routes to WooCommerce AJAX endpoints:
//   /cart/add.js  → /?wc-ajax=add_to_cart
//   /cart/update.js → /?wc-ajax=update_cart
//   /cart/change.js → /?wc-ajax=update_cart

// 3. Section Rendering Bridge
// When app.js requests sections in cart API response:
//   { sections: { "cart-drawer": html, "main-cart": html } }
// The bridge fetches rendered HTML from WordPress and returns it
// in the expected format.

// 4. Section Load Event Bridge
// Dispatches 'shopify:section:load' events after AJAX updates
// so app.js re-initializes components.

// 5. Money Format Bridge
// Reads wc_price data from wp_localize_script
// Provides Shopify.formatMoney() compatibility
```

#### Cart API Contract

**Shopify Pattern (what app.js expects):**
```javascript
// app.js calls:
fetch('/cart/add.js', {
  method: 'POST',
  body: JSON.stringify({
    items: [{ id: variantId, quantity: 1 }],
    sections: 'cart-drawer,main-cart'
  })
})

// Expects response:
{
  items: [...],
  sections: {
    "cart-drawer": "<div data-component='cartDrawer'>...</div>",
    "main-cart": "<div data-component='cartMain'>...</div>"
  }
}
```

**WooCommerce Bridge (what bridge.js provides):**
```javascript
// bridge.js intercepts and translates:
fetch('/cart/add.js', ...) 
  → fetch('/?wc-ajax=add_to_cart', {
      method: 'POST',
      body: new FormData().append('product_id', id).append('quantity', qty)
    })
  → Returns Shopify-shaped JSON response
  → Includes rendered HTML for cart-drawer and main-cart sections
```

### 3b. PHP Template Bridge — Frozen HTML + WordPress Data

**Pattern:**
```php
<?php
// Freeze the complete Ferm HTML structure
// Replace only dynamic/business placeholders with PHP
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <?php wp_head(); ?>
  <!-- Frozen source CSS + fonts -->
</head>
<body <?php body_class(); ?> data-money-format="<?php echo esc_attr( wc_price(1) ); ?>">
  
  <!-- Frozen header shell — PHP injects dynamic data -->
  <?php get_template_part('parts/ferm-header'); ?>
  
  <!-- Frozen main content — PHP injects products/pages -->
  <main class="content" id="main-content">
    <?php
    // Page-specific frozen HTML with PHP data injection
    // Example: foreach($products as $product) {
    //   // Frozen product card HTML
    //   // with PHP-injected values for id, name, price, image, url
    // }
    ?>
  </main>
  
  <!-- Frozen footer shell -->
  <?php get_template_part('parts/ferm-footer'); ?>
  
  <!-- Frozen source JS + bridge -->
  <?php wp_footer(); ?>
</body>
</html>
```

---

## 4. Data Injection Points

### What PHP Provides to Frozen HTML

| Data Point | Source | Injection Method |
|-----------|--------|-----------------|
| Product name | `WC_Product::get_name()` | `<?php echo esc_html($product->get_name()); ?>` |
| Product price | `wc_price()` | `<?php echo wc_price($product->get_price()); ?>` |
| Product image | `wp_get_attachment_url()` | `<?php echo esc_url($image_url); ?>` |
| Product URL | `get_permalink()` | `<?php echo esc_url($product_url); ?>` |
| Product ID | `WC_Product::get_id()` | `data-product-id="<?php echo esc_attr($id); ?>"` |
| Category name | `WP_Term::get_name()` | `<?php echo esc_html($term->get_name()); ?>` |
| Category image | `get_term_meta()` | `<?php echo esc_url($image); ?>` |
| Cart count | `WC()->cart->get_cart_contents_count()` | `data-cart-count="<?php echo $count; ?>"` |
| Cart total | `WC()->cart->get_cart_total()` | `<?php echo WC()->cart->get_cart_total(); ?>` |
| User name | `wp_get_current_user()` | `<?php echo esc_html($user->display_name); ?>` |
| Search value | `get_search_query()` | `value="<?php echo esc_attr(get_search_query()); ?>"` |
| Site name | `get_bloginfo('name')` | `<?php echo esc_html(get_bloginfo('name')); ?>` |
| Navigation | `wp_nav_menu()` | Custom walker matching Ferm HTML structure |
| Currency | `get_woocommerce_currency()` | `data-shop-currency="<?php echo esc_attr($currency); ?>"` |
| Money format | WooCommerce settings | `data-money-format="<?php echo esc_attr($format); ?>"` |

### Data Attributes to Preserve

All frozen source `data-*` attributes must be preserved in PHP templates:

```php
<!-- Product card — all data attributes from frozen source -->
<div class="product"
     data-component="productThumb"
     data-product-click=""
     data-product-id="<?php echo esc_attr($product->get_id()); ?>"
     data-product-title="<?php echo esc_attr($product->get_name()); ?>"
     data-product-price="<?php echo esc_attr($product->get_price()); ?>"
     data-product-handle="<?php echo esc_attr($product->get_slug()); ?>"
     data-product-url="<?php echo esc_url($product_url); ?>"
     data-product-type="<?php echo esc_attr($product->get_type()); ?>"
     data-product-image="<?php echo esc_url($image_url); ?>">
  
  <!-- Cart drawer -->
  <div data-component="cartDrawer"
       data-cart-drawer-content=""
       data-cart-drawer-footer="">
  </div>
  
  <!-- Cart count badges -->
  <span data-cart-count><?php echo $cart_count; ?></span>
</div>
```

---

## 5. Asset Loading Contract

### manifest.json Update

```json
{
  "id": "fermliving",
  "label": "Ferm Living",
  "version": "3.0.0",
  "description": "Complete frozen Ferm frontend with thin AUREON integration",
  "assets": {
    "css": [
      "css/ferm-compiled.css",
      "fonts/fonts.css"
    ],
    "js": [
      { "file": "js/bridge.js", "deps": [], "priority": "first" },
      { "file": "js/app.js", "deps": ["ferm-bridge"], "priority": "normal" },
      { "file": "js/product.js", "deps": ["ferm-app"], "priority": "normal" },
      { "file": "js/customer.js", "deps": ["ferm-app"], "priority": "normal" },
      { "file": "js/speedblitz.js", "deps": ["ferm-app"], "priority": "last" },
      { "file": "js/cart-page.js", "deps": ["ferm-app"], "priority": "normal" }
    ]
  },
  "templates": {
    "front-page": "templates/page-home.php",
    "single-product": "templates/page-product.php",
    "archive-product": "templates/archive-product.php",
    "page": "templates/page-static.php",
    "single-post": "templates/page-article.php",
    "home": "templates/page-blog.php",
    "cart": "templates/page-cart.php",
    "checkout": "templates/page-checkout.php",
    "myaccount": "templates/page-account.php",
    "search": "templates/page-search.php",
    "404": "templates/page-404.php"
  },
  "components": {
    "overrides": {}
  },
  "sections": {
    "overrides": {}
  },
  "composition": {
    "file": "composer.php",
    "hooks": ["aether_frontpage_sections"]
  }
}
```

### Asset Enqueue Priority

```
1. bridge.js          (MUST load before app.js)
2. app.js             (main theme bundle)
3. product.js         (PDP behaviors)
4. customer.js        (account behaviors)
5. speedblitz.js      (lazy loading, last)
6. cart-page.js       (cart page only)
7. WordPress core jQuery (for WC AJAX compatibility)
```

---

## 6. Page Family Integration Map

### 6a. Homepage

| Element | Frozen Source | WordPress Bridge |
|---------|--------------|-----------------|
| Shell (header/footer) | Static HTML in frozen source | PHP template parts with data injection |
| Hero section | `<div data-component="heroWithCta">` | PHP injects hero image/CTA from Customizer |
| Category grid | `<div data-component="categoryOverview">` | PHP loops `WC Product Categories` |
| Product grid | `<div data-component="productThumb">` | PHP loops `WC Product Query` (bestsellers) |
| Editorial split | Static HTML | PHP injects image/text from page content |
| Room grid | Static HTML | PHP injects room data from Customizer/options |
| Newsletter | `<div data-component="newsletter">` | Klaviyo embed or PHP form |

### 6b. Product Page (PDP)

| Element | Frozen Source | WordPress Bridge |
|---------|--------------|-----------------|
| Gallery | `<div data-component="emblaSlider">` | PHP injects `WC_Product::get_gallery_image_ids()` |
| Product info | `<div data-component="addToCart">` | PHP injects name, price, variants, description |
| Variant selector | `<select data-variant-select>` | PHP loops `WC_Product::get_available_variations()` |
| Add to cart | `<button data-button-add-to-cart>` | Form posts to `/?wc-ajax=add_to_cart` |
| Recommendations | `<div data-component="recommendations">` | PHP loops related products or Clerk.io |
| Accordion | `<div data-component="accordion">` | PHP injects product attributes/descriptions |

### 6c. Collection Page (CLP)

| Element | Frozen Source | WordPress Bridge |
|---------|--------------|-----------------|
| Collection header | `<h1>` with category name | `<?php single_term_title(); ?>` |
| Product grid | `<div data-component="productThumb">` | PHP loops `WC Product Query` |
| Sort/filter | `<div data-component="collectionFilters">` | PHP/WC query vars |
| Pagination | Standard pagination | `woocommerce_pagination()` |

### 6d. Cart Page

| Element | Frozen Source | WordPress Bridge |
|---------|--------------|-----------------|
| Cart items | `<div data-component="cartMain">` | PHP loops `WC()->cart->get_cart()` |
| Quantity controls | `<button data-quantity-button>` | AJAX update via bridge.js |
| Remove item | `<button data-remove-item>` | AJAX remove via bridge.js |
| Cart summary | Price totals | `WC()->cart->get_totals()` |
| Checkout button | Link to `/checkout/` | `wc_get_checkout_url()` |

### 6e. Account Pages

| Element | Frozen Source | WordPress Bridge |
|---------|--------------|-----------------|
| Login form | `<form action="/account/login">` | `woocommerce_login_form()` |
| Register form | `<form action="/account/register">` | `woocommerce_register_form()` |
| Orders list | Account dashboard | `wc_get_account_orders()` |
| Addresses | Account section | WooCommerce account functions |

---

## 7. WooCommerce Template Routing

### Theme Template Overrides

```
aureon/theme/
├── header.php              → Opens document, calls aether_compose_header()
├── footer.php              → Calls aether_compose_footer(), closes document
├── front-page.php          → Homepage (renders frozen homepage template)
├── single-product.php      → Product page (renders frozen PDP template)
├── archive-product.php     → Collection page (renders frozen CLP template)
├── home.php                → Blog listing (renders frozen blog template)
├── single.php              → Single article (renders frozen article template)
├── page.php                → Static pages (renders frozen page template)
├── search.php              → Search results (renders frozen search template)
├── 404.php                 → 404 (renders frozen 404 template)
├── cart.php                → Cart (renders frozen cart template)
├── checkout/
│   └── form-checkout.php   → Checkout (renders frozen checkout template)
├── myaccount/
│   └── my-account.php      → Account (renders frozen account template)
└── woocommerce/
    └── loop/
        └── wrapper.php     → Product loop wrapper (minimal)
```

### WC Template Override Strategy

**Minimal overrides.** The frozen source provides the complete presentation. WC templates are only overridden where WooCommerce forces template output (loop wrapper, cart totals, checkout form structure).

---

## 8. Cart Drawer Bridge (Critical Path)

The cart drawer is the most complex integration point because `app.js` uses Shopify's section rendering to update it.

### Shopify Pattern
```
User clicks "Add to Cart"
  → app.js POSTs to /cart/add.js with { sections: "cart-drawer,main-cart" }
  → Server returns JSON with rendered HTML for both sections
  → app.js parses HTML, extracts [data-component="cartDrawer"]
  → app.js replaces cart drawer innerHTML
  → app.js updates cart count badges
  → app.js dispatches 'cart:update' event
```

### WooCommerce Bridge Pattern
```
User clicks "Add to Cart"
  → bridge.js intercepts POST to /cart/add.js
  → bridge.js translates to WC AJAX: /?wc-ajax=add_to_cart
  → bridge.js fetches rendered cart HTML from:
      - /cart (for main-cart section)
      - A custom endpoint that renders cart-drawer HTML
  → bridge.js包装 response in Shopify format:
      { sections: { "cart-drawer": html, "main-cart": html } }
  → app.js processes as normal
```

### Custom WP Endpoint for Cart Drawer Rendering

```php
// Add to theme functions.php or a plugin
add_action('wp_ajax_ferm_cart_drawer', 'ferm_render_cart_drawer');
add_action('wp_ajax_nopriv_ferm_cart_drawer', 'ferm_render_cart_drawer');

function ferm_render_cart_drawer() {
  // Render the cart drawer HTML using WC cart data
  // Output the frozen source cart drawer structure
  // with PHP-injected cart items, totals, etc.
  // Return as JSON: { html: "..." }
}
```

---

## 9. Section Rendering Bridge

### What app.js Expects (Shopify Section Rendering)
```javascript
// After cart operations, app.js calls:
fetch('/cart/add.js', {
  body: JSON.stringify({
    items: [{ id, quantity }],
    sections: 'cart-drawer,main-cart'
  })
})

// Expects:
{
  sections: {
    "cart-drawer": "<div data-component='cartDrawer'>...rendered HTML...</div>",
    "main-cart": "<div data-component='cartMain'>...rendered HTML...</div>"
  }
}
```

### WordPress Bridge
```javascript
// bridge.js provides a custom fetch wrapper:
const originalFetch = window.fetch;
window.fetch = function(url, options) {
  if (url.includes('/cart/add.js') || url.includes('/cart/update.js') || url.includes('/cart/change.js')) {
    return handleCartRequest(url, options);
  }
  return originalFetch(url, options);
};

async function handleCartRequest(url, options) {
  // 1. Parse the original request
  const body = JSON.parse(options.body);
  
  // 2. Translate to WooCommerce AJAX
  const wcResponse = await originalFetch('/?wc-ajax=ferm_cart_operation', {
    method: 'POST',
    body: JSON.stringify(body)
  });
  
  // 3. Get rendered HTML for sections
  const sectionHtml = {};
  for (const sectionId of body.sections.split(',')) {
    sectionHtml[sectionId] = await fetchSectionHtml(sectionId.trim());
  }
  
  // 4. Return Shopify-shaped response
  return {
    json: () => Promise.resolve({
      items: wcResponse.items,
      sections: sectionHtml
    })
  };
}
```

---

## 10. Implementation Phases

### Phase 1: Asset Copy (No code changes)
Copy frozen source CSS, JS, fonts, images into design pack.

### Phase 2: Bridge Layer
Create `bridge.js` (Shopify API shim + cart rendering bridge).

### Phase 3: Template Conversion
Convert frozen HTML pages to PHP templates with data injection.

### Phase 4: Integration Testing
Test in WordPress runtime: navigation, cart, checkout, search.

### Phase 5: Visual Verification
Compare WordPress screenshots against frozen source at 1440/1024/768/390px.

---

## 11. Stop Conditions

Implementation STOP if any of these are true:

- [ ] Frozen CSS cannot be served as-is (selectors broken by WP theme)
- [ ] Frozen JS requires Shopify runtime to function (beyond what bridge provides)
- [ ] Cart operations cannot be bridged to WooCommerce without rewriting app.js
- [ ] Template conversion requires rewriting >20% of the frozen HTML structure
- [ ] AUREON core must be significantly modified to serve the frozen frontend

---

## 12. Expected Result

```
GOLDEN AUREON CORE (unchanged)
       ↓
THIN BRIDGE (~300 lines total: 150 JS + 150 PHP)
       ↓
COMPLETE FERM PRESENTATION
  ├── Frozen compiled CSS (12,879 lines, zero modifications)
  ├── Frozen app.js (151KB, zero modifications)
  ├── Frozen product.js, customer.js, speedblitz.js, cart-page.js
  ├── Frozen fonts (Canela + KHTeka, or Fraunces/Inter)
  ├── Frozen HTML structure (in PHP templates)
  └── Frozen images (referenced subset)
```

The complete Ferm frontend remains the source of truth. AUREON provides only the business/data layer. The bridge translates between the two.
