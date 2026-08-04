# COMPATIBILITY MATRIX — Phantom Theme / Phantom Core

- **Version:** 0.14.0 (RC)
- **Date:** 2026-08-04
- **Phase:** 15 (Release Candidate Quality Gate)

> **Status legend:** `✅ verified` = executed/measured in this gate (CI,
> smoke suite, static analysis, build). `✅ design` = verified by code
> review / integrity / architecture only — no live runtime test occurred in
> Phase 15 (staging + browser tests are Phase 15.5). `⛔ n/a` = below the
> declared support floor or out of scope.

---

## 1. Platform Matrix

| Platform    | Version/Config | Status      | Notes                                                                                        |
| ----------- | -------------- | ----------- | -------------------------------------------------------------------------------------------- |
| PHP         | 8.1            | ⛔ n/a      | Below declared floor `^8.2` (composer.json + style.css) — not a defect                       |
| PHP         | 8.2            | ✅ verified | Verified locally (8.2.31) + CI (`shivammathur/setup-php` 8.2)                                |
| PHP         | 8.3            | ✅ design   | Above floor; CI runs 8.2 only — no 8.3 run executed, no 8.2-specific syntax beyond the floor |
| WordPress   | ≥ 6.5          | ✅ design   | `Requires at least: 6.5`; only 6.5+ public APIs used                                         |
| MySQL       | 5.7 / 8.x      | ✅ design   | WP API only — no raw SQL in theme code                                                       |
| MariaDB     | 10.x           | ✅ design   | Same WP API code path as MySQL                                                               |
| Environment | Single site    | ✅ design   | Primary target                                                                               |
| Environment | Multisite      | ✅ design   | No site-scoped assumptions; no `network_*` misuse                                            |

## 2. WordPress Core Integration

| Area                           | Status      | Evidence                                                                                             |
| ------------------------------ | ----------- | ---------------------------------------------------------------------------------------------------- |
| Template hierarchy             | ✅ verified | Phase-6 resolver + `template_include` bridge; 23 template slugs (smoke-phase6/12)                    |
| Customizer                     | ✅ verified | Not overridden — GP Customizer untouched (integrity 473/473)                                         |
| Menus / Navigation             | ✅ design   | Phase-11 header/nav components consume `Menu` data adapter                                           |
| Widgets                        | ✅ design   | Not overridden — GP widget areas untouched                                                           |
| Media Library                  | ✅ design   | `Assets\Image` srcset/build_srcset; lazy attrs                                                       |
| Block Editor                   | ✅ design   | No block-editor interference; theme is classic-agnostic                                              |
| Classic Editor                 | ✅ design   | Compatible (no Gutenberg-only dependency)                                                            |
| Shortcodes                     | ✅ verified | `[phantom:{slug}]` DSL (Phase-5 registry, smoke-phase5)                                              |
| Cron                           | ✅ design   | No custom cron jobs; no interference                                                                 |
| REST API                       | ✅ design   | No custom routes registered (no surface to break)                                                    |
| Theme activation               | ✅ design   | `functions.php` + `load.php` guarded by `! defined( 'ABSPATH' )`                                     |
| Theme switching                | ✅ design   | Clean switch — no persistent globals/options set on boot                                             |
| Theme updates                  | ✅ design   | Child theme pattern — parent updates safe (ADR-004)                                                  |
| Plugin activation/deactivation | ✅ design   | All bridge detection guarded (`function_exists`/`class_exists`)                                      |
| Localization                   | ✅ design   | `load_theme_textdomain`-compatible; plugin bridges WPML/Polylang-aware                               |
| Translation loading            | ✅ design   | Strings are plain text; WPML/Polylang bridges passive adapters                                       |
| RTL                            | ✅ design   | Token-driven layout; RTL-safe tokens (Phases 3/11); no LTR hardcoding — no live RTL test in Phase 15 |
| Child-theme compat             | ✅ design   | Phantom itself is the child theme; no deeper nesting assumptions                                     |

## 3. WooCommerce Compatibility (Phase 9 Bridge)

| Area                       | Status      | Notes                                                   |
| -------------------------- | ----------- | ------------------------------------------------------- |
| Shop                       | ✅ verified | Shop adapter via public WC APIs (smoke-phase9)          |
| Category                   | ✅ verified | Category archive data through Woo Bridge                |
| Product (simple)           | ✅ verified | ProductAdapter snapshot                                 |
| Variable products          | ✅ verified | Variation data via public WC API                        |
| Grouped products           | ✅ verified | Same adapter path                                       |
| Cart                       | ✅ verified | CartAdapter (items/totals/count/currency)               |
| Checkout                   | ✅ verified | CheckoutAdapter (fields schema + session order)         |
| Account                    | ✅ verified | AccountAdapter (nav/pages/user)                         |
| Orders                     | ✅ verified | OrderAdapter — HPOS-safe via `wc_get_order()`           |
| Coupons / Taxes / Shipping | ✅ design   | Preserved — no WC hooks removed (HookPreservation)      |
| Refunds                    | ✅ design   | Not overridden; WC default flow preserved               |
| HPOS                       | ✅ verified | Single HPOS-safe read path (legacy + HPOS)              |
| WooCommerce Blocks         | ✅ verified | Blocks-safe — legacy override OFF by default            |
| Emails                     | ✅ design   | WC email hooks untouched                                |
| Mini Cart / AJAX fragments | ✅ design   | WC fragment hooks preserved; no `woocommerce_*` removed |

> Note: adapter-level rows (`✅ verified`) are exercised by the WP-free
> smoke-phase9 suite with WC stubs. Runtime rows (`✅ design`) reflect hook
> preservation + no-override policy — no live WooCommerce store test
> occurred in Phase 15.

**Hook preservation:** 30-hook canonical table + `woocommerce_account_*`
wildcard audited (smoke-phase9) — every hook the architecture expects still
executes.

## 4. Plugin Bridge Compatibility (Phase 8)

| Plugin              | Bridge      | Absent-plugin behavior                        |
| ------------------- | ----------- | --------------------------------------------- |
| ACF                 | Acf         | ✅ verified — inactive adapter, safe defaults |
| Yoast SEO           | Yoast       | ✅ verified — same                            |
| Rank Math           | RankMath    | ✅ verified — same                            |
| WPML                | Wpml        | ✅ verified — same                            |
| Polylang            | Polylang    | ✅ verified — same                            |
| Gravity Forms       | Gravity     | ✅ verified — same                            |
| Fluent Forms        | FluentForms | ✅ verified — same                            |
| WPForms             | Wpforms     | ✅ verified — same                            |
| BuddyPress          | Buddypress  | ✅ verified — same                            |
| bbPress             | Bbpress     | ✅ verified — same                            |
| LearnDash           | Learndash   | ✅ verified — same                            |
| The Events Calendar | Tec         | ✅ verified — same                            |

All 12 adapters guard every vendor call; absent plugins yield inactive
bridges that never throw (smoke-phase8, 29 assertions).

## 5. Frontend Compatibility

| Area                  | Status      | Notes                                                                    |
| --------------------- | ----------- | ------------------------------------------------------------------------ |
| Responsive layouts    | ✅ design   | Token breakpoints + component media queries                              |
| Dark mode             | ✅ design   | `[data-phantom-theme="dark"]` preset (tokens)                            |
| Light mode            | ✅ design   | Default preset                                                           |
| RTL                   | ✅ design   | Token-driven spacing/flow; no directional hardcoding                     |
| Reduced motion        | ✅ verified | CSS guard + JS early exit + `Lazy::prefers_reduced_motion()`             |
| Animation             | ✅ verified | GSAP/Lenis/Three code-split; observer caps; idle cleanup (smoke-phase10) |
| Focus order           | ✅ verified | Skip link first; no positive tabindex (smoke-phase14)                    |
| Keyboard navigation   | ✅ design   | Tabs/accordions/modals/drawers keyboard-accessible                       |
| Rendering consistency | ✅ verified | All components render via one engine + ViewContext (smoke-phase4/11)     |

## 6. Browser / Device Targets

| Browser                       | Status    | Notes                                                 |
| ----------------------------- | --------- | ----------------------------------------------------- |
| Chrome                        | ✅ design | Primary target; Playwright Chromium run is Phase 15.5 |
| Firefox                       | ✅ design | Standards-only JS/CSS (no vendor prefixes required)   |
| Safari                        | ✅ design | ES2019+ output; no Safari-only APIs                   |
| Edge                          | ✅ design | Chromium engine                                       |
| Mobile Chrome / Mobile Safari | ✅ design | Responsive tokens; lazy media                         |

Browser-level device testing was **not executed** in Phase 15 — it is
scheduled for Phase 15.5 (staging install). The rows above are
design-level compatibility assessments only.

## 7. Constraint Summary

- **Supported floor:** WordPress ≥ 6.5 · PHP ≥ 8.2 · GeneratePress 3.6.1 ·
  GP Premium 2.5.6.
- **Not supported:** PHP < 8.2 (below declared floor).
- **Never modified:** GeneratePress, GP Premium, WooCommerce, WordPress Core,
  or any plugin (integrity gate enforced).
