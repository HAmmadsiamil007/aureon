# GOLDEN CORE: SOURCE-OF-TRUTH DOCUMENTATION

**Date:** 2026-08-31
**Phase:** 8 — Core Cleanup
**Status:** ✅ ANALYSIS COMPLETE

---

## Executive Summary

The repository contains **four candidate AUREON core trees** plus a deployment artifact:

| Tree | Files Tracked | Status | Verdict |
|------|---------------|--------|---------|
| `aureon/` | 738 | **ACTIVE** | ✅ SOURCE OF TRUTH |
| `AUREON-GOLDEN-COPY/` | 978 | FROZEN | 📦 ARCHIVE & REMOVE |
| `AUREON-WORDPRESS-DEPLOY/` | 674 | FROZEN | 📦 ARCHIVE & REMOVE |
| `_temp_golden/` | 767 | FROZEN | 📦 ARCHIVE & REMOVE |
| `theme/` | 0 (gitignored) | DEPLOYMENT | ✅ KEEP (Docker artifact) |

---

## 1. Active Core: `aureon/`

### Structure
```
aureon/
├── ferm-page.php              ← Complete-page template (latest version)
├── frontend/                  ← AETHER engine (views, adapters, components, designs)
│   ├── views/                 ← design.php, assets.php, composer.php, etc.
│   ├── designs/               ← Client packs (fermliving, lumen)
│   │   └── fermliving/        ← Active client pack with CDN assets
│   ├── adapters/              ← WP/WC data adapters
│   ├── components/            ← AETHER component templates
│   ├── sections/              ← Section templates
│   ├── tokens/                ← Design tokens
│   └── manifest/              ← Component manifest
├── plugin/                    ← AUREON plugin (customizer, sections, etc.)
└── theme/                     ← WordPress theme (subset of files)
    ├── functions.php          ← Theme bootstrap
    ├── header.php             ← wp_head() + aether_compose_header()
    ├── footer.php             ← aether_compose_footer() + wp_footer()
    ├── inc/                   ← Theme includes (frontend.php, tokens, security, etc.)
    ├── assets/                ← Theme CSS/JS assets
    ├── checkout/              ← WC checkout templates
    ├── myaccount/             ← WC account templates
    ├── woocommerce/           ← WC templates
    └── frontend/              ← Legacy integration directory
```

### Why This Is Authoritative

1. **Runtime path:** WordPress loads `theme/aureon/` which requires `aureon/frontend/views/loader.php` via:
   ```php
   // theme/aureon/inc/frontend.php
   require_once get_template_directory() . '/../../frontend/views/loader.php';
   ```

2. **Latest versions:** All key files have been updated in this tree:
   - `aureon/ferm-page.php` — latest complete-page template
   - `aureon/frontend/views/design.php` — latest design resolver
   - `aureon/frontend/views/assets.php` — latest asset pipeline
   - `aureon/frontend/designs/fermliving/composer.php` — latest Ferm bridge

3. **CDN assets:** Contains the full Ferm Living CDN assets (9789 files) required for frozen HTML rendering.

4. **Git tracking:** 738 files tracked, all development activity happens here.

---

## 2. Frozen Copy: `AUREON-GOLDEN-COPY/`

### Origin
Created in commit `13bd4a3` ("FREEZE: Golden Aureon pre-transformation checkpoint") as a frozen snapshot before the AETHER transformation began.

### Structure
```
AUREON-GOLDEN-COPY/
├── aureon/                    ← Full Aureon theme (original layout)
│   ├── [all theme files]      ← 404.php, archive.php, header.php, etc.
│   ├── frontend/              ← Older engine version
│   ├── plugin/                ← Same plugin
│   └── [no theme/ subdirectory]
├── docs/                      ← Documentation
├── .serena/                   ← AI assistant memories
└── .superpowers/              ← AI assistant specs
```

### Why It's Obsolete

1. **Older engine version:** `design.php` hash `a8e615319198bc147fa05eed9abf407f` vs active `e28da370a0ebc055f60b50aad1ee79f7`
2. **Older ferm-page.php:** Hash `be2ac57da84a8ac35bccf7cb252cd110` vs active `731aec9548f3490245c6502f9df7e709`
3. **No CDN assets:** Gitignored, so frozen HTML would fail to render images
4. **Original layout:** Theme files at root level, not in `theme/` subdirectory
5. **No unique functionality:** All PHP files are either identical or older versions

### Unique Content (Non-PHP)
- `.serena/memories/` — AI assistant context (can be preserved separately)
- `.superpowers/` — AI assistant specs (can be preserved separately)
- `docs/` — Documentation (some unique files, but can be preserved separately)

---

## 3. Frozen Copy: `AUREON-WORDPRESS-DEPLOY/`

### Origin
Created in the same commit `13bd4a3` as a deployment-focused snapshot.

### Structure
Identical to `AUREON-GOLDEN-COPY/aureon/` (same hash for all PHP files).

### Why It's Obsolete
- Exact duplicate of `AUREON-GOLDEN-COPY/aureon/`
- No unique functionality
- No unique files

---

## 4. Frozen Copy: `_temp_golden/`

### Origin
Created in the same commit `13bd4a3` as a temporary working copy.

### Structure
Identical to `AUREON-GOLDEN-COPY/aureon/` (same hash for all PHP files).

### Why It's Obsolete
- Exact duplicate of `AUREON-GOLDEN-COPY/aureon/`
- No unique functionality
- No unique files

---

## 5. Deployment Artifact: `theme/`

### Structure
```
theme/
├── aureon/                    ← WordPress theme directory
├── aureon-studio/             ← AUREON Studio plugin
├── frontend/                  ← Frontend assets (for deployment)
├── mu-plugins/                ← Must-use plugins
├── *.zip                      ← Deployment packages
└── README.md                  ← Deployment instructions
```

### Why It's Kept
- **Gitignored:** Line 50 of `.gitignore`: `/theme/`
- **Deployment artifact:** Contains ZIP packages for Docker deployment
- **Docker mounts:** WordPress runtime mounts `theme/aureon/` as the active theme
- **Not a source tree:** This is a build output, not a development directory

---

## 6. Dependency Trace

### References to Frozen Copies
| Reference Type | Count | Status |
|----------------|-------|--------|
| PHP require/include | 0 | ✅ No code dependencies |
| Asset paths | 0 | ✅ No asset dependencies |
| Docker mounts | 0 | ✅ No Docker dependencies |
| Configuration | 0 | ✅ No config dependencies |
| Scripts | 0 | ✅ No script dependencies |
| CI/build | 0 | ✅ No CI dependencies |

**Conclusion:** The frozen copies have ZERO active references. They are safe to archive and remove.

---

## 7. Cleanup Strategy

### Safe Actions
1. **Archive `AUREON-GOLDEN-COPY/`** — Create verified archive, then remove from git
2. **Archive `AUREON-WORDPRESS-DEPLOY/`** — Create verified archive, then remove from git
3. **Archive `_temp_golden/`** — Create verified archive, then remove from git
4. **Keep `aureon/`** — Active core, source of truth
5. **Keep `theme/`** — Deployment artifact (gitignored)

### Unsafe Actions (DO NOT)
- ❌ Delete `aureon/` — Active core
- ❌ Delete `theme/` — Deployment artifact
- ❌ Modify any PHP files — Phase 8 is cleanup only
- ❌ Modify any frontend code — Phase 8 is cleanup only
- ❌ Remove CDN assets from `aureon/` — Required for frozen HTML rendering

---

## 8. Runtime Loading Path

```
WordPress
  ↓
Docker mounts theme/aureon/ as active theme
  ↓
theme/aureon/functions.php
  ↓
theme/aureon/inc/frontend.php
  ↓
aureon/frontend/views/loader.php (via ../../ relative path)
  ↓
aether_frontend_boot()
  ↓
aether_active_design() → 'fermliving'
  ↓
aureon/frontend/designs/fermliving/ (active pack)
  ↓
Browser renders complete Ferm Living frontend
```

---

## 9. Post-Cleanup Architecture

```
                    GOLDEN AUREON
                         │
              ┌──────────┴──────────┐
              │                     │
          CORE ENGINE          CLIENT PACKS
              │                     │
       Woo / Customizer       Ferm / Client B / ...
       Routing / Security           │
       Menus / Search               │
       Account / Cart               │
              │                      │
              └──────────┬───────────┘
                         ↓
                  Active Client Only
```

**Canonical paths:**
- Core engine: `aureon/frontend/`
- Theme: `aureon/theme/`
- Plugin: `aureon/plugin/`
- Client packs: `aureon/frontend/designs/<slug>/`
- Deployment: `theme/` (gitignored)

---

## 10. Verification Checklist

After cleanup, verify:
- [ ] `aureon/` contains all active code
- [ ] No frozen copies remain in git
- [ ] Runtime still works (homepage, products, cart, account)
- [ ] Customizer still works (logo, hero, announcement, footer)
- [ ] Active pack loading still works (Ferm assets only)
- [ ] Git state is clean (no untracked frozen copies)
- [ ] Documentation is preserved (unique docs from frozen copies)
