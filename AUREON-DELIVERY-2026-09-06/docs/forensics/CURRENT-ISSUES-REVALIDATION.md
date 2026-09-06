# CURRENT-ISSUES-REVALIDATION (Phase 1 — Master Plan)

**Date:** 2026-09-06 · Method: every finding from the 2026-09-06 forensic audit re-checked against current code. Old labels were NOT trusted; each was re-derived from source. This file is the gate that decided what got fixed.

## Classification legend
`RESOLVED` · `CURRENTLY BROKEN` (reproduced in code) · `PARTIAL` · `MISSING` · `UNPROVEN` (needs runtime) · `FALSE/STALE FINDING` (audit was wrong) · `FIXED THIS PASS` (was broken, fixed now)

## Revalidation ledger

| Audit ID | Old status | Re-check method | New status | Notes / action |
|---|---|---|---|---|
| A-01 untracked auth header | BROKEN | `git status` + commit 39e8215 dependency | **FIXED THIS PASS** | Committed in `2d8f4e0` (T-01). Fresh clones now ship the header. |
| A-02 root mutation scripts | RISK | workspace root listing | **FIXED THIS PASS** | Quarantined to `scripts/one-off/` + README in `dd001c5` (T-02). |
| A-03 Core hardcodes 'vineta' | CORE_REVIEW_REQUIRED | `views/design.php` re-read | CONFIRMED, **NOT FIXED** | One-line fix would restore default `luxury`, but requires runtime option/constant change (QUESTIONS.md Q3) — BLOCKED (B-1). Do not blind-fix: could change live design resolution. |
| A-04 runtime invisible | RISK | no runtime in repo | CONFIRMED, **UNRESOLVABLE HERE** | Blocker B-1. Drives every BLOCKED entry in the acceptance matrix. |
| B-01 six parallel trees | RISK | trees still present | CONFIRMED, **NOT FIXED** | Archiving/deleting trees needs user approval (QUESTIONS.md Q3/Q4 territory). No code depends on them; risk documented. |
| B-02 composer layer-mixing | HIGH | re-read | CONFIRMED, **DEFERRED** | Approved plan gates refactor behind T-03/T-05; behavior-preserving split is P1 work after blockers clear. |
| B-03 fragile DOM contracts | HIGH | extracted ALL selectors from bridge code | PARTIALLY ADDRESSED | Contract suite built (`tests/contract-tests.cjs`, 57 checks, green) — the missing safety net now exists. Splicer still class-based by design. |
| B-04 demo `auto` stub | MEDIUM | re-read `vineta_show_demo_content()` + all callers | **FIXED THIS PASS** | Important nuance found: old audit said "dead code", but grep proves the function has **zero callers** — the LIVE auto logic is in `vineta_filter_demo_products` / `vineta_filter_demo_categories` (query-level) and was already correct. Fixed the stub anyway so the function can never mislead a future caller (T-11, commit `c97552d`). Old finding reclassified: **severity lower than audited; live behavior was never broken.** |
| B-05 404 fallback | MEDIUM | `ferm-page.php` resolver + pack listing (`404.html` exists) | **FIXED THIS PASS** | Resolver now prefers pack `404.html` (T-12). |
| B-06 triple cart surfaces | MEDIUM | re-verified | CONFIRMED, **DEFERRED** | Consolidation is a behavior-affecting refactor — gated on runtime testing (B-1). |
| B-07 account endpoint hooks | MEDIUM | re-read `my-account.php` | CONFIRMED, **DEFERRED** | Decision required (QUESTIONS.md Q7). |
| B-08 newsletter REST/export | MEDIUM | line-level re-read | **FALSE/STALE FINDING** | Export is double-guarded: `manage_options` submenu + `check_admin_referer('aether_newsletter_export')` (lines 268–269). REST subscribe is public-by-design with `sanitize_email`/`is_email` — correct for a public signup endpoint. **No fix needed.** |
| C-01…C-08 hardening set | P2 | spot re-reads | CONFIRMED, DEFERRED | Manual suppression list, CSP-vs-inline, jQuery dup, bfcache, ferm-era paths, dead templates, icon systems, favicons — all still present; P2 queue. |
| Phase-5 claim: "no data-aureon-slot" | (audit text) | full grep across pack | **FALSE/STALE FINDING** | Slots exist extensively (`global.*`, `shop.*`, `product.*` families — 50 distinct names across templates). Original ripgrep timed out on the large tree; corrected here. Contract suite now asserts real slot names. |
| F-002 / F-003 / F-072 / F-080 | BROKEN/RISK | see A-01/A-02/B-05/B-04 | **FIXED THIS PASS** (4) | — |
| F-042 variable products | IMPLEMENTED/UNPROVEN | re-read add-to-cart handler | CONFIRMED UNCHANGED | Implementation solid (variation membership validation, attribute resolution). Client test N/A — no catalog in repo. Never converted to PASS. |
| J1–J8 JS findings | MEDIUM/LOW | re-verified key ones | CONFIRMED, DEFERRED | J5 note: CSP/inline interaction needs runtime; contract suite pins the selector layer. |
| CU1–CU5 Customizer | as audited | re-read | CONFIRMED, DEFERRED | Color repaint removed per client directive 2026-09-04 (comment in composer) — CU findings about platform controls unchanged. |

## Net result of revalidation

- **4 findings fixed this pass:** A-01, A-02, B-05, B-04(+) plus T-14 double-injection (audited as J3/B-06 component) → 5 code changes total.
- **2 audit findings corrected (stale):** B-08 newsletter security, Phase-5 slot claim.
- **1 finding downgraded:** B-04 (live auto-mode logic was already correct in the query filters).
- **All runtime-dependent work: BLOCKED (B-1).** Nothing was blindly "fixed" without reproduction; nothing was marked PASS without evidence.
