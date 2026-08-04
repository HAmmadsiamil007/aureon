# Lumina — Development Guide

## Prerequisites

| Tool    | Version | Notes                   |
| ------- | ------- | ----------------------- |
| PHP     | 8.2+    | CLI required (`php -v`) |
| Node.js | 20+     | LTS recommended         |
| npm     | 10+     | Ships with Node         |
| Git     | any     | —                       |

Composer is **not** required globally — the toolchain bootstraps a project-local
phar into `tools/` (git-ignored).

## One-time setup

```bash
bash bin/setup-toolchain.sh
```

This downloads `tools/composer.phar`, installs PHP dev dependencies (PHPCS,
PHPStan, Psalm, WPCS), dumps the PSR-4 autoloader, and runs `npm ci`.

## Daily commands

```bash
# Composer (project-local phar)
php tools/composer.phar install
php tools/composer.phar dump-autoload --optimize

# Static analysis
php tools/composer.phar cs          # PHPCS (WordPress Coding Standards)
php tools/composer.phar stan        # PHPStan level 5 (WordPress stubs via phpstan-wordpress)
php tools/composer.phar psalm       # Psalm errorLevel 5 (stubs via php-stubs/wordpress-stubs)

# Phase 1 — bootstrap smoke suite (WP-free boot lifecycle, 24 assertions)
php bin/smoke-phase1.php            # exit 0 = all assertions pass

# Phase 2 — framework smoke suite (container/events/hooks/registry/factory/config/cache/providers, 39 assertions)
php bin/smoke-phase2.php            # exit 0 = all assertions pass

# Node toolchain
npm run lint                        # ESLint (flat config)
npm run format:check                # Prettier check
npm run typecheck                   # tsc --noEmit
npm run build                       # Vite build → assets/dist (Phase 7 expands)

# Everything at once
npm run check                       # lint + format + typecheck
```

## Quality gates (Phases 1–2)

Every commit must pass:

1. `php -l` on every `.php` file under `app/` + `bin/`
2. `composer validate` + successful `composer dump-autoload`
3. PHPCS — zero errors, zero warnings
4. PHPStan level 5 — zero errors
5. Psalm errorLevel 5 — zero errors
6. `php bin/smoke-phase1.php` — 24/24 bootstrap assertions
7. `php bin/smoke-phase2.php` — 39/39 framework assertions (Phase 1 regression included)
8. ESLint + Prettier + `tsc --noEmit` + Vite build
9. `bin/verify-lumina-integrity.sh` — Lumina self-integrity gate

CI runs all of these on push/PR (`.github/workflows/ci.yml` at repo root).

## Integrity gate (ADR-004)

```bash
bash bin/verify-lumina-integrity.sh
```

Hashes every shipped Lumina theme file against the audited SHA-256 baseline
recorded at the release freeze. Fails on any unexpected difference. The
baseline is regenerated when a deliberate, reviewed change ships.

## Git workflow

- Branch `main` is protected; work on `feature/*` branches, PR to merge.
- Conventional commits (`feat:`, `fix:`, `chore:`, `docs:`, `test:`).
- Rebase-based sync (`git pull --rebase`) keeps history linear.
- Phase gates: a phase is only "Completed" after its verification report passes
  (`MASTER_ROADMAP.md` + `docs/` report per phase).

## Conventions quick reference

| Concern                            | Convention                             |
| ---------------------------------- | -------------------------------------- |
| Namespace                          | `Lumina\Core\` (PSR-4 → `app/`)        |
| Global functions / options / hooks | `lumina_*`                             |
| Asset handles                      | `lumina-*`                             |
| Domain events                      | `lumina_core:event` (ADR-006)          |
| Feature flags                      | `lumina_feature_*`                     |
| Text domain                        | `lumina`                               |
| PHP                                | strict types, WPCS, PHPStan 5, Psalm 5 |
| Versioning                         | SemVer `0.x` — see `docs/versions.md`  |
