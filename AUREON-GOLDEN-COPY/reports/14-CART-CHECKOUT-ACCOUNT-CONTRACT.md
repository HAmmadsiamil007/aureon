# 14 — CART / CHECKOUT / ACCOUNT CONTRACT

## Cart Operations

### Add to Cart (Ferm)
```
AJAX: ferm_cart_add
Nonce: ferm_cart_nonce
POST: product_id, quantity
Response: {item_count, items[], total_price}
```

### Update Cart (Ferm)
```
AJAX: ferm_cart_update
Nonce: ferm_cart_nonce
POST: updates (JSON: {cart_item_key: quantity})
Response: {item_count, items[], total_price}
```

### Get Cart (Ferm)
```
AJAX: ferm_cart_get
Nonce: ferm_cart_nonce
Response: {item_count, items[], total_price}
```

### Cart Response Schema
```json
{
  "item_count": 2,
  "items": [{
    "key": "cart_item_key",
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
  "total_price": 19900
}
```

## Checkout

Uses WooCommerce native template (`checkout/form-checkout.php`).
Complete-page designs still use WC checkout.

## Account

### Logged Out
- Ferm: frozen `account/login.html`
- Component: `myaccount/my-account.php`

### Logged In
- Both: WC native `myaccount/my-account.php`

## Cart Fragments

```php
// aether-cart.php
add_filter('woocommerce_add_to_cart_fragments', 'aether_cart_count_fragment');
```

Provides header cart count update fragments for both AETHER and Ferm designs.

## Security

All cart AJAX handlers verify:
- `check_ajax_referer('ferm_cart_nonce', 'nonce')`
- `check_ajax_referer('aether_nonce', 'nonce')`
