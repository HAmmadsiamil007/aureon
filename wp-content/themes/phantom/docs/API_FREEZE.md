# API FREEZE — Phantom Theme / Phantom Core

- **Version:** 0.14.0 (RC — Production Freeze)
- **Date:** 2026-08-04
- **Phase:** 15.5 (Production Freeze & Release Candidate)
- **Status:** 🔒 **FROZEN** — no public API changes permitted without an ADR + minor/major version bump

This document is the canonical inventory of every frozen public API surface.
All identifiers below are locked at `v0.14.0` and become the permanent
baseline for Phase 16 (rebranding) and beyond.

---

## 1. PHP Namespaces & Public Classes

PSR-4: `Phantom\Core\` → `app/` (ADR-009). All classes final, documented,
static-analysis clean.

| Subsystem   | Public classes                                                                                                                                                                                                                           |
| ----------- | ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Boot        | `Kernel`, `Sequencer`, `BootableInterface`                                                                                                                                                                                               |
| Core        | `App` (facade), `Version`                                                                                                                                                                                                                |
| Config      | `ConfigLoader`, `Repository`                                                                                                                                                                                                             |
| Support     | `Env`, `FeatureFlags`, `ErrorHandler`, `Debug\Log`, `Debug\Loggers`                                                                                                                                                                      |
| Container   | `Container`                                                                                                                                                                                                                              |
| Events      | `Dispatcher`, `GenericEvent`, `EventInterface`, `StoppableEventInterface`                                                                                                                                                                |
| Hooks       | `HookManager`, `WpBridge`                                                                                                                                                                                                                |
| Registry    | `ArrayRegistry`, `DynamicRegistry`, `RegistryInterface`                                                                                                                                                                                  |
| Factory     | `SimpleFactory`, `FactoryInterface`                                                                                                                                                                                                      |
| Cache       | `CacheInterface`, `CacheKey`, `ObjectCache`, `TransientCache`                                                                                                                                                                            |
| Providers   | `ServiceProviderInterface`                                                                                                                                                                                                               |
| Tokens      | `TokenRepository`, `TokenSource`, `Preced`, `Resolver`, `TokenFactory`, `Invariant`, `Renderer\CssRenderer`, `Loader\DataProvider`, `UnknownToken`                                                                                       |
| Render      | `Renderer`, `RendererInterface`, `PhpTemplateEngine`, `TemplateEngineInterface`, `TemplateResolver`, `Layout`, `ViewModel`, `ViewContext`, `RenderCache`, `RenderException`                                                              |
| Data        | `Post`, `Term`, `User`, `Menu`, `Site`, `Settings`, `Tax`, `WpQuery` adapters                                                                                                                                                            |
| Components  | `Registry`, `ComponentDefinition`, `Loader`, `DefinitionCompiler`, `CycleDetector`, `Resolver`, `ComponentException`, `ComponentNotFoundException`, `ComponentCycleException`                                                            |
| Templates   | `Composer`, `TemplateResolver`, `PartialLoader`, `Sections`, `View` (facade), `ThemeTemplatesBridge`                                                                                                                                     |
| Assets      | `AssetLoader`, `ManifestReader`, `DevServer`, `BuildFingerprint`, `Image`, `Markup`, `Pipeline\Entries`, `Pipeline\DepsResolver`                                                                                                         |
| Bridges     | `BridgeInterface`, `Bridge`, `Registry`, `BridgeManager`, `FeatureMatrix`, `HealthCheck` + 12 bridges (`Acf`, `RankMath`, `Yoast`, `Wpml`, `Polylang`, `FluentForms`, `Gravity`, `Wpforms`, `Buddypress`, `Bbpress`, `Learndash`, `Tec`) |
| Woo         | `WooBridge`, `Data\ProductAdapter`, `Data\CartAdapter`, `Data\CheckoutAdapter`, `Data\AccountAdapter`, `Data\OrderAdapter`, `Hooks\HookPreservation`                                                                                     |
| Animation   | `AnimationRegistry`, `Preset`, `Engine`, `ReducedMotion`, `Breaking`, `Lenis`, `Three`, `Scroll\Trigger`                                                                                                                                 |
| Performance | `Budget`, `BudgetLogger`, `QueryGuard`, `Lazy`, `CachePurger`                                                                                                                                                                            |
| A11y        | `Checker`, `SkipLink`, `DialogManager`                                                                                                                                                                                                   |

## 2. Container Bindings (frozen keys)

```
env · config · container · log
tokens.source · tokens.preced · tokens.resolver · tokens.invariant ·
tokens.factory · tokens.repository · tokens.renderer · tokens.provider
render.renderer · render.engine · render.resolver · render.layout ·
render.cache
components.registry · components.json_paths
templates.resolver · templates.partials · templates.sections ·
templates.bridge · templates.composer
assets.manifest · assets.dev_server · assets.entries · assets.deps ·
assets.loader · assets.enqueue
bridges.registry · bridges.manager · bridges.matrix · bridges.health
woo.bridge · woo.products · woo.cart · woo.checkout · woo.account ·
woo.orders · woo.hooks
animation.registry · animation.engine · animation.reduced_motion ·
animation.breaking · animation.lenis · animation.three · animation.trigger
performance.budget · performance.budgets · performance.logger ·
performance.guard · performance.query_guard · performance.lazy ·
performance.purger
a11y.checker · a11y.skip_link · a11y.dialog
```

## 3. Facades (static, WP + WP-free)

- `App::instance()`, `App::make()`, `App::get()`, `App::env()`,
  `App::is_debug()`, `App::log()`
- `View::partial()`, `View::section()`, `View::compose()`
- `Version::VERSION`, `Version::API_LEVEL`

## 4. Custom Events (namespaced `phantom_core:*`)

```
phantom_core:booting · phantom_core:booted · phantom_core:ready ·
phantom_core:boot_steps (filter) · phantom_core:boot_error ·
phantom_core:animation:ready · phantom_core:cache_purged
```

## 5. WordPress Hooks & Filters (registered surface)

| Hook                              | Kind   | Purpose                                              |
| --------------------------------- | ------ | ---------------------------------------------------- |
| `plugins_loaded` (prio 5)         | action | Framework boot entry (ADR-013)                       |
| `template_include`                | filter | Template-system bridge (Phase 6)                     |
| `wp_body_open` (prio 1)           | action | Skip-link emission (Phase 14)                        |
| `wp_enqueue_scripts`              | action | Conditional asset/animation enqueue (Phases 7/10/11) |
| `pre_get_posts`                   | action | QueryGuard registration (debug-only, Phase 13)       |
| `phantom_template_data`           | filter | Template composition data (Phase 12)                 |
| `phantom_template_slug`           | filter | Custom template slug (Phase 12)                      |
| `phantom_render_resolver_context` | filter | Render resolver context (Phase 4)                    |

WooCommerce hooks: **30-hook canonical table + `woocommerce_account_*`
wildcard** — preserved, never removed (Phase 9 `HookPreservation`).

## 6. Shortcodes

- `[phantom:{slug}]` — component registry DSL (Phase 5).

## 7. Design Tokens & CSS Variables

- Token groups: `color, typography, space, radius, shadow, motion, layout,
grid, breakpoints, z-index, component` (Phase 3).
- Presets: `default`, `dark`.
- CSS variables: `--phantom-*` prefix (ADR-002/003); scope
  `:root` + `[data-phantom-theme="…"]`.
- Component classes: `phantom-*`; animation hooks `data-phantom-anim`.

## 8. Configuration Schema (frozen keys)

```
environment.override · debug · features.{phantom_core, render_engine,
component_registry, template_system, asset_pipeline, plugin_bridges,
woo_bridge, animation, component_library, template_library, performance,
accessibility} · log.{level, redact} · error_handler.register ·
render.{engine, cache, cache_ttl} · components.json_paths ·
performance.{budgets, query_guard} · assets.enqueue · providers[]
```

Environment overrides: `phantom.env.json` (ADR-011).

## 9. JavaScript APIs (frozen)

- Vite entries: `main` (index), `styles`, `components`, `animation`.
- Global: `window.phantomAnimation` (animation boot config, Phase 10).
- DOM protocol: `[data-phantom-*]` hooks, `data-phantom-theme` for theming,
  `data-phantom-anim` for reveal presets.
- Runtime dependencies (dynamic-import only): `gsap`, `gsap/ScrollTrigger`,
  `lenis`, `three`.

## 10. Filesystem & Asset Manifest Structure (frozen)

- `app/` (PSR-4) · `assets-src/{scss,ts}/` · `assets/dist/{assets,.vite}/`
  · `templates/{components,frontend,partials}/` · `bin/` · `docs/` · `inc/`
  · `tests/` · `e2e/`.
- Vite manifest: `assets/dist/.vite/manifest.json` (Vite 6 layout).
- Built asset naming: `<name>-<contenthash>.<ext>` (cache busting).

---

## Freeze Enforcement

- Any change to the identifiers above requires a new ADR and a SemVer
  **minor** (additive) or **major** (breaking) bump.
- Phase 16 (rebranding) may only change _display_ strings, file headers,
  `style.css` header metadata, and the theme slug/name — **never** the
  technical API surface listed here. Functional parity is measured against
  this document.

**STATUS: 🔒 API FREEZE IN EFFECT — v0.14.0**
