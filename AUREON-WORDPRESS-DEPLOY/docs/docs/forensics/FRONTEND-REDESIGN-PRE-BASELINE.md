# FRONTEND REDESIGN — PRE-BASELINE

**Date:** 2026-09-04 · Phase: complete client frontend redesign (visual/UI/UX only)
**Status:** `VINETA_FRONTEND_REDESIGN_BLOCKED` — baseline captured; code stages not started; runtime verification pending (Docker down).
**Rule honored:** *Do not change anything before the baseline exists.* No source file was modified to produce this document.

---

## 1. CURRENT IMPLEMENTATION STATE (recorded 2026-09-04)

| Item | Value |
|---|---|
| Git branch | `master` |
| Current commit | `9315121` (2026-09-04 12:49:36 +0500, "complete Stage B runtime revalidation") |
| Git status | 36 M / 540 D / 174 ?? (collapsed) vs HEAD — **intentional**: the tested build lives in the working tree, not in the commit (see §3) |
| Canonical tree | `aureon/` (theme + frontend + plugin + ferm-page.php override) = the Stage-B **tested** tree; unchanged since 2026-09-04 12:45 |
| Tested build identity | `RELEASE_IDENTITY_CONFIRMED` (canonical) — `docs/forensics/TESTED-BUILD-IDENTITY.md` |
| Release candidate | `RELEASE-CANDIDATE-MANIFEST.json` — 1,084 payload files, 32,747,379 B, per-file SHA-256 |
| Deploy mirror | `AUREON-WORDPRESS-DEPLOY` — verified 771/771 SHA-256 vs canonical (2026-09-04) |
| Golden Copy | `AUREON-GOLDEN-COPY` — **FROZEN BASELINE, untouched** |
| Active design | `vineta` (default fallback in `frontend/views/design.php`) |
| Active client pack | `aureon/frontend/designs/vineta/` — 444 files, 46 HTML templates |
| Current manifest | `aureon/frontend/designs/vineta/manifest.json` (page-gated asset system) |
| Current bridge | `composer.php` (emits `VinetaPageData`) · `js/vineta-data-shims.js` (consumers) · `js/vineta-path-bridge.js` (routing) |
| Customizer | `aether_*` options → CSS vars/`VinetaPageData` (colors/fonts/logo/favicon/hero/announcement/newsletter/search placeholder/layout), round-trip proven (SET→SAVE→RELOAD→RESET) |
| Menus | WP `primary` + `footer` menus; mobile menu built client-side from the desktop menu DOM |
| WooCommerce | Real sessions/cart/badge/auth/account/checkout; currency CHF via WC symbol+position (`vFmt`); 0 variable products in current catalog (variation UI implemented, untested vs real data) |
| Plugin list | AUREON companion plugin (`aureon/plugin/`, 317 files, 0 working-tree changes) + WooCommerce + WordPress core (Docker) |
| Route map | `/`(index) `/shop/`(shop-default) `/product/*`(product-detail) `/product-category/*` search cart checkout my-account blog blog-single static pages 404 |
| Runtime | Local Docker `localhost:8080` — **NOT RUNNING at baseline capture** (verification gates deferred) |

## 2. APPROVED VISUAL REFERENCE (read-only, outside repo)

| Item | Value |
|---|---|
| Path | `C:\Users\hamma\Downloads\vinetahtml-10\vinetahtml-10\vineta-package\vineta-ready-for-wordpress` |
| Package | Vineta HTML **v1.0.3** (master package) |
| Contents | 108 HTML pages · 10 CSS · 23 JS · 6 fonts · docs/ (97 files) · index.html |
| Acceptance | `PREMIUM-VINETA-FRONTEND-ACCEPTANCE-MATRIX.json` → `VINETA_PREMIUM_TEMPLATE_100_PASS` (2026-09-02): 122 slots, 90 hooked, 0 blocked, 0 broken images |
| Provenance | Current canonical pack (46 templates) = **strict subset** of this reference (46/46 names match). Repo-local `vineta-primary-only/` (09-02 snapshot) = same 46-template subset. This reference is the master from which the current pack was curated. |

## 3. REDESIGN OPERATING CONTRACT

```
VISUAL SOURCE (read-only)   C:\Users\hamma\Downloads\...\vineta-ready-for-wordpress  (108 pages)
FUNCTIONAL TRUTH            CURRENT TESTED CANONICAL aureon/  (bridge + WP + WC + Customizer + menus + auth + cart + checkout + plugins + routes)
GOLDEN COPY                 AUREON-GOLDEN-COPY  🔒 frozen — do NOT overwrite
```

Layer ownership for this redesign:

| Layer | Path | Allowed to change |
|---|---|---|
| Client frontend | `aureon/frontend/designs/vineta/` (HTML, CSS, presentation JS, images) | ✅ Yes |
| Client bridge | `.../vineta/composer.php`, `js/vineta-data-shims.js`, `js/vineta-path-bridge.js`, `manifest.json` | Only if a DOM/data contract actually changes (expected: minimal/none) |
| Engine / Core | `aureon/frontend/` (non-designs), `aureon/theme/`, `aureon/plugin/`, `aureon/ferm-page.php` | ❌ No — unless generic defect proven (CORE-CHANGE-REQUEST.md) |

## 4. ROLLBACK CHECKPOINT

- **Protected-layer fingerprint (pre-redesign):** every `theme`, `engine`, `plugin`, and `ferm-page-override` record in `RELEASE-CANDIDATE-MANIFEST.json` carries a SHA-256 taken today. ~644 protected payload files (theme 176 + engine 151 + plugin 317) + ferm-page override. Redesign must leave all of these byte-identical; Stage 30 (Core diff) compares against this fingerprint, **not** against HEAD (HEAD predates the tested deltas).
- **Frontend/bridge rollback:** current vineta pack state == candidate manifest records (layer `vineta`); any template edit can be reverted to those hashes. A full pre-edit copy of the pack can be taken at edit time if requested.
- **Deploy mirror** verified (771/771) and **Golden Copy** frozen — neither may be touched by the redesign pass; mirror re-sync happens only after redesign acceptance.
- Release candidate + zip `AUREON-RELEASE-PREP-DELIVERABLES-2026-09-04.zip` (SHA-256 `23a2e7e0…`) remain the pre-redesign archive.

## 5. STATUS FLAGS

```
All VINETA_*_PASS / AUREON_CLIENT_PRODUCTION_READY_BLOCKED flags unchanged from
docs/forensics/RELEASE-CANDIDATE-VERIFICATION.md — the redesign operates ON TOP of the
confirmed tested build and must not regress any of them.

VINETA_FRONTEND_REDESIGN_PASS      ⏳ (target)
VINETA_FRONTEND_REDESIGN_BLOCKED   ⏳ (current — awaiting direction + runtime)
```

## 6. NEXT STEPS (per redesign prompt stages)

Stage 1 read current implementation (bridge/slots/pages) → Stage 2 read reference (108 pages) →
Stage 3 comparison matrix → Stage 4 dynamic contract freeze → Stage 5 redesign plan →
Stages 6–33 (per-component redesign + regression + acceptance). Code stages require the
local Docker runtime for verification; blocked until Docker is started.
