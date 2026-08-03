# Architectural Decision Records — Phantom Core

Every significant architectural decision is recorded here. Records are
append-only — never rewrite history, always add a new ADR.

## Index

| ADR                   | Decision                                                                                                         | Status   |
| --------------------- | ---------------------------------------------------------------------------------------------------------------- | -------- |
| [ADR-001](ADR-001.md) | Phantom is a GeneratePress child theme                                                                           | Accepted |
| [ADR-002](ADR-002.md) | Naming: `Phantom\Core`, `phantom_`, `phantom-*`                                                                  | Accepted |
| [ADR-003](ADR-003.md) | No magic numbers — design tokens as CSS custom properties                                                        | Accepted |
| [ADR-004](ADR-004.md) | Public WP/GP APIs only; CI hash-gate on parent dirs                                                              | Accepted |
| [ADR-005](ADR-005.md) | Vite build pipeline with manifest + dev server                                                                   | Accepted |
| [ADR-006](ADR-006.md) | Domain events `phantom_core:*` vs WP actions                                                                     | Accepted |
| [ADR-007](ADR-007.md) | Bridges are capability adapters; plugins untouched                                                               | Accepted |
| [ADR-008](ADR-008.md) | Rebranding is planning-only                                                                                      | Accepted |
| [ADR-009](ADR-009.md) | PSR-4 autoload via Composer `Phantom\Core\` → `app/`                                                             | Accepted |
| [ADR-010](ADR-010.md) | Caching via WP Transients + object-cache abstraction                                                             | Accepted |
| [ADR-011](ADR-011.md) | Environment via `wp_get_environment_type()` + `phantom.env.json`                                                 | Accepted |
| [ADR-012](ADR-012.md) | PHPCS + PHPStan level 5 + Psalm; tests WP_Mock/BrainMonkey + Playwright                                          | Accepted |
| [ADR-013](ADR-013.md) | Phase 1 bootstrap: load.php → Kernel → Sequencer; App facade; `plugins_loaded` priority 5                        | Accepted |
| [ADR-014](ADR-014.md) | Phase 2 framework: DI Container, Events, Hooks, Registries, Factory, Config Repository, Cache, Service Providers | Accepted |

Source of truth for phase status: `Report/MASTER_ROADMAP.md`.
