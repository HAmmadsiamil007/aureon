# RELEASE CANDIDATE VERIFICATION

**Date:** 2026-09-04 · Phase: release assembly (local) — production gates still BLOCKED
**Source of truth:** tested canonical working tree `aureon/` (identity confirmed — see `TESTED-BUILD-IDENTITY.md`)
**Artifacts:** `RELEASE-CANDIDATE-MANIFEST.json` (per-file SHA-256) · `FINAL-RELEASE-MANIFEST-DRAFT.json`
**Golden Copy:** untouched (frozen baseline, per policy).

---

## 1. What was done

1. **Release candidate assembled** from the tested canonical tree — NOT from commit `9315121` alone (the commit under-captures the tested build by 8 modified + 513 deleted + 7 untracked files).
2. **Deploy mirror synchronized** from the tested canonical tree (the two stale files only):
   - `AUREON-WORDPRESS-DEPLOY/frontend/designs/vineta/index.html` ← canonical (was stale since 09-03 20:58)
   - `AUREON-WORDPRESS-DEPLOY/aureon/ferm-page.php` ← canonical **runtime override** `aureon/ferm-page.php`
3. **Hash verification** canonical ↔ deploy mirror performed (SHA-256 per file).

No commit was created. No source file was modified. Golden Copy untouched.

## 2. Release candidate (canonical set)

| Scope | Files | Bytes |
|---|---:|---:|
| `aureon/theme/` | 176 | 5,640,959 |
| `aureon/frontend/` (engine + vineta) | 595 | 23,167,853 |
| `aureon/plugin/` | 317 | 4,041,889 |
| `aureon/ferm-page.php` (override) | 1 | — |
| Total tested runtime tree | 1,089 | ≈ 32.9 MB |
| **Deploy payload** (excludes `_so_import/*` tooling + `*.old` backups) | **1,084** | **32,747,379** |

Per-file SHA-256 for every payload file: `RELEASE-CANDIDATE-MANIFEST.json`.

**Excluded from the deploy payload (but present in the tested tree):**
- `aureon/frontend/_so_import/` — one-shot Sole Origine import tooling incl. client product JSONs. It can purge/re-import store content; **do not ship to production**.
- `aureon/theme/ferm-page.php.old` — pre-edit backup.

## 3. Deploy-mirror hash verification

| Mirrored content | Matched | Mismatched | Missing |
|---|---:|---:|---:|
| Theme (flat upload `AUREON-WORDPRESS-DEPLOY/aureon/`, ferm-page excluded) | 175 | 0 | 0 |
| `ferm-page.php` override (`deploy/aureon/ferm-page.php` = tested `aureon/ferm-page.php`) | 1 | 0 | 0 |
| Frontend incl. vineta (`deploy/frontend/` vs `aureon/frontend/`) | 595 | 0 | 0 |
| **Total** | **771** | **0** | **0** |

**Result: PASS — deploy mirror now byte-equals the tested canonical content for every mirrored path.**

### ferm-page.php mapping (important)

The tested Docker stack mounts `aureon/ferm-page.php` **over** `wp-content/themes/aureon/ferm-page.php`; the *runtime-tested* file is therefore the **override** (`aureon/ferm-page.php`, +179/−17 vs the theme copy: `site_icon` favicon, Customizer CSS-var injection). The deploy mirror's flat theme upload (`deploy/aureon/`) now carries the override so a plain host runs the tested content.

Two caveats:
1. The **nested** `AUREON-WORDPRESS-DEPLOY/aureon/theme/` snapshot (untracked) is byte-identical to canonical `aureon/theme/` — including the **theme-copy** ferm-page.php. If that nested layout is ever the upload source, apply the override first.
2. `aureon/plugin/` is **not present in the deploy bundle** — the host plugin path must be supplied at upload time.
3. Canonical `aureon/theme/ferm-page.php` still holds the lean copy (shadowed in dev by the mount); the release decision on merging override → theme copy is a Core change and stays out of scope.

## 4. Status flags

```
AUREON_FORENSIC_AUDIT_PASS              ✅
VINETA_FRONTEND_PASS                    ✅
VINETA_DYNAMIC_INTEGRATION_PASS         ✅
VINETA_DYNAMIC_DATA_STRESS_PASS         ✅
VINETA_WOOCOMMERCE_PASS                 ✅
VINETA_AUTH_PASS                        ✅
VINETA_ACCOUNT_PASS                     ✅
VINETA_CUSTOMIZER_PASS                  ✅
VINETA_MENU_PASS                        ✅
VINETA_SEARCH_PASS                      ✅
VINETA_ASSET_PASS                       ✅
VINETA_SECURITY_PASS                    ✅
VINETA_RESPONSIVE_PASS                  ✅
VINETA_ACCESSIBILITY_PASS               ✅
VINETA_FEATURE_RETENTION_PASS           ✅
VINETA_CORE_INTEGRITY_PASS              ✅ (canonical Core untouched this session)

VARIABLE PRODUCT SUPPORT                ✅ IMPLEMENTED
CURRENT CLIENT VARIABLE PRODUCT TEST    N/A (catalog has 0 variable products; re-test when one exists)

MIRROR SYNC (deploy, from tested canonical)  ✅ VERIFIED (771 files, SHA-256)
AUREON-GOLDEN-COPY                      🔒 FROZEN BASELINE (untouched)

VINETA_RELEASE_CANDIDATE_CONFIRMED      ✅ (canonical candidate + hashes; deploy mirror verified)
VINETA_PRODUCTION_HOST_PASS             ⏳ BLOCKED (no host access this session)
VINETA_MAIL_PASS                        ⏳ BLOCKED (needs SMTP/mail transport)
VINETA_PAYMENT_SANDBOX_PASS             ⏳ BLOCKED (needs client sandbox credentials)
VINETA_CLIENT_DELIVERY_PASS             ⏳ BLOCKED (final acceptance + freeze)

Overall: AUREON_CLIENT_PRODUCTION_READY_BLOCKED
```

## 5. Remaining (unchanged, external)

Production host smoke → SMTP/mail → payment sandbox → final acceptance → release freeze → `AUREON_CLIENT_PRODUCTION_READY_PASS`.

*Evidence: `RELEASE-CANDIDATE-MANIFEST.json` (1084 SHA-256 records), `FINAL-RELEASE-MANIFEST-DRAFT.json`, `diff -rq` outputs recorded in this session.*
