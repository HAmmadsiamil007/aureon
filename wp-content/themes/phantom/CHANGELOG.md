# Changelog

All notable changes to the Phantom theme / Phantom Core are documented here.
Format follows [Keep a Changelog](https://keepachangelog.com/en/1.1.0/);
versioning follows SemVer `0.x` (see `docs/versions.md`).

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
