# AETHER-BRIDGE.md — How the AETHER Frontend Connects to the Aureon Core Theme

> **Product:** Aureon theme + Aureon Studio plugin + AETHER frontend engine
> **Status:** Live on Docker `aureon_wp` (localhost:8080), verified 2026-08-29
> **Doc version:** 2026-08-29 — Ferm Living integration complete

This is the authoritative document for **how the AETHER frontend engine is connected to the Aureon core theme** and **which features are connected**. Companion docs: [`THEME.md`](./THEME.md) (theme framework), [`PLUGIN.md`](./PLUGIN.md) (Aureon Studio modules), [`FRONTEND.md`](./FRONTEND.md) (Phase 17 implementation guide — historical), [`FRONTEND-OPERATIONS.md`](./FRONTEND-OPERATIONS.md) (edit / replace / create dynamic frontends).

---

## 1. One-line summary

The theme boots the engine: `aureon/theme/inc/frontend.php` requires `frontend/views/loader.php` (the engine kernel), suppresses everything the engine owns, and enqueues the engine's assets — while **8 feature bridges** (`inc/aether-*.php`) connect SEO, security, analytics, newsletter, wishlist/quick-view/contact AJAX, cart fragments, tokens, and performance to the engine's scripts.

---

## 2. Boot sequence (theme → engine)

```
WordPress boots the Aureon theme
  └─ functions.php  (L90–126 require chain)
      └─ inc/frontend.php  (L108)  ← THE BRIDGE
          ├─ require ../../frontend/views/loader.php   (L16)  ← engine kernel
          │     (wp-content/frontend/views/loader.php at deploy;
          │      repo/frontend/views/loader.php in dev)
          ├─ require inc/aether-tokens.php             (L18)  feature bridge
          ├─ require inc/aether-security.php           (L19)  feature bridge
          ├─ require inc/aether-seo.php                (L20)  feature bridge
          ├─ require inc/aether-newsletter.php         (L21)  feature bridge
          ├─ require inc/aether-ajax.php               (L22)  feature bridge
          ├─ require inc/aether-cart.php               (L23)  feature bridge
          ├─ require inc/aether-analytics.php          (L24)  feature bridge
          └─ require inc/aether-performance.php        (L25)  feature bridge
```

### The exact hooks (verified against `inc/frontend.php`, 235 lines)

| Hook | Priority | Callback | What it does |
|---|---|---|---|
| `after_setup_theme` | 20 | `aureon_aether_register_nav_menus` | Registers `primary` ("AETHER Primary Menu") + `footer` ("AETHER Footer Menu") locations |
| `after_setup_theme` | 30 | `aureon_aether_frontend_boot` | Calls `aether_frontend_boot()` — engine kernel boot (after the theme is set up) |
| `wp_enqueue_scripts` | 1000 | `aureon_aether_suppress_theme_output` | Dequeues/de-registers every theme presentation handle the engine owns (see §3) |
| `wp_enqueue_scripts` | 20 | `aureon_aether_enqueue_assets` | Enqueues AETHER CDN + local assets, localizes `aetherAjax`, registers favicon output (see §4). **Guarded:** runs only when `'luxury' === aether_active_design()` — design packs own their presentation |
| `wp_head` | 1 | `aureon_aether_favicons` | Favicons (ico/32/16/apple), `msapplication-TileColor #09090B`, `theme-color #09090B` (registered inside the enqueue callback) |
| `template_include` | 99 | `aureon_aether_wc_page_templates` | Routes WooCommerce cart/checkout/account pages to AETHER templates (see §5) |

---

## 3. Output suppression — what the engine takes over

At `wp_enqueue_scripts` priority **1000** (after the theme's own enqueue callbacks, so nothing the engine owns is ever output), the bridge removes:

**10 theme layout styles:** `aureon-comments`, `aureon-widget-areas`, `aureon-style`, `aureon-style-grid`, `aureon-mobile-style`, `aureon-font-icons`, `font-awesome` (theme's own FA 4.7 — AETHER loads FA 6.5.1), `aureon-rtl`, `aureon-fonts`, `aureon-child`.

> **Deliberately KEPT:** `aureon-google-fonts` — the dynamic Typography Manager (Font Manager) enqueues it, and AETHER bridges those families into `--font-heading` / `--font-body` tokens. Suppressing it would break the font bridge.

**5 theme layout scripts:** `aureon-menu`, `aureon-dropdown-click`, `aureon-modal`, `aureon-navigation-search`, `aureon-back-to-top`.

**4 theme output hookups** (removed via `remove_action`):
- `wp_footer` → `aureon_do_a11y_scripts`
- `wp_footer` → `aureon_do_search_modal`
- `wp_footer` → `aureon_clone_sidebar_navigation`
- `aureon_after_header` → `aureon_featured_page_header` (defensive; header construction is already bypassed by template replacement)

The theme's Customizer options remain fully available — suppression is **output-level only**, never option-level.

---

## 4. Asset contract — what the engine enqueues

Enqueued by `aureon_aether_enqueue_assets` (`wp_enqueue_scripts`, priority 20). Asset base: `content_url()/frontend/assets`. All local files are versioned by `filemtime` (fresh deploy is served immediately).

### CSS (source-contract order)

| Handle | Source | Version |
|---|---|---|
| `aether-bootstrap` | jsdelivr Bootstrap 5.3.3 | `5.3.3` |
| `aether-fontawesome` | cdnjs Font Awesome 6.5.1 | `6.5.1` |
| `aether-swiper` | jsdelivr Swiper 11 | `11` |
| `aether-style` | `css/style.css` (local) | filemtime |
| `aether-motion` | `css/motion.css` (local, dep: aether-style) | filemtime |
| `aether-responsive` | `css/responsive.css` (local, dep: aether-style) | filemtime |
| `aether-a11y` | `css/a11y.css` (local, dep: aether-style) | filemtime |
| `aether-pages` | `css/pages.css` (local, dep: aether-style) | filemtime |
| `aether-fonts` | `css/fonts.css` (local — self-hosted Fontshare woff2) | filemtime |

### JS (source-contract order, footer)

| Handle | Source | Deps | Version |
|---|---|---|---|
| `aether-bootstrap-js` | jsdelivr Bootstrap 5.3.3 bundle | — | `5.3.3` |
| `aether-swiper-js` | jsdelivr Swiper 11 | — | `11` |
| `aether-gsap` | cdnjs GSAP 3.12.5 | — | `3.12.5` |
| `aether-scrolltrigger` | cdnjs ScrollTrigger 3.12.5 | aether-gsap | `3.12.5` |
| `aether-lenis` | unpkg Lenis 1.1.19 | — | `1.1.19` |
| `aether-lenis-scroll` | `js/lenis-scroll.js` (local) | aether-lenis | filemtime |
| `aether-animations` | `js/animations.js` (local) | bootstrap-js, gsap | filemtime |
| `aether-main` | `js/main.js` (local, 884 lines) | aether-animations | filemtime |
| `aether-phantom-bridge` | `js/phantom-bridge.js` (local) | aether-main | filemtime |
| `aether-countdown` | `js/countdown.js` (local) | — | filemtime |

### The `aetherAjax` JS context (localized on `aether-main`)

| Key | Value |
|---|---|
| `ajaxUrl` | `admin_url('admin-ajax.php')` |
| `nonce` | `wp_create_nonce('aether_nonce')` — one shared nonce for all engine AJAX |
| `restUrl` | `rest_url('aether/v1/')` |
| `isUserLoggedIn` | `is_user_logged_in()` |
| `shopUrl` | `wc_get_page_permalink('shop')` (falls back to `/shop/`) |
| `searchUrl` | `home_url('/?s=')` |
| `wcAjaxUrl` | `add_query_arg('wc-ajax', 'add_to_cart', home_url('/'))` (G1 fix — empty-value bug hardened) |

---

## 5. WooCommerce template routing (theme-side)

`template_include` filter at priority 99 (`aureon_aether_wc_page_templates`) — WC 11's loader only handles product/shop archives, so these pages are routed to AETHER templates:

| Condition | Template |
|---|---|
| `is_cart()` | `theme/cart.php` (AETHER section composition) |
| `is_wc_endpoint_url('order-received')` | `theme/woocommerce/checkout/thankyou.php` |
| `is_checkout() && ! is_wc_endpoint_url('order-pay')` | `theme/checkout/form-checkout.php` |
| `is_account_page()` | `theme/myaccount/my-account.php` |
| everything else | unchanged (WC stock flow, incl. order-pay) |

Every routed template is pure section composition: `aether_render_section(...)` with Customizer `aether_section_*` gating. The plugin's old `template-locator.php` override bridge was removed — **theme `template_include` is the single WC routing point**.

---

## 6. Feature bridges — what is connected (`inc/aether-*.php`)

All 8 are required from `inc/frontend.php` and are **always loaded** (no module toggle). Hooks verified against source:

### `aether-tokens.php` — design tokens
- `wp_enqueue_scripts` 98 → `aether-tokens` stylesheet: dynamic `:root` tokens (`--void`, `--surface`, `--gold`, `--chrome`, `--font-heading`, `--font-body`, `--container-max`, …) built from `aureon_get_option()`.
- Registered as a dependency of `aether-style` (register/enqueue split required — `wp_enqueue_style(handle, false)` alone drops dependents in WP ≥ 6.9).

### `aether-security.php` — hardening
- `send_headers` 1: `nosniff`, `X-Frame-Options: SAMEORIGIN` (with Customizer bypass), `Referrer-Policy`, `Permissions-Policy`, CSP **Report-Only** (nonce + `strict-dynamic` + CDN allowlists); `AETHER_CSP_STRICT` const gates strict mode.
- `wp_body_open` 1: CSP nonce script.
- `script_loader_tag` 10: nonce injection on engine scripts.
- `init` 1: `X-Powered-By` removal; `send_headers` 2: HSTS.

### `aether-seo.php` — SEO output
- `wp_head` priorities 1–5: OpenGraph tags, Twitter cards, JSON-LD structured data (Organization, WebSite + SearchAction, BreadcrumbList, Product with aggregateRating, Article), canonical, extra meta (robots/geo).
- Every surface covered: site, singular, product, author, taxonomy.

### `aether-newsletter.php` — DB-backed newsletter
- `admin_init` 5: lazy `dbDelta` table creation (`wp_aether_newsletter_subscribers`).
- `wp_ajax`/`wp_ajax_nopriv` `aether_newsletter_subscribe` — **rate-limited 1/IP/min** (G4 fix).
- `admin_menu`: admin page (Appearance → Newsletter) with stats, pagination, bulk-delete, CSV export.
- `rest_api_init`: `/aether/v1/newsletter/subscribe`.
- Environment note: container has no MTA — `wp_mail()` returns false; AJAX contract (200 + status) is proven, delivery needs a real SMTP host.

### `aether-ajax.php` — engine AJAX endpoints
- `wp_ajax` + `wp_ajax_nopriv`: `aether_wishlist_toggle`, `aether_wishlist_count`, `aether_quick_view`, `aether_contact_submit`. Wishlist is user-meta backed; nopriv returns "please log in" + my-account redirect. Contact uses `form.getAttribute('action')` fallback (G3 hardening).

### `aether-cart.php` — cart fragments
- `woocommerce_add_to_cart_fragments` → custom `.aether-cart-count` fragment so the header badge updates on WC AJAX add-to-cart (no full-page reload; G1-verified).

### `aether-analytics.php` — event tracking
- `wp_head` 1: analytics head snippet; `wp_footer` 99: flush queue.
- `template_redirect` 5/6: `view_item` / `view_item_list` events.
- `woocommerce_add_to_cart` 10: add-to-cart event (6 args).
- `woocommerce_payment_complete` 10: purchase event.

### `aether-performance.php` — performance
- `wp_head` 1: resource hints (preconnect CDNs); `wp_head` 2: font preloads (no `crossorigin` — ORB-block fix).
- `style_loader_src` / `script_loader_src` 10: strips `?ver=` from CDN srcs (local assets keep filemtime).
- `wp` 99: disables WC scripts/styles off-WC pages.
- `template_redirect`: HTML output compression.

---

## 7. Design resolution — luxury vs design packs

`aether_active_design()` (`frontend/views/design.php`) resolves the active design:

- Option `aether_active_design` (single option row): `''` → **luxury** (the engine tree itself is the luxury design), `'lumen'` → the **lumen design pack**.
- **Cache fix (2026-08-15):** the static cache previously stored the raw sanitized option (`''`) *before* the fallback — first call per request returned `'luxury'`, every later call returned `''` (broken body class `design-`, skipped luxury assets). The function now resolves the branch, applies the fallback, then caches. Verified through `wp-load`: `string(6) "luxury"`.
- Body class: `design-<slug>` (e.g. `design-luxury`, `design-lumen`).
- **Isolation by construction (M7):** luxury's design system never coexists with a pack — the enqueue guard at `frontend.php:111` skips luxury assets for non-luxury designs, and packs ship their own manifest + tokens + assets.

---

## 8. What is NOT connected (by design)

| Theme feature | Why not connected |
|---|---|
| Theme layout styles/scripts | Suppressed (§3) — engine owns presentation |
| Plugin module styles | Never enqueued — modules extend Customizer/options, not presentation |
| Site Library / license system | Removed (2026-08-05) — in-house templates, no activation |
| `aureon-google-fonts` | **Connected** — deliberately kept and bridged into font tokens (§3) |
| Core theme REST (`/aureon-pro/v1/*`, `/aureon/v1/reset`) | Admin-side only — engine uses `/aether/v1/` + admin-ajax |

---

## 9. Verification (2026-08-15, M6–M10 state)

- Design isolation: **6/6** (`frontend/tests/specs/design-isolation.spec.js`).
- Route suite: **32/32** (`frontend/tests/specs/routes.spec.js`).
- `frontend/tests/verify.sh` — **PASSED** (PHP lint, JS check, component grep gate, adapters/tokens/manifest/renderer presence).
- main.js MD5 frozen: `6d8f3b671333571508efcb53b1e39e60`.
- 0 console errors across tested routes; theme + plugin + engine committed at `9dd4e21`.