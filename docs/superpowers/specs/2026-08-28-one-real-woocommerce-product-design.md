# Design: One Real WooCommerce Product Integration

**Date:** 2026-08-28
**Status:** Approved
**Phase:** Single-product proof of concept

## Objective

Connect ONE real WooCommerce product (Meridian Lamp Black, #834) to the existing frozen Ferm product presentation through the thin AUREON bridge. Prove that real WC data can flow into the complete Ferm DOM without rebuilding the frontend.

## Architecture

```
WooCommerce DB (product #834)
    ↓
adapter-product.php (existing, unchanged)
    ↓
aether_adapter_product_data filter
    ↓
ferm_remap_product() in composer.php (existing)
    ↓
ferm_build_product_page_data() (NEW — thin mapper)
    ↓
window.FermPageData.product = { real WC data }
    ↓
ferm-data-shims.js merges into FermPageData context
    ↓
Ferm product.js reads FermPageData.product + DOM data-attributes
    ↓
Same frozen Ferm DOM, real data
```

### Boundaries

| Layer | Responsibility |
|-------|---------------|
| PHP (AUREON adapter) | Product data retrieval, pricing, stock |
| PHP (Ferm mapper) | Remap adapter data → FermPageData.product schema |
| PHP (ferm-page.php) | Serve frozen Ferm HTML, inject FermPageData via wp_localize_script |
| Ferm JS (product.js) | Read FermPageData, update DOM data-attributes, handle gallery/variants/CTA |
| Ferm JS (shims.js) | Intercept Shopify fetch calls, route to WC AJAX |
| WooCommerce | Cart, checkout, stock management, business logic |

### Product.js Data Source Rule

Before changing product.js, trace its actual data source.

Preferred:
```
FermPageData
→ existing Shopify-compatible shim (Shopify.product)
→ existing frozen product.js
```

Only if that cannot work:
```
FermPageData
→ minimal bridge JS
→ existing DOM
```

Do not rewrite the entire product.js.

### Non-Negotiable Constraints

- Frozen Ferm product HTML remains the presentation source of truth
- Do NOT create `section-product.php`
- Do NOT split the product page into sections/components
- Do NOT recreate the Ferm product DOM in PHP
- Do NOT rewrite Ferm CSS
- Do NOT rewrite Ferm presentation JS except where required to replace Shopify/business APIs
- Do NOT modify `adapter-product.php`
- Do NOT modify WooCommerce core
- Do NOT alter unrelated products
- PHP is a data/bridge layer only

## Test Product

| Field | Value |
|-------|-------|
| Product ID | 834 |
| Name | Meridian Lamp Black |
| Slug | meridian-lamp-black |
| SKU | FL-LAMP-MER-001 |
| Type | simple |
| Regular price | 189.00 |
| Stock status | instock |
| Category | lighting |
| Frozen template | `products/meridian-lamp-black.html` |
| Manifest mapping | `"meridian-lamp-black": "products/meridian-lamp-black.html"` |

## FermPageData.product Schema

```javascript
window.FermPageData.product = {
  id: 834,                    // WC product ID
  variant_id: null,           // null for simple product
  title: "Meridian Lamp Black",
  handle: "meridian-lamp-black",
  slug: "meridian-lamp-black",
  url: "/product/meridian-lamp-black/",  // actual WC permalink
  sku: "FL-LAMP-MER-001",
  price: 18900,               // cents (Ferm Shopify format)
  price_html: "...",          // WC-formatted price
  compare_at_price: null,
  currency: "...",            // from WC config
  availability: "in-stock",
  description: "...",         // real WC description
  gallery: [
    { src: "/path/to/image.jpg", alt: "Meridian Lamp Black" }
  ],
  options: [],
  variants: [],
  badge: null,
  product_type: "simple",
  tags: ["lighting"]
}
```

All values come from WooCommerce/AUREON. No hardcoded presentation values.

- `price` = normalized numeric representation required by Ferm JS (e.g., cents)
- `price_html` = actual WooCommerce-formatted output (from `wc_price()`)
- `currency` = actual WooCommerce currency (from `get_woocommerce_currency()`)
- `url` = actual WooCommerce permalink (from `get_permalink()`)

No hardcoded EUR formatting just to make this one test pass.

## Implementation Steps

### Step 1: Populate WC Product #834

Add real data to Meridian Lamp Black:
- Regular price: 189.00
- SKU: FL-LAMP-MER-001
- Description
- Category: lighting
- Images: copy from `C:\Users\hamma\Downloads\SiteOne-Crawler\fermliving-template-ready\cdn\shop\files\` (matching Meridian Lamp images)

**Image constraint:** Do not replace the frozen gallery structure. Do not create new gallery markup. Only replace existing image src/alt/data attributes with real local media.

### Step 2: Extend composer.php — Add `ferm_build_product_page_data()`

Create a new function that:
1. Hooks into `aether_adapter_product_data` filter (extending existing `ferm_product_data`)
2. Reads adapter output via the filter
3. Remaps to FermPageData.product schema
4. Injects via `wp_localize_script` as part of FermPageData

### Step 3: Update ferm-data-shims.js — Read FermPageData.product

Extend the FermPageData merge block to:
1. Read `FermPageData.product` if present
2. Update the Shopify product shim with real data
3. Set the body `data-template` attribute
4. Ensure Ferm product.js can consume the data

### Step 4: Verify DOM Update Path

Check that Ferm product.js:
1. Reads `FermPageData.product` (or Shopify.product shim)
2. Updates price elements
3. Updates SKU elements
4. Updates gallery images
5. Sets correct `data-variant-id` for add-to-cart
6. Handles CTA state (in-stock vs sold-out)

If product.js doesn't automatically update from FermPageData, add **minimal bridge JS** that:
- Updates specific DOM elements (price text, SKU text, image src, data-attribute values)
- Does NOT create new DOM elements
- Does NOT modify the DOM hierarchy
- Does NOT add new CSS classes or layout
- Only changes the text/src/attribute values of existing frozen DOM elements

This is a data injection into existing elements, not a product renderer.

### Step 5: Test Add-to-Cart

1. Click Add to Cart button
2. Verify FermCart.addItem() is called with correct variant ID
3. Verify WC AJAX receives the request
4. Verify cart count updates in header
5. No Shopify API calls

## Verification Checklist

### Data Flow
- [ ] `window.FermPageData.product` contains real WC data
- [ ] FermPageData fields match frozen DOM data-attribute hooks

**One authoritative product state:**
```
WooCommerce/AUREON
        ↓
FermPageData.product
        ↓
existing Ferm compatibility layer / DOM
```

Do not maintain separate:
- PHP product state
- FermPageData product state
- independent JS product state
- hardcoded HTML product state

Reference HTML may contain original placeholder/demo values, but runtime must replace them from the authoritative state.

### Visual Presentation
- [ ] Real price displayed (not hardcoded)
- [ ] Real SKU displayed
- [ ] Real product title displayed
- [ ] Real product images displayed
- [ ] Same layout as standalone Ferm product template

### Business Behavior
- [ ] Add-to-cart uses WooCommerce/AUREON
- [ ] Cart count updates in header
- [ ] Correct product ID sent to WC cart

### Network/Console
- [ ] Zero Shopify API calls
- [ ] Zero unexpected external requests
- [ ] Zero console errors
- [ ] Zero asset 404s

### Screenshots
- [ ] 1440px viewport captured
- [ ] 390px viewport captured
- [ ] Compare against standalone Ferm product template

## Files Changed

| File | Change |
|------|--------|
| `frontend/designs/fermliving/composer.php` | Add `ferm_build_product_page_data()` |
| `frontend/designs/fermliving/cdn/shop/t/164/assets/ferm-data-shims.js` | Extend FermPageData.product merge |
| WC product #834 | Populate real data |

## Success Criteria

```
PRODUCT_DYNAMIC_INTEGRATION_PASS
```

Returned only when all verification checkpoints pass. Includes:
- Product ID
- FermPageData.product snapshot
- Route
- Screenshot paths
- Network results
- Console results
- Cart result

## Scope Boundary

This is a ONE-PRODUCT proof only. Do NOT proceed to:
- Other products
- Collections/archive
- Cart page
- Checkout
- Search
- Account
- Any other page family

Until this product passes all verification checkpoints.

## Architecture Block Rule

If product #834 cannot be integrated without:
- creating `section-product.php`
- creating new visual components
- changing the DOM hierarchy
- rewriting the product presentation

**STOP and return `PRODUCT_INTEGRATION_ARCHITECTURE_BLOCKED`.**

Do not implement a workaround.
