# MASTER ARCHITECTURE & FRONTEND REPLACEMENT BLUEPRINT
## GeneratePress 3.6.1 → Premium Frontend Framework (Update-Safe)

**Date:** 2026-08-03
**Author:** Lead Software Architect (Phase 4 — Master Architecture Blueprint)
**Read-only input:** prior audits used solely as context (list below). No audit is repeated here.

**Prior context:**
- 12-phase baseline forensic audit — `Report/phases/01-12-*.md` (re-verified 2026-08-03)
- Second-Stage Enterprise Forensic Verification — `Report/second_stage_forensic_report.md`
- Complete Engineering Review / Bug Hunt — `Report/complete_engineering_review_report.md`

**Ground truth (re-derived for this blueprint from the shipped packages):**
- GeneratePress theme 3.6.1 (144 files) — `functions.php`, `inc/*`, `assets/*`
- GP Premium plugin 2.5.6 (329 files) — `gp-premium.php`, 15 module dirs, `library/`, `dist/`

---

# EXECUTIVE DECISION RECORD

## The Core Choice: Child Theme, Not a Fork, Not a Plugin

The premium frontend **is delivered as a GeneratePress child theme** (plus an optional companion mu-plugin for asset/token concerns).

| Approach | Compatibility impact | Verdict |
|----------|----------------------|---------|
| **A. GP child theme (recommended)** | Keeps GP + GP Premium auto-updating; keeps `get_stylesheet()` semantics; child templates win in the template hierarchy; Gutenberg/editor integration retained; `generate_*` hooks/filters stay public surface | **ADOPT** |
| **B. Standalone custom theme** | Loses GP Premium entirely (GP Premium hard-checks the active theme template slus `generatepress`); must re-implement WooCommerce/editor integration; forks GP CSS | **REJECT** |
| **C. Plugin-only presentation** | Cannot override templates through the theme hierarchy (plugin templates require `template_include`); Gutenberg preview breaks; fights `get_header()`/`get_footer()` | **REJECT** |

**Dependency direction (one-way, no crossing):**

```
WordPress Core
  └─ GeneratePress (parent)        ← UNTOUCHED, auto-updates
        └─ GP Premium (plugin)     ← UNTOUCHED, auto-updates
              └─ WooCommerce + 3rd-party plugins   ← UNTOUCHED
                    └─ CHILD THEME = Presentation layer (everything visual)
                          ├─ Templates (header/footer/loop/archive/single/WC)
                          ├─ Components (renderers + JS controllers)
                          ├─ Design Tokens (CSS custom properties)
                          └─ Asset pipeline (SCSS → CSS, ESM → JS)
                                └─ GSAP → Lenis → Three.js → Premium Frontend
```

**Non-negotiable rules:**
1. The child may only use GP's **public, documented filter/action API** (`generate_get_option()`, `apply_filters('generate_*')`, `do_action('generate_*')`, `wp_nav_menu`, widgets, template hierarchy). It **never calls internal symbols, never shadows, never forks GP/Premium files.**
2. All custom code is namespaced `gpv\`, function prefix `gpv_`, option prefix `gpv_`, hook prefix `gpv_`. No globals leak.
3. Presentation is **tokenized** — components consume CSS custom properties, never hard-coded hex/px.
4. Update safety is verified by a CI job that hashes GP/Premium directories and fails if any shipped file changes.

---

# PHASE 1 — COMPLETE SYSTEM MAP

## 1.1 Theme bootstrap (execution order)

`generatepress/` `functions.php` executes synchronously at theme activation **per-request**, in this order:

```
functions.php
 ├─ define( 'GENERATE_VERSION', '3.6.1' )
 ├─ add_action('after_setup_theme', 'generate_setup')
 │   • load_theme_textdomain('generatepress')
 │   • add_theme_support: automatic-feed-links, post-thumbnails, post-formats,
 │       woocommerce, title-tag, html5, customize-selective-refresh-widgets,
 │       align-wide, responsive-embeds, editor-color-palette, custom-logo, editor-styles
 │   • register_nav_menus( primary )
 │   • global $content_width = 1200
 ├─ require inc/theme-functions.php       → option API, layout helpers, attr/microdata API, templates
 ├─ require inc/defaults.php              → generate_get_defaults() (all option defaults)
 ├─ require inc/class-css.php             → CSS builder engine (properties → selectors, media queries)
 ├─ require inc/css-output.php            → base/advanced/typography/spacing/ic dynamic CSS + cache
 ├─ require inc/general.php               → enqueues (assets) + dashboard/admin assets
 ├─ require inc/customizer.php            → customizer helpers (priority 1) + fields (priority 20)
 ├─ require inc/markup.php                → body/header/nav/main/footer class filters + microdata
 ├─ require inc/typography.php            → reader font stacks, typography CSS output
 ├─ require inc/plugin-compat.php         → WooCommerce, bbPress, BuddyPress, Beaver, Elementor, Pro shims
 ├─ require inc/block-editor.php          → editor styles + block palette bridging
 ├─ require inc/class-typography.php      → typography resolver + dynamic font CSS
 ├─ require inc/class-typography-migration.php
 ├─ require inc/class-html-attributes.php → attribute context registry (generate_parse_attr)
 ├─ require inc/class-theme-update.php    → theme update class
 ├─ require inc/class-rest.php            → REST API for settings
 ├─ require inc/deprecated.php            → legacy API BC layer (never rely on)
 ├─ is_admin():
 │   ├─ inc/meta-box.php                  → per-post sidebar/footer/hero overrides
 │   └─ inc/class-dashboard.php           → dashboard page
 ├─ inc/structure/archives.php            → archive title, description, search results heading
 ├─ inc/structure/comments.php            → comment template/loop output
 ├─ inc/structure/featured-images.php     → featured image markup/position
 ├─ inc/structure/footer.php              → footer widgets (1–5), copyright gallery, back-to-top
 ├─ inc/structure/header.php              → top bar, logo, site branding, header widget
 ├─ inc/structure/navigation.php          → primary nav positions, mobile toggle, search, menu bar items
 ├─ inc/structure/post-meta.php           → post meta markup
 ├─ inc/structure/sidebars.php            → generate_construct_sidebars() (left/right by layout)
 └─ inc/structure/search-modal.php        → modal markup + shortcode
```

## 1.2 GP Premium bootstrap (`gp-premium.php`)

```
define GP_PREMIUM_VERSION '2.5.6'
constants: GP_PREMIUM_DIR_PATH/URL, GP_LIBRARY_DIRECTORY(/URL)
require inc/functions.php, inc/deprecated.php, inc/class-rest.php, inc/class-singleton.php

Module gate → generatepress_is_module_active( $option, $constant )
  • $option "generate_package_<module>" === 'activated'  OR  constant defined

Gated requires (plugin load):
  backgrounds/generate-backgrounds.php   blog/generate-blog.php
  copyright/generate-copyright.php       disable-elements/generate-disable-elements.php
  elements/elements.php                  secondary-nav/generate-secondary-nav.php
  spacing/generate-spacing.php           menu-plus/generate-menu-plus.php
  woocommerce/woocommerce.php            (only when WooCommerce active)
 ↓ legacy modules:                        hooks/generate-hooks.php
    page-header (GENERATE_PAGE_HEADER), sections (GENERATE_SECTIONS)

after_setup_theme (prio > 0) → generate_premium_load_modules()
  typography/generate-fonts.php   (when ! is_using_dynamic_typography)
  colors/generate-colors.php      (only when theme < 3.1.0)
  load_plugin_textdomain('gp-premium')

General (always loaded): general/class-external-file-css.php, smooth-scroll.php,
                         icons.php, enqueue-scripts.php, inc/class-dashboard.php

Feature modules (guarded): site-library (REST+helper), font-library (CPT+REST+optimize)

Admin: inc/deprecated-admin.php, site-library class
Updater: admin_init → library/class-plugin-updater.php (EDD SL) to generatepress.com
```

## 1.3 Asset loading

**Theme (inc/general.php → `generate_scripts()` @ `wp_enqueue_scripts`):**

| Handle | File | Condition |
|---|---|---|
| `generate-style` | `assets/css/main.min.css` (flexbox) or `all.min.css`; legacy = `style.min.css` | always |
| `generate-style-grid` | `assets/css/unsemantic-grid.min.css` | when not using flexbox |
| `generate-mobile-style` | `assets/css/mobile.min.css` | always (mobile) |
| `generate-font-icons` | `assets/css/components/font-icons.min.css` | when `icons` w/ FA |
| `font-awesome` | `font-awesome.min.css` v4.7 | when FA selected |
| `generate-rtl` | `main-rtl/style-rtl` | is_rtl skip |
| `generate-comments` | `components/comments.min.css` | comments open |
| `generate-widget-areas` | `components/widget-areas.min.css` | always |
| `generate-child` | `get_stylesheet_uri()` of child | child ! active |
| `generate-menu`, `dropdown-click`, `navigation-search`, `back-to-top`, `modal.js` | `assets/js/{menu,dropdown-click,navigation-search,back-to-top}.js`, `assets/dist.197` | conditional |

**Dynamic CSS — the critical integration point**

```
wp_enqueue_scripts @50 → generate_enqueue_dynamic_css()
   css = generate_get_dynamic_css()
   wp_add_inline_style('generate-style', wp_strip_all_tags($css))

init        → generate_set_dynamic_css_cache()            (option cache)
customize_save_after → generate_update_dynamic_css_cache()
generate_base_css / generate_advanced_css / ... (add_action) → module appends to $css
```

GP Premium swaps the print method via `class-external-file-css.php`:
- `add_filter('generatepress_dynamic_css_print_method', …)` → `'external'`
- writes `uploads/generatepress/dynamic-css-{hash}.css` via `WP_Filesystem`
- enqueues handle `generatepress-dynamic` and re-enqueues `generate-child` after it
- `generate_dynamic_css_skip_cache` filter → inline fallback (kept for auth + turbo pages)

## 1.4 Customizer loading

```
customize_register @1  → generate_set_customizer_helpers()  (primary helpers)
customize_register @20 → generate_customize_register()
  • inc/customizer/fields/*.php → each add_panel/ section/setting/control
  • model: GeneratePress_Customize_Field; Transport = refresh OR postMessage
customize_controls_enqueue_scripts → customizer.css + customizer.js + postMessage.js
GP Premium adds fields via library/customizer-helpers.php + module fields/*
```

**Data flow:** `get_option('generate_settings')` merged over `generate_get_defaults()`; `generate_get_option()` wrapper used everywhere. GP Premium uses the same options pattern (`generate_background_settings`, `generate_blog_settings`, `generate_hooks`, etc.).

## 1.5 Runtime request sequence (per frontend render)

```
template_redirect → WP selects template (hierarchy) → get_header()
header.php → doctype/<html>/wp_head() → <body body_class + microdata>
  wp_body_open()
  do_action('generate_before_header')     /* skip link @2, top bar @5, nav-before @5 */
  do_action('generate_header')            /* construct_header @10 */
  do_action('generate_after_header')      /* page hero @10, nav-after @5 */
  <div generate_do_attr('page')>  → do_action('generate_inside_site_container')
    <div generate_do_attr('site-content')> → do_action('generate_inside_container')

index.php → <div generate_do_attr('content')><main generate_do_attr('main')>
    do_action('generate_before_main_content')
    have_posts → do_action('generate_before_loop','index') → the_post()
       generate_do_template_part('index')   /* content-{post_format}.php */
    do_action('generate_after_loop','index')
    do_action('generate_after_main_content')
  </main></div>
  do_action('generate_after_primary_content_area')
  generate_construct_sidebars()   /* get_sidebar('left') / get_sidebar() by layout */
  get_footer()

footer.php → do_action('generate_before_footer')
  <div generate_do_attr('footer')>
    do_action('generate_before_footer_content')
    do_action('generate_footer')           /* widgets@5, construct_footer@10 */
    do_action('generate_after_footer_content')
  </div>
  do_action('generate_after_footer')       /* back-to-top */
  wp_footer() → </body></html>
```

Layout selection (`generate_get_layout()`): option `layout_setting` → single override post meta `_generate-sidebar-layout-meta` → home/archive/search/blog override → filter `generate_sidebar_layout`.

## 1.6 System connections (who calls what)

| Subsystem | Triggered by | Depends on |
|---|---|---|
| WooCommerce theme integration | `inc/plugin-compat.php` (`generate_setup_woocommerce` @ `after_setup_theme`) + GP Premium `woocommerce/functions/functions.php` | `class_exists('WooCommerce')` guard |
| WooCommerce wrappers | `woocommerce_before_main_content`/`woocommerce_after_main_content` → `generate_woocommerce_start/end` | GP markup filters |
| WooCommerce sidebar | `woocommerce_sidebar` → `generate_construct_sidebars()` | — |
| bbPress / BuddyPress CSS | `wp_enqueue_scripts` @ 100 (guarded by class_exists) | plugin presence only |
| Beaver / Elementor CSS | `wp_enqueue_scripts` @ 50/100 guarded | plugin presence |
| Customizer → CSS | `customize_save_after` → invalidates dynamic CSS cache | options |
| Menus | `wp_nav_menu('primary')` from navigation.php | nav locations |
| Widgets | `dynamic_sidebar('header'|'top-bar'|'footer-1..5'|'sidebar')` | registered sidebars |
| REST | `inc/class-rest.php` (theme) + GP `inc/class-rest.php` (plugin) | `rest_api_init` |

WC template overrides: none in GP or GP Premium → WooCommerce core `templates/*` are used, so WooCommerce **must** be overridden in the child for visual control (see Phase 8).

---

# PHASE 2 — FRONTEND REPLACEMENT MAP

Decision codes: **RETAIN** (keep), **HOOK** (via filter/action only), **OVERRIDE** (child template file), **FILTER** (alter output), **EXTEND** (child-safe extension), **SUPPRESS** (turn off, ship own), **CONTROL** (Customizer values power it).

| # | Subsystem | Decision | Why |
|---|-----------|----------|-----|
| 1 | `<head>` / `wp_head` / meta | RETAIN | SEO/OG/Tag Manager plugins rely on `wp_head`; never break it |
| 2 | `wp_body_open` | RETAIN | GTM/GA/consent plugins hook it |
| 3 | Skip-link & focus management | RETAIN / re-emit | WCAG requirement; never remove |
| 4 | Header markup (`generate_header`) | SUPPRESS + REBUILD | premium header needs mega-menu/announcement; GP header is flexbox fixed |
| 5 | Top bar | SUPPLANT w/ announcement bar | own component (data from header widget or option) |
| 6 | Header widget area | RETAIN as data source | `dynamic_sidebar('header')` consumed by drawer/announce component |
| 7 | Primary navigation | OVERRIDE + HOOK | build own `<nav>`/mega; still call `wp_nav_menu` so Max Mega Menu/I18n plugins survive |
| 8 | Dropdown JS | SUPPRESS + own module | needs Lenis-driven reveal, not GP `menu.js` fade |
| 9 | Search modal | EXTEND | reuse GP `<gp-modal>` primitive but restyle; keeps focus management |
| 10 | Main content wrapper (`#content`/`<main>`) | OVERRIDE | full grid control |
| 11 | Sidebars | OVERRIDE render (sample 6 legacy areas) | keep registered areas so widget plugins work, but style them |
| 12 | Blog loop card | OVERRIDE `content-*.php`, `archive`, `index` | new card layout |
| 13 | Single/article | OVERRIDE `single.php` + parts | design control |
| 14 | Pages | OVERRIDE `page.php` (+ `front-page.php`) | design control |
| 15 | 404 / search / no-results | OVERRIDE w/ component pages | branded |
| 16 | Comments | EXTEND (keep `comments_template`) | comment plugins (Gravatar, Disqus) must keep working |
| 17 | Footer markup | SUPPRESS + REBUILD (footer widgets RENDER) | tokens, footer nav, social |
| 18 | WooCommerce pages | OVERRIDE `woocommerce/*` (keep every `woocommerce_*` hook) | hooks = gateway/loop compat; see Phase 8 |
| 19 | Page builders (Elementor/Bricks/Beaver) | EXTEND (never override their templates) | their content is plugin-printed; only add theme CSS guards |
| 20 | Dynamic CSS output | HOOK (extern the file) | keep GP option-driven, override print method via filter |
| 21 | Customizer outward look | **CONTROL → token bridge** | read GP settings, translate to tokens; do not re-render GP CSS 1:1 |
| 22 | Gutenberg blocks | RETAIN + token palette | block editor must keep working |
| 23 | Icons | SUPPLANT FA 4.7 → our SVG icon set | deprecation + perf (see audit) |

---

# PHASE 3 — TEMPLATE STRATEGY

| GP template | Decision | Child behavior |
|---|---|---|
| `header.php` | REPLACE | Own `<head>`+header; re-emit `generate_before_header`/`generate_header` boundaries for compat |
| `footer.php` | REPLACE | Own footer; keep `wp_footer()` |
| `index.php` | REPLACE | loop → our card grid |
| `page.php` | REPLACE | layout wrapper + content page |
| `single.php` | REPLACE | hero + article + related |
| `archive.php` | REPLACE | grid + pagination |
| `search.php` | REPLACE | cards + no-results |
| `404.php` | REPLACE | CTA component |
| `comments.php` | EXTEND | keep template structure, feed `comments_template()` |
| `searchform.php` | REPLACE | own `<form>` (keeps `get_search_form()` compat) |
| `sidebar.php` / `sidebar-left.php` | REPLACE | slot render via `dynamic_sidebar()` in our layout |
| `content-*.php` | REPLACE | new card shapes using components |
| `no-results.php` | REPLACE | empty state |
| `front-page.php` | ADD | composed page (ACF / site sections) |
| `header-min.php` / `footer-min.php` | DO NOT TOUCH | legacy fallbacks used by GP filters, leave intact |
| `template-parts/*` (block templates) | RETAIN | GP has none; don't fabricate |
| `single-{cpt}.php` / `archive-{cpt}.php` | ADD flexibly | as needed (e.g., testimonial, download) |
| `woocommerce/*` (from WC core) | OVERRIDE in child | Phase 8 |

Everything non-`*` stays minimal; each child template is a thin shell calling components + `the_content()`, never recreating GP internals.

**priority:** never *fork* from parent `inc/`. Only override top-level template files by own child copy.

---

# PHASE 4 — COMPONENT ARCHITECTURE

**Principles**
1. A component = `data` provider + `render()` → HTML, plus optional `js` controller + scss block.
2. **Components never depend on GeneratePress templates or controllers.** They consume `data` (array) plus tokens.
3. Registry: `gpv\components::register('harpoon', $args)`; `render('harb', $props)`.
4. All assets namespaced: `gpv-{component}` handles.

Component catalog (initial) — each independent:

| Component | Data source | State | JS? |
|-----------|-------------|-------|-----|
| Announcement bar | option / header widget | dismiss cookie | yes |
| Header | logo + nav + optional CTA | scroll state | GSAP |
| Mega menu | `wp_nav_menu` (walker) | open/closed | yes |
| Hero | ACF / post meta / page content | — | GSAP |
| Breadcrumb | Yoast/RankMath or custom | — | no |
| Category grid | `get_terms`/WP_Query | — | — |
| Featured products | `wc` query | live add-to-cart | front |
| Product card | `WC_Product` | quantity | front |
| Collection grid | taxonomy terms | — | — |
| Blog card | `WP_Post` | — | — |
| Article card | `WP_Post` | — | — |
| Newsletter | shortcode / MailPoet | submit | yes |
| Testimonials | CPT `gpv_testimonial` | — | — |
| Timeline | CPT `gpv_timeline` | — | — |
| FAQ | global/ACF repeater | accordion | yes |
| CTA | static config | — | — |
| Footer | widgets + content | `dynamic_sidebar` slots | — |
| Popup | JS controller | — | GSAP modal |
| Drawer (cart/menu) | WC cart / nav | | GSAP |
| Cart (mini) | `woocommerce_add_to_cart_fragments` | | — |
| Search | WP search form | | — |
| Quick view | product via REST/AJAX | | yes |
| Wishlist | plugin (YITH/WCWL) adapter | | — |
| Account | WooCommerce my-account | | — |
| Compare | plugin (YITH) | | — |

**Rendering model**
- PHP server-side for SEO-critical (header, cards, footer, breadcrumbs).
- JS controller hydrates `[data-gpv]` islands: `Gpv.init()` scans, dispatches to a module by `data-gpv-component`, applies GSAP/Lenis bound to `prefers-reduced-motion`.
- Scroll helper: Lenis smooth scroll + ScrollTrigger (GSAP) registered on `document` → sections animate when `data-gsap="..."` present.

---

# PHASE 5 — DATA LAYER

**Never couple HTML directly to WP logic.** Components receive `$props`; adapters provide data.

| Source | Adapter (namespace `Gpv\Data`) | Returns |
|---|---|---|
| Blog / CPT | `Wp_Post_Adapter` | normalized {id,title,excerpt,thumb,url,cats,date} |
| ACF | `Acf_Adapter` (fallback `get_field` if ACF present) | field map |
| WooCommerce | `Wc_Product_Adapter` | {id,title,price,rate,image,addToCart,variations,promo} |
| Menu | `Menu_Adapter`          `wp_get_nav_menu_items` → tree | nested nodes |
| Settings | `Settings_Adapter` (gpv_options + generate options) | typed value |
| Widgets | `Widget_Adapter` → `dynamic_sidebar` inside component shell | final markup (never data) |
| REST (custom data) | `Rest_Adapter` | `register_rest_route` guarded by permission |
| Taxonomies | `Tax_Adapter` | term trees |
| Template (static/VP) | `Page_Adapter` (the_content bridge) | post/chunks |

**Component contract:** only `array $props`; component never reads `global $post` directly. `Wp_Context::current()` holds the post reference once; adapters pass normalized values outward.

**Filters:** `gpv_component_data_{name}` / `gpv_adapter_{type}` allow extension.

---

# PHASE 6 — DESIGN SYSTEM

Tokens as CSS custom properties (`:root`), the single source of truth. Dark mode via `[data-theme=dark]` + `prefers-color-scheme`.

```
:root {
  --gpv-color-primary       : oklch/hcl or hex;
  --gpv-color-on-primary    : …
  --gpv-color-surface-1..4:  …
  --gpv-font-display: "Plus Jakarta Sans";
  --gpv-font-body: system-ui/-apple-system,…;
  --gpv-space-1..12  (0.25rem scale)
  --gpv-radius-sm..xl
  --gpv-shadow-1..4
  --gpv-container: clamp(1200px, 90vw, 1440px)? etc
  --gpv-grid-gap
  --gpv-motion-duration-* (150/260/400/700/1200ms)
  --gpv-motion-ease-* (in/out/inOut/spring)
  --gpv-z-* (sticky200 drawer1000 moda1200 menu100)
}
```

**Scales to define**
- **Color:** semantic (bg, surface, text, primary, secondary, success, warning, error, info) + contrast-compliant pairs.
- **Typography:** fluid `clamp()` type scale − 1..; line-height; tracking.
- **Spacing:** 4px-based, namespace.
- **Radius / Elevation / Container / Grid:** presets.
- **Breakpoints:** mobile 480, phablet 720, tablet 1024, desktop 1280, wide 1440 — coordinate with GP `desktop:1025`.
- **Motion tokens:** duration + easing map (used by GSAP).
- **Z-index scale** as above.
- **Icons:** custom `<gpv-icon name>` SVG sprite (replaces FA).
- **Buttons / Forms / Cards / Tables / Alerts / Badges / Pagination / Navigation:** complete pattern library, component-agnostic (scope via classes).

**Tooling boundary:** `tokens` come from `generate_*` options → `Gpv\Design\TokenResolver` normalizes into assoc array → writer emits:
- inline `:root{}` block (fast), or
- external `tokens.css` file (Cache-friendly), plus optional editor palette.

---

# PHASE 7 — ASSET PIPELINE

## CSS
- SCSS architecture (7-1-ish): `abstracts/` (tokens), `base/`, `layout/`, `components/`, `pages/`, `themes/`, `vendors/`.
- Build with **Vite** (`vite.config.ts`) → `dist/app.css`, `dist/app.min.css`, `dist/editor.css`, `dist/manifest.json`.
- **Critical CSS** inline in `<head>` (configured via a `critical.php` map); rest deferred external.
- **De-enqueue** GP styles we don't need (by filter `wp_enqueue_scripts` late) but keep GP token handles if any; our final css last (`priority 200`).

## JS
- Modules: `main.js` (Loader, nav, menu), `gsap.js`, `lenis.js`, `three.js`.
- Ship as **ES modules** with `type="module"` + `defer`; non-LCP features lazy via `import()` on `IntersectionObserver`.
- Vendor: GSAP (3.x) + ScrollTrigger; Lenis; Three.js optional bundle (only if a scene is used).

## Images
- `loading="lazy"` + `decoding="async"` (+ `fetchpriority="high"` on LCP).
- `srcset`/`sizes` via core; AVIF/WebP handled by hosts/converters or WP core.

## Fonts
- Self-host w/ `@font-face` + `font-display: swap` + unicode subsets; preload display fonts.
- Consider variable fonts (one file).

## Caching / versioning
- `manifest.json` hash → `assets/build-{hash}.css/js`.
- Bust token/critical files on `customizer_save_after`.
- `do not cache` for logged-in admin; aggressive cache for guests.

---

# PHASE 8 — WOOCOMMERCE

### Template mapping

| Page | WC core template (override in child) | Decision |
|---|---|---|
| Shop / archive | `woocommerce/archive-product.php`, `loop/?` | OVERRIDE to catalog grid, keep `woocommerce_before/after_main_content` |
| Single product | `woocommerce/single-product.php` (parts) | OVERRIDE to product layout; keep HOOKS (galleries, tabs, related) |
| Cart | `woocommerce/cart/` （3 templates） | OVERRIDE appearance, KEEP `cart`/`coupon` form scripts |
| Checkout | `woocommerce/checkout/form-*` | RETAIN WooCommerce output, overlay CSS only — **never break WC_Payment nodes** |
| My Account | `woocommerce/myaccount/*` | RETAIN WC templates; wrap in our account component |
| Order received | `woocommerce/checkout/thanks.php` | RETAIN; wrap |
| Order tracking | `woocommerce/order/order-tracking.php` | RETAIN WC markup |

### Golden rule
Only override a WC template when the visual change **cannot** be achieved via `woocommerce_*`. Never de-register WC scripts.

**Every overridden template MUST re-emit:**
- the originating `woocommerce_* do_action`/`apply_filters` line(s),
- with signature + priority intact (see WP-style override rules).

### Compat protection
- Payment gateways: only touch visual wrappers; `wc_checkout_*` untouched.
- `woocommerce_add_to_cart_fragments` → our mini-cart markup.
- Keep `query_args`, `loop_columns`, `loop_products_per_page`.

---

# PHASE 9 — CUSTOMIZER

### Strategy — bridge not replacement
`Customizer → tokens → front-end` (data flow):

```
GeneratePress (option) + GP Premium (option) + gpv (option)
   → generateToken\Resolver (converts into semantic CSS tokens)
   → tokens.css (external) or inline :root{}
   → components consume via var(--token)
```

### What is consumed
| Feature | Behavior |
|---|---|
| `global_colors` (GP) | map to `--gpv-color-primary`… via name-> token |
| typography (GP) | font family + font size scale -> tokens |
| spacing (GP) | tokenized spacing |
| container width / layout | used as the grid clamp |
| sidebar / content layout | drives layout slot selection |
| header / nav | **our** component options — GP header *values still read but repurposed for TOP/TOG relative sizes* |
| footer | ours |
| colors secondary (GPP) | `--gpv-color-secondary` |

### Panel additions
- New child panel **`gpv` → Design System**: tokens (colors, typography, spacing, radius) with sanitize callbacks on `gpv_options` option.
- "Presentation reset" control: clears my tokens, falls back to defaults — user-safe.
- **Rule:** never let GP CSS owner write output controls for tokens we own two; we phase out via hidden sections (`show_active` false when `gpv_use_tokens` true).

---

# PHASE 10 — THIRD-PARTY PLUGINS COMPATIBILITY

| Plugin | Strategy |
|---|---|
| Rank Math / Yoast SEO | RETAIN (their templates/hooks) + breadcrumbs via wrapper |
| ACF | Adapter (`Acf_Adapter`) — no changes to plugin |
| Fluent Forms / Gravity Forms / WPForms | RETAIN; wrap in `.form-` container; never touch their assets |
| Elementor / Bricks / Beaver | RETAIN full; only add isolated theme CSS (namespaced class on `body.gpv-*`) — never override their templates (it's already converted to plugin output) |
| BuddyPress / bbPress | RETAIN core; add layout CSS only |
| LearnDash | RETAIN; add spacing in theme |
| EDD | RETAIN; optional GP shop-style inverted |
| The Events Calendar | RETAIN; CSS only, no template override |
| WPML / Polylang | RETAIN; use `wpml-config.xml` (GP ships one) + `Home()` language switch in adapters; nav via `wp_nav_menu` location per lang |

**Universal plugin rule:** never open a plugin's file, never override a plugin template unless required; when styling, use the plugin's own class hooks/variables; those still function after GP updates because we only touch **theme-side** input.

---

# PHASE 11 — FILE STRUCTURE

```
gp-premium-frontend/  (child theme "generatepress-premium")
├─ functions.php          bootstrap imports (all gpv_ requires) 
├─ style.css              Theme Name: GeneratePress Premium, w/ Text Domain
├─ theme.json             (block editor tokens – optional)
├─ inc/
│   ├─ bootstrap.php      constants + autoloader (Composer PSR-4 → src/)
│   ├─ setup.php          theme supports, menu registration
│   ├─ dequeue.php        handles to keep (or drop) vs our own shell
│   ├─ assets.php         enqueue pipeline (tokens, css, js)
│   ├─ helpers.php        gpv_* helpers/extras
│   ├─ context.php        template context / layout resolver
│   ├─ customize.php      token+icon panel (phase 9)
│   └─ widgets.php        limited widget-area map
├── src/
│   ├── Design/           (Tokens, TokenResolver, Palette)
│   ├── Data/             (Adapters: Wp, Acf, Wc, Menu, Settings, Tax …)
│   ├── Components/       (component registry + each .php/.scss/.js)
│   ├── Render/           (PhpRenderer wrappers)
│   ├── Woo/              (wc adapters + loop wrappers)
│   └── Services/         (Assets, Motion/Scroll, Editor)
├── includes/
│   ├── woocommerce/      # child CC overrides (wc/loop/single/cart)
│   ├── template-parts/    (components/grid extraction)
├── assets/
│   ├── dist/             # built css/js + manifest
│   ├── fonts/
│   └── images/
├── scss/                # source (or src/styles)
├── js/                  # src ts/js/es modules
├── languages/
├── tests/               # PHPUnit (double) + Playwright specs
└── docs/
```

**Roles**
- **inc/** = boot + environment
- **src/** = domain model (data, tokens, services, components)
- **includes/** = theme template parts + WC overrides
- **assets/dist** = release build (only shipped)
- **scss/js** = source, compiled at build time

---

# PHASE 12 — UPDATE SAFETY

### File risk table
| GP file | Handling |
|---|---|
| `assets/css/*` (core styles) | **De-enqueued** (own loader supersedes) → never modify |
| `assets/js/*` (menu etc.) | Dequeue !critical (we supply) — never modify |
| `inc/general.php` `generate_scripts` | handled via `wp_dequeue_style(_handle)` at `wp_enqueue_scripts (last)` |
| `header.php` / `footer.php` | **child override** (top-level themed copy) — exceptions: none |
| `index.php`, `single.php`, `page.php`, `archive.php`, … | **child override**; WP picks child first |
| `content-*.php` | child overrides |
| `inc/structure/*` | **never touched**; their defaults stay for non-child template usage |
| `inc/css-output.php` | only via `generate_get_dynamic_css` via filter (phase 9) |
| `inc/customizer.php` | no edit; add on top the child's own panel |
| `generate_*` functions | **never redefine** a GP function. Wrapping via filters only |
| GP Premium module files | **never touch**; no custom hooks leaked from them |
| WC core templates | **child overrides only mismatch (Phase 8)** |

### Safety rules
1. **Never modify a file that ships with `generatepress/` or `gp-premium/`.** Everything replaces is at the theme filter surface or by child files.
2. **Never define a function with `generate_` prefix.**
3. **Never `require` a GP internal file from our code.**
4. Whenever uncertain: use a **filter** over a template override; template override over a symbol call.

### Update test
Every GP/GP Upgrade: the CI job runs PHP integration suite + Playwright on current parent + `wp core is-installed`; any canonical – catches broken hook or copier.

---

# PHASE 13 — IMPLEMENTATION ROADMAP

### Phase 0 — Environment & baseline
- **Goals:** local WP 6.9 + PHP 8.2/8.3, build pipeline (Vite+Lightning?), Playwright, PHPUnit.
- **Deliverables:** `system`, bash scripts, baseline Lighthouse screenshots + `Theme.json`.
- **Risks:** parent update mid-line; GP + Premium beta.
- **Verify:** `wp check`, Lighthouse 90+.
- **Rollback:** tagging point.

### Phase 1 — Theme skeleton boot + tokens
- Child registered, dequeue list, worker styles minimal, customizer placeholder.
- **Verify:** no GP assets on our main shell (inspect Network) + correct our assets.

### Phase 2 — Layout system
- Page wrapper (header/sidebar/footer), containers, grid, breakpoints, Menu/ nav.
- **Verify:** per-page layouts, no horizontal scroll, CSS-only (no JS required for layout).

### Phase 3 — Component library build
- Registry + first 10 components (announcement, header, hero, breadcrumb, blog-card, article-card, category, newsletter, footer, CTAs).
- **Verify:** render tests + a11y & Lighthouse.

### Phase 4 — WooCommerce layer
- catalog/single/cart; templates re-hook preservation; mini-cart.
- **Verify:** whole purchase flow; HPOS, coupons; reconcile anomal.
- Payment provider sandbox smoke test.

### Phase 5 — Design tokens + theming
- Connect Customizer→token bridge; token resolve; dark mode; editor palette.
- **Verify:** change a color in Customizer → token reflects; “Reset” passes.

### Phase 6 — Motion system (GSAP/Lenis/Three)
- Lenis + ScrollTrigger reveals; hero 3D (optional Three.js module).
- **Verify:** prefers-reduced-motion honored; CLS clean chart on desktop/mobile.

### Phase 7 — Performance
- Lazy modules, image pipeline, fonts, critical CSS, caching, preconnect.
- **Verify:** Lighthouse ≥ 95 (mobile), LCP < 2s, no render-blocking js.

### Phase 8 — Accessibility
- Focus management; ARIA (nav/tabs/accordions); WCAG 2.2 AA audit.
- **Verify:** axe, keyboard nav, contrast tokens.

### Phase 9 — Testing & hardening
- PHPUnit (adapters, TokenResolver, WC wrappers) + Playwright e2e for 3 core templates and WC flow; snapshot regression = pixel.
- **Verify:** CI green.

### Phase 10 — Release
- 1.0.0 release; theme zip, docs, changelog; translate; support.

---

# FINAL COMPLIANCE CHECKLIST

| Constraint | How met |
|---|---|
| **GeneratePress updates** | Child-theme-only; filters; never file edits |
| **GP Premium updates** | Zero file engagement; same route |
| **WooCommerce** | Template overrides re-emit every `woo_*` hook; scripts intact |
| **WP standards** | `gpv_` prefix; PSR-4; escaping; nonces; PHPCS |
| **Accessibility** | WCAG 2.2 AA; skip-link; focus; reduced-motion; token-driven |
| **Performance** | Single CSS/JS core; lazy images/JS; tokens; fonts swap |
| **Long-term maintainability** | Token-driven; documented phases; test suite; CI |

---

## Final deliverable note
This blueprint is the **single source of architectural truth** for the GP→Premium frontend transition. Engineering teams can proceed to a task-track implementation using Phase 13 as the sequence, with each phase expanding into its own detailed work plan. No GP/Premium file is expected to be modified; every visual change happens in the child theme and its assets.

*Blueprint authored from reading the GeneratePress 3.6.1 + GP Premium 2.5.6 packages. Read-only analysis; no files modified.*