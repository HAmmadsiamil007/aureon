# 10 — Architecture Problem Report (Phases 24–25)

Each problem: severity · root cause · files · impacted features · recommended layer · safe fix · test · rollback. **Nothing has been changed.**

## P0 (must resolve before production)

### A-01 — Untracked file required by a shipped feature
- **Severity:** CRITICAL · **Layer:** REPO HYGIENE
- **Files:** `AUREON-WORDPRESS-DEPLOY/frontend/designs/vineta/components/shell/header.php` (untracked), commit 39e8215 (auth pages use frozen header/footer)
- **Impact:** fresh clone / redeploy loses the auth-page header (silent fallback to AETHER shell — visually broken auth pages).
- **Root cause:** deploy-tree commits made without staging the pack components dir.
- **Safe fix:** `git add` the file (or delete if superseded). · **Test:** fresh clone → /my-account/?auth=login shows Vineta header. · **Rollback:** trivial revert.

### A-02 — Config-mutating scripts in workspace root
- **Severity:** CRITICAL · **Layer:** PROCESS/SECURITY
- **Files:** `enable_cod.php`, `update-contact.php` (both hardcode `/var/www/html/wp-load.php`)
- **Impact:** accidental execution reconfigures payment gateway / overwrites client contact data; if web-served, any visitor could trigger them.
- **Root cause:** ad-hoc live-server fixes committed to workspace root.
- **Safe fix:** move to `scripts/one-off/` with README warning, or delete. · **Test:** grep deploy package for these files. · **Rollback:** n/a (no code depends).

### A-03 — Golden Core contains client slug; 'luxury' engine unreachable
- **Severity:** HIGH · **Layer:** GOLDEN CORE
- **Files:** `frontend/views/design.php` (`$design = $design ? $design : 'vineta';`)
- **Impact:** Core cannot boot without a client pack; upgrade path for future clients broken; 'luxury' asset stack dead; docblock lies.
- **Root cause:** hotfix to force vineta active leaked into Core.
- **Safe fix:** restore default `'luxury'`, set `AETHER_DESIGN`/option `aether_active_design='vineta'` in DB/wp-config (runtime change, needs live access). · **Test:** option absent → design falls back correctly per contract. · **Rollback:** revert one line.
- **BLOCKED:** requires DB/runtime decision → QUESTIONS.md.

### A-04 — Production state invisible to repo (DB, plugins, cache, server)
- **Severity:** HIGH · **Layer:** RELEASE ENGINEERING
- **Files:** n/a (absence)
- **Impact:** catalog, menus, gateways, page-cache, active plugin list, PHP version — all unknown here; every "works" claim about them is UNPROVEN.
- **Root cause:** no deploy/IaC manifests; runtime configured by hand (see A-02).
- **Safe fix:** document runtime topology + export production config (plugin list, options snapshot) into repo under `docs/forensics/runtime/`. · **Test:** n/a. · **Rollback:** n/a.

## P1 (important)

### B-01 — Six parallel trees, no declared canonical
- **Severity:** HIGH · **Files:** `aureon/`, `AUREON-GOLDEN-COPY/`, `theme/`, `frontend/`, nested `theme/` copies · **Impact:** edits land in wrong tree (already happened: aureon/ is 2 days stale); test suites test dead trees.
- **Fix:** archive + README "canonical tree" declaration; prune after approval. **Test:** `diff -rq` drift report. **Rollback:** keep archived zips.

### B-02 — Client composer mixes layers (2,885 lines)
- **Severity:** HIGH · **Files:** `designs/vineta/composer.php` · **Impact:** client redesigns require touching bridge+core logic; the exact coupling the architecture docs forbid.
- **Fix (approved plan only):** split into pack `bridge/` (data), `customizer/`, `menus/` with Core providing stable filters. Rollback: single-file restore.

### B-03 — Fragile regex/DOM contracts on frozen HTML
- **Severity:** HIGH · **Files:** `ferm-page.php` rewrite rules, composer `vineta_html_splice_list`, pack JS selector hooks · **Impact:** any client HTML redesign silently breaks menus/login/logo/cart.
- **Fix:** contract tests asserting selectors exist per template + snapshot tests on rendered pages (test plan §3).

### B-04 — Demo-mode `auto` stub
- **Severity:** MEDIUM · **Files:** composer `vineta_show_demo_content()` · **Impact:** demo content can surface on live store if catalog empty and mode left `auto`. **Fix:** implement auto = real-if-exists-else-demo; **Test:** empty catalog → demo; non-empty → real only.

### B-05 — 404 fallback references non-existent pack path
- **Severity:** MEDIUM · **Files:** `ferm-page.php` `aureon_ferm_resolve_page()` · **Impact:** broken/ugly 404 when manifest map misses. **Fix:** point to `404.html`; **Test:** request /nonexistent.

### B-06 — Triple cart surfaces + double VinetaPageData injection + triple rewrite systems
- **Severity:** MEDIUM · **Files:** composer, shims, ferm-page, aether-cart.php · **Impact:** consistency bugs (badge mismatch), double-defined globals. **Fix:** consolidate in approved plan (P1). **Test:** cart E2E matrix.

### B-07 — Account dashboard bypasses WC endpoint hooks
- **Severity:** MEDIUM · **Files:** `myaccount/my-account.php` · **Impact:** WC account plugins won't inject content; future plugin conflicts. **Fix:** add `woocommerce_account_content`-compatible zones or document limitation (decision → QUESTIONS.md).

### B-08 — Newsletter export/REST permission verification
- **Severity:** MEDIUM (security) · **Files:** `inc/aether-newsletter.php` · **Impact:** potential data exposure if capability checks missing. **Fix:** audit + add `current_user_can`/`permission_callback` (P0 before prod). **Test:** unauthenticated export attempt must 403.

## P2 (hardening)
- C-01 Suppression list is manual (new handles leak theme CSS). → handle-registry pattern. Files: `inc/frontend.php`.
- C-02 CSP vs inline pack scripts unverified. Files: `aether-security.php` + composer echoes.
- C-03 jQuery duplication (pack vs WP). Files: pack manifest js.
- C-04 bfcache cart-badge staleness. Files: shims.
- C-05 Legacy ferm-era code paths in Core (`collections/furniture.html`, struct.com rewrites, FermPageData). Files: `ferm-page.php`, composer.
- C-06 Dead pack templates/assets kept in tree (checkout.html, view-cart.html, cart-empty.html, shop variants, product-3d, model-viewer). Files: pack dir.
- C-07 Two icon systems + luxury-era CSS kept. Files: assets.
- C-08 Hardcoded favicons bypass site icon. Files: `inc/frontend.php`.

## Phase 25 — Future design editability (what a future redesign may touch)

| Area | Classification | Notes |
|---|---|---|
| Header markup/classes | FRONTEND_ONLY (pack HTML) **but** header/menu/footer splicing + logo bridge + auth header depend on classes → effectively BRIDGE_REVIEW |
| Hero | FRONTEND_ONLY via Customizer repeater (pack) | safe if selectors kept |
| Product cards | FRONTEND_ONLY if VinetaPageData schema (id/name/price/image/url) preserved; JS render code is pack | FRONTEND_ONLY |
| Category cards | same | FRONTEND_ONLY |
| Shop page | FRONTEND_ONLY (grid JS) + keep `woocommerce_product_query` demo filter semantics | FRONTEND_ONLY |
| Product page | BRIDGE_REVIEW — hydration selectors + VinetaPageData.product schema are the contract |
| Cart drawer | BRIDGE_REVIEW — VinetaCart response schema |
| Cart page | BRIDGE_REVIEW — section + inline CSS coupling |
| Checkout | CORE_REVIEW — WC-native; only presentation shell editable |
| Footer | FRONTEND_ONLY + menu splice classes → BRIDGE_REVIEW |
| Colors | FRONTEND_ONLY — `--vineta-*` vars via Customizer emitter |
| Typography | BRIDGE_REVIEW — token bridge from platform fonts |
| Animations | FRONTEND_ONLY (pack animate/wow) |
| Spacing/layout | FRONTEND_ONLY (pack CSS) |
| Auth pages | CORE_REVIEW — standalone theme templates own them |
| Menus | BRIDGE_REVIEW — splicer + WP locations |
| Search | BRIDGE_REVIEW — bridge scripts + data builder |
| 404/static pages | FRONTEND_ONLY (pack HTML swap) |

**Bottom line:** pure-visual edits (colors, hero copy, section order, card styling) are safe. Anything touching header/footer/product/cart DOM classes silently breaks the bridge — every redesign must run the selector contract tests from the test plan first.
