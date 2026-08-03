# Phantom

**Token-driven premium frontend framework for WordPress** — delivered as a
GeneratePress child theme.

Phantom Core (the framework layer) provides the service container, design-token
engine, render engine, component registry, and plugin bridges that power the
Phantom frontend — while **GeneratePress, GP Premium, WooCommerce, and WordPress
Core remain 100% untouched** (byte-verified by CI, ADR-004).

## Status

**Phase 0 — Project Foundation: `Completed`** (see `docs/` and
`Report/MASTER_ROADMAP.md`).

Phases 1–17 follow the master implementation plan
(`Report/PHASE_5_PHANTOM_CORE_IMPLEMENTATION_PLAN.md`), each gated by its own
verification report.

## Layout (Phase 0)

```
phantom/
├─ app/          Phantom Core — PSR-4 namespace Phantom\Core\ (app/Core/Version.php)
├─ assets-src/   SCSS/TS/fonts/images source (pipeline built in Phase 7)
├─ templates/    Theme templates (Phase 6/12)
├─ template-parts/  Partial components
├─ inc/          Child-theme glue
├─ tests/        PHPUnit + WP_Mock (Phase 15)
├─ e2e/          Playwright (Phase 15)
├─ docs/         Architecture, versioning, development guide
└─ bin/          Dev tooling scripts
```

## Getting started

```bash
bash bin/setup-toolchain.sh   # project-local Composer + PHP deps + npm deps
php tools/composer.phar check # PHPCS + PHPStan + Psalm
npm run check                 # ESLint + Prettier + tsc
bash bin/verify-parent-integrity.sh  # ADR-004 gate
```

See `docs/development.md` for the full guide.

## Documentation

- `docs/architecture.md` — canonical structure, conventions, ADRs
- `docs/versions.md` — semver `0.x` policy
- `docs/development.md` — tooling and quality gates
- `Report/MASTER_ROADMAP.md` — phase tracker + ADR table (source of truth)
- `Report/PHASE_5_PHANTOM_CORE_IMPLEMENTATION_PLAN.md` — engineering spec
- `Report/master_architecture_blueprint.md` — architecture blueprint

## License

GPL-2.0-or-later — Phantom Core is a child theme of GeneratePress (GPL-2.0+).
GP Premium remains a separate commercial product with its own license.
