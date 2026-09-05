# 37 — GOLDEN CORE REFERENCE

## Quick Start

**New to this repository? Read this first.**

Golden AUREON is a multi-client premium frontend platform on WordPress + WooCommerce.

## Architecture

```
GOLDEN AUREON CORE
    ↓
ACTIVE DESIGN RESOLVER
    ↓
CLIENT PACK (or ENGINE DEFAULTS)
    ↓
THIN BRIDGE
    ↓
WORDPRESS / WOOCOMMERCE
```

**Core = Platform | Client = Presentation | Bridge = Connector**

## Important Paths

| Path | Purpose |
|------|---------|
| `aureon/frontend/views/` | Engine kernel (design, assets, renderer) |
| `aureon/frontend/adapters/` | WP/WC data access |
| `aureon/frontend/components/` | Presentation components |
| `aureon/frontend/sections/` | Page section compositions |
| `aureon/frontend/designs/` | Client design packs |
| `aureon/frontend/manifest/` | Component manifest |
| `aureon/frontend/tokens/` | Design tokens |
| `aureon/theme/` | WordPress theme |
| `aureon/theme/inc/` | Theme includes |
| `aureon/ferm-page.php` | Complete-page host |

## Important Files

| File | What It Does |
|------|-------------|
| `views/loader.php` | Boots the engine |
| `views/design.php` | Resolves active design |
| `views/assets.php` | Loads CSS/JS |
| `views/renderer.php` | Renders components/sections |
| `views/composer.php` | Composes shell (header/footer) |
| `views/viewmodel.php` | Normalizes data |
| `adapters/adapter-*.php` | Access WP/WC data |
| `manifest/components.php` | Component registry |
| `tokens/tokens.php` | Engine defaults |
| `ferm-page.php` | Complete-page template |
| `theme/inc/frontend.php` | Engine integration |

## Request Lifecycle

```
HTTP → WP → Theme → Design Resolution → Route Detection → Template → Data → Assets → Render → Browser
```

## Two Modes

### Complete-Page (`complete_page: true`)
- Frozen HTML served directly
- AUREON shell bypassed
- Platform assets suppressed
- Only pack CSS/JS loaded
- Data via FermPageData globals

### Component Mode (`complete_page: false`)
- AUREON shell renders
- Adapters fetch data
- Components render presentation
- Platform CDNs + pack CSS/JS

## Quick Answers

### Where does product data come from?
`adapters/adapter-product.php` → `aether_adapter_product()`

### Where does the client frontend live?
`aureon/frontend/designs/<slug>/`

### How is the active design selected?
`aether_active_design()` → constant > option > default 'fermliving'

### How are assets loaded?
`aether_design_enqueue_assets()` → pack-first, active-pack-only

### How does a complete page work?
`ferm-page.php` → `aureon_ferm_resolve_page()` → serve frozen HTML

### How does WooCommerce reach the frontend?
Adapters → ViewModels → Components (or FermPageData bridge)

### How does Customizer reach the frontend?
Tokens → CSS custom properties (component mode)
FermPageData.customizer → JS (complete-page)

### How do menus reach the frontend?
`adapter-menu.php` → tree structure → shell component or FermPageData

### How does search work?
Search modal → form → `/?s=query` → WordPress search

### How does account/login work?
Logged out: Ferm login.html or WC login
Logged in: WC native my-account.php

### How does cart work?
AJAX handlers → WC()->cart → response

### How do I add a new client?
1. Create pack in `designs/<slug>/`
2. Add `manifest.json`
3. Set `aether_active_design` option
4. Test isolation

### What must never be changed?
- WooCommerce core
- WordPress core
- Client frontend (unless editing that specific client)
- Required hooks without dependency tracing

## Golden Core Protection

```
GOLDEN AUREON v1.0 = PROTECTED RELEASE BASELINE
    ↓
CLIENT PACKS CHANGE AROUND IT
    ↓
CORE REMAINS STABLE
```

## Git State

- Branch: master
- Release tag: v1.0.0-golden-aureon-release
- Latest commit: Phase 11 final acceptance
