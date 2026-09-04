# 02 — CORE ARCHITECTURE

## Architectural Layers

```
HTTP Request
    ↓
WordPress Bootstrap
    ↓
Active Theme (aureon/theme/)
    ↓
AUREON Initialization (functions.php → frontend.php → loader.php)
    ↓
Design Resolution (aether_active_design())
    ↓
Route Detection (is_product(), is_cart(), etc.)
    ↓
Template Selection (template_include filter)
    ↓
Data Loading (Adapters → ViewModels)
    ↓
Asset Loading (wp_enqueue_scripts)
    ↓
Rendering (Components/Sections or Complete-Page HTML)
    ↓
Browser
```

## Ownership Diagram

```
┌─────────────────────────────────────────────────────────┐
│                    GOLDEN AUREON CORE                     │
├──────────────┬──────────────────┬───────────────────────┤
│   PLATFORM   │  DATA/BUSINESS   │    EXTENSIBILITY      │
├──────────────┼──────────────────┼───────────────────────┤
│ WordPress    │ WooCommerce      │ Client Packs          │
│ Routing      │ Products         │ Complete Pages        │
│ Customizer   │ Variations       │ Component Mode        │
│ Menus        │ Cart             │ Design Resolver       │
│ Search       │ Checkout         │ Manifest System       │
│ Account      │ Authentication   │ Active-Pack Loading   │
│ Security     │ Orders           │ Client Isolation      │
│ Media        │ Pricing/Stock    │                       │
├──────────────┴──────────────────┴───────────────────────┤
│                   THIN CLIENT BRIDGE                      │
│            (Data Mapping · URL Rewriting · Translation)    │
├─────────────────────────────────────────────────────────┤
│                  ACTIVE CLIENT FRONTEND                    │
│             (HTML · CSS · JS · Assets · Fonts)            │
└─────────────────────────────────────────────────────────┘
```

## Two Rendering Modes

### Complete-Page Mode (`complete_page: true`)
- Client HTML served directly via `ferm-page.php`
- AUREON shell (header/footer) bypassed
- Platform assets suppressed (Bootstrap, Swiper, GSAP)
- Only pack CSS/JS loaded
- Client owns all presentation
- Bridge injects data via `FermPageData`

### Component Mode (`complete_page: false`)
- AUREON shell renders (header → sections → footer)
- Adapters fetch data from WP/WC
- ViewModels normalize data
- Components render presentation
- Platform CDNs + pack CSS/JS loaded
- Pack can override component templates

## Key Files

| File | Role |
|------|------|
| `aureon/theme/functions.php` | Theme bootstrap, includes all subsystems |
| `aureon/theme/inc/frontend.php` | Frontend engine integration, asset suppression |
| `aureon/frontend/views/loader.php` | Engine boot, loads adapters/sections |
| `aureon/frontend/views/design.php` | Design resolver, manifest loading |
| `aureon/frontend/views/assets.php` | Asset pipeline, pack asset enqueuing |
| `aureon/frontend/views/composer.php` | Shell composition (header/footer) |
| `aureon/frontend/views/renderer.php` | Component/section rendering |
| `aureon/frontend/views/viewmodel.php` | Data normalization helpers |
| `aureon/ferm-page.php` | Complete-page template host |

## Data Flow

```
WordPress/WooCommerce
    ↓
Adapters (adapter-*.php)
    ↓
ViewModel Normalization (viewmodel.php)
    ↓
Component Data ($componentData / $sectionData)
    ↓
Templates (components/**/*.php or sections/section-*.php)
    ↓
HTML Output
```

For complete-page mode:
```
WordPress/WooCommerce
    ↓
Ferm Composer (composer.php)
    ↓
FermPageData (wp_localize_script)
    ↓
Client JavaScript reads globals
    ↓
Client DOM updates
```

## Safety Boundaries

| Change Type | Safe? | Risk |
|-------------|-------|------|
| Add new adapter | ✅ SAFE | Low |
| Add new component | ✅ SAFE | Low |
| Modify client pack | ✅ SAFE | Low |
| Add Customizer setting | ⚠️ REVIEW | Medium |
| Modify template routing | ⚠️ REVIEW | Medium |
| Modify asset loading | ⚠️ REVIEW | Medium |
| Modify WooCommerce core | ❌ FORBIDDEN | Critical |
| Modify WordPress core | ❌ FORBIDDEN | Critical |
| Rebuild client frontend | ❌ FORBIDDEN | Critical |
