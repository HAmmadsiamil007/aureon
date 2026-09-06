# AUREON — RUNTIME FLOW AUDIT
## AUDIT DATE: 2026-09-04
## AUDITOR: Forensic Pass 1

## Route Traces

### / (Homepage)
1. WP recognizes `is_front_page()`.
2. `aureon_ferm_template_include` routes to `ferm-page.php`.
3. `aureon_ferm_resolve_page()` selects `index.html`.
4. Page is assembled with injected customizer variables.

### /shop/ and /product-category/{slug}
1. WP recognizes `is_post_type_archive('product')` or `is_tax('product_cat')`.
2. Routed to `ferm-page.php`.
3. Resolves to the specified collections HTML (e.g. `collections/furniture.html` or manifest mapped).
4. `section-shop-grid.php` and adapters inject product data.

### /cart/
1. WP recognizes `is_cart()`.
2. Priority 99 routes to native `cart.php`, but Priority 998 forces `ferm-page.php` for complete pages.
3. Resolves to `view-cart.html`.
4. `vineta_inject_cart_data()` provides cart state (has multi-item update bug).

### /checkout/
1. WP recognizes `is_checkout()`.
2. Priority 99 routes to native `checkout/form-checkout.php`.
3. Priority 998 explicitly ignores checkout (`if (is_checkout()) return $template;`).
4. Result: WC Native Checkout, bypassing Vineta complete-page.

## Known Issues
- Cart multi-item update bug (only updates first item).
- Checkout uses native WC templates, breaking design continuity.
- Missing CDN images cause 404s.
