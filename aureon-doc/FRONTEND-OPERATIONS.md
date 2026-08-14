# FRONTEND-OPERATIONS.md — Edit, Replace, and Create Dynamic Frontends

> **Audience:** developers operating the AETHER frontend engine.
> **Doc version:** 2026-08-15 — M6–M10 state. Companion docs: [`AETHER-BRIDGE.md`](./AETHER-BRIDGE.md) (how the frontend connects to the theme), [`FRONTEND.md`](./FRONTEND.md) (Phase 17 implementation guide — historical), `../docs/frontend-platform/MASTER_FRONTEND_REPLACEMENT_PLAN.md` (M11 master plan).

---

## 1. The one rule that keeps this maintainable

**Components never call WordPress functions.** Adapters fetch data (the only layer touching WP/WC APIs); viewmodels sanitize; renderers merge; components output escaped markup only. Breaking this rule is what produced the Phase 17 integration mess (see `aureon-doc/FRONTEND_FORENSIC_REPORT.md`).

Forbidden in presentation components: `WP_Query`, `get_posts()`, `get_option()`, `get_post_meta()`, `wc_get_product(s)`, direct DB calls. Data must flow through adapters.

---

## 2. Engine layout (`frontend/`)

```
frontend/
├── views/                    ← kernel (the only code the theme requires directly)
│   ├── loader.php            ← bootstrap: tokens + registry + renderer + viewmodel +
│   │                            composer + glob adapters/sections; defines AETHER_FRONTEND_DIR
│   ├── design.php            ← design resolution: aether_active_design() + body class
│   ├── registry.php          ← section registry (global-based)
│   ├── renderer.php          ← aether_render_section(): adapter args merge + component render
│   ├── viewmodel.php         ← sanitize, image resolution, motion toggles
│   ├── assets.php            ← design-pack asset pipeline (M7)
│   └── composer.php          ← aether_compose_header/footer
├── adapters/                 ← 23 files, the ONLY WP/WC-touching layer
├── components/               ← 54 templates in 14 families (pure markup, zero WP calls)
├── sections/                 ← 26 self-registering section templates
├── designs/                  ← design packs; only lumen ships today
├── tokens/tokens.php         ← fallback content + demo data + design tokens
├── manifest/components.php   ← component manifest (every component key listed)
├── assets/
│   ├── css/                  ← style, motion, responsive, a11y, pages, fonts
│   ├── js/                   ← main (884 ln), animations (1045 ln), phantom-bridge,
│   │                            lenis-scroll, countdown
│   ├── fonts/                ← 7 self-hosted woff2 (Cabinet Grotesk, Satoshi)
│   └── images/               ← fog, favicons, demo imagery
├── source/                   ← 22 static HTML pages — the pristine design mirror (reference only)
└── tests/                    ← Playwright specs, verify.sh, validate-manifest.cjs
```

**Deployment topology (critical):**
- `aureon/theme/` + `aureon/plugin/` → **host bind mounts** in Docker (`/var/www/html/wp-content/themes|plugins`) — edits apply instantly, no redeploy.
- `frontend/` → **volume copy** (`/var/www/html/wp-content/frontend/`) — every change must be synced:

```powershell
# any engine change (assets, views, adapters, components, tokens, manifest)
tar.exe -czf - --exclude "frontend/source" frontend | Out-File -Encoding ascii frontend.tar.b64   # or:
docker cp frontend/assets/js/main.js aureon_wp:/var/www/html/wp-content/frontend/assets/js/main.js
```

> Use `tar.exe` (bsdtar, forward slashes), not `Compress-Archive` (literal backslash junk on Linux). Assets are `filemtime`-versioned, so a fresh `docker cp` is served immediately.

---

## 3. How to EDIT the frontend

### 3.1 Change styling
1. **Design tokens / demo content:** `frontend/tokens/tokens.php` — palette, fonts, hero slides, fallback content (`aether_*_items`), section-visibility defaults. Customizer values always win; tokens are fallbacks.
2. **Live `:root` tokens:** theme `inc/aether-tokens.php` outputs them from `aureon_get_option()` (`--void`, `--gold`, `--font-*`, `--container-max`, …).
3. **CSS:** `frontend/assets/css/*.css` (style → motion → responsive → a11y → pages). Enqueue order is the source contract — keep `aether-style` first; fonts via `fonts.css`.

### 3.2 Change what a section shows (data)
- **Adapters** (`frontend/adapters/adapter-*.php`): normalize WP/WC data to plain arrays; demo fallbacks only when `aether_demo_content` is on and real data is empty.
- **Tokens**: add/change fallback arrays in `tokens.php`.

### 3.3 Change markup
- **Components** (`frontend/components/**/*.php`): pure markup + `esc_html()/esc_url()/esc_attr()`. Never add WP calls.
- **Sections** (`frontend/sections/section-*.php`): register via `aether_register_section('slug', [...])` with `adapter_args`; render with `aether_render_section('slug', $data)` where per-call `$data` wins over registered args (renderer merges via `wp_parse_args`).

### 3.4 Change behavior (JS)
- `assets/js/main.js` (interactions, AJAX via `aetherAjax`), `animations.js` (GSAP; guards GSAP presence before adding `html.has-motion`), `phantom-bridge.js`, `lenis-scroll.js`, `countdown.js`.
- AJAX context is localized by the theme bridge (`aetherAjax`): `ajaxUrl`, `nonce`, `restUrl` (`aether/v1/`), `shopUrl`, `searchUrl`, `wcAjaxUrl`. Endpoints live in theme `inc/aether-ajax.php` (wishlist, quick-view, contact), `inc/aether-newsletter.php`, `inc/aether-cart.php` (fragments).

### 3.5 Add a NEW component
1. Create `frontend/components/<family>/<name>.php` (pure markup).
2. Register the key in `frontend/manifest/components.php`.
3. Add an adapter (if it needs data) + token fallbacks (if any).
4. Run `node frontend/tests/validate-manifest.cjs` — every `aether_render_component()` call must match a manifest key (verified by `verify.sh` grep gate).

### 3.6 Edit checklist (run before you claim done)
```powershell
php -l <changed php files>                      # PHP lint
node --check <changed js files>                 # JS syntax
bash frontend/tests/verify.sh                   # engine gates (lint + grep + presence)
npx playwright test specs/design-isolation.spec.js --project=desktop --reporter=list   # isolation 6/6
npx playwright test specs/routes.spec.js --project=desktop --reporter=line             # routes 32/32
docker cp <engine file> aureon_wp:/var/www/html/wp-content/frontend/<...>              # deploy
```

---

## 4. How to REPLACE a frontend (design packs)

**Business model: ONE AUREON CORE + ONE PREMIUM STARTER FRONTEND + CLIENT-SPECIFIC FRONTEND REPLACEMENT.** "Change the frontend completely while keeping the engine underneath."

### 4.1 How designs resolve
- **luxury** (default) = the engine tree itself (`''` or `'luxury'` option value). There is no `designs/luxury/` — luxury **is** the engine.
- **Design packs** live in `frontend/designs/<slug>/` and ship their own:
  - `manifest/components.php` — pack component manifest (M8 contract),
  - token overrides (pack palette/fonts),
  - `assets/` (pack CSS/JS/images).
- Activation: set option `aether_active_design = '<slug>'` (or `AETHER_DESIGN` constant). Body class becomes `design-<slug>`; **isolation by construction (M7):** luxury assets never load alongside a pack (`frontend.php:111` guard), packs never load alongside luxury.

### 4.2 To create and ship a pack
1. Scaffold `frontend/designs/<slug>/` (manifest + tokens + assets).
2. Wire the pack asset pipeline: `frontend/views/assets.php` enqueues pack assets when `aether_active_design() === '<slug>'` (platform CDNs + contract JS still load via `aether_design_enqueue_assets` for non-luxury designs).
3. Register the pack slug + add `design-<slug>` expectations to `tests/specs/design-isolation.spec.js` (per-design baselines, M9).
4. Verify: isolation 6/6, routes 32/32, `verify.sh` PASSED, freeze the pack's main asset MD5.
5. Sync `frontend/` to the container; commit.

### 4.3 Lumen — the reference pack (M10)
`frontend/designs/lumen/` is the first shipped pack — the proof that a full alternate presentation layer can live inside the same engine with zero theme-core changes. Study it before building a client pack.

---

## 5. How to CREATE a dynamic frontend (client replacement, M11)

The M11 workflow is the repeatable, client-proof pipeline. Master plan: `../docs/frontend-platform/MASTER_FRONTEND_REPLACEMENT_PLAN.md` (37 KB, full 37-section plan); per-client intake + mapping schemas: `../docs/frontend-platform/CLIENT_FRONTEND_INTAKE.md`, `MAPPING_MANIFEST_SCHEMA.md`, `COMPONENT_CONTRACT.md`, `DATA_CONTRACT.md`.

### 5.1 The pipeline
```
1. CLIENT INTAKE      → static design delivered (HTML/CSS/JS/images)
2. PHASE 0 (read-only audit)   → docs/frontend-platform/M11_FORENSIC_BASELINE.md
                                 + M11_CLIENT_FRONTEND_PROOF_PLAN.md
                                 → STOP. Approval gate before any code.
3. MAPPING            → component manifest mapping (design → engine components)
                         design tokens → token overrides
4. PACK SCAFFOLD      → frontend/designs/<client>/ (manifest, tokens, assets)
5. ADAPTERS           → only new data needs new adapters; reuse 23 existing
6. COMPONENTS/SECTIONS→ new templates only where mapping has no match
7. TESTS              → design-isolation entry, routes, visual baselines
8. SHIP               → verify.sh PASSED, MD5 frozen, commit + tag
```

### 5.2 The core-freeze rule (LEVEL 1–5)
Classify every needed change before touching shared code:
- **L1** engine template tweak (pack scope) → allowed, no approval.
- **L2** tokens/manifest within pack → allowed.
- **L3** adapter addition (new data source) → allowed, additive only.
- **L4** engine kernel (`views/loader|registry|renderer|viewmodel`) change → requires documented case.
- **L5** theme core (`aureon/theme/`) modification → **only for a proven core defect**; must be a bug with evidence, never a client preference. Client preferences are satisfied in the pack.

### 5.3 Guardrails (from the M11 plan)
- No one-off hacks in shared code; the first client frontend is **proof of the platform**, not a bespoke build.
- All client-specific behavior lives under `frontend/designs/<client>/`.
- Demo content (`aether_demo_content`) stays a master toggle; real data wins.
- Regression gates must stay green: design isolation 6/6, routes 32/32, `verify.sh`, MD5 freeze of shipped assets.

---

## 6. Testing & verification (M6–M10 harness)

| Gate | Command | Baseline |
|---|---|---|
| Engine gates | `bash frontend/tests/verify.sh` | PASSED |
| Manifest contract | `node frontend/tests/validate-manifest.cjs` | 1:1 with call sites |
| Design isolation | `npx playwright test specs/design-isolation.spec.js --project=desktop` | 6/6 |
| Routes | `npx playwright test specs/routes.spec.js --project=desktop` | 32/32 |
| Interactions / failure / a11y / visual | per-spec Playwright runs | green |
| JS/PHP syntax | `node --check` / `php -l` | 0 errors |
| CI | `.github/workflows/ci.yml` (static gate on push/PR; e2e via workflow_dispatch) | green |

Per-design visual baselines (M9) live in `frontend/tests/`; a design change that alters a baseline must be an intentional, reviewed change.