# 12 — WOOCOMMERCE ARCHITECTURE

## Integration Points

| Area | Integration | File |
|------|-------------|------|
| Products | Query, display, cards | adapter-wc-products.php |
| Categories | Taxonomy, display | adapter-wc-categories.php |
| Single Product | Full product page | adapter-product.php |
| Cart | Cart data, AJAX | adapter-cart.php, aether-cart.php |
| Checkout | WC native template | checkout/form-checkout.php |
| Account | WC native template | myaccount/my-account.php |
| Orders | Order display | adapter-order.php |
| Filter | Category filter | adapter-wc-filter.php |

## Product Data Flow

```
WC Product Object
    ↓
adapter-wc-products.php / adapter-product.php
    ↓
Normalized array: {id, name, price, image, url, badge, ...}
    ↓
Component template (card/product.php or product/info.php)
```

## Cart Flow

```
Add to Cart:
  Client JS → wc-ajax=add_to_cart → WC()->cart->add_to_cart()
  → fragments response → header count updated

Update Cart:
  Client JS → ferm_cart_update AJAX → WC()->cart->set_quantity()
  → ferm_build_cart_response() → JSON

Get Cart:
  Client JS → ferm_cart_get AJAX → ferm_build_cart_response() → JSON
```

## Checkout

Uses WooCommerce native template (`checkout/form-checkout.php`).
Complete-page designs still use WC checkout (not frozen HTML).

## Account

- Logged out: Ferm frozen `account/login.html`
- Logged in: WC native `myaccount/my-account.php`

## WooCommerce Template Overrides

```
aureon/theme/woocommerce/
├── checkout/
│   └── thankyou.php
```

## WC Asset Optimization

`aether_optimize_woocommerce()` disables WC frontend scripts on non-WC pages.

## WC Color Bridge

AETHER bridges WC Customizer palette into `--aether-wc-*` tokens:
- `--aether-wc-primary`
- `--aether-wc-highlight`
- `--aether-wc-subtext`
- `--aether-wc-price`
