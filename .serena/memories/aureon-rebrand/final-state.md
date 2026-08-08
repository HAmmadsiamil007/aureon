# Aureon Rebrand — Final State (v1.0.0) — UPDATED 2026-08-05

## Status: COMPLETE + VERIFIED (detection SOLVED)

## Completed
- Full rebrand of GeneratePress 3.6.1 + GP Premium 2.5.6 → Aureon theme + Aureon Studio plugin
- **Version constants (DISK TRUTH):** `AUREON_VERSION='3.6.1'`, `AUREON_STUDIO_VERSION='3.0.0'`, display 1.0.0
  - ⚠️ Earlier records said 4.0.0 — that was corrected to 3.6.1. Do not raise/lower.
- All GP fingerprints removed:
  - JS globals → `aureonTypography`, `aureonCustomizerControls`, `aureonBlog`, `aureonProDashboard`, `aureonSecondaryNav`, `aureonWooCommerce`, `aureonGlobalColors`, `aureonQuantityButtons`
  - Plugin React global → `aureonProCustomizerControls` (collision fix)
  - Class `GenerateLabelControl` → `AureonLabelControl`
  - Files `generate-sections-metabox.*` → `aureon-sections-metabox.*` (also fixes broken Sections editor)
  - GP→Aureon comment cleanup (18 plugin files, incl. user-facing "Aureon Hooks")
- **Customizer collision fixes (the big ones):**
  - Handle: theme `aureon-customizer-controls-react` / plugin `aureon-pro-customizer-controls-react`
  - Global: theme `aureonCustomizerControls` / plugin `aureonProCustomizerControls`
- i18n: 22 .mo rebuilt (28-byte header writer, round-trip verified, 267 brand strings removed); 6 .json JS-translations cleaned
- License files: GPL v2+ with Aureon Studio copyright + **upstream GeneratePress/EDGE22 attribution** (legal requirement, intentional)
- readmes: Contributors Aureon Studio, Stable tag 1.0.0; style.css URIs → aureonstudio.com

## Verification (all pass)
- `php -l`: 0 errors all files | `node --check`: 0 errors all non-min JS
- Brand scan: 0 hits outside license.txt (camelCase + literal + filenames)
- Live Docker (phantom-wp:8080): customizer 0 console errors, React panels render, plugin groups inject

## Sentinels (DO NOT RENAME)
GenerateBlocks/generateblocks (519 refs), regenerate/generated/generates (English), gpDynamicTextType/gpDynamicDisplayType (DB schema)

## Open (non-GP, pre-existing)
Site Library `https://example.com/invalid`, legacy activation `https://example.com` — dead code, documented in aureon-doc/STATUS.md

## Git (UPDATED 2026-08-06)
- Remote: https://github.com/HAmmadsiamil007/aureon.git | Branch: main
- Latest commit: 3e5741a — "feat(aureon): complete hardening — security, SEO, newsletter DB, performance" (contains the FIRST AETHER integration attempt + PHASE-17-1 docs — now rolled back, see below)

## Phase 17 — Frontend Integration Framework (UPDATED 2026-08-07)
- The AETHER direct-integration from 3e5741a was **deliberately rolled back** (working tree, UNCOMMITTED), then rebuilt properly as a component framework.
- **Status now: IMPLEMENTED + LIVE (2026-08-07).** Stages 0–12 complete: 17 routes live with 0 console errors, M2 component audit (52), hardening layer live (security/SEO/newsletter/performance), self-hosted fonts, wishlist + quick view working. Root-cause font bug (`esc_attr()` → `&#039;` in tokens) fixed along the way.
- **#1 blocker: all work since HEAD `3e5741a` (main, +1 unpushed) is UNCOMMITTED** — normalize + commit + push. See `mem:aureon-rebrand/current-snapshot` (authoritative done/remaining) + `mem:aureon-rebrand/phase17-stage12-hardening`.
- Full detail: `mem:aureon-rebrand/phase17-frontend-framework`
