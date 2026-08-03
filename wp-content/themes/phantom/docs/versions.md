# Versioning Policy — Phantom Core

> Required by Phase 0 acceptance criteria: "Semver policy `0.x` documented in `docs/versions.md`."

## Policy

- **Semantic Versioning 2.0** (`MAJOR.MINOR.PATCH`).
- **Pre-1.0 rule:** while the framework is `0.x`, **any change may be a breaking change**. Consumers must not assume stability of any public API before `1.0.0`.
- `PATCH` (`0.x.Y`) — bug fixes, internal refactors, documentation. No public-API change.
- `MINOR` (`0.Y.0`) — new backwards-compatible-ish features. Because we are pre-1.0, this is where most feature work lands.
- `MAJOR` (`1.0.0`+) — reserved for the first stable, API-locked release. After `1.0.0`, standard SemVer rules apply (breaking changes bump MAJOR).

## Where versions live

| Artifact            | Location                                    | Notes                                        |
| ------------------- | ------------------------------------------- | -------------------------------------------- |
| Child theme version | `style.css` header `Version:`               | Must match `Version::VERSION`                |
| Framework version   | `app/Core/Version.php` → `Version::VERSION` | Single source of truth                       |
| PHP package version | `composer.json` `version` field             | Mirrors `Version::VERSION` on tag            |
| NPM asset pipeline  | `package.json` `version`                    | `0.x`; asset source is an internal toolchain |
| Release tag         | `v0.1.0`, `v0.2.0`, …                       | Git tags drive CI + zips (Phase 17)          |

## API level

`Version::API_LEVEL` increments whenever a public API changes incompatibly, even
during `0.x`. It is the tool that tells integrators "your code may break" without
waiting for a MAJOR bump. Cache keys, transient namespaces, and option schemas
embed the API level so stale caches can never survive an upgrade.

## Feature flags

Every phase ships behind a `phantom_feature_*` flag (ADR-002). Flags default
**off** for unshipped subsystems, **on** for complete ones. Toggling is
per-environment via `phantom.env.json` (ADR-011) — never edited in code.

## Change discipline

- Every change lands as a conventional commit (`feat:`, `fix:`, `chore:`, …).
- `MASTER_ROADMAP.md` status changes require a PR with the linked plan diff.
- A `CHANGELOG.md` is generated per release (Phase 17), not hand-maintained mid-cycle.
