# AETHER Dynamic Architecture — Current State

> Evidence snapshot: 2026-08-29 (post Ferm Living integration). Re-run `git status` / docker checks before trusting any section after a change.

## 1. Repository state

- Repo root: `C:\Users\hamma\Downloads\phantom\wordpress`, git branch `main`, HEAD `5b62b88`
  `feat: complete Ferm Living integration + distribution packages`
- Working tree: **modified** — see `git status` for uncommitted changes (npm updates, new docs, design pack archives).
- Tags: `v1.3.0-m6-m10` (frontend platform milestone) + `v1.2.0-g6` + all prior tags on origin.
- Remote: `https://github.com/HAmmadsiamil007/aureon.git`

## 2. Live stack (Docker)

| Container | Image | Role | Port |
|---|---|---|---|
| `aureon_wp` | php (8.3.x) | WordPress + WooCommerce + Aureon theme + plugin + AETHER engine | http://localhost:8080 |
| `aureon_db` | mysql:8.0 | Database | internal |

## 3. Mount topology (what is live vs what must be synced)

| Path on host | Path in container | Live bind-mount? |
|---|---|---|
| `aureon/theme` | `/var/www/html/wp-content/themes/aureon` | **YES** — edits apply instantly |
| `aureon/plugin` | `/var/www/html/wp-content/plugins/aureon` | **YES** |
| `frontend/` | `/var/www/html/wp-content/frontend/` | **NO** — copy required |

### Sync procedure (every engine change)

```powershell
docker cp frontend/assets/js/main.js aureon_wp:/var/www/html/wp-content/frontend/assets/js/main.js
# full tree: tar.exe -czf - --exclude "frontend/source" frontend | ... | in-container tar -xzf
```

- Assets are `filemtime`-versioned (`?ver=<ts>`); a fresh `docker cp` is served immediately.
- main.js MD5 (frozen, M6–M10): `6d8f3b671333571508efcb53b1e39e60`.

## 4. Architecture (current, post Ferm Living integration)

```
WordPress → WC → Aureon theme (inc/frontend.php bridge)
                 ├─ views/loader.php (engine kernel: tokens, registry, renderer,
                 │    viewmodel, composer, design resolution)
                 ├─ adapters/ (23 — only WP/WC-touching layer)
                 ├─ components/ (53, 14 families — pure markup, zero WP calls)
                 ├─ sections/ (26 self-registering)
                 ├─ designs/
                 │   ├─ fermliving/ (active client pack — frozen Ferm DOM)
                 │   ├─ fermliving-legacy/ (archive)
                 │   ├─ fermliving-legacy-integration/ (archive)
                 │   └─ fermliving-broken-v4.0.0-archive/ (archive)
                 ├─ tokens/tokens.php + manifest/components.php
                 └─ assets/ (6 css, 5 js, 7 woff2)
```

- **Design resolution:** `aether_active_design()` — option `''`/`'luxury'` → luxury engine; `'fermliving'` → Ferm Living pack; body class `design-<slug>`; **isolation by construction (M7)** — luxury assets never coexist with a pack.
- **Cache bug fixed (2026-08-15):** fallback was applied *after* static caching, so first call returned `'luxury'` but every later call returned `''`. Now: resolve branch → apply fallback → cache → return. Verified via wp-load probe: `string(6) "luxury"`.
- **Theme bridge (`inc/frontend.php`, 235 ln):** boots kernel (`after_setup_theme` 30), registers nav menus (20), suppresses 10 theme styles + 5 scripts + 4 output hookups (wp_enqueue_scripts 1000, `aureon-google-fonts` deliberately kept for font-token bridging), enqueues CDN + local assets + `aetherAjax` context (20, luxury-guarded), routes WC pages (template_include 99), prints favicons/theme-color #09090B.
- **Feature bridges (`inc/aether-*.php`, 8):** tokens (`:root` vars), security (CSP report-only + nonce, headers, HSTS), SEO (OG/Twitter/JSON-LD/canonical at wp_head 1–5), newsletter (DB table, AJAX + REST `/aether/v1/newsletter/subscribe`, rate limit 1/IP/min), AJAX (wishlist/quick-view/contact, shared `aether_nonce`), cart (add-to-cart fragments + Shopify API shims: `/cart.js`, `/cart/add.js`, `/cart/change.js`), analytics (view_item/add_to_cart/purchase events), performance (hints, preloads, CDN ver-strip, WC off-page script disable, HTML compression).
- **WC routing:** cart → `cart.php`, order-received → `woocommerce/checkout/thankyou.php`, checkout (non order-pay) → `checkout/form-checkout.php`, account → `myaccount/my-account.php`.
- **CDN contract:** Bootstrap 5.3.3, FA 6.5.1, Swiper 11, GSAP 3.12.5 + ScrollTrigger, Lenis 1.1.19; fonts self-hosted (Cabinet Grotesk + Satoshi, 7 woff2).
- **Ferm Living integration:** Complete-page host via `ferm-page.php` (frozen HTML files), thin adapter bridge, 4 cart endpoint shims, bridge.js ≤150 lines for cart-count sync + wishlist. 10 page families cover 980 crawled pages. Pack shadows engine components via filesystem.

## 5. Feature status

| Milestone | Status |
|---|---|
| M6 design isolation (luxury vs packs) | ✅ verified live |
| M7 asset engine + isolation by construction | ✅ verified live |
| M8 design manifest contract + active-design cache fix | ✅ verified |
| M9 per-design visual baselines | ✅ in `frontend/tests/` |
| M10 lumen pack (first shipped design pack) | ✅ verified live |
| M11 Ferm Living client frontend replacement | ✅ complete — frozen DOM, thin adapter bridge, cart shims |
| G4 newsletter rate-limit flake | ✅ fixed (1/IP/min) |
| G6 aether_pack_url() helper | ✅ added to design.php |
| Verification: design-isolation 6/6, routes 32/32, verify.sh PASSED, main.js MD5 frozen | ✅ |

**Current:** Ferm Living integration complete. Design pack at `frontend/designs/fermliving/`. Complete-page host via `ferm-page.php`. Cart API shims bridge Shopify→WooCommerce. Architecture documented in `docs/FRONTEND_REPLACEMENT_AND_EDITING_GUIDE.md`.

## 6. Toolchain (host)

- Node v24.x, PowerShell-only shell.
- Playwright: `@playwright/test 1.48.x`, `@axe-core/playwright 4.10.x` (project: Google Chrome channel).
- CI: `.github/workflows/ci.yml` — static gate (php lint + JS check + `verify.sh`) on push/PR; Playwright E2E via `workflow_dispatch`.
- npm packages (updated 2026-08-29): opencode-mem 2.25.0, @mimo-ai/cli 0.1.13, @shopify/dev-mcp 1.14.7, @wordpress/env 11.14.0, @wordpress/scripts 34.2.0, sass 1.103.1, terser 5.51.2, vite 8.2.2.

## 7. Verification repeat commands

```powershell
# design isolation (6 tests)
npx playwright test specs/design-isolation.spec.js --project=desktop --reporter=list

# route suite (32 tests)
npx playwright test specs/routes.spec.js --project=desktop --reporter=line

# engine gates
bash frontend/tests/verify.sh

# live active-design probe through wp-load (must print luxury)
php -r "require 'wp-load.php'; var_dump(aether_active_design());"   # in-container
```