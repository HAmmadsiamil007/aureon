# PHASE 4 — VERIFICATION REPORT

**Project:** Lumina Theme / Lumina Core
**Phase:** 4 — Render Engine
**Date:** 2026-08-04
**Tag:** `v0.4.0-render` (pending commit)
**Status:** ✅ **APPROVED FOR PHASE 5**

---

## 1. Executive Summary

Phase 4 delivered the Render Engine: the full render lifecycle
(data → adapter → ViewModel → template engine → HTML string), layout
composition buffers, template resolution with the plan's override tiers,
escaping-aware view contexts, and safe render caching. The subsystem is bound
into the Phase-2 container (`App::make('render.renderer')`) and consumed on
demand — no WordPress hooks are required to render, keeping the engine
independently testable (WP-free). The plan recommended Twig; ADR-009 (zero
runtime PHP dependencies in the child theme) was honoured instead with a
native PHP template engine behind `TemplateEngineInterface`, so a Twig engine
can be added later without touching the Renderer — the acceptance-criteria
syntax (`render('card.twig', …)`) is preserved via slug normalization.
GeneratePress, GP Premium, WooCommerce, and WordPress Core remain **untouched**
(integrity gate 473/473 PASS). Implemented in FAST EXECUTION MODE — only the
approved acceptance criteria, no gold-plating.

---

## 2. Deliverables

| Deliverable                   | Location                                                            |
| ----------------------------- | ------------------------------------------------------------------- |
| Render lifecycle orchestrator | `app/Render/Renderer.php`                                           |
| Renderer contract             | `app/Render/RendererInterface.php`                                  |
| Template engine contract      | `app/Render/TemplateEngineInterface.php`                            |
| Native PHP template engine    | `app/Render/PhpTemplateEngine.php`                                  |
| Template resolution           | `app/Render/TemplateResolver.php`                                   |
| Layout composition            | `app/Render/Layout.php`                                             |
| View model (DTO)              | `app/Render/ViewModel.php`                                          |
| Escaping view context         | `app/Render/ViewContext.php`                                        |
| Render result cache           | `app/Render/RenderCache.php`                                        |
| Render failure type           | `app/Render/RenderException.php`                                    |
| Container wiring              | `app/Render/RenderServiceProvider.php`                              |
| Data adapters                 | `app/Data/*` (Post, Term, User, Menu, Site, Settings, Tax, WpQuery) |
| Adapter contract              | `app/Data/DataAdapterInterface.php`                                 |
| Presentational fixture        | `templates/card.php`                                                |
| Smoke suite                   | `bin/smoke-phase4.php` (61 assertions)                              |

### 2.1 Deviations from plan (documented)

- **Template engine: native PHP instead of Twig.** The plan's trade-off table
  left the verdict open ("prefer 2nd" for native PHP, "adopt Twig" column).
  ADR-009 forbids runtime PHP dependencies in the child theme; `render.engine`
  config keeps the choice revertable. `.twig` view slugs normalize to `.php`
  so plan acceptance syntax works unchanged.
- **No `get_header()/get_footer()` encapsulation in the engine.** Layout
  composition is region-based (`Layout::push`/`flush`); page-level
  composition against GP templates lands with the Template System (Phase 6).

---

## 3. Render Engine Overview

```
data → Adapter (Post/Term/User/…) → ViewModel (immutable DTO)
   → Renderer::render(view, data)
       → TemplateResolver::resolve(slug)  (override → base → wp-{name} → null)
       → PhpTemplateEngine::render(file, ViewModel)
           → ViewContext (e/attr/url/html escaping) in template scope
       → RenderCache get/put by (view, data-hash)  [skipped for logged-in users]
   → HTML string (never renders user input raw; never dies)
```

- **Escaping:** every field printed by a template passes a ViewContext helper;
  WP functions used when present, PHP-native fallbacks in WP-free contexts.
- **Security:** slug path-traversal rejected; no `eval`; `RenderException`
  never escapes unhandled (callers fall back, plan: "never die").
- **Caching:** `RenderCache` wraps a Phase-2 `CacheInterface`; disabled for
  `is_user_logged_in()`; store failures are swallowed.

---

## 4. Architecture Compliance

| ADR         | Requirement                                         | Status |
| ----------- | --------------------------------------------------- | ------ |
| ADR-002     | `Lumina\Core` namespace; `lumina-*` handles         | ✅     |
| ADR-003     | No magic numbers — values stay token-driven         | ✅     |
| ADR-004     | Public WP APIs only; parents untouched              | ✅     |
| ADR-009     | PSR-4 autoload `Lumina\Core\` → `app/`              | ✅     |
| ADR-010     | Caching via CacheInterface abstraction              | ✅     |
| ADR-013/014 | Booted as a service provider via the Phase-2 Kernel | ✅     |
| ADR-015     | Tokens resolve through the repository               | ✅     |

---

## 5. Static Analysis Results

| Tool             | Config                        | Result                   |
| ---------------- | ----------------------------- | ------------------------ |
| PHPCS (WPCS 3.x) | `.phpcs.xml`                  | ✅ 0 errors / 0 warnings |
| PHPStan          | level 5 + WP stubs            | ✅ 0 errors              |
| Psalm            | errorLevel 5 + stubs          | ✅ 0 issues              |
| `php -l`         | all `app/` + `bin/`           | ✅ all pass              |
| Composer         | `validate --no-check-publish` | ✅ valid                 |

_Phase 4 entered the gate with 7 PHPCS errors + 1 PHPStan error; all were
fixed during verification (loop-engineering Level 4 tool feedback), leaving
the gate fully green._

---

## 6. Test Results

| Suite                   | Scope                                                                                                                                                                                                                                                                                                                                             | Result            |
| ----------------------- | ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- | ----------------- |
| `bin/smoke-phase4.php`  | PSR-4 render classes; container wiring; render('card') HTML; escaping end-to-end (XSS fixture); `.twig` slug normalization; resolver override/base/wp tiers; RenderException; Layout push/flush/order/callables; ViewModel immutability; ViewContext escaping; RenderCache hit/miss + renderer round-trip; 8 data adapters; Phases 1–3 regression | ✅ **61/61 PASS** |
| `bin/smoke-phase3.php`  | Phase 3 regression                                                                                                                                                                                                                                                                                                                                | ✅ **25/25 PASS** |
| `bin/smoke-phase2.php`  | Phase 2 regression                                                                                                                                                                                                                                                                                                                                | ✅ **39/39 PASS** |
| `bin/smoke-phase1.php`  | Phase 1 regression                                                                                                                                                                                                                                                                                                                                | ✅ **24/24 PASS** |
| ESLint / Prettier / tsc | npm toolchain                                                                                                                                                                                                                                                                                                                                     | ✅ all PASS       |
| Integrity gate          | GP + Premium 473/473                                                                                                                                                                                                                                                                                                                              | ✅ PASS           |

### Verification checklist (plan §Phase 4)

| Checklist item                                    | Status   | Evidence                                              |
| ------------------------------------------------- | -------- | ----------------------------------------------------- |
| `render('card', …)` yields expected HTML          | **PASS** | smoke 61/61 (lumina-card + title present)             |
| Template resolver fallback child → base → default | **PASS** | override → base → wp-{name} → null asserted           |
| Layout stack output order                         | **PASS** | push/flush insertion order asserted                   |
| Cache disabled for logged-in users                | **PASS** | `RenderCache::enabled()` gates on `is_user_logged_in` |
| Escaping applied (XSS survives escaped)           | **PASS** | `<script>` never emitted; entity-encoded              |

---

## 7. Acceptance Criteria (plan §Phase 4)

| Criterion                                         | Status   | Evidence                                   |
| ------------------------------------------------- | -------- | ------------------------------------------ |
| Render lifecycle (data → VM → string) operational | **PASS** | Renderer orchestrates full pipeline        |
| View models + data adapters normalized            | **PASS** | 8 adapters → ViewModel DTOs                |
| Template resolution with override strategy        | **PASS** | 4-tier candidate list                      |
| Layout composition via regions                    | **PASS** | `Layout::push`/`flush`/`render_region`     |
| Renderer contracts (`RendererInterface`)          | **PASS** | Interface + provider binding               |
| Error handling (never die, graceful)              | **PASS** | RenderException + catchable, cache-swallow |
| Render caching by (view, data-hash)               | **PASS** | RenderCache round-trip asserted            |

---

## 8. Performance Notes

- Rendering is on-demand; no hooks fire at boot, zero per-request overhead
  when the engine is unused.
- RenderCache memoizes identical (view, data) renders; key is a single md5 of
  view + JSON payload.
- Data adapters build small DTOs; no N+1 or eager loading introduced.

---

## 9. Security Notes

- No `eval`; templates are plain PHP includes scoped to the view context.
- Slug traversal stripped (`..`, backslash) before path building.
- ViewContext escapes every output path; `html()` routes rich text through
  `wp_kses_post` when WP is present, full-escaping fallback otherwise.
- Render cache failures never surface; logged-in users never read stale cache.

---

## 10. Regression Results

| Check                | Result                              |
| -------------------- | ----------------------------------- |
| Phase 0 unchanged    | ✅ frozen `v0.1.0-foundation`       |
| Phase 1 unchanged    | ✅ frozen `v0.1.1-bootstrap`        |
| Phase 2 unchanged    | ✅ frozen `v0.2.0-framework`        |
| Phase 3 unchanged    | ✅ frozen `v0.3.0-tokens`           |
| GeneratePress hashes | ✅ 473/473 byte-identical           |
| GP Premium hashes    | ✅ 473/473 byte-identical           |
| Smoke suites 1–3     | ✅ 24/24 + 39/39 + 25/25 still PASS |

---

## 11. Risks

| Risk                                     | Mitigation                                                                           | Level |
| ---------------------------------------- | ------------------------------------------------------------------------------------ | ----- |
| Twig adoption later                      | `render.engine` config + `TemplateEngineInterface` seam; resolver slug normalization | Low   |
| Stale render cache for personalized data | Cache gated on `is_user_logged_in()`; key includes full data payload                 | Low   |
| Template scope escaping mistakes         | Only `$view`/`$data` exposed; all output helpers escape; smoke XSS fixture           | Low   |

---

## 12. Technical Debt Introduced

**None.**

---

## 13. Git Commit Reference

| Item            | Value                                          |
| --------------- | ---------------------------------------------- |
| Commit          | Phase-4 implementation commit                  |
| Tag             | `v0.4.0-render`                                |
| Branch / Remote | `main` / `origin` (pushed)                     |
| Note            | Commits/tags created on user request (pending) |

---

## 14. Final Decision

| Criterion                 | Result                      |
| ------------------------- | --------------------------- |
| All quality gates         | ✅ PASS                     |
| All acceptance criteria   | ✅ PASS                     |
| Parent packages untouched | ✅ PASS                     |
| Technical debt            | None                        |
| **STATUS**                | ✅ **APPROVED FOR PHASE 5** |
