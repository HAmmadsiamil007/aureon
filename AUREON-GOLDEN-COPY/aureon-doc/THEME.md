# Aureon Theme — Complete Documentation

> Everything about the Aureon theme: what it is, how it boots, how its framework works, every file, every feature, and how to extend it.

---

## 1. What Aureon is

Aureon is a **lightweight, developer-friendly, speed-focused WordPress theme**. A fresh Aureon install adds under ~10 KB (gzipped) to page size. It is a fork/rebrand of the GeneratePress 3.6.1 theme, fully GPL-licensed.

**Positioning:** a "framework" theme — it ships minimal styling, exposes a huge number of Customizer options, and stays out of the way of the block editor and page builders (Beaver Builder, Elementor, etc.). All heavy features (block-element theme building, spacing, secondary nav, WooCommerce styling, starter sites…) come from the **Aureon Studio plugin**.

### Core principles
1. **Speed first** — dynamic CSS only prints what you configure; JS/CSS load conditionally; SVG icons by default.
2. **Customizer-driven** — nearly every visual property is an option in Appearance → Customize.
3. **Child-theme friendly** — parent files never get edited; structure hooks make everything replaceable.
4. **Standards-compliant** — valid HTML/CSS, WordPress coding standards, microdata schema, hAtom, WCAG-conscious (aria labels, keyboard-accessible menus).
5. **Translation-ready** — text domain `aureon`, 25+ community languages.

---

## 2. Requirements

| Requirement | Value |
|---|---|
| WordPress | 6.0 or newer (`style.css` header) |
| PHP | 7.4 or newer |
| WooCommerce | optional, supported |
| Page builders | optional, compatible (Beaver Builder, Elementor) |

---

## 3. Full file structure

```
aureon/theme/
├── 404.php                     # not-found template
├── archive.php                 # archive template (category/tag/author/date)
├── comments.php                # comments template (hooked into the loop)
├── content.php                 # loop content part (standard format)
├── content-404.php             # 404 loop part
├── content-link.php            # link post-format part
├── content-page.php            # page loop part
├── content-single.php          # single-post loop part
├── footer.php                  # footer wrapper (footer-min.php exists for FSE future)
├── footer-min.php              # minimal footer (site-editor-ready)
├── functions.php               # theme bootstrap — do not edit, use a child theme
├── header.php                  # header wrapper (header-min.php exists)
├── header-min.php              # minimal header
├── index.php                   # main fallback template
├── no-results.php              # empty-loop part
├── page.php                    # page template
├── readme.txt                  # WP.org-style readme
├── search.php                  # search results template
├── searchform.php              # search form markup
├── sidebar-left.php            # left sidebar template part
├── sidebar.php                 # main sidebar template part
├── single.php                  # single post template
├── style.css                   # theme header + (minimal) base styles
├── license.txt                 # GPL v2-or-later full text
├── screenshot.jpg              # theme screenshot
│
├── assets/
│   ├── css/
│   │   ├── admin/              # meta-box.css, style.css (dashboard admin)
│   │   ├── components/         # comments.css, font-icons.css, widget-areas.css
│   │   ├── all.css             # combined stylesheet (when combine_css = true)
│   │   ├── main.css            # base layout styles
│   │   ├── main.min.css        # minified
│   │   ├── mobile.css          # mobile styles
│   │   ├── mobile.min.css
│   │   ├── style.css           # legacy (floats) stylesheet
│   │   ├── style.min.css
│   │   ├── style-rtl.css       # RTL variants
│   │   ├── main-rtl.css
│   │   └── *.min.css           # all minified
│   ├── dist/                   # compiled (webpack) assets
│   │   ├── block-editor.js/.css # block editor enhancements
│   │   ├── customizer.js/.css   # React-based Customizer controls
│   │   ├── dashboard.js/.css    # React Dashboard
│   │   ├── modal.js             # navigation search modal
│   │   └── *.asset.php          # dependency manifests
│   ├── fonts/                  # aureon.{eot,svg,ttf,woff,woff2} icon font
│   └── js/
│       ├── back-to-top.js/.min.js
│       ├── dropdown-click.js/.min.js
│       ├── menu.js/.min.js     # also contains a11y helpers
│       └── navigation-search.js/.min.js
│
└── inc/
    ├── theme-functions.php     # option getters, attribute system, SVG icons, media queries
    ├── defaults.php            # all default option values (colors, typography, spacing)
    ├── class-css.php           # Aureon_CSS — fluent dynamic-CSS builder
    ├── css-output.php          # assembles ALL dynamic CSS from options
    ├── general.php             # asset enqueueing, body classes, misc
    ├── customizer.php          # Customizer bootstrapping + default controls
    ├── markup.php              # HTML markup helpers (attr system wiring)
    ├── typography.php          # (legacy) typography customizer controls
    ├── plugin-compat.php       # compatibility with page builders / plugins
    ├── block-editor.php        # block editor support (colors, typography, widths)
    ├── class-typography.php    # Aureon_Typography — dynamic typography engine
    ├── class-typography-migration.php  # migrates old → new typography options
    ├── class-html-attributes.php       # registers default HTML attribute sets
    ├── class-theme-update.php  # internal theme update data maintenance
    ├── class-rest.php          # theme REST endpoints (e.g. reset, options import/export)
    ├── deprecated.php          # back-compat shims for old GeneratePress-era hooks
    ├── meta-box.php            # per-post meta boxes (sidebar layout, footer widgets, container)
    ├── class-dashboard.php     # loads the React dashboard page
    ├── dashboard.php           # legacy dashboard page (pre-3.0 fallback)
    ├── structure/              # the layout engine — see §7
    │   ├── archives.php        # archive page header + loop
    │   ├── comments.php        # comment list/form output
    │   ├── featured-images.php # featured image output
    │   ├── footer.php          # footer widgets, footer bar, copyright, back-to-top
    │   ├── header.php          # top bar, header, branding, menu toggle
    │   ├── navigation.php      # primary navigation output
    │   ├── post-meta.php       # entry meta (date, author, categories, tags)
    │   ├── search-modal.php    # navigation search modal output
    │   └── sidebars.php        # sidebar registration + output
    └── customizer/
        ├── class-customize-field.php   # Aureon_Customize_Field — declarative field API
        ├── helpers.php         # sanitizers, enqueues, live-preview wiring
        ├── deprecated.php      # legacy control aliases
        ├── controls/           # control classes + their CSS/JS
        │   ├── class-color-control.php
        │   ├── class-range-control.php
        │   ├── class-react-control.php      # React control base
        │   ├── class-typography-control.php
        │   ├── class-upsell-control.php / class-upsell-section.php
        │   ├── class-wrapper-control.php / class-deprecated.php
        │   ├── css/            # control styles
        │   └── js/             # customizer-controls.js, customizer-live-preview.js,
        │                        # postMessage.js, slider-control.js, typography-customizer.js…
        └── fields/             # one file per Customizer panel
            ├── back-to-top.php, body.php, buttons.php, content.php,
            ├── footer-bar.php, footer-widgets.php, forms.php, header.php,
            ├── primary-navigation.php, search-modal.php, sidebar-widgets.php, top-bar.php
```

---

## 4. How the theme boots (bootstrap flow)

`functions.php` executes on every request:

1. Defines `AUREON_VERSION = '3.6.1'`.
2. `aureon_setup()` on `after_setup_theme`:
   - `load_theme_textdomain('aureon')`
   - theme supports: `automatic-feed-links`, `post-thumbnails`, post formats `(aside, image, video, quote, link, status)`, `woocommerce`, `title-tag`, `html5` (search-form, comment-form, comment-list, gallery, caption, script, style), `customize-selective-refresh-widgets`, `align-wide`, `responsive-embeds`, `editor-color-palette` (built from global colors), `custom-logo` (70×350 flexible), `editor-styles` + `assets/css/admin/block-editor.css`.
   - Registers the **primary** nav menu (Aureon Studio registers more).
   - Sets `$content_width = 1200` (refined later by `aureon_smart_content_width()`).
3. Requires, in order: `inc/theme-functions.php` → `inc/defaults.php` → `inc/class-css.php` → `inc/css-output.php` → `inc/general.php` → `inc/customizer.php` → `inc/markup.php` → `inc/typography.php` → `inc/plugin-compat.php` → `inc/block-editor.php` → `inc/class-typography.php` → `inc/class-typography-migration.php` → `inc/class-html-attributes.php` → `inc/class-theme-update.php` → `inc/class-rest.php` → `inc/deprecated.php`.
4. In `is_admin()`: `inc/meta-box.php`, `inc/class-dashboard.php`.
5. Requires the layout engine: `inc/structure/{archives,comments,featured-images,footer,header,navigation,post-meta,search-modal,sidebars}.php`.

Each structure file registers `add_action( 'aureon_*' , 'aureon_construct_*' )` hooks, so the entire layout is composed of hook callbacks — easy to remove/reorder in a child theme.

---

## 5. The option system

- **Settings bucket:** a single WP option array **`aureon_settings`** stores most settings (the plugin adds `aureon_spacing_settings`, colors live in `aureon_settings`, typography in `aureon_settings['typography']`).
- **Reader:** `aureon_get_option( $key )` — parses stored options over `aureon_get_defaults()`, so unset options always return sensible defaults.
- **Defaults:** `inc/defaults.php` defines:
  - `aureon_get_defaults()` — layout, structure (`flexbox`/`floats`), container width (1200), nav position, `global_colors` (7 tokens), font manager, dynamic typography flag…
  - `aureon_get_color_defaults()` — ~65 color keys (top bar, header, nav, content, forms, footer, back-to-top, search modal…) using CSS-variable values like `var(--base-3)`.
  - `aureon_get_default_fonts()` — body/site-title/nav/widget-title/buttons/headings 1–6/footer typography with weight/transform/size/line-height/mobile sizes.
  - `aureon_spacing_get_defaults()` — used by the plugin's Spacing module (top/right/bottom/left for top bar, header, menu, content, widgets, footer…).
- All defaults are filterable (`aureon_option_defaults`, `aureon_color_option_defaults`, `aureon_font_option_defaults`).

### Global color tokens (default)
| Token | Default | Token | Default |
|---|---|---|---|
| `--contrast` | #222222 | `--base` | #f0f0f0 |
| `--contrast-2` | #575760 | `--base-2` | #f7f8f9 |
| `--contrast-3` | #b2b2be | `--base-3` | #ffffff |
| `--accent` | #1e73be | | |

These tokens become the block editor palette and drive every color option.

---

## 6. The CSS pipeline (how styles are generated)

1. **Static CSS:** `assets/css/main.css` (base layout) + optional `all.css` combined build + `mobile.css` + component files (`comments.css`, `font-icons.css`, `widget-areas.css`). Enqueued by `inc/general.php` with handles `aureon-style`, `aureon-mobile-style`, etc.
2. **Dynamic CSS:** `inc/css-output.php` builds per-element rules from the current option values using **`Aureon_CSS`** (`inc/class-css.php`) — a fluent builder (`set_selector()` → `add_property()` → media queries → minified `css_output()`). Only properties that differ from defaults are emitted, keeping the output tiny.
3. **Print method:** inline `<style>` by default; the plugin's General module can switch to an external cached file (`aureon_dynamic_css_print_method` filter) and regenerates it via admin-ajax.
4. **Editor styles:** `block-editor.php` outputs matching editor CSS (colors, typography, content width) via `block_editor_settings_all`.

---

## 7. Structure engine (the "how the theme works" core)

Every page is assembled through **hooks + attribute contexts**:

### Key output hooks (all `aureon_*`)
| Hook | Fires | Default callback |
|---|---|---|
| `aureon_before_header` / `aureon_after_header` | around header | — |
| `aureon_top_bar` | top of page | `aureon_construct_top_bar()` |
| `aureon_header` | header area | `aureon_construct_header()` |
| `aureon_navigation` | nav area | `aureon_construct_navigation()` |
| `aureon_before_main_content` / `aureon_after_main_content` | content column | — |
| `aureon_before_content` / `aureon_after_content` | inside content | — |
| `aureon_before_loop` / `aureon_after_loop` | loop | search title, archives header, pagination |
| `aureon_before_entry_title` / `aureon_after_entry_title` | post title | — |
| `aureon_before_primary_sidebar` / `aureon_after_primary_sidebar` | sidebar | — |
| `aureon_before_footer` / `aureon_after_footer` | footer | — |
| `aureon_footer` | footer area | `aureon_construct_footer()` |
| `aureon_before_copyright` / `aureon_after_copyright` | inside footer | footer bar, copyright |
| `aureon_menu_bar_items` | nav bar | (plugin adds search/cart) |
| `aureon_do_template_part` (filter) | loop parts | `content-{$format}` |

### Attribute contexts (`aureon_do_attr( 'context' )`)
The theme prints HTML attributes via a registry (`class-html-attributes.php`) — contexts include `body`, `site-header`, `site-navigation`, `site-content`, `content-area`, `site-main`, `site-info`, `entry`, `comments`, etc. Each is filterable (`aureon_attr_{context}_output`) — child themes can inject classes/attributes without template overrides.

### Microdata / schema
`aureon_get_microdata( $context )` emits schema.org `itemtype`/`itemprop` attributes (WebPage, Blog, WPHeader, SiteNavigationElement, CreativeWork, WPSideBar, WPFooter…), toggleable via the `aureon_schema_type` filter (`microdata` default; return `''` to disable). hAtom output is controlled by `aureon_is_using_hatom()`.

---

## 8. Typography system

- **Dynamic typography (default):** the modern system (`use_dynamic_typography = true`). Options live in `aureon_settings['typography']` with a **Font Manager** (React Customizer control) supporting system fonts, Google Fonts (localizable via the plugin's Font Library), and per-element customization (font family, weight, transform, size, line-height, decoration, style) plus responsive (tablet/mobile) sizes. Backed by `Aureon_Typography` (`class-typography.php`).
- **Legacy typography:** the plugin's deprecated `typography` module (only loads when dynamic typography is off).
- **Migration:** `class-typography-migration.php` maps legacy font options onto the new system on update.
- Google Fonts are output with `font-display: auto` (filter `aureon_google_font_display`).

---

## 9. Block editor integration

- Editor palette from global colors; per-option typography applied to the editor.
- `align-wide`, `responsive-embeds`, content-width matching (`aureon_smart_content_width()`).
- Editor stylesheet `assets/css/admin/block-editor.css` + JS `dist/block-editor.js`.
- Dark-theme awareness, full-width previews for configured layouts.

---

## 10. Meta boxes (per-page control)

Registered by `inc/meta-box.php` (with `assets/css/admin/meta-box.css`):
- **Sidebar Layout** — right/left/both/none/full-width per post or page (`_aureon-sidebar-layout-meta`).
- **Footer Widgets** — 0–5 per page (`_aureon-footer-widget-meta`).
- **Content Container** — full-width or contained per page.
- (Plugin adds more: disable elements, page hero, etc.)

---

## 11. Dashboard (Appearance → Aureon)

- **Modern (default):** React-based page (`dist/dashboard.js` + `dist/style-dashboard.css`) with module overview, import/export, and reset; loads via `class-dashboard.php`.
- **Legacy:** `inc/dashboard.php` only if the plugin is older than 2.1 (never for current builds).
- Page slug `aureon-options`; links to docs at `https://aureonstudio.com`.

---

## 12. Feature catalog (theme alone)

- 60+ color controls with global-color variables
- Dynamic typography + Font Manager + Google Fonts
- Flexbox **or** Floats structure; container width 1200 (customizable)
- 5 sidebar layouts; header/nav/footer fluid or contained
- Multiple navigation positions (float left/right, left, right, centered, above/below header); dropdown hover **or** click; submenu direction (RTL-aware)
- Navigation search modal (keyboard accessible)
- Mobile menu with inline toggle; back-to-top button
- Top bar + footer bar + 0–5 footer widgets (9 widget areas total)
- Post formats; SVG icon system (inline SVG, icon-font fallback)
- Microdata + hAtom schema; RTL; 25+ languages
- Block editor styling; WooCommerce support
- Per-post meta boxes; React dashboard; REST endpoints; theme-update data maintenance
- ~10 KB gzipped baseline

---

## 13. Filters & hooks worth knowing (extendability)

**Filters:** `aureon_option_defaults`, `aureon_get_option`, `aureon_sidebar_layout`, `aureon_show_title`, `aureon_show_entry_header`, `aureon_show_excerpt`, `aureon_attr_{context}_output`, `aureon_{context}_class`, `aureon_parse_attr`, `aureon_get_the_title_parameters`, `aureon_schema_type`, `aureon_is_using_hatom`, `aureon_has_active_menu`, `aureon_has_default_loop`, `aureon_do_template_part`, `aureon_load_child_theme_stylesheet`, `aureon_svg_icon`, `aureon_editor_styles`, `aureon_typography_customize_list`, `aureon_number_of_fonts`, `aureon_get_media_query` (see `aureon_get_media_query()` in `theme-functions.php` for the full query set).

**Actions:** every `aureon_*` construct hook in §7 plus `aureon_before_loop`, `aureon_after_loop`, `aureon_menu_bar_items`, `aureon_wp_footer`… Full list in the structure files.

---

## 14. Performance notes

- Conditional enqueuing: menu JS only when a menu exists; comments CSS only when comments exist; back-to-top CSS only when enabled.
- Dynamic CSS only prints non-default values; `combine_css` merges component files into `all.css`.
- `dynamic_css_cache` caching of compiled CSS.
- SVG icons default (no font file needed); icon font loaded only when selected.
- No jQuery in the front-end except dropdown-click/menu scripts which degrade gracefully.

---

## 15. Customizer React architecture & JS globals (read this before editing)

The theme ships a **React-based Customizer control bundle** (`assets/dist/customizer.js` + `style-customizer.css`). On `customize_controls_enqueue_scripts` (`inc/customizer/helpers.php`) it:

1. Enqueues the bundle under handle **`aureon-customizer-controls-react`** with deps `lodash, react, react-dom, wp-components, wp-element, wp-hooks, wp-i18n, wp-polyfill, jquery, customize-base, customize-controls, wp-color-picker`.
2. Calls `wp_set_script_translations('aureon-customizer-controls-react', 'aureon')`.
3. Localizes various global objects **onto that same handle** (see table below).
4. Enqueues `style-customizer.css` under the same handle.

### JS globals written by the theme (paired writer/reader)

| Global | Written by | Consumed by |
|---|---|---|
| `aureonTypography` | theme `helpers.php` on `aureon-typography-customizer` | `inc/customizer/controls/class-typography-control.php` (template) |
| `aureonCustomizerControls` | theme `helpers.php` on `aureon-customizer-controls-react` (keys: `palette`, `showGoogleFonts`, `colorPickerShouldShift`, `aureonFontLibrary`, `aureonFontLibraryURI`) | `assets/dist/customizer.js` (React Font Manager) |
| `aureonCustomizeControls` | theme `helpers.php` on `aureon-customizer-controls` (key: `mappedTypographyData`) | `assets/dist/customizer.js` |
| `aureonGlobalColors` | *(internal, no PHP writer)* | `assets/dist/customizer.js` (4× internal function) |
| `aureon_customize` | theme + plugin (key: `nonce`) | customizer JS |

> ⚠️ **These names are fingerprint-sensitive.** They were renamed from the upstream `generatePressTypography` / `generateCustomizerControls` / `generateGlobalColors`. If you rename any of them again, you **must** rename the PHP writer **and** every JS reader in the same commit — a stale pair breaks the control.

### ⚠️ The global-name collision (fixed) — do not reintroduce

Originally both **the theme and the Aureon Studio plugin** called `wp_localize_script()` for the **same global name**. The plugin loads after the theme, so its `var aureonCustomizerControls = {...}` overwrote the theme's — deleting `palette` and `aureonFontLibrary` and crashing the React Typography/Colors panels with `Cannot read properties of undefined (reading 'length')`.

**Convention now enforced:**
- `aureonCustomizerControls` → **theme only** (React customizer bundle, `aureon-customizer-controls-react` handle).
- `aureonProCustomizerControls` → **plugin only** (`aureon-pro-customizer-controls-react` handle; keys `hasSecondaryNav`, `hasMenuPlus`, `hasWooCommerce`).

The remaining harmless shared names (`aureon_customize`, `aureonTypography`, `typography_defaults`) are localize-identical on both sides and safe.

### ⚠️ The handle collision (fixed) — do not reintroduce

Both products shipped a React control handle named **exactly the same** (`…-customizer-controls-react`). The plugin's enqueue overwrote the theme's — the theme bundle lost its translations/localize and **no save button rendered**. Fix: theme handle = **`aureon-customizer-controls-react`**, plugin handle = **`aureon-pro-customizer-controls-react`**. Handles must remain distinct and each `enqueue`/`localize` pair must stay identical.
