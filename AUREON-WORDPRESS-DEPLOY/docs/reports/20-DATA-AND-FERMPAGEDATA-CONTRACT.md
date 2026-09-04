# 20 — DATA AND FERMPAGEDATA CONTRACT

## FermPageData Structure

Injected via `wp_localize_script('ferm-data-shims', 'FermPageData', $page_data)`.

```json
{
  "cart": {
    "items": [{
      "key": "string",
      "id": 834,
      "variant_id": 0,
      "quantity": 1,
      "title": "Product Name",
      "price": 19900,
      "line_price": 19900,
      "variant_title": "",
      "product_id": 834,
      "url": "https://site/product/slug/",
      "image": "https://site/wp-content/uploads/..."
    }],
    "item_count": 2,
    "total_price": 39800,
    "currency": "EUR"
  },
  "customer": {
    "logged_in": false,
    "id": null,
    "email": null,
    "first_name": null,
    "last_name": null,
    "addresses": []
  },
  "shop": {
    "name": "Site Name",
    "url": "https://site/",
    "currency": "EUR",
    "money_format": "EUR {{amount_with_comma_separator}}",
    "money_format_decimals": "EUR {{amount_with_comma_separator}}"
  },
  "navigation": {
    "main": [{"title": "Shop", "url": "...", "children": [...]}],
    "footer": [{"title": "About", "url": "...", "children": []}]
  },
  "config": {
    "ajax_url": "https://site/wp-admin/admin-ajax.php",
    "nonce": "string",
    "wc_ajax_url": "https://site/?wc-ajax=%%endpoint%%",
    "is_logged_in": false,
    "template": "index|product|collection|page|blog",
    "money_format": "EUR {{amount_with_comma_separator}}",
    "shop_url": "https://site/",
    "search_url": "https://site/?s="
  },
  "product": { /* only on product pages */ },
  "collection": { /* only on collection pages */ },
  "customizer": {
    "site": {"name": "", "description": "", "logo_url": ""},
    "announcement": [],
    "hero": [],
    "categories": [],
    "footer": [],
    "newsletter": {"heading": "", "text": "", "subtitle": ""},
    "social": [],
    "usp_items": [],
    "colors": {"bg": "", "surface": "", "text": "", "muted": "", "accent": "", "accent_hover": "", "border": ""},
    "fonts": {"heading": "", "body": ""}
  }
}
```

## Product Data (on product pages)

```json
{
  "id": 834,
  "title": "Product Name",
  "slug": "product-slug",
  "url": "https://site/product/slug/",
  "sku": "SKU-123",
  "price": 19900,
  "formatted_price": "€199,00",
  "currency": "EUR",
  "availability": "in-stock",
  "gallery": [{"src": "url", "alt": "text"}],
  "attributes": ["Color", "Size"],
  "variants": [{
    "id": 1234,
    "price": 19900,
    "available": true,
    "option1": "Black",
    "option2": "M",
    "image": "url"
  }],
  "description": "text",
  "badge": "Sale"
}
```

## Collection Data (on collection pages)

Injected via `ferm_inject_collection_fermpagedata()` in `wp_head`.

## Data Sources

| Field | Source | Visibility |
|-------|--------|------------|
| cart.* | WC()->cart | Private (session) |
| customer.* | wp_get_current_user() | Private (auth) |
| shop.* | bloginfo() | Public |
| navigation.* | wp_get_nav_menu_items() | Public |
| config.* | WP/WC functions | Public (nonce required) |
| customizer.* | aureon_get_option() | Public |
| product.* | wc_get_product() | Public |
| collection.* | WP_Query | Public |

## Security Notes

- Prices in cents (integer) to avoid floating-point issues
- Nonce required for all AJAX operations
- No raw WP objects exposed
- Customer email exposed only to logged-in user
