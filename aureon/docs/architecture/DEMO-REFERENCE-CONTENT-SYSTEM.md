# Ferm Living Demo/Reference Content System

## Architecture Overview

```
ONE-TIME SOURCE COLLECTION
        ↓
COMPLETE CURATED DEMO DATASET
        ↓
REMOTE IMAGE URL REFERENCES
        +
SMALL LOCAL FALLBACKS
        ↓
CONTENT RESOLVER (composer.php)
        ↓
SAME FROZEN FERM PRESENTATION
```

## Data Flow

### Real WooCommerce Products
```
WordPress/WooCommerce Database
        ↓
aether_demo_products filter (hidden when real exist)
        ↓
aether_adapter_wc_products_data filter
        ↓
Frozen Ferm HTML + JS bridges
        ↓
Complete Ferm Presentation
```

### Demo Products (No Real Products)
```
demo/demo-products.json
        ↓
ferm_demo_products filter
        ↓
ferm_get_demo_products_for_collection()
        ↓
FermPageData.collection.products
        ↓
ferm-data-shims.js collection bridge
        ↓
Frozen Ferm HTML with demo content
```

## Page Family Coverage

| Page Family | Frozen HTML | Demo Data Bridge | Collection Bridge |
|-------------|------------|------------------|-------------------|
| Homepage | index.html | ✓ | - |
| Shop/All Products | furniture.html (fallback) | ✓ | ✓ |
| Furniture | furniture.html | ✓ | ✓ |
| Lighting | lighting.html | ✓ | ✓ |
| Accessories | accessories.html | ✓ | ✓ |
| Kids | furniture.html (fallback) | ✓ | ✓ |
| Kitchen | furniture.html (fallback) | ✓ | ✓ |
| Textiles | furniture.html (fallback) | ✓ | ✓ |
| Rugs | furniture.html (fallback) | ✓ | ✓ |
| Outdoor | furniture.html (fallback) | ✓ | ✓ |
| Sofas | furniture.html (fallback) | ✓ | ✓ |
| Product Detail | _generic-product.html | ✓ | - |
| Search | search-bridge.js | ✓ | - |
| Cart | cart.html | ✓ | - |
| Checkout | checkout.html | ✓ | - |
| Account | login.html | ✓ | - |
| Blog/Stories | stories.html | ✓ | - |
| About | about-ferm-living.html | ✓ | - |
| Contact | contact.html | ✓ | - |
| Store Locator | store-locator.html | ✓ | - |
| 404 | 404.php | ✓ | - |

## Image Strategy

### Remote Demo Images
- Source: Shopify CDN (`cdn.shopify.com/s/files/1/0150/3336/8640/`)
- Format: Remote URL references (no local download)
- Fallback: `customizer-bridge.js` replaces failed loads with SVG placeholder
- Verification: HTTP status checked at build time

### Fallback Hierarchy
```
1. Custom client asset (WordPress Customizer)
2. Demo asset from demo-assets.json
3. Frozen Ferm HTML default
4. SVG placeholder (customizer-bridge.js)
```

## Real/Demo Transition

### Products
- 0 real WC products → ALL demo products visible
- 1+ real WC products → ALL demo products hidden
- Delete ALL real products → demo products return

### Categories
- 0 real WC categories → ALL demo categories visible
- 1+ real WC categories → ALL demo categories hidden
- Delete ALL real categories → demo categories return

## File Structure

```
aureon/frontend/designs/fermliving/
├── composer.php              # Data bridge (PHP)
├── manifest.json             # Design pack manifest
├── tokens.php                # Default options
├── index.html                # Homepage
├── cart.html                 # Cart page
├── checkout.html             # Checkout page
├── 404.php                   # 404 page
├── demo/
│   ├── demo-manifest.json    # Demo data manifest
│   ├── demo-products.json    # 66 curated demo products
│   ├── demo-categories.json  # 9 demo categories
│   ├── demo-collections.json # 4 demo collections
│   ├── demo-homepage.json    # Homepage demo data
│   ├── demo-navigation.json  # Navigation data
│   ├── demo-assets.json      # Asset configuration
│   └── demo-image-url-inventory.json  # Image URL catalog
├── products/
│   ├── _generic-product.html # Generic product template
│   ├── meridian-lamp-black.html
│   ├── rico-lounge-chair-raw-boucle-natural.html
│   └── rico-sofa-2-boucle-off-white.html
├── collections/
│   ├── furniture.html        # Furniture collection (primary)
│   ├── lighting.html         # Lighting collection
│   └── accessories.html      # Accessories collection
├── pages/
│   ├── about-ferm-living.html
│   ├── contact.html
│   └── store-locator.html
├── blogs/
│   └── stories.html
├── account/
│   └── login.html
└── cdn/shop/t/164/assets/
    ├── ferm-data-shims.js    # Data shims + collection bridge
    ├── customizer-bridge.js  # Customizer + image fallback
    ├── search-bridge.js      # Search overlay + demo search
    └── app.1e7cf79a09.js     # Ferm frontend application
```

## Golden Core Protection

The following directories are FROZEN and must NOT be modified:
- `aureon/frontend/views/`
- `aureon/frontend/adapters/`
- `aureon/frontend/components/`
- `aureon/frontend/sections/`
- `aureon/frontend/manifest/`
- `aureon/frontend/tokens/`
- `aureon/theme/`
- `aureon/plugin/`
- `aureon/ferm-page.php`

All demo content implementation lives in:
- `aureon/frontend/designs/fermliving/`
- `aureon/docs/`
