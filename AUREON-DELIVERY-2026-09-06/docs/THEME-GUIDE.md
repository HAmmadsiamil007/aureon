# AUREON — Theme & Design Guide (updated 2026-09-06)

This supersedes older `HOW-TO-INSTALL` notes on theme selection. Install steps: `HOW-TO-INSTALL.md` (same folder). Docker: `DOCKER.md`.

## The two themes in the repo

| Theme | Path | Use |
|---|---|---|
| **Aureon** (`AUREON_VERSION 3.6.1`) | `AUREON-WORDPRESS-DEPLOY/themes/aureon/` | **The active platform theme.** Required. Provides the WordPress bootstrap, `template_include` routing (priorities 99/998), the AETHER engine boot, and the standalone WC page templates (cart/checkout/account/auth). |
| `aureon/theme/` (nested copy) | legacy duplicate inside the tree | **Never install.** Legacy duplication kept only for history (decision Q4 archives it). |

A `twentytwentyfive` default may exist in a fresh WordPress install — activating it is harmless but shows no AUREON design; always `wp theme activate aureon`.

## How "the theme you see" actually works

The visible design is **not** the theme's own markup. Aureon boots the AETHER engine (`wp-content/frontend/`), which resolves a **design pack**:

```
wp theme activate aureon          → platform theme (required base)
wp-content/frontend/designs/vineta/  → the visible client design (complete-page mode)
```

- The theme + engine render WordPress/WooCommerce data through a stable contract (`VinetaPageData`, `data-aureon-slot` DOM hooks, cart AJAX endpoints).
- Presentation lives in the pack's frozen HTML/CSS/JS — future design edits are **frontend-only** as long as the selector contract (`tests/contract-tests.cjs` in the repo) passes.

## Design packs available

| Pack | Location | Status |
|---|---|---|
| **vineta** | `frontend/designs/vineta/` | **Active by default** (decision Q3). Multipurpose eCommerce client pack, `complete_page: true`, 58 templates, demo dataset included. |
| fermliving / lumen | repo history (root `frontend/`, archived per Q4) | Legacy packs from previous generations — **not shippable** without a migration pass; kept for reference/rollback. |

## Installing / switching another theme or design

```bash
# activate the platform theme (always)
wp theme activate aureon

# select the design pack (any directory under wp-content/frontend/designs/)
wp option update aether_active_design vineta
# or hard-pin it in wp-config.php:
#   define( 'AETHER_DESIGN', 'vineta' );
```

To add a **new** design pack later: create `wp-content/frontend/designs/<slug>/` with `manifest.json` (pages map + assets + `complete_page` flag), `composer.php`, tokens, HTML/CSS/JS — then set the option. The engine requires no core changes (see `docs/forensics/FUTURE-DESIGN-EDIT-CONTRACT.md`).

## Required companions

- **Plugin `aureon-studio` 1.1.0** (`plugins/aureon-studio/`) — platform modules; activate with the theme.
- **MU-plugin `ob-buffer.php`** (`mu-plugins/`) — output buffering required for WooCommerce redirects; must be present in `wp-content/mu-plugins/`.
- **WooCommerce** — the storefront, cart, checkout, account flows are genuine WooCommerce; install and activate before judging shop pages.

## Integrity rule

This theme/design combination ships as release candidate **RC-2026-09-06** (SHA-256 manifest, 1,972 files). Any file you edit afterwards creates a new release candidate — regenerate the manifest and re-run regression before deploying.
