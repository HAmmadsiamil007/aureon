# MASTER ROADMAP — Lumina Theme / Lumina Core

> **Single source of truth** for the Lumina project. Tracks every completed, planned, and blocked phase, dependency ordering, acceptance criteria, status, and architectural decision records (ADRs).

| Field | Value |
|---|---|
| Project | Lumina Theme (standalone premium frontend framework for WordPress) |
| Core deliverable | **Lumina Core** — framework layer (service container, tokens, render engine, component registry, bridges) + **Lumina Companion** plugin |
| Root scope | `wp-content/themes/lumina` (standalone theme, no parent) + `wp-content/plugins/lumina-companion` |
| Last updated | 2026-08-04 |
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
| 2 | Framework Infrastructure | `Completed` (2026-08-03) | 1 | §Phase 2 — report: `wp-content/themes/phantom/docs/PHASE_2_VERIFICATION_REPORT.md` | ↑ |
| 3 | Design Token Engine | `Completed` (2026-08-03) | 1, 2 | §Phase 3 — report: `wp-content/themes/phantom/docs/PHASE_3_VERIFICATION_REPORT.md` | ↑ |
| 4 | Render Engine | `Completed` (2026-08-04) | 2, 3 | §Phase 4 — report: `wp-content/themes/phantom/docs/PHASE_4_VERIFICATION_REPORT.md` | ↑ |
| 5 | Component Registry | `Completed` (2026-08-04) | 2, 4 | §Phase 5 — report: `wp-content/themes/phantom/docs/PHASE_5_VERIFICATION_REPORT.md` | ↑ |
| 6 | Template Engine | `Completed` (2026-08-04) | 5 | §Phase 6 — report: `wp-content/themes/phantom/docs/PHASE_6_VERIFICATION_REPORT.md` | ↑ |
| 7 | Asset Pipeline | `Completed` (2026-08-04) | 0, 1 | §Phase 7 — report: `wp-content/themes/phantom/docs/PHASE_7_VERIFICATION_REPORT.md` | ↑ |
| 8 | Plugin Bridges | `Completed` (2026-08-04) | 2, 4 | §Phase 8 — report: `wp-content/themes/phantom/docs/PHASE_8_VERIFICATION_REPORT.md` | ↑ |
| 9 | WooCommerce Bridge | `Completed` (2026-08-04) | 2, 4, 8 | §Phase 9 — report: `wp-content/themes/phantom/docs/PHASE_9_VERIFICATION_REPORT.md` | ↑ |
| 10 | Animation Engine | `Completed` (2026-08-04) | 3, 4, 7 | §Phase 10 — report: `wp-content/themes/phantom/docs/PHASE_10_VERIFICATION_REPORT.md` | ↑ |
| 11 | Frontend Components | `Completed` (2026-08-04) | 5, 10 | §Phase 11 — report: `wp-content/themes/phantom/docs/PHASE_11_VERIFICATION_REPORT.md` | ↑ |
| 12 | Frontend Templates | `Completed` (2026-08-04) | 6, 11 | §Phase 12 — report: `wp-content/themes/phantom/docs/PHASE_12_VERIFICATION_REPORT.md` | ↑ |
| 13 | Performance | `Completed` (2026-08-04) | 4,7,10,11,12 | §Phase 13 — report: `wp-content/themes/phantom/docs/PHASE_13_VERIFICATION_REPORT.md` | ↑ |
| 14 | Accessibility | `Completed` (2026-08-04) | 3,4,11,12 | §Phase 14 — report: `wp-content/themes/phantom/docs/PHASE_14_VERIFICATION_REPORT.md` | ↑ |
| 15 | QA, Validation & Production Readiness | `Completed` (2026-08-04) | 1–14 | §Phase 15 — report: `wp-content/themes/phantom/docs/PHASE_15_VERIFICATION_REPORT.md` | ↑ |
| 15.5 | Production Freeze & Release Candidate | `Completed` (2026-08-04) | 15 | §Phase 15.5 — report: `wp-content/themes/phantom/docs/PHASE_15_5_PRODUCTION_FREEZE_REPORT.md` | ↑ |
| 16 | Safe Rebranding — Lumina (standalone theme + companion plugin) | `Completed` (2026-08-04) | all | §Phase 16 — report: `wp-content/themes/lumina/docs/PHASE_16_VERIFICATION_REPORT.md` | ↑ |
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
| 2 | Framework Infrastructure — DI Container, Events, Hooks, Registries, Factory, Config Repository, Cache, Service Providers, Kernel integration | `wp-content/themes/phantom/app/{Container,Events,Hooks,Registry,Factory,Config,Cache,Providers}` · `bin/smoke-phase2.php` | `Completed` 2026-08-03 |
| 2 | Verification report (APPROVED FOR PHASE 3) | `wp-content/themes/phantom/docs/PHASE_2_VERIFICATION_REPORT.md` | `Completed` |
| 3 | Design Token Engine — TokenSource/Preced/Resolver/TokenFactory/CssRenderer/Invariant, presets, canonical groups | `wp-content/themes/phantom/app/Tokens/` · `bin/smoke-phase3.php` | `Completed` 2026-08-03 |
| 3 | Verification report (APPROVED FOR PHASE 4) | `wp-content/themes/phantom/docs/PHASE_3_VERIFICATION_REPORT.md` | `Completed` |
| 4 | Render Engine — Renderer/TemplateResolver/Layout/ViewModel/ViewContext/RenderCache/PhpTemplateEngine, Data adapters | `wp-content/themes/phantom/app/Render/` · `app/Data/` · `bin/smoke-phase4.php` | `Completed` 2026-08-04 |
| 4 | Verification report (APPROVED FOR PHASE 5) | `wp-content/themes/phantom/docs/PHASE_4_VERIFICATION_REPORT.md` | `Completed` |
| 5 | Component Registry — Registry/ComponentDefinition/Loader/DefinitionCompiler/CycleDetector/Resolver, JSON discovery, `[phantom:{slug}]` DSL | `wp-content/themes/phantom/app/Components/` · `bin/smoke-phase5.php` | `Completed` 2026-08-04 |
| 5 | Verification report (APPROVED FOR PHASE 6) | `wp-content/themes/phantom/docs/PHASE_5_VERIFICATION_REPORT.md` | `Completed` |
| 6 | Template System — hierarchy resolver, override tiers, partial loader, dynamic sections, View facade, template_include bridge | `wp-content/themes/phantom/app/Templates/` · `templates/single.php` · `bin/smoke-phase6.php` | `Completed` 2026-08-04 |
| 6 | Verification report (APPROVED FOR PHASE 7) | `wp-content/themes/phantom/docs/PHASE_6_VERIFICATION_REPORT.md` | `Completed` |
| 7 | Asset Pipeline — AssetLoader/ManifestReader/DevServer/BuildFingerprint/Image/Markup, Pipeline entries+deps, Vite build, static token emission | `wp-content/themes/phantom/app/Assets/` · `vite.config.js` · `bin/smoke-phase7.php` | `Completed` 2026-08-04 |
| 7 | Verification report (APPROVED FOR PHASE 8) | `wp-content/themes/phantom/docs/PHASE_7_VERIFICATION_REPORT.md` | `Completed` |
| 8 | Plugin Bridges — BridgeInterface/Registry/BridgeManager, FeatureMatrix/HealthCheck, 12 guarded capability adapters (ACF, Rank Math, Yoast, WPML, Polylang, Fluent Forms, Gravity, WPForms, BuddyPress, bbPress, LearnDash, TEC) | `wp-content/themes/phantom/app/Bridges/` · `docs/plugins.md` · `bin/smoke-phase8.php` | `Completed` 2026-08-04 |
| 8 | Verification report (APPROVED FOR PHASE 9) | `wp-content/themes/phantom/docs/PHASE_8_VERIFICATION_REPORT.md` | `Completed` |
| 9 | WooCommerce Bridge — WooBridge facade + Product/Cart/Checkout/Account/Order adapters (HPOS-safe), HookPreservation (30 hooks + account wildcard), Blocks-safe (legacy override off), analysis-only WC stubs | `wp-content/themes/phantom/app/Woo/` · `tests/stubs/woocommerce-stubs.php` · `bin/smoke-phase9.php` | `Completed` 2026-08-04 |
| 9 | Verification report (APPROVED FOR PHASE 10) | `wp-content/themes/phantom/docs/PHASE_9_VERIFICATION_REPORT.md` | `Completed` |
| 10 | Animation Engine — AnimationRegistry/Preset/Engine, ReducedMotion + CSS guard, Breaking budgets, Lenis/Three/Trigger facades, code-split animation entry (GSAP/Lenis/Three dynamic imports), conditional enqueue, canonical reveal preset | `wp-content/themes/phantom/app/Animation/` · `assets-src/ts/animation.ts` · `bin/smoke-phase10.php` | `Completed` 2026-08-04 |
| 10 | Verification report (APPROVED FOR PHASE 11) | `wp-content/themes/phantom/docs/PHASE_10_VERIFICATION_REPORT.md` | `Completed` |
| 11 | Frontend Component Library — 78-component JSON catalog on the Phase-5 registry, `templates/components/*.php` (ViewContext-only), token-driven `_components.scss` layer, vanilla delegated `components.ts` Vite entry, conditional enqueue, slot composition | `wp-content/themes/phantom/app/Components/config/components.json` · `templates/components/` · `assets-src/scss/_components.scss` · `assets-src/ts/components.ts` · `bin/smoke-phase11.php` | `Completed` 2026-08-04 |
| 11 | Verification report (APPROVED FOR PHASE 12) | `wp-content/themes/phantom/docs/PHASE_11_VERIFICATION_REPORT.md` | `Completed` |
| 12 | Frontend Template Library — Templates\Composer slug→region→component automap, canonical maps.php (23 slugs), thin templates/frontend/*.php delegating to View::compose, Woo via bridge only, custom.php filterable slug | `wp-content/themes/phantom/app/Templates/Composer.php` · `app/Templates/config/maps.php` · `templates/frontend/` · `bin/smoke-phase12.php` | `Completed` 2026-08-04 |
| 12 | Verification report (APPROVED FOR PHASE 13) | `wp-content/themes/phantom/docs/PHASE_12_VERIFICATION_REPORT.md` | `Completed` |
| 13 | Performance Engineering — Performance\Budget VO, BudgetLogger, QueryGuard (debug-only), Lazy deferred runner, CachePurger + `phantom_core:cache_purged` action, performance config + provider, zero production overhead | `wp-content/themes/phantom/app/Performance/` · `bin/smoke-phase13.php` | `Completed` 2026-08-04 |
| 13 | Verification report (APPROVED FOR PHASE 14) | `wp-content/themes/phantom/docs/PHASE_13_VERIFICATION_REPORT.md` | `Completed` |
| 14 | Accessibility Engineering — A11y\Checker static HTML audit, SkipLink via `wp_body_open`, DialogManager attribute contract + validation, provider + config, WP-free CI-verifiable | `wp-content/themes/phantom/app/A11y/` · `bin/smoke-phase14.php` | `Completed` 2026-08-04 |
| 14 | Verification report (APPROVED FOR PHASE 15) | `wp-content/themes/phantom/docs/PHASE_14_VERIFICATION_REPORT.md` | `Completed` |
| 15 | Release Candidate Quality Gate — full regression (425 assertions), static analysis, Vite build, security review, compatibility matrix, production readiness | `wp-content/themes/phantom/docs/PHASE_15_VERIFICATION_REPORT.md` · `docs/PRODUCTION_READINESS_REPORT.md` · `docs/COMPATIBILITY_MATRIX.md` · `docs/KNOWN_LIMITATIONS.md` · `docs/QUALITY_GATE_SUMMARY.md` | `Completed` 2026-08-04 |
| 15 | Verification report (APPROVED FOR PHASE 15.5) | `wp-content/themes/phantom/docs/PHASE_15_VERIFICATION_REPORT.md` | `Completed` |
| 15.5 | Production Freeze — RC1 (`v0.14.0`): API + contract freeze, dependency lockdown, reproducible build, version consistency (npm aligned), 7 release artifacts | `wp-content/themes/phantom/docs/PHASE_15_5_PRODUCTION_FREEZE_REPORT.md` · `docs/API_FREEZE.md` · `docs/CONTRACT_FREEZE.md` · `docs/DEPENDENCY_INVENTORY.md` · `docs/BUILD_REPRODUCIBILITY_REPORT.md` · `docs/RELEASE_CANDIDATE_REPORT.md` · `docs/FINAL_RISK_REGISTER.md` | `Completed` 2026-08-04 |
| 15.5 | Verification report (APPROVED FOR PHASE 16) | `wp-content/themes/phantom/docs/PHASE_15_5_PRODUCTION_FREEZE_REPORT.md` | `Completed` |

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
| ADR-014 | Phase 2 Framework Infrastructure — DI Container, Events, Hooks, Registries, Factory, Config Repository, Cache, Service Providers | `Accepted` | Full record: `docs/architecture/ADR/ADR-014.md`. |
| ADR-015 | Phase 3 Design Token Engine — TokenSource/Preced/Resolver/TokenFactory/CssRenderer/Invariant, presets, name-safety pattern | `Accepted` | Full record: `docs/architecture/ADR/ADR-015.md`. |
| ADR-016 | Phase 5 Component Registry — Registry/ComponentDefinition/Loader/DefinitionCompiler/CycleDetector/Resolver, JSON discovery, `[phantom:{slug}]` DSL | `Accepted` | Full record: `docs/architecture/ADR/ADR-016.md`. |
| ADR-017 | Phase 6 Template System — hierarchy resolver, override tiers, partial loader, sections, template_include bridge, `View` facade | `Accepted` | Full record: `docs/architecture/ADR/ADR-017.md`. |
| ADR-018 | Phase 7 Asset Pipeline — Vite entries + manifest reader, dev-server detection, hashed cache busting, static token emission | `Accepted` | Full record: `docs/architecture/ADR/ADR-018.md`. |
| ADR-019 | Phase 8 Plugin Bridges — BridgeInterface/Registry/BridgeManager, 12 guarded capability adapters, matrix + FeatureMatrix, HealthCheck | `Accepted` | Full record: `docs/architecture/ADR/ADR-019.md`. |
| ADR-020 | Phase 9 WooCommerce Bridge — WooBridge + Product/Cart/Checkout/Account/Order adapters (HPOS-safe), hook preservation, Blocks-safe, analysis-only WC stubs | `Accepted` | Full record: `docs/architecture/ADR/ADR-020.md`. |
| ADR-021 | Phase 10 Animation Engine — AnimationRegistry/Preset/Engine, ReducedMotion + CSS guard, Breaking budgets, Lenis/Three/Trigger facades, GSAP/Lenis/Three code-split dynamic imports, conditional enqueue | `Accepted` | Full record: `docs/architecture/ADR/ADR-021.md`. |
| ADR-022 | Phase 11 Frontend Component Library — 78-component JSON catalog on the Phase-5 registry, token-driven `_components.scss`, vanilla delegated `components.ts` Vite entry, conditional enqueue, ViewContext-only templates (WP-free parity) | `Accepted` | Full record: `docs/architecture/ADR/ADR-022.md`. |
| ADR-023 | Phase 12 Frontend Template Library — Templates\Composer slug→region→component automap, canonical maps.php (23 slugs), thin templates/frontend/*.php delegating to View::compose, Woo via bridge only, WP-free compose() parity | `Accepted` | Full record: `docs/architecture/ADR/ADR-023.md`. |
| ADR-024 | Phase 13 Performance Engineering — Performance\Budget VO, BudgetLogger, QueryGuard (debug-only), Lazy deferred runner, CachePurger + `phantom_core:cache_purged` action, performance config + provider, zero production overhead | `Accepted` | Full record: `docs/architecture/ADR/ADR-024.md`. |
| ADR-025 | Phase 14 Accessibility Engineering — A11y\Checker static HTML audit, SkipLink via `wp_body_open`, DialogManager attribute contract + validation, provider + config, WP-free CI-verifiable | `Accepted` | Full record: `docs/architecture/ADR/ADR-025.md`. |
| ADR-026 | Phase 16 Rebrand — `Lumina\Core` namespace, `lumina_`/`lumina-*` handles, `--lumina-*` tokens, `lumina_core:*` events, text domain `lumina`, v1.0.0 major bump (API_LEVEL 2), grep-zero gate | `Accepted` | Full record: `docs/architecture/ADR/ADR-026.md`. |
| ADR-027 | Phase 16 Standalone — parent `Template:` removed, original shell files, resolver parent tier dropped, region hooks, self-integrity gate | `Accepted` | Full record: `docs/architecture/ADR/ADR-027.md`. |
| ADR-028 | Phase 16 Lumina Companion plugin — original implementation, theme-gated, zero runtime deps, no commercial code copied | `Accepted` | Full record: `docs/architecture/ADR/ADR-028.md`. |

> **Future ADRs go here — always append, never rewrite history.**

---

## Release / versioning policy

- Semantic versioning: `0.1.0` … `1.x` — pre-1.0: any change may be a breaking change.
- Child theme version lives in `style.css` header + `composer.json`; npm package version for `assets-src`.
- Every phase ships behind a **feature flag** (`phantom_feature_*`) so it can be toggled per environment.
- `MASTER_ROADMAP.md` status changes require a PR with the linked plan diff.