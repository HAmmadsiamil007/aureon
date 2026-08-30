# Phase 8 — Performance Audit

**Audit:** GeneratePress 3.6.1 + GP Premium 2.5.6
**Date:** 2026-08-02
**Re-verified:** 2026-08-03 (perf surface analysis — byte-consistent)

---

## 8.1 Theme Frontend Asset Loading (`generate_scripts`, inc/general.php)

### CSS (9 handles on flexbox-mode default)
| Handle | File | Size |
|--------|------|------|
| generate-style | assets/css/main.min.css | 19,512 B |
| generate-font-icons | components/font-icons.min.css | 2,964 B |
| font-awesome | components/font-awesome.min.css | 30,805 B (v4.7) |
| generate-comments | components/comments.min.css | 1,495 B (conditional) |
| generate-widget-areas | components/widget-areas.min.css | 3,356 B (conditional) |
| generate-rtl | main-rtl.min.css | 2,892 B (RTL only) |
| generate-child | child theme style.css | (child only) |
| (legacy mode: + unsemantic-grid + mobile + style) | — | — |

Total default CSS ≈ **~58 KB raw / ~44 KB minified** (flexbox mode, non-RTL). Legacy mode is heavier (grid + mobile + style ≈ +44 KB).

### JS (6 handles, ALL in footer ✓)
| Handle | File | Size |
|--------|------|------|
| generate-menu | assets/js/menu.min.js | 7,333 B |
| generate-dropdown-click | assets/js/dropdown-click.min.js | 3,196 B |
| generate-modal | assets/js/modal.min.js (dist) | 3,315 B |
| generate-navigation-search | assets/js/navigation-search.min.js | 2,123 B |
| generate-back-to-top | assets/js/back-to-top.min.js | 737 B |
| comment-reply | WP core (conditional) | — |

Total JS ≈ **16.7 KB min**. All deferred to footer. Minification via `$suffix = SCRIPT_DEBUG ? '' : '.min'`.

## 8.2 Dynamic CSS Caching

- `css-output.php` caches the entire generated CSS in `wp_options` → `generate_dynamic_css_output` (+ `generate_dynamic_css_cached_version`).
- Busted on: `GENERATE_VERSION` change, `customize_save_after`, `generate_dynamic_css_skip_cache` filter.
- Injected once via `wp_add_inline_style('generate-style')`.
- GP Premium `general/class-external-file-css.php` can externalize to a static file (`generate_using_dynamic_css_external_file`) with WP_Filesystem + AJAX regen.
- **Excellent caching design — one option read per page for all dynamic CSS.**

## 8.3 GP Premium Module Asset Loading

**Lazy loading pattern verified:** each module checks `is_active` before enqueuing:
- font-library.js (380,613 B) — only when Font Library active
- block-elements.js (174,843 B) — only when Elements active
- site-library.js (32,711 B) — admin only
- dashboard.js (24,201 B) — admin only
- Module scripts (blog columns, menu-plus, woocommerce, page-header, sections) — only when module + context active
- WooCommerce module assets only when WC plugin active AND module active

**Inactive modules = zero bytes on frontend.** No global plugin-wide asset dump.

## 8.4 Largest Files (disk, not all loaded on every page)

| File | Size | Context |
|------|------|---------|
| fontawesome-webfont.svg (theme) | 444 KB | Font asset (only if SVG used; browsers use woff2/ttf) |
| dist/font-library.js (plugin) | 380 KB | Font Library editor only |
| assets/dist/customizer.js (theme) | 312 KB | Customizer only |
| dist/block-elements.js (plugin) | 174 KB | Elements editor only |
| fontawesome-webfont.eot/ttf (theme) | 165 KB each | Legacy browser font fallback |
| inc/typography.php (theme) | 96 KB | Server-side (no transfer) |
| elements/class-metabox.php (plugin) | 103 KB | Server-side |
| typography google-fonts.json (plugin) | 102 KB | Server-side data |
| select2.full.min.js (plugin) | 79 KB | Menu/slideout admin UI |

**Largest transferred assets are admin/editor-only** — frontend payload is lean.

## 8.5 Autoload Size & Query Count Estimate

- Theme options: `generate_settings` (autoload) + `generate_dynamic_css_output` (autoload) + `generate_dynamic_css_cached_version` — **3 option reads, standard**.
- Plugin options: module flags (`generate_package_*`, ~12) + license options — autoloaded as part of standard options query.
- No transients on frontend. No `get_option` calls in loops (verified — dynamic CSS read once).
- Expected frontend DB cost: **default WP options query only** (~1 query) + nothing else. Excellent.

## 8.6 Font Loading

- FontAwesome 4.7: local CSS + webfonts (woff2/woff/ttf/eot/otf/svg) — **37 KB CSS is render-blocking**, icons load on demand.
- Google Fonts (optional): enqueued via standard `wp_enqueue_style`, limited to 200 families max, `generate_google_font_display` filter for `font-display` swap.
- GP Premium font-library can self-host/optimize Google Fonts (subsetting).
- **Recommendation:** FontAwesome is a legacy (4.7) icon set — a v6 slim bundle or SVG sprite would cut ~37 KB; `generate_fontawesome_essentials` filter exists to load only needed icons.

## 8.7 Bottleneck Analysis

| Area | Assessment |
|------|-----------|
| CSS payload | Moderate (~44 KB min) — no critical CSS extraction, no preload hints |
| JS payload | Excellent (~17 KB min, footer) |
| Render-blocking | FontAwesome 37 KB + main.css — no `media`/preload optimization |
| Images | No native lazy-load in theme markup (WP core `loading=lazy` applies to content images) |
| Lazy loading | None for CSS/JS (only module-level) |
| Admin overhead | Minimal — editor bundles only on block editor |
| WooCommerce | Hooks-only integration — no template copies to maintain, negligible overhead |
| Caching | Strong — dynamic CSS cached in options, version-busted |

## 8.8 Performance Score: 8/10

**Strengths:** cached dynamic CSS, footer JS, minified-by-default, lazy module loading, minimal DB footprint, hook-based WC integration.
**Gaps (recommended for the custom frontend build):** no critical CSS, no CSS/JS defer beyond WP defaults, FontAwesome 4.7 render-blocking (37 KB), no preload for hero fonts. These are *enhancement opportunities*, not defects — the theme's 10KB-gzipped claim is accurate for flexbox defaults.
