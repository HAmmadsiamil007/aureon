# FRONTEND DYNAMIC CONVERSION — BASELINE AUDIT

> **Status:** SUPERSEDED BY IMPLEMENTATION · **Date:** 2026-08-08 (baseline) · **Closure:** 2026-08-09
> ⚠ **This document is the pre-implementation baseline.** Since it was written, Phases A–E of the conversion were implemented in the working tree (animation hardening, WC guards, Customizer bindings, demo-content toggle, Playwright suite) and Phase F (styleguide) was added. **Authoritative current state: `docs/PHASE_17_FRONTEND_DYNAMIC_CLOSURE_REPORT.md`.** Rows below marked ❌/⚠️ for Phases A–E are historical.
> **Scope:** `frontend/` presentation layer only. **Core theme + plugin: untouched** (per mission constraint).
> **Method:** Forensic inventory of actual code (not just reports). Every claim below was verified against the working tree on 2026-08-08, cross-checked with `aureon-doc/STATUS.md` (Phase 17 / Stage 13 handoff) and the git working state.

---

## 0. Executive summary

The premium frontend is **already ~90% converted** to a data-driven AUREON frontend through a staged rebuild (Phase 17 / Stages 1–13, see `aureon-doc/STATUS.md`). The architecture contract is intact and healthy:

```
WordPress / WooCommerce / Customizer
        ↓
AUREON Settings / Data Sources  (aureon_get_option + tokens)
        ↓
Data Adapters                   (frontend/adapters/*.php — only layer touching WP/WC)
        ↓
ViewModels                      (frontend/views/viewmodel.php)
        ↓
Component Registry              (frontend/manifest/components.php)
        ↓
Template Composer               (frontend/views/composer.php)
        ↓
Premium Frontend                (frontend/components/* + frontend/sections/*)
```

The task is therefore **NOT a from-scratch conversion**. It is a **gap-closure, hardening, and verification mission** over an already-working integration. The remaining work concentrates in 6 areas, ranked by risk:

| # | Area | Risk | Evidence |
|---|------|------|----------|
| 1 | **Animation failure-mode** — content can be permanently hidden if GSAP/ScrollTrigger CDN fails while `animations.js` loads (Rule 7 violation) | 🔴 HIGH | `animations.js` adds `has-motion` **before** the GSAP guard; `motion.css` hides `[data-reveal]` under `has-motion`; no watchdog/timeout fallback |
| 2 | **Demo fallback content** — `tokens.php` ships full demo catalog (products, reviews, team, FAQ, hero) used when real data is empty | 🟠 MEDIUM | `tokens.php` `aether_product_items` ($449/$99/$479/$69), `aether_faq_items`, `aether_testimonial_items`, etc. |
| 3 | **Customizer round-trip gaps** — layout/color controls exist, but several section-level settings (hero slides, categories title/subtitle) are wired via adapter args, not live-preview bound | 🟠 MEDIUM | `customizer/fields/frontend.php` + `adapter-wc-categories.php` args |
| 4 | **Hardcoded shell strings** — announcement items, footer links, fallback menu are hardcoded in adapters (Customizer text control exists but adapter ignores DB value) | 🟡 LOW | `adapter-shell.php` comment: *"Always use premium marquee items — Customizer DB values are legacy"* |
| 5 | **Direct WP/WC calls in adapters** — allowed by contract (adapters are the WP boundary), but several are unguarded (no `function_exists`) | 🟡 LOW | `adapter-wc-products.php` calls `wc_get_product_ids_on_sale()` unguarded |
| 6 | **Verification gaps** — no Playwright suite committed, no visual regression harness, no failure-injection tests | 🟡 LOW | `frontend/tests/verify.sh` is grep-only; STATUS documents manual Playwright sweeps |

**Verdict: architecture PASSES. Preservation PASSES (design unchanged by the conversion).** The mission's remaining work is targeted hardening + a committed verification suite + demo-data policy — NOT redesign, NOT core surgery.

---

## 1. Current frontend architecture

Verified source of truth: `frontend/views/*`, `frontend/manifest/components.php`.

| Layer | File | Role | Verified state |
|---|---|---|---|
| Loader | `views/loader.php` | Defines `AETHER_FRONTEND_DIR`, includes tokens → registry → renderer → viewmodel → composer → all adapters (glob) → all sections (glob) | ✅ Boots via `aureon_aether_frontend_boot()` on `after_setup_theme` (30) |
| Registry | `views/registry.php` | Global `$aether_section_registry`; `aether_register_section(id, {template, adapter, adapter_args, behavior})` | ✅ |
| Renderer | `views/renderer.php` | `aether_render_component()` (manifest lookup + `aether_component_data` filter); `aether_render_section()` (adapter resolution, `adapter_args` merge with per-call `$data` winning, flat arrays → `items`, `aether_section_data` filter); `aether_behavior_attrs()` whitelist | ✅ **Fixed bugs verified:** multi-word adapter fn (`adapter-wc-products` → `aether_adapter_wc_products`) and adapter_args precedence both resolved |
| ViewModel | `views/viewmodel.php` | `aether_viewmodel_image()`, `aether_viewmodel_resolve_image()` (content_url prefix), `aether_viewmodel_merge()`, `aether_viewmodel_behavior()` (honors 5 motion toggles) | ✅ |
| Composer | `views/composer.php` | `aether_compose_header()` / `aether_compose_footer()` — shell composition, single theme-facing entry | ✅ |
| Manifest | `manifest/components.php` | 53 component entries (verified count — supersedes STATUS's "39") | ✅ All resolve |
| Adapters | `adapters/*.php` (23 files) | The **only** layer calling WP/WC | ✅ |
| Sections | `sections/*.php` (26 files) | Register + render guard (`if (!isset($sectionData)) return;`) | ✅ |
| Tokens | `tokens/tokens.php` | Registers ~90 AETHER option defaults on `aureon_option_defaults` / `aureon_color_option_defaults` / `aureon_font_option_defaults` | ✅ |

**Theme bridge** (`aureon/theme/inc/frontend.php` — NOT to be modified without a stop-condition report):
- Requires `frontend/views/loader.php` + 6 hardening inc files (`aether-tokens`, `-security`, `-seo`, `-newsletter`, `-ajax`, `-performance`).
- Suppresses theme layout styles/scripts at priority 1000 (keeps `aureon-google-fonts` for the Font Manager bridge).
- Enqueues CDN (bootstrap 5.3.3, FA 6.5.1, swiper 11, gsap 3.12.5, ScrollTrigger 3.12.5, lenis 1.1.19) + local css/js (6 CSS, 6 JS + countdown).
- Routes WC cart/checkout/account/order-received templates via `template_include` (99).
- Localizes `aetherAjax` (ajaxUrl/nonce/restUrl/isUserLoggedIn/shopUrl/searchUrl).

---

## 2. Current AUREON architecture (as consumed by the frontend)

- **Settings bucket:** single `aureon_settings` option array; `aureon_get_option()` reads it with `aureon_get_defaults()`.
- **Token pipeline:** `aether_frontend_defaults()` (tokens.php) → `aureon_get_option` → `inc/aether-tokens.php` `aether_generate_tokens_css()` → inline `:root` block (priority 98 enqueue). Customizer-aware (no `is_customize_preview()` bail).
- **Color precedence** (`aether_resolve_color`): explicit `aether_color_*` option → customized `global_colors` palette (React Color Manager, slug-mapped) → AETHER default.
- **Font precedence** (`aether_font_for`): explicit `aether_font_*` → Typography Manager entry (`body` / `all-headings`) → classic font option → default (Cabinet Grotesk / Satoshi).
- **Motion toggles:** 5 Customizer flags (`aether_motion_enabled/reveal/tilt/parallax/text`) honored by `aether_viewmodel_behavior()`.
- **Section gating:** 16 `aether_section_*` toggles consumed by all theme page templates.

---

## 3. Current component inventory

Source: `frontend/manifest/components.php` (verified = **53 entries**, 3 of which are referenced only conditionally).

| Group | Components |
|---|---|
| Shell (7) | preloader, fog, skip-link, announcement, header, mobile-chrome, footer |
| Hero (4) | slider, slide, page-title, page-banner |
| Section (6) | header, filter-bar, accordion, cta, newsletter, pagination |
| Cards (6) | product (home+shop variants), category, blog, review, team, wishlist |
| Cart/Checkout/Account (5) | cart/items, cart/summary, checkout/order-items, account/profile, account/orders |
| Auth (1) | auth/password-strength |
| Order (1) | order/confirmation |
| Commerce (2) | commerce/rating, commerce/quick-view |
| Product (9) | breadcrumb, gallery, info, sticky-bar, specs, reviews, related, size-guide (+commerce/rating) |
| Content (6) | page, article-hero, article-meta, article-body, author-bio, story |
| Forms (5) | contact, login, register, newsletter, forgot-password |
| Utility (2) | error/404, soon/countdown |

**Contract compliance:** components receive normalized `$componentData`; they call **no** WP/WC functions (grep gate in `tests/verify.sh` documents the rule; the single known hit — `mobile-chrome.php` reading `home_url` from data — is a false positive). Escaping verified: `esc_html`/`esc_attr`/`esc_url` used consistently; `product.php` `$price` output is a documented intentional exception (`wc_price` HTML passthrough with phpcs ignore).

---

## 4. Current template inventory

All theme templates are **pure section composition** (verified in working tree):

| Template | Composition |
|---|---|
| `front-page.php` | hero → categories → bestsellers → reviews → faq → newsletter (all gated) |
| `home.php` | page-title + blog-grid + newsletter |
| `single.php` | blog-single (post_id) + blog-grid related (3, category, post__not_in) + newsletter |
| `single-product.php` | product + related (related_to, 4) + newsletter |
| `cart.php` | section-cart + newsletter |
| `archive-product.php` | shop-hero + shop-filter + shop-grid (paged/tax/on_sale) + newsletter |
| `page-{about,contact,faq,team,wishlist,login,register,coming-soon}.php` | gated section composition |
| `myaccount/my-account.php` | page-banner + account/profile + account/orders |
| `woocommerce/checkout/thankyou.php` | order-confirmation + newsletter |
| `search.php` | page-title + blog-grid (s) + newsletter |
| `404.php` | error/404 component |
| `header.php` / `footer.php` | delegate to composer |

---

## 5. Current Customizer settings (AETHER group)

Source: `aureon/theme/inc/customizer/fields/frontend.php` — section **"AETHER Frontend"** (`aureon_aether_section`, priority 120). Verified controls:

- **Section Visibility (16 checkboxes):** hero, categories, bestsellers, reviews, faq, newsletter, mission, features, story, stats, team, values, contact, auth, wishlist, coming_soon.
- **Shell & Motion (8):** preloader, fog, announcement (+ motion_enabled, reveal, tilt, parallax, text).
- **Announcement & Commerce (3):** announcement_text, announcement_url, shop_per_page.
- **Design — Colors (11):** bg, surface, surface_2, surface_3, text, muted, accent, accent_hover, border, error, success — empty = inherit palette.
- **Design — Layout (8 sliders + text):** container_max, announcement_height, header_height, grid_gap, radius_sm/md/lg, section_padding.
- Sanitizers: checkbox / text / url / absint / hex / integer; transport = refresh.

**Gap:** hero slides and section copy (categories title/subtitle, newsletter copy, contact info) are **not** exposed as controls — they flow via adapter args / token defaults / hardcoded adapter values. Live-preview binding for colors/layout works via `aether-tokens.php` (customizer-aware). React Color Manager + Typography Manager (theme-owned panels) feed the token resolvers.

---

## 6. Current design tokens

- **CSS consumption (verified):** `--void`, `--surface`, `--chrome`, `--gold`, `--font-heading`, `--font-body`, `--container-max` in `style.css`/`responsive.css`/`pages.css`; hardcoded fallbacks in `:root` (style.css:4-13) are **overridden** by the dynamic inline block.
- **Dynamic `:root` emission (`inc/aether-tokens.php`, theme-side):** 12 colors, 2 font stacks, 9 layout tokens (container, section padding, announcement/header heights, grid gap, radii incl. pill). NOTE — this file lives in the **theme**; per mission constraints it is read-only; any change = stop-condition report.
- **Configurable-but-unbound values (frontend-side gap):** hero slide content, category grid copy, testimonial/team/FAQ content, footer copy, announcement items (adapter hardcodes), size table, product spec copy.

**Rule 5 compliance:** semantics-configurable values are tokenized. Intentional design constants (spacing rhythm in CSS, card paddings, animation presets) remain static — correct per the mission ("Do NOT blindly replace every literal value").

---

## 7. Current WooCommerce data flow

| Surface | Adapter | WC API used | Verified |
|---|---|---|---|
| Product cards (home/shop/related) | `adapter-wc-products.php` | `wc_get_product`, `get_price_html`, `wc_get_product_ids_on_sale`, `wc_get_related_products`, `WP_Query` + `total_sales` | ✅ real data |
| Category grid | `adapter-wc-categories.php` | `get_terms('product_cat')`, `wp_count_terms`, term thumbnail → first product image → `wc_placeholder_img_src` | ✅ real data |
| Shop filter | `adapter-wc-filter.php` | `get_terms`, `wc_get_product_ids_on_sale` | ✅ |
| Shop hero | `adapter-shop-hero.php` | `woocommerce_page_title`, `wc_get_page_id` | ✅ |
| Single product | `adapter-product.php` | `wc_get_product`, gallery ids, attributes (`pa_color`, `pa_size`), review comments, rating counts | ✅ real + demo fallback |
| Cart | `adapter-cart.php` | `WC()->cart`, `wc_get_cart_remove_url`, totals | ✅ |
| Order confirmation | `adapter-order.php` | `wc_get_order` | ✅ |
| Account | `adapter-account.php` | `wc_get_customer_order_count`, `wc_get_orders`, endpoint URLs | ✅ |
| Wishlist | `adapter-wishlist.php` | `WC_Query` over user meta + `wc_get_product` | ✅ |
| Header/mobile cart count | `adapter-shell.php` | `WC()->cart->get_cart_contents_count()` | ✅ |

**Rule 4 compliance:** no component queries WC directly; WC business logic is never recreated. `?add-to-cart={id}` classic flow used. **Unguarded calls** (small risk): `wc_get_product_ids_on_sale()` / `wc_get_related_products()` in `adapter-wc-products.php` assume WooCommerce active (theme gates template anyway, but adapters are loaded globally).

---

## 8. Current plugin feature flow

- **Theme hardening** (`inc/aether-*.php`, theme-side, read-only): security headers, SEO (OG/Twitter/JSON-LD/canonical), DB-backed newsletter (`wp_aether_newsletter_subscribers` + REST `/aether/v1/newsletter/subscribe`), wishlist AJAX (`aether-ajax.php`), performance (preloads, ver-stripping, WC script gating).
- **Companion plugin (Aureon Studio):** 16 modules; WC styling/session bridge (`mu-plugins/aureon-fix-wc-session.php` — early `WC()->session` init + payment null-guard); plugin `template-locator.php` WC override bridge was **removed** in Stage 11 — WC template routing now lives in theme `template_include` filter.
- **Third-party bridges:** ACF/Rank Math/WPML/etc. remain theme/plugin-owned guarded bridges; frontend does not re-implement any.

---

## 9. Current static / hardcoded values (frontend)

| Location | Value | Classification |
|---|---|---|
| `tokens/tokens.php` `aether_product_items` | 4 demo products `$449/$99/$479/$69` | REPLACE (fallback only when store empty) |
| `tokens/tokens.php` `aether_category_items`, `aether_faq_items` (6), `aether_testimonial_items` (4), `aether_team_items` (4), hero slides (3, same image), size table, specs, score bars, product reviews | full demo catalog | REPLACE / CONFIGURE |
| `adapter-shell.php` announcement | 4 hardcoded marquee strings (Customizer `aether_announcement_text` ignored) | CONFIGURE (bind to settings) |
| `adapter-site.php` footer | 4 link columns hardcoded (Men/Women/Kids/…, FAQ/Contact/…) | CONFIGURE |
| `adapter-menu.php` fallback | About/Blog/FAQ/Contact + Shop dropdown | CONFIGURE (fallback only — WP menu wins) |
| `adapter-contact.php` | `admin_email` fallback `support@example.com` | CONFIGURE |
| `adapter-wc-categories.php` fallback | curated SKU-based categories | REPLACE (fallback only) |
| `main.js` search overlay | "Popular Searches" suggestion labels | KEEP (intentional UX; links already dynamic) |
| `source/*.html` | static demo HTML (design reference only, excluded from deploy) | KEEP (reference) |
| CSS `:root` fallbacks | hardcoded hexes (style.css:4-13) | TOKENIZE-overridden (dynamic block wins; keep as no-JS/CSS-first fallback) |

---

## 10. Current dynamic values

All adapters' primary output: site identity (`get_bloginfo`), WP menus (`wp_get_nav_menu_items` + tree builder), WC products/categories/prices/ratings/galleries/variations, cart totals/count, orders, account endpoints, blog `WP_Query` + pagination, search results, page titles, post content/meta, hero slides (Customizer repeater JSON), motion/section toggles, colors/layout/fonts via tokens. This is the *intended* majority of the system.

---

## 11. Current direct WordPress calls (by layer)

- **Adapters (allowed):** `get_bloginfo`, `home_url`, `get_permalink`, `wp_get_nav_menu_items`, `WP_Query`, `get_posts`, `get_terms`, `get_term_link`, `get_option` (admin_email, default_product_cat), `wp_get_attachment_image_url`, `get_post_meta`, `get_page_by_path`, `get_the_post_thumbnail_url`, `wp_count_terms`, `wp_login_url`.
- **Components:** **zero** (verified).
- **Sections:** `home_url` only as URL fallbacks in section templates (`section-cart.php`, `section-faq.php`, `section-shop-grid.php` — the latter reads `$_SERVER['REQUEST_URI']` for pagination base, phpcs-tagged). Minor: sections should prefer `$sectionData` — acceptable as presentation fallback.
- **Views/composer:** `aureon_get_option` (allowed — settings layer).

---

## 12. Current direct WooCommerce calls

All inside adapters (allowed boundary): `wc_get_page_permalink`, `wc_get_cart_url`, `wc_get_checkout_url`, `wc_get_product`, `wc_get_product_ids_on_sale`, `wc_get_related_products`, `wc_get_product_id_by_sku`, `wc_price`, `wc_placeholder_img_src`, `WC()->cart`, `wc_get_orders`, `wc_get_customer_order_count`, `wc_get_endpoint_url`, `wc_get_account_endpoint_url`, `wc_get_cart_remove_url`, `wc_get_order`, `woocommerce_page_title`, `wc_get_page_id`, `get_woocommerce_currency`. **No component-level WC calls.**

---

## 13. Current frontend / core mismatches

1. **Announcement:** Customizer text/url controls exist but `adapter-shell.php` ignores them (comment: "legacy"). Ownership ambiguity → per STOP conditions, this is a *contract* question: is the marquee a design constant (KEEP) or a setting (CONFIGURE)? **Recommendation: CONFIGURE via `aether_announcement_items` with current strings as defaults.**
2. **Demo fallback policy:** adapters fall back to demo tokens when real data is empty. This is intentional (pixel-visible before content), but means a **fresh store shows fake products** unless `cleanup-demo.php`/`boost-products.php` run. Needs an explicit policy toggle (e.g. `aether_demo_content` option).
3. **Hero slides:** default 3 slides share one image; editor repeater + legacy shape normalized in adapter. The Customizer has **no hero repeater control** (tokens default is the only source besides DB JSON).
4. **Fonts:** `aureon-google-fonts` kept (dynamic Typography Manager) while `aureon-fonts` suppressed — deliberate, documented, correct.
5. **`$_SERVER['REQUEST_URI']`** in `section-shop-grid.php` pagination base — fine but prefers `get_pagenum_link`/WC canonical for robustness.
6. **Unguarded WC calls** in `adapter-wc-products.php` (`wc_get_product_ids_on_sale`, `wc_get_related_products`) — add `function_exists` guards for degraded-mode safety.

---

## 14. Current animation dependencies

**Dependency graph (verified):** bootstrap 5.3.3 + FA 6.5.1 + swiper 11 + GSAP 3.12.5 + ScrollTrigger 3.12.5 (CDN) → `lenis-scroll.js` (lenis 1.1.19) → `animations.js` → `main.js` → `phantom-bridge.js`; `countdown.js` standalone. All local JS via `filemtime` versions; CDN ver-stripped.

**Rule 7 (animation must never control visibility) — AUDIT RESULT: ✅ FIXED (Phase A implemented 2026-08-09 — see closure report §3).**

- ✅ JS disabled: no `has-motion` class → CSS never hides → fully visible.
- ✅ Reduced motion: JS adds `no-motion` **and** CSS `@media (prefers-reduced-motion: reduce)` force-visible — doubly covered.
- ✅ GSAP+ScrollTrigger loaded normally: `has-motion` set → CSS hides → GSAP `fromTo`/`fromTo` reveals on scroll (`once:true`) → visible.
- ✅ **GSAP or ScrollTrigger CDN fails/blocked but `animations.js` executes:** **FIXED** — `has-motion` is only added **after** the `typeof gsap === 'undefined'` / `typeof ScrollTrigger === 'undefined'` gate; failure path calls `disableMotion()` (removes `has-motion`, adds `no-motion` → CSS force-visibles everything). A 2.5 s watchdog + try/catch/finally provide belt-and-braces. (animations.js:12–38, 1017–1024.)
- ✅ Runtime exception inside `init()`: **FIXED** — `init()` wrapped in try/catch; any failure → `disableMotion()`; `finally` clears the watchdog (animations.js:1017–1024).
- ⚠️ `page-load` fade: `body.page-load { animation: pageLoadFadeIn }` starts at `opacity:0` — brief flash risk only (0.6s, `forwards`; acceptable).

**Required fix — IMPLEMENTED (2026-08-09):** `animations.js` now verifies GSAP+ScrollTrigger **before** adding `has-motion`; missing → `disableMotion()` + return; 2.5 s watchdog force-adds `no-motion` unless init completed; `init()` wrapped in try/catch with the same fallback; orphan `[data-reveal-item]` and plain-text `[data-motion-text]` explicitly revealed; preloader got a `<noscript>` kill rule. Acceptance automated in `frontend/tests/specs/failure-injection.spec.js`.

---

## 15. Current responsive behavior

- CSS layers: `responsive.css` (breakpoints ~1024/820/768/767/480/390/375), `a11y.css`, `motion.css` (mobile will-change off; `pointer: coarse` magnetic/tilt off).
- Shell: mobile-chrome (header + slide-out menu) vs desktop header; announcement marquee; hamburger + `mobileMenuBtn`.
- Grids: `products-grid`/`category-grid` collapse; product card variants (home/shop); shop filter horizontal scroll.
- STATUS Stage-13 live sweeps: all routes 200 at desktop/mobile, 0 console errors. **No committed automated responsive regression suite** (manual Playwright only).

---

## 16. Current known visual problems

1. **`has-motion` + GSAP failure = invisible content** (see §14) — the only *critical* visual risk.
2. Demo fallback images: all demo products/categories/hero slides reference **one** sneaker photo (`Luxury_running_sneaker_on_pedestal_202607222032.jpeg`) → a fresh store shows the same image 12×. Cosmetic, replaced by real data.
3. `product-card` home layout "Add to Cart" button links to product permalink (not a true add-to-cart) — intentional per Stage 13 routing fix; shop layout button same. If a real add-to-cart UX is required, that's a feature decision (deferred).
4. `main.js` "Popular Searches" labels are static suggestions (Void Runner / Cloud Stride / New Arrivals) — links dynamic; labels are marketing copy (KEEP or CONFIGURE).
5. Announcement/footer/size-table/specs copy is adapter/token-hardcoded (no Customizer UI) — content ownership gap, not a visual defect.
6. Preloader: source contract removes it via its own JS; if JS disabled it persists (needs CSS fallback `@media (scripting: none)` or noscript — check `preloader.php`/`main.js:742`).

---

## 17. Conversion risks

| Risk | Level | Mitigation |
|---|---|---|
| Breaking Rule 7 fix by touching theme enqueue order | HIGH | Fix lives 100% in `frontend/assets/js/animations.js` + `motion.css` — no theme change |
| `source/` drift — design reference diverges from PHP components | MEDIUM | `frontend/source/` is pristine reference (364 files, read-only mirror); any visual question resolved against it + screenshots |
| Demo fallback appearing as "real" content in production | MEDIUM | Add explicit `aether_demo_content` master toggle; default OFF in production |
| Customizer live-preview regressions when binding announcement/footer | MEDIUM | Keep transport refresh; extend `aether-tokens.php` only if unavoidable (theme read-only → then stop-condition report) |
| Unguarded WC calls fatal on non-WC installs | LOW | `function_exists` guards in adapters (frontend-only) |
| Duplicating presentation (e.g. adding a styleguide with demo-only components) | LOW | Styleguide must reuse manifest components only (§16 of mission) |
| Playwright suite committing local creds (as happened with `.playwright-mcp/`) | LOW | Use `?nocache` + env-driven base URL; never commit snapshots w/ creds (STATUS Stage 13 lesson) |

---

## 18. Recommended conversion order

Based on verified state, NOT the original 30-section plan (much of it is already done). Each step = small commit + gate.

| Phase | Scope | Files (frontend-only) | Gate |
|---|---|---|---|
| **A — Animation hardening (Rule 7)** | Watchdog: move `has-motion` after GSAP check; timeout fallback; try/catch init; `@media (scripting: none)` preloader fallback | `assets/js/animations.js`, `assets/css/motion.css`, `components/shell/preloader.php` | Browser: block GSAP via devtools → content visible; node --check |
| **B — Data-source completeness** | `function_exists` guards in WC adapters; `$_SERVER` pagination hardening; adapter arg binding for hero/categories copy | `adapters/adapter-wc-products.php`, `adapter-wc-filter.php`, `sections/section-shop-grid.php`, `adapters/adapter-hero.php` | php -l; route sweep |
| **C — Customizer round-trip closure** | Bind announcement items + footer copy + hero slides to settings via adapters (defaults preserved); expose missing controls if feasible frontend-side | `adapters/adapter-shell.php`, `adapters/adapter-site.php`, `adapters/adapter-hero.php`, `tokens/tokens.php` | Customizer live check (manual + Playwright) |
| **D — Demo-content policy** | `aether_demo_content` master toggle gating all demo fallbacks; keep fallbacks for empty stores | `tokens/tokens.php`, all adapters with fallbacks | Fresh-store vs seeded-store render diff |
| **E — Verification suite** | Committed Playwright suite (routes, console, key interactions) + failure-injection test (GSAP blocked, empty data) + visual snapshots to `frontend/tests/` | `frontend/tests/playwright/*`, `frontend/tests/verify.sh` update | `npx playwright test` green |
| **F — Reports + styleguide** | `FRONTEND_DATA_CONTRACT.md`, `FRONTEND_COMPONENT_DYNAMICITY_MATRIX.md`, `CUSTOMIZER_FRONTEND_BINDING_MATRIX.md`, `WOO_FRONTEND_BINDING_MATRIX.md`, `FRONTEND_CONVERSION_REPORT.md`, styleguide reusing manifest components | `docs/*`, `/styleguide/` | Review gates |

**Explicitly OUT of scope (no core theme/plugin changes):** `aureon/theme/inc/aether-tokens.php`, `inc/frontend.php`, `inc/customizer/fields/frontend.php`, plugin modules, mu-plugins. If any phase proves a core change is objectively required, a stop-condition report will be produced (Problem / Evidence / Affected files / Options / Recommended / Risk / Core-change-required?) before any edit.

---

## 19. Acceptance criteria status (as of baseline)

| Criterion | Status |
|---|---|
| Existing premium UI visually intact | ✅ (design = source of truth, preserved through 13 stages) |
| Responsive design intact | ✅ (manual sweeps; automated suite pending — Phase E) |
| Animations intact when available | ✅ (normal path) |
| **Animation failure never hides content** | ✅ **FIXED (2026-08-09)** — guard-first + watchdog + try/catch (see closure report §3) |
| JS disabled renders usable content | ✅ (no-motion path; preloader edge pending) |
| Customizer changes affect frontend | ✅ (colors/layout/motion/sections verified; copy controls pending Phase C) |
| WooCommerce data renders correctly | ✅ (6 real products, categories, cart, orders verified) |
| Menus use WordPress menus | ✅ (`wp_get_nav_menu_items` + graceful fallback) |
| Blog uses WordPress content | ✅ (WP_Query + pagination) |
| Tokens control configurable visuals | ✅ (dynamic `:root` emission verified) |
| No regex HTML injection | ✅ (no string-replace DOM hacks; adapter architecture) |
| No duplicate component implementation | ✅ (single manifest source of truth) |
| No core architecture regression | ✅ (13 stages, 0 console errors documented) |
| Playwright / visual regression committed | ✅ suite committed (`frontend/tests/`); **live re-run NOT VERIFIED** (Docker stack down — closure report §7) |

---

*Next step per mission rules: present findings + conversion map (this document), then await approval before Phase A implementation. No production code was modified during this audit.*
