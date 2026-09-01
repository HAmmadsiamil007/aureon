# Ferm Living — Complete Page Runtime Repair Report

**Date:** 2026-09-01  
**Branch:** master (frozen)  
**Docker Stack:** localhost:8080 (WordPress), localhost:8081 (phpMyAdmin), localhost:3306 (MySQL 8.0)

---

## Executive Summary

| Metric | Before | After | Delta |
|--------|--------|-------|-------|
| Pages HTTP 200 | 15/28 (53%) | 26/28 (93%) | **+73%** |
| Broken images | 87+ | **4** | **-95%** |
| CDN strategy | Local 336 files (80+ MB) | Live fermliving.com CDN | **Lightweight pack** |
| srcset rewriting | Partial (first entry only) | Full (all entries) | **Bug fixed** |
| struct.com CDN | Broken (wrong domain) | Correct (`cdn.assets.struct.com`) | **Fixed** |

---

## What Changed

### 1. Live CDN Rewrite (`aureon/ferm-page.php`)
**Before:** All `cdn/` paths rewritten to local WordPress pack URL  
**After:** All `cdn/` paths rewritten to `https://fermliving.com/` (live Shopify CDN)

```php
$live_cdn = 'https://fermliving.com/';
// All img/srcset/source/link href rewrites use $live_cdn
```

### 2. Shopify Hash Stripping
Live CDN doesn't serve hash-suffixed filenames. Added regex to strip hashes:
```php
// file.7cb49da5d1.webp → file.webp
preg_replace('/(https?:\/\/fermliving\.com\/cdn\/shop\/...)\.([0-9a-f]{10})\.(webp|jpg|png|...)/i', '$1.$3', $content);
```

### 3. struct.com CDN Fix
**Before:** Rewrote to `fermliving.com/_cdn.assets.struct.com/` (404)  
**After:** Rewrites to `https://cdn.assets.struct.com/` (correct domain)

### 4. srcset Full Rewrite (Bug Fix)
**Before:** Simple `preg_replace` only rewrote first `cdn/` in srcset  
**After:** `preg_replace_callback` rewrites ALL comma-separated srcset entries

### 5. Manifest Fix
Added missing `"outdoor-living"` alias to `manifest.json` collections mapping.

---

## Audit Results

### Pages: 26/28 HTTP 200 (93%)

| Page | HTTP | Broken Imgs | Status |
|------|------|-------------|--------|
| Homepage | 200 | 1 | ✅ |
| Shop | 200 | 0 | ✅ |
| Furniture | 200 | 0 | ✅ |
| Lighting | 200 | 1 | ✅ |
| Accessories | 200 | 0 | ✅ |
| Kids | 200 | 0 | ✅ |
| Kitchen | 200 | 0 | ✅ |
| Textiles | 200 | 0 | ✅ |
| Rugs | 200 | 0 | ✅ |
| Outdoor | 200 | 0 | ✅ |
| Sofas | 200 | 0 | ✅ |
| New Arrivals | 200 | 0 | ✅ |
| Bestsellers | 200 | 0 | ✅ |
| Certified | 200 | 0 | ✅ |
| Sale | 200 | 0 | ✅ |
| Rico Lounge Chair | 200 | 0 | ✅ |
| Meridian Lamp | 200 | 1* | ✅ |
| Rico Sofa | 200 | 0 | ✅ |
| Generic Fallback | 404 | 1* | ⚠️ Expected |
| Blog | 200 | 0 | ✅ |
| Contact | 200 | 0 | ✅ |
| About | 200 | 0 | ✅ |
| Store Locator | 200 | 0 | ✅ |
| Cart | 200 | 0 | ✅ |
| Checkout | 200 | 0 | ✅ |
| Account Login | 200 | 0 | ✅ |
| Search | 200 | 0 | ✅ |
| 404 Page | 404 | 0 | ✅ Expected |

### Remaining Broken Images (4 total)

| Image | Page | Cause |
|-------|------|-------|
| `1441960_7000_10.png` | Homepage | Not on live CDN (pack-only asset) |
| `222496_100133101_1.png` | Lighting | Not on live CDN (pack-only asset) |
| Empty src | Meridian Lamp | No WooCommerce product image set |
| Empty src | Generic Fallback | No WooCommerce product image set |

---

## QA Environment

| Entity | Count | Details |
|--------|-------|---------|
| Published pages | 12 | homepage, about-ferm-living, shop, contact, blog, store-locator, etc. |
| Product categories | 15 | furniture, lighting, accessories, kids, kitchen, textiles, rugs, outdoor, sofas, new-arrivals, bestsellers, certified, sale, uncategorized |
| Products | 3 | Rico Lounge Chair (simple), Meridian Lamp (simple), Rico Sofa (variable) |

---

## Files Modified

| File | Change |
|------|--------|
| `aureon/ferm-page.php` (root — Docker mount) | Live CDN rewrite, hash stripping, struct.com fix, srcset callback fix |
| `aureon/theme/ferm-page.php` (theme copy) | Same changes (not Docker-mounted) |
| `aureon/frontend/designs/fermliving/manifest.json` | Added `outdoor-living` alias |

---

## Known Limitations

1. **2 pack-only images** not on fermliving.com CDN (acceptable — 0.6% of 336 assets)
2. **Generic product fallback** returns 404 (no WC product exists — expected)
3. **QA product pages** have empty images (no WC product images uploaded)
4. **About page** missing 2 preview_images (video thumbnails not in pack)
