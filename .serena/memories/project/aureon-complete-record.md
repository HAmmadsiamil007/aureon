# Aureon — Complete Project Record (UPDATED 2026-08-07)

## Repository
- **Remote:** https://github.com/HAmmadsiamil007/aureon.git
- **Branch:** main (NOT master)
- **Latest commit:** 3e5741a — "feat(aureon): complete hardening — security, SEO, newsletter DB, performance" (2026-08-06). **Still 1 commit ahead of origin, NOT pushed.**
- **Status (2026-08-07, verified):** AETHER frontend integration IMPLEMENTED + LIVE (Stages 2–12 complete, hardening verified, wishlist/quick-view/fonts live). **All work since 3e5741a is UNCOMMITTED** — 260 working-tree entries: 200 deletions (legacy `assets/aether/*` removed by stage-12 cleanup), 23 modifications, 37 untracked (`frontend/`, `mu-plugins/`, `.playwright-mcp/`, `.serena/` docs). **#1 handoff blocker: normalize + commit + push.**
- Live: Docker container `aureon_wp` @ http://localhost:8080, DB `aureon_db`, admin/admin123, WC 11.0.0, WP 6.9.1. Theme+plugin bind-mounted (edits live); `frontend/` deployed into container volume `/var/www/html/wp-content/frontend/` (tar.gz pipeline, never zip).

## Phase 17 — Frontend Integration Framework (current work)
- **What:** proper component-framework integration of the AETHER template (void #09090B + gold #C8956C) into Aureon. Replaces the rolled-back direct conversion from 3e5741a.
- **Status (2026-08-07): IMPLEMENTED + LIVE.** Stages 0–12 complete: engine renders shell/home/shop/product/cart/checkout/account/blog/static (17 routes, 0 console errors), M2 component library audit done (52 components, API headers), Stage-12 hardening layer live (security/SEO/newsletter DB/performance), fonts self-hosted + verified, wishlist + quick view working live. See `mem:aureon-rebrand/current-snapshot` + `mem:aureon-rebrand/phase17-stage12-hardening`.
- **Architecture (in production):** WordPress → WC → Modules → Adapters → ViewModels → Renderer → Components. Components never call WP/WC (data-dumb, receive `$componentData`). Tokens as CSS custom props from Customizer options. GSAP 3.12.5/Lenis 1.1.18 locally pinned; Cabinet Grotesk + Satoshi self-hosted.
- **Known next (post-RC roadmap):** M3 token-design system → M4 Customizer → M5 integration engine extension → M6 demo import → M8/M9 builder — see `mem:aureon-rebrand/current-snapshot` REMAINING.
- **Full detail:** `mem:aureon-rebrand/phase17-frontend-framework`

## What Was Built
A complete rebrand of GeneratePress 3.6.1 + GP Premium 2.5.6 into:
- **Aureon Theme** (v1.0.0) — WordPress theme
- **Aureon Studio Plugin** (v1.0.0) — Premium plugin with 17 modules

## Version Strategy (DISK TRUTH — do not trust old memories saying 4.0.0)
- `AUREON_VERSION = '3.6.1'` (theme functions.php — keep at 3.6.1, passes version gates)
- `AUREON_STUDIO_VERSION = '3.0.0'` (plugin aureon-studio.php)
- Display headers show `1.0.0`
- Text domain: theme `aureon`, plugin `aureon-studio`
- **Never lower the constants; they gate features.**

## Folder Structure
```
wordpress/
├── aureon/
│   ├── theme/          (145 files)
│   └── plugin/         (331 files)
├── generatepress/      (gitignored — original GP files, upstream reference)
├── aureon-doc/         (README/THEME/PLUGIN/CHANGELOG/STATUS docs — authoritative)
├── Report/             (audit reports incl. DETECTION.md — RESOLVED addendum)
├── AUREON_REBRAND_PLAN.md
└── .serena/memories/
```

## Detection Status: SOLVED (2026-08-05)
- 0 hits for `generatepress|gp premium|edge22|tom usborne` outside license.txt (intentional GPL attribution)
- 0 camelCase `generate[A-Z]` tokens; 0 `generate-*`/`gp-*` filenames
- All JS globals renamed: `aureonTypography`, `aureonCustomizerControls`, `aureonBlog`, `aureonProDashboard`, `aureonSecondaryNav`, `aureonWooCommerce`, `aureonGlobalColors`, `aureonQuantityButtons`, `aureonProCustomizerControls`, `AureonLabelControl` (class)
- GP→Aureon comment cleanup in 18 plugin files ("GP Hooks"→"Aureon Hooks")
- i18n: 22 .mo rebuilt (28-byte MO header, 267 brand entries removed); 6 .json JS-translation files cleaned

## Customizer Collision Fixes (critical knowledge)
1. **Handle collision:** theme handle `aureon-customizer-controls-react`, plugin handle `aureon-pro-customizer-controls-react` — must stay distinct
2. **Global collision:** theme owns `aureonCustomizerControls`; plugin owns `aureonProCustomizerControls` — must NOT share names
3. Harmless shared globals (identical localize both sides): `aureon_customize`, `aureonTypography`, `typography_defaults`
4. Symptom of regression: `Cannot read properties of undefined (reading 'length')` at customizer.js, blank React panels, no save button

## Sentinel Words (DO NOT RENAME)
- GenerateBlocks, generateblocks (519 refs — live third-party integration)
- regenerate / generated / generates (English words)
- gpDynamicTextType, gpDynamicDisplayType (DB schema)

## Key Files
- `aureon/theme/style.css` — Theme Name: Aureon, Version: 1.0.0
- `aureon/plugin/aureon-studio.php` — AUREON_STUDIO_VERSION='3.0.0'
- `aureon/theme/functions.php` — AUREON_VERSION='3.6.1'
- `aureon/plugin/library/customizer-helpers.php` — localizes `aureonProCustomizerControls`
- `aureon/theme/inc/customizer/helpers.php` — localizes `aureonCustomizerControls` on react handle
- Both `license.txt` — MUST keep upstream GeneratePress/EDGE22 attribution (GPL requirement)

## Open Items (pre-existing, non-GP)
- Site Library API `https://example.com/invalid` — feature dead until real endpoint
- Legacy activation `https://example.com` — dead code
- EDD updater → aureonstudio.com (works when license server exists)

## Verification Commands
- `php -l` on all PHP: 0 errors
- `node --check` on all non-min JS: 0 errors
- Brand scan: `grep -rni 'generatepress\|gp premium\|edge22\|tom usborne' aureon/theme aureon/plugin | grep -v langs` → only license.txt
- CamelCase: `grep -rEn 'generate(Press|Customizer|Blog|ProDashboard|SecondaryNav|WooCommerce|GlobalColors|LabelControl|QuantityButtons|Typography)' aureon | grep -v langs` → nothing
