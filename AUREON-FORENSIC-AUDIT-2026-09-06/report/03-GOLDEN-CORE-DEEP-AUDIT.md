# 03 — Golden Aureon Core Deep Audit

**Scope:** `AUREON-WORDPRESS-DEPLOY/themes/aureon` + `frontend/views` + `mu-plugins` + `plugins/aureon-studio`. Read-only.

## Core execution path (verified by code reading)

```
WordPress request
  → wp-load / plugin load (aureon-studio 1.1.0)
  → mu-plugins/ob-buffer.php            ob_start() on frontend
  → theme functions.php (AUREON_VERSION 3.6.1)
      → inc/frontend.php
          → requires frontend/views/loader.php   (AETHER engine boot)
          → requires inc/aether-*.php            (security, seo, newsletter, ajax, cart, analytics, performance)
  → after_setup_theme(20)  register nav menus (primary, footer)
  → after_setup_theme(30)  aether_frontend_boot()
      → tokens, design, registry, renderer, viewmodel, assets, composer
      → pack composer (designs/vineta/composer.php)   ← 2,885 lines
      → 23 adapters, 26 sections, pack sections
  → wp_enqueue_scripts(20)   aether_design_enqueue_assets()  pack CSS/JS from manifest
  → wp_enqueue_scripts(1000) aureon_aether_suppress_theme_output()
                             → strips theme styles + (complete-page) WC/platform CSS/JS,
                               except checkout/account pages
  → template_include(99)   aureon_aether_wc_page_templates()  cart/checkout/account/order-received
  → template_include(998)  aureon_ferm_template_include()     → ferm-page.php or pack account template
  → ferm-page.php          manifest route map → frozen HTML → rewrite paths → wp_head/wp_footer
  → (pack composer hooks)  VinetaPageData injection, menu splicing, bridges
```

## Design resolution — **ARCHITECTURE DEFECT (documented, not fixed)**

`aether_active_design()` (`frontend/views/design.php`):

```php
$design = $design ? $design : 'vineta';
```

- The docblock states the fallback is `'luxury'` (the engine tree itself).
- The code hardcodes `'vineta'`. Even when `AETHER_DESIGN` is undefined and the option is empty, the "default design" silently becomes the client pack.
- Consequence: the `'luxury'` branch in `aureon_aether_enqueue_assets()` (`if ( 'luxury' !== aether_active_design() ) return;`) is **dead code by construction** — the entire AETHER shell asset stack (Bootstrap/GSAP/Lenis/main.js) can never enqueue while this fallback stands.
- Consequence 2: `aether_active_design_dir()` returns `''` only for `'luxury'`, so for vineta everything resolves to the pack. This couples Core behavior to a client pack slug — a Golden Core file contains a client-specific value.

## AETHER engine files (per-file)

| File | Purpose | Callers | Risk | Safe to edit? |
|---|---|---|---|---|
| `views/loader.php` | engine boot, loads pack composer + adapters + sections | `inc/frontend.php` | HIGH (boot order dependency) | CORE REVIEW |
| `views/design.php` | design slug/dir/url/manifest/defaults/complete-page flag | everywhere | HIGH (hardcoded 'vineta' defect above) | CORE REVIEW |
| `views/assets.php` | enqueues pack assets from manifest css/js lists | wp_enqueue_scripts | MEDIUM | CORE REVIEW |
| `views/renderer.php` | renders section/component with pack-first shadowing | sections/components | MEDIUM | CORE REVIEW |
| `views/viewmodel.php` | viewmodel builder consumed by adapters | adapters | MEDIUM | CORE REVIEW |
| `views/registry.php` | section registration | sections | LOW | CORE REVIEW |
| `views/composer.php` | composes pages from sections (luxury path) | templates | LOW, currently blocked | CORE REVIEW |
| `inc/frontend.php` | suppression + routing (the "traffic cop") | WP hooks | HIGH — three template_include filters interplay (99, 998) + suppression at 1000 | BRIDGE REVIEW |
| `inc/aether-security.php` | security headers + CSP nonce | wp_head/send_headers | MEDIUM — CSP interacts with inline scripts from pack (needs verification CSP doesn't break VinetaPageData inline JSON) | CORE REVIEW |
| `inc/aether-newsletter.php` | own DB table, AJAX, REST route, admin page, CSV export | pack + admin | MEDIUM — verify export capability check + REST permission callback | CORE REVIEW |
| `inc/aether-performance.php` | resource hints, HTML compression | hooks | MEDIUM — `aether_compress_html` runs on complete-page output; interaction with frozen HTML unverified | UNPROVEN |
| `inc/aether-cart.php` | cart count fragment | WC fragments filter | MEDIUM — duplicates pack composer cart count logic | DUPLICATION |
| `mu-plugins/ob-buffer.php` | buffers all frontend output until template_redirect | mu-plugin | MEDIUM — why it exists: early HTML from templates prevented WC redirects; symptom-level fix | CORE REVIEW |

## Hooks inventory (Core-owned)

- `template_include` — **three** filters: `aureon_aether_wc_page_templates` (99), `aureon_ferm_template_include` (998), plus WC's own loader. Order-dependent behavior; account routing logic exists in *both* 99 and 998 filters with a conditional escape hatch (`aether_is_complete_page_design()`).
- `wp_enqueue_scripts` — theme enqueue (20), pack assets (20, separate callback), suppression (1000), pack inline CSS (1001).
- `after_setup_theme` — menus (20), engine boot (30).
- `wp_head` — favicons (1), base tag injection (1, pack), page data (5, pack), customizer CSS (20, pack), OpenGraph (aether-seo).
- `wp_footer` — pack bridges (search, auth, jquery bridge), analytics flush.
- Body class filter — `design-vineta` appended.

## Golden Core problems (documented, not fixed)

| # | Problem | Evidence | Severity |
|---|---|---|---|
| C1 | Core contains client slug `'vineta'` as hardcoded fallback | `views/design.php` | HIGH |
| C2 | 'luxury' engine + its asset stack unreachable | same | HIGH (dead system) |
| C3 | Triple template routing with overlapping account logic | `inc/frontend.php` both filters | HIGH (fragile) |
| C4 | Suppression list is a hardcoded allowlist/denylist of handles — every new asset must be remembered by hand | `aureon_aether_suppress_theme_output` | MEDIUM (maintenance trap) |
| C5 | `aether-newsletter` creates a DB table at runtime; schema migration story absent | `aether_newsletter_maybe_create_table` | MEDIUM |
| C6 | ob-buffer mu-plugin is a symptom fix for output-before-redirect; root cause is templates echoing before WC decides to redirect (checkout form-checkout.php has its own early-redirect guard — duplicated logic) | mu-plugin + checkout template | MEDIUM |
| C7 | README/docs claim "100+ Customizer options, 800+ hooks" for the *product*; the *deployed frontend* consumes a tiny subset — docs describe capability, not current runtime | README vs suppression list | LOW (doc drift) |
| C8 | `ferm-page.php` contains an entire hardcoded fallback route map (`collections/furniture.html`, `pages/contact.html`) from the Ferm era — dead references for the vineta pack | `aureon_ferm_resolve_page()` fallbacks | MEDIUM |

## Hooks/filters that pack composer adds to Core surfaces

The pack composer (client file) filters **Core adapter data** (`aether_adapter_site_data`, `aether_adapter_header_data`, `aether_adapter_footer_data`, `aether_adapter_wc_products_data`, …), registers **AJAX endpoints**, injects **wp_head scripts**, registers **Customizer controls**, and hooks **`woocommerce_product_query`** + **`get_terms`**. This inverts the documented architecture: per `docs/architecture/*`, adapters are "the only layer allowed to touch WP/WC"; in practice the client pack is the most aggressive WP/WC toucher in the codebase.
