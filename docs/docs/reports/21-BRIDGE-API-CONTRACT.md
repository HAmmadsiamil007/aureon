# 21 — BRIDGE API CONTRACT

## Bridge Boundary

The bridge connects client presentation to platform data/logic.

```
Client Frontend (HTML/CSS/JS)
    ↓
Bridge (composer.php + JS)
    ↓
AUREON/WooCommerce (data/logic)
```

## Bridge Components

### PHP Bridge (composer.php)
- Data filtering (aether_adapter_*_data)
- Product remapping (ferm_remap_product)
- Cart AJAX handlers
- FermPageData construction
- Demo content filtering
- Navigation normalization

### JS Bridge (ferm-data-shims.js)
- FermPageData global injection
- AJAX configuration
- Nonce handling

### Client Bridges
- search-bridge.js — search functionality
- cart-page.ferm.js — cart page interactions
- customizer-bridge.js — Customizer value application

## Data Mapping

### Product Data
```
WC Product Object
    ↓
ferm_remap_product()
    ↓
Ferm-compatible format (cents, Shopify schema)
```

### Cart Data
```
WC()->cart
    ↓
ferm_build_cart_response()
    ↓
Ferm cart format (items, total_price in cents)
```

### Navigation Data
```
wp_get_nav_menu_items()
    ↓
ferm_get_nav_menu()
    ↓
[{title, url, children}]
```

### Customizer Data
```
aureon_get_option()
    ↓
ferm_build_page_data()['customizer']
    ↓
FermPageData.customizer
```

## Client Assumptions

A client frontend MAY assume:
- `FermPageData` exists (or equivalent)
- Cart operations via AJAX
- Product data on product pages
- Navigation data for menus
- Customizer values for content
- Nonce for AJAX operations

## Client MUST NOT Assume

- Direct database access
- WordPress functions
- WooCommerce objects
- PHP rendering
- Server-side templating
