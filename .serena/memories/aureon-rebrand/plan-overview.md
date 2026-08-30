# AUREON REBRAND — Master Plan Overview

## Project
- **Source:** GeneratePress 3.6.1 theme + GP Premium 2.5.6 plugin
- **Output:** Aureon theme + Aureon Studio plugin
- **Location:** `C:\Users\hamma\Downloads\wordpress`
- **Plan file:** `C:\Users\hamma\Downloads\wordpress\AUREON_REBRAND_PLAN.md`
- **Todo file:** Managed via todowrite in session

## Goal
100% functional rebrand, zero GP fingerprints, zero detection risk, clean legal posture (GPL v2+), sellable to clients.

## Key Decisions (CONFIRMED)
- **Domain:** `aureonstudio.com` (placeholder)
- **Site Library:** Keep enabled (neutralized endpoints)
- **Langs:** Keep renamed .mo/.json (English fallback works)
- **Changelog:** Keep historical (not GP detection)
- **Fonts:** Accept internal name-table traces (generic, safe)
- **WP.org link:** Remove entirely (not on WP.org)

## Version Strategy (CRITICAL)
- `AUREON_VERSION = '4.0.0'` (passes all `>= 3.1.0` version gates)
- `AUREON_STUDIO_VERSION = '3.0.0'`
- Display headers: `1.0.0`

## Ordered Replacement Rules (39 rules)
File: `AUREON_REBRAND_PLAN.md` §3.1
- Rules #1-#6: Brand names + URLs
- Rules #7-#13: Core identifiers + sentinels
- Rules #14-#21: GP/GPP prefixes
- Rules #22-#26: NEW gpp/GP One/GPP (gpp-, gppVersion, gppSiteLibrary, GP One, GPP)
- Rules #27-#35: Constants, functions, author, options
- Rules #36-#39: RESTORE sentinels

## Hazard Protection
- **GenerateBlocks:** Sentinel-protected (`@@GENERATEBLOCKS@@`), restored after
- **regenerate/generated:** Sentinel-protected (`@@regenerate@@`/`@@generated@@`), restored after
- **Binary files:** Font filenames renamed, content untouched; .mo files renamed, binary untouched
- **Version gates:** `AUREON_VERSION='4.0.0'` ensures `version_compare(..., '>= 3.1.0')` returns TRUE

## Remote Endpoints (5 neutralized)
1. `sites.generatepress.com` → `sites.aureonstudio.com`
2. `gpsites.co` → `sites.aureonstudio.com`
3. `generatepress.com` (EDD updater) → `aureonstudio.com`
4. `docs.generatepress.com` → `docs.aureonstudio.com`
5. Dashboard links → `aureonstudio.com`

## 17 Modules (all preserved)
backgrounds, blog, colors, copyright, disable-elements, elements, font-library, general, hooks, menu-plus, page-header, secondary-nav, sections, site-library, spacing, typography, woocommerce

## Execution Phases (12)
1. Preparation — build workspace, copy sources
2. Sentinel Protection — shield GenerateBlocks + regenerate/generated
3. Ordered Token Replacement — 39 rules
4. Branding Headers — style.css, plugin header, readme.txt
5. Version Constants — AUREON_VERSION=4.0.0, AUREON_STUDIO_VERSION=3.0.0
6. File/Folder Renames — directories, files, translations, fonts
7. Remote Endpoints — neutralize all GP URLs
8. License/Legal — create license.txt, update copyright
9. WPML Config — update wpml-config.xml
10. Screenshot — replace with Aureon branding
11. Verification — full audit (grep, PHP syntax, file counts)
12. Package — create distributable ZIPs

## Detection Vectors Covered
- PHP: All class names, functions, constants, options, textdomains
- CSS: All variables, classes, handles
- JS: All variables, REST namespaces, AJAX handlers, script handles
- XML: wpml-config.xml options, meta fields, CPTs
- Text: readme.txt, style.css, descriptions, copyright
- URLs: All 5 remote endpoints
- Files: Directories, module files, fonts, translations, icons
