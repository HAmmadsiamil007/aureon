# 06 — Dynamic Slot Deep Audit

**Terminology note:** this codebase has **no `data-aureon-slot` attributes** (verified by search across the deploy tree — 0 matches). The dynamic system uses:
- `window.VinetaPageData` (PHP-produced JSON, injected via `wp_localize_script` + inline `<script>`)
- `window.FermPageData` (legacy, still injected for logged-out account pages)
- `aether_adapter_*` data filters (viewmodels for AETHER sections)
- DOM class/id hooks (`.cart-count`, `.header__logo`, `#customer_login`, `#submit-login`, `#mobileMenuBtn`, etc.)

Statuses follow the audit's controlled vocabulary. Runtime statuses are UNPROVEN where only code evidence exists.

## Producer/consumer matrix

| Slot / channel | Producer | Consumer | DOM target | Data | Required? | Fallback | Sanitization | Status |
|---|---|---|---|---|---|---|---|---|
| site identity (name/url) | composer `vineta_site_data` (filter `aether_adapter_site_data`) | AETHER shell components | shell markup | site name, url | no (shell mostly blocked) | demo defaults | `sanitize_key`, esc | WORKING_NEEDS_HARDENING (unproven runtime) |
| header data | `vineta_header_data` | shell header (blocked on complete-page) | shell | brand, menu, icons | no | defaults | esc_* | DEAD-ish on complete-page (shell bypassed) |
| announcement bar | VinetaPageData `announcement` | pack JS shims | `.announcement` (pack classes) | text, link | no | demo text | wp_kses? — needs verification | UNPROVEN |
| navigation | `vineta_get_nav_menu($location)` → server-spliced into frozen HTML (`vineta_server_render_menus_html` via output buffer at `template_redirect` 25) | frozen HTML `<ul>` splice | `.main-menu` / footer list (class-based) | WP menu tree | YES | hardcoded demo menu if no WP menu assigned | esc_html/esc_url on items | WORKING_NEEDS_HARDENING |
| footer menu | same splicing (footer variant) | frozen footer | class-based | WP menu tree | yes | demo links | esc_* | WORKING_NEEDS_HARDENING |
| hero slides | Customizer repeater (composer `vineta_customize_register_hero_banner`) → `vineta_emit_customizer_css`/data | pack JS slider | hero section DOM | images, headings, links | no | frozen demo slides | esc + URL validation unverified | UNPROVEN |
| wc-products (shop grid) | adapters + `vineta_wc_products_data` filter; demo filter on `woocommerce_product_query` | pack JS renders grid from VinetaPageData.products | product grid DOM | id, name, price, image, url | YES | demo JSON | price via wc_price; image URL esc | PARTIAL (runtime unproven) |
| wc-categories | adapters + `vineta_wc_categories_data`; `get_terms` demo filter | pack JS | category cards | slug, name, count, image | no | demo JSON | esc | PARTIAL |
| product page data | `vineta_store_product_page_data` (wp hook) → `vineta_build_product_page_data` | pack JS `shop.js` hydration | title, price, sku, gallery, swatches, add-to-cart form | full product | YES | demo product | wc functions + esc; UNVERIFIED for gallery/swatches | UNPROVEN (hydration completeness) |
| product variations | `vineta_build_product_page_data` includes variation data? (present for swatch UI) | pack JS swatch logic | swatch DOM | variation ids, attrs, price, stock | conditional | none | unverified | UNPROVEN |
| cart count badge | `aether_cart_count_fragment` (WC fragments) + pack `VinetaCart.updateCount` + composer `vineta_build_cart_response` | `.cart-count` (multiple DOM locations) | header + drawer + mobile | item count | YES | 0 | absint | VERIFIED_WORKING (3 redundant systems; needs hardening) |
| cart add/update/get | composer AJAX `vineta_cart_add|update|get` + `vineta_add_to_cart` (nonce `vineta_cart_nonce`, check_ajax_referer verified ×4) | `vineta-data-shims.js` VinetaCart | drawer + cart page | WC session cart | YES | error toast | absint on ids/qty; wc functions | IMPLEMENTED (runtime UNPROVEN) |
| cart drawer render | `vineta_build_cart_response` JSON | pack JS drawer | drawer DOM | items, subtotal, count | no | empty drawer | wc_price, esc | UNPROVEN |
| cart page items | `section-cart.php` (adapter-cart) + inline CSS | WP render | cart table | WC cart | YES | empty state | esc | IMPLEMENTED / UNPROVEN |
| checkout fields | WC `WC()->checkout()->get_checkout_fields()` read directly in `form-checkout.php` | template | checkout form | fields, countries, nonce | YES | none | WC native | IMPLEMENTED / UNPROVEN |
| auth state | VinetaPageData `customer.is_logged_in` + `vineta_auth_bridge` footer script | pack header icons, account nav | header icon URLs | logged-in bool, display name | no | logged-out | esc_js | WORKING_NEEDS_HARDENING |
| login form (frozen account page path) | ferm-page.php regex rewrite: action→/my-account/, customer[email]→username, + nonce injection after `#submit-login` | WP login handler | `#customer_login` | credentials | YES | — | esc_attr/esc_url on attrs; regex on HTML (brittle) | WORKING_NEEDS_HARDENING (standalone login.php path separate) |
| register | standalone `register.php` | WP/WC register | form | — | yes | — | unverified nonce | UNPROVEN |
| lost password | rewritten to `wc_lostpassword_url()` / `wp_lostpassword_url()` | WP | link | — | no | — | esc_url | IMPLEMENTED |
| search | `vineta_build_search_data` + footer `vineta_search_bridge` (suggestions fetch) | pack search UI | search input/dropdown | query, suggestions, products | no | empty state? | esc + sanitize_text_field | PARTIAL (empty state unproven) |
| newsletter | `aether_ajax_newsletter_subscribe` + REST `aether/v1` | pack newsletter form | form | email | no | — | is_email + nonce (verify REST permissions) | IMPLEMENTED / hardening needed |
| contact form | `aether_contact_submit` AJAX | pack contact page | form | name/email/message | no | — | sanitize + nonce (verify) | IMPLEMENTED / UNPROVEN |
| wishlist/compare | `aether_wishlist_toggle/count`, `aether_quick_view` | pack? | — | — | no | — | — | UNPROVEN (pack wiring not found in shim JS) |
| blog data | `vineta_build_blog_data` / `vineta_build_article_data` | pack JS/blog grid | grid | posts | no | demo | esc | UNPROVEN |
| logo | `custom_logo` theme mod → JS img swap in ferm-page.php | `.header__logo`, `[data-header-logo]` | header logo | image url | no | frozen SVG | esc_js | IMPLEMENTED (fragile DOM injection) |
| currency formatting | pack `data-money-format` body attr (preserved by ferm-page attrs extractor) | pack JS | prices | format string | no | default | allowlisted attr | STATIC_BY_DESIGN |

## Summary counts

- VERIFIED_WORKING: 1 (cart badge — triple-redundant)
- WORKING_NEEDS_HARDENING: 5
- IMPLEMENTED / runtime UNPROVEN: 14
- PARTIAL: 3
- DEAD / unreachable: 2 (header shell data path, checkout.html pack page)
- STATIC_BY_DESIGN: 2
- BROKEN: 1 code-level (404 fallback pointing at non-existent pack path)

## Cross-cutting risks

1. **Three cart systems** (WC fragments, pack AJAX, composer response builder) that must agree.
2. **Class-based DOM splicing** (`vineta_html_splice_list`) on frozen HTML breaks silently if the pack HTML is ever redesigned — fragile contract by regex.
3. **VinetaPageData is double-injected** (wp_localize_script on shims handle + inline echo at wp_head 5) — potential ordering/conflict issue on some pages.
4. **Legacy FermPageData** still injected on account pages alongside VinetaPageData — two data contracts coexist.
