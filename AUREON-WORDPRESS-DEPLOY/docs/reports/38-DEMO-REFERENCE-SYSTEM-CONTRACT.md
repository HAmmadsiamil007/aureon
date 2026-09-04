# 38 — DEMO REFERENCE SYSTEM CONTRACT

**Status:** PERMANENT REFERENCE
**Version:** 1.0
**Date:** 2026-08-31

---

## Purpose

This document consolidates ALL demo reference system rules into a single authoritative contract. It is the definitive source for how demo content behaves in Golden AUREON.

---

## 1. DEMO SOURCE MODEL

### Two Demo Data Sources

```
DEMO REFERENCE DATA (JSON)
→ Stored in client-pack/demo/ directory
→ demo-products.json
→ demo-categories.json
→ demo-assets.json
→ demo-homepage.json
→ Never in WooCommerce database
→ Presentation-only fallback

DEMO WOOCOMMERCE RECORDS (optional)
→ WC products with aureon_demo=1 meta
→ WC categories with aureon_demo_category=1 meta
→ Filtered from queries when real content exists
→ Never deleted, just hidden
```

### Which Source to Use

| Content Type | Source | Reason |
|-------------|--------|--------|
| Products | JSON | Lightweight, no DB overhead |
| Categories | JSON | Lightweight, no DB overhead |
| Logo | JSON/Customizer | Simple text/URL |
| Hero | JSON/Customizer | Presentation asset |
| Heading | JSON/Customizer | Simple text |
| Announcement | Customizer | Client-configurable |
| Footer | Customizer | Client-configurable |
| Social | Customizer | Client-configurable |

---

## 2. DEMO vs REAL IDENTITY

### Real Product Definition

```
REAL CLIENT PRODUCT =
  published
  + public/catalog-eligible
  + not marked demo (aureon_demo != 1)
```

**Excludes:**
- trash
- draft
- private
- pending
- auto-draft
- subscription/internal/admin-only

### Real Category Definition

```
REAL CLIENT CATEGORY =
  valid/public WooCommerce category
  + not marked demo (aureon_demo_category != 1)
  + has published products (hide_empty semantics)
```

### Demo Product Identity

```json
{
  "source": "demo",
  "business_id": null,
  "demo_id": "rico-lounge-chair",
  "name": "Rico Lounge Chair",
  "purchasable": false
}
```

**Rules:**
- `source` = "demo" (never "woocommerce")
- `business_id` = null (no real WC product ID)
- `demo_id` = string identifier (not numeric to avoid confusion with WC IDs)
- `purchasable` = false (always)

### Real Product Identity

```json
{
  "source": "woocommerce",
  "business_id": 834,
  "id": 834,
  "name": "Product Name",
  "purchasable": true
}
```

---

## 3. PRODUCT RULES

### Global Replacement Rule

```
REAL PRODUCT COUNT > 0
  → hide ALL demo products globally

REAL PRODUCT COUNT = 0
  → show demo products
```

**Not incremental.** One real product hides ALL demo products.

### Filtering Behavior

```
Front-end queries:
  → demo products filtered out when real products exist
  → admin queries: NOT filtered (show all)

FORCE_DEMO mode:
  → demo products shown regardless of real content
  → admin queries: NOT affected
```

### Product Query Filter

```php
// ferm_filter_demo_products()
// Applied to woocommerce_product_query hook
// Adds meta_query to exclude aureon_demo=1
// Only when:
//   - is_admin() = false
//   - mode != 'force_demo'
//   - real products exist
```

---

## 4. CATEGORY RULES

### Global Replacement Rule

```
REAL CATEGORY COUNT > 0
  → hide ALL demo categories globally

REAL CATEGORY COUNT = 0
  → show demo categories
```

**Not incremental.** One real category hides ALL demo categories.

### Filtering Behavior

```
Front-end queries:
  → demo categories filtered out when real categories exist
  → admin queries: NOT filtered (show all)

FORCE_DEMO mode:
  → demo categories shown regardless of real content
```

---

## 5. ASSET RULES

### Asset Fallback Chain

```
CUSTOM VALUE (Customizer)
  ↓ if empty
ACTIVE CLIENT DEMO DEFAULT (demo-assets.json)
  ↓ if empty
STATIC CLIENT FALLBACK (frozen HTML)
  ↓ if empty
GENERIC SAFE FALLBACK (neutral placeholder)
```

### Asset Resolution Per Slot

| Slot | Custom → Demo → Static → Generic |
|------|-----------------------------------|
| Logo | Custom logo → demo-assets.json logo → site name text → "Welcome" |
| Hero | Custom slides → demo-assets.json hero → frozen HTML hero → gradient |
| Heading | Custom heading → demo-assets.json heading → frozen HTML heading → "Welcome" |
| Announcement | Custom items → demo defaults → frozen HTML → "Free shipping" |
| Footer | Custom columns → demo defaults → frozen HTML → empty |
| Social | Custom links → demo defaults → frozen HTML → empty |

### Asset Health Metadata

```json
{
  "src": "...",
  "required": true,
  "source_site": "fermliving.com",
  "last_verified": "2026-08-31",
  "fallback": "...",
  "description": "..."
}
```

**Required field:**
- `required: true` → asset is critical, fallback must exist
- `required: false` → asset is optional, failure is non-critical

---

## 6. FALLBACK RULES

### Valid Custom Value

```
Customizer value exists?
  → validate it actually resolves to usable content
  → not merely: setting != empty

Example:
  Custom logo URL set
    → check: does the image load?
    → if broken: treat as empty, use demo fallback
    → if valid: use custom
```

### Invalid/Deleted Custom Value

```
Custom value set but image deleted?
  → treat as invalid
  → fall through to demo/default
  → never show broken frontend
```

### Image Fallback (Runtime)

```
Remote demo image fails to load?
  → replace with safe neutral placeholder
  → no broken-image icon
  → no fatal console error
  → site still works
```

---

## 7. CUSTOMIZER RULES

### Settings That Support Demo Fallback

| Setting | Custom | Demo | Static |
|---------|--------|------|--------|
| Logo | Custom logo | demo-assets.json | site name |
| Hero | aether_hero_slides | demo-assets.json hero | frozen HTML |
| Heading | aether_site_heading | demo-assets.json heading | frozen HTML |
| Announcement | aether_announcement_items | demo defaults | frozen HTML |
| Footer | aether_footer_columns | demo defaults | frozen HTML |
| Social | aether_social_items | demo defaults | frozen HTML |

### Preview Behavior

```
Customizer preview:
  → shows custom value immediately
  → if custom empty, shows demo/default
  → if demo empty, shows static fallback
```

### Publish Behavior

```
Customizer publish:
  → saves to aureon_settings
  → frontend reflects new state
  → cache invalidated
```

### Remove/Reset Behavior

```
Customizer value removed:
  → demo/default automatically returns
  → no manual intervention needed
```

---

## 8. SEARCH RULES

### Demo Content in Search

```
AUTO mode + no real products:
  → demo content may participate in search results

AUTO mode + real products:
  → demo content excluded from search

DISABLED mode:
  → demo content excluded from search

FORCE_DEMO mode:
  → demo content included in search
```

### Search Implementation

```
WP search query
  ↓
ferm_filter_demo_products() applied
  ↓
Demo products filtered when real exist
  ↓
Results returned
```

---

## 9. CATEGORY PAGE RULES

### Demo Category Pages

```
Demo category URL:
  → presentation-only route
  → not a real WooCommerce category
  → shows demo products from JSON

Real category URL:
  → WooCommerce category
  → shows real WC products
  → standard WooCommerce behavior
```

### Category Page Behavior

```
No real categories + demo enabled:
  → demo category pages visible

Real categories exist:
  → demo category pages hidden
  → real category pages shown
```

---

## 10. URL RULES

### Demo URL Isolation

```
Demo product URL:
  → /product/rico-lounge-chair/
  → controlled demo/reference route
  → presentation-only

Real product URL:
  → WooCommerce permalink
  → /product/{slug}/
  → real commerce behavior
```

### URL Safety

```
NEVER use:
  → Shopify cart
  → Shopify checkout
  → Shopify customer API
  → Shopify business API

Demo URLs:
  → may reference external presentation assets
  → must NOT become real business URLs
  → must NOT trigger real cart/checkout
```

---

## 11. CART SAFETY

### Multi-Layer Protection

```
LAYER 1 — Frontend Guard:
  → demo products have purchasable=false
  → add-to-cart button hidden/disabled

LAYER 2 — AJAX Business Boundary Guard:
  → ferm_wc_ajax_cart_add() checks aureon_demo meta
  → blocks demo products from entering cart
  → also validates: published, in-stock

LAYER 3 — Server-Side Purchase Validation:
  → WC checkout validates product status
  → demo products cannot reach checkout
  → demo products cannot become orders
```

### Invariant

```
DEMO PRODUCT
  → never purchasable
  → never addable to cart
  → never allowed into checkout
  → never orderable
```

### Test via Direct Request

```
POST to ferm_cart_add with demo product ID
  → must return error
  → must NOT add to cart
  → must NOT create order
```

---

## 12. CHECKOUT SAFETY

```
Demo product in cart (somehow):
  → WC checkout validates product status
  → demo product blocks checkout
  → order cannot be placed

Real product in cart:
  → normal WooCommerce checkout
  → real order created
```

---

## 13. CACHE RULES

### Cache Invalidation Points

```
Product create    → invalidate product cache
Product delete    → invalidate product cache
Category create  → invalidate category cache
Category delete  → invalidate category cache
Logo upload      → invalidate customizer cache
Logo remove      → invalidate customizer cache
Hero upload      → invalidate customizer cache
Hero remove      → invalidate customizer cache
```

### Cache Transition Test

```
Test 1: First real product
  → verify cached demo disappears immediately

Test 2: Last real product removed
  → verify cached demo returns immediately

Failure mode to prevent:
  database = real product exists
  cache = old demo state
  browser = demo still visible
```

### Acceptance Criterion

```
Cache-busting/invalidation must be EXPLICIT
and VERIFIED for every state transition.
```

---

## 14. DEMO MODES

### Mode Definitions

| Mode | Behavior | Scope |
|------|----------|-------|
| AUTO | Real content → hide demos; No real → show demos | Client/default |
| FORCE_DEMO | Show demos regardless of real content | Admin/dev only |
| DISABLED | Never show demo content | Client handoff |

### Mode Scope Rules

```
AUTO
  → default for all clients
  → normal production behavior

FORCE_DEMO
  → admin/development/demo environment only
  → controls PRESENTATION only
  → does NOT delete real WooCommerce data
  → does NOT hide real products from admin
  → does NOT affect checkout/cart for real products
  → does NOT modify WooCommerce business logic

DISABLED
  → client handoff when real content is complete
  → no demo content shown
  → empty states displayed instead
```

### Mode Switching

```
Only administrators can change demo mode
Mode changes take effect immediately
Mode is stored in aureon_settings
```

---

## 15. ACTIVE-CLIENT SCOPING

### Demo Content Belongs to Active Client Pack

```
Ferm active:
  → Ferm demo/ directory loaded
  → Ferm demo-products.json
  → Ferm demo-categories.json
  → Ferm demo-assets.json

Client B active:
  → Client B demo/ directory loaded
  → Client B demo products
  → Client B demo categories
  → Client B demo assets
  → Ferm demo ABSENT
```

### Golden Core Must NOT Contain

```php
// FORBIDDEN:
if ( fermliving === aether_active_design() ) {
    // demo logic
}

// REQUIRED:
// Generic, pack-scoped fallbacks
// No client-specific demo logic in Golden Core
```

---

## 16. REMOTE URL POLICY

### Classification

```
REMOTE DEMO PRESENTATION DEPENDENCY
  → optional
  → non-business
  → non-critical
  → fallback-protected
  → manual-maintenance
```

### Rules

```
1. No automatic recurring scraping
2. Manual URL updates only
3. Every remote asset MUST have a fallback
4. Fallback must be local or neutral placeholder
5. Remote asset failure must never break frontend
6. Source tracking required (source_site field)
7. Last verification date required (last_verified field)
```

### Performance

```
Demo images:
  → may be remote (presentation only)
  → must have lazy loading
  → must have alt text
  → must have error fallback

Real images:
  → WordPress/WooCommerce media
  → local storage
  → optimized sizes
```

---

## 17. ADMIN BEHAVIOR

### Critical Boundary

```
DEMO FILTERS ARE PRESENTATION/QUERY CONTROLS,
NOT DESTRUCTIVE ADMIN DATA DELETION.
```

### Admin Frontend

```
WordPress admin:
  → ALL products visible (including demo)
  → ALL categories visible (including demo)
  → demo flags visible in product editor
  → no mysterious disappearances
```

### Admin Backend

```
WooCommerce admin:
  → full product list
  → demo products marked with aureon_demo=1
  → can edit/delete demo products
  → can create real products
  → demo mode does not affect admin queries
```

---

## 18. SECURITY

### Validation Rules

```
1. Validate/sanitize all configurable URLs
2. Never allow demo configuration to create:
   - arbitrary executable code
   - unsafe scripts
   - untrusted HTML
   - unsafe redirects
3. Demo product IDs must never collide with real WC IDs
4. Demo URLs must never trigger real business actions
```

### Input Sanitization

```
Customizer values:
  → sanitized on save
  → escaped on output
  → no raw HTML in demo content

Demo JSON:
  → validated on load
  → malformed JSON ignored
  → fallback to defaults
```

---

## 19. PERFORMANCE

### Lightweight Demo Strategy

```
BEFORE (broken):
  Ferm site → copy 6GB media → WordPress → hosting failures

AFTER (working):
  Ferm reference → curated URLs in JSON → lightweight manifest → works
```

### Requirements

```
1. No 6GB media library
2. No recurring scraping
3. Demo JSON files < 10KB total
4. Demo images: remote or local pack assets
5. No additional database queries for demo data
6. Demo filtering uses existing meta_query (no new tables)
```

### Metrics

```
Disk size: < 1MB (demo JSON + manifest)
File count: < 10 files (demo directory)
Page requests: same as without demo
Image requests: only visible demo images
Network size: minimal (remote images lazy-loaded)
```

---

## 20. ACCEPTANCE TESTS

### Demo Product Transition

| State | Expected | Method |
|-------|----------|--------|
| 0 real products | Demo shown | Verify demo-products.json loaded |
| 1 real product | ALL demo hidden | Create product, verify filtering |
| 2+ real products | Demo remains hidden | Verify filtering continues |
| 0 real products (again) | Demo returns | Delete all real, verify restore |

### Demo Category Transition

| State | Expected | Method |
|-------|----------|--------|
| 0 real categories | Demo shown | Verify demo-categories.json loaded |
| 1 real category | ALL demo hidden | Create category, verify filtering |
| 0 real categories (again) | Demo returns | Delete all real, verify restore |

### Customizer Fallback

| State | Expected | Method |
|-------|----------|--------|
| No custom logo | Demo/default logo | Verify FermPageData.customizer.site.logo_url |
| Custom logo set | Custom logo | Upload logo, verify |
| Custom logo removed | Demo/default returns | Remove logo, verify |
| No custom hero | Demo hero | Verify FermPageData.customizer.hero |
| Custom hero set | Custom hero | Set hero, verify |
| Custom hero image deleted | Demo fallback | Delete image, verify fallback |
| No custom heading | Demo heading | Verify FermPageData.customizer.heading |

### Cart Safety

| Test | Expected | Method |
|------|----------|--------|
| Demo product add to cart (UI) | Blocked | Click add-to-cart, verify error |
| Demo product add to cart (direct) | Blocked | AJAX request, verify error |
| Real product add to cart | Works | AJAX request, verify success |
| Real product in cart | Normal | Verify cart state |
| Demo product in checkout | Blocked | Attempt checkout, verify block |

### Search

| Test | Expected | Method |
|------|----------|--------|
| Search (no real products) | Demo in results | Search, verify demo appears |
| Search (real products exist) | Demo excluded | Search, verify demo absent |
| Search (FORCE_DEMO) | Demo in results | Search, verify demo appears |
| Search (DISABLED) | Demo excluded | Search, verify demo absent |

### Responsive

| Viewport | Expected | Method |
|----------|----------|--------|
| 1440px | Works | Screenshot |
| 1024px | Works | Screenshot |
| 768px | Works | Screenshot |
| 390px | Works | Screenshot |

### Cache

| Test | Expected | Method |
|------|----------|--------|
| Create product → demo disappears | Immediate | Verify no stale cache |
| Delete product → demo returns | Immediate | Verify no stale cache |
| Upload logo → custom appears | Immediate | Verify no stale cache |
| Remove logo → demo returns | Immediate | Verify no stale cache |

### Image Fallback

| Test | Expected | Method |
|------|----------|--------|
| Remote image loads | Displayed | Verify image visible |
| Remote image fails | Placeholder shown | Block URL, verify fallback |
| Broken custom image | Demo fallback | Set broken URL, verify |

---

## Document Links

| Document | Purpose |
|----------|---------|
| [GOLDEN-AUREON-FRONTEND-WORKFLOWS.md](GOLDEN-AUREON-FRONTEND-WORKFLOWS.md) | Master index |
| [DEMO-REFERENCE-CONTENT-SYSTEM.md](DEMO-REFERENCE-CONTENT-SYSTEM.md) | Demo architecture |
| [DEMO-REFERENCE-SYSTEM-IMPLEMENTATION-PLAN.md](DEMO-REFERENCE-SYSTEM-IMPLEMENTATION-PLAN.md) | Implementation plan |
| [37-GOLDEN-CORE-REFERENCE.md](37-GOLDEN-CORE-REFERENCE.md) | Core reference |

---

## Final Classification

```
GOLDEN CORE ARCHITECTURE      ✅ KEEP
DEMO ARCHITECTURE             ✅ KEEP
GLOBAL REPLACEMENT RULE       ✅ KEEP
REMOTE URL STRATEGY           ✅ KEEP
6GB MEDIA APPROACH            ❌ DO NOT RETURN

DOCUMENTATION                 ✅ HARDENED
REMOTE FALLBACK               ✅ IMPLEMENTED
DEMO IDENTITY CONTRACT        ✅ CLARIFIED
SEARCH/CATEGORY DEMO RULES    ✅ ADDED
CACHE TRANSITION TEST         ✅ ADDED
ADMIN/PRESENTATION BOUNDARY   ✅ DOCUMENTED
```
