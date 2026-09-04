# FINAL REDESIGN BASELINE (Slice 4)

**Date:** 2026-09-04 · Stage 1 of the Slice 4 master prompt
**Purpose:** freeze the redesigned build state before full regression. No source modified to produce this document.

## 1. STATE RECORD

| Item | Value |
|---|---|
| Branch | `master` |
| Git commit (HEAD) | `9315121` (2026-09-04 12:49:36 +0500) — **commit alone is NOT the build**; the tested build lives in the working tree |
| Git status | canonical `aureon/` worktree with 8 M + 513 D + 7 ?? pre-redesign files **plus redesign slices** (below) — all uncommitted by design |
| Canonical tree | `aureon/` (theme + frontend + plugin + ferm-page.php) = tested source |
| Deployment mirror | `AUREON-WORDPRESS-DEPLOY` — currently holds the **pre-redesign** candidate (771/771 verified earlier); to be re-synced after regression passes |
| Golden Copy | `AUREON-GOLDEN-COPY` — 🔒 FROZEN BASELINE, untouched |
| Active design | `vineta` (default in `frontend/views/design.php`) |
| Frontend version | Vineta pack v1.0.3 family; redesign layers `premium-black.css` added |
| Manifest | `aureon/frontend/designs/vineta/manifest.json` (css: bootstrap → swiper → animate → styles → monochrome-black → **premium-black**) |
| Redesign slices | 1 (premium design layer) ✅ · 2 (homepage composition) ✅ · 3 (cleanup/hardening) ✅ |
| Release candidate status | PRE-REDESIGN candidate archived as rollback; NEW candidate to be assembled from redesigned tested tree (Stage 38) |
| Runtime | Docker `localhost:8080` up (wordpress/db/phpmyadmin healthy) |

## 2. REDESIGN FILE SET (canonical pack changes)

| File | Slice | Type |
|---|---|---|
| `aureon/frontend/designs/vineta/css/premium-black.css` | 1 | new (design layer) |
| `aureon/frontend/designs/vineta/manifest.json` | 1 | +1 css entry |
| `aureon/frontend/designs/vineta/index.html` | 2 | promo→brand band; section titles h4→h2 |
| `aureon/frontend/designs/vineta/index.html` (+7 templates) | 2/3 | demo-promo sweep |
| 46 templates | 3 | encoding UTF-8 normalization + language/currency labels |
| 15 templates | 3 | static page-title h4→h1 |
| 4 templates | 3 | raw duplicate vendor-lib tags removed |

## 3. CONFIRMATION

- [x] Slice 1 files present (premium-black.css + manifest entry)
- [x] Slice 2 files present (homepage band + h2 titles + rhythm CSS)
- [x] Slice 3 files present (promo sweep, UTF-8 labels, H1 titles, dup-lib removal)
- [x] Current frontend matches intended redesigned state (verifications in `FRONTEND-REDESIGN-SLICE-LOG.md`)

## 4. TEST PLAN (Slice 4)

Stages 2–37 per master prompt: source check, contract audit, homepage, Customizer, logo, typography, color, hero, shop, category, product, variable (N/A), search, cart, checkout, auth, account, menus, blog, static, 404, footer/newsletter, plugins, assets, JS, CSS, responsive, a11y, security, network, cache, visual regression, feature-loss, editability, Core integrity, QA cleanup → then new candidate + mirror + reports (Stages 38–46).

Interactive gates requiring credentials/gateway (Customizer SET/SAVE, admin auth round-trips, live checkout with payment) will be recorded honestly as BLOCKED where the environment cannot prove them this session.