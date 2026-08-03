# MASTER ROADMAP — Phantom Theme / Phantom Core

> **Single source of truth** for the Phantom project. Tracks every completed, planned, and blocked phase, dependency ordering, acceptance criteria, status, and architectural decision records (ADRs).

| Field | Value |
|---|---|
| Project | Phantom Theme (custom frontend framework atop GeneratePress 3.6.1 + GP Premium 2.5.6) |
| Core deliverable | **Phantom Core** — framework layer (service container, tokens, render engine, component registry, bridges) |
| Root scope | `wp-content/themes/phantom` (child theme) — GP + GP Premium remain untouched |
| Last updated | 2026-08-03 |
| Status legend | `Not Started` · `In Progress` · `Completed` · `Blocked` |

---

## How to use this document

- Each phase row links to its detailed plan/PRD in `Report/`.
- `ADRs` section records every major architectural decision with rationale.
- Update status + last-updated date when a phase starts or finishes.
- Phase dependencies are explicit — never start a phase whose dependencies are not `Completed`.

---

## Phase Dependency Graph

```
Phase 0 (Foundation)
   ↓
Phase 1 (Bootstrap)
   ↓
Phase 2 (Framework Infrastructure)
   ↓
Phase 3 (Design Token Engine)
   ↓
Phase 4 (Render Engine)
   ↓
Phase 5 (Component Registry) ──→ Phase 6 (Template Engine) ──→ Phase 11 (Frontend Components) ──→ Phase 12 (Frontend Templates)
   ↓                                                                                                    ↑
Phase 7 (Asset Pipeline) ──────── (supplies built CSS/JS to 4,5,6,11,12,10)
   ↓
Phase 8 (Plugin Bridges) ─────────────────────────────────┐
Phase 9 (WooCommerce Bridge) ──┐                          │
Phase 10 (Animation Engine) ───┼── supplies capabilities ─┤
                              │                          │
Phase 13 (Performance) ────────┤                          │
Phase 14 (Accessibility) ──────┤                          │
                              ▼                          ▼
Phase 15 (Testing) → Phase 16 (Rebranding plan only) → Phase 17 (Release)
```

---

## Phase Status Table

| # | Phase | Status | Dependencies | Acceptance criteria (link to section) | Plan doc |
|---|---|---|---|---|---|
| 0 | Project Foundation | `Completed` (2026-08-03) | — | `PHASE_5_IMPLEMENTATION_PLAN.md` §Phase 0 — report: `wp-content/themes/phantom/docs/PHASE_0_VERIFICATION_REPORT.md` | ↑ link |
| 1 | Bootstrap | `Completed` (2026-08-03) | 0 | §Phase 1 — report: `wp-content/themes/phantom/docs/PHASE_1_VERIFICATION_REPORT.md` | ↑ |
| 2 | Framework Infrastructure | `Not Started` | 1 | §Phase 2 | ↑ |
| 3 | Design Token Engine | `Not Started` | 1, 2 | §Phase 3 | ↑ |
| 4 | Render Engine | `Not Started` | 2, 3 | §Phase 4 | ↑ |
| 5 | Component Registry | `Not Started` | 2, 4 | §Phase 5 | ↑ |
| 6 | Template Engine | `Not Started` | 5 | §Phase 6 | ↑ |
| 7 | Asset Pipeline | `Not Started` | 0, 1 | §Phase 7 | ↑ |
| 8 | Plugin Bridges | `Not Started` | 2, 4 | §Phase 8 | ↑ |
| 9 | WooCommerce Bridge | `Not Started` | 2, 4, 8 | §Phase 9 | ↑ |
| 10 | Animation Engine | `Not Started` | 3, 4, 7 | §Phase 10 | ↑ |
| 11 | Frontend Components | `Not Started` | 5, 10 | §Phase 11 | ↑ |
| 12 | Frontend Templates | `Not Started` | 6, 11 | §Phase 12 | ↑ |
| 13 | Performance | `Not Started` | 4,7,10,11,12 | §Phase 13 | ↑ |
| 14 | Accessibility | `Not Started` | 3,4,11,12 | §Phase 14 | ↑ |
| 15 | Testing | `Not Started` | 1–14 | §Phase 15 | ↑ |
| 16 | Rebranding (plan only) | `Not Started` | all | §Phase 16 | ↑ |
| 17 | Release | `Not Started` | 15, 16 | §Phase 17 | ↑ |

> Full acceptance criteria live in the per-phase sections of `PHASE_5_PHANTOM_CORE_IMPLEMENTATION_PLAN.md` — the arrow "↑" in the Acceptance column points to that document.

---

## Phase Deliverables

| Phase | Deliverable | Location | Status |
|---|---|---|---|
| 0 | Project Foundation — structure, PSR-4, tooling, CI, integrity gate | `wp-content/themes/phantom/` (repo `main`, 2 commits) | `Completed` 2026-08-03 |
| 0 | Verification report (APPROVED FOR PHASE 1) | `wp-content/themes/phantom/docs/PHASE_0_VERIFICATION_REPORT.md` | `Completed` |
| 1 | Bootstrap — boot sequence, env/config/feature-flag/logger/error-handler services, App facade, smoke suite | `wp-content/themes/phantom/app/{Boot,Config,Core,Support}` · `bin/smoke-phase1.php` | `Completed` 2026-08-03 |
| 1 | Verification report (APPROVED FOR PHASE 2) | `wp-content/themes/phantom/docs/PHASE_1_VERIFICATION_REPORT.md` | `Completed` |

---

## Completed Work (source-of-truth inputs — NOT repeated)

| # | Deliverable | Report path | Status |
|---|---|---|---|
| A0 | Phase-1 forensic / package validation + 12-phase audit | `Report/gp_audit_report.md` · `Report/phases/01-12-*.md` | `Completed` |
| A1 | Second-stage enterprise forensic verification | `Report/second_stage_forensic_report.md` | `Completed` |
| A2 | Complete engineering review (incl. score fix) | `Report/complete_engineering_review_report.md` | `Completed` |
| A3 | Master architecture & frontend replacement blueprint | `Report/master_architecture_blueprint.md` | `Completed` |
| A4 | Phase-4 blueprint summary | `Report/PHASE_4_MASTER_ARCHITECTURE_SUMMARY.md` | `Completed` |

> These are the project's source of truth. The implementation plan builds ON them and never re-runs them.

---

## Architectural Decision Records (ADR)

| ADR ID | Decision | Status | Rationale |
|---|---|---|---|
| ADR-001 | Phantom begins as a **child theme** of GeneratePress | `Accepted` | Keeps GP + GP Premium + WooCommerce/template-hierarchy compatibility; standalone theme would lose GP Premium (hard template check); plugin-only cannot win template hierarchy. |
| ADR-002 | **Namespaces**: `Phantom\Core`, function prefix `phantom_`, option prefix `phantom_`, hooks `phantom_{event}`, handles `phantom-*` | `Accepted` | Collision-safe, grep-safe, matches WP Core conventions (`wporg_` style). |
| ADR-003 | **No magic numbers**: all presentational values are design tokens (CSS custom properties) | `Accepted` | Token-driven theming; a11y & theme presets become data changes only. |
| ADR-004 | Use only **public WP/GP APIs**; `get_stylesheet_directory()` not `get_theme_roots()` (documented) | `Accepted` | Update safety; CI hash gate on GP/Premium dirs |
| ADR-005 | **Vite** build for assets (SCSS→CSS, ES modules), JSON **manifest** + `php-vite` style asset loader | `Accepted` | Dev HMR, prod cache busting, no runtime build step. |
| ADR-006 | Event/hook names: singular `phantom_core:boot`, double-colon namespace | `Accepted` | Unambiguous bus vs WP-action separation. |
| ADR-007 | Bridges are capability adapters; plugins stay untouched | `Accepted` | Every `woocommerce_*` hook re-emitted; adapters isolate vendor specifics. |
| ADR-008 | **Rebranding is planning-only** (no implementation until all CSS/IDs/options audited) | `Accepted` | See Phase 16. |
| ADR-009 | PSR-4 autoload via Composer `Phantom\Core` → `app/` | `Accepted` | No external PHP runtime deps in the child theme; standard Composer autoload. |
| ADR-010 | Caching: WP Transients + object-cache abstraction, tagged keys | `Accepted` | Tag/slug scoped, purgeable, update-safe |
| ADR-011 | Environment: `wp_get_environment_type()` + `phantom.env.json` overrides | `Accepted` | Standard WP detection, feature flags, debug mode. |
| ADR-012 | PHPCS + PHPStan (level 5); tests via `WP_Mock`/`Brain Monkey` + Playwright (Chromium) | `Accepted` | See Phase 15. |
| ADR-013 | Phase 1 Bootstrap architecture — `load.php` entry, Kernel/Sequencer lifecycle, App facade, immutable config, smoke suite | `Accepted` | Full record: `docs/architecture/ADR/ADR-013.md`. |

> **Future ADRs go here — always append, never rewrite history.**

---

## Release / versioning policy

- Semantic versioning: `0.1.0` … `1.x` — pre-1.0: any change may be a breaking change.
- Child theme version lives in `style.css` header + `composer.json`; npm package version for `assets-src`.
- Every phase ships behind a **feature flag** (`phantom_feature_*`) so it can be toggled per environment.
- `MASTER_ROADMAP.md` status changes require a PR with the linked plan diff.