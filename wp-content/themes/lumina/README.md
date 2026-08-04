# Lumina

**Token-driven premium frontend framework for WordPress** — a fully
standalone theme with an original companion plugin. No parent theme required.

Lumina provides the service container, design-token engine, render engine,
component registry, template library, asset pipeline, plugin bridges, and
animation engine that power a complete premium frontend — while **WordPress
Core and all plugins remain 100% untouched**.

## Status

- **Phases 0–15.5:** `Completed` — framework, frontend, performance,
  accessibility, QA, and production freeze (see `docs/` and
  `Report/MASTER_ROADMAP.md`).
- **Phase 16 (Safe Rebranding):** `Completed` — Lumina is now a standalone
  theme; the original **Lumina Companion** plugin ships alongside it.

## Layout

```
lumina/
├─ app/             Lumina Core — PSR-4 namespace Lumina\Core\ (app/Core/Version.php)
├─ assets-src/      SCSS/TS/fonts/images source (pipeline built in Phase 7)
├─ templates/       Theme templates + component/template library (Phases 6/11/12)
├─ template-parts/  Partial components
├─ inc/             Theme glue — no business logic
├─ tests/           PHPUnit + WP_Mock
├─ e2e/             Playwright
├─ docs/            Architecture, versioning, development guide, reports
└─ bin/             Dev tooling + smoke suites (WP-free)
```

## Getting started

```bash
bash bin/setup-toolchain.sh            # project-local Composer + PHP deps + npm deps
php tools/composer.phar check          # PHPCS + PHPStan + Psalm
npm run check                          # ESLint + Prettier + tsc
bash bin/verify-lumina-integrity.sh    # ADR-004 self-integrity gate
```

See `docs/development.md` for the full guide.

## Documentation

- `docs/architecture.md` — canonical structure, conventions, ADRs
- `docs/versions.md` — semver policy
- `docs/development.md` — tooling and quality gates
- `Report/MASTER_ROADMAP.md` — phase tracker + ADR table (source of truth)
- `Report/PHASE_5_LUMINA_CORE_IMPLEMENTATION_PLAN.md` — engineering spec
- `Report/master_architecture_blueprint.md` — architecture blueprint

## License

GPL-2.0-or-later — Lumina is an original, independent implementation. All
code in this repository is original work with Lumina copyright headers.
