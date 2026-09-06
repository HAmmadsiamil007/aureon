# IMPLEMENTATION-PREFLIGHT

**Date:** 2026-09-06 · **Executor:** Buffy (Freebuff) · **Plan:** Master Implementation + Hardening + QA + Release Plan

## Recorded environment

| Item | Value | Evidence |
|---|---|---|
| Project root | `C:/Users/hamma/Downloads/phantom/wordpress` | `git rev-parse --show-toplevel` |
| Git branch | `master` | `git branch --show-current` |
| Git commit (pre-implementation) | `39e821516c4b65b7015f45c84dd250c39e6fc0c2` ("feat: all auth pages now use frozen Vineta header/footer") | `git rev-parse HEAD` |
| Git status | 16 untracked audit docs + 1 untracked pack dir (`vineta/components/`) + 3 root artifacts (`enable_cod.php`, `rendered-home.html`, `update-contact.php`) + untracked `AUREON-FORENSIC-AUDIT-2026-09-06/` | `git status --short` |
| Canonical tree | `AUREON-WORDPRESS-DEPLOY/` | forensic audit 01 (newest mtimes, matches HEAD) |
| Golden Copy | `AUREON-GOLDEN-COPY/` — 12 file diffs vs canonical; **not pristine**; will NOT be modified by this plan | audit 01 |
| Stale trees | `aureon/`, `theme/`, root `frontend/` — untouched by this plan | audit 01 |
| Docker mount | **NOT PRESENT in repo** — no docker-compose/wp-config tracked | audit 01/05 |
| Deployment mirror | `AUREON-WORDPRESS-DEPLOY/` doubles as mirror; sync procedure undocumented (→ blocker B-1) | audit 01 |
| Active theme | Aureon `AUREON_VERSION 3.6.1` | `themes/aureon/functions.php` |
| Active design | vineta (complete-page; hardcoded fallback — defect C1) | `frontend/views/design.php` |
| WordPress version | **UNKNOWN** — no runtime in repo | blocker B-1 |
| WooCommerce version | **UNKNOWN** — plugin not in repo | blocker B-1 |
| PHP (this machine) | 8.2.31 CLI (used for `php -l` syntax gates only) | `php -v` |
| Active production plugins | **UNKNOWN** — server-side (→ blocker B-1) | audit 08 Phase 14 |
| Database state | **UNKNOWN** — no DB in repo | audit 05 |
| Current routes | 15-route matrix defined in `14-SINGLE-FRONTEND-FULL-TEST-PLAN.md` | audit 14 |

## Recoverable checkpoint

- Everything is untracked-new (audit docs) — **zero tracked files modified so far**. Rollback = delete untracked files.
- Implementation will touch only the files listed per fix (T-01…T-16) in the canonical tree + new docs.
- Checkpoint commit will be taken before fixes; per-fix commits after.

## Runtime gate status

Phases requiring a live WordPress runtime (HTTP routes, DB, plugins, mail, payments, cache) are **BLOCKED** in this environment. They are recorded as `BLOCKED` with blocker ID **B-1** (no runtime provided; QUESTIONS.md Q1 unanswered). Code-level gates proceed.

## STOP check

Source of truth is clear: code in `AUREON-WORDPRESS-DEPLOY/` + forensic audit set. Proceeding with code-level phases only.
