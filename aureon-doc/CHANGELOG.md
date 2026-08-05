# Aureon — Changelog

All notable changes to the Aureon product (theme + plugin) and its rebrand/maintenance work.

Format: `[category] description`. Versioning follows the product display version (v1.0.0) with internal constants noted.

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

---

## v0.x — Initial rebrand (Aug 4, 2026)
- Original conversion: GP 3.6.1 → Aureon, GP Premium 2.5.6 → Aureon Studio; 39 ordered replacement rules; version strategy (display 1.0.0, internal `AUREON_VERSION`/`AUREON_STUDIO_VERSION`); GPL license files; WPML config; 15 professional-audit issues fixed.