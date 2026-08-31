# DEMO REFERENCE CONTENT SYSTEM

**Purpose:** Make a newly installed client website beautiful before the client has entered their own content.

**Status:** PERMANENT REFERENCE

---

## Architecture

```
                    CLIENT CONTENT RESOLVER
                            │
             ┌──────────────┼──────────────┐
             │              │              │
          PRODUCTS       CATEGORIES     PRESENTATION
             │              │              │
             ↓              ↓              ↓
       real exists?    real exists?   custom exists?
        /      \        /      \        /       \
      YES      NO      YES      NO     YES       NO
       ↓        ↓       ↓        ↓      ↓         ↓
     REAL     DEMO    REAL     DEMO   CUSTOM    DEMO
```

---

## Core Principles

### 1. Content Source Model

```
CONTENT SOURCE
├── DEMO (curated reference data)
└── REAL (WordPress/WooCommerce data)
```

The frontend receives normalized presentation data and must NOT need to know whether the source is DEMO or REAL.

### 2. Global Replacement Rule

```
REAL PRODUCT COUNT > 0
    ↓
hide ALL demo products

REAL CATEGORY COUNT > 0
    ↓
hide ALL demo categories
```

Not "one category created → only that demo category disappears."

**Global replacement, not incremental.**

### 3. Slot-Level Fallback

Products and categories use **global existence rules**.
Logo, hero, headings use **slot-level fallback**.

```
Product:
  real product exists? → YES: hide all demo products
                       → NO:  show demo products

Category:
  real category exists? → YES: hide all demo categories
                        → NO:  show demo categories

Logo:
  custom logo exists? → YES: custom logo
                      → NO:  demo/default logo

Hero:
  custom hero exists? → YES: custom hero
                      → NO:  demo/default hero
```

### 4. Demo Records Are Never Deleted

Demo products, categories, and content remain in the database. They are:
- Filtered from queries when real content exists
- Preserved when real content is removed
- Restored to view when appropriate

---

## Demo Asset Model

### Remote URL Storage

Demo assets may reference curated remote URLs:

```json
{
  "src": "https://fermliving.com/...",
  "source": "fermliving.com",
  "last_verified": "2026-08-31",
  "fallback": "gradient",
  "alt": "Product image"
}
```

**Rules:**
- No automatic recurring scraping
- Manual URL updates only
- Fallback required for every remote asset
- Source tracking for maintainability

### Fallback Hierarchy

```
CUSTOM CLIENT ASSET
        ↓
ACTIVE CLIENT DEFAULT
        ↓
GENERIC AUREON DEFAULT
        ↓
neutral safe fallback
```

---

## Demo Product Model

### Marker

```php
aureon_demo = 1  // WooCommerce product meta
```

### Real Product Definition

```
REAL CLIENT PRODUCT
= published WooCommerce product
AND
aureon_demo != 1
```

### Behavior

```
No real products + demo enabled:
  → demo products visible

Real products exist:
  → ALL demo products hidden globally

All real products removed:
  → demo products return
```

### Cart Safety

**DEMO PRODUCTS MUST NEVER ENTER THE REAL CUSTOMER CART.**

```
Demo product → presentation only → no real Woo cart mutation
Real product → normal WooCommerce cart behavior
```

Enforced at the business boundary, not merely by hiding a button.

### URL Safety

```
Demo product → demo/reference presentation URL
Real product → WooCommerce permalink
```

Never send a client to a fake Shopify checkout/product backend.

---

## Demo Category Model

### Marker

```php
aureon_demo_category = 1  // WordPress term meta
```

### Real Category Definition

```
REAL CLIENT CATEGORY
= valid/public category
AND
aureon_demo_category != 1
```

### Behavior

```
No real categories + demo enabled:
  → demo categories visible

Real categories exist:
  → ALL demo categories hidden globally

All real categories removed:
  → demo categories return
```

---

## Demo Modes

| Mode | Behavior | Use Case |
|------|----------|----------|
| **AUTO** | Real content → hide demos; No real → show demos | Default production |
| **FORCE_DEMO** | Show demos regardless of real content | Sales/demo environment |
| **DISABLED** | Never show demos | Client handoff |

---

## Demo Manifest Structure

```
client-pack/
  demo/
    demo-products.json
    demo-categories.json
    demo-homepage.json
    demo-navigation.json
    demo-assets.json
```

### demo-assets.json

```json
{
  "logo": {
    "src": "https://fermliving.com/...",
    "source": "fermliving.com",
    "last_verified": "2026-08-31",
    "fallback": "site-name-text"
  },
  "hero": {
    "src": "https://fermliving.com/...",
    "source": "fermliving.com",
    "last_verified": "2026-08-31",
    "fallback": "gradient-overlay"
  }
}
```

### demo-products.json

```json
[
  {
    "source": "demo",
    "id": null,
    "title": "Rico Lounge Chair",
    "price": 1299,
    "image": "https://fermliving.com/...",
    "url": "/product/rico-lounge-chair/",
    "purchasable": false
  }
]
```

### demo-categories.json

```json
[
  {
    "source": "demo",
    "name": "Furniture",
    "count": 24,
    "image": "https://fermliving.com/..."
  }
]
```

---

## Customizer Integration

| Setting | Custom Value | Demo/Default |
|---------|--------------|--------------|
| Logo | Custom logo uploaded | Site name text or demo logo |
| Hero | Custom hero slides | Demo/default hero slides |
| Heading | Custom heading | Demo/default heading |
| Announcement | Custom announcement | Demo/default announcement |
| Footer | Custom footer columns | Demo/default footer |
| Social | Custom social links | Demo/default social links |

**Rules:**
- Custom value exists → show custom
- Custom missing → show demo/default
- Custom removed → demo/default automatically returns

---

## Client-Scoped Demo Content

Demo content must belong to the active client pack:

```
Ferm active:
  → Ferm demo manifest
  → Ferm demo products
  → Ferm demo categories

Client B active:
  → Client B demo manifest
  → Client B demo products
  → Client B demo categories
```

Golden Core must NOT contain:
```php
if ( fermliving === aether_active_design() ) { ... }
```

for demo presentation logic. Use generic, pack-scoped fallbacks.

---

## Normalized Data Schema

### Demo Product

```json
{
  "source": "demo",
  "business_id": null,
  "id": null,
  "title": "Product Name",
  "price": 19900,
  "image": "url",
  "purchasable": false
}
```

### Real Product

```json
{
  "source": "woocommerce",
  "business_id": 834,
  "id": 834,
  "title": "Product Name",
  "price": 19900,
  "image": "url",
  "purchasable": true
}
```

The frontend receives the same presentation fields regardless of source.

---

## Cache Safety

Demo/real state must not become stale.

After:
- Creating product
- Deleting product
- Creating category
- Deleting category
- Uploading logo
- Removing logo

Invalidate/update relevant presentation cache.

Never let demo content remain visible after real content exists because of stale cached results.

---

## Asset Failure Fallback

Because demo assets may reference remote sources:

```
Remote demo asset unavailable
    ↓
Use configured fallback
    ↓
Do not break entire frontend
```

---

## Security

Validate/sanitize all configurable URLs and content.

Never allow demo configuration to create:
- Arbitrary executable code
- Unsafe scripts
- Untrusted HTML
- Unsafe redirects

---

## Admin UX

Expose clear controls in Customizer:

```
Demo Content
  → Auto / Force Demo / Disabled

Site Identity
  → Logo

Homepage
  → Hero image
  → Heading
  → Subtitle
  → CTA
```

Do not create a second competing settings system.

---

## Testing Matrix

### Empty Client State

| Input | Expected |
|-------|----------|
| No logo | Demo/default logo visible |
| No hero | Demo/default hero visible |
| No heading | Demo/default heading visible |
| No products | Demo products visible |
| No categories | Demo categories visible |

### Content Addition

| Action | Expected |
|--------|----------|
| Upload logo | Demo logo disappears |
| Upload hero | Demo hero disappears |
| Change heading | Demo heading replaced |
| Create ONE real product | ALL demo products disappear |
| Create ONE real category | ALL demo categories disappear |

### Content Removal

| Action | Expected |
|--------|----------|
| Remove custom logo | Demo/default logo returns |
| Remove custom hero | Demo/default hero returns |
| Remove custom heading | Demo/default heading returns |
| Remove all real products | Demo products return |
| Remove all real categories | Demo categories return |

---

## Hard Stop Conditions

STOP if:
- Demo content must be deleted instead of filtered
- Demo products can enter real cart
- External Ferm APIs become business dependencies
- Golden Core must hardcode Ferm
- Frontend must be rebuilt
- Existing proven Woo features would be weakened
- Complete client presentation must be split/recreated

---

## Final Behavior

```
NEW CLIENT
    ↓
beautiful Ferm demo automatically visible
    ↓
client uploads logo → logo replaced
client uploads hero → hero replaced
client changes heading → heading replaced
    ↓
client creates ONE product → ALL demo products disappear
client creates ONE category → ALL demo categories disappear
    ↓
client removes logo → demo/default logo returns
client removes hero → demo/default hero returns
client removes all real products → demo products return
client removes all real categories → demo categories return
```

---

## Document Links

| Document | Purpose |
|----------|---------|
| [GOLDEN-AUREON-FRONTEND-WORKFLOWS.md](GOLDEN-AUREON-FRONTEND-WORKFLOWS.md) | Master index |
| [NEW-CLIENT-TEMPLATE-CREATION-PLAN.md](NEW-CLIENT-TEMPLATE-CREATION-PLAN.md) | Template creation |
| [FRONTEND-REPLACEMENT-PLAN.md](FRONTEND-REPLACEMENT-PLAN.md) | Frontend replacement |
| [FRONTEND-EDIT-PLAN.md](FRONTEND-EDIT-PLAN.md) | Frontend editing |
