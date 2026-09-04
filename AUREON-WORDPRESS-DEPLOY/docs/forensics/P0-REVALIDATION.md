# AUREON / VINETA — P0 REVALIDATION REPORT (Round 2)
## Revalidation Date: 2026-09-04 (11:45–12:05 PST)
## Status: REVALIDATION_COMPLETE — 1 LIVE P0-CLASS ISSUE REMAINS
## Supersedes: Runtime-evidence claims in COMPLETE-PROJECT-FORENSIC-REPORT.md (2026-09-04 11:26) that were based on `console-errors.txt` (stale snapshot, mtime 2026-09-02 10:25).

---

## 1. PURPOSE

Phase A25 of the master execution prompt. The previous forensic round identified five
alleged P0 issues. **No finding was assumed to still be present.** Each allegation was
re-proven against the CURRENT source tree AND the CURRENT live runtime before any
implementation decision. No source file was modified during this pass.

## 2. METHOD / EVIDENCE GATHERED

| # | Check | Method | Result |
|---|-------|--------|--------|
| E1 | Runtime up? | `docker ps`, HTTP GET localhost:8080 | WordPress + MySQL healthy; homepage HTTP 200 |
| E2 | Source vs mirrors vs container | md5 across `aureon/`, `AUREON-WORDPRESS-DEPLOY/`, `AUREON-GOLDEN-COPY/`, and mounted files inside `wordpress-wordpress-1` | Byte-identical for every shared runtime path tested (see §4) |
| E3 | Asset URLs previously 404 | HTTP GET of 5 representative previously-broken image URLs | **All return HTTP 200** (except `cursor-close.svg` = genuine missing file) |
| E4 | Console errors on 7 routes | Playwright headless Chromium sweep of `/`, `/shop/`, `/cart/`, `/checkout/`, `/my-account/`, `/blog/`, `/product/*` — pageerror + console + requestfailed + 404 + broken-image count | Only error on every route: `SyntaxError: Unexpected token 'export'`. Zero broken images (0 of 162 on `/`; 0 of 346 on `/shop/`) |
| E5 | Identify export-file | vm.Script (script-mode) parse of all 21 served pack JS files | **Only `js/model-viewer.min.js` fails**: `Unexpected token 'export'` |
| E6 | jQuery/Bootstrap state | In-page evaluation on `/` and `/shop/`: jQuery version, `typeof $.fn.modal`, script dup counts | jQuery 3.7.1 (pack) + WP jQuery 3.7.1; `$.fn.modal` IS a function (Bootstrap 5.3.2 `defineJQueryPlugin`); modal `show()` executes without error |
| E7 | Checkout/Account template identity | Body-marker scan of served `/checkout/`, `/my-account/`, `/cart/` | Frozen Vineta templates served (tf-cart / VinetaPageData / `user_login` present). WC login form renders inside frozen account template |
| E8 | DB state | Read-only MySQL: products, pages, menus, options, plugins | Theme `aureon`; design `vineta`; `demo_mode=auto`; real Sole Origine products published; Primary/Footer menus assigned to `primary`/`footer`; plugins = `aureon-studio`, `woocommerce`; Customizer color/font/heading/search options stored |
| E9 | Route statuses | HTTP GET (following redirects) | `/` 200, `/shop/` 200, `/cart/` 200, `/checkout/` 200, `/my-account/` 200, `/blog/` 200, `/?s=loafer` 200, `/product/classic-buckle-loafer/` 301→200. `/product-category/bestsellers/` 404 (slug does not exist — not a template bug) |

## 3. EXECUTIVE VERDICT TABLE

| # | Alleged P0 (from previous report) | Prior IDs | Revalidation verdict | Layer (correct fix location) |
|---|----------------------------------|-----------|----------------------|------------------------------|
| P0-1 | Core file version drift | CANONICAL-TREE-AUDIT §Discrepancies; BF: ferm-page duplication (34987 vs 25062) | **NOT PRESENT.** All shared runtime files byte-identical across canonical/mirror/container. ferm-page.php duplication gone. One legacy copy (`index.html`) lags in mirrors | Deployment hygiene (mirror re-sync), not a code fix |
| P0-2 | Vineta asset path mismatch / 37 image 404s | BF-001, FP-005, AP-010, "Asset Deploy Configuration" | **RESOLVED.** All previously-404 URLs now return HTTP 200; volume mount `aureon/frontend/ → wp-content/frontend/` works. 1 cosmetic file still missing (`images/cursor-close.svg`, not referenced on `/` today) | None required (was infra); optional P2: add cursor-close.svg or remove reference |
| P0-3 | shop.js null-dataset crash on every page | BF-002, FP-001 | **FIXED.** `filterProducts()` now null-guards `#price-value-range`; module-level dedupe flag added. No shop.js error on any of 7 routes | Client pack (already applied) |
| P0-4 | ES-module `export` in non-module script | AP-003, BF-004, FP-003 | **STILL PRESENT on every route — but ROOT CAUSE WAS MISATTRIBUTED.** `vineta-data-shims.js` parses cleanly (no export/import anywhere). The actual offender is **`js/model-viewer.min.js`** (Google model-viewer ES-module build) enqueued as a classic script on EVERY page by the manifest | Client pack: `manifest.json` assets (page-gate or drop/`type=module`). Core already supports per-page gating (`page` key in `aether_enqueue_pack_asset`), so **Core does not need to change** |
| P0-5 | Bootstrap 4 API `$().modal()` vs Bootstrap 5 | BF-003, FP-002, JP-006 | **NOT CURRENTLY BROKEN.** Bootstrap 5.3.2 ships `defineJQueryPlugin` (jQuery-compat), so `$.fn.modal` exists and `show()` executes cleanly at runtime. The Sep-2 failure was load-order/version-state specific and is not reproducible on the 7 tested routes. Architecture remains fragile (two jQuery instances) | Hardening only (P1/P2): reduce duplicate library loading; keep load-order guarantees documented |

## 4. P0-1 — CORE FILE VERSION DRIFT: NOT PRESENT

Previous claim: duplicate `ferm-page.php` (34987 vs 25062 bytes) and general Core drift across the
Golden Copy / deploy mirror.

Current evidence (md5, first 8 hex):

| File (canonical rel. path) | `aureon/` | `AUREON-WORDPRESS-DEPLOY/` | `AUREON-GOLDEN-COPY/` | Container `/var/www/html/wp-content/` |
|---|---|---|---|---|
| frontend/views/design.php | 3ddab303 | 3ddab303 | 3ddab303 | n/a (mounted) |
| frontend/views/assets.php | 289dd801 | 289dd801 | 289dd801 | n/a (mounted) |
| frontend/designs/vineta/manifest.json | 05c9d0e9 | 05c9d0e9 | 05c9d0e9 | n/a (mounted) |
| frontend/designs/vineta/composer.php | 539b4b9c | 539b4b9c | 539b4b9c | n/a (mounted) |
| frontend/designs/vineta/js/vineta-data-shims.js | e5a3273a | e5a3273a | e5a3273a | n/a (mounted) |
| frontend/designs/vineta/js/shop.js | 2b222bc5 | 2b222bc5 | 2b222bc5 | n/a (mounted) |
| frontend/designs/vineta/js/main.js | 4a48429d | 4a48429d | 4a48429d | n/a (mounted) |
| frontend/designs/vineta/index.html | 00cd165a | b73e16de | b73e16de | n/a (mounted) |

Notes:
- Docker mounts `./aureon/theme`, `./aureon/frontend`, `./aureon/plugin` directly; the container is
  therefore definitionally in sync with `aureon/` (the canonical tree).
- Whole-tree `diff -rq` between `aureon/` and both mirrors found **zero differing code files**
  (only extra legacy files present in the mirrors — old theme-layout copies, Ferm-era docs, and
  `AUREON-WORDPRESS-DEPLOY`-specific deploy extras).
- **One drift item remains:** `vineta/index.html` in both mirrors is one revision behind canonical
  (00cd165a vs b73e16de). P2 — re-sync mirrors after Stage B.
- Mirrors carry duplicated legacy directory layouts (top-level theme files AND `theme/aureon/`
  AND `aureon/` AND `frontend/`) that are confusing but not executed by the Docker runtime.
  Risk is organizational, not functional.
- Working tree of both mirrors contains uncommitted Ferm-removal/Vineta changes relative to
  git HEAD `a79ce16` — needs a deliberate commit decision in Stage B (documentation of intent).

**Classification: PASS (no code fix). Residual: P2 sync hygiene.**

## 5. P0-2 — VINETA ASSET PATH MISMATCH / 37 IMAGE 404s: RESOLVED

Previous claim (console-errors.txt, 2026-09-02): 37 image 404s at
`/wp-content/frontend/designs/vineta/images/[slider|cls-categories|gallery|testimonial]/fashion/*`.

Current runtime probes (2026-09-04):

```
200  images/slider/fashion/slider-fashion-1.png
200  images/cls-categories/fashion/men.jpg
200  images/gallery/fashion/gallery-1.jpg
200  images/testimonial/author/author-fs1.jpg
404  images/cursor-close.svg          <- genuinely missing from disk (minor)
```

- All files exist on disk at `aureon/frontend/designs/vineta/images/...` and are served correctly.
- Browser sweep: 0 broken images on `/` (162 imgs) and `/shop/` (346 imgs).
- The docker-compose mount (`./aureon/frontend:/var/www/html/wp-content/frontend`) is correct.
- `console-errors.txt` (the "37 errors" source) is a **stale Sep-2 artifact** and must not be used
  as runtime evidence again. Consider deleting or renaming it `-STALE` in Stage B.

**Classification: RESOLVED. Residual P2: `images/cursor-close.svg` missing; add asset or drop the reference.**

## 6. P0-3 — shop.js NULL-DATASET CRASH: FIXED

Previous claim: `TypeError: Cannot read properties of null (reading 'dataset') at filterProducts
(shop.js:46:43)` on every page load.

Current source (`aureon/frontend/designs/vineta/js/shop.js`):
- Module-level dedupe: `if (window.__vinetaShopJsActive) return; window.__vinetaShopJsActive = true;`
  (prevents double-initialization from frozen-template + WP-enqueue double load on shop routes).
- `filterProducts()` null-guards before `.dataset`:
  `if (!priceSlider) return;` — added explicitly because shop.js is enqueued on all complete-page
  routes while the price filter only exists on shop pages.
- vm.Script parse: OK. Runtime sweep on 7 routes: no shop.js error anywhere.

**Classification: FIXED. Residual P3: shop.js still runs on every route (non-shop pages emit one
benign `console.warn` — "Page does not contain a valid layout"). Best handled together with P0-4
via manifest page-gating so shop.js + model-viewer only load where needed.**

## 7. P0-4 — ES-MODULE 'export' IN NON-MODULE SCRIPT: STILL PRESENT (ROOT CAUSE CORRECTED)

Previous claim: `vineta-data-shims.js` (86,966 B) uses ES-module syntax, fails to parse, and
therefore ALL dynamic data injection is broken.

Current evidence:
- `vineta-data-shims.js` (source AND served, 86,966 B): **zero** `export`/`import` statements;
  parses cleanly as a classic script (vm.Script). The old `.bak-phase3` backup may have been the
  module variant — irrelevant now.
- Every route still logs `SyntaxError: Unexpected token 'export'` (E4).
- Script-mode parse of all 21 served pack JS files (E5): **only `js/model-viewer.min.js`
  (936,287 B, Google model-viewer, ES-module output) fails to parse.**
- The Vineta manifest enqueues `model-viewer.min.js` unconditionally (no `page` key), so it loads
  and throws on every route — homepage included (confirmed in DOM script list).
- WordPress's single `type="module"` script on the page is its own core output — unrelated.

Impact today:
1. One parse error logged on every page view (console noise + 936 KB wasted download everywhere).
2. Any 3D-viewer feature actually relying on model-viewer is broken.
3. **False root cause in the previous report attributed this to shims.js; that must be corrected
   in the corpus or every subsequent fix will chase the wrong file.**

Correct fix layer — CLIENT PACK ONLY:
- `manifest.json`: gate `model-viewer.min.js` (+ `shop.js`, `main.js` pack assets) with the
  existing, supported `"page"` key (`aether_enqueue_pack_asset()` already implements
  `aether_is_pack_asset_page_match()`), or drop model-viewer and enqueue it only on product/3D
  routes via the bridge; OR load with `type="module"` if a page truly needs it.
- No Golden Core change required. Other client packs are unaffected by a Vineta-manifest change.

**Classification: LIVE P0/P1 (single real P0-class item). Fix = client-pack manifest gating.**

## 8. P0-5 — BOOTSTRAP/jQuery MISMATCH ($().modal): NOT CURRENTLY BROKEN (FRAGILE)

Previous claim: `$(...).modal is not a function at main.js:700` — Bootstrap 5 removed the jQuery
plugin API, so every modal in Vineta is broken.

Current evidence:
- Pack ships Bootstrap **v5.3.2** and jQuery **v3.7.1**.
- Bootstrap 5.3.x still ships `defineJQueryPlugin()` (jQuery-compat layer) — at runtime
  `typeof $.fn.modal === 'function'`, and `jQuery.fn.modal.toString()` is Bootstrap's
  `jQueryInterface` (`getOrCreateInstance` wrapper).
- Direct call `$('#quickView').modal('show')` (and a `.auto-popup` modal present on `/`) executes
  **without error**.
- main.js lines 541–550, 700 still use `$().modal('show')` — works only because the calling
  jQuery instance is the pack jQuery that Bootstrap attached its plugin to. Fragile coupling:
  two jQuery 3.7.1 instances coexist on the page (WP core + pack). Ordering matters; any AJAX
  fragment injected by WC (which binds WP's jQuery) that needs Bootstrap plugins would fail.

**Classification: WORKING but architecturally fragile — P1/P2 hardening, not a P0 blocker.**
Suggested hardening (bridge/pack layer): document the save/restore jQuery bridge; consider
dropping the pack's duplicate `jquery.min.js` load and reusing WP's jQuery (single instance)
once plugin-copy logic is verified; or keep as-is and lock load order with a regression test.

## 9. ADDITIONAL CURRENT-RUNTIME FINDINGS (not in the five allegations)

| ID | Finding | Severity | Evidence |
|----|---------|----------|----------|
| R1 | **Heavy duplicate library loading**: swiper, lazysize, wow, carousel, count-down, infinityslide, multiple-modal load twice on `/` (frozen HTML scripts + WP manifest enqueues); on `/shop/` additionally nouislider and shop.js twice. jQuery loads twice (WP core + pack) plus WP jquery-migrate. Only shop.js has a dedupe guard | P2 | Script-tag census on `/` and `/shop/` (53 script tags each) |
| R2 | `model-viewer.min.js` 936 KB loaded on every route (part of P0-4) | P2 (perf) | DOM script list |
| R3 | `vineta-data-shims.js.bak-phase3`, `composer.php.bak-phase3`, `manifest.json.bak-phase3` shipped in the pack | P3 | `ls` of pack dirs |
| R4 | Canonical vs mirrors `index.html` drift (00cd165a vs b73e16de) | P3 | md5 (E2) |
| R5 | Non-shop pages log one benign layout `console.warn` per load | P3 | E4 |
| R6 | `/checkout/`, `/my-account/`, `/cart/` 301 `/checkout/` → `/checkout` (canonical redirect before serving) | P3 | E9 |
| R7 | `manifest.json` `main.js` declares deps `["jquery","aether-bootstrap-js"]` — `aether-bootstrap-js` is never registered in complete-page mode; WordPress therefore drops main.js from its own queue (it still loads from the frozen template, masking the issue) | P2 | manifest.json + assets.php complete-page branch |
| R8 | `.bak-phase3` + `index.html` items above affect both mirrors; golden copy should stay pristine | P3 | diff/md5 |

## 10. WHAT WAS **NOT** RE-PROVEN THIS PASS (deliberately deferred)

Per the two-stage model, no write operations were performed. Therefore these remain OPEN items for
Stage B acceptance (they were not claimed fixed):

- Dynamic data injection correctness end-to-end (PHP adapters → wp_localize → shims → DOM):
  hero/Customizer text, product cards, category tiles, menu items, cart rows, blog, footer columns.
- Cart add/update/remove mutation flows and cart-badge live updates.
- Checkout order placement (COD/test gateway) and order-received/thank-you rendering.
- Auth flows (login with real credentials, registration, lost password, account orders/addresses).
- Search results content (route returns 200; results-content semantics not verified).
- Customizer save → reload → computed-style propagation; reset behavior.
- Responsive (1440/1024/768/390) and accessibility passes.
- Live/deployed site (InfinityFree mirror referenced in earlier reports) — not reachable/verified
  in this pass; credentials/URL needed if Stage B must include it.

## 11. IMPLICATIONS FOR THE IMPLEMENTATION ROADMAP (STAGE B ORDER)

The five "P0" items collapse to **one live P0-class issue**:

1. **P0-A (real): model-viewer ES-module parse failure on every route + 936 KB waste**
   → Client-pack `manifest.json` gating (Core already supports `page` gating). Also gate `shop.js`
   to shop/collection routes to remove per-page console noise, and resolve the `main.js`
   `aether-bootstrap-js` dep (drop the dep or register a no-op handle in complete-page mode —
   pack/bridge concern).
2. **P0-B (hygiene): mirror sync** — re-sync `index.html` (and any Stage B changes) into
   `AUREON-WORDPRESS-DEPLOY/` and `AUREON-GOLDEN-COPY/`; commit the pending mirror cleanups.
3. Then P1 groups from the master prompt (Customizer mappings, dynamic consumers, search, cart,
   auth/account, menus) — with every change gated, tested, and rollback-ready.

**Bottom line:** `AUDIT_REVALIDATED` — the previous "4 P0 JS crashes + 37 image 404s" narrative is
out of date. The remaining real P0-class defect is confined to the client pack's asset manifest
(model-viewer), which is fixable without touching Golden Core.
