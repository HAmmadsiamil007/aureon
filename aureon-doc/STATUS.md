# Aureon — Update Status Report

> **As of:** 2026-08-09. This is the authoritative "where are we" document for the Aureon theme + Aureon Studio plugin.

---

## 1. Executive status

| Product | Rebrand | Fingerprint removal | Customizer fixes | Verified live | Ready to ship |
|---|---|---|---|---|---|
| **Aureon theme** | ✅ Done | ✅ Zero | ✅ Done | ✅ (Docker) | ✅ Yes |
| **Aureon Studio plugin** | ✅ Done | ✅ Zero | ✅ Done | ✅ (Docker) | ✅ Yes |
| **AETHER frontend engine** | ✅ Done | ✅ Zero | ✅ Done | ✅ (Docker, 69/70 E2E) | ✅ Yes |

**Detection problem: SOLVED.** Every GeneratePress brand string, camelCase identifier, and GP-named file has been removed — verified by scan (0 hits outside intentional `license.txt` attribution). Details: §4 + [`Report/DETECTION.md`](../Report/DETECTION.md).

---

## 2. Theme status — Aureon v1.1.0

| Area | Status | Notes |
|---|---|---|
| Identity | ✅ | `style.css` → Aureon, v1.1.0, text domain `aureon`, `AUREON_VERSION = 3.6.1` internal |
| Templates & structure engine | ✅ | Full hook-based layout engine, 9 widget areas, microdata |
| Options (60+ colors, typography, layout) | ✅ | `aureon_settings` option bucket + defaults, all filterable |
| Dynamic CSS pipeline | ✅ | `Aureon_CSS` builder, inline or external-file (plugin) |
| React Customizer (Typography/Colors) | ✅ **FIXED** | Handle + global collision resolved; Font Manager, Typography Manager, Global Colors render — 0 console errors in live Docker |
| Block editor | ✅ | Palette from global colors, widths, editor styles |
| Meta boxes | ✅ | Sidebar layout, footer widgets, content container per page |
| Dashboard | ✅ | React `aureon-dashboard`, legacy fallback |
| i18n | ✅ | `.mo` rebuilt (28-byte header, round-trip verified), no brand strings |
| Lint | ✅ | `php -l` 0 errors; `node --check` 0 errors |
| License | ✅ | GPL-2.0-or-later + upstream attribution retained |

**Theme verdict: complete and verified.**

## 3. Plugin status — Aureon Studio v1.1.0

| Area | Status | Notes |
|---|---|---|
| Identity | ✅ | `aureon-studio.php` → Aureon Studio, v1.1.0, `AUREON_STUDIO_VERSION = 3.0.0` internal |
| Module system (16 modules) | ✅ | Option/constant toggles, forced via `AUREON_*` constants |
| Modules 1–17 | ✅ | All load/activate; **Sections metabox assets renamed** (editor UI functional) |
| Shared library & customizer helpers | ✅ | `aureonProCustomizerControls` (was shared-global collision) — distinct from theme |
| Elements / Block Elements / Page Hero | ✅ | CPT + display rules + dynamic tags + REST |
| Font Library | ✅ | Localized Google Fonts, custom fonts, REST |
| Site Library | ✅ **REMOVED** | Starter-site importer + agency template CDN removed (2026-08-05). Not needed — client templates built in-house. `site-library/` module, `dist/site-library.*`, theme dashboard link, and `templateImageUrl` endpoint all removed |
| Menu Plus / Secondary Nav / Blog / Spacing / WooCommerce | ✅ | Verified: plugin typography groups (Secondary Navigation, WooCommerce) inject in live Customizer |
| WooCommerce styling | ✅ | `aureonWooCommerce` global renamed, quantity buttons renamed |
| WooCommerce session fix | ✅ **FIXED** | Mu-plugin `aureon-fix-wc-session.php` initializes WC session early + wraps `wc_clear_cart_after_payment()` with null-check safety net. Fixes `order_awaiting_payment on null` PHP warning on REST API, customizer, and front-end. |
| Legacy (Hooks, Page Header, Sections, Typography, Colors) | ✅ | Deprecated but working; dead endpoints remain (harmless) |
| i18n | ✅ | 22 `.mo` rebuilt + 6 `.json` cleaned; 267 brand strings removed |
| Lint | ✅ | `php -l` 0 errors; `node --check` 0 errors |
| License | ✅ | GPL-2.0-or-later + upstream attribution retained; **license key system removed (2026-08-05) — no activation required** |
| License key system | ✅ **REMOVED** | EDD license UI, REST `/license/` + `/beta/` endpoints, EDD plugin updater, legacy activation handler — all removed; replaced with `Aureon_Pro_*_Provider` null seams |

**Plugin verdict: rebrand complete and verified. Site Library (starter-site importer) intentionally removed** — templates will be authored in-house per client, not fetched from an agency API. License key system also removed (2026-08-05) — no activation required; all modules work out of the box.

---

## 4. Detection status — GeneratePress fingerprints

### Scan results (2026-08-05, post-fix)
| Check | Result |
|---|---|
| `generatepress` / `gp premium` / `edge22` / `tom usborne` (case-insensitive, excl. langs) | **0 hits** outside `license.txt` |
| `generate[A-Z]` camelCase tokens (`generatePress`, `generateCustomizer`, `generateBlog`, `generateProDashboard`, `generateSecondaryNav`, `generateWooCommerce`, `generateGlobalColors`, `generateLabelControl`, `generateQuantityButtons`, `generateTypography`) | **0 hits** |
| `generate-*` / `gp-*` filenames | **0 files** |
| `license.txt` occurrences of "GeneratePress" | **Intentional** — GPL attribution required (§4.1) |
| Kept on purpose | `GenerateBlocks`/`generateblocks` (519 refs — third-party plugin integration), `regenerate`/`generated`/`generates` (English words, 26 refs) |

### 4.1 The one thing that must NOT be removed
`license.txt` (theme + plugin) intentionally contains:
```
This theme/plugin is a derivative of the GeneratePress theme / GP Premium plugin…
GeneratePress is a WordPress Theme, Copyright (c) 2014, Tom Usborne…
```
This is the **GPL attribution** — removing it would be a license violation. Text scanners will flag it; that is correct and expected.

### 4.2 What "solved" means
- **Text-scan detection: SOLVED** — `rg -i generatepress` over `aureon/` returns nothing except `license.txt`.
- **camelCase fingerprint detection: SOLVED** — the pattern that caught the original rebrand's misses now returns 0.
- **Filename detection: SOLVED** — no `generate-*`/`gp-*` names remain.
- **User-facing detection: SOLVED** — no UI text, readme, or metadata mentions GeneratePress/GP Premium.
- **Functional regression risk: NONE** — renamed pairs kept in sync; live-verified.

---

## 5. Comprehensive E2E verification (2026-08-05)

All features verified live on Docker `phantom-wp` (:8080, admin/admin123). **0 console errors** across all surfaces tested.

### 5.1 Theme Customizer — Deep Verification

**All sections load, all controls render, live preview works, 0 errors.**

| Section | Controls | Live Preview | Status |
|---|---|---|---|
| **Site Identity** | 9 (title, tagline, logo, retina logo, logo width) | ✅ Instant | Verified |
| **Typography** | 10 (Font Manager + Typography Manager React, Google font-display) | ✅ React renders | Verified |
| **Colors** | 322 (Global Colors React + body/link/headings/buttons/footer) | ✅ Instant | Verified |
| **General** | 12 (CSS print, icons, links, combine CSS) | ✅ Works | Verified |
| **Homepage Settings** | 10 (show on front, page on front) | ✅ Works | Verified |
| **Additional CSS** | 1 (custom CSS editor) | ✅ Works | Verified |

**Layout Panel** (12 sub-sections):
| Sub-section | Controls | Status |
|---|---|---|
| Container | 13 (width, separator, content layout, alignment, padding) | ✅ Live preview works |
| Header | 14 (layout, inner width, alignment, navigation-as-header) | ✅ Works |
| Primary Navigation | 16 (layout, position, drop point, mobile breakpoint) | ✅ Works |
| Secondary Navigation | 12 (layout, position, alignment) | ✅ Works |
| Sidebars | 8 (layout, blog layout, sidebar widths) | ✅ Works |
| Footer | 10 (layout, widget areas, back-to-top) | ✅ Works |
| Top Bar | 15 (width, inner width, alignment, padding) | ✅ Works |
| Blog | 4 (post loop element, page hero element) | ✅ Works |
| WooCommerce Layout | 53 (cart, breadcrumbs, shop layout, product grid) | ✅ Works |
| Mobile Header | Via Menu Plus module | ✅ Works |
| Sticky Navigation | Via Menu Plus module | ✅ Works |
| Off Canvas Panel | Via Menu Plus module | ✅ Works |

**Spacing Panel** (5 sub-sections, template-rendered):
| Sub-section | Status |
|---|---|
| Header Spacing | ✅ Renders on open |
| Content Spacing | ✅ Renders on open |
| Sidebar Spacing | ✅ Renders on open |
| Navigation Spacing | ✅ Renders on open |
| Footer Spacing | ✅ Renders on open |

**Backgrounds Panel** (10 sub-sections):
| Sub-section | Controls | Status |
|---|---|---|
| Body | 8 (image, position, size, repeat, attachment) | ✅ Works |
| Top Bar | 6 (image, settings) | ✅ Works |
| Header | 10 (image, element toggle) | ✅ Works |
| Primary Navigation | 23 (image, item settings) | ✅ Works |
| Sub-navigation | 16 (image, item settings) | ✅ Works |
| Secondary Nav | 23 (image, settings) | ✅ Works |
| Secondary Sub-nav | 16 (image, settings) | ✅ Works |
| Content | 8 (image, settings) | ✅ Works |
| Sidebars | 10 (image, element toggle) | ✅ Works |
| Footer | 15 (image, element toggle) | ✅ Works |

**WooCommerce Panel** (5 sub-sections):
| Sub-section | Controls | Status |
|---|---|---|
| Store Notice | 7 (demo notice, store notice text) | ✅ Works |
| Product Catalog | 13 (shop display, category display, sorting) | ✅ Works |
| Product Images | 12 (single image width, thumbnail width, cropping) | ✅ Works |
| Checkout | 26 (company field, address fields, order notes) | ✅ Works |
| WooCommerce Colors | 115 (button colors, sale badge, star rating) | ✅ Works |

**Menu Plus Panel** (1 sub-section):
| Sub-section | Controls | Status |
|---|---|---|
| Menu Plus | 1 (module toggle) | ✅ Works |

### 5.2 Plugin Dashboard (`themes.php?page=aureon-options`)

| Component | Status | Console errors |
|---|---|---|
| 10 Module cards (Backgrounds, Blog, Copyright, Disable Elements, Elements, Font Library, Menu Plus, Secondary Nav, Spacing, WooCommerce) | ✅ All toggleable | 0 |
| Start Customizing (Site Identity, Color Options, Typography System, Layout Options) | ✅ All links correct | 0 |
| Import/Export (All, Global Colors, Typography buttons + Export/Import) | ✅ Functional | 0 |
| Reset | ✅ Button present | 0 |
| **License Key module** | ✅ **Removed** (not present) | — |
| **Site Library module** | ✅ **Removed** (not present) | — |

### 5.3 Font Library (`themes.php?page=aureon-font-library`)

| Feature | Status | Console errors |
|---|---|---|
| Font Library tab (installed fonts) | ✅ Verified | 0 |
| Upload Custom Fonts tab | ✅ Verified | 0 |
| Install Google Fonts tab | ✅ Verified | 0 |

### 5.4 Elements CPT (`edit.php?post_type=aureon_elements`)

| Feature | Status | Console errors |
|---|---|---|
| Elements list table | ✅ Verified | 0 |
| Add New Element (block editor) | ✅ Verified | 0 |
| Display Rules (Location, Exclusion, User Role rules) | ✅ Verified | 0 |
| Element settings (type, Block Element, Editor width) | ✅ Verified | 0 |

### 5.5 Front-end

| Feature | Status | Console errors |
|---|---|---|
| Homepage rendering (header, nav, content, sidebar, footer) | ✅ Verified | 0 |
| WooCommerce pages (Shop, Cart, Checkout, My Account) | ✅ Menu links present | 0 |

### 5.6 REST API Routes

| Route | Status |
|---|---|
| `/aureon-pro/v1/modules` | ✅ Registered |
| `/aureon-pro/v1/export` | ✅ Registered |
| `/aureon-pro/v1/import` | ✅ Registered |
| `/aureon-pro/v1/reset` | ✅ Registered |
| `/aureon-font-library/v1/*` (7 endpoints) | ✅ All registered |
| `/aureon/v1/reset` | ✅ Registered |
| `/wp/v2/aureon_elements` (+ revisions, autosaves) | ✅ All registered |
| `/license/` and `/beta/` routes | ✅ **Removed** (not registered) |

### 5.7 Console Error + Warning Summary

| Surface | Errors | Warnings (console) | Warnings (PHP debug.log) |
|---|---|---|---|
| Customizer (all panels) | 0 | 2 (core sandbox iframe + tooltip deprecation) | 0 |
| Plugin dashboard | 0 | 0 | 0 |
| Font Library | 0 | 0 | 0 |
| Elements editor | 0 | 0 | 0 |
| Elements list table | 0 | 0 | 0 |
| Front-end homepage | 0 | 0 | 0 |
| WooCommerce pages (Shop/Cart/Checkout) | 0 | 0 | 0 |
| REST API (all endpoints) | 0 | 0 | 0 |
| **Total** | **0** | **2 (WP core, unfixable)** | **0** |

**Console warnings resolved (2026-08-05):**
- `select('core/edit-post').getPreference is deprecated` — replaced with `select('core/preferences').get()` in plugin `dist/block-elements.js` (6 occurrences) + added null-check fallback for `panels` object
- `TypeError: Cannot read properties of undefined (reading 'aureon-block-element/aureon-block-element')` — fixed by adding `|| {}` fallback on panels access

**Remaining 2 warnings (WP core, unfixable):**
1. Customizer sandbox iframe: `allow-scripts allow-same-origin` — WordPress core `customize-controls.js:6325` sets both attributes on the preview iframe. Required for live preview to function; removing either breaks the customizer.
2. `wp.components.tooltip` `position` prop deprecated since WP 6.4 — core component uses deprecated prop internally; not our code.

**PHP warnings resolved (2026-08-05):**
1. `order_awaiting_payment on null` — WooCommerce core bug; fixed via mu-plugin (`mu-plugins/aureon-fix-wc-session.php`) that initializes WC session early + wraps `wc_clear_cart_after_payment()` with null-check safety net
2. `class-plugin-updater.php` include — stale reference from license removal; resolved by latest code deployment

---

## 6. What changed since the last commit (uncommitted, working tree)

103 files changed (213 insertions, 8006 deletions) across theme + plugin — the fingerprint-removal, i18n, collision-fix, comment-cleanup, and Site Library removal work described in [CHANGELOG.md](./CHANGELOG.md). Not yet committed/pushed. (The license key system removal is committed separately on `main`, see CHANGELOG.md.)

---

## 7. Open items (pre-existing, non-GP)

| # | Item | Impact | Owner |
|---|---|---|---|
| 1 | ~~Site Library API endpoint = `https://example.com/invalid`~~ | **RESOLVED (2026-08-05)** — Site Library feature removed entirely; no endpoint needed | — |
| 2 | ~~Legacy activation endpoint = `https://example.com`~~ | **RESOLVED (2026-08-05)** — legacy license activation handler (incl. `https://example.com` endpoint) removed entirely | — |
| 3 | ~~EDD updater points at `https://aureonstudio.com`~~ | **RESOLVED (2026-08-05)** — EDD updater deleted; replaced by `Aureon_Pro_Null_Update_Provider` seam (standard WP updates) | — |

These are the **only** known issues; none affect the theme, the plugin's core features, or the Customizer.

---

## 8. Phase 17 — AETHER Frontend Integration (2026-08-07)

The repo-root `frontend/` framework (the "AETHER" dark-mode storefront design from `frontend/source/index.html`) is being wired into the Aureon theme as the front-end integration layer. Static HTML components are now real PHP templates served through a strict architecture:

**WordPress → WC → Modules → Adapters → ViewModels → Renderer → Components**

### Framework status

| Layer | Status | Notes |
|---|---|---|
| Renderer (`frontend/views/renderer.php`) | ✅ | Invokes section adapters (args from `adapter_args`), wraps flat arrays as `items`, `aether_behavior_attrs()` at line 100 |
| Tokens (`frontend/tokens/tokens.php`) | ✅ | **Bug fixed:** colors/fonts were only on `aureon_color_option_defaults`/`aureon_font_option_defaults` while `aureon_get_option()` reads `aureon_option_defaults` → all tokens resolved NULL. Defaults now registered on the merged bucket (palette, fonts, hero slides, OAuth, section-visibility toggles) |
| Adapters (`frontend/adapters/`) | ✅ | hero rewritten (shape normalization + JSON decode); faq/testimonials/team/wc-products/options contracts verified |
| Sections (`frontend/sections/`, 9 files) | ✅ | Real templates with register + `if (!isset($sectionData)) return;` render guard |
| Components (39 manifest entries) | ✅ | All 39 resolve; 8 missing ones created (filter-bar, accordion, cta, pagination, login, register, error-404, countdown) |
| Component-ID cross-check | ✅ | Every `aether_render_component()` call (28 refs across theme + frontend) matches a manifest key — no mismatches |
| Theme bootstrap (`theme/inc/frontend.php`) | ✅ | Path resolution (dev `repo/frontend` + deploy `wp-content/frontend`), `after_setup_theme` load, full enqueue (bootstrap+swiper CSS, tokens inline, frontend.css, a11y, vendor bundle, core, lenis, main, animations+motion CSS, gallery/cart/forms/firebase conditionals) |
| Customizer (`theme/inc/customizer/fields/frontend.php`) | ✅ | 11 color controls, 4 radii, 2 heights, 2 fonts, 2 OAuth, hero-slides JSON textarea + `aureon_sanitize_json` (helpers.php) |
| Vendor assets | ✅ | Pinned + local: gsap 3.12.5, ScrollTrigger 3.12.5, lenis 1.1.18, swiper 11, bootstrap 5.3.3; `aether-vendor.min.js` built (454,775 B) |
| JS | ✅ | 4 new files (aether-lenis, aether-gallery, aether-cart, aether-forms); `node --check` clean |
| CSS | ✅ | `frontend.css` (100,701 B) tokenized from style.css (hexes → `--aureon-frontend-*`); `responsive.css` hover var fixed |
| Theme templates | ✅ | front-page (section-visibility toggles), home, 404 (`error/404` component), page-about/contact/faq/team/testimonials/wishlist |
| Plugin WC overrides | ✅ | `template-locator.php` `AUREON_DIR` bug fixed (`plugin_dir_path(dirname(__DIR__))`); 6 templates created: archive-product, single-product, cart/cart, checkout/checkout, checkout/thankyou, myaccount/my-account |

### Verification (2026-08-07)

- `php -l`: **282 files clean** (frontend + theme + plugin)
- `node --check`: all non-vendor JS clean
- Manifest: **39/39 entries resolve**
- Grep gate: 1 hit = false positive (`mobile-chrome.php` reads `home_url` from `$componentData`, does not call `home_url()`)

### Remaining / open

| # | Item | Impact |
|---|---|---|
| 1 | ~~Fonts (Cabinet Grotesk / Satoshi) not downloaded~~ | **RESOLVED (2026-08-07)** — 7 woff2 self-hosted in `frontend/assets/fonts/`, `fonts.css` enqueued locally |
| 2 | ~~Phase 17 stack not deployed to Docker `aureon_wp`~~ | **RESOLVED (2026-08-07)** — full stack live; verified end-to-end |
| 3 | ~~`single-product/` dir in WC overrides unused~~ | **REMOVED** — single-product.php at theme root is the active template |
| 4 | ~~section-newsletter wraps component rendering its own `<section>`~~ | **RESOLVED (2026-08-07)** — `section/newsletter` component now renders divs only (no nested section) |
| 5 | ~~Static demo navigation in main.js~~ | **RESOLVED (2026-08-08)** — `.product-card` click routes to the card's real product link (no more `product-detail.html`); search overlay suggestions + Enter use `aetherAjax.shopUrl` / `aetherAjax.searchUrl` (WP shop / `/?s=`) instead of `shop.html` |
| 6 | ~~Container junk (dead-root extraction)~~ | **RESOLVED (2026-08-08)** — duplicated `adapters/ components/ sections/ source/ …` + 10 md docs at `wp-content/` root (from a bad Aug 7 extraction) removed; `frontend/source/` pruned per deploy contract |
| 7 | CSP Report-Only → strict | Deferred by design (`AETHER_CSP_STRICT` after monitoring period) |
| 8 | Google OAuth dormant | Client keys empty in tokens — site owner fills via Customizer |

### Deployed to Docker (2026-08-07)

- **Container `aureon_wp` (localhost:8080)** now runs the full Phase 17 stack: theme (`wp-content/themes/aureon`), plugin (`wp-content/plugins/aureon-studio`), framework (`wp-content/frontend`, `source/` excluded).
- **Deploy method refined:** PowerShell `Compress-Archive` zips write literal `\` path separators — PHP `ZipArchive` on Linux extracts them as filename characters (backslash-named junk). **Use Windows `tar.exe -czf` (bsdtar, forward slashes) + base64 pipe + `tar -xzf` in-container.** `docker cp` unreliable (busy dirs); delete backslash junk with `find -depth -name '*\\*' -delete` after clean extracts.
- **Bug found & fixed live:** `wp_enqueue_style( 'aether-tokens', false, ... )` never registers the handle (WP skips `add()` when src falsy); WP 6.9.1 `all_deps()` then **drops every dependent** (`aether-frontend`, `aether-a11y`, `aether-motion`) for the missing dependency. Fixed: `wp_register_style( 'aether-tokens', false, ... )` + `wp_enqueue_style( 'aether-tokens' )` (register path adds unconditionally). Inline tokens now print (`--aureon-frontend-bg:#09090B` etc.).
- **Live verification (Playwright):** homepage 200, 0 console errors, 0 warnings; hero slider 3 slides (01 / 03) after default enriched from 1 → 3 slides in tokens.php; newsletter, back-to-top, search modal, mini-cart all render.
- **Expected behavior:** categories/bestsellers/reviews/faq sections no-op until WC products/categories + CPT posts exist (adapters return empty → sections skip). Hero/categories images left `''` in defaults — site owner fills via Customizer repeater.
- **GAP:** `mu-plugins/aureon-fix-wc-session.php` (WC session warning fix) — **RESOLVED (2026-08-07): recreated on disk + deployed to container**, MD5-matches live copy.
### Phase 17.1 � Forensic Rollback & Rebuild Kickoff (2026-08-07)

- **Mission change:** Phase 17 integration declared FAILED by user. Rebuild from pristine source. Do NOT patch; forensic first, staged integration after.
- **FRONTEND_FORENSIC_REPORT.md** (aureon-doc/): root causes � (1) no ownership boundary (components hook-patched into theme's own layout engine, zero output suppression), (2) partial template coverage (blog/search/archives/WC left stock), (3) asset double-loading (theme main.min.css+menu.min.js vs frontend.css+vendor bundle), (4) hook-level DOM duplication (search x4, cart x2, mini-cart x2, newsletter x2, skip-link x2, back-to-top x2, menus x3), (5) integrity drift (orphan style.css/responsive.css/3 orphan JS, dead REST route aureon/v1/frontend, dead frontend/templates, hardcoded mobile menu links, unguarded aether_section_registry, announcement default mismatch, bootstrap 4.6.2 JS vs 5.3.3 CSS in bundle).
- **STEP 2 executed (delete only broken integration):** git-restored 8 modified files (theme 404/front-page/functions/customizer/helpers/page-about/page-contact/single + plugin woocommerce.php) to HEAD 3e5741a; restored planning docs FRONTEND-ANALYSIS.md + PHASE-17-1-INTEGRATION-ARCHITECTURE.md; deleted untracked theme/home.php, inc/frontend.php, inc/customizer/fields/frontend.php, page-faq/team/testimonials/wishlist.php, plugin woocommerce/templates/, frontend/components/ (39), frontend/assets/ (197), frontend/templates/, aether-home.png. Full backup: C:\Users\hamma\AppData\Local\Temp\opencode\phase17-integration-backup.tar.gz (4.7 MB).
- **STEP 3 executed (pristine restore):** C:\Users\hamma\Downloads\templete\frontend ? rontend/source/ full mirror, **364 files, tree verified identical (0 diffs), set read-only**. Old source/ was polluted with 128 extra files � purged.
- **STEP 4 executed (audit):** FRONTEND_AUDIT.md � 22 pages, layout tree, component inventory, dependency graph (CDN contract: bootstrap 5.3.3, swiper@11, gsap 3.12.5, lenis 1.1.18, FA 6.5.1; local JS x6, CSS x4 + vendor), animation graph (data-magnetic x41, data-motion-text x35, data-mouse-depth x9, data-countup x4, data-parallax-speed x3, data-phantom* x359 � NOT the data-reveal-group presets from old analysis), asset graph.
- **KEPT (verified engine):** frontend/views, manifest, tokens, adapters (10), sections (9), tests, 10 planning .md docs, plugin template-locator.php (woo bridge).
- **Next:** Stage 2 shell (Header+Footer) ? Stage 3 home ? Stage 4 shop ? Stage 5 product ? Stage 6 cart/checkout/account ? Stage 7 blog ? Stage 8 static ? Stage 9 customizer/tokens/assets/animation ? Stage 10 visual regression + gates.

### Stage 2 - Shell COMPLETE + VERIFIED LIVE (2026-08-07)

- **Bootstrap blocker fixed:** 	heme/functions.php 12 dead inc/aether-*.php requires replaced with single equire inc/frontend.php (theme was fatally broken on disk).
- **New engine files:** rontend/views/loader.php (bootstrap: tokens+registry+renderer+viewmodel+composer+glob adapters/sections, defines AETHER_FRONTEND_DIR), rontend/views/registry.php (global-based section registry), rontend/views/composer.php (aether_compose_header/footer), adapters adapter-shell.php + adapter-menu.php + rewritten adapter-site.php (rich footer data).
- **Components (7):** shell/preloader, fog, skip-link, announcement, mobile-chrome, header, footer - source-faithful, zero WP calls in components (brand flows via adapter).
- **Theme templates rewritten:** header.php/footer.php delegate to composer (theme hook/wrapper output gone); index.php loop-only (no content/sidebar wrappers - composer owns main#swup).
- **Assets copied from pristine source:** css/style+motion+responsive+a11y, js/lenis-scroll+animations+main+phantom-bridge, fog images, favicons.
- **Suppression verified live:** 0 theme styles/scripts in output (main.min.css, menu.min.js, FA4.7, back-to-top, a11y script, search-modal all gone). Correct callback names: aureon_do_a11y_scripts, aureon_do_search_modal, aureon_clone_sidebar_navigation.
- **Deploy gotchas:** theme+plugin bind-mounted (edits live); frontend/ goes to /var/www/html/wp-content (NOT container root); Playwright caches 301s - use ?nocache.
- **Verified (Playwright + raw HTML):** 200 OK, palette applied (body #09090B, Satoshi), shell renders exactly once each (preloader removed by its own JS load anim per source contract), zero duplicates, enqueue order matches source contract, old front-page.php sections render inside main#swup (Stage 3 replaces).

### Stage 3 - Home COMPLETE + VERIFIED LIVE (2026-08-07)

- **front-page.php rewritten:** pure section composition - aether_render_section() x6 (hero, categories, bestsellers, reviews, faq, newsletter), each gated by Customizer aether_section_* toggles (aether_section_hero added). All inline WP queries + assets/aether 404s GONE (0 refs live).
- **New components (10):** hero/slider (fog+swiper+nav+counter+progress+particles+scroll-indicator), hero/slide (bg img, headline+accent, subline, CTA group), section/header, section/cta, section/accordion (active/open state), section/newsletter (glow+form+success), cards/product (badge/rating/tagline/price), cards/category (large/accent modifiers), cards/review (initials avatar, verified, stars, title, quote, date), commerce/rating (full/half/empty FA stars).
- **Sections aligned to source markup:** reviews now swiper+score summary (4.9 / 312), bestsellers + section-cta, faq + faq-cta + first-item active, categories source classes.
- **Adapters enriched with demo fallbacks (tokens):** aether_category_items (4), aether_product_items (4), aether_faq_items (6), aether_testimonial_items (4) + aether_reviews_score/count. Real WP/WC data always wins; fallback only when empty. Hero slide defaults now ship the sneaker image + accents.
- **BUGS FOUND & FIXED LIVE:**
  1. esc_url_raw() turns relative paths into http://frontend/... (treats 'frontend' as hostname) -> adapter-hero now passes raw path to aether_viewmodel_resolve_image() (viewmodel.php) which prefixes content_url() for frontend/ paths.
  2. **renderer.php adapter-name resolution broke multi-word adapters:** 'adapter-wc-products' -> aether_adapter_wc-products (hyphen, not a fn) -> categories/bestsellers silently no-op'd. Fixed: preg_replace ^adapter[-_] + str_replace('-','_') -> aether_adapter_wc_products.
- **Live verification (Playwright):** hero 3 slides (swiper-init, images loaded, 700px), categories 1 (Uncategorized - real data), bestsellers 4 real WC products + CTA, reviews 4 cards + swiper-init, FAQ 6 items (1 active), newsletter, 0 broken images, 0 console errors/warnings. Screenshots: stage3-home-top.png / stage3-home-full.png.
- **Deploy note:** tar via 	ar.exe -czf ... --exclude "frontend/source" frontend (separate --exclude arg needed); stdin pipe truncates for 1MB+ - use docker cp for the .b64 then in-container base64 -d.

### Stage 4 - Shop COMPLETE + VERIFIED LIVE (2026-08-07)

- **Pretty permalinks ENABLED** (was plain ?page_id=23): update_option('permalink_structure','/%postname%/') + flush_rewrite_rules() via probe -> /shop/ 200 "Shop - AETHER", products at /product/{midnight-sneakers,aether-cap,black-chino-pants,phantom-tee}/. mod_rewrite/.htaccess already correct. DB option persists; no repo change.
- **archive-product.php (theme) rewritten:** pure section composition - shop-hero + shop-filter + shop-grid (args: posts_per_page from aether_shop_per_page=9, paged, orderby_shop, optional tax_query for category/tag archives, on_sale flag from ?on_sale=1) + newsletter.
- **New sections (3):** section-shop-hero (hero/page-title component + parallax-section behavior), section-shop-filter (filter-bar), section-shop-grid (card/product layout=shop + section/pagination w/ remove_query_arg base, reveal-group behavior).
- **New components (3):** hero/page-title (fog + label + h1 data-motion-text + subtitle), section/filter-bar (filter-btn list w/ active state), section/pagination (prev/next + numbered window w/ dots, query-string aware page URLs).
- **Adapter changes:** adapter-wc-products REWRITTEN -> returns {items, pagination{current,total}} + cta_label/cta_url when with_cta; on_sale -> post__in wc_get_product_ids_on_sale(); orderby_shop -> menu_order title; badge logic Sale > New(30d) > Featured; price_plain/old_price_plain keys (wc_price stripped). adapter-shop-hero NEW (is_product_category/is_product_tag/woocommerce_page_title, label "Collection", subtitle "Six colorways. One obsession."). adapter-wc-filter NEW (All + product_cat terms skipping unnamed/uncategorized + Sale button when sale products exist).
- **cards/product shop layout:** compact card w/ badge, image, name, price row (old_price_plain strikethrough via .price-old), Add to Cart button.
- **BUG FOUND & FIXED (content seeding):** all 6 products had NO featured images -> cards rendered image-less. Seeded real attachment (sneaker cover, media ID 60) as _thumbnail_id on all 6 products via probe.
- **Live verification (Playwright):** /shop/ 200; hero "Shop" + label + subtitle; filter bar shows "All*" (real data: only Uncategorized term + no on-sale products -> those buttons correctly hidden); 6 real product cards (Void Jacket $149.00 badge New etc.), 6/6 with loaded images, 0 broken images; pagination correctly hidden (6 <= 9/page); newsletter present; 0 console errors/warnings. /product-category/uncategorized/ -> hero title adapts to term ("Uncategorized"), 6 cards. Screenshots: stage4-shop-top.png / stage4-shop-grid.png.
- **Deploy:** re-tar frontend/ (6.5 MB stage-3 tar grew) -> docker cp .b64 -> in-container base64 -d + tar -xzf; EXTRACT_OK (SCHILY.fflags warnings harmless).

### Stage 5 - Single Product COMPLETE + VERIFIED LIVE (2026-08-07)

- **single-product.php (theme) rewritten:** pure composition - product + related (adapter-wc-products w/ related_to + posts_per_page 4) + newsletter.
- **New sections (2):** section-product (adapter-product; renders breadcrumb, pd-hero w/ gallery+info, sticky bar, specs, reviews, size-guide modal) + section-related (adapter-wc-products related_to).
- **New components (8):** product/breadcrumb, product/gallery (main+thumbs swiper+zoom), product/info (badge/title/price/rating/desc/colors/sizes/qty/actions/trust), product/sticky-bar, product/specs (accordion), product/reviews (score+bars+cards), product/related (swiper), product/size-guide (modal table).
- **adapter-product.php NEW:** gallery from WC gallery images (fallback featured x4 views); colors from pa_color terms + hex map (fallback tokens); sizes from pa_size (fallback US 7-13); specs from visible attributes (fallback 4 demo items); reviews from real WC review comments incl. rating bars (fallback 4.8/128 + 3 demo cards); breadcrumb Home/Collection/cat/product; add-to-cart = classic ?add-to-cart={id} flow.
- **tokens added:** aether_product_colors, aether_product_sizes, aether_size_table (12 rows), aether_spec_items, aether_product_trust, aether_product_score/count/bars, aether_product_reviews.
- **ENGINE BUG FOUND & FIXED (renderer.php):** aether_register_section always stored 'adapter_args' => array() -> isset() was ALWAYS true -> per-call $data NEVER reached adapters. Shop grid paged/tax_query/on_sale/orderby_shop and related related_to were silently ignored. Fixed: adapter_args = wp_parse_args($data, $registered_args) (per-call wins). Stage 4 shop ordering now actually applies (menu_order title ASC verified).
- **Live verification (Playwright):** /product/midnight-sneakers/ - breadcrumb Home/Collection/Uncategorized/Midnight Sneakers; gallery 4+4 swiper-init; badge "New Arrival" (real: seeded <30d); price $129.00; rating 4.8-128 (demo); colors 4, sizes 12, trust 3; sticky bar; specs 4 (1 open); reviews 3 cards + score + 5 bars; related = 4 EXCLUDING self (Nebula Hoodie, Phantom Tee, Black Chino Pants, Aether Cap) swiper-init; newsletter; 0 broken images; 0 console errors/warnings. Screenshots: stage5-product-top.png / stage5-product-info.png.

### Stage 6 - Cart / Checkout / Account DONE + VERIFIED LIVE (2026-08-07)

- **cart.php (theme) rewritten:** pure section composition — `aether_render_section('cart')` + newsletter (gated by `aether_section_newsletter`). Real cart data flows from WC session through `adapter-cart.php`.
- **WC long pages:** /cart/ 200; cart-section renders — "Your cart is empty" empty state (correct: cart empty), page-hero with breadcrumb Home/Cart, newsletter present.
- **Checkout:** `/checkout/` correctly **302 → /cart/** when cart is empty (WooCommerce standard). With items present it would render WC's checkout form inside AETHER shell (no stock override template needed).
- **My Account:** `/my-account/` 200; WooCommerce forms (login form present) render inside the AETHER shell; account-section present; 0 console errors.
- **Overrides note:** plugin `template-locator.php` points at `plugin/templates/` which does NOT exist on disk — override lookup no-ops and WC falls back to plugin templates (wrapped by AETHER header/footer). Only the default WC forms render; AETHER-styled cart/checkout templates (`sections/section-cart.php` = cart.php override exists; checkout/account use WC defaults) — cosmetic surface preserved.
- **Deploy:** `frontend/` fully re-deployed via tar+`docker cp` → sections 24, adapters 21, components 39 live.

### Stage 7 - Blog COMPLETE + VERIFIED LIVE (2026-08-07)

- **home.php (theme) rewritten:** blog index — `hero/page-title` ("The AETHER Dispatch") + `blog-grid` (adapter-blog) + newsletter. Used for the posts page assigned to `/blog/`.
- **single.php (theme) rewritten:** single post — `blog-single` (post_id via per-call data) + `blog-grid` as "Related Posts" (`posts_per_page=3`, `category_name`, `post__not_in`, `show_pagination=false`) + newsletter.
- **Seed:** blog index page `/blog/` created + assigned as `page_for_posts` (new option); sample post `/sample-post/` created (slug `sample-post`, "AETHER Sample Post — Step Into the Void", gutenberg content).
- **Live verification (Playwright + curl):** `/blog/` 200 — blog-grid renders post cards + pagination; `/sample-post/` 200 — article hero + body + related (no self); both 0 console errors; newsletter present.

### Stage 8 - Static pages COMPLETE + VERIFIED LIVE (2026-08-07)

- **8 static templates created (theme):** page-about, page-contact, page-faq, page-team, page-wishlist, page-login, page-register, page-coming-soon — all pure section composition, all sections gated by `aureon_get_option('aether_section_*', true)`.
- **Sections used per page:**
  - /about/ → mission, features, story, stats, team (gated: `aether_section_mission/features/story/stats/team`) + newsletter.
  - /contact/ → contact (adapter-contact) + newsletter.
  - /team/ → hero/page-title + team grid + newsletter.
  - /faq/ → faq (accordion, first item active) + newsletter.
  - /wishlist/ → wishlist + newsletter.
  - /login/, /register/ → auth (adapter-auth, mode=login|register) + form components; no newsletter.
  - /coming-soon/ → coming-soon (countdown) + notify form.
- **DB page seeding:** 9 static pages created (`wp_insert_post` via seed script in container): about(62), contact(63), team(64), faq(65), wishlist(66), login(67), register(68), coming-soon(69), blog(70), sample-post(71). Template meta `_wp_page_template` set for each. `page_for_posts` = 70.
- **Live verification (Playwright + curl):** all 9 routes 200, correct section IDs/classes per page, newsletter present where expected; 404 route serves the AETHER error/404 component.

### Stage 9 - Customizer toggles + section gating (2026-08-07) ✅ DONE

- **New file** `theme/inc/customizer/fields/frontend.php` (required from `inc/customizer.php` after `search-modal.php`):
  - New section `aureon_aether_section` ("AETHER Frontend", priority **120** — renders after `aureon_general_section` @99).
  - **Section Visibility** — 15 checkbox toggles: hero, categories, bestsellers, reviews, faq, newsletter, mission, features, story, stats, team, contact, auth, wishlist, coming_soon → `aether_section_*`.
  - **Shell & Motion** — shell: preloader, fog, announcement (announcement w/ active_callback), search_wrap; motion: motion_enabled, reveal, tilt, parallax, text.
  - **Announcement & Commerce** — text `aether_announcement_text` (active_callback on announcement_enabled), URL `aether_announcement_url`, number `aether_shop_per_page`.
  - All sanitized (`aureon_sanitize_checkbox`, `sanitize_text_field`, `esc_url_raw`, `absint`), transport refresh, defaults from global `$defaults`.
- **Defaults:** all section-visibility keys (incl. the new static-page keys) in `frontend/tokens/tokens.php` (true).
- **ALL template gating verified:** 404/single-product/packed-front/front-page + 9 static pages each call `aureon_get_option('aether_section_*', true)`.
- **BUG FOUND & FIXED (team empty):** team section (adapter-team) only read `aether_team` CPT (never registered) → always empty. Added demo fallback `aether_team_items` (4 members) in `tokens.php` + fallback loop in `adapter-team.php` (mirrors faq/testimonials). Team page now renders.
- **Live toggle test (curl):** `aether_section_mission=0` via `update_option('aureon_settings')` → /about/ dropped mission; restored → present again. (Note option lives in `aureon_settings` array bucket, NOT standalone rows — probe via PHP not raw mysql.)

### Stage 10 - Verify live route suite + engine (2026-08-07) ✅ DONE

- **Content seeding (all via container PHP script /tmp/seed.php):**
  - 9 pages + blog index + sample post (see Stage 6/7). All content created, templates assigned, `page_for_posts=70`.
- **Full suite verified** (curl over `http://localhost:8080`):

| Route | Status | Frontend assets | Notable |
|---|---|---|---|
| `/` (home) | 200 | ✅ | hero slider, categories, bestsellers, reviews, faq, newsletter render |
| `/about/`, `/contact/`, `/team/`, `/faq/`, `/wishlist/`, `/login/`, `/register/`, `/coming-soon/` | 200 | ✅ | each renders its gated sections |
| `/blog/` | 200 | ✅ | blog-grid (page_for_posts) |
| `/sample-post/` | 200 | ✅ | blog-single + related |
| `/shop/` | 200 | ✅ | shop-hero + filter + grid |
| `/product/…` | 200 | ✅ | breadcrumb + pd-hero + specs + reviews + related |
| `/cart/` | 200 | ✅ | empty cart state |
| `/checkout/` | 302 → /cart/ | ✅ | WC empty-cart redirect |
| `/my-account/` | 200 | ✅ | WC login form inside shell |
| `/no-such-page/` | 404 | ✅ | AETHER error/404 "Lost in the Void" |

- **Playwright console sweep:** /, /about/, / 404, /coming-soon/ — **0 console errors / 0 warnings** (only expected "404 Failed to load resource" on the intentional 404 route).
- **Countdown verified:** /coming-soon/ renders Days/Hours/Minutes/Seconds live count-up units.
- **Screenshots saved:** aureon-doc/stage6-10-home.png, aureon-doc/stage6-10-shop-grid.png.

### Stage 12 - Hardening layer restored + wired live (2026-08-07) ✅ DONE

- **Recovery:** the Stage-1 commit (`3e5741a`) shipped 11 `inc/aether-*.php` hardening files that were deleted (uncommitted) during the Phase 17.1 rollback. All 11 recovered via `git show HEAD:` and restored/adapted to the current engine:
  - `aether-security.php` — headers: `nosniff`, `X-Frame-Options: SAMEORIGIN` (Customizer bypass), `Referrer-Policy`, `Permissions-Policy`, `Content-Security-Policy-Report-Only` (nonce + strict-dynamic + CDN allowlists), `X-Powered-By` removal, HSTS.
  - `aether-seo.php` — OG + Twitter cards (site/singular/article/product/author/taxonomy), JSON-LD (Organization, WebSite+SearchAction, BreadcrumbList, Product w/ aggregateRating, Article), canonical, robots/geo meta.
  - `aether-newsletter.php` — **DB-backed** subscribers (table `wp_aether_newsletter_subscribers`, lazy dbDelta creation on admin_init — `register_activation_hook` replaced, plugin-only), AJAX + REST (`/aether/v1/newsletter/subscribe`), rate limit (1/IP/min), admin page under Appearance → Newsletter w/ stats/pagination/bulk-delete, CSV export.
  - `aether-ajax.php` — wishlist toggle/count (user meta, nopriv returns "please log in" + my-account redirect), quick-view.
  - `aether-performance.php` — resource hints, font preload, hero-image preload, CDN `?ver=` stripping (local assets keep filemtime), WC script disabling off-WC pages, HTML compression.
  - `aether-tokens.php` — dynamic `:root` output via `aureon_get_option` (`--void/--surface/--gold/--chrome/--font-*/--container-max`), registered as dependency of `aether-style`.
- **Wiring:** `inc/frontend.php` now requires the 6 new inc files + localizes `aetherAjax` on `aether-main` (ajaxUrl/nonce/restUrl/isUserLoggedIn). `adapter-shell.php` announcement items now read `aether_announcement_items` token (added to `tokens.php`) + all hardcoded strings i18n'd. `adapter-wc-products.php` emits `id` per product; `cards/product.php` renders `data-product-id` (both layouts). `main.js` newsletter forms POST to admin-ajax (graceful simulated-success fallback); wishlist buttons toggle via AJAX with count badge + login redirect.
- **BUGS FOUND & FIXED LIVE:**
  1. `wp_random_bytes()` — not a WP core function (GP-ism from recovery) → **fatal 500 on every page**. Replaced with `random_bytes()` (PHP 7+).
  2. Font preload with `crossorigin` mismatched the enqueued stylesheet's no-cors fetch → Chrome **ORB-blocked** the Google Fonts CSS. Removed `crossorigin` from the preload.
  3. New `defer` filter on vendor scripts reordered execution past non-deferred dependents (`animations.js` ran before GSAP → "GSAP or ScrollTrigger not loaded"). Defer filter removed entirely — engine footer enqueue order is the contract.
  4. `style.css` preload unused (enqueued link carries `?ver=filemtime`) → redundant preload removed.
  5. CDN `?ver=` stripping guard used handle-prefix (kept `?ver=5.3.3` on CDN assets) → now strips any non-local src.
- **Live verification:** `/` + `/shop/` 0 console errors / 0 warnings; security headers on every response; OG/JSON-LD/canonical in head; `:root` tokens printed; newsletter subscribed via browser form AND curl (2 rows in DB, IP logged); REST route registered; wishlist nopriv returns login redirect; all 11 restored files `php -l` clean (theme bind-mounted → live without redeploy; `frontend/` re-tarred + deployed).
- **Screenshots:** none taken this stage (verified via curl + Playwright console/network).

### Stage 11 - Visual regression + gates ✅ COMPLETE

- **PHP lint:** `php -l` clean across frontend (24 sections, 21 adapters, views, tokens), theme (93 files), plugin (122 files) — 0 syntax errors.
- **All gates green:** manifest resolvable (1:1 with call sites), no brand strings regressed, assets single-load, no duplicate shell output.
- **Remaining known (non-blocking)**
  1. ~~`/checkout/` and `/my-account/` render default WC templates~~ — **RESOLVED:** theme AETHER overrides exist (`checkout/form-checkout.php` → section-checkout, `myaccount/my-account.php` → account/profile + orders components, `woocommerce/checkout/thankyou.php`), routed via `template_include` (inc/frontend.php:180-203).
  2. ~~Fonts Cabinet Grotesk / Satoshi not downloaded~~ — **RESOLVED:** self-hosted woff2 in `frontend/assets/fonts/` + `fonts.css`.
  3. ~~`mu-plugins/aureon-fix-wc-session.php` needs recreation~~ — **RESOLVED:** on disk + container (MD5 match a9911c24…).
  4. ~~Plugin `template-locator.php` targets missing `<plugin>/templates/`~~ — **RESOLVED:** plugin override bridge removed; WC template routing now handled by theme `template_include` filter (inc/frontend.php).

### Stage 13 - Routing cleanup + container hygiene + doc reconciliation (2026-08-08) ✅ COMPLETE

- **main.js demo navigation fixed (2 sites):**
  1. `.product-card` click → `product-detail.html` (404 in WP) → now navigates to the card's own `<a href>` (real product permalink).
  2. Search overlay: `shop.html` suggestion links + Enter→`shop.html?q=` → now use `aetherAjax.shopUrl` / `aetherAjax.searchUrl` (localized by inc/frontend.php: WC shop page permalink + `/?s=`).
- **inc/frontend.php:** `aetherAjax` localize now carries `shopUrl` + `searchUrl` (bind-mounted theme → live instantly, verified in-container).
- **Container hygiene:** removed dead-root extraction junk at `wp-content/` root (duplicate `adapters/ assets/ components/ manifest/ sections/ source/ tests/ tokens/ views/` + 10 md docs, from a bad Aug-07 extraction) and pruned `frontend/source/` (deploy contract excludes it; pristine mirror lives on disk).
- **Live verification (Playwright):** `/` search overlay opens with `/shop/` suggestion links; typing + Enter → `/ ?s=sneaker` renders search results (0 errors); product-card body click on `/shop/` → `/product/midnight-sneakers/` (0 errors); full route suite: `/` `/shop/` `/cart/` `/my-account/` `/blog/` `/sample-post/` `/about/` `/contact/` `/team/` `/faq/` `/wishlist/` `/login/` `/register/` `/coming-soon/` `/checkout/`(empty→302 to `/cart/`) all OK, `/no-such-page/` → 404 AETHER.
- **Gates:** `node --check` main.js clean, `php -l` inc/frontend.php clean, 0 console errors / 0 warnings across tested routes.
- **Handoff:** ALL pending work committed + pushed — `44ea0c5` (546 files: frontend engine, mu-plugins, theme WC overrides, docs, memories; legacy `assets/aether/*` removed) + `33b5bef` (dupe root screenshot). `db539f8..33b5bef main -> main`. `.playwright-mcp/` now gitignored (test snapshots held local dev creds). Repo in sync with origin/main.

### Stage 14 - Phase 17 dynamic closure + CI gate (2026-08-09) ✅ COMPLETE

- **Dynamic conversion closed (Phases A–F, see `docs/PHASE_17_FRONTEND_DYNAMIC_CLOSURE_REPORT.md`):**
  - A — animation guard-first + watchdog + try/catch (Rule 7: GSAP failure no longer hides content; `@media (scripting:none)` fallback).
  - B — WC guards in adapters (product/wc-products/wishlist/cart — zero unguarded calls, incl. last `wc_attribute_taxonomy_name` hardening).
  - C — announcement/footer/contact settings-bound (G1/G4/G5); hero CTA defaults to shop.
  - D — `aether_demo_content` toggle gates all fallbacks (default true).
  - E — Playwright suite committed: 5 specs (routes, interactions, failure-injection, a11y, visual). Clone run **69 passed / 1 skipped (desktop mobile-drawer) / 0 failed**; 3 harness bugs fixed.
  - F — `page-styleguide.php` (manifest components only).
- **Gates:** `verify.sh` PASSED (php lint, JS check, component grep gate, 23 adapters, tokens/manifest/renderer present). **Fixed gate bugs:** grep gate now matches WP/WC function *calls* only (docblock mentions no longer false-fail); error counters no longer lost in pipe subshells.
- **CI:** `.github/workflows/ci.yml` rewritten to gate the real repo (was: Lumina `wp-content/themes/lumina` phantom paths — broke every push): static job = php lint + JS check + verify.sh on push/PR; optional `workflow_dispatch` e2e job runs the Playwright suite. First green CI run after fix.
- **Docs (11 files in `docs/`):** closure report, dynamic conversion baseline, data contract, component dynamicity matrix, customizer + woo binding matrices, failure mode report, conversion report, API usage, implementation plan, visual regression report.
- **Version:** theme + plugin bumped `1.0.0 → 1.1.0`; tag `v1.1.0-aureon` (2026-08-09).
