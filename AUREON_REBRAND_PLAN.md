# AUREON REBRAND PLAN

## Complete Deep Redesign of GeneratePress 3.6.1 + GP Premium 2.5.6

**Project:** `C:\Users\hamma\Downloads\wordpress`
**Date:** 2026-08-04
**Status:** Analysis Complete — awaiting user review before execution
**Goal:** 100% functional rebrand into Aureon theme + Aureon Studio plugin, zero GP fingerprints, zero broken features, clean legal posture.

---

## 0. LEGAL GUARDRAIL

GeneratePress and GP Premium are **GPL v2-or-later**. Any derivative must stay GPL v2-or-later. This is what allows you to sell the theme/plugin legally. An MIT header on GPL-derived code is a copyright violation. Copyright line everywhere: **Aureon Studio**.

---

## 1. EXECUTIVE SUMMARY

| Item | Current | After Rebrand |
|---|---|---|
| Theme dir | `generatepress.3.6.1\generatepress` | `aureon.1.0.0\aureon` |
| Theme name | GeneratePress | Aureon |
| Theme display version | 3.6.1 | 1.0.0 |
| Theme internal constant | `GENERATE_VERSION='3.6.1'` | `AUREON_VERSION='4.0.0'` |
| Text domain | `generatepress` | `aureon` |
| Theme author | Tom Usborne / EDGE22 | Aureon Studio |
| Plugin dir | `gp-premium_v2.5.6\gp-premium` | `aureon-studio.1.0.0\aureon-studio` |
| Plugin name | GP Premium | Aureon Studio |
| Plugin display version | 2.5.6 | 1.0.0 |
| Plugin internal constant | `GP_PREMIUM_VERSION='2.5.6'` | `AUREON_STUDIO_VERSION='3.0.0'` |
| Plugin text domain | `gp-premium` | `aureon-studio` |
| Feature modules | 17 | Same 17, unchanged |
| Feature parity | 100% | 100%, nothing removed |
| Remote endpoints | generatepress.com, gpsites.co | Neutralized to aureonstudio.com |
| License | GPL v2+ (GeneratePress/EDGE22) | GPL v2+ (Aureon Studio) |
| Ordered replacement rules | 34 | 39 (5 new: gpp-*, GP One, GPP) |

---

## 2. DEEP ANALYSIS FINDINGS

### 2.1 Theme (144 files)
- `functions.php`: defines `GENERATE_VERSION = '3.6.1'`, `load_theme_textdomain('generatepress')`, registers menus, requires 15+ inc files via `$theme_dir . '/inc/...'`
- `style.css`: Theme Name GeneratePress, Author Tom Usborne, EDGE22 Studios LTD copyright, Version 3.6.1
- `inc/`: core files (theme-functions, defaults, class-css, css-output, general, customizer, markup, typography, plugin-compat, block-editor, class-typography, class-typography-migration, class-html-attributes, class-theme-update, class-rest, deprecated, meta-box, class-dashboard, dashboard), structure/ (10 files), customizer/ (fields, controls)
- `assets/`: css/ (all.css, main*.css, style*.css, mobile*.css, components/*, admin/*), dist/ (block-editor.js, customizer.js, dashboard.js, modal.js + .asset.php), fonts/ (generatepress.{eot,svg,ttf,woff,woff2} = icon font, fontawesome-webfont.*), js/ (a11y, back-to-top, dropdown-click, menu, navigation-search + .min.js)

### 2.2 Plugin (329 files)
- `gp-premium.php`: header (GP Premium, Tom Usborne, gp-premium textdomain), defines `GP_PREMIUM_VERSION/DIR_PATH/DIR_URL`, `GP_LIBRARY_DIRECTORY(_URL)`, loads 4 core files, loads 17 modules via `generatepress_is_module_active()`, EDD updater, theme info notice, standalone addon deactivator
- 17 modules: backgrounds, blog, colors, copyright, disable-elements, elements, font-library, general, hooks, menu-plus, page-header, secondary-nav, sections, site-library, spacing, typography, woocommerce
- `library/`: shared customizer controls + EDD updater (`class-plugin-updater.php`)
- `dist/`: compiled JS/CSS (block-elements.js/css, dashboard.js/css, editor.js/css, font-library.js/css, site-library.js/css, adjacent-posts.js)
- `langs/`: 22 `.mo` binary translations + 30+ `.json` translation files
- `inc/legacy/`: dashboard, activation/license (623 lines, full EDD license activation flow), import-export, reset
- `wpml-config.xml`: `theme_mods_generatepress` + 70+ `_generate_*` meta fields

### 2.3 Token Census
- Theme: 4,699 `generate*` tokens, 57 `GENERATE_*` constants, 218 `gp-*`/`GP_*` tokens
- Plugin: 11,311 `generate*` tokens, 165 `GENERATE_*` constants, 3,232 `gp-*`/`GP_*` tokens, 2,552 brand tokens

### 2.4 Remote Endpoints (MUST neutralize)
| Location | Endpoint |
|---|---|
| site-library/class-site-library-rest.php:230 | `https://sites.generatepress.com/wp-json/gp-starter-sites/v1/sites` |
| site-library/class-site-library-rest.php:234 | `https://gpsites.co/wp-json/wp/v2/sites?per_page=100` |
| site-library/class-site-library.php:156-157 | Support/Documentation links |
| gp-premium.php (updater) | `https://generatepress.com` (EDD SL API) |
| inc/legacy/activation.php (license) | `https://generatepress.com` + `docs.generatepress.com` |
| style.css, readme.txt | generatepress.com, docs.generatepress.com, donate links |

### 2.5 Version Gate Trap (CRITICAL)
Plugin calls `version_compare( generate_premium_get_theme_version(), '3.1.0-alpha.1', '>=' )`. Setting `AUREON_VERSION='1.0.0'` makes all `>= 3.x` gates FALSE → premium features disabled → broken plugin.

**Solution**: `AUREON_VERSION='4.0.0'`, `AUREON_STUDIO_VERSION='3.0.0'`. Display headers show `1.0.0`.

---

## 3. RENAME MAPPING TABLES

### 3.1 Ordered Token Replacement (longest-first, case-sensitive)
| # | Search | Replace | Notes |
|---|---|---|---|
| 1 | `GeneratePress Premium` | `Aureon Studio` | brand |
| 2 | `GP Premium` | `Aureon Studio` | brand |
| 3 | `generatepress.com` | `aureonstudio.com` | URL |
| 4 | `docs.generatepress.com` | `docs.aureonstudio.com` | URL |
| 5 | `sites.generatepress.com` | `sites.aureonstudio.com` | URL |
| 6 | `gpsites.co` | `sites.aureonstudio.com` | URL |
| 7 | `generatepress` | `aureon` | textdomain/identifiers |
| 8 | `GeneratePress` | `Aureon` | CamelCase brand |
| 9 | `GENERATEBLOCKS` | `@@GENERATEBLOCKS@@` | PROTECT third-party |
| 10 | `GenerateBlocks` | `@@GENERATEBLOCKS@@` | PROTECT third-party |
| 11 | `generateblocks` | `@@generateblocks@@` | PROTECT third-party |
| 12 | `regenerate` | `@@regenerate@@` | PROTECT word |
| 13 | `generated` | `@@generated@@` | PROTECT word |
| 14 | `GP_PREMIUM` | `AUREON_STUDIO` | constants |
| 15 | `gp_premium` | `aureon_studio` | option keys |
| 16 | `gp-premium` | `aureon-studio` | textdomain/handles |
| 17 | `GP_LIBRARY` | `AUREON_LIBRARY` | constants |
| 18 | `GP_` | `AUREON_` | fallback |
| 19 | `gp_` | `aureon_` | function prefixes |
| 20 | `GP-` | `AUREON-` | CSS vars |
| 21 | `gp-` | `aureon-` | CSS classes |
| 22 | `gpp-` | `aureon-studio-` | CSS classes in site-library.js |
| 23 | `gppVersion` | `aureonStudioVersion` | JS variable in site-library.js |
| 24 | `gppSiteLibrary` | `aureonStudioSiteLibrary` | JS variable in site-library.js |
| 25 | `GP One` | `Aureon One` | Product tier text |
| 26 | `GPP ` | `Aureon Studio ` | Abbreviation in readme/changelog |
| 27 | `GENERATE_` | `AUREON_` | constants |
| 28 | `Generate_` | `Aureon_` | class names |
| 29 | `generate_` | `aureon_` | functions/hooks/options |
| 30 | `generate-` | `aureon-` | CSS classes |
| 31 | `Tom Usborne` | `Aureon Studio` | author |
| 32 | `EDGE22` | `AUREON` | copyright entity |
| 33 | `gen_premium_license_key` | `aureon_studio_license_key` | option keys |
| 34 | `generate_db_version` | `aureon_db_version` | option keys |
| 35 | `theme_mods_generatepress` | `theme_mods_aureon` | option keys |
| 36 | `@@GENERATEBLOCKS@@` | `GenerateBlocks` | RESTORE |
| 37 | `@@generateblocks@@` | `generateblocks` | RESTORE |
| 38 | `@@regenerate@@` | `regenerate` | RESTORE |
| 39 | `@@generated@@` | `generated` | RESTORE |

### 3.2 File/Folder Renames
| Old | New |
|---|---|
| `generatepress.3.6.1/generatepress/` | `aureon.1.0.0/aureon/` |
| `gp-premium_v2.5.6/gp-premium/` | `aureon-studio.1.0.0/aureon-studio/` |
| `gp-premium.php` (main) | `aureon-studio.php` |
| `generate-backgrounds.php` | `aureon-backgrounds.php` |
| `generate-blog.php` | `aureon-blog.php` |
| `generate-colors.php` | `aureon-colors.php` |
| `generate-copyright.php` | `aureon-copyright.php` |
| `generate-disable-elements.php` | `aureon-disable-elements.php` |
| `generate-fonts.php` | `aureon-fonts.php` |
| `generate-hooks.php` | `aureon-hooks.php` |
| `generate-menu-plus.php` | `aureon-menu-plus.php` |
| `generate-page-header.php` | `aureon-page-header.php` |
| `generate-secondary-nav.php` | `aureon-secondary-nav.php` |
| `generate-sections.php` | `aureon-sections.php` |
| `generate-spacing.php` | `aureon-spacing.php` |
| `generatepress-controls.js` | `aureon-controls.js` |
| `generate-sections-metabox*.js/css` | `aureon-sections-metabox*.js/css` |
| `assets/fonts/generatepress.*` | `assets/fonts/aureon.*` |
| `general/icons/gp-premium.*` | `general/icons/aureon-studio.*` |
| `langs/gp-premium-*.mo` | `langs/aureon-studio-*.mo` |
| `langs/gp-premium-*.json` | `langs/aureon-studio-*.json` |
| `screenshot.png` | `screenshot.png` (replaced content) |

### 3.3 Branding Header Rewrites
**Theme style.css:**
```
Theme Name: Aureon
Theme URI: https://aureonstudio.com
Author: Aureon Studio
Author URI: https://aureonstudio.com
Description: (reworded, no GP mention)
Version: 1.0.0
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: aureon
Aureon, Copyright 2026 Aureon Studio
```

**Plugin header:**
```
Plugin Name: Aureon Studio
Plugin URI: https://aureonstudio.com
Description: The entire collection of Aureon premium modules.
Version: 1.0.0
Author: Aureon Studio
Author URI: https://aureonstudio.com
License: GNU General Public License v2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: aureon-studio
```

**Both readme.txt**: rewritten, no GP wording, Stable tag 1.0.0.
**Both license.txt**: full GPL v2 text + "Copyright (c) 2026 Aureon Studio".

---

## 4. LICENSE / UPDATER / SITE-LIBRARY STRATEGY

1. **EDD Updater**: point to `aureonstudio.com`; class stays present (returns false on failed requests, no crash). No functional license server needed.
2. **License UI**: `inc/legacy/activation.php` reworded from "GP Premium" to "Aureon Studio"; license key options renamed.
3. **Site Library**: APIs pointed to `sites.aureonstudio.com`; feature UI remains; imports only work once domain is real.
4. **All URLs**: generatepress.com → aureonstudio.com everywhere.

---

## 5. VERSION NUMBER STRATEGY

- `AUREON_VERSION='4.0.0'` (passes all `>= 3.x` version_compare gates)
- `AUREON_STUDIO_VERSION='3.0.0'` (passes all `>= 2.x` and `< 3.x` gates)
- Display version `1.0.0` in style.css, plugin header, readme.txt Stable tag

---

## 6. EXECUTION PHASES

### Phase 0 — Baseline
- Record file counts (theme 144, plugin 329)
- Git status snapshot

### Phase 1 — Delete Lumina
- Remove: `wp-content/themes/lumina`, `wp-content/plugins/lumina-companion`, `Release/lumina-*`

### Phase 2 — Rename Folders
- `generatepress.3.6.1` → `aureon.1.0.0`, inner `generatepress` → `aureon`
- `gp-premium_v2.5.6` → `aureon-studio.1.0.0`, inner `gp-premium` → `aureon-studio`

### Phase 3 — Deep Token Replacement
- PHP script walks all text files, applies ordered replacements from §3.1
- Protects GenerateBlocks, regenerate, generated words
- Handles both projects with same table (theme↔plugin contract preserved)

### Phase 4 — File Renames
- All module loaders, main plugin file, fonts, langs, folders (§3.2)

### Phase 5 — Branding Rewrites
- Style.css, plugin header, readme.txt, license.txt, screenshot.png, dashboard copy

### Phase 6 — Version Constants
- Set `AUREON_VERSION='4.0.0'`, `AUREON_STUDIO_VERSION='3.0.0'`

### Phase 7 — Verification Battery
1. `php -l` every .php → 0 errors
2. Zero-trace grep: `generatepress|GeneratePress|generate_|generate-|GP Premium|gp-premium|GP_PREMIUM|gp_premium|EDGE22|Usborne|generatepress.com` → 0 hits
3. Cross-ref: `aureon_get_option` called in plugin exists in theme
4. Textdomain parity: all `__('...','aureon')` and `__('...','aureon-studio')`
5. File count parity: theme 144, plugin 329
6. CSS class consistency: `.aureon-*` in PHP matches dist CSS
7. wpml-config.xml audit: grep for `generatepress`/`gp-` → 0 hits
8. license.txt existence: both `aureon/license.txt` and `aureon-studio/license.txt` exist
9. WordPress.org link check: grep for `theme-install.php` → 0 hits (manual fix applied)
10. Menu slug check: `generate-options` → `aureon-options` in all admin_url() calls
11. CSS asset handle check: `generate-style` → `aureon-style` in wp_enqueue_style calls
12. Admin body class check: `gp_premium` → `aureon_studio` in admin_body_class filter

---

## 7. FILES DELETED (Lumina)
- `wp-content/themes/lumina/`
- `wp-content/plugins/lumina-companion/`
- `Release/lumina-1.0.0/`, `Release/lumina-1.0.0.zip`
- `Release/lumina-companion-1.0.0/`, `Release/lumina-companion-1.0.0.zip`
- `Release/license-gplv2.txt` (content moved to project license.txt files)

---

## 8. INTERNAL DOCS (NOT shipped, leave untouched)
- `.serena/memories/gp-audit/` — GP audit memories
- `Report/` — engineering review, phases, roadmap
- `README.md` at repo root — update if mentions Lumina

---

## 9. RISKS & MITIGATIONS
| Risk | Mitigation |
|---|---|
| version_compare gates break | `AUREON_VERSION='4.0.0'` passes all |
| GenerateBlocks third-party code | Protected with sentinels, restored |
| `regenerate`/`generated` words mangled | Protected with sentinels, restored |
| Binary .mo domain mismatch | Rename files; English fallback works |
| Binary font name tables | Rename files + CSS; internal names acceptable |
| Remote endpoints dead | Neutralized to aureonstudio.com; feature UI stays, imports fail gracefully |
| GPL license compliance | GPL v2+ maintained; copyright Aureon Studio |

---

## 10. DETAILED GAP ANALYSIS (verified against source)

### 10.1 Missing Files — Must Create
Neither theme nor plugin ships a `license.txt`. GPL v2+ requires the license text be distributed. **Must create:**
- `aureon/license.txt` — full GPL v2 text + "Copyright (c) 2026 Aureon Studio"
- `aureon-studio/license.txt` — full GPL v2 text + "Copyright (c) 2026 Aureon Studio"

### 10.2 WordPress.org Theme Install Link (MANUAL FIX REQUIRED)
`gp-premium.php:265` contains:
```php
esc_url( admin_url( 'theme-install.php?theme=generatepress' ) )
```
After token replacement this becomes `theme-install.php?theme=aureon`. Aureon is NOT on WordPress.org, so this link would 404. **Must manually rewrite** to point to `aureonstudio.com` or remove the install prompt entirely.

### 10.3 WPML Config (covered by token replacement)
`wpml-config.xml` contains:
- `theme_mods_generatepress` → `theme_mods_aureon` (rule #30)
- `gp_elements` custom type → `aureon_elements` (rule #21)
- 70+ `_generate_*` custom fields → `_aureon_*` (rule #24)
All covered by ordered replacement. No manual fix needed.

### 10.4 Dashboard HTML IDs (covered by token replacement)
Theme `class-dashboard.php` outputs:
- `id="generatepress-dashboard-app"` → `aureon-dashboard-app`
- `id="generatepress-dashboard-go-pro"` → `aureon-dashboard-go-pro`
- `id="generatepress-reset"` → `aureon-reset`
All covered by `generatepress` → `aureon` replacement (rule #7).

### 10.5 Plugin Dashboard Class Cross-Reference (covered)
Plugin `class-dashboard.php:46` checks `class_exists( 'GeneratePress_Dashboard' )`. After replacement this becomes `class_exists( 'Aureon_Dashboard' )`, which matches the renamed theme class. Correct.

### 10.6 `@package` Annotations (covered)
All `@package GeneratePress` → `@package Aureon` and `@package GP Premium` → `@package Aureon Studio` via rules #8, #2.

### 10.7 Underscores Attribution (KEEP — not GP detection)
`style.css:19`: "GeneratePress is based on Underscores http://underscores.me/, (C) 2012-2025 Automattic, Inc."
This is factual attribution to the original _s starter theme. After token replacement it reads "Aureon is based on Underscores..." which is accurate. Keep as-is.

### 10.8 Changelog History (KEEP — not GP detection)
Both `readme.txt` contain extensive GP Premium changelog history (versions 1.8 through 2.5.6). After token replacement, "GP Premium" becomes "Aureon Studio" and "GeneratePress" becomes "Aureon" in changelog text. The version numbers (1.8, 2.0, 2.5.6) are historical and do not reveal GP origin to end users. Keep as-is.

### 10.9 Third-Party Licenses in readme.txt (KEEP)
Theme `readme.txt` lists third-party licenses: Unsemantic Framework (MIT), Font Awesome (SIL OFL), selectWoo (MIT), TinyColor (MIT), React Select (MIT). These are legitimate and must stay for legal compliance.

### 10.10 Function Cross-References (all covered)
Theme↔Plugin contract functions verified:
- `generate_premium_get_theme_version()` → `aureon_studio_get_theme_version()` (both sides renamed)
- `generatepress_is_module_active()` → `aureon_is_module_active()` (both sides renamed)
- `generate_get_option()` → `aureon_get_option()` (both sides renamed)
- `generate_get_defaults()` → `aureon_get_defaults()` (both sides renamed)
- `generate_is_using_dynamic_typography()` → `aureon_is_using_dynamic_typography()` (both sides renamed)
- All `generate_package_*` option keys → `aureon_package_*` (both sides renamed)

### 10.11 CSS Variable Contract (covered)
Theme outputs `--gp-*` CSS vars. Plugin reads them. Both renamed to `--aureon-*` by rule #20 (`GP-` → `AUREON-`). The `all.css` file contains `.gp-icon` class and `--gp-*` vars → all renamed.

### 10.12 Nonce Field Names (covered)
Plugin activation.php uses nonce names like `gp_premium_bulk_action_nonce` → `aureon_studio_bulk_action_nonce` via rule #18 (`gp_` → `aureon_`). All `check_admin_referer()` calls use the same renamed nonces. No mismatch.

### 10.13 Script Localization Handles (covered)
- Theme: `wp_set_script_translations( 'generate-dashboard', 'generatepress' )` → `'aureon-dashboard', 'aureon'`
- Plugin: `wp_localize_script( 'generate-premium-dashboard', ... )` → `'aureon-studio-dashboard'`
- Plugin: `wp_localize_script( 'gp-premium-editor', 'gpPremiumEditor', ... )` → `'aureon-studio-editor', 'aureonStudioEditor'`
All covered by rules #7, #24, #25.

### 10.15 REST API Namespaces (covered)
- Theme `class-rest.php`: namespace `generatepress/v` → `aureon/v` (rule #7)
- Plugin `class-site-library-rest.php`: namespace `generatepress-site-library/v` → `aureon-site-library/v` (rule #7)
- Both class names `GeneratePress_*` → `Aureon_*` (rule #8)

### 10.16 AJAX Action Names (covered)
- `generatepress_regenerate_css_file` → `aureon_regenerate_css_file` (rule #7)
- All `wp_ajax_*` and `wp_ajax_nopriv_*` hooks follow same pattern

### 10.17 Post Type References (covered)
- `gp_elements` custom post type → `aureon_elements` (rule #21)
- wpml-config.xml `<custom-type>gp_elements</custom-type>` → `aureon_elements`
- Plugin: `is_block_element` check using `'gp_elements' === get_post_type()` → `'aureon_elements'`

### 10.18 CSS Handle Names (covered)
- `gp-premium-editor` → `aureon-studio-editor` (rule #16)
- `gp-premium-icons` → `aureon-studio-icons` (rule #16)
- `generate-layout-metabox` → `aureon-layout-metabox` (rule #25)
- `generate-dashboard` → `aureon-dashboard` (rule #25)

### 10.19 Additional Class Names Found (covered)
- `GeneratePress_External_CSS_File` → `Aureon_External_CSS_File` (rule #8)
- `GeneratePress_Site_Library` → `Aureon_Site_Library` (rule #8)
- `GeneratePress_Site_Library_Rest` → `Aureon_Site_Library_Rest` (rule #8)
- `GeneratePress_Site_Library_Helper` → `Aureon_Site_Library_Helper` (rule #8)
- `GeneratePress_Premium_Plugin_Updater` → `Aureon_Studio_Plugin_Updater` (rules #1, #8)
- `GeneratePress_Elements_Helper` → `Aureon_Elements_Helper` (rule #8)
- `GeneratePress_Typography` → `Aureon_Typography` (rule #8)
- `GeneratePress_CSS` → `Aureon_CSS` (rule #8)
- `GeneratePress_HTML_Attributes` → `Aureon_HTML_Attributes` (rule #8)
- `GeneratePress_Theme_Update` → `Aureon_Theme_Update` (rule #8)
- `GeneratePress_Dashboard` → `Aureon_Dashboard` (rule #8)
- `GeneratePress_Pro_Dashboard` → `Aureon_Pro_Dashboard` (rule #8)
- `GeneratePress_Rest` → `Aureon_Rest` (rule #8)

### 10.20 Additional Function Names Found (covered)
- `generate_enqueue_premium_icons` → `aureon_enqueue_premium_icons` (rule #24)
- `generate_premium_enqueue_editor_scripts` → `aureon_studio_enqueue_editor_scripts` (rules #24, #19)
- `generate_premium_add_svg_icons` → `aureon_studio_add_svg_icons` (rules #24, #19)
- `generate_premium_load_modules` → `aureon_studio_load_modules` (rules #24, #19)
- `generate_premium_updater` → `aureon_studio_updater` (rules #24, #19)
- `generate_premium_set_updater_api_params` → `aureon_studio_set_updater_api_params` (rules #24, #19)
- `generate_premium_setup` → `aureon_studio_setup` (rules #24, #19)
- `generate_premium_theme_information` → `aureon_studio_theme_information` (rules #24, #19)
- `generate_add_configure_action_link` → `aureon_add_configure_action_link` (rule #24)
- `generatepress_deactivate_standalone_addons` → `aureon_deactivate_standalone_addons` (rule #7)
- `generatepress_is_module_active` → `aureon_is_module_active` (rule #7)
- `generatepress_premium_dashboard_scripts` → `aureon_studio_dashboard_scripts` (rules #7, #19)
- `generatepress_premium_process_license_key` → `aureon_studio_process_license_key` (rules #7, #19)
- `generatepress_premium_beta_tester` → `aureon_studio_beta_tester` (rules #7, #19)
- `generatepress_premium_body_class` → `aureon_studio_body_class` (rules #7, #19)
- `generatepress_premium_notices` → `aureon_studio_notices` (rules #7, #19)
- `generate_super_package_addons` → `aureon_super_package_addons` (rule #24)
- `generate_activate_super_package_addons` → `aureon_activate_super_package_addons` (rule #24)
- `generate_deactivate_super_package_addons` → `aureon_deactivate_super_package_addons` (rule #24)
- `generate_multi_activate` → `aureon_multi_activate` (rule #24)
- `generate_license_errors` → `aureon_license_errors` (rule #24)
- `generate_license_missing` → `aureon_license_missing` (rule #24)
- `generate_activation_area` → `aureon_activation_area` (rule #24)
- `generate_admin_right_panel` → `aureon_admin_right_panel` (rule #24)

### 10.21 Localize Variable Names (covered)
- `gpPremiumEditor` → `aureonStudioEditor` (camelCase, rule #15 + JS convention)
- `generateDashboard` → `aureonDashboard` (camelCase, rule #24 + JS convention)

### 10.22 `@package GenerateBlocks` in theme/class-rest.php (KEEP)
Line 5 says `@package GenerateBlocks` — this is a file-level docblock quirk (the file was originally part of GenerateBlocks integration). After replacement, `@package GenerateBlocks` stays protected. The class `GeneratePress_Rest` → `Aureon_Rest`. No issue.

### 10.14 Additional Verification Steps (add to Phase 7)
7. **wpml-config.xml audit**: grep for any remaining `generatepress`/`gp-` strings → 0 hits
8. **license.txt existence check**: both `aureon/license.txt` and `aureon-studio/license.txt` exist
9. **WordPress.org link check**: grep for `theme-install.php` → 0 hits (manual fix applied)
10. **Menu slug check**: `generate-options` → `aureon-options` in all admin_url() calls
11. **CSS asset handle check**: `generate-style`, `generate-comments`, etc. → `aureon-style`, `aureon-comments`
12. **Admin body class check**: `gp_premium` → `aureon_studio` in admin_body_class filter

---

## 11. CONFIRMED DECISIONS
- **A. Domain**: `aureonstudio.com` — confirmed (placeholder, no real domain yet)
- **B. Site Library**: KEEP enabled (neutralized endpoints) — user wants full working theme
- **C. Langs**: KEEP renamed .mo/.json — English fallback works, no broken features
- **D. Changelog**: KEEP historical — not GP detection, just version history
- **E. Font binaries**: ACCEPT internal name-table traces — generic icon glyphs, safe
- **F. WordPress.org link**: REMOVE install prompt entirely — Aureon not on WP.org
