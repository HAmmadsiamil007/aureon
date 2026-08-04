# CONTRACT FREEZE — Lumina Theme / Lumina Core

- **Version:** 1.0.0 (Lumina — Safe Rebranding)
- **Date:** 2026-08-04
- **Phase:** 16 (Safe Rebranding) — re-freeze at the Lumina surface
- **Status:** 🔒 **FROZEN** — every contract below is locked at `v1.0.0-lumina`

This document freezes the _behavioral_ contracts (as opposed to the API
surface in `API_FREEZE.md`): component contracts, render/template contracts,
view-model/data-adapter contracts, service-provider lifecycle, bridge
contracts, the animation contract, and the **theme ↔ companion plugin
extension contract** (ADR-027/028). Phase 16 preserved functional parity
against every contract listed here while renaming the identifier surface.

---

## 0. Theme Shell Contract (Phase 16 — ADR-027)

- Lumina ships its own WP hierarchy files (`header.php`, `footer.php`,
  `index.php`, `single.php`, `page.php`, `archive.php`, `search.php`,
  `404.php`, `comments.php`, `searchform.php`) — no parent theme required.
- The shell fires public region actions: `lumina_before_header`,
  `lumina_after_header`, `lumina_before_footer`, `lumina_after_footer`.
- The `TemplateResolver` resolves through `templates/{override}/{slug}.php`
  → `templates/{slug}.php` → `templates/wp-{slug}.php` → `null` (no parent
  tier).
- The Lumina self-integrity gate (`bin/verify-lumina-integrity.sh`) pins the
  shipped tree to the freeze baseline.

## 0b. Companion Plugin Contract (Phase 16 — ADR-028)

- The companion plugin (`Lumina\Companion\`, theme-gated) registers its
  features only when `get_template() === 'lumina'`.
- Modules fill the region hooks above and inject settings through the
  `lumina_template_data` filter; never through globals.
- The plugin never removes a WooCommerce hook and only uses public WC APIs
  (guarded by `class_exists('WooCommerce')`).

## 1. Component Contracts (Phase 5 + 11)

- **Registration:** components are discovered from `components.json`
  (canonical `app/Components/config/components.json`); name/renderer/slug/
  version/data/variants/slots/deps fields validated by `DefinitionCompiler`.
- **Render contract:** `render(name, data)` → escaped HTML string; a
  component renders **only** through the Phase-4 Render Engine — never
  directly.
- **Template contract:** every component template is `ViewContext`-only
  (`e`/`attr`/`url`/`html`), contains **zero** WordPress calls, and is
  WP-free CLI-verifiable (smoke-phase11 enforces parity).
- **Catalog:** 78 components frozen. Component names/versions/variants/slots
  are locked; adding/renaming a component is a contract change.
- **Slot system:** `hero → button`, `footer → footer-columns, copyright`,
  `card → button`, `product-card → badges, actions` (frozen composition
  edges).
- **DSL:** `[lumina:{slug}]` shortcode with `[lumina:{slug} variant="…" …]`
  attribute surface.

## 2. Render Contracts (Phase 4)

- Lifecycle: **resolve → ViewModel → engine → string**, optionally wrapped
  in the render cache.
- `ViewContext` escaping semantics: `e()` (HTML text), `attr()` (attribute),
  `url()` (URL), `html()` (trusted markup — template-author opt-in).
- Templates resolve through 4 tiers (override → base → `wp-{name}` → null)
  with a path-traversal guard.
- Render failures throw `RenderException` — never `die()`.
- Cache keyed on (view, data-hash); disabled for logged-in users.

## 3. Template Contracts (Phase 6 + 12)

- **Hierarchy:** template_include bridge with override tiers (child →
  parent); 23 frozen template slugs: site shell (header/footer), home/
  landing, commerce (shop/product/cart/checkout/thank-you/account/wishlist/
  compare), blog/content (blog/single-post/archive/author/search/not-found),
  utility (contact/about/faq-page/privacy/terms).
- **Composition contract:** every template delegates to
  `View::compose($slug, $data)` → `Templates\Composer` → registry. No
  template contains business logic, `wc_*` calls, `get_template_part`
  bypasses, or `WP_Query`.
- **Data contract:** template data arrives via the `lumina_template_data`
  filter; `custom.php` also reads `lumina_template_slug`.
- **Partials:** `View::partial(name, data)` with `index` fallback chain;
  `View::section()` for dynamic regions.

## 4. View-Model & Data-Adapter Contracts (Phase 4 `Data\*`)

- Adapters normalize WP/vendor data into arrays/ViewModels through public
  APIs only; every adapter is WP-free safe (absent WordPress → safe defaults,
  never throws).
- Frozen adapters: Post, Term, User, Menu, Site, Settings, Tax, WpQuery
  (+ Woo Product/Cart/Checkout/Account/Order).

## 5. Service-Provider Lifecycle (Phase 2)

- `register(Container)` then `boot(Container)`; providers list in config is
  frozen (10 providers). Bindings use `set`/`singleton`; resolution is lazy.
- Boot order: config → env → flags → logger → errorHandler → container →
  core services → providers (frozen sequence, ADR-013).

## 6. Bridge Contracts (Phase 8)

- `BridgeInterface`: slug/name/is_active/version/capabilities/supports.
- Bridges are **capability adapters** — never modify vendor code; absent
  plugins yield inactive adapters with safe defaults (never throw).
- Frozen bridge set (12): ACF, Rank Math, Yoast, WPML, Polylang, Fluent
  Forms, Gravity Forms, WPForms, BuddyPress, bbPress, LearnDash, The Events
  Calendar.
- `FeatureMatrix` reads `app/Bridges/config/plugins.php` (frozen matrix).

## 7. WooCommerce Bridge Contract (Phase 9)

- Woo flows **only** through the bridge — templates never call `wc_*`.
- **Hook preservation:** 30-hook canonical table + `woocommerce_account_*`
  wildcard; the bridge never removes a WC hook (`HookPreservation::audit()`/
  `re_emit()`).
- **HPOS contract:** order reads exclusively via `wc_get_order()` (single
  code path, legacy CPT + HPOS).
- **Blocks contract:** legacy template override is OFF by default
  (Blocks-safe); opt-in only via explicit configuration.

## 8. Animation Contract (Phase 10)

- Presets are named, immutable, allowlisted (`AnimationRegistry` + `Preset`);
  canonical `reveal` preset frozen.
- Engine aggregates presets + gates into one serialized boot config delivered
  via `window.luminaAnimation`.
- **Reduced motion:** config-enforced gate + inline CSS guard + JS early
  exit — no listeners installed when reduced motion is preferred.
- **Budget:** JS ≤ 120 KB, observer cap 40, `will-change` only while
  animating (`Breaking`).
- Runtime libs (gsap/ScrollTrigger/lenis/three) are **code-split dynamic
  imports**, loaded only when the engine is active.

## 9. Performance Contract (Phase 13)

- Budgets: LCP ≤ 2.0 s · CLS ≤ 0.05 · INP ≤ 150 ms · JS ≤ 120 KB · CSS ≤
  50 KB · server ≤ 300 ms · queries ≤ 8 (`performance.budgets`).
- QueryGuard is debug-only, observes, never blocks queries.
- Cache invalidation fires `lumina_core:cache_purged`.

## 10. Accessibility Contract (Phase 14)

- `A11y\Checker` audit rules: single h1 (+ no missing h1), no skipped
  heading levels, landmarks (header/main/footer/nav-or-aside), img alt,
  form label association, interactive accessible names, no positive
  tabindex, dialogs carry `tabindex="-1"`.
- Skip link: first-focusable, `screen-reader-text lumina-skip-link` →
  `#main`, emitted via `wp_body_open`.
- Dialog contract: `role="dialog"` + `aria-modal="true"` + `tabindex="-1"`
  - `aria-labelledby`; runtime focus trap in `components.ts`.

## 11. Design-Token Contract (Phase 3)

- Names match `/^[a-z0-9][a-z0-9\-]{0,63}$/` per segment; extends graph is
  cycle-safe; WCAG AA contrast validated by `Invariant`.
- CSS output: `:root` + `[data-lumina-theme="default|dark"]` blocks using
  `--lumina-*` custom properties.

---

## Freeze Enforcement

- Contract changes (rename, re-typed prop, new slot, changed behavior)
  require an ADR + SemVer bump; breaking changes require major.
- Phase 16 rebranding may alter **brand-visible** strings and the theme
  header metadata only — never the contracts above. Parity is measured by
  re-running every smoke suite against this baseline.

**STATUS: 🔒 CONTRACT FREEZE IN EFFECT — v0.14.0**
