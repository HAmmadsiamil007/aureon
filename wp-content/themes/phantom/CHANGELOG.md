# Changelog

All notable changes to the Phantom theme / Phantom Core are documented here.
Format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/);
versioning follows SemVer `0.x` (see `docs/versions.md`).

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
- `Tokens\TokenFactory` — token → CSS custom-property map (`--phantom-*`).
- `Tokens\Invariant` — name/fallback validation + WCAG AA contrast ratio.
- `Tokens\Renderer\CssRenderer` — `:root` + `[data-phantom-theme="…"]` blocks.
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
- `phantom.env.json.example` schema fixed to `environment.override`/`features`.
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
- `Boot\Kernel` — ordered bootstrap lifecycle with `phantom_core:booting`,
  `phantom_core:booted`, `phantom_core:ready` events (ADR-006/013).
- `Boot\Sequencer` — ordered, filterable boot steps (`phantom_core:boot_steps`),
  failure isolation via `phantom_core:boot_error`.
- `Boot\BootableInterface` — `register()` + `boot()` contract for bootable
  components (providers in Phase 2).
- `Core\App` — public facade: `instance()`, `make()`, `get()`, `env()`,
  `is_debug()`, `log()`; holds the Phase-1 service registry (WPCS snake_case).
- `Config\ConfigLoader` — immutable config (defaults + `phantom.env.json`
  overrides, ADR-011); `Config\config.php` defaults.
- `Support\Env` — wraps `wp_get_environment_type()`, debug detection.
- `Support\FeatureFlags` — reads `features` map, `phantom_feature_*` policy.
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

- Canonical theme structure: `app/` (PSR-4 `Phantom\Core\`), `assets-src/`,
  `templates/`, `template-parts/`, `inc/`, `tests/`, `e2e/`, `docs/`, `bin/`.
- `Core\Version` constants (VERSION 0.1.0, API_LEVEL 1, prefixes).
- Composer (PSR-4 + dev tools) + npm (Vite 6.4, ESLint 9, Prettier, TS) configs.
- PHPCS (WPCS 3.4), PHPStan level 5, Psalm errorLevel 5 — all green.
- `bin/verify-parent-integrity.sh` — ADR-004 hash gate (473/473 verified).
- GitHub Actions CI (bootstrap / static-analysis / assets / integrity).
- Docs: architecture, development, versions (semver `0.x` policy),
  PHASE_0_VERIFICATION_REPORT (APPROVED FOR PHASE 1).
- Verification report: `docs/PHASE_0_VERIFICATION_REPORT.md`.
