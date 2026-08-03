# Phase 3 — GeneratePress Core Analysis

**Audit:** GeneratePress 3.6.1 (theme)
**Date:** 2026-08-02
**Re-verified:** 2026-08-03 (core module analysis — byte-consistent)

---

## 3.1 Bootstrapping & Initialization

`functions.php` is the single bootstrap entry point:

```
functions.php
├── define('GENERATE_VERSION', '3.6.1')
├── add_action('after_setup_theme', 'generate_setup')
│   ├── load_theme_textdomain('generatepress')
│   ├── add_theme_support: automatic-feed-links, post-thumbnails, post-formats,
│   │   woocommerce, title-tag, html5, customize-selective-refresh-widgets,
│   │   align-wide, responsive-embeds, editor-color-palette, custom-logo,
│   │   editor-styles
│   └── register_nav_menus(['primary' => 'Primary Menu'])
├── require inc/theme-functions.php    (general helpers, attr system)
├── require inc/defaults.php           (option defaults)
├── require inc/class-css.php          (GeneratePress_CSS builder)
├── require inc/css-output.php         (dynamic CSS generation, 67 KB)
├── require inc/general.php            (asset enqueueing, utilities)
├── require inc/customizer.php         (Customizer registration, 46 KB)
├── require inc/markup.php             (markup classes/attributes)
├── require inc/typography.php         (typography system, 96 KB)
├── require inc/plugin-compat.php      (WC + plugin compat, 31 KB)
├── require inc/block-editor.php       (block editor styles)
├── require inc/class-typography.php
├── require inc/class-typography-migration.php
├── require inc/class-html-attributes.php
├── require inc/class-theme-update.php
├── require inc/class-rest.php         (REST: /generatepress/v1/reset)
├── require inc/deprecated.php         (back-compat aliases)
├── if is_admin(): inc/meta-box.php, inc/class-dashboard.php
└── require inc/structure/{archives,comments,featured-images,footer,header,
    navigation,post-meta,sidebars,search-modal}.php
```

**Key design pattern:** All public functions wrapped in `if ( ! function_exists() )` — child-theme and plugin friendly. This is the hallmark of a mature WP theme.

## 3.2 Template Hierarchy

Classic theme, 22 root templates:
- `header.php` / `footer.php` — standard; `header-min.php` / `footer-min.php` — minimal variants used by Elements (GP Premium) for blank canvases
- `index.php, archive.php, search.php, single.php, page.php, 404.php, no-results.php, comments.php`
- `content.php, content-link.php, content-page.php, content-single.php, content-404.php` — partials
- `sidebar.php, sidebar-left.php, searchform.php`

Templates are deliberately thin; all structure is produced by **hooks in `inc/structure/*.php`** — the GP signature architecture. `header.php` calls `do_action('generate_header')` etc.; structure files hook in:

| File | Hooks into | Produces |
|------|-----------|----------|
| structure/header.php | generate_header | Top bar, header, logo, nav wrappers |
| structure/navigation.php | generate_header | Primary nav, mobile menu, page walker |
| structure/footer.php | generate_footer | Footer bar, copyright |
| structure/sidebars.php | generate_sidebar_layout | Left/right/both/none sidebars |
| structure/archives.php | — | Archive/post loops, nav |
| structure/post-meta.php | — | Entry meta (date, author, categories) |
| structure/featured-images.php | — | Featured image output |
| structure/comments.php | — | Comments |
| structure/search-modal.php | — | Search modal |

## 3.3 Hook Inventory

| Hook type | Count |
|-----------|-------|
| `do_action()` calls | 127 |
| `apply_filters()` calls | 223 |
| `add_action()` registrations | 99 |
| `add_filter()` registrations | 48 |
| **Total customization points** | **350** |

Notable actions: `generate_header`, `generate_inside_header`, `generate_before_main_content`, `generate_after_main_content`, `generate_footer`, `generate_before_footer`, `generate_credits`, `generate_before_copyright`, `generate_sidebar_layout`, `generate_before_do_template_part`.
Notable filters: `generate_sidebar_layout`, `generate_footer_widgets`, `generate_navigation_location`, `generate_show_title`, `generate_typography_google_fonts`, `generate_schema_type`, `generate_parse_attr`, `generate_dynamic_css_skip_cache`, `generate_svg_icon`, `generate_editor_styles`.

## 3.4 HTML Attributes System (`inc/class-html-attributes.php`)

`GeneratePress_HTML_Attributes` + `generate_do_attr($context)` — a centralized attribute builder used by every structural template (header, footer, nav, sidebar, content wrappers). All attributes flow through `generate_parse_attr` filter. Supports microdata schema output (`generate_schema_type`: microdata or JSON-LD). This is the backbone of the theme's semantics and aks a11y (ARIA roles emitted centrally).

## 3.5 Dynamic CSS Engine

- `inc/class-css.php` — `GeneratePress_CSS` class: `set_selector()`, `add_property()`, `start_media_query()`, `stop_media_query()`, `css_output()`.
- `inc/css-output.php` (67 KB) — builds all structural CSS (colors, layout, spacing, fonts) from `generate_get_option()` values.
- **Caching:** output stored in `wp_options` under `generate_dynamic_css_output` + `generate_dynamic_css_cached_version`; busted on `GENERATE_VERSION` change or `customize_save_after`; filter `generate_dynamic_css_skip_cache` to bypass; `generate_using_dynamic_css_external_file` filter can externalize to a file (GP Premium `general/class-external-file-css.php` implements it).
- Injected via `wp_add_inline_style('generate-style', $css)`.

## 3.6 Customizer

- `inc/customizer.php` (46 KB) — registers panels/sections/controls via a hybrid approach: direct `add_panel`/`add_section`/`add_control` calls (colors, typography, layout panels) + a declarative field registry (`inc/customizer/fields/*.php`, 12 field files required at customizer.php:292-307, each defining `new GeneratePress_Customize_Field(...)` entries).
- Prior-session runtime Customizer E2E census reported 10 panels, 26 sections, 289 controls, 314 settings; these numbers were captured by counting live registered Customizer objects in that session and are NOT independently reproducible via static grep (fields are instantiated declaratively). Treat as indicative scale, not a fresh verification.
- Controls include React-based customizer UI (`class-react-control.php`), range, color, typography, upsell, wrapper controls; selectWoo for font selects.
- Live preview: `postMessage.js`, `customizer-live-preview.js`.

## 3.7 Typography (`inc/typography.php`, 96 KB)

- Full dynamic typography engine: system fonts, Google Fonts (hardcoded JSON list of ~800 families in typography.php:770), font subsets, variants.
- `generate_get_google_fonts()` / `generate_get_all_google_fonts()` / AJAX `generate_get_all_google_fonts_ajax`.
- Google Fonts limited to `generate_number_of_fonts` (default 200), alphabetized by `generate_alphabetize_google_fonts`.
- `inc/class-typography-migration.php` — migrates pre-3.0 typography options.
- **GP Premium typography module is skipped when dynamic typography is active** (see Phase 4) — no double-loading.

## 3.8 Asset Loading (`inc/general.php` → `generate_scripts`)

- Flexbox mode: `main.min.css` (29 KB); legacy mode: `style.min.css` + `unsemantic-grid.min.css` + `mobile.min.css`, or combined `all.min.css` if `combine_css`.
- Icons: `font-icons.css` (4.3 KB) if `icons === 'font'`; FontAwesome 4.7 CSS (37 KB) unless `generate_fontawesome_essentials` filter.
- JS: `menu.min.js`, `dropdown-click.min.js`, `modal.min.js`, `navigation-search.min.js`, `back-to-top.min.js`, `a11y.js` — **all in footer** (`true`).
- `$suffix = SCRIPT_DEBUG ? '' : '.min'` — minified by default.
- `block-editor.asset.php` etc. in `assets/dist/` — webpack-built editor/dashboard/customizer bundles.

## 3.9 WooCommerce Integration

- `add_theme_support('woocommerce')` (functions.php:32).
- `inc/plugin-compat.php`:
  - Removes default WC wrappers (`woocommerce_output_content_wrapper`), replaces with GP structure (`generate_woocommerce_start/end` using `generate_do_attr('woocommerce-content')`).
  - Redirects `woocommerce_sidebar` → `generate_construct_sidebars` (GP sidebar engine controls WC sidebar).
  - Adds WC CSS via `wp_add_inline_style('woocommerce-general')` at priority 100 (product grid columns, ordering select, page-header-image).
- **No WC template overrides shipped** — hooks-only approach, which is the safest for updates (WC template changes never break the theme).

## 3.10 Accessibility

- `assets/js/a11y.js` — focus management.
- Centralized `generate_do_attr()` emits `role`, `aria-*`, `itemscope` attributes.
- Skip-to-content link, screen-reader-text classes, accessible nav (`.mobile-menu` label, `aria-expanded` handling in menu.js).
- `wp_body_open()` hook emitted in header-min.php.

## 3.11 Performance Strategy

- Minimal default CSS (main.min.css 19.5 KB min / style ~21.9 KB min + grid).
- Dynamic CSS cached in options table; version-busted.
- JS in footer, minified by default.
- Lazy loading: no native `loading="lazy"` in markup (WP core handles images); no critical-CSS extraction (Phase 8 note).
- The theme's headline claim ("<10kb gzipped fresh install") holds for flexbox mode defaults.

## 3.12 REST API

`inc/class-rest.php` — `GeneratePress_Rest` singleton; route `generatepress/v1/reset` (DELETE generate_settings + dynamic CSS cache) — **gated `manage_options`** (line 62). No unauthenticated endpoints.

## 3.13 Theme Update Mechanism

`inc/class-theme-update.php` — legacy "theme update" handler for pre-WP-5.5 era theme updates via generatepress.com API; harmless residual (WP core now handles theme updates via wp.org for the free theme).

## 3.14 Dashboard & Meta Box

- `inc/class-dashboard.php` — admin dashboard module (appearance > dashboard), premium upsell.
- `inc/meta-box.php` — per-post meta (sidebar layout, content type, disable elements) — standard postmeta usage, `current_user_can` guarded.

## 3.15 Verdict

**PASS (9/10).** Architecture is exemplary for a classic WP theme: thin templates + hook-driven structure, centralized attribute system, cached dynamic CSS, guarded functions, proper WC integration, strong a11y. No architectural anti-patterns found.
