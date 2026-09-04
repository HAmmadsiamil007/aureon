# TESTED BUILD IDENTITY

**Date:** 2026-09-04 (release-prep reconciliation)
**Companion:** `docs/forensics/RELEASE-WORKTREE-RECONCILIATION.md` · `FINAL-RELEASE-MANIFEST-DRAFT.json`

---

## 1. QUESTION

Which exact file set produced the successful Stage-B runtime QA
(commit `9315121`, client-work status `VINETA_CLIENT_READY_BLOCKED` → production status `AUREON_CLIENT_PRODUCTION_READY_BLOCKED`), and can that set be
reconstructed unambiguously for production deployment?

## 2. FINDING

```
RELEASE_IDENTITY_CONFIRMED   — canonical runtime tree (aureon/)
RELEASE_IDENTITY_BLOCKED     — commit 9315121 alone, the deploy mirror, and production gates
```

**Commit `9315121` ≠ the complete tested release.** The commit staged only 12 files.
The tested runtime additionally contained:

- 8 modified files not staged (`theme/inc/frontend.php`, `theme/inc/aether-cart.php`,
  `frontend/views/design.php`, `theme/ferm-page.php`, `ferm-page.php`,
  `designs/vineta/index.html`, `designs/vineta/js/shop.js`,
  `designs/vineta/js/vineta-path-bridge.js`)
- 513 deletions (Ferm Living pack + legacy theme integration + lumen + testclient + helpers)
- 7 untracked runtime files (`_so_import/*`, `css/monochrome-black.css`,
  `sections/section-checkout.php`, `theme/ferm-page.php.old`)

## 3. EVIDENCE

| # | Evidence | Supports |
|---|---|---|
| 1 | docker-compose mounts only `./aureon/*` — mirrors are never runtime | Canonical tree = the tested tree |
| 2 | All canonical mtimes ≤ 12:45:07 on 09-04; Stage-B session ended 12:49:36 (commit) — **no canonical file changed after the tests** | Current tree == tested tree |
| 3 | Acceptance matrix written 12:48:41, readiness doc 12:48:56, Stage-B doc 12:49:08 — all reference the final state | Final state was the tested state |
| 4 | Stage-B doc §5: incremental edits marked; “pre-existing uncommitted changes from earlier rounds” present in the diff | 8 M files were live during tests |
| 5 | `designs/` dir mtime 09-02 18:10; Stage-B routes show only Vineta; SOLEORIGINE verification 09-03 21:04 on the same store | Deletions pre-date tests; tree was Vineta-only |
| 6 | Full recursive `diff -rq`: `aureon/theme` ≡ `AUREON-WORDPRESS-DEPLOY/aureon/theme`; vineta dir ≡ except `index.html`; engine dirs ≡ | Deploy mirror is in sync except one stale file |
| 7 | Golden copy frozen pre-Stage-B (doc: “left immutable”; 4 vineta files differ, missing `cursor-close.svg`, `.bak-phase3` present) | Golden ≠ tested build (by design) |
| 8 | Docker not currently running → Stage-B proof is documentary (matrices/docs), not reproducible live today | Production gates remain the real proof |

## 4. TESTED BUILD DEFINITION (release-candidate)

```
aureon/theme/          176 files   5,640,959 B
aureon/frontend/       595 files  23,167,853 B   (vineta pack = 444 files / 21,208,382 B)
aureon/plugin/         317 files   4,041,889 B
aureon/ferm-page.php     1 file    (mounted over theme copy)
─────────────────────────────────────────────
total ≈ 1,089 files ≈ 32.9 MB — captured from the WORKING TREE, not from HEAD
```

Runtime plugin inventory (from `aureon/plugin/`): Aureon companion plugin —
untouched in the working tree (0 changes).

## 5. STATUS (per final local checkpoint)

```text
LOCAL CODE / INTEGRATION              COMPLETE (client-work status VINETA_CLIENT_READY_BLOCKED)
PRODUCTION ENVIRONMENT VERIFICATION   REMAINING (honest status AUREON_CLIENT_PRODUCTION_READY_BLOCKED)

TESTED BUILD → PRODUCTION HOST → PRODUCTION SMOKE → MAIL TEST → PAYMENT SANDBOX
             → FINAL ACCEPTANCE → RELEASE FREEZE → AUREON_CLIENT_PRODUCTION_READY_PASS
```

Evidence-based corrections to the summary rows:
- **Variable product ✅ → N/A.** The store contains 0 variable products; the variation UI was exercised earlier against other data but is not retestable against this client's real data until a variable product is added (readiness report limitation 2).
- **Mirror synchronization ✅ → PARTIAL.** `AUREON-WORDPRESS-DEPLOY/frontend/designs/vineta/index.html` is stale vs the tested canonical tree (09-03 20:58 sync; canonical edited 09-04 10:00); `AUREON-GOLDEN-COPY` is deliberately frozen pre-Stage-B. Re-sync before any host upload.

## 6. REQUIRED BEFORE PRODUCTION (closes the BLOCKED parts)

1. Capture the release from the working tree (8 M + 513 D + 7 ?? runtime files);
   commit decision recorded in the reconciliation doc §8.
2. Re-sync `AUREON-WORDPRESS-DEPLOY/frontend/designs/vineta/index.html` from canonical
   (stale since 09-03 20:58; canonical edited 09-04 10:00) and re-md5 the vineta dir.
3. Decide Golden Copy policy (refresh vs documented pre-Stage-B archive).
4. Review/accept the protected-layer deltas (generic Core fixes + the `design.php`
   Vineta-default flip) — no formal CORE-CHANGE-REQUEST exists for them.
5. Execute production gates A1–A9 (host, SMTP, payment sandbox, route/commerce/auth/
   Customizer/menu regression) — `VINETA_CLIENT_READY_BLOCKED` persists until then.

## 7. STATEMENT

No source file was modified, no git operation was performed, and no mirror was
synced to produce this verdict. The identity rests on repository evidence and the
session's documentary records; live re-verification is a production gate, not an
assumption.
