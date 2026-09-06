# 01 — Canonical Tree Audit

**Audit date:** 2026-09-06 · **Method:** read-only file-tree diffing + git history · **No source files modified.**

## Verdict (one line)

The repo contains **six near-duplicate copies of the application**; `AUREON-WORDPRESS-DEPLOY/` is the only current canonical tree (newest mtimes, matches latest commits), and everything else is a stale copy that will drift further with every commit.

## Tree census (file counts, .serena/node_modules excluded)

| Tree | Files | Role (documented) | Role (evidence) | Last relevant activity |
|---|---|---|---|---|
| `AUREON-WORDPRESS-DEPLOY/` | 1,983 | Deploy tree | **Canonical.** Designs: `vineta` only. Newest mtimes (2026-09-05). Matches commit history (auth pages, checkout fixes). | **Active** |
| `AUREON-GOLDEN-COPY/` | 1,977 | Golden Copy | Near-identical copy of deploy tree (12 file-level diffs vs deploy). No vineta components dir. | Frozen snapshot |
| `aureon/` | 1,709 | Source of truth? | **Stale.** `designs/` = vineta but ~100 files differ from deploy (older ferm-page.php 2026-09-03 vs 09-05, older vineta HTML/JS). No `components/` shadow, no standalone checkout/account work. | Frozen ~09-03 |
| `theme/` | 1,091 | Local staging copy | gitignored (`.gitignore` line: `/theme/`). 146 files differ from deploy tree. Contains `theme/mu-plugins/aureon-fix-wc-session.php` (exists nowhere else). | Staging, untracked |
| `frontend/` (root) | 598 | Client frontend | **Orphaned.** Designs: `fermliving`, `lumen` — **no vineta**. Nothing in the deploy tree references this directory (checked all `require`/path construction: engine resolves only `content()/frontend/`). Has own test suite (`frontend/tests/`). | Legacy (Ferm era) |
| `aureon/theme/`, `aureon/frontend/` | — | Nested copies | `aureon/` contains its own `theme/`, `frontend/`, `plugin/` sub-trees duplicating the above. | Stale |

## Critical inconsistencies found (documented, not fixed)

1. **Root `frontend/` is dead but still consumes attention.** Its designs (`fermliving`, `lumen`) belong to the previous design generation. Its Playwright test suite (`frontend/tests/specs`) targets paths that the deploy tree does not serve. Any test run against it tests nothing that runs.
2. **`aureon/` claims to be source but is not current.** `aureon/theme/ferm-page.php` (2026-09-03) lacks the checkout/account routing fixes present in `AUREON-WORDPRESS-DEPLOY/themes/aureon/ferm-page.php` (2026-09-05). Last commits (39e8215 auth header/footer, 5ce4dd5 My Account dashboard) are visible only in the deploy tree.
3. **Golden Copy is not pristine.** It differs from deploy in 12 places; some golden files are *older* than deploy, some newer. It cannot currently serve as a trusted rollback target.
4. **Nested duplicate trees inside trees.** `AUREON-WORDPRESS-DEPLOY/themes/aureon/theme/` contains a *second* theme copy including `theme/frontend/designs/fermliving/...` (legacy pack inside the deploy tree). `aureon/theme/theme/` likewise. `ferm-page.php` exists at 9 paths across the repo.
5. **Untracked runtime artifacts at repo root** (never committed, appeared 09-05):
   - `enable_cod.php` — one-off script that hard-enables WooCommerce Cash on Delivery by writing `woocommerce_cod_settings` directly. Has a hardcoded `/var/www/html/wp-load.php` path → **server-side config mutation script checked into workspace**. If executed on production it silently changes payment config.
   - `update-contact.php` — same pattern; writes placeholder contact info (`+92 300 1234567`, San Francisco) into `aureon_settings`. Overwrites real client data with demo data if run.
   - `rendered-home.html` — 0 bytes.
6. **`AUREON-WORDPRESS-DEPLOY/frontend/designs/vineta/components/shell/header.php` is untracked.** The newest commit's feature (frozen Vineta header/footer on auth pages) depends on a file that is not in git. A fresh clone would lose the header on auth pages.
7. **`.gitignore` hides real code**: `/theme/` (staging) is ignored, so tracked-vs-untracked status cannot be used to reason about which theme copy is real without this audit.

## Canonical identity resolution (evidence)

- Theme version constant: `AUREON_VERSION = '3.6.1'` (functions.php, deploy tree).
- Aureon Studio plugin: `Version: 1.1.0`.
- Active design: `aether_active_design()` hardcodes fallback `'vineta'` (deploy `frontend/views/design.php` line ~54) — regardless of the docblock that says `'luxury'`.
- The only tree whose `frontend/designs/` contains `vineta/` plus the untracked `components/shell/header.php` is `AUREON-WORDPRESS-DEPLOY/`.
- Docker/runtime mount: not present in this repository (no `docker-compose`, no `wp-config.php` tracked — gitignored). The hardcoded `/var/www/html/` in the two root scripts is the only runtime path evidence; **the actual WordPress runtime tree is not in this repo** and must be confirmed by the user (→ QUESTIONS.md).

## Recommendation (documentation only)

1. Declare `AUREON-WORDPRESS-DEPLOY/` canonical in writing; archive `aureon/`, `frontend/`, `theme/`, and nested `theme/` duplicates.
2. Commit or delete the untracked `vineta/components/shell/header.php` (P0: a deployed feature depends on an untracked file).
3. Delete or move `enable_cod.php` / `update-contact.php` out of any deployable path (P0: config-mutating scripts in workspace root).
4. Re-freeze Golden Copy *from* canonical after this audit.
5. Confirm the true runtime tree (`/var/www/html`) is managed by a deploy step, not by copying these folders by hand (this is the suspected cause of the 6-tree drift).
