# AETHER Master Verification Task — Status by Phase (16 phases)

## PLAN DOCUMENTATION STATUS (2026-08-14)
- The plan we are executing = "Frontend Dynamic Completion & Integration Hardening": only Step 0 exists on disk → `docs/plans/frontend-dynamic-completion/00_BASELINE.md` (2026-08-13; HEAD `88ab98a` @ tag `v1.2.0-g6`, working tree WAS clean then; live numbers therein are from 2026-08-09, superseded by THIS task's 2026-08-14 live runs). Remaining step files not yet written.
- Older predecessor plan file `AETHER-PRO-INTEGRATION-PLAN.md` (WS-1..WS-7, 2026-08-08, built the Customizer-driven AETHER frontend) is **MISSING from disk** — never committed, deleted since. Sole surviving record: memory `project/aether-pro-integration-plan`. Its deferred P2/P3 queue (G8 WC color bridge, G9 fonts, G11 elements, G12 blog, demo, CSP) = phases 12–15 of this task.

> Commands context: `frontend/tests`, PowerShell only. ALWAYS run Playwright per-spec (full-suite invocation hangs >10 min). Playwright CLI only accepts specs under `frontend/tests` (absolute temp paths → "No tests found").

## LEGEND
DONE (verified) · IN PROGRESS · PENDING · BLOCKED (needs owner/env)

## PHASE-BY-PHASE STATUS

| Phase | Scope | Status | Evidence / Notes |
|---|---|---|---|
| 0 | Current-state architecture doc | **DONE 2026-08-14** | `docs/AETHER_DYNAMIC_ARCHITECTURE_CURRENT_STATE.md` — git state (main @ `88ab98a`, 21 uncommitted files), Docker stack (`aureon_wp`/`aureon_db`, WP 7.0.2 / WC 11.0.0 / PHP 8.3.33), mount topology + `docker cp` sync proc, defect ledger, toolchain |
| 1 | Baseline report (tool matrix, env facts, E2E results) | **DONE 2026-08-14** | `docs/AETHER_DYNAMIC_BASELINE_REPORT.md` — NOT INSTALLED rows (phpcs/PHPStan/Psalm/ESLint/wp-cli/composer), E2E 56/1/0, live-gaps 6/6, fix ledger, WC-11 response-shape finding |
| 2 | Static audit: 27-surface data-flow matrix | **DONE 2026-08-14** | `docs/PHASE02_DATAFLOW_MATRIX.md` |
| 3 | Static audit: source-of-truth registry | **DONE 2026-08-14** | `docs/PHASE03_SOURCE_OF_TRUTH_REGISTRY.md` (F3-1…F3-7) |
| 4 | Static audit: Customizer round-trip | **DONE 2026-08-14** | `docs/PHASE04_CUSTOMIZER_ROUNDTRIP.md` — 49 controls, live :root emission, G9 bridge live (F4-1…F4-7) |
| 5 | Static audit: plugin modules | **DONE 2026-08-14** | `docs/PHASE05_PLUGIN_MODULE_AUDIT.md` (F5-1…F5-10) |
| 6 | Static audit: WC surfaces binding matrix | **DONE 2026-08-14** | `docs/PHASE06_WC_BINDING_MATRIX.md` — 5 commerce surfaces end-to-end (F6-1…F6-3) |
| 7 | Static audit: token sweep | **DONE 2026-08-14** | `docs/PHASE07_TOKEN_SWEEP.md` (F7-1…F7-4) |
| 8 | Static audit: bindings/remaining surfaces | **DONE 2026-08-14** | `docs/PHASE08_REMAINING_SURFACES_AUDIT.md` (F8-1…F8-4) |
| 9 | Live: baseline screenshots all routes | **DONE 2026-08-14** | `docs/PHASE09_LIVE_SCREENSHOTS.md` + `docs/screenshots/01-…15-*.png` |
| 10 | Live: failure injection | **DONE 2026-08-14** | `docs/PHASE10_FAILURE_INJECTION.md` — 3/3 survived; re-verified in Phase 15 on hardened code |
| 11 | Live: a11y full sweep | **DONE 2026-08-14** | `docs/PHASE11_A11Y_SCAN.md` (F11-1 CRITICAL → fixed P12, F11-2 MED → fixed P12) |
| 12 | CRITICAL fixes: newsletter a11y + suite coverage | **DONE 2026-08-14** | F11-1 (button name — `section/newsletter.php` forward-when-non-empty + `forms/newsletter.php` !empty guard) + F11-2 (scroll-then-axe; A11Y_PAGES 11 surfaces incl. cart/team/product) — axe 14/14 desktop + 14/14 mobile |
| 13 | MED fixes (6 items) | **DONE 2026-08-14** | F3-1 about demo-gate, F3-2 coming-soon date persist, F7-1 7 tokens → static :root, F7-2 color bridge align, F8-1 blog empty-state (+manifest entry; adapter paged-array bug fixed), F6-1/F5-3 WC gate by page state — all live-verified |
| 14 | LOW cleanups | **DONE 2026-08-14** | F4-3 clamp 1–48, F4-4 padding regex, F3-3 real review aggregation; F7-3/F8-2/F8-3/F6-2/F6-3 documented no-action; F8-4 no-results.php deleted |
| 15 | Regression suite + injections | **DONE 2026-08-14** | php -l 0, node --check 0, verify gates, routes 16/16×2, interactions 5+1skip/6, failure-injection 4/4, live-gaps 6/6, a11y 14/14×2, visual 3/3 zero-delta; injections #1/#2 re-verified on hardened code, state restored |
| 16 | Final audit doc + gates | **DONE 2026-08-14** | `docs/AETHER_DYNAMIC_FINAL_AUDIT.md` written; STOP gate reached — no further code changes without re-planning |

## ALREADY-CLOSED WORK (pre-this-task, context)
- Dynamic conversion Phases A–F: implemented 2026-08-09, live-verified 2026-08-14 (see `project/frontend-dynamic-conversion-baseline`).
- License removal, WC session mu-plugin fix, module system, Customizer deep verification: complete (see `project/final-state`).
- GP forensic audit 12/12 phases: complete (`gp-audit/state`).

## SESSION LOG (2026-08-14) — what was actually executed
1. Full E2E per-spec: routes desktop 16/16 → interactions+failure+a11y desktop 5+1skip → visual 3/3 → mobile 21+1flaky. (Respects the >10-min hang on combined invocations.)
2. Built `live-gaps.spec.js` (temporary file, kept as regression spec) — 6 tests; iterated 2 failures → 6/6.
3. Root-caused G1 navigation via forensics: init-script event tracing (sessionStorage/console-flush pitfalls documented) + CDP initiator stack → `main.js:420`.
4. Discovered WC 11.x removed `success` key (container `class-wc-ajax.php` read) → fixed success-detection; CDP flag test confirmed zero navigation.
5. Root-caused G3 `form.action` = own-prop `"[object HTMLInputElement]"` (definition: client script outside repo; proof: getOwnPropertyDescriptor; setter/defineProperty tracers silent) → hardened to `getAttribute('action')`.
6. Verified `location.assign/replace/href` unforgeable in Chromium (own-property assignment silently ignored) → CDP initiator stacks are the ONLY reliable navigation attribution.
7. Post-fix full regression re-run green (same per-spec commands) + `php -l`/`node --check` clean; container main.js MD5 `316793241060172d13932307c22d3417`.
8. Wrote Phase 0 + Phase 1 docs (see table) + updated baseline memory.

## SESSION LOG (2026-08-14, phases 12–16)
1. Phase 12: completed F11-1 (section/newsletter.php forward-when-non-empty for button_text/note/success_text — shadowing class fixed for all 3) + F11-2 (A11Y_PAGES + /cart/, /team/, real product slug).
2. Phase 13: verified 5/6 MEDs already in working tree (F3-1, F3-2, F7-1, F7-2, F6-1/F5-3); F8-1 had a REAL bug — `adapter-blog.php` always returns non-empty `paged` array so `empty($paged)` never fired → fixed condition to `empty($items) && 0 === (int)$paged['total']`; added missing manifest entry `utility/empty-state` (component no-oped without it). Live-verified `/?s=zzzzzznomatch` → "Nothing found".
3. Phase 14: added `aureon_sanitize_shop_per_page` (clamp 1–48; absint-strips-sign bug caught & fixed) + `aureon_sanitize_section_padding` (regex 1–4 lengths) in customizer/helpers.php; wired into fields/frontend.php.
4. Phase 15: deployed frontend/ to container (tar→base64→docker cp recipe; md5-verified); full regression green (see table); injection #2 (hostile section_padding/color via direct option write → neutralized at render, no breakout) + #1 (demo gate) re-verified, state restored.
5. Phase 16: `docs/AETHER_DYNAMIC_FINAL_AUDIT.md` written (16-phase verdict table, fix ledger, regression evidence, deferred list, STOP gate). 3 closure screenshots (13-search-empty-state, 14-shop-newsletter, 15-coming-soon).
6. Working tree: 55 changed/untracked entries — NOT committed (user hasn't asked).

## NEXT ACTIONS
- Commit the working tree in small batches — ONLY when the user asks.
- Post-STOP candidates needing owner decision: demo import packs, CSP enforcement, real-MTA email verification.