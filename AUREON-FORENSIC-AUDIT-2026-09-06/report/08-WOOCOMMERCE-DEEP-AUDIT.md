# 08 — WooCommerce Deep Audit (Phases 7–14)

**Scope:** product → cart → checkout → order → account chain, search, menus, plugins. Code-level evidence only; DB state (actual catalog, gateway config, menu assignments) is not in this repo, so anything depending on data is UNPROVEN.

## Phase 7 — Commerce chain

| Stage | Implementation | Evidence | Status |
|---|---|---|---|
| Product → card data | `vineta_map_wc_product` (id, name, price via `wc_price`-adjacent fields, image, url) + adapter `adapter-wc-products.php` | composer + adapter | IMPLEMENTED / UNPROVEN |
| Shop grid | frozen `shop-default.html` + JS grid render from VinetaPageData.products; demo filter on `woocommerce_product_query` when catalog empty | composer | IMPLEMENTED / UNPROVEN |
| Category pages | frozen `shop-default.html` shared for all categories (`collections.default`) | manifest | IMPLEMENTED / UNPROVEN |
| Search | `vineta_build_search_data` + suggestions bridge; results render as shop grid | composer | PARTIAL (empty-state unproven) |
| Product page | single generic frozen template (`products._generic`) hydrated by JS from `vineta_build_product_page_data` (title, price, sku, gallery, attributes, variations, stock) | composer lines ~1567+ | IMPLEMENTED / UNPROVEN |
| Add-to-cart | AJAX `vineta_add_to_cart` / `vineta_cart_add` → `WC()->cart->add_to_cart()` with `absint` ids, nonce, variation-membership validation | composer | IMPLEMENTED (needs runtime test) |
| Cart page | `cart.php` → AETHER section with real WC cart via `adapter-cart.php`; plus standalone composer response builder for drawer | multiple | IMPLEMENTED / UNPROVEN |
| Checkout | `checkout/form-checkout.php`: real `WC()->checkout()->get_checkout_fields()`, countries, WC nonce — genuine WC order pipeline; only *presentation* is custom | template | IMPLEMENTED / UNPROVEN |
| Order → thankyou | `order-received` endpoint → `woocommerce/checkout/thankyou.php` | `aureon_aether_wc_page_templates` | IMPLEMENTED / UNPROVEN |
| Payment gateways | none configured in repo; `enable_cod.php` (untracked root script) exists to force-enable COD on a live server — evidence gateways were being configured **by hand on the server**, not via tracked config | root file | UNPROVEN + PROCESS RISK |
| Currency | `data-money-format` body attr + WC currency; formatting mismatch fixes present in commit c844cb3 ("currency formatting") | git log | IMPLEMENTED / UNPROVEN |

**Does any frontend pretend to do WC business logic?** No — checkout fields, totals, and order creation come from WC. The pack only computes *display* cart responses (subtotal/count) for the drawer, which mirrors WC session data. Acceptable, but the three redundant cart surfaces must stay in sync (see 06).

## Phase 8 — Variable products

- **IMPLEMENTED (code):** `vineta_build_product_page_data` builds `is_variable`, `variation_attributes`, `variations[]` (id, attrs, price, stock) for variable products; `vineta_add_to_cart` requires an explicit variation, validates it is a child of the parent (`in_array($variation_id, $children, true)`), resolves `attribute_*` meta with slug fallback, then calls `WC()->cart->add_to_cart($product_id, $qty, $variation_id, $variation)`.
- **CURRENT CLIENT TEST:** N/A from repo — the catalog is in the server DB, not here.
- **Per audit rule:** recorded as `IMPLEMENTED / CURRENT CLIENT TEST N/A / PRODUCTION UNPROVEN`. **Not a PASS.**
- UI wiring of unavailable combinations (greyed swatches) exists in pack JS (`shop.js` swatch logic) — code present, runtime UNPROVEN.

## Phase 9 — Cart

- **Badge:** triple system — (1) WC cart fragments `aether_cart_count_fragment`, (2) pack `VinetaCart.updateCount` after AJAX ops, (3) composer `vineta_build_cart_response.item_count`. Consistency risk on bfcache/restore.
- **Drawer:** pack drawer fed by `vineta-data-shims.js` + AJAX endpoints; quantity updates/removes → `vineta_cart_update` (JSON updates map).
- **Cart page:** AETHER section route (not frozen view-cart.html); inline CSS compensation added by composer (`vineta_wc_page_inline_css`, priority 1001).
- **Persistence:** WC session (PHP session / cookie) — standard; `theme/mu-plugins/aureon-fix-wc-session.php` exists **only in the gitignored staging tree**, hinting at past session breakage on the real host (UNPROVEN whether the fix is deployed — the deploy tree's mu-plugins has only ob-buffer.php).
- **bfcache/navigation:** no explicit pageshow handler found in shims — stale badge after back-navigation is a plausible defect (UNPROVEN).
- **Empty states:** `cart-empty.html` exists in pack but cart route never serves it (WC cart page + section handles empties) — DEAD template.

## Phase 10 — Checkout

- Template source: theme `checkout/form-checkout.php` (standalone Vineta-styled, self-contained header/footer, includes pack CSS).
- Engine: WC native (`WC()->checkout()`), fields/validation/order creation untouched → **no fake checkout detected**. Good.
- Payment: gateway state lives on the server. The presence of `enable_cod.php` shows live-server mutation by script — process risk documented in 01.
- Thank-you page: custom thankyou.php exists; content correctness UNPROVEN.
- Empty-cart redirect guarded in **three** places (filter 99, filter 998, template head) — works but fragile.

## Phase 11 — Auth / Account

- Login (logged-out, `/my-account/`): standalone `my-account.php` routes `?auth=login` → `login.php` (frozen-Vineta header/footer + WC login form). The frozen-account-page path (ferm-page.php) rewrites Shopify field names to WC names and injects `woocommerce-login-nonce`. Two login paths exist; both target WC's native handler → IMPLEMENTED; runtime UNPROVEN.
- Register: standalone `register.php` (commit 2e51705/5ce4dd5). WC account creation; nonce usage inside UNVERIFIED (must check before production).
- Logout: `wc_get_account_endpoint_url('customer-logout')` — WC native link (token-based logout in modern WC) → IMPLEMENTED.
- Lost password: rewritten to `wc_lostpassword_url()` → IMPLEMENTED.
- Dashboard (logged-in): custom premium dashboard, own HTML doc, real order counts/address counts via WC functions. **Bypasses WC endpoint template hierarchy** — endpoints (orders/downloads/edit-address/edit-account) are detected via `WC()->query->get_current_endpoint()` inside the custom template. Compatibility with account plugins (e.g. Points & Rewards, subscriptions) that hook `woocommerce_account_*` is **at risk** — their content zones don't exist in this template. UNPROVEN.
- Session: `aetherAjax.isUserLoggedIn` + `VinetaPageData.customer` both expose state; consistency relies on cache not serving one user another's page (see 21).

## Phase 12 — Search

- Query → WP `is_search()` → resolver → `shop-default.html` + `vineta_build_search_data` (products matching `s` via WC query) + footer suggestions bridge.
- Partial queries: WP `LIKE` search over products/posts — depends on WC search sku/short-description filters (none found configured).
- Empty state: UNPROVEN (no dedicated empty-results markup found in the data path).
- Hardcoded data: suggestions come from live query; initial frozen HTML contains demo product cards which the JS should replace — mismatch window possible (flash of demo content) UNPROVEN.
- Mobile: search UI in pack JS; runtime UNPROVEN.

## Phase 13 — Menus

- Source: WP menus `primary` + `footer` (registered in `inc/frontend.php`).
- Rendering: **server-side output-buffer splicing** of frozen HTML (`vineta_server_render_menus_start` at `template_redirect` 25 → `vineta_server_render_menus_html` → `vineta_html_splice_list` with a hand-written balanced-tag scanner) + separate standalone header renderer for auth pages (`vineta_render_standalone_header`).
- Active state/dropdowns: pack CSS/JS handles; hierarchy from WP menu tree preserved by splicer.
- Risks: (a) splicer depends on exact classes in frozen HTML (`vineta_html_splice_list( $html, $class, $inner )`) — redesign breaks it silently; (b) menus not assigned in WP → fallback demo links; DB state unknown.
- Mobile toggle: `#mobileMenuBtn` + pack JS — UNPROVEN.

## Phase 14 — Plugins

Inventory from deploy tree (`plugins/`):

| Plugin | Version | Purpose | Frontend surface on vineta | Status |
|---|---|---|---|---|
| aureon-studio | 1.1.0 | 17-module GP-derived suite (elements, hooks, menu-plus, page-header, sections, spacing, typography, colors, woocommerce module, …) | mostly suppressed on complete-page; Customizer surface active; some modules (typography enqueue) still live | CORE_CRITICAL (theme expects it for some options) — needs confirm-on-server |
| WooCommerce | not in repo | commerce engine | everything | CORE_CRITICAL (external; version unknown from repo) |
| (WooCommerce Blocks) | unknown | wc-blocks-style handle is dequeued in suppression list | CSS only | UNKNOWN |

- Only one plugin ships in-tree. Anything else active on production (payments, SEO, security, email) is **invisible to this repo** → listed as UNKNOWN; full plugin audit must be run against the live admin (→ QUESTIONS.md).
- `aether-*` "plugins" named in manifest `integrations.plugins` are actually theme `inc/aether-*.php` files (newsletter, ajax, analytics) — naming inconsistency, not separate plugins.
