# 05 — Runtime Execution Audit (per route)

**Method:** static trace of template_include chain + ferm-page.php resolver + manifest. **No live server available in this environment** — no HTTP assertions were made. Everything below is CODE-LEVEL trace; runtime claims are marked UNPROVEN.

> **Critical caveat:** This repo contains no WordPress runtime (`wp-config.php` is gitignored; no Docker config present). Per audit rules, "HTTP 200" is not treated as proof anywhere — and in fact no runtime testing was possible at all from here. All route outcomes below are *intended behavior per code*, classified IMPLEMENTED (code path exists) vs UNPROVEN (no runtime evidence in repo).

## Routing precedence (deploy tree, `inc/frontend.php`)

1. `template_include(99)` `aureon_aether_wc_page_templates` — cart → `cart.php` (AETHER section); order-received → `woocommerce/checkout/thankyou.php`; checkout → `checkout/form-checkout.php` (with empty-cart redirect guard); account → `myaccount/my-account.php` **unless complete-page design** (then falls through).
2. `template_include(998)` `aureon_ferm_template_include` — checkout bypass (returns WC template), cart bypass, account → `myaccount/my-account.php` (always, when complete-page), else → `ferm-page.php`.
3. `ferm-page.php` — manifest `pages` map, then hardcoded fallback map.

## Route table

| Route | Resolved template (per code) | Data source | Page identity risk | Status |
|---|---|---|---|---|
| `/` | ferm-page.php → `index.html` | VinetaPageData.home (composer `vineta_build_home_data`): hero, demo/real products, categories, blog | frozen demo copy could render if catalog empty + demo on | IMPLEMENTED (code) / runtime UNPROVEN |
| `/shop/` | ferm-page.php → manifest `pages.shop` = `shop-default.html` | `vineta_build_collection_data` / wc-products adapter; `woocommerce_product_query` demo filter active | shop shows demo products when catalog empty | IMPLEMENTED / UNPROVEN |
| `/product/{slug}` | manifest `products._generic` → `product-detail.html` (all slugs — no per-product templates) | `vineta_build_product_page_data($product_id)` injected via `wp` hook + VinetaPageData; DOM bridged by pack JS | every product gets the same frozen template; JS must hydrate title/price/gallery — hydration completeness UNPROVEN | IMPLEMENTED / UNPROVEN |
| `/product-category/{slug}` | manifest `collections.default` → `shop-default.html` (no per-category files) | `vineta_build_collection_data` + `get_terms` demo filter | same frozen template for all categories | IMPLEMENTED / UNPROVEN |
| `/search/?s=` | ferm-page.php `is_search()` → manifest `pages.search` = `shop-default.html` | `vineta_build_search_data` + `vineta_search_bridge` (footer) | search renders shop grid; empty-state behavior UNPROVEN | IMPLEMENTED / UNPROVEN |
| `/cart/` | `cart.php` → AETHER section `section-cart.php` (pack CSS stripped; `vineta_wc_page_inline_css` injects styling) | WC cart via adapter + composer `vineta_build_cart_response` | three cart rendering systems exist (section, drawer JSON, view-cart.html — the latter unreachable) | IMPLEMENTED / UNPROVEN |
| `/checkout/` | `checkout/form-checkout.php` (standalone Vineta markup + real WC fields/nonce) | WC checkout engine | order creation is genuine WC; frozen `checkout.html` dead | IMPLEMENTED / UNPROVEN |
| `/checkout/order-received/...` | `woocommerce/checkout/thankyou.php` | WC order | — | IMPLEMENTED / UNPROVEN |
| `/my-account/` (logged out) | `myaccount/my-account.php` → redirect semantics: `?auth=login` → `login.php`; default logged-out view renders login (per template) | WP auth + WC endpoints | login POST targets `/my-account/` with `woocommerce-login-nonce` (injected by ferm-page rewrite for frozen page; standalone login.php needs same — VERIFIED only for ferm-page path) | IMPLEMENTED / UNPROVEN |
| `/my-account/` (logged in) | premium dashboard (own HTML doc, no shell) | WC endpoints, order counts | no WC `woocommerce_before_my_account` hooks — plugin compatibility risk | IMPLEMENTED / UNPROVEN |
| `/my-account/orders|edit-address|edit-account|downloads` | same standalone template consumes `WC()->query->get_current_endpoint()` | WC | endpoint rendering inside custom dashboard UNPROVEN | IMPLEMENTED / UNPROVEN |
| `/blog/` | manifest `pages.blog` = `blog-grid-01.html` | `vineta_build_blog_data` | pagination over frozen template UNPROVEN | IMPLEMENTED / UNPROVEN |
| `/blog/{post}` | manifest `blog_single` = `blog-single.html` | `vineta_build_article_data` | same frozen template for all posts | IMPLEMENTED / UNPROVEN |
| static pages (`/about-us/`, `/contact-us/`, …) | manifest `static` map → respective HTML | frozen content + adapter data where wired (contact form → aether ajax) | — | IMPLEMENTED / UNPROVEN |
| `/404` | resolver has `is_404()` fallback to `pages/contact.html` — **but vineta pack has 404.html and no pages/ dir**; hardcoded fallback is dead-wrong for this pack | — | wrong-page-content risk on 404 if manifest resolution fails | BROKEN fallback path |
| `*.php` direct file access to pack HTML | served raw by web server if server allows (nginx/apache config unknown) | — | potential exposure of demo content; config-dependent | UNPROVEN |

## Verified-by-code vs verified-by-runtime ledger

- **Verification possible from repo:** file existence, function wiring, hook priorities, manifest consistency.
- **Not possible from repo:** DB state (options, menu assignments, product catalog), HTTP behavior, JS console errors, payment gateway behavior, email delivery.
- Therefore the matrix in `test-results/FULL-FINAL-FORENSIC-MATRIX.json` marks every route-level feature `UNPROVEN` unless a code path proves it.

## Route-identity traps found (documented, not fixed)

1. `is_page('shop')` checks in resolver rely on a page with slug `shop` existing in DB — DB state unknown.
2. Checkout double-guard (both 99 filter and ferm-page 998 filter and inside form-checkout.php itself) — three redirect guards, one behavior; a change to any one can silently break the others.
3. The account routing logic exists in two filters with an inversion (`is_account_page() && ! complete-page` in filter 99; `is_account_page()` always in filter 998) — correct only while vineta (complete-page) is active; switching designs changes account routing semantics.
