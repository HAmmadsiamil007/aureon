# FRONTEND_FORENSIC_REPORT — Why the AETHER Integration Failed

**Date:** 2026-08-07
**Phase:** 17.1 — STEP 1 (Forensic Analysis)
**Status:** COMPLETE — root causes identified, deletion executed, source restored
**Author:** Forensic pass over evidence gathered 2026-08-06/07 (3 parallel exploration reports + live-container verification + git archaeology)

---

## 1. Executive Summary

The Phase 17 AETHER frontend integration **did not fail because of the component framework** — the render engine (`frontend/views/`), component registry (`frontend/manifest/`), tokens, and adapters were verified sound (39/39 manifest resolution, zero syntax errors, live render with 0 console errors). It failed because of **the integration strategy**: AETHER was *layered on top of* the theme's existing GeneratePress-derived layout engine instead of *replacing* it. The result was **two complete design systems rendering simultaneously** — duplicated DOM, duplicated assets, duplicated scripts, and mutually conflicting presentation output on every page type.

Five root causes, in order of severity:

1. **No ownership boundary (architecture)** — components were injected into theme hooks (`aureon_before_header`, `aureon_header`, `wp_footer`, …) while the theme's own header/footer/nav/search/widget output remained active. Mission STEP 9 ("suppress default WP output") was **never implemented** — the codebase contained zero `remove_action()` calls.
2. **Partial template coverage (surfaces)** — only front-page/home/404/5 static pages were rewritten; blog archive, single posts, search, category archives, and all WC pages fell back to stock theme output → two designs on one site.
3. **Asset pipeline double-loading (performance/conflict)** — theme `main.min.css` + dynamic inline CSS + `menu.min.js` + `back-to-top.min.js` ran alongside `frontend.css` + vendor bundle + `aether-*.js`. Duplicated CSS rules and duplicated behaviors (smooth scroll ×3, cart JS ×2, menu JS ×2).
4. **Hook-level DOM duplication (defects)** — identical UI artifacts rendered twice with different markup: search (4 renderers), cart icon (2), mini-cart (2), newsletter (2), skip-link (2), back-to-top (2), announcement (2 sources).
5. **Asset/content integrity drift (quality)** — orphaned files, dead REST route, dead templates, hardcoded data bypassing the data layer, unguarded calls, option-default mismatches.

---

## 2. Evidence — Duplication Inventory

### 2.1 Search — up to 4 concurrent renderers
| Renderer | Location | Gate | Status in failed state |
|---|---|---|---|
| Classic search form | `theme/searchform.php` | always | **ACTIVE** |
| Search modal `#aureon-search` | `theme/inc/structure/search-modal.php:12` | `nav_search_modal` (default false) | OFF by default |
| Nav search bar | `theme/inc/structure/navigation.php:540` | `nav_search` (default disable) | OFF by default |
| AETHER search modal + icon | `inc/frontend.php:239` (`wp_footer:30`) + header-actions `:227` (prio 15) | **UNCONDITIONAL** | **ACTIVE** |

→ Result: theme search form + AETHER modal search icon in the same header.

### 2.2 Cart — 2 icons + 2 drawers
| Renderer | Location | Gate | Status |
|---|---|---|---|
| Plugin WC menu cart item | `plugin/woocommerce/functions/functions.php:565` (`aureon_menu_bar_items` prio 5) | module on | **ACTIVE** |
| Plugin WC mini-cart drawer `#wc-mini-cart` | `plugin/.../functions.php:686` | gated | conditional |
| AETHER header-actions cart icon | `inc/frontend.php:227` (prio 15) | **UNCONDITIONAL** | **ACTIVE** |
| AETHER nav mini-cart drawer | `inc/frontend.php:240` (`wp_footer:31`) | **UNCONDITIONAL** | **ACTIVE** |

→ Result: two cart icons, two drawers, both cart JS modules on the same pages.

### 2.3 Menus — 3 renderers
| Renderer | Location | Gate | Status |
|---|---|---|---|
| Theme `wp_nav_menu` primary | `theme/inc/structure/navigation.php:73` | always | **ACTIVE** |
| Plugin menu-plus slideout nav | `plugin/inc/modules/menu-plus.php:1231` (`wp_footer` prio 0) | module on | **ACTIVE** |
| AETHER mobile-chrome menu | `inc/frontend.php:345-351` | **UNCONDITIONAL** | **ACTIVE** |

→ Result: mobile users got both the theme slide-out nav AND the AETHER mobile menu; the AETHER menu additionally used a **hardcoded link array with dead `#` hrefs**, bypassing the menu system entirely (mission STEP 7 violation: adapters must supply nav data).

### 2.4 Newsletter — rendered twice on the front page
| Renderer | Location | Gate |
|---|---|---|
| Section render | `theme/front-page.php:35` | option |
| Hooked render | `inc/frontend.php:233` (`aureon_after_footer_widgets`) | same option |

→ Same option gates both → the newsletter section appeared twice (and as a nested `<section>` inside `<section>` — invalid HTML).

### 2.5 Skip-link / back-to-top
- **Skip links ×2:** theme (`header.php`, a11y) + AETHER shell component (`aureon_before_header` prio 5).
- **Back-to-top ×2:** theme `back-to-top.min.js` + gated output; AETHER back-to-top component (unconditional).

### 2.6 CSS — two full design systems
| Source | Rules | Status in failed state |
|---|---|---|
| Theme `main.min.css` + inline dynamic CSS (`inc/dynamic-css/css-output.php:1231`) | GP-style system | **ACTIVE** |
| `frontend/assets/css/frontend.css` + `responsive.css` + `motion.css` + `a11y.css` | AETHER system | **ACTIVE** |
| Plugin `woocommerce.css` + `woocommerce-mobile.css` + blog css | WC module | **ACTIVE** |
| `frontend/assets/css/style.css` + `responsive.css` (imported copies) | **ORPHANS — never enqueued** | dead |
| Vendor loose copies (`bootstrap.min.js`, `gsap`…) | **ORPHANS** | dead |

→ Result: conflicting rules for the same selectors (theme body colors vs AETHER `--void` tokens); `style.css` (the actual 4,550-line design system) was **never loaded** — a hand-reduced `frontend.css` was used instead.

### 2.7 JS — overlapping concerns
| Concern | Theme | Plugin | AETHER |
|---|---|---|---|
| Smooth scroll | `back-to-top.min.js` | `smooth-scroll.php` | `aether-lenis.js` |
| Menu/mobile nav | `menu.min.js` | menu-plus module | mobile-chrome component |
| Cart | — | `woocommerce.js` | `aether-cart.js` + `aether-gallery.js` (same pages) |
| Back-to-top | `back-to-top.min.js` | — | component |
| Orphans | — | — | `animations.js`, `lenis-scroll.js`, `phantom-bridge.js` (in `frontend/assets/js/` — superseded, never enqueued) |

### 2.8 WooCommerce — double system
- Plugin WC module: `wc-column-container` wrappers, cart links, sticky cart panel, hover image swap, ~200 dynamic WC CSS rules.
- AETHER: hero/page-title on shop/cart/checkout/account + gallery/cart JS + mini-cart drawer.
- Theme had **no** `woocommerce/` overrides in the restored base (the earlier `theme/woocommerce/*` files were part of the first-attempt rollback) — plugin `templates/` overrides (created Phase 17) injected AETHER hero markup into WC pages **without disabling WC's own header/shop output**.
- `wc_products` adapter was verified correct; the failure was that WC pages rendered BOTH WC-native markup and AETHER wrappers.

### 2.9 Template surface inconsistency
| Surface | Failed state |
|---|---|
| Front page, home, 404, about, contact, faq, team, testimonials, wishlist | AETHER rewrites |
| Blog archive (`index.php`), single (`single.php`), search, category/tag archives | **Stock theme** (never rewritten) |
| Shop/cart/checkout/account | WC stock + plugin module + AETHER hero wrappers |
| `frontend/templates/{front-page,page-about,page-contact,page-faq}.php` | **ORPHANS** — used `get_header('aureon')` which nothing called; duplicate source of truth |

→ Site presented 3 different design languages (AETHER home, GP-style blog, hybrid shop).

### 2.10 Integrity defects found
| Defect | Location |
|---|---|
| Unguarded registry call (fatal if framework missing) | `front-page.php:21` — `aether_section_registry()` no `function_exists` |
| Dead REST route | `aetherData.restUrl` → `aureon/v1/frontend/` — **never registered** |
| Announcement default mismatch | tokens default `false` vs customizer default `true` → toggle appeared inverted |
| Undefined var in customizer field | `inc/customizer/fields/frontend.php:233` — `$color_defaults` (control default fell back to `#FFFFFF`) |
| Bootstrap version discrepancy | `frontend.css` built from bootstrap **5.3.3**; vendor bundle contained bootstrap **4.6.2** JS+CSS (verified by header grep) |
| Swiper version discrepancy | vendor bundle + standalone = **11.2.10** (both, consistent) — earlier record "11.0.0" was wrong |
| Content formatting | `page-about` rendered `get_the_content()` raw → **no `wpautop`** → broken paragraphs |
| Hardcoded data | mobile-chrome menu links `#` (bypasses menus) |
| `home_url` false positive | `mobile-chrome.php` reads `home_url` *data key* — confirmed NOT a call |

### 2.11 What was verified working (NOT part of the failure)
- Render engine (`views/renderer.php` `aether_render_section()`, `viewmodel.php`) — 39/39 manifest keys resolve; all 28 `aether_render_component()` calls match manifest.
- Adapter contracts: options (`$key=>$default`), faq/testimonials/team (CPT), wc-products (`tilt`).
- Helpers: `aureon_do_attr`, `aureon_construct_sidebars`.
- Live container: homepage 200, 0 console errors, hero 3 slides, newsletter/search/modal/mini-cart rendered; empty-state no-ops correct.
- `wp_enqueue_style('aether-tokens', false, …)` bug (WP 6.9.1 drops dependents) — the only *engine* defect found; fix pattern: `wp_register_style($handle, false)` + `wp_enqueue_style($handle)`.

---

## 3. Root Cause (canonical statement)

> **The integration injected a second, complete presentation system into a theme that already had one, without suppressing the first, without covering all surfaces, and without unifying the asset pipeline. The component framework was sound; the *deployment* of it was not.**

The design principle for the rebuild (per mission): **WordPress/WooCommerce own data; the Aureon frontend owns 100% presentation** — enforced by template ownership (not hook layering), output suppression, a single asset system, and a single token source.

---

## 4. Execution Record — STEP 2 (Delete) + STEP 3 (Restore)

### 4.1 Safety backup (created first)
- `C:\Users\hamma\AppData\Local\Temp\opencode\phase17-integration-backup.tar.gz` (4.7 MB) — full `frontend/` + all deleted integration files.

### 4.2 Restored from git HEAD `3e5741a` (8 integration-modified files + 2 planning docs)
```
aureon/theme/404.php
aureon/theme/front-page.php
aureon/theme/functions.php            (removed require inc/frontend.php)
aureon/theme/inc/customizer.php
aureon/theme/inc/customizer/helpers.php
aureon/theme/page-about.php
aureon/theme/page-contact.php
aureon/theme/single.php
aureon/plugin/woocommerce/woocommerce.php
aureon-doc/FRONTEND-ANALYSIS.md        (371-line original analysis — restored)
aureon-doc/PHASE-17-1-INTEGRATION-ARCHITECTURE.md  (1520-line locked architecture — restored)
```

### 4.3 Deleted (untracked integration artifacts — all backed up)
```
aureon/theme/home.php
aureon/theme/inc/frontend.php                 (integration layer)
aureon/theme/inc/customizer/fields/frontend.php
aureon/theme/page-faq.php / page-team.php / page-testimonials.php / page-wishlist.php
aureon/plugin/woocommerce/templates/          (6 WC hero/page-title overrides)
frontend/components/    (39 files — failed component implementations)
frontend/assets/        (197 files — polluted asset pipeline incl. orphaned copies)
frontend/templates/     (4 orphaned integration templates)
frontend/source/        (244 files — junk-polluted, see 4.4)
aether-home.png         (artifact)
```

### 4.4 Pristine source restored (STEP 3)
- `C:\Users\hamma\Downloads\templete\frontend` → `frontend/source/` via robocopy full mirror.
- **364 files, tree verified identical** (Compare-Object: 0 diffs).
- Set **read-only** (all 364 files) — design reference, never edited.
- Prior `frontend/source/` was polluted with 128 non-template files (bannar.png, page-snapshot.md, index.html.reference, *.yml, screenshots) — purged by the re-copy.

### 4.5 KEPT (verified engine — mission keep-list)
```
frontend/views/        (renderer.php + viewmodel.php — Render Engine)
frontend/manifest/     (components.php — Component Registry, 39 entries)
frontend/tokens/       (tokens.php — Design Tokens)
frontend/adapters/     (10 adapters)
frontend/sections/     (9 section types — Section/Template Engine)
frontend/tests/
frontend/*.md          (10 planning/report docs — deliverables in progress)
aureon/plugin/woocommerce/functions/template-locator.php   (WooCommerce bridge)
```

### 4.6 Post-state verification
- `git status`: theme/plugin modified-file list now empty (only `.serena/` memories + `aureon-doc/STATUS.md` remain modified — intentional, they document this rollback).
- `frontend/source/` mirror: 364/364 files, read-only, 0 diffs.

---

## 5. Rebuild Architecture Requirements (from mission, condensed)

1. **Template ownership:** Template → Composer → Components → Renderer → HTML. Pages are *composed*, not hook-patched.
2. **Suppression layer:** default WP/WC output disabled *where replaced* (presentation only — never functionality). Every suppression documented.
3. **Full surface coverage:** header/footer/page/single/archive/shop/cart/checkout/account/search/404/blog/taxonomy — no mixed-design pages.
4. **Data-only components:** one responsibility, zero WP/WC queries/globals; receive ViewModel/DTO/adapter objects.
5. **Adapters:** menu, header, footer, product, category, review, cart, checkout, customer, wishlist, blog, archive, search, breadcrumb, customizer, theme-options, animation.
6. **Design tokens:** every color/font/spacing/radius/shadow/transition/z-index; Customizer → Tokens → Frontend.
7. **Single asset system** (Aureon Asset Engine) — no duplicates; **single animation system** (components expose only `data-aureon-animation`).
8. **WooCommerce:** frontend owns 100% markup, WC supplies data only; preserve shop/categories/filters/product/gallery/quick-view/cart/checkout/account/orders/coupons/related/cross-sells/upsells/reviews/HPOS/blocks.
9. **Visual match:** pixel-level parity with `frontend/source/`.
10. **Quality gates:** PHPCS/PHPStan/Psalm/ESLint/Prettier/TS/Vite/a11y/performance/WC-bridge/regression/integrity.

---

## 6. Staged Integration Plan (approved direction)

Per user direction — forensic + architecture first, then staged integration to isolate regressions:

| Stage | Scope | Deliverable |
|---|---|---|
| 0 | Forensic + restore (DONE) | FRONTEND_FORENSIC_REPORT.md (this file) |
| 1 | Audit of pristine source | FRONTEND_AUDIT.md (component inventory, layout/page tree, dep/animation/asset graphs) |
| 2 | Shell: Header + Footer (announcement, nav, mega menu, mobile menu, drawers, footer) | Stage report + live check |
| 3 | Home: hero slider, categories, bestsellers, reviews, FAQ, newsletter | Stage report + live check |
| 4 | Shop: page hero, filter bar, product grid/card, pagination | Stage report + live check |
| 5 | Product detail: gallery, options, reviews, related, sticky bar | Stage report + live check |
| 6 | Cart / Checkout / Account / Wishlist | Stage report + live check |
| 7 | Blog: archive, single, related | Stage report + live check |
| 8 | Static pages: about/contact/faq/team/testimonials/404/legal/coming-soon | Stage report + live check |
| 9 | Customizer → Tokens wiring + asset/animation system finalization | TOKEN/ASSET/ANIMATION reports |
| 10 | Pixel-level visual regression vs `frontend/source/` + full quality gates | VISUAL_REGRESSION + PHASE_17_1_VERIFICATION reports |

## 7. Files
- This report: `aureon-doc/FRONTEND_FORENSIC_REPORT.md`
- Evidence backups: `C:\Users\hamma\AppData\Local\Temp\opencode\phase17-integration-backup.tar.gz`
- Restored planning docs: `aureon-doc/FRONTEND-ANALYSIS.md`, `aureon-doc/PHASE-17-1-INTEGRATION-ARCHITECTURE.md`
