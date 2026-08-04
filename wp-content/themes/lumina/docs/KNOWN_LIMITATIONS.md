# KNOWN LIMITATIONS — Lumina Theme / Lumina Core

- **Version:** 0.14.0 (RC)
- **Date:** 2026-08-04
- **Phase:** 15 (Release Candidate Quality Gate)

This document records every known limitation, deferred item, and accepted
trade-off at the Phase-15 release-candidate gate. **No Critical and no High
severity defects exist.** All items below are Low severity or planned scope
deferrals — none blocks the production freeze.

---

## 1. Defect-Class Limitations (all Low)

| #   | Area          | Limitation                                                                                               | Root cause                                                                         | Risk                                                                      | When to address                |
| --- | ------------- | -------------------------------------------------------------------------------------------------------- | ---------------------------------------------------------------------------------- | ------------------------------------------------------------------------- | ------------------------------ |
| 1   | Build tooling | `package.json` (npm) version is `0.1.0` while the theme version is `0.14.0`                              | Separate versioning domain for the frontend build toolchain established in Phase 0 | Negligible — npm version is not exposed to WordPress or users             | Phase 17 (release engineering) |
| 2   | Composer      | `composer.lock` records no root package `name`                                                           | Expected Composer 2 behavior (locks do not store the root name)                    | Negligible — `composer validate` passes in CI                             | None required                  |
| 3   | A11y audit    | `A11y\Checker` is a static HTML analyzer; it cannot detect dynamic/focus-trap behavior or color contrast | Design: deterministic, browser-free CI audit (ADR-025)                             | Low — runtime behavior covered by Phase-11 JS + Phase-15.5 browser audits | Phase 15.5 (browser-level)     |

## 2. Planned Verification Deferrals (Phase 15.5 — Production Freeze)

These require a live WordPress staging install and are scheduled for the
production freeze, per the sprint brief:

| Item                                                                     | Why deferred                                 |
| ------------------------------------------------------------------------ | -------------------------------------------- |
| Live Lighthouse / PageSpeed runs                                         | Requires a running WP site with real content |
| Core Web Vitals measurement (LCP/INP/CLS/FCP)                            | Same — needs browser instrumentation         |
| Screen-reader compatibility sweep (NVDA/VoiceOver)                       | Requires real assistive-tech environment     |
| Cross-browser device matrix (Chrome/Firefox/Safari/Edge, mobile, tablet) | Needs browser farm + staging data            |
| Visual regression baselines (header/footer/home/shop/product/checkout/…) | Needs rendered staging pages to snapshot     |
| WordPress beta/nightly compatibility check                               | Optional per brief; stable floor is 6.5      |

## 3. Accepted Trade-offs (design decisions, not defects)

| Trade-off                                                                           | Rationale                                                                                              |
| ----------------------------------------------------------------------------------- | ------------------------------------------------------------------------------------------------------ |
| PHP floor `^8.2` (8.1 not supported)                                                | Modern typing (readonly, enums, DNF) for static-analysis clarity; matches `Requires PHP: 8.2`          |
| No custom REST/AJAX endpoints                                                       | All interactivity is client-side progressive enhancement; zero admin surface = zero CSRF/nonce surface |
| Frontend behaviors are vanilla TS (no framework)                                    | Code-split lazy chunks, ~36 kB gzip total; no runtime framework cost                                   |
| GSAP/Lenis/Three loaded via dynamic import only when the animation engine is active | Zero cost when unused (Phase 10)                                                                       |
| No visual regression snapshot suite in repo yet                                     | Deferred to Phase 15.5 where staging pages exist                                                       |
| Templates render through the Composer (no per-template markup)                      | Single source of truth; templates are data maps (Phase 12)                                             |
| BudgetLogger default transport is in-memory                                         | Injectable; production telemetry hookup is a Phase-17 ops concern                                      |

## 4. Support Constraints

- **Supported:** WordPress ≥ 6.5 · PHP 8.2/8.3 · standalone (no parent theme)
  Premium 2.5.6 · MySQL/MariaDB · single site + multisite.
- **Out of scope:** PHP < 8.2; unsupported WP versions below 6.5; modifying
  parent/vendor code (never — ADR-004).
- **WooCommerce:** legacy override opt-in, OFF by default (Blocks-safe);
  HPOS supported through the single `wc_get_order()` read path.

## 5. Conclusion

Every limitation above is either negligible-risk or a planned,
already-scheduled deferral to the Phase-15.5 production freeze. There are no
open defects that affect functionality, security, update safety, or
compliance.

**No blocking items. ✅**
