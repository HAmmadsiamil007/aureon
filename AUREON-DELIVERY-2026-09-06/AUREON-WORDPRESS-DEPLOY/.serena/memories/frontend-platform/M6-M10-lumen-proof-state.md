# M6-M10: Lumen Proof Pack — Final Verified State

## Status: 2026-08-14 — ALL M6..M10 WORK COMPLETE AND VERIFIED. EVERYTHING GREEN.
G4 flake RESOLVED (root cause was the server IP rate limit, NOT the reveal).
Design.php static-cache bug FOUND and FIXED (luxury fallback lost after first call per request).
Luxury restored and smoke-tested. All commits pushed. This memory is the final state.

## Committed work (batches, all pushed to origin)
- `901c1f6` feat(frontend): M7 asset engine — `aether_design_enqueue_assets()` + luxury bridge
- `9b54703` feat(frontend): M8 design manifest contract — body class, manifest validator, verify step 7c + aether_active_design static-cache fix
- `1fcdaff` test(frontend): M9 per-design visual baselines — DESIGN_SLUG-prefixed snapshots + lumen baselines
- `83533b8` feat(frontend): M10 lumen pack — manifest, tokens, css/js, 9 shadows + design-isolation spec + schema doc
- `ce96ec8` fix(aureon): G4 newsletter flake — exempt private/reserved IPs from per-minute rate limit

## G4 flake — ROOT CAUSE (corrected) + FIX (verified 10/10)
- SYMPTOM: `.newsletter-form` reveal tween never fires → G4 revealAndWait fails ~50%.
- The reveal mechanics were NEVER the failure: timeline diag (diag-reveal-timeline.cjs, now deleted)
  proved visibility 10/10 — settleReveals + spec re-scroll handle the short-page/font-reflow case.
- ACTUAL CAUSE: `aether_ajax_newsletter_subscribe()` rate-limited one subscribe per IP per minute.
  Rapid hammer runs tripped it → `ajaxJson.success=false` → G4's AJAX-phase assertion failed.
  Container REMOTE_ADDR is 172.19.0.1 (Docker bridge) — loopback-only exemption would NOT work.
- FIX (aureon/theme/inc/aether-newsletter.php:210): private/reserved IP ranges exempt via
  `filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)`;
  IP passes through `aether_newsletter_rate_limit_ip` filter (null disables entirely).
- Verified: G4 hammer 10/10 PASS, live-gaps 12/12. First attempt had inverted `false ===`; corrected.

## design.php static-cache bug (found during luxury restore) — FIXED
- `aether_active_design()` cached the RAW sanitized option into `static $design` BEFORE applying
  the `?: 'luxury'` fallback → first call per request returned 'luxury', every later call returned ''
  (body class `design-`, bridge skipped luxury assets). Invisible in lumen mode (truthy slug).
- Fix: resolve branch → apply `$design ? $design : 'luxury'` → THEN cache → return.
  Deployed to container; probe through wp-load now returns "luxury".

## Verification state (Luxury ACTIVE in container, final)
- luxury smoke: design-isolation 6/6 PASS (body class `design-luxury`, luxury assets only, wp-login clean)
- routes 32/32, verify.sh PASSED, main.js MD5 unchanged 6d8f3b671333571508efcb53b1e39e60 (platform core frozen)
- prior lumen-mode results (still valid): isolation 3/3, failure-injection 4/4, a11y 28/28,
  live-gaps+interactions 22 pass/1 flaky/1 skip (G3 contact success=false is EXPECTED: no MTA in dev)

## Cleanup (done)
- Deleted diag scripts (diag-footer/newsletter/exact/grow/swiper/probe/reveal-timeline/rate-limit .cjs),
  diag-fail-*.png, frontend-m6.tar/.b64 (~48MB), frontend/tests/results/, container /tmp probes + logs.
- Container lumen.js clean (settleReveals kept, __settleCount counter removed).

## Environment reminders
- Container: aureon_wp, site http://localhost:8080, wp-content at /var/www/html/wp-content/.
- theme (aureon) + plugin (aureon-studio) = HOST bind mounts (edit repo = live). frontend/ = volume copy:
  deploy single file via `docker cp <local> aureon_wp:/var/www/html/wp-content/frontend/<path>`.
- Playwright from frontend/tests; `$env:DESIGN_SLUG='lumen'` for per-design tests; 1 worker; projects desktop 1280x800 / mobile 390x844.
- PowerShell 5.1: no ternary `?:`; use `& 'C:\Program Files\Git\bin\bash.exe'` for verify.sh.
- Activation: `docker exec aureon_wp php -r "require '/var/www/html/wp-load.php'; update_option('aether_active_design','lumen');"` ('' = luxury).
- Platform contract: animations.js:328-332 auto-assigns data-reveal to .btn-primary/.btn-outline/.btn-lg/.section-cta
  (guard: skip if inside [data-reveal] ancestor); reveal ScrollTrigger `top 86% once:true`. main.js:748 toggles
  `#pdStickyBar.visible`. helpers.js expectHeaderVisible needs `header.header` or `#mobileHeader` visible.
- Debug lesson: when docker-cp'ing a debug build, base the patch on the CURRENT container file
  (`docker cp aureon_wp:<path> <local>` first) — the local repo file may differ from what's deployed.