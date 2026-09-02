# AETHER Frontend — Complete Status (DONE / REMAINING / MISSING)

> Snapshot 2026-08-14, live-verified. Engine = `frontend/` (views/adapters/components/tokens/manifest), bridge = `aureon/theme/inc/frontend.php` (READ-ONLY constraint: any theme-core change requires a stop-condition report), plugin = `aureon/plugin`.

## 1. WHAT IS DONE (verified green)

### Conversion build (Phases A–F of the dynamic-conversion plan — closed)
- A: animation guard-first + watchdog + try/catch, `@media (scripting:none)` fallback (`assets/js/animations.js`).
- B: WC guards in product adapters — zero unguarded `wc_get_*`.
- C: G1/G4/G5 settings-bound (announcement, footer columns, contact); hero CTA defaults to shop; G6 hero-slides Customizer repeater shipped (HEAD `88ab98a`).
- D: `aether_demo_content` master toggle gates all demo fallbacks.
- E: Playwright + axe E2E suite committed (`frontend/tests/`).
- F: styleguide `aureon/theme/page-styleguide.php`; 8 forensic docs in `docs/`; CI rewritten (real repo paths, static job + optional e2e job).

### LIVE VERIFICATION (2026-08-14, host :8080 `aureon_wp` — WP 7.0.2, WC 11.0.0, PHP 8.3.33)
- Full E2E suite: **56 passed / 1 skipped (expected) / 0 failed** per-spec: routes desktop 16/16; interactions+failure+a11y desktop 5+1skip; visual 3/3 `/`,`/shop/`,`/about/` ZERO pixel delta vs committed snapshots; mobile routes+interactions 22 total (21 + 1 flaky→green).
- Live-gap spec `frontend/tests/specs/live-gaps.spec.js` **6/6 passed**: G1 card AJAX add-to-cart (wc-ajax POST, count +1, fragment `a.aether-cart-count`, NO navigation); G1 product page qty stepper (1→2, count +2); G3 contact form (admin-ajax, status rendered, no nav); G4 newsletter (JSON success); G1 no-JS classic fallback (native `?add-to-cart=` lands on cart); G1 cart page renders.
- Zero page JS errors on all surfaces; CSP = report-only (INFO-only violations).
- Container main.js synced (`docker cp frontend/assets/js/main.js aureon_wp:/var/www/html/wp-content/frontend/assets/js/main.js`) MD5 `316793241060172d13932307c22d3417` (platform core now FROZEN at `6d8f3b671333571508efcb53b1e39e60`, see M6-M10 section).

### Two REAL live bugs found & fixed (root-caused, evidence chains kept)
1. **G1 add-to-cart full-page navigation** — dual defect: (a) `frontend.php:165` localized `?wc-ajax` with EMPTY value → WordPress rendered `…/?wc-ajax` (trailing `=` stripped) → old code concatenated to `?wc-ajaxadd_to_cart` (malformed); (b) main.js success-gated on `json.success`, and **WC 11.x removed the `success` key** (verified in container `class-wc-ajax.php add_to_cart()`: success = fragments+cart_hash only; failure = `{error:true, product_url}`). CDP `requestWillBeSent` initiator stack proved the navigation came from `main.js:420` (`window.location.href = btn.href`) — NOT the browser default (defaultPrevented=true was confirmed). Fix: `frontend.php` → `add_query_arg('wc-ajax','add_to_cart', home_url('/'))`; main.js → success by `json.fragments`, errors via `json.error`+`product_url`, last-resort fallback `btn.href`.
2. **G3 contact form corrupted POST URL** — a client script NOT in any repo source (vendor-bundle territory) writes an OWN data property `action = "[object HTMLInputElement]"` on the contact `<form>`; `form.action` (IDL) returns it while `getAttribute('action')` and the server-sent HTML stay the clean `…/wp-admin/admin-ajax.php` (proof: getOwnPropertyDescriptor + tracer stack recorded zero setter/defineProperty calls). Fix: main.js posts to `form.getAttribute('action') || aetherAjax.ajaxUrl`. Side finding: container has NO MTA → `wp_mail()` returns `false` (`aether-ajax.php:164`) → the handler's error path is what a valid submission gets; frontend contract (AJAX + status + no nav) is proven, delivery is ENV-LIMITED.

### Docs produced
- `docs/AETHER_DYNAMIC_ARCHITECTURE_CURRENT_STATE.md` (Phase 0: repo/git state, live stack, mount topology, sync procedure, defect ledger, unforgeable-location note, repeat commands).
- `docs/AETHER_DYNAMIC_BASELINE_REPORT.md` (Phase 1: tool matrix w/ NOT INSTALLED rows, environment facts, E2E + live-gap results, fix ledger, known limitations).
- (Earlier, pre-verification) 8 forensic conversion docs + CI rewrite.

## 2. WHAT IS REMAINING (post-closure — all 16 phases DONE 2026-08-14)

Master verification task CLOSED — see `docs/AETHER_DYNAMIC_FINAL_AUDIT.md` (16-phase verdict table, fix ledger, regression evidence, deferred list, STOP gate). Memory `project/verification-master-task-status` has the full per-phase ledger.
- Fixes shipped in phases 12–15: F11-1 + F11-2 (newsletter a11y + suite coverage), F3-1/F3-2/F7-1/F7-2/F8-1/F6-1+F5-3 (6 MED), F4-3/F4-4/F3-3 (LOW), F8-4 (dead template deleted), blog empty-state manifest entry + paged-array condition fix.
- Deferred (documented, need owner decision): CSP enforcement, demo import packs (F5-8 auto-draft cleanup), real-MTA email verification, form.action vendor-bundle writer, checkout field-type cosmetics.
- **DELIVERABLE**: `docs/AETHER_DYNAMIC_FINAL_AUDIT.md` — DONE.
- Commit the uncommitted working tree (55 changed/untracked entries) in small batches — DONE 2026-08-14 (6 batches, all pushed; see M6-M10 section below).

## 3. WHAT IS MISSING / KNOWN GAPS (recorded, not blockers)

- **Tooling NOT INSTALLED (host)**: phpcs, PHPStan, Psalm, ESLint, wp-cli, composer — lint gates run `php -l` + `node --check` only; quality-gate rows marked NOT-APPLICABLE in the baseline.
- **Email delivery unverifiable** (no MTA in container) — contact success path can never be proven green here; re-check on a host with a real MTA.
- **`form.action` corruption source UNKNOWN** — writer is outside the repo (vendor bundle hypothesis); engine hardened defensively; monitor upstream bundles.
- **Visual snapshot coverage only 3 pages** (desktop `/`, `/shop/`, `/about/`) — all other routes lack committed baselines until Phase 9.
- **Cart-page check is a soft probe** (selector presence only — `renders=` console log); quantity-form POST native behaviour needs a real assertion (Phase 6/9 scope).
- **Wishlist, WooCommerce blocks surfaces, My Account internals, Checkout form fields** — not yet dynamically exercised browser-side (premium gaps queue).
- **Axe rules**: full WCAG 2.2 AA sweep on every route pending (current a11y spec covers a subset).
- **Mobile edge-case flake**: `/coming-soon/` route occasionally needs a retry (timing, marquee) — investigated, benign, but note for CI stability.
- **`?ver=` asset versioning** auto-bumps per file mtime; container-side JS must be re-copied manually after every change (no bind mount for `frontend/`) — documented in the architecture doc.

## 4. Environment & stack facts (ground truth for any future session)

- Containers: `aureon_wp` (:8080, Apache/PHP 8.3.33, WP 7.0.2, WC 11.0.0) + `aureon_db` (mysql:8.0). NOTE: older memories reference `phantom-wp`/`phantom-db` — the live stack used since is `aureon_wp`/`aureon_db`.
- Bind mounts: `aureon/theme` + `aureon/plugin` → live; `frontend/` NOT mounted → `docker cp` after each JS change.
- Product fixture: #435 `[W] Denim Trucker Jacket — Pitch Black`, simple, purchasable, in stock.
- Admin: admin/admin123 (admin-ajax tests pass nonce from live pages — works for logged-out flows too).
- Host: Windows + PowerShell-only shell (no bash); Node v24.18.0, npm 11.18.0; Playwright 1.48.2 (chrome channel), axe-core 4.10.1.
- git: branch main, HEAD `88ab98a`, working tree 21 changed files, NOTHING committed this session.

## 5. M6–M10 DESIGN-PACK MILESTONE (COMPLETE — 2026-08-14, ALL VERIFIED + PUSHED)

> Master record: `.serena/memories/frontend-platform/M6-M10-lumen-proof-state.md` (final state). Commits:
> `901c1f6` (M7 asset engine), `9b54703` (M8 manifest contract + design.php cache fix), `1fcdaff` (M9 visual baselines),
> `83533b8` (M10 lumen pack + isolation spec + schema doc), `ce96ec8` (G4 flake fix), `dda0ffd` (memory finalize). Tag: `v1.3.0-m6-m10`.

### G4 newsletter flake — RESOLVED (root cause CORRECTED)
- The reveal was never the failure (timeline diag proved visibility 10/10). REAL cause: server-side
  per-minute IP rate limit in `aether_ajax_newsletter_subscribe()` (`aether-newsletter.php:210`) — rapid
  hammer runs tripped it → `ajaxJson.success=false`. Container REMOTE_ADDR = 172.19.0.1 (Docker bridge).
- Fix: private/reserved IP ranges exempt (`FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE`), IP
  filterable via `aether_newsletter_rate_limit_ip`. Verified: G4 hammer 10/10 PASS, live-gaps 12/12.

### design.php static-cache bug — FOUND + FIXED
- `aether_active_design()` cached the raw sanitized option BEFORE the `?: 'luxury'` fallback → first call
  per request returned 'luxury', every later call returned `''` (body class `design-`, bridge skipped luxury
  assets). Invisible in lumen mode (truthy slug). Fix: apply fallback before caching. Root-caused via
  container probes (sentinel function returns + return-value logging proved the executed file/logic).

### Luxury mode — RESTORED + SMOKE-VERIFIED
- `update_option('aether_active_design','')`; design-isolation 6/6 (body class `design-luxury`, luxury assets
  only, wp-login clean), routes 32/32, verify.sh PASSED, main.js MD5 frozen `6d8f3b671333571508efcb53b1e39e60`.

### Cleanup — DONE
- Deleted all diag scripts (`diag-*.cjs` incl. reveal-timeline + rate-limit), `diag-fail-*.png`,
  `frontend-m6.tar`/`.b64` (~48MB), `frontend/tests/results/`, container `/tmp` probes + logs.
- Container lumen.js clean (settleReveals kept, `__settleCount` debug counter removed).

### git — CLEAN + PUSHED
- Working tree clean; `origin/main` = local main; 6 commits pushed; milestone tagged `v1.3.0-m6-m10`.