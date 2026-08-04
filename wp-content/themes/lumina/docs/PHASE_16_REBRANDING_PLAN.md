# PHASE 16 — SAFE REBRANDING: LUMINA → LUMINA (MASTER PLAN)

- **Phase:** 16 (Safe Rebranding — per ADR-008, planning-first)
- **Baseline:** `v0.14.0` RC1 (frozen; functional parity is measured against it)
- **Date:** 2026-08-04
- **Status:** ⏳ PLAN APPROVED — implementation pending user go-ahead
- **Target brand:** **Lumina** — standalone WordPress theme + original companion plugin

---

## 0. EXECUTIVE SUMMARY

Transform the Lumina Core framework (a GeneratePress child theme) into
**Lumina** — a fully standalone, independently-branded WordPress theme with a
companion **Lumina Companion** plugin, with **zero detectable trace** of
GeneratePress, GP Premium, or the Lumina identity.

**Measured scope (verified by grep at planning time):**

| Asset                                                                                   | Count   |
| --------------------------------------------------------------------------------------- | ------- |
| Files scanned (php/css/json/md/ts/scss/js/yml/xml/neon, excl. vendor/node_modules/dist) | 779     |
| Files using the `Lumina\Core` namespace                                                 | **261** |
| Files mentioning "lumina"                                                               | **206** |
| Files mentioning generatepress / gp_premium                                             | **46**  |

**Frozen contract (from Phase 15.5):** the technical API surface
(`API_FREEZE.md`) and behavioral contracts (`CONTRACT_FREEZE.md`) must be
preserved **functionally** — every smoke suite must still pass 100% after
rebranding. Only identifiers, naming, and brand-visible strings change.

---

## 1. DECISIONS (confirmed with user)

| Decision     | Choice                                                                                                                                                                                                                                                                              |
| ------------ | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| Brand name   | **Lumina** (slug `lumina`, text domain `lumina`, namespace `Lumina\Core`)                                                                                                                                                                                                           |
| Independence | **Fully standalone theme** — remove `Template: generatepress`, ship own shell files                                                                                                                                                                                                 |
| Rename depth | **Complete** — namespace, handles, classes, docs, CI, composer — until zero detection remains                                                                                                                                                                                       |
| Plugin       | **Original "Lumina Companion" plugin written from scratch** (spacing/typography/page-header/secondary-nav/menu-plus equivalents as original code). GP Premium 2.5.6 is **NOT** rebranded — it is removed from the product (legal requirement: it is a commercial, non-GPL product). |
| License      | Theme + plugin: **GPL-2.0-or-later** with Lumina copyright headers (GPL is required for WP theme/plugin distribution and is fully compatible with our original code)                                                                                                                |
| Integrity    | The 473/473 integrity gate is retired in favor of a new **Lumina self-integrity gate** (the GP baseline no longer applies once standalone)                                                                                                                                          |

---

## 2. LEGAL BOUNDARIES (non-negotiable)

1. **GeneratePress** (GPL-2.0+) — we are _removing the dependency_, not
   copying or stripping its files. Its own files keep their attribution (and
   they leave the product).
2. **GP Premium 2.5.6** — **commercial, non-GPL**. Rebranding it as Lumina
   would be piracy. It is **deleted** from the repo/distribution. Lumina
   never used it at runtime (verified: zero references in app/), so no
   feature is lost.
3. **Original code** — every line of `app/`, `templates/`, `assets-src/`,
   and the new plugin is original work; it can carry Lumina's own GPL
   copyright header.
4. **Do not claim authorship of GeneratePress** — Lumina is "inspired by
   nothing"; it is an independent implementation. Docs must not imply we
   authored GP.

---

## 3. EXECUTION ORDER (STRICT — theme first, then plugin)

```
STEP 0  Safety net: tag v0.14.0-RC1 baseline + copy working tree backup
STEP 1  Standalone shell (remove parent dependency)          [THEME]
STEP 2  Complete identifier rename (Lumina → Lumina)         [THEME]
STEP 3  Brand surface (style.css header, README, docs)        [THEME]
STEP 4  Toolchain & CI rebrand                                 [THEME]
STEP 5  Lumina self-integrity gate + full regression          [THEME] → THEME FREEZE
STEP 6  Scaffold Lumina Companion plugin (original code)      [PLUGIN]
STEP 7  Plugin features (original implementations)            [PLUGIN]
STEP 8  Plugin + theme integration tests                      [PLUGIN] → PLUGIN FREEZE
STEP 9  Documentation, ADRs, reports, git commit + tag        [BOTH]
```

Each step ends with a quality gate; nothing proceeds on red.

---

## 4. STEP-BY-STEP TASKS

### STEP 0 — Safety net

- [ ] `git tag v0.14.0-rc1-baseline` (already committed as `v0.14.0`)
- [ ] Working-tree backup (zip of `wp-content/themes/lumina/`) so any
      misstep is reversible in seconds
- [ ] Record current state: 14/14 smoke suites green (baseline evidence)

### STEP 1 — Standalone shell (remove the parent dependency)

**Why:** Lumina currently has no `header.php`/`footer.php`/`index.php` and
relies on GeneratePress's shell. Standalone requires our own shell files —
written as **original Lumina markup** (never copied from GP):

- [ ] `header.php` — `<!doctype html>`, `<head>` with `wp_head()`, body
      classes, skip link (Phase 14), `wp_body_open` hook, Lumina header
      region
- [ ] `footer.php` — footer region, `wp_footer()`
- [ ] `index.php` — loop shell delegating to the Composer/`View`
- [ ] `404.php`, `search.php`, `archive.php`, `single.php`, `page.php` —
      thin WP-hierarchy files delegating to `templates/frontend/*` (mirror
      the existing Phase-12 pattern)
- [ ] Remove `Template: generatepress` from `style.css`
- [ ] `TemplateResolver` — remove the parent fallback tier (tier 4) and its
      `$parent_dir` plumbing; update smoke-phase6 assertions
- [ ] Delete now-unused `ThemeTemplatesBridge` parent logic (keep the
      `template_include` wiring)

**Gate:** all smoke suites still green (shell is additive); new smoke asserts
"standalone": no `Template:` header, no parent-dir tier.

### STEP 2 — Complete identifier rename (Lumina → Lumina)

**261 files use `Lumina\Core`; 206 mention "lumina".** Systematic replace:

- [ ] PHP namespace `Lumina\Core\` → `Lumina\Core\` (261 files) + `use`
      statements + FQCNs in config providers, `functions.php`, templates,
      smoke suites
- [ ] Composer PSR-4 `"Lumina\\Core\\": "app/"` → `"Lumina\\Core\\": "app/"` + composer name `lumina/lumina-core` → `lumina/lumina`
- [ ] Composer `keywords` (remove `generatepress`)
- [ ] Functions/handles: `lumina_*` → `lumina_*`; option prefix; hook
      prefix `lumina_core:*` → `lumina_core:*`; asset handles `lumina-*`
      → `lumina-*`
- [ ] CSS classes `lumina-*` → `lumina-*` (templates + `_components.scss` +
      `components.ts` selectors) — keep `data-lumina-*` → `data-lumina-*`
      data attributes consistent
- [ ] CSS variable prefix `--lumina-` → `--lumina-` (TokenFactory + token
      renderer + built tokens) — **this is a contract-level change**; update
      `API_FREEZE.md`/`CONTRACT_FREEZE.md` accordingly (ADR-026)
- [ ] `lumina.env.json(.example)` → `lumina.env.json(.example)`
- [ ] Text domain `lumina` → `lumina` (all `esc_html__`, `__`, `_e`)
- [ ] `bin/` scripts, smoke suites (namespace assertions), `docs/*` links
- [ ] `.github/workflows/ci.yml` references + `.serena/` project refs
- [ ] `package.json` name `lumina-core` → `lumina`

**Gate:** grep-zero proof — `grep -ri 'lumina\|generatepress' ` must return
0 hits in the theme (excluding the migration notes doc); every smoke suite
passes.

### STEP 3 — Brand surface

- [ ] `style.css` header: `Theme Name: Lumina`, `Author:` (user), `Author
URI:`, `Theme URI:`, `Description:` (no GP mention), `Text Domain:`
      lumina, `License: GPL-2.0-or-later`, `License URI:`
- [ ] `README.md` fully rewritten for Lumina
- [ ] All `@package Lumina` / `@package Lumina\Core` docblocks →
      `@package Lumina\Core`
- [ ] `docs/` brand pass (architecture, development, versions, plugins)
- [ ] Update `Report/MASTER_ROADMAP.md` (Phase 16 row + brand header)

**Gate:** brand scan — no "GeneratePress", "GP Premium", or "Lumina"
anywhere in user-visible files.

### STEP 4 — Toolchain & CI rebrand

- [ ] `composer.lock` content-hash resync after composer.json name change
- [ ] `phpcs.xml` excludes/handles (namespace prefix rules: `Lumina`)
- [ ] CI: working-directory unchanged (folder renamed to `lumina/`? see
      decision in §7 Q3), job names, smoke suite names
- [ ] `npm run build` + manifest rebrand (asset names now lumina-*)

### STEP 5 — Lumina self-integrity gate + full regression

- [ ] New `bin/verify-lumina-integrity.sh`: hashes the Lumina theme tree at
      the freeze commit (replaces the GP integrity gate)
- [ ] Replace `.github/workflows/ci.yml` integrity job (no more
      generatepress/GP manifests)
- [ ] Remove `Report/gp_audit_manifest*.txt` references; keep them archived
      in git history only
- [ ] Full gate: 14 smoke suites + PHPCS + PHPStan + Psalm + npm + Vite

**THEME FREEZE** — record `docs/PHASE_16_THEME_VERIFICATION_REPORT.md`.

### STEP 6 — Scaffold Lumina Companion plugin (original code)

- [ ] New dir `wp-content/plugins/lumina-companion/`
- [ ] `lumina-companion.php` header (Name: Lumina Companion, GPL-2.0-or-later)
- [ ] PSR-4 `Lumina\Companion\` → `src/`, Composer + autoload
- [ ] Guarded activation/deactivation; no activation on non-Lumina themes
      (graceful: features register only when `lumina` theme is active)

### STEP 7 — Plugin features (original implementations, no GP code)

Re-implement the _premium feature categories_ as **original Lumina code**
(no GP Premium files copied or referenced):

- [ ] Spacing controls (per-element spacing via Customizer + tokens)
- [ ] Typography controls (font family/size/weight via tokens)
- [ ] Page Header module (via the Phase-11 `page-header` component)
- [ ] Secondary Navigation (via `secondary-nav` component + `lumina_core:*` hooks)
- [ ] Menu Plus (mega menu enhancements via existing `mega-menu` component)
- [ ] Sections / layout builder hooks
- [ ] WooCommerce styling hooks (only via the Phase-9 Woo Bridge contract)
- [ ] All features gated by `function_exists`/theme-presence guards

**Gate:** plugin activates on Lumina, deactivates cleanly, produces zero
warnings; WP-free smoke suite for the plugin (`bin/smoke-phase16-plugin.php`).

### STEP 8 — Plugin + theme integration

- [ ] Theme declares the plugin's feature slots; plugin fills them
- [ ] End-to-end smoke: theme renders with plugin active (header, page
      header, secondary nav, spacing applied)
- [ ] Full regression with plugin active AND inactive (no regressions either way)

**PLUGIN FREEZE** — record `docs/PHASE_16_PLUGIN_VERIFICATION_REPORT.md`.

### STEP 9 — Documentation, ADRs, reports, release

- [ ] ADR-026 (Rebrand to Lumina: namespace/handle/contract renames)
- [ ] ADR-027 (Standalone theme — parent dependency removal)
- [ ] ADR-028 (Lumina Companion plugin — original implementation; GP Premium
      removal)
- [ ] Update `API_FREEZE.md` + `CONTRACT_FREEZE.md` for the Lumina surface
- [ ] `CHANGELOG.md` — `[1.0.0-lumina]` (semver: rebrand + namespace change
      is a major break → **v1.0.0**)
- [ ] `docs/PHASE_16_VERIFICATION_REPORT.md` (full phase report →
      APPROVED FOR PHASE 17)
- [ ] Git: commit + tag `v1.0.0-lumina`; push
- [ ] Update GitHub repo (rename if desired) + remove GP Premium folder from
      public repo (see §2)

---

## 5. RISK REGISTER (rebrand-specific)

| #   | Risk                                                            | Severity | Mitigation                                                                                          |
| --- | --------------------------------------------------------------- | -------- | --------------------------------------------------------------------------------------------------- |
| R1  | Namespace rename breaks autoload/container                      | High     | Mechanical replace + `composer dump-autoload` + full regression before proceeding; grep-zero gate   |
| R2  | CSS var `--lumina-*` → `--lumina-*` breaks built CSS            | High     | TokenFactory change + rebuild + grep `--lumina-` = 0 in dist                                        |
| R3  | Standalone shell loses GP features (e.g., GP layout/Customizer) | Medium   | Lumina ships its own shell + Companion re-implements needed features as original code; parity smoke |
| R4  | Docs/CI still reference GP → detection                          | Medium   | grep-zero scan across repo; CI job renamed                                                          |
| R5  | GP Premium removed → user perceives "lost features"             | Medium   | Companion re-implements the categories; messaging explains legal reason                             |
| R6  | WP template hierarchy regression without parent                 | Medium   | Thin WP-hierarchy files added in Step 1; smoke-phase6/12 cover resolution                           |
| R7  | GitHub history still contains GP Premium                        | Medium   | Remove from current tree + (option) make repo private / rewrite history — user decision (§7 Q4)     |

---

## 6. ACCEPTANCE CRITERIA (Phase 16 = 100% only when all pass)

1. **Zero detection:** `grep -riE 'generatepress|gp.premium|gp_premium|lumina'`
   returns 0 hits in the Lumina theme + Companion plugin (excluding this
   plan + historical reports).
2. **Standalone:** no `Template:` header; theme activates without any parent;
   every WP hierarchy file ships in Lumina.
3. **Functional parity:** all smoke suites (updated for Lumina) pass 100% —
   same assertion count, same coverage as v0.14.0.
4. **Toolchain green:** PHPCS 0 · PHPStan 0 · Psalm 0 · ESLint/Prettier/tsc ·
   Vite build · composer validate.
5. **Plugin works with theme:** Companion activates, renders features, and
   degrades gracefully when the theme is switched.
6. **License:** GPL-2.0-or-later with Lumina headers on all original files;
   no GP/GP Premium files in the product.
7. **Version:** `1.0.0` (major — namespace/contract renames) consistent
   across style.css/composer/package/CHANGELOG.
8. **Docs:** ADRs 026–028, updated freezes, phase reports, roadmap.

---

## 7. OPEN QUESTIONS (user decisions needed before STEP 1)

| #   | Question                                                                                             | Options                                                              |
| --- | ---------------------------------------------------------------------------------------------------- | -------------------------------------------------------------------- |
| Q1  | Folder slug: rename `wp-content/themes/lumina/` → `lumina/`?                                         | Rename (recommended — cleanest detection removal) / Keep folder name |
| Q2  | PHP version floor stays `^8.2`?                                                                      | Yes (recommended) / Raise to 8.3                                     |
| Q3  | Companion plugin scope: all 7 categories in Step 7, or a starter subset first?                       | All (recommended) / Subset                                           |
| Q4  | GitHub: keep `wordpress` repo public after removing GP Premium, or make private / new repo `lumina`? | Public (GP removed) / Private / New repo                             |
| Q5  | Author name + URI to put in the theme header?                                                        | Type your brand author line                                          |
| Q6  | Version: confirm `v1.0.0` major bump (rebrand = breaking)?                                           | Yes v1.0.0 (recommended) / Keep 0.15.0                               |

---

**STATUS: ⏳ PLAN APPROVED — awaiting answers to §7 before implementation.**
