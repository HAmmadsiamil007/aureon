# 13 — Single-Frontend Implementation Plan (Phase 27)

**Status: PLAN ONLY — nothing implemented.** Derived exclusively from audit findings (01–12). Every task: phase, files, layer, dependency, risk, change type, expected result, test, regression, rollback.

## P0 — Critical (do first, smallest blast radius)

| ID | Task | Files | Layer | Why | Deps | Risk | Change type | Expected result | Targeted test | Regression check | Rollback |
|---|---|---|---|---|---|---|---|---|---|---|---|
| T-01 | Commit or remove untracked pack header component | `AUREON-WORDPRESS-DEPLOY/frontend/designs/vineta/components/shell/header.php` | REPO | shipped feature depends on untracked file (A-01) | none | LOW | config | auth pages render Vineta header from fresh clone | fresh-clone smoke of `/my-account/?auth=login` | visual diff auth pages | `git revert` |
| T-02 | Quarantine/remove root one-off scripts | `enable_cod.php`, `update-contact.php` | REPO | server-mutating scripts in root (A-02) | none | LOW | file-move | no executable config mutators in web root | `test ! -f enable_cod.php` | none | restore from git history |
| T-03 | Set design via runtime, restore Core default | `frontend/views/design.php` + wp-config/option | CORE | remove client slug from Core (A-03) | T-04 (confirm runtime) | MEDIUM | code+config | Core default `luxury`; vineta active via `AETHER_DESIGN`/option | boot with option unset → luxury; set → vineta | full route smoke | 1-line revert |
| T-04 | Document/export production runtime state | new `docs/forensics/runtime/*` | DOCS | DB/plugins/cache invisible (A-04) | user input | LOW | docs | runtime topology + plugin list + gateway state recorded | review | — | — |
| T-05 | Declare canonical tree, archive others | repo dirs | REPO | 6-tree drift (B-01) | user approval | LOW | file-move | single canonical tree + archives | drift diff script | none | archives |

## P1 — Important

| ID | Task | Files | Layer | Why | Deps | Risk | Change type | Expected result | Targeted test | Regression | Rollback |
|---|---|---|---|---|---|---|---|---|---|---|---|
| T-10 | Split vineta composer into bridge/data/ajax/menus/customizer modules | `designs/vineta/composer.php` → pack subdirs | BRIDGE | layer mixing (B-02) | T-05 | MEDIUM | refactor (behavior-preserving) | identical page output; smaller files with single responsibilities | full route snapshot diff | visual + JSON schema diff | restore single file |
| T-11 | Fix demo `auto` semantics | composer | BRIDGE | demo leakage risk (B-04) | T-10 | LOW | code | auto = real-if-exists-else-demo | empty/non-empty catalog matrix | home/shop/product data JSON diff | revert |
| T-12 | Fix 404 fallback to `404.html` | `ferm-page.php` | BRIDGE | dead fallback path (B-05) | none | LOW | code | /nonexistent serves pack 404 | request unknown slug | route table re-run | revert |
| T-13 | Consolidate cart surfaces | composer, `aether-cart.php`, shims | BRIDGE | triple cart systems (B-06) | T-10 | MEDIUM | refactor | single source for badge/count/drawer/page | cart E2E matrix (add/update/remove/refresh/bfcache) | cart JSON snapshots | revert |
| T-14 | Single VinetaPageData injection | composer | BRIDGE | double injection (J3) | T-10 | LOW | code | one injection point | assert single global definition | page source diff | revert |
| T-15 | Selector-contract test suite | new `frontend/tests/contracts` | TEST | fragile DOM contracts (B-03) | T-05 | LOW | tests | CI fails if contract selectors missing | run suite | — | delete dir |
| T-16 | Newsletter REST/export permission audit + fix | `inc/aether-newsletter.php` | CORE | security (B-08) | none | LOW | code | unauthenticated export 403; REST requires consent | unauthorized-request tests | subscribe flow E2E | revert |

## P2 — Hardening

| ID | Task | Files | Why | Risk | Test | Rollback |
|---|---|---|---|---|---|---|
| T-20 | Data-driven suppression registry | `inc/frontend.php` | manual handle list (C-01) | MEDIUM | asset audit per route | revert |
| T-21 | CSP + inline script reconciliation | `aether-security.php`, composer | J5 | MEDIUM | console error sweep | revert |
| T-22 | jQuery single-source | pack manifest | J1 | LOW | console/behavior sweep | revert |
| T-23 | bfcache badge refresh | shims | C-04 | LOW | back-navigation test | revert |
| T-24 | Purge ferm-era dead paths from Core | `ferm-page.php`, composer | C-05 | LOW | route smoke | revert |
| T-25 | Remove dead pack templates/assets | pack dir | C-06 | LOW | route smoke | archives |
| T-26 | Unify icon systems | pack css | C-07 | LOW | visual diff | revert |
| T-27 | WP site icon integration | `inc/frontend.php` | C-08 | LOW | favicon assertions | revert |
| T-28 | Account endpoint plugin-hook zones (decision-dependent) | `myaccount/my-account.php` | B-07 | MEDIUM | plugin compat matrix | revert |

## P3 — Optional
- Re-enable luxury engine E2E as Core regression harness (depends on T-03).
- i18n pass on pack strings. · Automated a11y (axe) in CI. · Visual-regression baseline per viewport.

## Execution order & gates

T-01 → T-02 → T-04/T-05 → T-03 → (T-10 → T-11/T-12/T-13/T-14 parallel) → T-15 → T-16 → P2 batch.

**Gate rule:** no P1/P2 task starts before T-03+T-05 are done (prevents more drift while refactoring).

## Explicitly NOT in scope without new decisions (see QUESTIONS.md)
- Choosing new plugins (SEO/payments/SMTP). · Redesigning pack visuals. · Removing luxury engine entirely. · DB/schema changes beyond newsletter audit.
