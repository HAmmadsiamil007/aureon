# 27 — ACTIVE-PACK ISOLATION

## Isolation Architecture

```
aether_active_design() ← single resolver
    ↓
aether_active_design_dir() ← only active pack
    ↓
aether_design_manifest() ← only active manifest
    ↓
aether_enqueue_pack_asset() ← only active CSS/JS
    ↓
aureon_aether_suppress_theme_output() ← kills ALL platform assets
```

**The single design resolver is the gatekeeper. Every downstream consumer reads this one value.**

## Isolation Types

### HTML Isolation
- Complete-page: Only active pack's HTML files served
- Component-mode: Only active pack's component overrides used
- No inactive pack DOM in page source

### CSS Isolation
- Complete-page: Only active pack's CSS loaded
- Component-mode: Only active pack's CSS + platform CDNs
- No inactive pack stylesheets
- No inactive pack @font-face
- No inactive pack CSS variables

### JavaScript Isolation
- Complete-page: Only active pack's JS executed
- Component-mode: Only active pack's JS + platform JS
- No inactive pack scripts
- No inactive pack globals
- No inactive pack event listeners

### Font Isolation
- Complete-page: Only active pack's fonts loaded
- Component-mode: Only platform fonts + pack fonts
- No inactive pack @font-face rules

### Data Isolation
- FermPageData belongs only to active client
- No stale client data after switching
- Per-request static cache resolves fresh

### Routing Isolation
- Active pack's pages mapping used
- No inactive pack route resolution

### Customizer Isolation
- Active pack's token defaults applied
- Customizer values scoped to active design

## Proven Architecture (Ferm ↔ Testclient)

| When Ferm Active | When Testclient Active |
|------------------|----------------------|
| Ferm CSS ✅ | Testclient CSS ✅ |
| Ferm JS ✅ | Testclient JS ✅ |
| Ferm DOM ✅ | Testclient DOM ✅ |
| Ferm data ✅ | Testclient data ✅ |
| Testclient CSS ❌ | Ferm CSS ❌ |
| Testclient JS ❌ | Ferm JS ❌ |
| Testclient DOM ❌ | Ferm DOM ❌ |
| Testclient data ❌ | Ferm data ❌ |

## Cache Isolation

Per-request static cache:
```php
static $design = null;
static $manifest = null;
```

Each new page load resolves fresh. No stale state between requests.

## Switching Proof

```
Ferm → Client B → Ferm → Client B → Ferm
```

On each fresh request:
- Active design is correct
- Correct manifest loaded
- Correct HTML served
- Correct assets loaded
- Inactive client assets absent
