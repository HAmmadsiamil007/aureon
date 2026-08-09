# Frontend dynamic conversion — baseline (2026-08-08)

## Status
Forensic baseline COMPLETE (2026-08-08, audit-only). **CLOSED 2026-08-09** — Phases A–F implemented in the working tree (see `docs/PHASE_17_FRONTEND_DYNAMIC_CLOSURE_REPORT.md`):
- A: animation guard-first + watchdog + try/catch (Rule 7 fixed in `assets/js/animations.js`).
- B: WC guards in `adapter-product`/`adapter-wc-products`/`adapter-wishlist` (zero unguarded `wc_get_*`).
- C: G1/G4/G5 settings-bound (announcement, footer columns, contact); hero CTA defaults to shop; G6 deferred.
- D: `aether_demo_content` toggle gates all fallbacks (default true).
- E: Playwright suite committed; pre-fix 9 failures root-caused + fixed; live re-run NOT VERIFIED (Docker down).
- F: styleguide `aureon/theme/page-styleguide.php` (manifest components only).
- Gates: `frontend/tests/verify.sh` PASSED. Theme files touched (documented stop-condition): `inc/aether-tokens.php`, `inc/customizer/fields/frontend.php`, `inc/frontend.php`.
- **Open:** start Docker (`docker start aureon_wp`) → `npx playwright test` to close the last NOT VERIFIED item; commit the working tree in small batches.

## Mission
Convert the static premium frontend (`frontend/source/*.html`) into a fully data-driven AUREON frontend WITHOUT redesigning it and WITHOUT touching `aureon/theme` core or `aureon/plugin`. The prompt is a 30-section playbook; section 1 mandates a baseline audit before any code.

## Verified architecture (working tree)
- Engine: `frontend/views/{loader,registry,renderer,viewmodel,composer}.php` — healthy; loader globs 23 adapters + 26 sections; renderer resolves adapter fns (`adapter-wc-products` → `aether_adapter_wc_products`), per-call `$data` wins over `adapter_args`.
- Manifest: `frontend/manifest/components.php` = **53 components** (supersedes STATUS.md's "39").
- Tokens: `frontend/tokens/tokens.php` registers ~90 defaults on `aureon_option_defaults` (+ color/font bridges). Full demo catalog shipped as fallback (products/FAQ/testimonials/team/hero/specs/size table).
- Theme bridge (`aureon/theme/inc/frontend.php`) — READ-ONLY: loads loader + 6 hardening inc files, suppresses theme styles/scripts @1000, enqueues CDN (bootstrap 5.3.3, FA 6.5.1, swiper 11, gsap 3.12.5, ScrollTrigger, lenis 1.1.19) + local, routes WC templates via `template_include` @99, localizes `aetherAjax`.
- Customizer (theme-side, READ-ONLY): `aureon_aether_section` — 16 section toggles, 8 shell/motion, 3 announcement/commerce, 11 colors, 8 layout. All transport=refresh.
- Theme templates: pure section composition (front-page, home, single, single-product, cart, archive-product, 9 static pages, myaccount, thankyou, search, 404).
- STATUS.md (aureon-doc) documents Phase 17 Stages 1–13 all complete + live-verified.

## Critical finding (Rule 7 FAIL)
`frontend/assets/js/animations.js` adds `html.has-motion` **before** the GSAP/ScrollTrigger existence guard; guard only warns and returns. `motion.css` hides `[data-reveal]`, `[data-reveal-item]`, `[data-motion-text]`, `[data-image-reveal]` under `has-motion`. **Result: GSAP/ScrollTrigger CDN failure → content permanently hidden.** No watchdog/try-catch. This is Phase A (frontend-only fix: guard order + watchdog + try/catch + `@media (scripting:none)` preloader fallback).

## Other gaps (all frontend-fixable)
- G1: `adapter-shell.php` ignores Customizer announcement text/items ("legacy" comment).
- G2: demo fallbacks show fake content on empty stores → need `aether_demo_content` master toggle.
- G3/G4/G5: categories copy / footer links / contact info hardcoded in adapters.
- W1/W2: unguarded `wc_get_product_ids_on_sale()` / `wc_get_related_products()` / `wc_get_product_id_by_sku()` in adapters.
- No committed Playwright/a11y/perf/visual-regression suite.

## Docs produced
`docs/` (8 files): FRONTEND_DYNAMIC_CONVERSION_BASELINE, FRONTEND_DATA_CONTRACT, FRONTEND_COMPONENT_DYNAMICITY_MATRIX, CUSTOMIZER_FRONTEND_BINDING_MATRIX, WOO_FRONTEND_BINDING_MATRIX, FRONTEND_CONVERSION_REPORT, FRONTEND_VISUAL_REGRESSION_REPORT, FRONTEND_FAILURE_MODE_REPORT, FRONTEND_API_USAGE. Updated Report/MASTER_ROADMAP.md + aureon-doc/CHANGELOG.md.

## Conversion map (remaining phases)
A animation watchdog (critical) · B WC guards + pagination base · C Customizer round-trip closure · D demo-content policy · E Playwright/a11y/perf/failure-injection suite · F styleguide + final reports. All frontend-only; any theme-side need = stop-condition report first.

## Implementation plan (2026-08-08)
`docs/FRONTEND_IMPLEMENTATION_PLAN.md` — full execution plan (v1.0) with exact before/after code for every phase (quoted current code from working tree). Phases A–F, one commit each, gates per phase (php -l / node --check / verify.sh / Playwright / screenshot diff). Open questions pending owner: (1) aether_demo_content default true vs false, (2) product-card Add-to-Cart = real AJAX vs permalink nav, (3) hero-slides Customizer repeater (needs theme-side control → stop-condition report).
