# 13 — PRODUCT AND VARIATION CONTRACT

## Product Schema (Component Mode)

```php
[
    'id'                => int,
    'name'              => string,
    'price'             => string (HTML formatted),
    'price_plain'       => string (plain text),
    'old_price_plain'   => string,
    'tagline'           => string (truncated short description),
    'rating'            => float,
    'reviews'           => int,
    'image'             => string (URL),
    'alt'               => string,
    'url'               => string (permalink),
    'badge'             => string ('Sale'|'New'|'Featured'|''),
    'add_to_cart_url'   => string,
    'product_type'      => string ('simple'|'variable'),
    'behavior'          => array,
]
```

## Product Schema (Complete-Page / FermPageData)

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
  "availability": "in-stock|low-stock|out-of-stock",
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

## Variation Flow

```
WC Variable Product
    ↓
ferm_build_product_page_data($product_id)
    ↓
1. Get parent product
2. Build gallery from parent + gallery images
3. Calculate base price in cents
4. Determine availability
    ↓
5. Get attributes → options array
6. Get children (variations)
7. For each variation:
   - Get price in cents
   - Get availability
   - Get image
   - Get attribute values (option1, option2, option3)
   - Calculate price range (min/max)
    ↓
8. Build variants array
9. Build price_varies flag
10. Inject into FermPageData.product
```

## Reference Products

- **#834**: Simple product (permanent regression reference)
- **#828**: Variable product (permanent regression reference)

## Price Format

Component mode: `wc_price()` (HTML formatted)
Complete-page: cents integer (Ferm Shopify format)
