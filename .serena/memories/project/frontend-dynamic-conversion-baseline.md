# Frontend dynamic conversion — baseline (2026-08-08)

## Status
Forensic baseline COMPLETE (2026-08-08, audit-only). **CLOSED 2026-08-09** — Phases A–F implemented in the working tree (see `docs/PHASE_17_FRONTEND_DYNAMIC_CLOSURE_REPORT.md`):
- A: animation guard-first + watchdog + try/catch (Rule 7 fixed in `assets/js/animations.js`).
- B: WC guards in `adapter-product`/`adapter-wc-products`/`adapter-wishlist` (zero unguarded `wc_get_*`).
- C: G1/G4/G5 settings-bound (announcement, footer columns, contact); hero CTA defaults to shop; G6 deferred.
- D: `aether_demo_content` toggle gates all fallbacks (default true).
- E: Playwright suite committed; pre-fix 9 failures root-caused + fixed; live re-run **VERIFIED 2026-08-14** — full suite green: 56 passed / 1 expected skip / 0 failed (desktop routes 16, desktop interactions+failure+a11y 5+1skip, visual 3, mobile 22 incl. 1 flaky→green).
- F: styleguide `aureon/theme/page-styleguide.php` (manifest components only).
- Gates: `frontend/tests/verify.sh` PASSED. Theme files touched (documented stop-condition): `inc/aether-tokens.php`, `inc/customizer/fields/frontend.php`, `inc/frontend.php`.
- CI (2026-08-09): rewrote `.github/workflows/ci.yml` to gate the REAL repo (old file referenced dead Lumina paths `wp-content/themes/lumina`/`bin/smoke-phase*` and failed every push). New: static job (php -l all tracked PHP + node --check tracked JS + verify.sh) on push/PR; optional `workflow_dispatch` e2e job runs Playwright (`WEB_BASE_URL`). verify.sh bugfix: grep gate flags WP/WC function CALLS only (docblock mentions no longer false-fail), error counters moved out of pipe subshells.
- **Live closure 2026-08-14:** stack Up (`aureon_wp` :8080 + `aureon_db`, WP 7.0.2, WC 11.0.0, PHP 8.3.33). E2E re-verified green (see E line) + dedicated live-gap spec `frontend/tests/specs/live-gaps.spec.js` 6/6 (G1 card AJAX wc-ajax + count + fragment + no nav; G1 product-page qty stepper; G3 contact; G4 newsletter; G1 no-JS fallback; cart page).
- **Two REAL live bugs found & fixed during live verification (2026-08-14):**
  1. G1 add-to-cart navigated full-page — (a) `aureon/theme/inc/frontend.php:165` localized `?wc-ajax` WITHOUT endpoint (`add_query_arg('wc-ajax','',…)` → `…/?wc-ajax`); fixed to `?wc-ajax=add_to_cart`. (b) main.js gated on `json.success`, but WC 11.x no longer sends `success` (success = fragments+cart_hash only; failure = error+product_url) → stale guard fell to `window.location.href = btn.href` (proof: CDP initiator stack main.js:419/420). Fixed: success by `json.fragments`, errors via `json.error`+`product_url`. Verified: no navigation, header count +1, `is-added` state, fragment `a.aether-cart-count` updated.
  2. G3 contact form posted to `…/contact/[object%20HTMLInputElement]` — some client script (outside repo, vendor bundle) writes own data prop `action` = "[object HTMLInputElement]" on the form, poisoning `form.action` while attr + server HTML stay clean (proof: getOwnPropertyDescriptor). Fixed: main.js posts to `form.getAttribute('action') || aetherAjax.ajaxUrl`. Verified: admin-ajax reached, status message rendered, no navigation.
- **Environment findings (recorded in baseline report):** container has NO MTA → `wp_mail()` returns false → contact success path unverifiable (frontend contract proven; handler `aether-ajax.php:164` error path shown). `location.assign/replace/href` are UNFORGEABLE in Chromium — prototype-patch traces cannot see navigation initiators; must use CDP `Network.requestWillBeSent` initiator stacks. Playwright full-suite single invocation hangs >10 min → ALWAYS run per-spec.
- **Docs produced (2026-08-14):** `docs/AETHER_DYNAMIC_ARCHITECTURE_CURRENT_STATE.md` (Phase 0 — repo state, mount topology, sync proc, defect ledger), `docs/AETHER_DYNAMIC_BASELINE_REPORT.md` (Phase 1 — tool matrix with NOT INSTALLED, env facts, E2E results, fix ledger). Remaining deliverables: `docs/AETHER_DYNAMIC_FINAL_AUDIT.md` after the remaining phases.
- **Open:** working tree uncommitted (21 files, HEAD `88ab98a` G6 hero slides repeater); commit in small batches when asked; remaining master-task phases 2–16 (see `project/verification-master-task-status` memory).

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
