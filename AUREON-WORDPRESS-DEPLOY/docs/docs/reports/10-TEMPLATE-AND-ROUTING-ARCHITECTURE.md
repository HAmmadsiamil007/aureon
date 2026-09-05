# 10 — TEMPLATE AND ROUTING ARCHITECTURE

## WordPress Template Hierarchy

```
Request → WordPress Query → Template Hierarchy → Template File
```

## AUREON Template Override Chain

```
template_include filter
    ↓
Priority 99: aureon_aether_wc_page_templates()
  - is_cart() → cart.php
  - is_checkout() → checkout/form-checkout.php
  - is_account_page() (not complete-page) → myaccount/my-account.php
  - order-received → woocommerce/checkout/thankyou.php
    ↓
Priority 998: aureon_ferm_template_include()
  - complete_page + not checkout + not logged-in account → ferm-page.php
```

## Route → Template Map

| Route | Complete-Page | Component Mode |
|-------|---------------|----------------|
| `/` | ferm-page.php → index.html | front-page.php |
| `/shop/` | ferm-page.php → collections/furniture.html | archive-product.php |
| `/product/*` | ferm-page.php → products/{slug}.html | single-product.php |
| `/product-category/*` | ferm-page.php → collections/{slug}.html | archive-product.php |
| `/cart/` | cart.php (WC native) | cart.php |
| `/checkout/` | checkout/form-checkout.php (WC native) | checkout/form-checkout.php |
| `/my-account/` (logged in) | myaccount/my-account.php (WC native) | myaccount/my-account.php |
| `/my-account/` (logged out) | ferm-page.php → account/login.html | myaccount/my-account.php |
| `/?s=*` | ferm-page.php → blogs/stories.html | index.php |
| `/blog/` | ferm-page.php → blogs/stories.html | index.php |
| `/about/` | ferm-page.php → pages/about-ferm-living.html | page.php |
| `/contact/` | ferm-page.php → pages/contact.html | page.php |
| `/*` (fallback) | ferm-page.php → index.html | index.php |

## WooCommerce Template Routing

WC's template loader only handles product/shop archives. AUREON adds:

```php
// Priority 99
add_filter('template_include', 'aureon_aether_wc_page_templates', 99);
```

Routes: cart → cart.php, checkout → form-checkout.php, account → my-account.php, order-received → thankyou.php.

## Complete-Page Routing

```php
// Priority 998 (after WC at priority 99)
add_filter('template_include', 'aureon_ferm_template_include', 998);
```

Bypasses AETHER shell, serves frozen HTML via `ferm-page.php`.

## Homepage Section Composition

```php
// front-page.php
$sections = apply_filters('aether_frontpage_sections', [
    'hero', 'categories', 'bestsellers', 'reviews', 'faq', 'newsletter'
]);
```

Ferm overrides: `['hero', 'categories', 'editorial-split', 'bestsellers', 'room-grid', 'newsletter']`
