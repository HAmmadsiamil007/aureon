# AUREON / VINETA — Delivery Package

**Date:** 2026-09-06 · **RC identity:** RC-2026-09-06 · commit `1289995` (tree unchanged through `384a231`) · SHA-256 manifest: `AUREON-WORDPRESS-DEPLOY` = the tested 1,972-file release artifact.

## What is in this package

| Folder | What it is | Role |
|---|---|---|
| `AUREON-WORDPRESS-DEPLOY/` | The **tested release candidate** — exact canonical tree (theme `aureon` 3.6.1, plugin `aureon-studio` 1.1.0, AETHER engine, Vineta design pack, docs). Deploy THIS to WordPress `wp-content/`. | Deployment package / release artifact |
| `AUREON-GOLDEN-COPY/` | Frozen pre-release snapshot (pre-auth-pages generation). **Immutable baseline — never deploy, never modify.** | Rollback baseline |
| `docs/` | Full documentation: forensic audits (`docs/forensics/`), architecture reports (`docs/reports/`, `docs/docs/`), `HOW-TO-INSTALL.md`, decisions log, acceptance matrix. | Knowledge base |
| `.serena/` | Project configuration + AI-assistant memories for this codebase (tooling config, not runtime code). | Tooling |

## Layout inside WordPress (the one thing to get right)

The theme expects the `frontend/` engine **one level above the theme folder**, i.e. directly under `wp-content/`:

```
wp-content/
├── themes/aureon/            ← from AUREON-WORDPRESS-DEPLOY/themes/aureon
├── plugins/aureon-studio/    ← from AUREON-WORDPRESS-DEPLOY/plugins/aureon-studio
├── mu-plugins/ob-buffer.php  ← from AUREON-WORDPRESS-DEPLOY/mu-plugins/
└── frontend/                 ← from AUREON-WORDPRESS-DEPLOY/frontend  (ENGINE — required)
    └── designs/vineta/       ← the active client design pack
```

Full step-by-step: **`docs/HOW-TO-INSTALL.md`** and `docs/docs/HOW-TO-INSTALL.md`. Docker one-command: **`docker-compose.yml`** in this folder + `docs/DOCKER.md`.

## Theme & design switching

- Active client design: **Vineta** (`frontend/designs/vineta/`, complete-page mode). Hardcoded default by decision Q3 (see `docs/forensics/DECISIONS-LOG.md`).
- To run a different design on another install: define `AETHER_DESIGN` in `wp-config.php` **or** set the `aether_active_design` option (WP-CLI: `wp option update aether_active_design <slug>`). The engine resolves any pack under `wp-content/frontend/designs/<slug>/`.
- The AETHER/luxury engine tree is preserved in git history as the platform baseline (decision Q4: archive, never delete).

## Release state (honest)

- **37 acceptance gates: 10 PASS · 24 BLOCKED · 3 N/A · 0 FAIL** — all static/local gates pass; runtime gates await a live environment.
- Verdict: `AUREON_CLIENT_PRODUCTION_READY_BLOCKED` (blocked solely on runtime/SMTP/sandbox access, not on code).
- Reports of record: `docs/forensics/FINAL-CLOSURE-STATUS.md`, `docs/forensics/VINETA-FINAL-PRODUCTION-REPORT.md`, `test-results/` (in the deploy tree).

## Integrity

- Deploy-tree SHA-256 manifest: `AUREON-WORDPRESS-DEPLOY/../test-results/release-candidate-sha256.txt` (1,972 files; also committed to git under `test-results/`).
- Rule: any change to any file after RC `1289995` = a new release candidate with a regenerated manifest. Never deploy an unmanifested build.
