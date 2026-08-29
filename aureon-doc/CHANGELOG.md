# Aureon — Changelog

All notable changes to the Aureon product (theme + plugin) and its rebrand/maintenance work.

Format: `[category] description`. Versioning follows the product display version (v1.0.0) with internal constants noted.

---

## v2.0.0 — Ferm Living frontend integration + comprehensive documentation (2026-08-29)

Complete Ferm Living client frontend replacement with design pack, cart bridge, complete-page host, and authoritative documentation.

### Ferm Living Design Pack
- Complete frozen DOM integration at `frontend/designs/fermliving/`
- 10 shell components (announcement, header, footer, mobile-chrome, preloader)
- 2 card components (product, category)
- Product components (gallery, info, related)
- 16 section templates covering all page types
- `composer.php` with section ordering + adapter filters
- `manifest.json` v3.0.0 with asset references
- `tokens.php` with design token overrides
- Pack CSS (3285 lines) + pack JS (463 lines)

### Cart Bridge Layer
- 4 Shopify→WooCommerce cart API shims (`/cart.js`, `/cart/add.js`, `/cart/change.js`, `/cart/clear.js`)
- `bridge.js` ≤150 lines for cart-count sync + wishlist
- WC cart fragment integration

### Complete-Page Host
- `ferm-page.php` for frozen HTML page rendering
- Route mapping for all page types (front page, products, collections, pages, blogs)
- Body content extraction from frozen HTML
- `wp_head()`/`wp_footer()` wrapping

### Documentation (NEW)
- **FRONTEND_REPLACEMENT_AND_EDITING_GUIDE.md** (1162 lines): Authoritative guide for safely replacing/editing the frontend — architecture, golden rule, safe zones, forbidden zones, pack creation, component/section editing, fonts, CSS, JS, cart bridge, complete-page mode, data flow, testing, rollback, cookbook
- **TEMPLATE_REQUIREMENTS_FOR_CORE_THEME.md** (1863 lines): Complete guide for creating, cloning, and building any template frontend — feature map, architecture, connection points, template types, cloning, creation, data flow, components, sections, assets, tokens, adapters, security, performance, accessibility, testing, deployment, feature integration checklist
- **FRONTEND_REPLACEMENT_PROMPT.md**: Copy-paste prompt for AI/developer to replace frontend with new design
- **AETHER_DYNAMIC_ARCHITECTURE_CURRENT_STATE.md**: Updated architecture state

### Forensics Audit (10 documents)
- `docs/forensics/CORE-THEME-AUDIT.md`: 120+ files classified, request lifecycle traced
- `docs/forensics/CORE-TO-FERM-INTEGRATION-MAP.md`: All dynamic fields mapped, 11 gaps documented
- `docs/forensics/FERM-TEMPLATE-AUDIT.md`: 980 pages → 10 families, CSS/JS/fonts inventory
- `docs/forensics/COMPLETE-PAGE-HOST-ARCHITECTURE.md`: Complete-page host analysis, asset contamination fix
- `docs/forensics/FERM-COMPLETE-INTEGRATION-MAP.md`: Complete integration map
- `docs/forensics/FERM-SOURCE-COMPLETE-INVENTORY.md`: Complete source inventory

### npm Package Updates (12 packages)
- opencode-mem 2.25.0, @mimo-ai/cli 0.1.13, @shopify/dev-mcp 1.14.7
- @wordpress/env 11.14.0, @wordpress/scripts 34.2.0
- sass 1.103.1, terser 5.51.2, vite 8.2.2
- @opencode-ai/plugin 1.18.25, superpowers 0.0.2

### Architecture
- 6-layer data flow: WP/WC → 23 adapters → ViewModels → Renderer → Composer → 53 components + 26 sections
- Design pack system: `aether_resolve_design_path()` checks pack dir first, falls back to engine tree
- Golden Rule: NEVER edit `aureon/theme/` or `aureon/plugin/` — only `frontend/` layer

### Verification
- Git tag `v2.0.0-ferm-living` pushed
- GitHub release created
- 24 files committed, 8273 insertions

---

## v1.3.0 — Frontend platform: M6–M10 design packs (2026-08-15)

The AETHER frontend became a **design-pack platform** — one engine, multiple frontends, theme core untouched.

### M6 — Design isolation
- `aether_active_design()` resolution: option `''`/`'luxury'` → luxury engine (default), `'lumen'` → design pack; body class `design-<slug>`.
- **Critical cache bug fixed:** the static cache stored the raw sanitized option (`''`) before the fallback applied — first call per request returned `'luxury'`, every later call returned `''` (broken body class `design-`, skipped luxury assets). Root-caused via container probes (whole-body sentinel returns proved the volume file executes; logic-logging build showed `ret='luxury'` while probe read `''`). Fix: resolve branch → apply fallback → cache → return. Verified through wp-load: `string(6) "luxury"`.

### M7 — Asset engine + isolation by construction
- `frontend/views/assets.php`: per-design asset pipeline; luxury assets never load alongside a pack (`theme/inc/frontend.php:111` guard); platform CDNs + contract JS still load for non-luxury designs.

### M8 — Design manifest contract
- `frontend/designs/<slug>/manifest/components.php` pack contract; manifest validation (`validate-manifest.cjs`).

### M9 — Per-design visual baselines
- `frontend/tests/` per-design baselines; `design-isolation.spec.js` 6/6.

### M10 — Lumen pack (first shipped design pack)
- `frontend/designs/lumen/` — full alternate presentation layer inside one engine; proof that a client frontend ships with zero theme-core changes.

### G4 — Newsletter flake fix
- Rate limit 1/IP/min in `inc/aether-newsletter.php` (deterministic test behavior; was flaky under burst).

### Verification
- design-isolation 6/6, routes 32/32, `verify.sh` PASSED, main.js MD5 frozen `6d8f3b671333571508efcb53b1e39e60`, 0 console errors.
- Docs: `aureon-doc/AETHER-BRIDGE.md` (new), `aureon-doc/FRONTEND-OPERATIONS.md` (new), README/STATUS refreshed, `docs/AETHER_DYNAMIC_ARCHITECTURE_CURRENT_STATE.md` refreshed.
- Commits `901c1f6..9dd4e21` (7), tag `v1.3.0-m6-m10`, pushed.

---

## v1.1.0 — AETHER frontend dynamic closure & CI (2026-08-09)

Frontend **v1.1.0** — closure of the Phase 17 dynamic-conversion loop (all gates VERIFIED, 69/69 E2E).

### Dynamic conversion (Phase 17 A–F)
- **A — Animation safety (Rule 7 fix):** `animations.js` now checks GSAP/ScrollTrigger existence *before* adding `html.has-motion`; watchdog + try/catch added; `@media (scripting:none)` preloader fallback. Motion CSS can no longer hide `[data-reveal]` content on CDN failure.
- **B — WC guards:** zero unguarded `wc_get_*`/`wc_*` calls across product/wc-products/wishlist adapters; final raw call (`wc_attribute_taxonomy_name` in adapter-cart) `function_exists`-guarded.
- **C — Settings-bound:** announcement bar, footer columns, contact info read Customizer settings; hero CTA defaults to shop page.
- **D — Demo policy:** `aether_demo_content` master toggle (default on) gates all demo fallbacks.
- **E — Test suite committed:** Playwright 5 specs (routes, interactions, failure-injection `@failure`, a11y, visual). 69 passed / 1 skipped (desktop mobile-drawer) / 0 failed; 3 harness bugs fixed (absolute-href product selector, FAQ live-locator, search overlay timeout).
- **F — Styleguide:** `page-styleguide.php` renders manifest components only.

### Gates & CI
- `frontend/tests/verify.sh` — grep gate now matches WP/WC function *calls* only (docblock mentions no longer false-fail); error counting fixed (was lost in pipe subshells). PASSED 2026-08-09.
- `.github/workflows/ci.yml` — rewritten for the actual repo (was Lumina `wp-content/themes/lumina` phantom that failed every push): php -l + JS check + verify.sh on push/PR; optional `workflow_dispatch` Playwright E2E with `WEB_BASE_URL`.

### Docs
- 11 files under `docs/` — closure report, dynamic conversion baseline, data contract, component dynamicity matrix, customizer/woo binding matrices, failure-mode report, conversion report, API usage, implementation plan, visual regression report.

### Versioning
- Theme `style.css` + plugin `aureon-studio.php` → **1.1.0** (from 1.0.0), aligned tag `v1.1.0-aureon`.

---

## v1.0.0 — Full rebrand & hardening (2026-08-05)

Aureon = fork/rebrand of **GeneratePress 3.6.1** (theme) + **GP Premium 2.5.6** (plugin), both GPL-2.0-or-later.

### Branding
- Renamed product to **Aureon** (theme) and **Aureon Studio** (plugin); text domains `aureon` / `aureon-studio`.
- Style/readme headers: `Version 1.0.0`, `Theme URI`/`Author URI` → `https://aureonstudio.com`, `Contributors: Aureon Studio`.
- Replaced theme screenshot; icon font rebuilt as `aureon.{eot,svg,ttf,woff,woff2}` with all `generate*` glyph names → `aureon*`.
- Removed EDD license-key UI (updater kept, silently no-ops); removed email contact; cleaned repo structure.

### Fingerprint removal (GeneratePress detection → zero)
Scan methodology: iterative loop (`generatepress` literals **and** `generate[A-Z]` camelCase tokens), each hit verified at `file:line`.

| Category | Renamed | Where |
|---|---|---|
| JS globals (6) | `generatePressTypography`→`aureonTypography`, `generateCustomizerControls`→`aureonCustomizerControls`, `generateBlog`→`aureonBlog`, `generateProDashboard`→`aureonProDashboard`, `generateSecondaryNav`→`aureonSecondaryNav`, `generateWooCommerce`→`aureonWooCommerce` | PHP writers + JS readers (theme + plugin bundles) |
| JS bundle internals (2) | `generateGlobalColors`→`aureonGlobalColors`, `generateQuantityButtons`→`aureonQuantityButtons` | `theme/assets/dist/customizer.js`, `plugin/woocommerce/functions/js/woocommerce*.js` |
| PHP class (1) | `GenerateLabelControl`→`AureonLabelControl` | theme `inc/customizer/controls/class-deprecated.php` |
| Files (3) | `generate-sections-metabox.{css,js,js-4.9}`→`aureon-sections-metabox.*` | plugin `sections/functions/metaboxes/` — also fixes broken editor |
| Comments | `GP `→`Aureon ` incl. user-facing **"GP Hooks"→"Aureon Hooks"** (4 strings), `/* GP */`→`/* Aureon */` | plugin (18 files), theme offside.css/style tags |
| Class globals | `generateProDashboard`/`generatePressTypography` reader bundles | plugin `dist/dashboard.js`, `dist/customizer.js` |

**Result:** zero `generatepress` / `gp premium` / `edge22` / `tom usborne` (camelCase) hits outside the intentional GPL attribution in `license.txt`; zero `generate-*` filenames.

### Customizer collision fixes (two related bugs)
1. **Handle collision** — theme and plugin both enqueued `…-customizer-controls-react`; plugin overwrote the theme's enqueue → theme React bundle lost translations/localize. Fixed: theme `aureon-customizer-controls-react`, plugin `aureon-pro-customizer-controls-react`.
2. **Global collision** — theme and plugin both localized `var aureonCustomizerControls`; plugin loaded last, overwrote the theme's `palette`/`aureonFontLibrary` → React Font Manager crashed (`Cannot read properties of undefined (reading 'length')`). Fixed: plugin uses `aureonProCustomizerControls` in PHP (`library/customizer-helpers.php`) + bundle (`dist/customizer.js`, 6 refs).

### i18n cleanup (267 brand entries removed)
- **`.mo` (22 files):** rebuilt via custom Python MO-writer with the correct 28-byte header; strings round-trip verified; 267 GeneratePress-brand strings removed (e.g. "Requires GeneratePress %s.", "Requires GP Premium %s.").
- **`.json` (6 files, `-92fa…` variants):** WordPress JS-translation format; 2 dead brand entries removed per file; PHP-style escaping preserved; GenerateBlocks strings + `""` empty marker retained.

### Validation
- `php -l`: 0 errors across all theme + plugin PHP.
- `node --check`: 0 errors across all non-minified JS.
- **Live Docker verification** (phantom-wp @ `localhost:8080`, WP + WooCommerce, admin/admin123):
  - Customizer loads → **0 console errors** (only WP-core sandbox/deprecation warnings).
  - React **Font Manager** ("Add Font") and **Typography Manager** ("Add Typography") render.
  - **Global Colors** palette renders.
  - Plugin typography groups inject correctly (Secondary Navigation, WooCommerce) via `aureonProCustomizerControls`.
- Repository scans: 0 bad filenames, 0 camelCase `generate*` tokens, no JS syntax errors.

### Feature removal — License key system
- **Removed the EDD license key system entirely** (2026-08-05): REST `/license/` + `/beta/` endpoints, `library/class-plugin-updater.php` (EDD SL updater), updater init + API-params filter in `aureon-studio.php`, license localize data (`licenseKey`, `licenseKeyStatus`, `betaTester`), the React license section + `#aureon-license-key` mount (this also **fixes the React #299 console error** — `createRoot(null)` on the removed container), legacy activation handler in `inc/legacy/activation.php`, deprecated wrappers in `inc/deprecated.php`, and the `.aureon-license-key-area` styles.
- No activation required — all modules work out of the box.
- Replaced by clean seams for a future commercial system: `Aureon_Pro_License_Provider` / `Aureon_Pro_Update_Provider` interfaces with null implementations, swappable via `aureon_studio_license_provider` / `aureon_studio_update_provider` filters.

### Known open items (non-GP, pre-existing)
- ~~Site Library API `https://example.com/invalid`~~ → **RESOLVED 2026-08-05:** Site Library feature removed entirely (module `site-library/`, `dist/site-library.*` bundles, theme-dashboard "Site Library" link, `templateImageUrl` element-template CDN endpoint). Client starter templates are built in-house, not fetched from an agency API.
- ~~Legacy activation endpoint `https://example.com`~~ → **RESOLVED 2026-08-05:** legacy license activation handler in `inc/legacy/activation.php` removed entirely.
- ~~EDD updater points at `https://aureonstudio.com`~~ → **RESOLVED 2026-08-05:** EDD updater deleted; replaced by `Aureon_Pro_Null_Update_Provider` seam (standard WP updates).

### Comprehensive E2E verification (2026-08-05)
Full end-to-end verification of ALL features on Docker `phantom-wp` (:8080). **0 console errors, 0 PHP warnings** across all surfaces:

- **Customizer — Deep Verification (2026-08-05):** All 18 major sections verified with control counts and live preview testing:
  - Site Identity: 9 controls, live preview instant ✅
  - Typography: Font Manager + Typography Manager React render correctly ✅
  - Colors: 322 controls (Global Colors + body/link/headings/buttons/footer) ✅
  - General: 12 controls (CSS print, icons, links, combine CSS) ✅
  - Layout Panel: 12 sub-sections (Container 13, Header 14, Navigation 16, Sidebars 8, Footer 10, Top Bar 15, Blog 4, WooCommerce 53) ✅
  - Spacing Panel: 5 sub-sections (template-rendered on open) ✅
  - Backgrounds Panel: 10 sub-sections (body, header, nav, content, sidebar, footer) ✅
  - WooCommerce: 5 sub-sections (Store Notice 7, Product Catalog 13, Product Images 12, Checkout 26, Colors 115) ✅
  - Menus: 3 menu locations, 10 widget areas ✅
  - Homepage Settings, Additional CSS ✅
  - Live preview: site title change + container width change both update instantly ✅
- **Dashboard:** 10 modules (Backgrounds, Blog, Copyright, Disable Elements, Elements, Font Library, Menu Plus, Secondary Nav, Spacing, WooCommerce) — all toggleable. Start Customizing (4 quick links), Import/Export (All/Global Colors/Typography + Export/Import), Reset — all functional. No License Key, no Site Library.
- **Font Library:** 3 tabs (Font Library, Upload Custom Fonts, Install Google Fonts) — all functional.
- **Elements CPT:** List table, Add New Element (block editor), Display Rules (Location/Exclusion/User Role), Element settings (type, Block Element, Editor width) — all functional.
- **Front-end:** Homepage renders header, navigation (Sample Page, Shop, Cart, Checkout, My Account), content ("Hello world!"), sidebar (Search, Recent Posts, Recent Comments), footer — 0 errors.
- **WooCommerce:** Shop, Cart, Checkout pages render without warnings. Session initialization fixed via mu-plugin.
- **REST API:** 17 routes registered across 4 namespaces (`aureon-pro/v1`, `aureon-font-library/v1`, `aureon/v1`, `wp/v2/aureon_elements`). No `/license/` or `/beta/` routes.
- **Console error summary:** 0 errors, 2 warnings (WP core — sandbox iframe + tooltip deprecation). **0 PHP warnings** in debug.log.

### WooCommerce session fix (2026-08-05)
- **Root cause:** WooCommerce's `wc_clear_cart_after_payment()` accesses `WC()->session->order_awaiting_payment` on `template_redirect` without checking if `WC()->session` is initialized. This caused `PHP Warning: Attempt to read property "order_awaiting_payment" on null` on REST API, customizer, and front-end pages.
- **Fix:** Mu-plugin `mu-plugins/aureon-fix-wc-session.php` that:
  1. Initializes WC session early on `init` hook (priority 1)
  2. Removes the original unguarded `wc_clear_cart_after_payment` hook
  3. Re-registers it with a safe wrapper that ensures session is initialized before access
- **Verification:** Debug.log empty after visiting all 12 pages (customizer, dashboard, font library, elements, front-end, WooCommerce pages, REST API endpoints).

### Editor deprecation warnings fix (2026-08-05)
- **Root cause:** Plugin `dist/block-elements.js` called `wp.data.select('core/edit-post').getPreference('panels')` — deprecated since WP 6.0 in favor of `wp.data.select('core/preferences').get('core/edit-post', 'panels')`.
- **Fix:** Replaced 6 occurrences in `dist/block-elements.js` with the new API; added `|| {}` null-check fallback to prevent TypeError on initial render when panels data isn't yet available.
- **Verification:** Elements editor now shows 0 errors, 0 warnings (down from 2 errors + 2 warnings).

---

## v0.x — Initial rebrand (Aug 4, 2026)
- Original conversion: GP 3.6.1 → Aureon, GP Premium 2.5.6 → Aureon Studio; 39 ordered replacement rules; version strategy (display 1.0.0, internal `AUREON_VERSION`/`AUREON_STUDIO_VERSION`); GPL license files; WPML config; 15 professional-audit issues fixed.