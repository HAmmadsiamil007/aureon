# RELEASE CANDIDATE REPORT — Lumina Theme / Lumina Core

- **Version:** 0.14.0 (RC1)
- **Date:** 2026-08-04
- **Phase:** 15.5 (Production Freeze & Release Candidate)
- **Status:** ✅ **RC1 — FINAL RELEASE CANDIDATE** (no RC2 required)

---

## 1. Release Candidate Process (documented flow)

```
RC1 (v0.14.0)          ← this build
   ↓
Full regression        ← 14/14 smoke suites + integrity + static analysis
   ↓
Fix only Critical/High ← none found → no code fixes required
   ↓
RC2 (if required)      ← NOT required (0 Critical, 0 High)
   ↓
Full regression        ← n/a
   ↓
Final Release Candidate ← RC1 stands as the Final RC
```

**RC iteration log:**

| RC  | Version | Date       | Changes from previous  | Regression | Decision     |
| --- | ------- | ---------- | ---------------------- | ---------- | ------------ |
| RC1 | 0.14.0  | 2026-08-04 | Baseline (Phases 0–15) | 14/14 ✅   | **Final RC** |

Only one RC was required: the full regression produced zero Critical and
zero High defects, so no RC2 iteration was needed. The only change in this
phase was the version-consistency alignment of `package.json` +
`package-lock.json` to `0.14.0` (allowed: version consistency), verified by
a re-run of the full gate.

## 2. RC1 Validation Evidence

| Gate                            | Result                                                                     |
| ------------------------------- | -------------------------------------------------------------------------- |
| Smoke suites (14)               | ✅ 425 assertions, 0 failures                                              |
| Integrity (GP + Premium)        | ✅ 473/473 byte-identical                                                  |
| PHPCS / PHPStan / Psalm         | ✅ 0 errors each                                                           |
| npm check (ESLint/Prettier/tsc) | ✅ exit 0                                                                  |
| Vite production build ×2        | ✅ deterministic hashes                                                    |
| Security re-scan                | ✅ clean (secrets/dangerous fns/superglobals/debug/remote/TODO all absent) |
| Version consistency             | ✅ 0.14.0 everywhere (incl. npm)                                           |
| Performance budgets             | ✅ CSS 32.5 kB ≤ 50 · JS 78.9 kB ≤ 120 · gzip ~36 kB                       |
| Accessibility audit             | ✅ 42/42 (smoke-phase14)                                                   |

## 3. Version Consistency Verification (frozen)

| File                             | Version                     |
| -------------------------------- | --------------------------- |
| `app/Core/Version.php`           | 0.14.0                      |
| `style.css`                      | 0.14.0                      |
| `composer.json`                  | 0.14.0                      |
| `composer.lock` (content-hash)   | synced `66d42788…`          |
| `package.json`                   | 0.14.0 (aligned this phase) |
| `package-lock.json`              | 0.14.0 (aligned this phase) |
| `CHANGELOG.md` head              | 0.14.0                      |
| `bin/smoke-phase1.php` assertion | 0.14.0                      |
| Verification reports             | 0.14.0 consistent           |

**No mismatches permitted and none remain.**

## 4. Release Candidate Reproducibility

- Clean-clone sequence documented in `BUILD_REPRODUCIBILITY_REPORT.md`.
- Two consecutive builds produced **byte-identical** outputs.
- CI enforces the exact sequence (bootstrap/static-analysis/assets/
  integrity jobs).

## 5. Outstanding Issues (must be Low or Informational)

| #   | Severity      | Item                                                                         | Status                                                          |
| --- | ------------- | ---------------------------------------------------------------------------- | --------------------------------------------------------------- |
| 1   | Informational | npm package version was 0.1.0 — aligned to 0.14.0 this phase                 | ✅ Closed                                                       |
| 2   | Informational | `composer.lock` has no root `name` — expected Composer 2 behavior            | ✅ Documented (DEPENDENCY_INVENTORY)                            |
| 3   | Low           | Browser-level Lighthouse/screen-reader/device runs require a staging install | Deferred to Phase 16+ staging (documented in KNOWN_LIMITATIONS) |

No Critical or High issues.

## 6. Conclusion

RC1 (v0.14.0) passes every release-qualification gate with objective
evidence, is byte-reproducible, and carries no Critical or High defects.
**RC1 is declared the FINAL RELEASE CANDIDATE** and becomes the permanent
baseline for Phase 16 rebranding.

**STATUS: ✅ RC1 — FINAL RELEASE CANDIDATE — v0.14.0**
