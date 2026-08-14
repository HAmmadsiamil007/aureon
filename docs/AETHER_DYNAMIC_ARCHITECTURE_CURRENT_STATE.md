# AETHER Dynamic Architecture — Current State (Phase 0)

> Evidence snapshot: 2026-08-14. Re-run `git status` / docker checks before trusting any section after a change.

## 1. Repository state

- Repo root: `C:\Users\hamma\Downloads\wordpress`, git branch `main`, HEAD `88ab98a`
  `feat(frontend): G6 hero slides repeater - schema-driven Customizer control…`
- Working tree: **21 changed files** (modified theme/plugin/frontend sources + new files):
  - New: `aureon/theme/inc/aether-cart.php`, `aureon/theme/inc/aether-analytics.php`, `docs/plans/…`
  - Modified: `aureon/theme/inc/frontend.php` (wcAjaxUrl), `frontend/assets/js/main.js` (2 live-bug fixes, see §4)
  - Nothing committed in this session; no push performed.

## 2. Live stack (Docker)

| Container | Image | Role | Port |
|---|---|---|---|
| `aureon_wp` | php (8.3.33) | WordPress **7.0.2**, WooCommerce **11.0.0**, Aureon theme + plugin | http://localhost:8080 |
| `aureon_db` | mysql:8.0 | Database | internal |

- All containers Up; site loads over HTTP on localhost:8080.

## 3. Mount topology (what is live vs what must be synced)

| Path on host | Path in container | Live bind-mount? |
|---|---|---|
| `aureon/theme` | `/var/www/html/wp-content/themes/aureon` | **YES** — edits apply instantly |
| `aureon/plugin` | `/var/www/html/wp-content/plugins/aureon` | **YES** |
| `frontend/` | `/var/www/html/wp-content/frontend/` | **NO** — copy required |

### Sync procedure (every JS change to the engine)

```powershell
docker cp frontend/assets/js/main.js aureon_wp:/var/www/html/wp-content/frontend/assets/js/main.js
```

- Asset is versioned `…/main.js?ver=<ts>` (wordpress version query, current `…?ver=1786647`); a fresh `docker cp` is served immediately.
- main.js container MD5 (post-fixes): `316793241060172d13932307c22d3417`.

## 4. Live defects found and fixed during verification (evidence kept)

### 4.1 G1 — card add-to-cart caused full-page navigation (FIXED + VERIFIED)

Root cause was **two stacked** defects:

1. `aureon/theme/inc/frontend.php:165` localized `wcAjaxUrl` via
   `add_query_arg('wc-ajax', '', home_url('/'))` → rendered as `…/?wc-ajax` (WordPress strips the trailing `=` for empty values). Old main.js concatenated `wcAjaxUrl + 'add_to_cart'` → malformed `…/?wc-ajaxadd_to_cart`.
2. Even with a correct URL, main.js gated success on `json.success`, but **WooCommerce 11.x no longer emits a `success` key** — the success branch of `WC_AJAX::add_to_cart()` (class-wc-ajax.php, `add_to_cart()`) returns only `fragments` + `cart_hash`; failures return `{error:true, product_url}`. The stale guard fell into the `else` → `window.location.href = btn.href` (main.js:420).

Evidence chain (kept): CDP `Network.requestWillBeSent` initiator stack showed the navigation call at `main.js:419/420, col 37`; forensic console (forensic.cjs) showed `FETCH-RESP status=200 keys=fragments,cart_hash success=undefined` while `defaultPrevented=true` — i.e. the anchor default was suppressed; the "navigation" was the engine's own fallback.

Fixes:
- `frontend.php:165` → `add_query_arg('wc-ajax', 'add_to_cart', home_url('/'))` (verified live: `aetherAjax.wcAjaxUrl = "http://localhost:8080/?wc-ajax=add_to_cart"`).
- `main.js` `aetherAddToCart()`: builds a guarded wc-ajax URL and detects success by `json.fragments`, explicit failure via `json.error`+`json.product_url`; last-resort fallback remains `btn.href`.

Verified (live-gaps.spec.js, then CDP-flagged no nav): fetch 200 with fragments (incl. custom `a.aether-cart-count`), header count +1, `.is-added` state toggles, URL unchanged, zero console errors.
- **Note:** `window.location.assign`/`replace`/`href=` are unforgeable in Chromium (own-property assignment is ignored — verified experimentally), so stack-capture via prototype patching cannot see navigation initiators; use CDP initiator stacks.

### 4.2 G3 — contact form POST went to a poisoned URL (HARDENED + VERIFIED)

Symptom: status showed `Network error — please try again.`; the fetch target was
`http://localhost:8080/contact/[object%20HTMLInputElement]` (404).

Root cause: some client-side script (not present in any repo source file — vendor bundle territory) writes an **own data property `action`** on the contact `<form>` with the value `"[object HTMLInputElement]"`. `form.action` (IDL) then returns the corruption while `getAttribute('action')` and the server HTML remain the clean `…/wp-admin/admin-ajax.php`. Evidence: `Object.getOwnPropertyDescriptor(form,'action') = {value:'[object HTMLInputElement]'}` with no accessors; prototype setter + `Object.defineProperty` tracers recorded zero writes.

Fix: `main.js` contact handler now posts to `form.getAttribute('action') || aetherAjax.ajaxUrl`.

Verified: status renders the server JSON message, URL unchanged, admin-ajax reached.
**Environment limitation (not a frontend bug):** the container has no MTA — `wp_mail()` returns `false`, so even a valid submission yields `The message could not be sent…` (handler `aether-ajax.php:164`). Email delivery is therefore UNVERIFIABLE in this environment; frontend contract (AJAX + status + no navigation) is proven.

### 4.3 Test-side corrections (no app change)

- Product page buy box `.pd-add-to-cart` is reveal-animated → use the house `scrollIntoView → 1500ms → toBeVisible` pattern before interaction.
- G3 assertion must return a boolean (`!!(…)`) — the expression evaluates to the message string otherwise.

## 5. Toolchain (host)

- Node v24.18.0, npm 11.18.0, PowerShell-only shell.
- Playwright: `@playwright/test 1.48.2`, `@axe-core/playwright 4.10.1` (project: Google Chrome channel).
- NOT INSTALLED (no CLI): PHP_CodeSniffer (phpcs), PHPStan, Psalm, ESLint, wp-cli, composer — see Baseline Report.

## 6. Verification repeat commands

```powershell
# live gap regression (6 tests, desktop)
npx playwright test specs/live-gaps.spec.js --project=desktop --reporter=list

# E2E suite, per-spec only (a full single-run invocation can hang >10 min)
npx playwright test specs/routes.spec.js --project=desktop --reporter=line
npx playwright test specs/interactions.spec.js specs/failure.spec.js specs/accessibility.spec.js --project=desktop --reporter=line
npx playwright test specs/visual.spec.js --project=desktop --reporter=line
npx playwright test specs/routes.spec.js specs/interactions.spec.js --project=mobile --reporter=line

# raw POST check for cart add (server-side ground truth)
curl -s -X POST "http://localhost:8080/?wc-ajax=add_to_cart" --data "product_id=435&quantity=1"
# expected success shape in WC 11.x: {"fragments":{...},"cart_hash":"..."} (no "success" key)
```