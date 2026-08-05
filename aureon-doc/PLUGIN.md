# Aureon Studio Plugin — Complete Documentation

> Everything about Aureon Studio: architecture, the module activation system, all 17 modules in detail, shared library, Dashboard, compiled assets, legacy code, and known issues.

---

## 1. What Aureon Studio is

Aureon Studio is the companion plugin for the Aureon theme — **"the entire collection of Aureon premium modules."** It is a fork/rebrand of GP Premium 2.5.6 (GPL-2.0-or-later). Once activated, modules can be toggled on/off from **Appearance → Aureon**; each module extends a specific aspect of the theme with extra Customizer options, block-editor features, or admin screens.

**It requires the Aureon theme to be active** — otherwise it shows an admin notice telling you to install it.

### Requirements
| | |
|---|---|
| WordPress | 6.1+ |
| PHP | 7.2+ (Site Library needs 5.4+, Font Library needs modern WP) |
| Theme | Aureon active |

---

## 2. Boot flow (`aureon-studio.php`)

1. Defines constants: `AUREON_STUDIO_VERSION = '3.0.0'`, `AUREON_STUDIO_DIR_PATH`, `AUREON_STUDIO_DIR_URL`, `AUREON_LIBRARY_DIRECTORY(_URL)`.
2. Loads core: `inc/functions.php` (media queries, CSS print method, filesystem helper, enqueue-asset helper), `inc/deprecated.php`, `inc/class-rest.php`, `inc/class-singleton.php`.
3. Loads each module whose option is `'activated'` **or** whose constant is defined:
   - `aureon_is_module_active( 'aureon_package_<module>', 'AUREON_<MODULE>' )`.
   - WooCommerce module additionally requires the WooCommerce plugin to be active.
4. On `after_setup_theme`: loads `typography` module only when the theme is **not** using dynamic typography; loads `colors` only when theme < 3.1 (never for current Aureon 3.6.1 builds — colors now live in the theme).
5. Loads always-on services: `general/class-external-file-css.php` (external CSS print), `general/smooth-scroll.php`, `general/icons.php` (the aureon-studio icon font), `general/enqueue-scripts.php`, `inc/class-dashboard.php`.
6. Loads `font-library` (4 classes) when active.
7. Sets up update checks via the **update provider seam** (`inc/update/class-update-provider.php` — `Aureon_Pro_Update_Provider` + `Aureon_Pro_Null_Update_Provider`); the default provider relies on standard WordPress updates (the EDD Software-Licensing updater was **removed 2026-08-05** — see §4).
8. Registers a **"Configure"** action link → `themes.php?page=aureon-options`; deactivates any legacy standalone add-ons (aureon-backgrounds, aureon-blog, etc.) to avoid conflicts.

### Module activation options (one row per module)
| Option | Constant |
|---|---|
| `aureon_package_backgrounds` | `AUREON_BACKGROUNDS` |
| `aureon_package_blog` | `AUREON_BLOG` |
| `aureon_package_colors` | `AUREON_COLORS` |
| `aureon_package_copyright` | `AUREON_COPYRIGHT` |
| `aureon_package_disable_elements` | `AUREON_DISABLE_ELEMENTS` |
| `aureon_package_elements` | `AUREON_ELEMENTS` |
| `aureon_package_font_library` | `AUREON_FONT_LIBRARY` |
| `aureon_package_hooks` | `AUREON_HOOKS` |
| `aureon_package_menu_plus` | `AUREON_MENU_PLUS` |
| `aureon_package_page_header` | `AUREON_PAGE_HEADER` |
| `aureon_package_secondary_nav` | `AUREON_SECONDARY_NAV` |
| `aureon_package_sections` | `AUREON_SECTIONS` |
| `aureon_package_site_library` | `AUREON_SITE_LIBRARY` |
| `aureon_package_spacing` | `AUREON_SPACING` |
| `aureon_package_typography` | `AUREON_TYPOGRAPHY` |
| `aureon_package_woocommerce` | `AUREON_WOOCOMMERCE` |

---

## 3. The 17 modules

### 1) Backgrounds — `backgrounds/`
Apply background images to any HTML element (body, header, nav, content, footer, secondary nav…). Features a live **background-image editor control** (position, size, repeat, attachment, colors, parallax via CSS) with desktop/tablet/mobile breakpoints. Also styles the **secondary navigation** background.

### 2) Blog — `blog/`
Rich blog/archive options:
- **Columns / masonry** layouts (2–4 columns) with the **infinite scroll** load-more system (vanilla JS) and column classes in the CSS.
- **Featured images:** enable/disable globally or per post type; choose size & alignment; CSS-based sizing; full-width featured images; `aureon_regenerate_*_images_notice` regeneration nudges after size changes.
- Post image options for singles and pages; filters like `aureon_blog_columns`, `aureon_blog_masonry_init`, `aureon_blog_infinite_scroll_init`.
- Migrates old settings via `migrate.php`.

### 3) Colors — `colors/`
**Deprecated** for theme 3.1+ (the free theme now owns all color options). Ships for older-theme compatibility only: adds 60+ color controls, including secondary-nav and off-canvas (slideout) color options and WooCommerce color options. The module only loads when `AUREON_VERSION < 3.1.0` — with current Aureon it never loads.

### 4) Copyright — `copyright/`
Replaces the default footer copyright line with a customizable message, with a live-preview Customizer textarea control and `aureon_copyright` filter hooks.

### 5) Disable Elements — `disable-elements/`
Adds "Disable" checkboxes for theme elements — site header, primary/secondary navigation, mobile header, top bar, featured image, content title, footer, footer widgets, comments — **per page/post** (meta box) or within a Layout Element. Hooks into the `aureon_*` construct callbacks to prevent output.

### 6) Elements — `elements/` ⭐ the flagship
A **block-editor theme builder** + advanced hooks + layout control:
- Custom post type **`aureon_elements`** (edit.php?post_type=aureon_elements) with types: **Block Element** (Header, Footer, Sidebar, Content Template, Post Meta, Post Navigation, Archive Navigation, Loop Template, Page Hero, Search Modal, 404), **Hook Element** (HTML hooked anywhere), **Layout Element** (page-level layout overrides), **Page Hero** (legacy page-header replacement).
- **Display Rules** (conditions): singular/archive by ID, taxonomy, author, pagination, no-results; user state; custom filter `aureon_element_display`.
- Dynamic tags (via `inc/class-register-dynamic-tags.php`): post title, featured image, post meta, date, author, site title/logo, WooCommerce tags…
- `class-adjacent-posts.php` powers prev/next post navigation templates.
- Editor-sidebar options (width, alignment, hide title, etc.), autosave, revisions, `class-block-elements.php` renders blocks on the front end; `class-hero.php` renders Page Heroes (with parallax); `class-layout.php` applies per-page layout; `class-hooks.php` executes hook elements (with PHP-execution toggle gated on `DISALLOW_FILE_EDIT`).
- Metabox UI uses select2 + alpha color picker from `library/`.

### 7) Font Library — `font-library/`
Modern font management:
- **Download & localize Google Fonts** (store font files locally → GDPR-friendly, faster).
- Upload **custom fonts**; organize via a Font Library CPT.
- REST endpoints (`class-font-library-rest.php`) + optimization (`class-font-library-optimize.php`) + Customizer integration (the theme's Font Manager dropdown).

### 8) General — `general/`
Always-on plumbing:
- **External CSS file** option (`class-external-file-css.php`): instead of inline `<style>`, writes dynamic CSS to an external file, regenerates via `wp_ajax_aureon_regenerate_css_file`, respects AMP (inline).
- **Smooth scroll** (`smooth-scroll.php`): anchor scrolling with `aureon_smooth_scroll_offset` filter.
- **Icons** (`icons.php` + `icons/`): the `aureon-studio` icon font (eot/ttf/woff/svg) with `.aureon-icon` classes for menu items, mobile menu, cart, back-to-top…
- **Enqueue scripts** (`enqueue-scripts.php`): loads `dist/packages.js` + module bundles with dependency manifests from `dist/*.asset.php`.

### 9) Hooks — `hooks/`
**Deprecated** (use Elements → Hook Element). Legacy admin page (`aureon-hooks` settings) to paste HTML into ~20 named theme hooks, with PHP-execution toggle (gated on `DISALLOW_FILE_EDIT`) and cookie-based legacy handling.

### 10) Menu Plus — `menu-plus/`
Advanced navigation:
- **Mobile Header** (custom logo/site-title mobile header, breakpoint option).
- **Sticky Navigation** (auto-hide on scroll, logo, full-width toggle).
- **Off-Canvas Panel** ("slideout" drawer — left/right, overlay, escape/spacebar a11y, focus trap).
- **Navigation branding** (logo inside the nav), menu-bar items integration (`aureon_menu_bar_items`).
- Uses `dist/` bundles + `js/offside.js`, `js/sticky.js`.

### 11) Page Header — `page-header/`
**Deprecated** (use Elements → Page Hero). Legacy full-width page header with title/container/parallax/background image options, a per-page meta box, global locations, and a custom post type.

### 12) Secondary Nav — `secondary-nav/`
A fully-featured second navigation (top of site), with its own spacing/colors/typography options, sticky option, dropdown direction, and mobile menu; merges with the top bar; registers a `secondary` menu location.

### 13) Sections — `sections/`
**Deprecated** (use GenerateBlocks). Classic multi-section editor: add full-width "sections" with background image/color, parallax, custom classes, per-page via meta box. Front-end renderer + editor meta box. The three metabox assets were renamed `generate-sections-metabox.*` → `aureon-sections-metabox.*` (editor UI functional).

### 14) ~~Site Library — `site-library/`~~ — **REMOVED (2026-08-05)**
React-based **starter-site importer** (Appearance → Site Library): fetches starter sites, imports content/widgets/customizer options/media, undo/revert, WXR importer, widget importer, Beaver Builder batch processing.
> **Removed:** this module's directory, `dist/site-library.*`, the theme-dashboard "Site Library" link, and the `templateImageUrl` element-template CDN were all deleted/disabled. The feature was non-functional (`https://example.com/invalid` endpoint placeholder) and is intentionally NOT shipped — client starter templates are authored in-house instead of being fetched from an agency API. **Do not reintroduce.**

### 15) Spacing — `spacing/`
**Padding controls** (top/right/bottom/left, desktop + tablet/mobile) for top bar, header, navigation (menu item height, sub-menu width), content, widgets, footer widgets, footer, sidebars, and the content separator; live-preview via `aureon_live_preview` data. Settings stored in `aureon_spacing_settings`; old values migrated by `migration.php`.

### 16) Typography — `typography/`
**Deprecated** (the theme's dynamic typography supersedes it). Legacy per-element font controls (family, weight, size, transform, line-height) for body, headings, nav, widgets, site title, footer, plus Google Fonts from `google-fonts.json`. Only loads when dynamic typography is off.

### 17) WooCommerce — `woocommerce/`
Store styling & behavior (needs WooCommerce active):
- Customizer: product archive **columns** (with gaps), related/upsells columns, single-product image width, menu mini-cart (icon, items count), **sticky add-to-cart bar**, +/- quantity buttons, off-canvas on add-to-cart, secondary product image on hover.
- Colors + typography integration (button, price, sale, star ratings, mini-cart).
- CSS grid archives; clean product tabs; mobile cart panel; quantity JS.
- Filters: `aureon_wc_show_sticky_add_to_cart`, `aureon_wc_cart_panel_checkout_button_output`, `aureon_woocommerce_show_add_to_cart_panel`, etc.

---

## 4. Shared library — `library/`

- **Customizer controls** (`library/customizer/controls/`): `Aureon_Alpha_Color_Control` (alpha color picker), `Aureon_Backgrounds_Control`, `Aureon_Range_Slider_Control`, `Aureon_Spacing_Control`, `Aureon_Typography_Control`, `Aureon_Copyright_Control`, `Aureon_Title_Control`, `Aureon_Information_Control`, `Aureon_Refresh_Button_Control`, `Aureon_Section_Shortcut_Control`, `Aureon_Control_Toggle`, action-button control, plus their CSS/JS (slider, spacing, alpha, button-actions, section-shortcuts, control-toggle…).
- **Helpers:** `library/customizer-helpers.php` (localizes `aureonTypography` google-font list, `typography_defaults`, `aureon_customize` nonce, `aureonButtonActions` overlay defaults, and enqueues `aureon-controls.js`), `library/customizer/active-callbacks.php`, `library/customizer/sanitize.php`, `library/customizer/deprecated.php`.

> ⚠️ **JS-global naming (collision fix, 2026-08-05):** the plugin localizes its React data on the `aureon-pro-customizer-controls-react` handle under the global **`aureonProCustomizerControls`** (keys `hasSecondaryNav`, `hasMenuPlus`, `hasWooCommerce`). It must **never** share the name `aureonCustomizerControls` with the theme — the plugin originally did, silently overwriting the theme's `palette`/`aureonFontLibrary` keys and crashing the React Font Manager. Keep the two names distinct.
- **`library/class-make-css.php`:** the plugin-side dynamic CSS builder (sibling of the theme's `Aureon_CSS`).
- **`library/class-plugin-updater.php`** **REMOVED (2026-08-05)** — EDD updater deleted. Update seam: `inc/update/class-update-provider.php` (`Aureon_Pro_Update_Provider` + `Aureon_Pro_Null_Update_Provider`), swap via the `aureon_studio_update_provider` filter.
- **Licensing:** `inc/licensing/class-license-provider.php` — `Aureon_Pro_License_Provider` + `Aureon_Pro_Null_License_Provider` (everything unlocked, no activation), swap via the `aureon_studio_license_provider` filter. License key system fully removed (2026-08-05).
- **`library/alpha-color-picker/`:** `wp-color-picker-alpha.js`.

## 5. Compiled assets — `dist/`
Webpack-built bundles with `.asset.php` dependency manifests:
`block-elements.js/.css`, `customizer.js`, `dashboard.js/.css`, `editor.js/.css` (Elements editor), `font-library.js/.css`, `adjacent-posts.js`, `packages.js/.css` (shared), plus RTL stylesheets. The plugin enqueues these via `aureon_premium_get_enqueue_assets()`. (`site-library.*` bundles were removed with the Site Library feature — see §3.14.)

## 6. Dashboard (Appearance → Aureon)
- Modern React dashboard (`dist/dashboard.js`): module list with title/description (see `inc/class-dashboard.php::get_modules()`), activation toggles, import/export, reset.
- Legacy fallback (`inc/legacy/`): `dashboard.php`, `import-export.php`, `reset.php`, `activation.php` — loaded only when the theme's `Aureon_Dashboard` class is absent (never in normal operation). License key activation was removed (2026-08-05).

## 7. Translations
Text domain `aureon-studio`, with 22 `.mo` files and 36 `.json` (JS) files in `langs/` covering ar, bn_BD, cs_CZ, da_DK, de_DE, es_AR, es_ES, fi, fr_FR, hr, hu_HU, it_IT, nb_NO, nl_NL, pl_PL, pt_BR, pt_PT, ru_RU, sv_SE, uk, vi, zh_CN.

**2026-08-05 cleanup (do not regress):**
- **`.mo` files** — all 22 were rebuilt with a custom MO writer using the **28-byte header** (`<IIIIIII`: magic `0x950412de`, rev 0, n, o_off=28, t_off=28+8n, str_start=28+16n, 0); strings sorted, round-trip verified, and **267 GeneratePress-brand entries removed** (e.g. "Requires GeneratePress %s.", "Requires GP Premium %s."). Headers decode correctly with `msgfmt`/PHP.
- **`.json` files** — WordPress JS-translation format. Only the 6 files whose hash matched the *old* plugin build (`-92fa…`) were touched; each had **2 dead brand entries removed**. Original PHP-style escaping preserved (`\/`, `\uXXXX`, no trailing newline), GenerateBlocks strings and the empty `""` marker kept (the loader needs the `""` placeholder).

## 8. Multilingual (WPML/Polyglot)
`wpml-config.xml` declares `theme_mods_aureon` as an option to translate and maps all `_aureon_element_*` / `_aureon_hook_*` / `_aureon_hero_*` custom fields for the Elements module (translate/copy-once rules).

## 9. Known issues (as shipped + resolved)
| # | Issue | Severity | Status / Fix |
|---|---|---|---|
| 1 | Sections editor trimmed | High (admin UI) | **FIXED** — PHP now enqueues `aureon-sections-metabox.*` and the 3 files were renamed `generate-sections-metabox.*` → `aureon-sections-metabox.*` on disk |
| 2 | Site Library API hard-coded to `https://example.com/invalid` | High (feature non-functional) | **RESOLVED (2026-08-05)** — Site Library removed entirely (module, bundles, dashboard link, element-template CDN); no dead endpoint shipped |
| 3 | `inc/legacy/activation.php` license endpoint = `https://example.com` | Medium (dead code) | **RESOLVED (2026-08-05)** - legacy license endpoint + handler removed entirely |
| 4 | JS globals still named `generatePressTypography` / `generateCustomizerControls` | Cosmetic | **FIXED** — all renamed to `aureonTypography` / `aureonCustomizerControls` / `aureonBlog` / `aureonProDashboard` / `aureonSecondaryNav` / `aureonWooCommerce` / `aureonGlobalColors` / `aureonProCustomizerControls` |
| 5 | `plugin/dist/customizer.js` read `aureonCustomizerControls` (shared global, clobbered theme) | High (blank React panels) | **FIXED** — plugin bundle + PHP now use `aureonProCustomizerControls`; verified in Docker (0 console errors) |
| 6 | Customizer handle collision (theme+plugin same React handle) | High (no save button) | **FIXED** — theme `aureon-customizer-controls-react`, plugin `aureon-pro-customizer-controls-react` |

## 10. Extending Aureon Studio
- **Filters:** `aureon_media_queries`, `aureon_dynamic_css_print_method`, `aureon_number_of_fonts`, `aureon_typography_customize_list`, `aureon_blog_columns`, `aureon_element_display`, `aureon_smooth_scroll_offset`, `aureon_wc_*` (WooCommerce), `aureon_studio_license_provider`, `aureon_studio_update_provider`, `aureon_desktop/tablet/mobile_media_query`.
- **Hooks:** `aureon_before_secondary_navigation`, `aureon_after_secondary_navigation`, `aureon_after_mobile_header_menu_button`, `aureon_menu_bar_items`, `aureon_admin_dashboard`, `aureon_admin_right_panel` (legacy).
- **Module toggles:** define the module constant (e.g. `AUREON_ELEMENTS`) in `wp-config.php` to force-enable a module.
