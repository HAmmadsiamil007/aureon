# Phantom — Architecture Overview

Phantom is a **GeneratePress child theme** that delivers a modern, token-driven
frontend framework ("Phantom Core") on top of WordPress. It follows the master
architecture blueprint (`Report/master_architecture_blueprint.md`) and the Phase 5
implementation plan (`Report/PHASE_5_PHANTOM_CORE_IMPLEMENTATION_PLAN.md`).

## Non-negotiable rules (ADR-001 … ADR-004)

1. **Never modify GeneratePress.** **Never modify GP Premium.** **Never modify
   WooCommerce or WordPress Core.** Everything visual lives in this child theme.
2. Only **public, documented WP/GP APIs** are used — `generate_get_option()`,
   `apply_filters('generate_*')`, `do_action('generate_*')`, `wp_nav_menu`,
   widgets, template hierarchy. Internal GP symbols are never called.
3. Naming: namespace `Phantom\Core\`, global prefix `phantom_`, option prefix
   `phantom_`, hook prefix `phantom_`, asset handles `phantom-*`.
4. Presentation is **token-driven** (CSS custom properties, ADR-003). No
   hard-coded hex/px in component code.
5. A CI job hashes the GP/Premium packages and fails on any change
   (`bin/verify-parent-integrity.sh`).

## Canonical directory layout (Phase 0)

```
phantom/
├─ style.css            # WP theme header; Version: 0.1.0
├─ functions.php        # thin loader → requires app/load.php (Phase 1)
├─ theme.json           # editor defaults (vanilla)
├─ composer.json        # PSR-4 Phantom\Core\ → app/ (ADR-009)
├─ package.json         # vite, scss, ts, linters
├─ vite.config.js       # build config (expanded in Phase 7)
├─ .phpcs.xml           # PHPCS + WordPress Coding Standards
├─ phpstan.neon         # PHPStan level 5
├─ psalm.xml            # Psalm errorLevel 5
├─ .editorconfig        # editor + lint baseline
├─ app/                 # Phantom Core — namespace Phantom\Core\
│  ├─ Boot/             # Phase 1 — Kernel, Sequencer
│  ├─ Container/        # Phase 1–2 — DI container (PSR-11 style)
│  ├─ Config/           # Phase 1–2 — immutable config repository
│  ├─ Events/           # Phase 2 — domain event dispatcher
│  ├─ Hooks/            # Phase 2 — WP boundary hook manager
│  ├─ Registry/         # Phase 2 — registries
│  ├─ Factory/          # Phase 2 — factories
│  ├─ Cache/            # Phase 2 — transients / object cache
│  ├─ Tokens/           # Phase 3 — design token engine
│  ├─ Render/           # Phase 4 — render engine
│  ├─ Components/       # Phase 5 — component registry
│  ├─ Templates/        # Phase 6 — template system
│  ├─ Assets/           # Phase 7 — asset loader / manifest
│  ├─ Bridges/          # Phase 8 — plugin bridges
│  ├─ Woo/              # Phase 9 — WooCommerce bridge
│  ├─ Animation/        # Phase 10 — animation engine
│  └─ Support/          # traits & utilities (no reuse across subsystems)
├─ assets-src/          # SCSS / TS / fonts / images source (Phase 7)
├─ templates/           # Theme templates (Phase 6 / 12)
├─ template-parts/      # Partial components inventory
├─ inc/                 # Child-theme glue — no business logic
├─ tests/               # PHPUnit + WP_Mock (Phase 15)
├─ e2e/                 # Playwright (Phase 15)
├─ docs/                # Per-phase docs
└─ bin/                 # Dev tooling scripts (see docs/development.md)
```

## Bootstrap flow (target — Phase 1)

```
wp → plugins_loaded (priority 5)
   → app/load.php → Composer autoload → Boot\Kernel
   → Sequencer: config → env → flags → logger → errorHandler
   → providers (Phase 2) → do_action('phantom_core:ready')
```

Phase 0 ships no runtime entry — `functions.php` loads `app/load.php` only when
it exists, so the theme is safe to activate against the empty `app/` tree.

## Environment (ADR-011)

`wp_get_environment_type()` is the base detector. `phantom.env.json` (git-ignored;
template: `phantom.env.json.example`) overrides it per environment, holds feature
flags, debug toggles, and Vite dev-server settings. No `.env` dependency.
