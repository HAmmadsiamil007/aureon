# Changelog

All notable changes to the Lumina theme / Lumina Core are documented here.
Format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/);
versioning follows SemVer (see `docs/versions.md`).

## [1.0.1] — 2026-08-04 — Phase 16.5: Full premium-module parity + provenance audit

Tag: `v1.0.1-lumina` · Companion plugin expands from 8 to **17 modules**,
covering the complete premium-theme surface (GP-Premium-style feature parity,
all original code). Provenance & licensing audit documented.

### Added (Companion plugin — 9 new modules, all original)

- `Colors` — per-element color overrides as `--lumina-color-*` tokens.
- `Backgrounds` — body/content/footer colors + body background image layer.
- `Blog` — archive columns, featured images, excerpts, meta, read-more.
- `Copyright` — footer copyright text/bar with removal option.
- `DisableElements` — hide header/footer/page-title globally or per post.
- `Elements` — reusable content blocks placed on Lumina region hooks (CPT).
- `FontLibrary` — Google-font enqueueing + `--lumina-font-*` families.
- `Hooks` — HTML/script injection at 7 public hook points.
- `General` — layout, container, sidebar, tagline, back-to-top.

### Changed

- Plugin customizer section (`lumina_companion`) now created before controls.
- Elements module wired to region hooks via the plugin facade.
- Plugin smoke suite: 17-module registry + conditional-module CSS coverage
  (WP-free `get_option` stub) → 26 assertions.
- Integration suite: 17-module assertion.
- Docs: `PROVENANCE_LICENSING_AUDIT.md`, `FEATURE_COMPLETENESS_MATRIX.md`.
- Readmes/composer description updated to the 17-module surface.

### Validated

- Plugin PHPCS 0, PHPStan 0; theme suite regression green; shipped ZIPs
  rebuilt (plugin 25 files / 39 KiB) and re-verified — 0 forbidden refs.

## [1.0.0] — 2026-08-04 — Phase 16: Standalone theme + companion plugin

Tag: `v1.0.0-lumina` · Phase 16 — Lumina ships as a **fully standalone theme**
with the original **Lumina Companion** plugin. Major bump: 1.0.0 freezes the
public API and contract surface (ADR-026–028).

### Added

- Standalone theme shell — original `header.php`, `footer.php`, `index.php`,
  `single.php`, `page.php`, `archive.php`, `search.php`, `404.php`,
  `comments.php`, `searchform.php` (ADR-027).
- `wp-content/plugins/lumina-companion/` — original companion plugin:
  Spacing, Typography, Page Header, Secondary Nav, Menu Plus, Sections, Site
  Library, WooCommerce modules; theme-gated, zero runtime deps (ADR-028).
- `bin/smoke-phase16-plugin.php` (17 assertions) +
  `bin/smoke-phase16-integration.php` (16 assertions) — WP-free.
- `bin/verify-lumina-integrity.sh` — Lumina self-integrity gate.
- `docs/architecture/ADR/ADR-026.md` … `ADR-028.md`.

### Changed

- `TemplateResolver` parent fallback tier removed; `Template:` header
  removed from `style.css`.
- Composer package `lumina/lumina`; `Version::API_LEVEL` 2.
- CI: working dir `wp-content/themes/lumina`; integrity job now runs the
  Lumina self-gate; plugin + integration suites added.

## [0.14.0] — 2026-08-04 — Phases 15 + 15.5: Release Candidate & Production Freeze (RC1)

Tag: `v0.14.0` (RC1 — FINAL RELEASE CANDIDATE) · Phases 15 (Enterprise QA /
Release Candidate) + 15.5 (Production Freeze) — Phase 15.5 is strict
zero-feature mode; frozen Phases 0–15 unchanged.

### Phase 15.5 — Production Freeze

### Added

- `docs/API_FREEZE.md` — frozen public API inventory (namespaces, container
  bindings, facades, events, hooks/filters, shortcodes, tokens/CSS vars,
  config schema, JS APIs, filesystem + manifest structure).
- `docs/CONTRACT_FREEZE.md` — frozen behavioral contracts (component,
  render, template, adapters, provider lifecycle, bridges, Woo bridge,
  animation, performance, accessibility).
- `docs/DEPENDENCY_INVENTORY.md` — locked dependency inventory (PHP ^8.2,
  WP ≥ 6.5, 40 dev-only Composer packages, 12 npm direct + locked
  transitives, 12 bridge targets, Woo HPOS/Blocks).
- `docs/BUILD_REPRODUCIBILITY_REPORT.md` — two-build md5 comparison:
  byte-identical artifacts (8 files) → deterministic build.
- `docs/RELEASE_CANDIDATE_REPORT.md` — RC process documented; RC1 declared
  FINAL (0 Critical/High → no RC2).
- `docs/FINAL_RISK_REGISTER.md` — 0 Critical/High/Medium, 4 Low, 3 Info.
- `docs/PHASE_15_5_PRODUCTION_FREEZE_REPORT.md` — full freeze report →
  APPROVED FOR PHASE 16.

### Changed

- Version consistency: `package.json` + `package-lock.json` aligned
  `0.1.0` → `0.14.0` (root + `packages[""]`; `yocto-queue` dependency
  version untouched) — only allowed change this phase.
- `Report/MASTER_ROADMAP.md` — Phase 15.5 status.

### Validated (no code changed)

- Full release regression re-run: 14/14 smoke suites (425 assertions, 0
  failures), integrity 473/473, PHPCS/PHPStan/Psalm clean, npm check clean,
  Vite build deterministic, security re-scan clean, performance budgets
  met, a11y 42/42.

### Phase 15 — Release Candidate (details below)

Tag: `v0.14.0` (RC) · Phase 15 — Enterprise QA, Validation & Production
Readiness (no features; release-qualification only; frozen Phases 0–14
unchanged)

### Added

- `docs/PHASE_15_VERIFICATION_REPORT.md` — release-candidate quality gate:
  environment matrix, 14-suite regression (425 assertions, 0 failures),
  static-analysis results, Vite production build, security review, issue
  log, acceptance checklist → APPROVED FOR PHASE 15.5.
- `docs/PRODUCTION_READINESS_REPORT.md` — 10/10 release-readiness
  checklist; update safety (473/473), zero runtime deps, security posture,
  performance/a11y readiness.
- `docs/COMPATIBILITY_MATRIX.md` — platform, WP Core, WooCommerce, plugin
  bridge, frontend, browser/device matrices with a `verified` vs `design`
  status legend (no overclaiming).
- `docs/KNOWN_LIMITATIONS.md` — 3 Low-severity items + planned Phase-15.5
  deferrals + accepted trade-offs; zero blocking items.
- `docs/QUALITY_GATE_SUMMARY.md` — 16/16 gates PASS with evidence table.

### Validated (no code changed)

- Regression: 14/14 smoke suites green (24/39/25/61/38/34/48/PASS/PASS/
  PASS/48/25/41/42).
- Static analysis: PHPCS 0 · PHPStan level 5 clean · Psalm clean · ESLint /
  Prettier / tsc clean · Vite production build (4 entries, gzip ~36 kB).
- Integrity: Lumina self-gate — shipped tree byte-identical to the frozen
  release baseline.
- Security review: zero secrets / eval / shell / superglobals / remote
  calls / admin surfaces; includes bounded to internal guarded paths.
- Version consistency: 0.14.0 across `Core\Version`, `style.css`,
  `composer.json`, smoke-phase1.

### Fixed

- Review hardening: PHP 8.3 matrix row no longer claims a CI run (8.3 not
  executed — now `✅ design`); compatibility matrix adds a verified-vs-
  design legend and marks browser/device/RTL/live-WC rows as design-level
  (no overstatement); WooCommerce matrix distinguishes adapter-verified vs
  hook-preservation rows.

## [0.14.0-a11y] — 2026-08-04

Tag: `v0.14.0-a11y` · Phase 14 — Accessibility Engineering (frozen
Phases 0–13 unchanged)

### Added

- `A11y\Checker` — deterministic static HTML audit (WP-free string
  analyzer): heading hierarchy (single h1, no skipped levels), landmarks
  (header/nav/main/footer), image alt attributes, form label coverage,
  interactive accessible names, focus hygiene (no positive tabindex),
  dialog focusability (`role="dialog"` must carry `tabindex="-1"`).
- `A11y\SkipLink` — static skip-to-content builder (`screen-reader-text
lumina-skip-link` → `#main`) with WP-first/PHP-fallback escaping;
  emitted via `wp_body_open` priority 1 when WordPress is present.
- `A11y\DialogManager` — required attribute contract (`role="dialog"`,
  `aria-modal="true"`, `tabindex="-1"`, `aria-labelledby`) + `validate()`;
  runtime focus trap stays in the Phase-11 `components.ts` entry.
- `A11y\A11yServiceProvider` — three lazy container bindings + guarded
  `wp_body_open` wiring.
- `bin/smoke-phase14.php` — WP-free Accessibility smoke suite (36
  assertions, incl. Phases 1–13 regression); added to CI.
- ADR-025 — Accessibility Engineering record.

### Changed

- Version 0.13.0 → 0.14.0 (`Core\Version`, `style.css`, `composer.json`,
  `composer.lock` content-hash); `bin/smoke-phase1.php` version assertion.
- `Config\config.php` — `accessibility` feature enabled +
  `A11yServiceProvider` in `providers`.
- `.github/workflows/ci.yml` — Phase 14 smoke step added.
- `docs/architecture/ADR/README.md`, `Report/MASTER_ROADMAP.md`.

### Fixed

- Static analysis hardening: WPCS docblock type hints aligned with native
  signatures (`Squiz.Commenting.FunctionComment.IncorrectTypeHint` —
  `list<string>` is not expressible natively, so docblocks use `array`);
  long dialog-check line wrapped; phpcs/phpcbf alignment pass over the new
  namespace.
- Review hardening: `Checker::check_forms` label-association finding was
  dead code ($ids merged every document id, so a control's own id always
  matched) — now only label `for=` refs are collected, so unreferenced ids
  are genuinely flagged; `check_focus` dialog check no longer depends on
  attribute order (tag-scoped `tabindex="-1"` test); `check_interactive`
  accepts an inner `<img alt>` as the accessible name (image-only links no
  longer false-positive); `check_headings` now flags documents with no h1;
  `A11yServiceProvider::boot()` skip-link emission is feature-gated on
  `features.accessibility`; smoke-phase14 grew to 42 assertions covering
  each fixed branch.

## [0.13.0-performance] — 2026-08-04

Tag: `v0.13.0-performance` · Phase 13 — Performance Engineering (frozen
Phases 0–12 unchanged)

### Added

- `Performance\Budget` — immutable Core Web Vitals + payload budget VO
  (LCP 2.0 s, CLS 0.05, INP 150 ms, JS 120 KB, CSS 50 KB, server 300 ms,
  queries 8) with stable accessors and `to_array()`; overridable via
  `performance.budgets` config.
- `Performance\BudgetLogger` — records metrics against the budget, reports
  pass/fail per key; injectable transport, WP-free in-memory default.
- `Performance\QueryGuard` — debug-only query introspection (counts queries
  on `pre_get_posts`, warns on budget breach; never blocks queries); off by
  default so production pays zero cost.
- `Performance\Lazy` — deferred-execution runner (shutdown when WordPress is
  present, immediate flush otherwise) so late work never blocks first paint.
- `Performance\CachePurger` — centralized cache purge firing the namespaced
  `lumina_core:cache_purged` action; `wp_cache_flush()` passthrough.
- `Performance\PerformanceServiceProvider` — five lazy container bindings +
  guarded `pre_get_posts` wiring for the query guard.
- `bin/smoke-phase13.php` — WP-free Performance smoke suite (41 assertions,
  incl. Phases 1–12 regression); added to CI.
- ADR-024 — Performance Engineering record.

### Changed

- Version 0.12.0 → 0.13.0 (`Core\Version`, `style.css`, `composer.json`,
  `composer.lock` content-hash); `bin/smoke-phase1.php` version assertion.
- `Config\config.php` — `performance` feature + `performance.{budgets,
query_guard}` config + `PerformanceServiceProvider` in `providers` (also
  pre-wires `A11yServiceProvider` for the Phase 14 provider list).
- `.github/workflows/ci.yml` — Phase 13 smoke step added.
- `docs/architecture/ADR/README.md`, `Report/MASTER_ROADMAP.md`.

### Fixed

- Static analysis hardening: WPCS docblock type hints aligned with native
  signatures (Squiz.Commenting.FunctionComment.IncorrectTypeHint); unused
  container param removed from the purger singleton; phpcs/phpcbf alignment
  pass over the new namespace.

## [0.12.0-templates] — 2026-08-04

Tag: `v0.12.0-templates` · Phase 12 — Frontend Template Library (frozen
Phases 0–11 unchanged)

### Added

- `Templates\Composer` — slug → region → component automap engine:
  `compose()`, `regions()`, `has()`, `slugs()`; props are static arrays or
  lazy callables `fn(array $data): array`. WP-free CLI-verifiable.
- `app/Templates/config/maps.php` — canonical composition map for 23 slugs
  (site shell, home/landing, commerce, blog/content, utility pages), every
  entry a registry component.
- `templates/frontend/{home,landing,shop,product,cart,checkout,thank-you,
account,wishlist,compare,blog,single-post,archive,author,search,404,
contact,about,faq,privacy,terms,custom}.php` — thin WordPress templates
  delegating entirely to `View::compose()`; data via `lumina_template_data`
  / `lumina_template_slug` filters. No markup, no business logic.
- `View::compose()` facade parity with the Composer (same pattern as
  `View::partial()`/`View::section()`); `templates.composer` container
  binding in `TemplatesServiceProvider`.
- `bin/smoke-phase12.php` — WP-free Template Library smoke suite (25
  assertions: inventory, template-file coverage, all-slug composition,
  escaping, no-`wc_*`/no-`get_template_part`/no-`WP_Query` map-check, mapped
  components registered, facade parity, Phases 1–11 regression); added to CI.
- ADR-023 — Frontend Template Library record.

### Changed

- Version 0.11.0 → 0.12.0 (`Core\Version`, `style.css`, `composer.json`,
  `composer.lock` content-hash); `bin/smoke-phase1.php` version assertion.
- `Config\config.php` — `features.template_library` flag enabled.
- `.github/workflows/ci.yml` — Phase 12 smoke step added.
- `docs/architecture/ADR/README.md`, `Report/MASTER_ROADMAP.md`.

### Fixed

- Provider map include path (`__DIR__` not `dirname(__DIR__)` — maps.php
  lives in `app/Templates/config/`); smoke-phase5 version-lifecycle
  assertions made self-contained (fresh registry) so the Phase-11 catalog
  evolution never breaks the Phase-5 regression suite.
- Review hardening: modal/popup dialogs now carry `tabindex="-1"` so focus
  actually enters the dialog (and restores on close); toggle panels (cart
  drawer / off-canvas) gain Escape-to-close + backdrop-click close;
  Composer prop resolution checks arrays before callables (a callable-shaped
  props array is never invoked); pagination supports per-page `page_urls`
  (falls back to `page_url`); counters animate into a dedicated number span
  so the styled suffix element survives; smoke-phase12 asserts `not-found`
  explicitly (dead `'404'` branch removed).

## [0.11.0-components] — 2026-08-04

Tag: `v0.11.0-components` · Phase 11 — Frontend Component Library (frozen
Phases 0–10 unchanged)

### Added

- Canonical 78-component catalog (`app/Components/config/components.json`) on
  the Phase-5 registry: shell/navigation, hero/banners, commerce catalog +
  interactive, cart/checkout, content/interactive, blog/footer, states — each
  with data schema, version, variants, slots, deps.
- 78 component templates (`templates/components/*.php`) rendered by the
  Phase-4 engine. Every template is `ViewContext`-only (`e`/`attr`/`url`),
  contains zero WordPress calls, and is WP-free CLI-verifiable.
- `assets-src/scss/_components.scss` — token-driven component layer: every
  value resolves to a `--lumina-*` token (no hardcoded colors/spacing/
  typography), dark-mode inherits `[data-lumina-theme="dark"]`, reduced
  motion respected, responsive breakpoints included.
- `assets-src/ts/components.ts` — vanilla, delegated, progressive-enhancement
  behavior entry (WAI-ARIA tabs, modal/popup + focus management, dismissible
  banners, counters, back-to-top, toggle panels, sticky add-to-cart);
  keyboard-accessible, aria-aware, reduced-motion-gated.
- Conditional enqueue in `ComponentsServiceProvider::boot()` — behaviors +
  styles load only when the registry is non-empty (zero cost unused);
  `vite.config.js` `components` entry (4th) code-splits the behaviors.
- Slot composition across the library (hero → button, footer → footer-columns
  - copyright, card → button, product-card → badges/actions).
- `bin/smoke-phase11.php` — WP-free Component Library smoke suite (48
  assertions, incl. Phases 1–10 regression); added to CI.
- ADR-022 — Frontend Component Library record.

### Changed

- Version 0.10.0 → 0.11.0 (`Core\Version`, `style.css`, `composer.json`,
  `composer.lock` content-hash); `bin/smoke-phase1.php` version assertion.
- `Config\config.php` — `features.component_library` flag enabled.
- `.github/workflows/ci.yml` — Phase 11 smoke step added.
- `docs/architecture/ADR/README.md`, `Report/MASTER_ROADMAP.md`.

### Fixed

- WP-free parity: `pagination`/`module-404`/`reviews` templates no longer call
  WP-only functions (`esc_url`/`home_url`/`number_format_i18n`) — all via
  `ViewContext` (WP-free smoke caught it).
- `footer` catalog slot list now includes `copyright` (declared dep, was not a
  slot), so the slot composes correctly without array-to-string conversion.
- Smoke fixture hardening: `product-card` badges fixture now passes label
  strings (template contract), eliminating the last array-to-string warning
  (verified `error_get_last()` is null at suite exit).

## [0.10.0-animation] — 2026-08-04

Tag: `v0.10.0-animation` · Phase 10 — Animation Engine (frozen Phases 0–9
unchanged)

### Added

- `Animation\AnimationRegistry` + `Animation\Preset` — named, immutable,
  allowlisted animation presets (name/type/target/options/scroll/decorative).
- `Animation\Engine` — controller: aggregates registry + gates into one
  serialized boot config; emits `lumina_core:animation:ready` when active.
- `Animation\ReducedMotion` — config-enforced gate (`motion.reduced` token)
  - inline CSS guard; `Animation\Breaking` — perf budgets (JS ≤ 120 KB,
    observer cap 40, will-change only while animating).
- `Animation\Lenis` (enable/disable smooth scroll), `Animation\Three`
  (`with_canvas()` mounts, lazy), `Animation\Scroll\Trigger` (declarative
  scroll triggers).
- `Animation\AnimationServiceProvider` — `animation.*` bindings + canonical
  `reveal` preset; conditional `wp_enqueue_scripts` enqueue of the animation
  entry + reduced-motion CSS guard only when the engine is active.
- `assets-src/ts/animation.ts` — code-split JS runtime: reduced-motion early
  exit (no listeners), observer cap, dynamic imports of `gsap`/`gsap/ScrollTrigger`,
  `lenis`, `three` (mount-presence gated); self-boots on DOM ready.
- `vite.config.js` — `animation` entry (4th) alongside main/styles.
- npm deps: `gsap ^3.15.0`, `lenis ^1.3.25`, `three ^0.185.1`,
  `@types/three` (dev) — all code-split into lazy chunks.
- `bin/smoke-phase10.php` — WP-free Animation Engine smoke suite (66
  assertions, incl. Phases 1–9 regression); added to CI.
- ADR-021 — Animation Engine record.

### Changed

- Version 0.9.0 → 0.10.0 (`Core\Version`, `style.css`, `composer.json`,
  `composer.lock` content-hash); `bin/smoke-phase1.php` version assertion.
- `Config\config.php` — `features.animation` now `true` + `AnimationServiceProvider`
  in `providers`.
- `.github/workflows/ci.yml` — Phase 10 smoke step added.
- `docs/architecture/ADR/README.md`, `Report/MASTER_ROADMAP.md`.

### Fixed

- Static analysis/toolchain hardening: ESLint unused var removed; `@types/three`
  added + named-import fix (TS2339); module self-boot (empty chunk); WPCS
  snake_case `with_canvas()`; PHPCS alignment (phpcbf) + Woo line-length
  rewrites (ProductAdapter/CartAdapter/CheckoutAdapter inline phpcs:ignore
  markers moved to their own lines); HookPreservation do_action ignore.
- Integration fixes from review: boot config now delivered to the browser via
  `wp_localize_script` (previously `window.luminaAnimation` was never set, so
  the runtime always early-exited); GSAP tweens now `paused` and scroll-driven
  via ScrollTrigger `onEnter` (previously played on load, defeating reveal-on-
  scroll); preset `duration`/`ease`/`stagger` now honoured in the `to` vars;
  reduced-motion CSS guard attached to a registered+versioned
  `lumina-animation` style handle (previously dropped); Lenis RAF loop stop
  via a real `lumina:lenis:stop` event; Three mounts queried by declared
  selector (each mount gets its own renderer).

## [0.9.0-woo] — 2026-08-04

Tag: `v0.9.0-woo` · Phase 9 — WooCommerce Bridge (frozen Phases 0–8
unchanged)

### Added

- `Woo\WooBridge` — WooCommerce facade on the Phase 8 bridge contract (slug
  `woocommerce`, capabilities product/cart/checkout/account/order/hooks/
  hpos/blocks_safe; active when `class_exists( 'WooCommerce' )`).
- `Woo\Data\ProductAdapter` — id/name/price/image/gallery/rating/stock/
  status/URLs/descriptions snapshot via public WC API only.
- `Woo\Data\CartAdapter` — items, totals, count, currency, cart/checkout
  URLs; `Woo\Data\CheckoutAdapter` — fields schema + session order id;
  `Woo\Data\AccountAdapter` — nav, pages, current user.
- `Woo\Data\OrderAdapter` — HPOS-safe order reads exclusively through
  `wc_get_order()` (single code path for legacy CPT + HPOS).
- `Woo\Hooks\HookPreservation` — canonical 30-hook table + `woocommerce_account_*`
  wildcard, guarded `audit()`/`re_emit()` (never removes a WC hook).
- `Woo\WooServiceProvider` — lazy `woo.*` + `woo.bridge` bindings; passive.
- `tests/stubs/woocommerce-stubs.php` — minimal analysis-only WC stubs wired
  into PHPStan (bootstrapFiles) + Psalm (`<stubs>`); CI stays self-contained.
- `bin/smoke-phase9.php` — WP-free WooCommerce Bridge smoke suite (46
  assertions, incl. Phases 1–8 regression); added to CI.
- ADR-020 — WooCommerce Bridge record.

### Changed

- Version 0.8.0 → 0.9.0 (`Core\Version`, `style.css`, `composer.json`,
  `composer.lock` content-hash); `bin/smoke-phase1.php` version assertion.
- `Config\config.php` — `features.woo_bridge` + `WooServiceProvider` in
  `providers`.
- `.github/workflows/ci.yml` — Phase 9 smoke step added.
- `docs/architecture/ADR/README.md`, `Report/MASTER_ROADMAP.md`.

### Fixed

- Static analysis hardening: 46 PHPStan unknown-class errors resolved via
  analysis-only WC stubs; PHPCS line-length wraps (inline phpcs:ignore
  markers moved to their own lines); dead ternary in `AccountAdapter`
  (wp_get_current_user always returns WP_User per stubs); alignment via
  phpcbf.

## [0.8.0-bridges] — 2026-08-04

Tag: `v0.8.0-bridges` · Phase 8 — Plugin Bridges (frozen Phases 0–7
unchanged)

### Added

- `Bridges\BridgeInterface` + `Bridges\Bridge` — capability-adapter contract
  (slug/name/is_active/version/capabilities/supports) + shared base with
  guarded detection helpers (`guard()`, `constant_version()`).
- `Bridges\Registry` — lazy slug → factory map, memoized per bridge;
  `Bridges\BridgeManager` — public facade (`get`/`all`/`active`/`is_active`/
  `supports`).
- `Bridges\FeatureMatrix` — typed plugin matrix reader
  (`app/Bridges/config/plugins.php`, 12 plugins); `Bridges\HealthCheck` —
  presence/version-floor checks via public WP APIs, WP-free safe.
- 12 canonical bridges: `Acf`, `RankMath`, `Yoast`, `Wpml`, `Polylang`,
  `FluentForms`, `Gravity`, `Wpforms`, `Buddypress`, `Bbpress`, `Learndash`,
  `Tec` — every vendor call guarded; absent plugins → inactive adapters with
  safe defaults (never throws).
- `Bridges\BridgesServiceProvider` — `bridges.registry`/`bridges.manager`/
  `bridges.matrix`/`bridges.health` bindings; passive (nothing booted).
- `docs/plugins.md` — human plugin matrix mirror.
- `bin/smoke-phase8.php` — WP-free Plugin Bridges smoke suite (29 assertions,
  incl. Phases 1–7 regression); added to CI.
- ADR-019 — Plugin Bridges record.

### Changed

- Version 0.7.0 → 0.8.0 (`Core\Version`, `style.css`, `composer.json`,
  `composer.lock` content-hash); `bin/smoke-phase1.php` version assertion.
- `Config\config.php` — `features.plugin_bridges` + `BridgesServiceProvider`
  in `providers`.
- `.github/workflows/ci.yml` — Phase 8 smoke step added.
- `docs/architecture/ADR/README.md` (ADR-019 index + ADR-016 row fix),
  `Report/MASTER_ROADMAP.md`.

### Fixed

- Static analysis hardening: guard() param renames (reserved keywords);
  BbpressBridge docblock capital + if-guard restructure (Psalm);
  FluentFormsBridge always-true ternary (PHPStan); FeatureMatrix line wrap;
  provider unused param; Registry spacing alignment (phpcbf).

## [0.7.0-assets] — 2026-08-04

Tag: `v0.7.0-assets` · Phase 7 — Asset Pipeline (frozen Phases 0–6
unchanged)

### Added

- `Assets\AssetLoader` — `asset_url()`/`resolve()` (dev URL | manifest-hashed
  | raw fallback), guarded `css()`/`js()`/`font_face()` with fingerprint
  version cache busting.
- `Assets\ManifestReader` — Vite manifest parser (memoized, suffix-tolerant,
  `.vite/` probe for Vite 6).
- `Assets\DevServer` — `LUMINA_VITE_ACTIVE`/`LUMINA_VITE_PORT`-driven
  detection + URL building (ADR-011).
- `Assets\BuildFingerprint` — manifest-md5 build identity.
- `Assets\Image` — `srcset()` (WP-guarded) + pure `build_srcset()`.
- `Assets\Markup` — `defer_all()` + `preload_critical_css()`.
- `Assets\Pipeline\{Entries,DepsResolver}` — entry index + transitive
  import closure (cycle-safe).
- `Assets\AssetsServiceProvider` — container bindings + optional
  `wp_enqueue_scripts` wiring (`assets.enqueue`).
- `bin/build-tokens.php` — static token emission → `assets-src/scss/_tokens.scss`
  (ADR-005); `_base.scss` token-driven base layer; `main.ts` interactive entry.
- `vite.config.js` expanded — `main` (TS) + `styles` (SCSS) entries, manifest.
- `bin/smoke-phase7.php` — WP-free Asset Pipeline smoke suite (48 assertions,
  incl. Phases 1–6 regression); added to CI.
- ADR-018 — Asset Pipeline record.

### Changed

- Version 0.6.0 → 0.7.0 (`Core\Version`, `style.css`, `composer.json`,
  `composer.lock` content-hash); `bin/smoke-phase1.php` version assertion +
  `asset_pipeline` flag now enabled.
- `Config\config.php` — `features.asset_pipeline` enabled, `assets.enqueue`
  key + `AssetsServiceProvider` in `providers`.
- `.github/workflows/ci.yml` — Phase 7 smoke + token/dist verification steps.
- `.prettierignore` — generated `_tokens.scss` excluded.
- `docs/architecture/ADR/README.md`, `Report/MASTER_ROADMAP.md`.

### Fixed

- Static analysis hardening: duplicate `animation` config key removed;
  enqueue version now set (WPCS); Image cast/ternary cleanup; Markup single-
  quoted split string; expanded inline `@var`.

## [0.6.0-templates] — 2026-08-04

Tag: `v0.6.0-templates` · Phase 6 — Template System (frozen Phases 0–5
unchanged)

### Added

- `Templates\TemplateResolver` — WP-hierarchy-aware resolver (override →
  child → `wp-{name}` → parent → null tiers), documented hierarchy data
  (home/single/page/archive/category/tag/author/date/search/404/attachment),
  context prefixes (`single-{post_type}-{slug}`…).
- `Templates\PartialLoader` — partial rendering with `index` fallback chain
  and throws-aware misses (RenderException).
- `Templates\Sections` — dynamic section registry (register/render/has/clear)
  for region-based composition.
- `Templates\View` — static facade (`partial()`, `section()`) for templates;
  works in WP and WP-free CLI contexts.
- `Templates\ThemeTemplatesBridge` — capability-guarded `template_include`
  filter + WP-free `locate()` seam.
- `Templates\TemplatesServiceProvider` — `templates.resolver`/`templates.partials`/
  `templates.sections`/`templates.bridge` bindings.
- `templates/single.php` + `templates/partials/{content-single,content,index}.php`
  — minimal structural templates (no page designs).
- `bin/smoke-phase6.php` — WP-free Template System smoke suite (34 assertions,
  incl. Phases 1–5 regression); added to CI.
- ADR-017 — Template System record.

### Changed

- Version 0.5.0 → 0.6.0 (`Core\Version`, `style.css`, `composer.json`,
  `composer.lock` content-hash); `bin/smoke-phase1.php` version assertion.
- `Config\config.php` — `features.template_system` +
  `TemplatesServiceProvider` in `providers`.
- `.github/workflows/ci.yml` — Phase 6 smoke step added.
- `docs/architecture/ADR/README.md`, `Report/MASTER_ROADMAP.md`.

### Fixed

- Static analysis hardening: constant type annotation removed (literal
  inference); unused property removed from `PartialLoader`; inline `@var`
  annotations expanded; `sanitize_key()` WP-free fallback in the resolver.

## [0.5.0-components] — 2026-08-04

Tag: `v0.5.0-components` · Phase 5 — Component Registry (frozen Phases 0–4
unchanged)

### Added

- `Components\Registry` — register/get/has/all/versions/provides_slot,
  resolve_dependencies (cycle + missing-dep validation), render, and the
  `[lumina:{slug}]` shortcode DSL (`render_shortcode`).
- `Components\ComponentDefinition` — immutable component metadata VO
  (name, renderer, slug, data schema, variants, slots, deps, version).
- `Components\Loader` — JSON discovery (`components.json`), memoized;
  `Components\DefinitionCompiler` — schema validation;
  `Components\CycleDetector` — DFS dependency cycle detection;
  `Components\Resolver` — variant merging + slot materialization.
- `Components\ComponentException` / `ComponentNotFoundException` /
  `ComponentCycleException` — deterministic failure types.
- `Components\ComponentsServiceProvider` — `components.registry` binding +
  `add_shortcode` wiring (guarded) on boot.
- `app/Components/config/components.json` — canonical `button` + `card`
  definitions (minimal set to validate the registry).
- `templates/components/button.php` + `templates/components/card.php` —
  presentational fixtures (variants + slots).
- `bin/smoke-phase5.php` — WP-free Component Registry smoke suite
  (38 assertions, incl. Phases 1–4 regression); added to CI.
- ADR-016 — Component Registry record.

### Changed

- Version 0.4.0 → 0.5.0 (`Core\Version`, `style.css`, `composer.json`,
  `composer.lock` content-hash); `bin/smoke-phase1.php` version assertion.
- `Config\config.php` — `features.component_registry`, `components.json_paths`
  - `ComponentsServiceProvider` in `providers`.
- `.github/workflows/ci.yml` — Phase 5 smoke step added.
- `docs/architecture/ADR/README.md`, `Report/MASTER_ROADMAP.md`.

### Fixed

- Static analysis hardening: callable/list docblock type-hint alignment with
  Squiz.Commenting; inline `@var` expanded to a documented comment; line-length
  wraps; `(array)` shortcode atts coercion; `(string)` renderer result cast.

## [0.4.0-render] — 2026-08-04

Tag: `v0.4.0-render` · Phase 4 — Render Engine (frozen Phases 0–3
unchanged)

### Added

- `Render\RendererInterface` + `Renderer` — full render lifecycle
  (resolve → ViewModel → engine → string) with optional cache around it.
- `Render\TemplateEngineInterface` + `PhpTemplateEngine` — native PHP engine
  behind an interface (ADR-009: zero runtime PHP deps; Twig swappable via
  `render.engine` config).
- `Render\TemplateResolver` — 4-tier resolution (override → base →
  `wp-{name}` → null), path-traversal guard, `.twig` → `.php` slug
  normalization.
- `Render\Layout` — region-based composition buffer (`push`/`has`/`flush`/
  `render_region`), view-slug or callable blocks.
- `Render\ViewModel` — immutable dot-notation data bag; `Render\ViewContext`
  — escaping helpers (`e`, `attr`, `url`, `html`) with WP-first, PHP-fallback
  resolution.
- `Render\RenderCache` — (view, data-hash) render cache over CacheInterface;
  disabled for logged-in users; failures swallowed.
- `Render\RenderException` — catchable render failure (never `die`).
- `Render\RenderServiceProvider` — container bindings (`render.renderer`,
  `render.layout`, `render.resolver`, `render.engine`, `render.cache`).
- `Data\*` adapters — Post, Term, User, Menu, Site, Settings, Tax, WpQuery
  (normalize WP/vendor data into ViewModels; WP-free CLI-safe).
- `templates/card.php` — presentational fixture with full escaping.
- `bin/smoke-phase4.php` — WP-free Render Engine smoke suite (61 assertions,
  incl. Phases 1–3 regression); added to CI.

### Changed

- Version 0.3.0 → 0.4.0 (`Core\Version`, `style.css`, `composer.json`,
  `composer.lock`); `bin/smoke-phase1.php` version assertion.
- `Config\config.php` — `features.render_engine`, `render.{engine,cache,ttl}`
  - `RenderServiceProvider` in `providers`.
- `.github/workflows/ci.yml` — Phase 4 smoke step added.
- `docs/architecture/ADR/README.md`, `Report/MASTER_ROADMAP.md`.

### Fixed

- Static analysis hardening: PHPDoc params/throws on `PhpTemplateEngine`;
  WPCS ignore for the swallow-catch in `RenderCache`; `json_encode` guard;
  PHPStan always-true ternary in `UserAdapter` author-link/avatar casts.

## [0.3.0-tokens] — 2026-08-03

Tag: `v0.3.0-tokens` · Phase 3 — Design Token Engine (frozen Phases 0–2
unchanged)

### Added

- `Tokens\TokenRepository` — public facade: `tokens($context)`, `token($name)`
  (throws `UnknownToken`), `resolve($name)`, `css($scope)`, `validate()`,
  `contrast_passes()`.
- `Tokens\TokenSource` — parses nested definitions into a flat dot-map;
  validates names against `/^[a-z0-9][a-z0-9\-]{0,63}$/` per segment.
- `Tokens\Preced` — precedence collector (default → preset → override).
- `Tokens\Resolver` — walks the `extends` inheritance graph (cycle-safe).
- `Tokens\TokenFactory` — token → CSS custom-property map (`--lumina-*`).
- `Tokens\Invariant` — name/fallback validation + WCAG AA contrast ratio.
- `Tokens\Renderer\CssRenderer` — `:root` + `[data-lumina-theme="…"]` blocks.
- `Tokens\Loader\DataProvider` — in-memory cached loader for config files.
- `Tokens\TokenServiceProvider` — container bindings (registered in
  `Config\config.php` `providers`).
- Canonical token groups (`app/Tokens/config/tokens.php`): color, typography,
  space (4px scale), radius, shadow, motion, layout, grid, breakpoints,
  z-index, component (extends-aliased). Presets: `default` + `dark`
  (`app/Tokens/config/presets.php`).
- `bin/smoke-phase3.php` — WP-free Design Token smoke suite (25 assertions,
  incl. Phases 1–2 regression); added to CI.
- ADR-015 — Design Token Engine record.

### Changed

- Version 0.2.0 → 0.3.0 (`Core\Version`, `style.css`, `composer.json`,
  `composer.lock`).

### Fixed

- Token name pattern relaxed to allow digit-leading segments (canonical
  numeric-scale tokens `space.4`, `type.size.2xl`); CSS var prefix keeps the
  full property name letter-initial (ADR-015 §5).

## [0.2.0-framework] — 2026-08-03

Tag: `v0.2.0-framework` · Phase 2 — Framework Infrastructure (frozen
Phases 0 + 1 unchanged)

### Added

- `Container\Container` — PSR-11-style DI container: `set()` raw values,
  `register()`/`singleton()` closures, class-string auto-wiring via
  constructor reflection; lazy resolution, singleton caching, cycle detection
  (`CircularDependencyException`), `NotFoundException` (PSR-11). Supersedes the
  Phase-1 App registry (ADR-013 → 014); `App::make()`/`get()` now delegate here.
- `Events\EventInterface`, `StoppableEventInterface`, `GenericEvent`,
  `Dispatcher` — ordered listeners (priority) + stop-propagation.
- `Hooks\WpBridge` (capability-guarded `add_action`/`add_filter`/`apply_filters`
  wrappers) + `Hooks\HookManager` (dedupes identical bindings).
- `Registry\RegistryInterface`, `ArrayRegistry` (immutable),
  `DynamicRegistry` (lazy factory-backed items).
- `Factory\FactoryInterface` + `SimpleFactory` (container-backed builder).
- `Config\Repository` — dot-notation get/set over the immutable config.
- `Cache\CacheInterface`, `CacheKey` (namespaced), `ObjectCache`,
  `TransientCache` (ADR-010) — shared interface; `flush()` only clears keys
  written through that instance.
- `Providers\ServiceProviderInterface` — `register()` then `boot()` lifecycle.
- Kernel boot extended: `config → env → flags → logger → errorHandler →
container → core services → providers` (providers array in `config.php`).
- `bin/smoke-phase2.php` — WP-free framework smoke suite (39 assertions,
  incl. Phase 1 regression); added to CI.
- `lumina.env.json.example` schema fixed to `environment.override`/`features`.
- ADR-014 — Phase 2 framework infrastructure record.

### Changed

- Version 0.1.1 → 0.2.0 (`Core\Version`, `style.css`, `composer.json`,
  `composer.lock`).

### Fixed

- Static analysis hardening: WPCS EscapeOutput ignores placed on the flagged
  token lines; param renames (`$class_name`, `$fallback`, `$wp_hook`,
  `$abstract_id`); Psalm reference-typed property in `Config\Repository::set()`
  replaced with a value-typed write.

## [0.1.1-bootstrap] — 2026-08-03

Tag: `v0.1.1-bootstrap` · Phase 1 — Bootstrap (frozen Phase 0 unchanged)

### Added

- `app/load.php` — single entry; registers Composer PSR-4 autoloader and binds
  `Boot\Kernel::launch()` to `plugins_loaded` priority 5.
- `Boot\Kernel` — ordered bootstrap lifecycle with `lumina_core:booting`,
  `lumina_core:booted`, `lumina_core:ready` events (ADR-006/013).
- `Boot\Sequencer` — ordered, filterable boot steps (`lumina_core:boot_steps`),
  failure isolation via `lumina_core:boot_error`.
- `Boot\BootableInterface` — `register()` + `boot()` contract for bootable
  components (providers in Phase 2).
- `Core\App` — public facade: `instance()`, `make()`, `get()`, `env()`,
  `is_debug()`, `log()`; holds the Phase-1 service registry (WPCS snake_case).
- `Config\ConfigLoader` — immutable config (defaults + `lumina.env.json`
  overrides, ADR-011); `Config\config.php` defaults.
- `Support\Env` — wraps `wp_get_environment_type()`, debug detection.
- `Support\FeatureFlags` — reads `features` map, `lumina_feature_*` policy.
- `Support\Debug\Log` + `Support\Debug\Loggers` — PSR-3-ish facade; level
  thresholding; **secret redaction** (`ph_pass`, `sku_key`).
- `Support\ErrorHandler` — `WP_Error` wrapping, single-emission registration.
- `bin/smoke-phase1.php` — WP-free CLI boot smoke suite (24 assertions,
  self-cleaning via snapshot/restore; CI-visible).
- ADRs materialized under `docs/architecture/ADR/` (001–012) + new ADR-013.
- Static analysis now wired to WordPress stubs: `phpstan-wordpress` extension
  (PHPStan) + `php-stubs/wordpress-stubs` (Psalm) — Phase 0 global Psalm
  suppressions removed.
- `CHANGELOG.md` — this file.

### Fixed

- CI workflow moved to repo root (GitHub requires it there); `working-directory`
  points at the theme; smoke suite added to the PHP job.

## [0.1.0-foundation] — 2026-08-03

Tag: `v0.1.0-foundation` · Phase 0 — Project Foundation (FROZEN)

### Added

- Canonical theme structure: `app/` (PSR-4 `Lumina\Core\`), `assets-src/`,
  `templates/`, `template-parts/`, `inc/`, `tests/`, `e2e/`, `docs/`, `bin/`.
- `Core\Version` constants (VERSION 0.1.0, API_LEVEL 1, prefixes).
- Composer (PSR-4 + dev tools) + npm (Vite 6.4, ESLint 9, Prettier, TS) configs.
- PHPCS (WPCS 3.4), PHPStan level 5, Psalm errorLevel 5 — all green.
- `bin/verify-lumina-integrity.sh` — ADR-004 self-integrity hash gate.
- GitHub Actions CI (bootstrap / static-analysis / assets / integrity).
- Docs: architecture, development, versions (semver `0.x` policy),
  PHASE_0_VERIFICATION_REPORT (APPROVED FOR PHASE 1).
- Verification report: `docs/PHASE_0_VERIFICATION_REPORT.md`.
