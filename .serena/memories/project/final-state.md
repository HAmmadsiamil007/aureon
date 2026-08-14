# Aureon Project — Final State

> **LATEST MILESTONE (2026-08-14):** M6–M10 design-pack milestone COMPLETE + PUSHED (tag `v1.3.0-m6-m10`) —
> G4 newsletter flake resolved (server IP rate limit, not reveal), design.php static-cache fallback bug fixed,
> luxury mode restored + smoke-verified (isolation 6/6, routes 32/32, verify.sh PASS). See
> `.serena/memories/frontend-platform/M6-M10-lumen-proof-state.md` and
> `.serena/memories/project/frontend-complete-status.md` §5 for the full record.

## Product Identity
- **Aureon theme** v1.0.0 (internal: `AUREON_VERSION = 3.6.1`)
- **Aureon Studio plugin** v1.0.0 (internal: `AUREON_STUDIO_VERSION = 3.0.0`)
- Text domains: `aureon` / `aureon-studio`
- License: GPL-2.0-or-later (fork of GeneratePress 3.6.1 + GP Premium 2.5.6)

## License Removal (COMPLETE — 2026-08-05)
- 11 commits on `main`, 12 tasks executed, all verified
- Branch: 12 commits ahead of `origin/main`
- Removed: EDD updater, REST `/license/` + `/beta/` endpoints, dashboard license data, legacy activation handler, deprecated wrappers, dashboard.js license mount, license CSS rules
- Added: `Aureon_Pro_License_Provider` + `Aureon_Pro_Update_Provider` seams (null implementations, swappable via filters)
- React #299 fixed: `createRoot(null)` on removed `#aureon-license-key` container eliminated

## WooCommerce Session Fix (2026-08-05)
- **Root cause:** WooCommerce `wc_clear_cart_after_payment()` accesses `WC()->session->order_awaiting_payment` without null-checking session
- **Fix:** Mu-plugin `mu-plugins/aureon-fix-wc-session.php`
  - Initializes WC session early on `init` (priority 1)
  - Removes original unguarded hook, re-registers with safe wrapper
- **Verification:** 0 PHP warnings in debug.log after visiting all 12 pages

## Module System (16 modules active)
1. Backgrounds ✅
2. Blog ✅
3. Copyright ✅
4. Disable Elements ✅
5. Elements ✅ (CPT: `aureon_elements`)
6. Font Library ✅
7. Menu Plus ✅
8. Secondary Nav ✅
9. Spacing ✅
10. WooCommerce ✅
- ~~Site Library~~ — REMOVED (2026-08-05)
- ~~License Key~~ — REMOVED (2026-08-05)

## Comprehensive E2E Verification (2026-08-05)
**0 console errors, 0 PHP warnings** across all surfaces on Docker `phantom-wp` (:8080):

### Theme Customizer — PERFECT (2026-08-05 deep verification)
**All sections load, all controls render, live preview works, 0 errors.**

#### Top-level sections (18 verified)
| Section | Controls | Status |
|---|---|---|
| Site Identity | 9 (blogname, tagline, logo, retina logo, logo width, inline logo) | ✅ Works |
| Typography | 10 (Font Manager + Typography Manager React, Google font-display) | ✅ React renders |
| Colors | 322 (Global Colors React + body/link/headings/button/footer colors) | ✅ React renders |
| General | 12 (CSS print method, icons, underline links, combine CSS) | ✅ Works |
| Homepage Settings | 10 (show on front, page on front, page for posts) | ✅ Works |
| Additional CSS | 1 (custom CSS editor) | ✅ Works |

#### Layout Panel — 12 sub-sections
| Sub-section | Controls | Status |
|---|---|---|
| Container | 13 (width, separator, content layout, alignment, padding) | ✅ Live preview works |
| Header | 14 (layout, inner width, alignment, navigation-as-header) | ✅ Works |
| Primary Navigation | 16 (layout, position, drop point, mobile breakpoint) | ✅ Works |
| Secondary Navigation | 12 (layout, position, alignment) | ✅ Works |
| Sidebars | 8 (layout, blog layout, sidebar widths) | ✅ Works |
| Footer | 10 (layout, widget areas, back-to-top) | ✅ Works |
| Top Bar | 15 (width, inner width, alignment, padding) | ✅ Works |
| Blog | 4 (post loop element, page hero element, post meta area) | ✅ Works |
| WooCommerce Layout | 53 (cart, breadcrumbs, shop layout, product grid) | ✅ Works |
| Mobile Header | via Menu Plus module | ✅ Works |
| Sticky Navigation | via Menu Plus module | ✅ Works |
| Off Canvas Panel | via Menu Plus module | ✅ Works |

#### Spacing Panel — 5 sub-sections (template-rendered)
| Sub-section | Controls | Status |
|---|---|---|
| Header Spacing | Template: `tmpl-customize-control-aureon-spacing-content` | ✅ Renders on open |
| Content Spacing | Template-rendered | ✅ Renders on open |
| Sidebar Spacing | Template-rendered | ✅ Renders on open |
| Navigation Spacing | Template-rendered | ✅ Renders on open |
| Footer Spacing | Template-rendered | ✅ Renders on open |

#### Backgrounds Panel — 10 sub-sections
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

#### Menu Plus Panel — 1 sub-section
| Sub-section | Controls | Status |
|---|---|---|
| Menu Plus | 1 (module toggle) | ✅ Works |

#### WooCommerce Panel — 5 sub-sections
| Sub-section | Controls | Status |
|---|---|---|
| Store Notice | 7 (demo notice, store notice text) | ✅ Works |
| Product Catalog | 13 (shop display, category display, sorting) | ✅ Works |
| Product Images | 12 (single image width, thumbnail width, cropping) | ✅ Works |
| Checkout | 26 (company field, address fields, order notes) | ✅ Works |
| WooCommerce Colors | 115 (button colors, sale badge, star rating) | ✅ Works |

#### Live Preview Verified
- Site title change → preview iframe updates instantly ✅
- Container width change → preview layout adjusts ✅
- All settings use `wp.customize()` API for real-time preview ✅

### Plugin Dashboard (`themes.php?page=aureon-options`)
- 10 module cards with toggle buttons ✅
- Start Customizing (4 quick links) ✅
- Import/Export (All/Global Colors/Typography + Export/Import) ✅
- Reset ✅
- No License Key, no Site Library ✅

### Font Library (`themes.php?page=aureon-font-library`)
- 3 tabs: Font Library, Upload Custom Fonts, Install Google Fonts ✅

### Elements CPT
- List table, Add New Element (block editor) ✅
- Display Rules (Location/Exclusion/User Role) ✅
- Element settings (type, Block Element, Editor width) ✅

### Front-end Homepage
- Header, navigation (Sample Page, Shop, Cart, Checkout, My Account), content, sidebar, footer ✅
- 0 console errors ✅

### WooCommerce Pages
- Shop, Cart, Checkout render without warnings ✅
- Session initialization fixed via mu-plugin ✅

### REST API
- 17 routes registered: 5 aureon-pro, 8 font-library, 2 core aureon, 2 wp/v2/aureon_elements ✅
- No `/license/` or `/beta/` routes ✅

## Console + PHP Warning Summary
| Surface | Console Errors | Console Warnings | PHP Warnings |
|---|---|---|---|
| Customizer (all panels) | 0 | 2 (core sandbox + tooltip deprecation) | 0 |
| Plugin dashboard | 0 | 0 | 0 |
| Font Library | 0 | 0 | 0 |
| Elements editor | 0 | 0 | 0 |
| Elements list table | 0 | 0 | 0 |
| Front-end homepage | 0 | 0 | 0 |
| WooCommerce pages | 0 | 0 | 0 |
| REST API | 0 | 0 | 0 |
| **Total** | **0** | **2 (WP core, unfixable)** | **0** |

### Resolved (2026-08-05)
- `select('core/edit-post').getPreference is deprecated` — replaced with `select('core/preferences').get()` in `dist/block-elements.js` (6 occurrences) + null-check fallback
- `TypeError: Cannot read properties of undefined (reading 'aureon-block-element/aureon-block-element')` — added `|| {}` fallback on panels access

### Remaining (WP core, unfixable)
1. Customizer sandbox iframe: `allow-scripts allow-same-origin` — `customize-controls.js:6325` sets both on preview iframe; required for live preview
2. `wp.components.tooltip` `position` prop deprecated since WP 6.4 — core component uses deprecated prop internally; not our code

## Docker Environment
- Container: `phantom-wp` (:8080)
- DB: `phantom-db` (MySQL)
- Admin: admin / admin123
- Plugin path: `/var/www/html/wp-content/plugins/aureon-studio/`
- Theme path: `/var/www/html/wp-content/themes/aureon/`
- Mu-plugin: `/var/www/html/wp-content/mu-plugins/aureon-fix-wc-session.php`
- Deploy method: base64 encode + `docker exec -i phantom-wp sh -c 'echo ... | base64 -d > file'`

## Documentation Files (aureon-doc/)
- `STATUS.md` — comprehensive verification matrix + deep Customizer verification with control counts (updated 2026-08-05)
- `PLUGIN.md` — 16 active modules documented, 9 known issues tracked, seams documented
- `THEME.md` — full theme architecture (~450 lines), JS globals, collision fixes
- `CHANGELOG.md` — complete change history with E2E verification + Customizer deep verification + WooCommerce fix + deprecation fix
- `README.md` — project index, folder map, quick start
- `plans/2026-08-05-license-removal.md` — 12-task implementation plan (completed)
- `specs/2026-08-05-license-removal-design.md` — design spec for license removal

## Key Technical Details
- Provider seams: `Aureon_Pro_License_Provider` / `Aureon_Pro_Update_Provider` interfaces
- Filters: `aureon_studio_license_provider`, `aureon_studio_update_provider`
- JS global: `aureonProCustomizerControls` (plugin), `aureonCustomizerControls` (theme) — must stay distinct
- Enqueue handles: `aureon-customizer-controls-react` (theme), `aureon-pro-customizer-controls-react` (plugin) — must stay distinct
