# Ferm Living Thin Integration — Status (2026-08-27)

## PHASE 1: ONE-PRODUCT THIN BRIDGE — IMPLEMENTATION COMPLETE

### What Was Built

**Architecture proven:** WC → adapter → FermPageData → frozen DOM → frozen JS

### Files Created (NEW)
| File | Purpose | Lines |
|------|---------|-------|
| `sections/section-product.php` | Frozen Ferm product DOM (overrides engine) | ~475 |
| `js/ferm-cart-bridge.js` | Shopify shim + WC AJAX cart bridge | ~200 |

### Files Modified
| File | Change |
|------|--------|
| `composer.php` | Added `ferm_build_product_page_data()` mapper (~130 lines), WC AJAX handlers (~80 lines), product JS enqueue |
| `js/ferm-product.js` | Rewritten for frozen DOM selectors (was AETHER-shaped) |
| `manifest.json` | Added `ferm-cart-bridge.js` and `ferm-product.js` entries |

### Files Untouched
- All adapter files (KEEP)
- All other section/component files (KEEP)
- All CSS files (KEEP — frozen CSS already in pack)
- WordPress core (KEEP)
- WooCommerce (KEEP)
- AUREON theme (KEEP)

### Data Flow
```
WooCommerce product
    ↓
aether_adapter_product()  (existing, UNCHANGED)
    ↓
section-product.php receives $sectionData
    ↓
ferm_build_product_page_data($sectionData)  (NEW — thin mapper)
    ↓
window.FermPageData = { ... }  (JSON, public-safe)
    ↓
Frozen Ferm HTML DOM  (verbatim from source)
    ↓
Frozen Ferm CSS  (verbatim)
    ↓
ferm-product.js  (reads FermPageData, NOT Shopify objects)
    ↓
ferm-cart-bridge.js  (intercepts Shopify cart calls → WC AJAX)
```

### FermPageData Schema
```json
{
  "product_id": 123,
  "variant_id": 123,
  "sku": "1104263489",
  "title": "Product Name",
  "url": "/product/slug/",
  "price_cents": 359500,
  "price_html": "<bdi>EUR 3.595,00</bdi>",
  "currency": "EUR",
  "gallery": [{"src": "...", "alt": "..."}],
  "colors": [{"name": "Off-White", "hex": "#FDF5E6", "url": "..."}],
  "color_name": "Off-White",
  "sizes": ["S", "M", "L"],
  "description": "<p>...</p>",
  "badge": "Sale",
  "tagline": "",
  "dimensions": "W: 148 x H: 76.5 x D: 84 cm",
  "weight": "48.5 kg",
  "seat_height": "41.0 cm",
  "backrest_height": "35.5 cm",
  "material": "...",
  "care": "...",
  "dimension_drawing": "...",
  "is_mto": false,
  "sample_url": "",
  "delivery_time": 56,
  "delivery_time_us": 119,
  "usps": ["Handmade in Europe", ...],
  "trust": []
}
```

### Cart Bridge Endpoints
| Shopify Endpoint | WC Handler | Action |
|-----------------|------------|--------|
| `POST /cart/add.js` | `ferm_wc_ajax_cart_add` | WC()->cart->add_to_cart() |
| `POST /cart/update.js` | `ferm_wc_ajax_cart_update` | WC()->cart->set_quantity() |
| `GET /cart.js` | `ferm_wc_ajax_cart_get` | Build Shopify-format response |

### PHP Syntax: ✅ Clean
### JSON Validity: ✅ Valid

## REMAINING VERIFICATION (requires running WordPress)
1. Visual parity test: standalone vs connected at 1440px and 390px
2. Network test: no Shopify/Clerk/external API calls
3. Cart bridge test: add-to-cart flow works end-to-end
4. Accordion, gallery, sticky bar, back button all functional

## BLOCKED ON
- Running WordPress instance with WooCommerce + AUREON + Ferm pack active
- Real product data in WooCommerce
- User verification of visual parity

## KEY ARCHITECTURE DECISION
The pack's `sections/section-product.php` OVERRIDES the engine's default.
It renders the frozen Ferm DOM directly — NOT via aether_render_component().
This preserves the original Ferm presentation structure exactly.

## NEXT STEP
After user verifies visual parity on one product:
→ Scale to catalog/archive pages
→ Scale to collection/filter pages
→ Scale to homepage
→ Scale to cart page
→ Scale to blog/content pages
→ Scale to search
→ Scale to checkout/account
