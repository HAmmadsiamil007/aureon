# 22 — AJAX / REST / ENDPOINTS

## AJAX Endpoints

### Ferm Cart AJAX
| Action | Handler | Nonce | Purpose |
|--------|---------|-------|---------|
| ferm_cart_add | ferm_wc_ajax_cart_add() | ferm_cart_nonce | Add product to cart |
| ferm_cart_update | ferm_wc_ajax_cart_update() | ferm_cart_nonce | Update cart quantities |
| ferm_cart_get | ferm_wc_ajax_cart_get() | ferm_cart_nonce | Get current cart state |

### AETHER AJAX
| Action | Handler | Nonce | Purpose |
|--------|---------|-------|---------|
| aether_wishlist_toggle | aether_wishlist_toggle() | aether_nonce | Toggle wishlist item |
| aether_wishlist_count | aether_wishlist_count() | — | Get wishlist count |
| aether_quick_view | aether_quick_view() | aether_nonce | Quick view product data |
| aether_contact_submit | aether_contact_submit() | aether_contact | Submit contact form |

### WooCommerce AJAX
| Endpoint | Purpose |
|----------|---------|
| wc-ajax=add_to_cart | Standard WC add to cart |
| wc-ajax=%%endpoint%% | WC cart fragments |

## REST Routes

| Route | Purpose |
|-------|---------|
| aether/v1/* | AETHER REST API (registered via class-rest.php) |

## AJAX Security

All custom AJAX handlers verify:
- `check_ajax_referer()` with appropriate nonce
- Input sanitization (`absint()`, `sanitize_text_field()`, etc.)
- Rate limiting (contact form: 1/minute per IP)
- Capability checks where appropriate

## AJAX Response Format

```json
{
  "success": true,
  "data": { ... }
}
```

or

```json
{
  "success": false,
  "data": "Error message"
}
```

## Localized AJAX Config

```php
wp_localize_script('aether-main', 'aetherAjax', [
    'ajaxUrl'       => admin_url('admin-ajax.php'),
    'nonce'         => wp_create_nonce('aether_nonce'),
    'restUrl'       => rest_url('aether/v1/'),
    'isUserLoggedIn'=> is_user_logged_in(),
    'shopUrl'       => wc_get_page_permalink('shop'),
    'searchUrl'     => home_url('/?s='),
    'wcAjaxUrl'     => add_query_arg('wc-ajax', 'add_to_cart', home_url('/')),
]);
```
