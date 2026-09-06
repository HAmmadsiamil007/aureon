# Post-Cleanup Full Regression Report

**Date:** 2026-08-31

## Route Test (18/18 PASS)

| Route | Status | Title |
|-------|--------|-------|
| `/` | 200 | Ferm Living Demo |
| `/shop/` | 200 | Shop – Ferm Living Demo |
| `/cart/` | 200 | Cart – Ferm Living Demo |
| `/checkout/` | 200 | Cart – Ferm Living Demo (redirects when empty) |
| `/my-account/` | 200 | My Account – Ferm Living Demo |
| `/contact/` | 200 | Contact – Ferm Living Demo |
| `/about/` | 200 | About – Ferm Living Demo |
| `/store-locator/` | 200 | Store Locator – Ferm Living Demo |
| `/wishlist/` | 200 | Wishlist – Ferm Living Demo |
| `/blog/` | 200 | Blog – Ferm Living Demo |
| `/product-category/furniture/` | 200 | Furniture – Ferm Living Demo |
| `/product-category/lighting/` | 200 | Lighting – Ferm Living Demo |
| `/product-category/accessories/` | 200 | Accessories – Ferm Living Demo |
| `/product-category/kids/` | 200 | Kids – Ferm Living Demo |
| `/product-category/kitchen/` | 200 | Kitchen – Ferm Living Demo |
| `/product-category/outdoor-living/` | 200 | Outdoor Living – Ferm Living Demo |
| `/product-category/textiles/` | 200 | Textiles – Ferm Living Demo |
| `/product-category/green-space/` | 200 | Green Space – Ferm Living Demo |

## Asset Checks

| Check | Result |
|-------|--------|
| CSS files loaded | ✅ 1 stylesheet |
| JS files loaded | ✅ 12 scripts |
| Local images (homepage) | ✅ 86 references |
| Remote refs (homepage) | ✅ 41 fermliving.com URLs |
| Ferm brand mentions | ✅ 12 |

## Business Dependency Check

| Check | Result |
|-------|--------|
| Shopify business API | ✅ 0 (only cosmetic section IDs) |
| Shopify checkout | ✅ 0 |
| Shopify cart API | ✅ 0 |
| Clerk JS loaded | ✅ 0 (data attributes only, no active script) |
| External JS scripts | ✅ 0 (all scripts from localhost) |

## Local Reference Integrity

| Check | Result |
|-------|--------|
| Total referenced files | 312 |
| Missing local files | 15 (all external CDN references, never local) |
| Required local 404s | 0 |

## Missing Files Analysis (15 items — ALL SAFE)

All 15 "missing" files are external CDN references, not local files:

1. `social-share-logo_x1200.jpg` — Full external URL in meta tag
2. `collection-*.jpg` (3 files) — JS data populated by backend at runtime
3. `_medium.png` (8 files) — Full external URLs in data attributes
4. Product gallery images (3 files) — JS data, not local files

**None of these were ever local files in the pack.**

## Plugin Status

| Plugin | Status |
|--------|--------|
| WooCommerce | ✅ Active |
| AUREON | ✅ Active |
| Active Design | ✅ fermliving |
| Demo Mode | ✅ auto |
| Permalinks | ✅ /%postname%/ |

## WooCommerce Pages

| Page | ID | Slug | Status |
|------|----|------|--------|
| Shop | 4 | shop | ✅ |
| Cart | 5 | cart | ✅ |
| Checkout | 6 | checkout | ✅ |
| My Account | 7 | my-account | ✅ |

## Product Categories (8 categories)

| Category | Slug | Status |
|----------|------|--------|
| Furniture | furniture | ✅ |
| Lighting | lighting | ✅ |
| Accessories | accessories | ✅ |
| Kids | kids | ✅ |
| Kitchen | kitchen | ✅ |
| Outdoor Living | outdoor-living | ✅ |
| Textiles | textiles | ✅ |
| Green Space | green-space | ✅ |

## Pack Size

| Metric | Before Cleanup | After Cleanup | Reduction |
|--------|---------------|---------------|-----------|
| Total size | 2.9 GB | 85 MB | 97% |
| Total files | 17,477 | 364 | 98% |
| Local images | 17,399 | 297 | 98% |
| Image size | 2.9 GB | 78 MB | 97% |

## Final Classification

```
FERM_DEMO_PACK_LIGHTWEIGHT_PASS
+ FULL_POST_CLEANUP_REGRESSION_PASS
```

### Remaining
- InfinityFree deployment and verification
- Visual browser testing at 1440/1024/768/390
