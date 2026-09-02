# Phase 17 / 17.1 — Aureon Frontend Framework

## STATUS (2026-08-07 LATE): PHASE 17.1 REBUILD COMPLETE — LIVE + VERIFIED. Phase 17 remains rolled-back (superseded).
- Stages 2–11 all built + verified live on aureon_wp @ localhost:8080; 17 routes, 0 console errors.
- M2 component audit done; M2.3 gap components (order/confirmation, form/forgot-password, auth/password-strength, account/orders) built + LIVE; M2.4 WP fallback purge done (0 WP calls in components); M2.5 API headers done (52/52 standardized docblocks). All M2 COMPLETE 2026-08-07.
- See `mem:aureon-rebrand/frontend-v2-roadmap` (roadmap + M2 record) and `mem:aureon-rebrand/lead-architect-review` for full state.

## Phase 17 (SUPERSEDED — declared FAILED by user, rolled back)
- Full component framework was built and verified (render engine, registry 39/39, tokens, adapters x10, sections x9, tests) but the INTEGRATION failed: layered AETHER onto theme's own layout engine without suppressing it → duplicated DOM/assets/scripts, partial template coverage, mixed design languages.
- Evidence: `aureon-doc/FRONTEND_FORENSIC_REPORT.md`.
- Rollback backup: `C:\Users\hamma\AppData\Local\Temp\opencode\phase17-integration-backup.tar.gz`.

## Phase 17.1 (ACTIVE) — Rebuild from pristine source
- **Source of truth:** `C:\Users\hamma\Downloads\templete\frontend` → `frontend/source/` (364 files, read-only, mirror-verified). NEVER EDIT source/.
- **Keep (verified engine):** `frontend/views/` (renderer+viewmodel), `frontend/manifest/` (39), `frontend/tokens/`, `frontend/adapters/` (10), `frontend/sections/` (9), `frontend/tests/`, `aureon/plugin/woocommerce/functions/template-locator.php` (woo bridge), 10 planning .md docs in frontend/ root.
- **Reports done:** FRONTEND_FORENSIC_REPORT.md (Stage 0), FRONTEND_AUDIT.md (Stage 1) — both in `aureon-doc/`.
- **Restored from git:** theme 404/front-page/functions/customizer/helpers/page-about/page-contact/single.php + plugin woocommerce.php + planning docs FRONTEND-ANALYSIS.md & PHASE-17-1-INTEGRATION-ARCHITECTURE.md (HEAD 3e5741a).
- **Deleted:** theme inc/frontend.php, home.php, customizer fields/frontend.php, page-faq/team/testimonials/wishlist.php, plugin woocommerce/templates/, frontend/components/, frontend/assets/, frontend/templates/.

## Key verified facts (do not re-derive)
- Pristine source contract: CDN bootstrap 5.3.3, swiper@11, gsap 3.12.5, lenis 1.1.18, FA 6.5.1; local JS lenis-scroll/animations(40KB)/main/phantom-data/phantom-bridge/firebase-auth(3 pages); CSS style(99KB)/responsive/motion/a11y + vendor (animate/blog/shop/owl).
- Animation attributes (REAL, not the old analysis): data-magnetic, data-motion-text, data-mouse-depth, data-countup, data-parallax-speed, data-swiper-parallax, data-phantom* (359 total). NO data-reveal-group/data-tilt.
- Rebuild mission: WordPress/WC = data only; frontend owns 100% presentation; Template→Composer→Components→Renderer→HTML; suppression layer required; stages: shell→home→shop→product→cart/checkout/account→blog→static→tokens/assets/animation→visual regression.
- Deploy: use Windows tar.exe (NOT Compress-Archive — backslash bug); container aureon_wp @ localhost:8080 (admin/admin123, WC 11.0.0); WP 6.9.1 drops stylesheet dependents if dependency handle registered with false src — register first.
- Theme requires: `aureon_do_attr` (helpers.php:744 region), `aureon_construct_sidebars` (structure/sidebars.php:18); plugin WC functions.php 1571 lines (colors :62, fonts :108, enqueue :215-253).
- Pending: fonts Cabinet Grotesk/Satoshi not downloaded; mu-plugins/aureon-fix-wc-session.php missing; Gutenberg/block-render duplication evidence still to gather for forensic completeness (subagent report truncated).
