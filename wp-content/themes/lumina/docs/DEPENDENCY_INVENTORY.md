# DEPENDENCY INVENTORY — Lumina Theme / Lumina Core

- **Version:** 0.14.0 (RC — Production Freeze)
- **Date:** 2026-08-04
- **Phase:** 15.5 (Production Freeze & Release Candidate)
- **Status:** 🔒 **LOCKED** — versions below are the frozen release baseline

---

## 1. Runtime Platform

| Dependency   | Version   | Constraint source                                     |
| ------------ | --------- | ----------------------------------------------------- |
| PHP          | ^8.2      | `composer.json` `require`, `style.css` `Requires PHP` |
| WordPress    | ≥ 6.5     | `style.css` `Requires at least`                       |
| MySQL        | 5.7 / 8.x | WP API only (no raw SQL)                              |
| MariaDB      | 10.x      | Same code path as MySQL                               |
| Node (build) | ≥ 20.0    | `package.json` `engines`                              |
| npm (build)  | ≥ 10.0    | `package.json` `engines`                              |

## 2. PHP Dependencies (Composer — dev only, zero runtime)

Locked in `composer.lock` (40 packages, all `require-dev`; `require` is
PHP-only per ADR-009):

| Package                                        | Version |
| ---------------------------------------------- | ------- |
| php-stubs/wordpress-stubs                      | v6.9.4  |
| phpstan/phpstan                                | 1.12.34 |
| szepeviktor/phpstan-wordpress                  | v1.3.5  |
| vimeo/psalm                                    | 5.26.1  |
| wp-coding-standards/wpcs                       | 3.4.1   |
| squizlabs/php_codesniffer                      | 3.13.5  |
| dealerdirect/phpcodesniffer-composer-installer | v1.2.1  |
| phpcsstandards/phpcsextra                      | 1.5.1   |
| phpcsstandards/phpcsutils                      | 1.2.3   |
| psr/container                                  | 2.0.2   |
| psr/log                                        | 3.0.2   |
| composer/pcre                                  | 3.3.2   |
| composer/semver                                | 3.4.4   |
| composer/xdebug-handler                        | 3.0.5   |
| nikic/php-parser                               | v4.19.5 |
| phpdocumentor/reflection-common                | 2.2.0   |
| phpdocumentor/reflection-docblock              | 5.6.7   |
| phpdocumentor/type-resolver                    | 1.12.0  |
| phpstan/phpdoc-parser                          | 2.3.3   |
| sebastian/diff                                 | 6.0.2   |
| spatie/array-to-xml                            | 3.4.4   |
| webmozart/assert                               | 2.4.1   |
| amphp/amp                                      | v2.6.5  |
| amphp/byte-stream                              | v1.8.2  |
| dnoegel/php-xdg-base-dir                       | v0.1.1  |
| doctrine/deprecations                          | 1.1.6   |
| felixfbecker/advanced-json-rpc                 | v3.2.1  |
| felixfbecker/language-server-protocol          | v1.5.3  |
| fidry/cpu-core-counter                         | 1.3.0   |
| netresearch/jsonmapper                         | v4.5.0  |
| symfony/console                                | v7.4.15 |
| symfony/deprecation-contracts                  | v3.7.1  |
| symfony/filesystem                             | v7.4.15 |
| symfony/polyfill-ctype                         | v1.37.0 |
| symfony/polyfill-intl-grapheme                 | v1.41.0 |
| symfony/polyfill-intl-normalizer               | v1.38.0 |
| symfony/polyfill-mbstring                      | v1.38.2 |
| symfony/polyfill-php73                         | v1.37.0 |
| symfony/service-contracts                      | v3.7.1  |
| symfony/string                                 | v7.4.15 |

## 3. Node Dependencies (npm — build-time only)

Locked in `package-lock.json` (lockfileVersion 3). Runtime-animatable libs are
code-split dynamic imports (never shipped unless the engine is active).

| Package                      | Spec     |
| ---------------------------- | -------- |
| gsap                         | ^3.15.0  |
| lenis                        | ^1.3.25  |
| three                        | ^0.185.1 |
| @types/three (dev)           | ^0.185.3 |
| vite (dev)                   | ^6.0.0   |
| sass (dev)                   | ^1.77.0  |
| typescript (dev)             | ^5.5.0   |
| eslint (dev)                 | ^9.0.0   |
| @eslint/js (dev)             | ^9.0.0   |
| eslint-config-prettier (dev) | ^9.1.0   |
| typescript-eslint (dev)      | ^8.0.0   |
| prettier (dev)               | ^3.3.0   |

Locked transitive versions (resolved in package-lock.json) include
`vite@6` toolchain, `rollup@4.62.4`, `esbuild@0.25.12`, `eslint@9.39.x`,
`@eslint/js@9.39.5`, `typescript-eslint@8.x`.

## 4. Plugin Bridge Compatibility Targets (Phase 8, frozen)

| Plugin                 | Target                        |
| ---------------------- | ----------------------------- |
| Advanced Custom Fields | Supported via guarded adapter |
| Yoast SEO              | Supported via guarded adapter |
| Rank Math SEO          | Supported via guarded adapter |
| WPML                   | Supported via guarded adapter |
| Polylang               | Supported via guarded adapter |
| Gravity Forms          | Supported via guarded adapter |
| Fluent Forms           | Supported via guarded adapter |
| WPForms                | Supported via guarded adapter |
| BuddyPress             | Supported via guarded adapter |
| bbPress                | Supported via guarded adapter |
| LearnDash              | Supported via guarded adapter |
| The Events Calendar    | Supported via guarded adapter |

Absent plugins → inactive adapters with safe defaults (never throw).

## 5. WooCommerce Compatibility Target

| Area               | Version/Target                                     |
| ------------------ | -------------------------------------------------- |
| WooCommerce        | Any modern release via public WC APIs              |
| HPOS               | Supported — reads exclusively via `wc_get_order()` |
| WooCommerce Blocks | Supported — legacy override OFF by default         |

## 6. Integrity Anchors

| Package | Files | Baseline                              |
| ------- | ----- | ------------------------------------- |
| Lumina  | —     | `bin/verify-lumina-integrity.sh` gate |

Verified by the Lumina self-integrity gate (14/14 WP-free smoke suites +
static analysis + deterministic build).

---

## Dependency Policy

- **No new dependencies** without an ADR + version bump (Phase 15.5 rule).
- Composer lock + package-lock are committed; installs are reproducible
  (`composer install` / `npm ci`).
- Runtime PHP deps: **zero** (ADR-009). Runtime JS deps: loaded lazily only
  when the animation engine is active.

**STATUS: 🔒 DEPENDENCY INVENTORY LOCKED — v0.14.0**
