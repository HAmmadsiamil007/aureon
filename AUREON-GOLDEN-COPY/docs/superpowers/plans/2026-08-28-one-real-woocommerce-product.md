# One Real WooCommerce Product Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Connect real WooCommerce product #834 (Meridian Lamp Black) to the frozen Ferm product presentation via thin data bridge.

**Architecture:** Frozen Ferm HTML remains the runtime document. PHP injects `window.FermPageData.product` with real WC data. Ferm product.js reads DOM data-attributes (set by the bridge) and updates the existing DOM. No PHP reconstruction, no section splitting, no CSS rewrite.

**Tech Stack:** PHP (WordPress/WooCommerce), JavaScript (Ferm Shopify-compat shims), Frozen HTML/CSS/JS from ferm living.

## Global Constraints

- Frozen Ferm product HTML remains the presentation source of truth
- Do NOT create `section-product.php`
- Do NOT split the product page into sections/components
- Do NOT recreate the Ferm product DOM in PHP
- Do NOT rewrite Ferm CSS
- Do NOT rewrite Ferm presentation JS except where required to replace Shopify/business APIs
- Do NOT modify `adapter-product.php`
- Do NOT modify WooCommerce core
- Do NOT alter unrelated products
- PHP is a data/bridge layer only
- All values come from WooCommerce/AUREON (no hardcoded EUR/price formatting)
- One authoritative product state (WC → FermPageData → DOM)
- If integration requires presentation reconstruction, STOP and return `PRODUCT_INTEGRATION_ARCHITECTURE_BLOCKED`

## File Structure

| File | Action | Responsibility |
|------|--------|---------------|
| `frontend/designs/fermliving/composer.php` | Modify | Add `ferm_build_product_page_data()` — thin mapper from adapter data to FermPageData.product |
| `frontend/designs/fermliving/cdn/shop/t/164/assets/ferm-data-shims.js` | Modify | Extend FermPageData merge to read `.product` and update frozen DOM data-attributes |
| WC product #834 | Modify (via WP admin/WC CLI) | Populate real data: price, SKU, images, description, category |

---

## Task 1: Populate WooCommerce Product #834

**Files:**
- Modify: WC product #834 (via WP-CLI or admin)

**Interfaces:**
- Produces: Real WC product with price, SKU, images, description, category

- [ ] **Step 1: Set product name and SKU**

```bash
docker exec aureon_wp wp post update 834 --post_title="Meridian Lamp Black" --post_name="meridian-lamp-black" --user=1 --allow-root 2>&1

docker exec aureon_wp wp wc product update 834 --user=1 --sku="FL-LAMP-MER-001" --allow-root 2>&1
```

- [ ] **Step 2: Set pricing**

```bash
docker exec aureon_wp wp wc product update 834 --user=1 --regular_price="189.00" --allow-root 2>&1
```

- [ ] **Step 3: Set stock status**

```bash
docker exec aureon_wp wp wc product update 834 --user=1 --manage_stock=false --stock_status="instock" --allow-root 2>&1
```

- [ ] **Step 4: Set product description**

```bash
docker exec aureon_wp wp wc product update 834 --user=1 --description="The Meridian Lamp Black combines sculptural form with warm ambient light. Crafted from powder-coated steel with a matte black finish, it features an adjustable arm and rotating shade for directed illumination. The weighted base ensures stability while the clean lines complement both modern and mid-century interiors." --allow-root 2>&1
```

- [ ] **Step 5: Set product category**

```bash
docker exec aureon_wp wp wc product update 834 --user=1 --categories='[{"id":1}]' --allow-root 2>&1
```

Note: Category ID 1 is "uncategorized". If a "lighting" category exists, use that ID instead. Verify with:
```bash
docker exec aureon_wp wp wc product_cat list --user=1 --allow-root 2>&1
```

- [ ] **Step 6: Verify product data**

```bash
docker exec aureon_wp wp wc product get 834 --user=1 --allow-root 2>&1
```

Expected: name="Meridian Lamp Black", sku="FL-LAMP-MER-001", regular_price="189.00", stock_status="instock"

- [ ] **Step 7: Commit**

No git commit needed — this is a WP database change.

---

## Task 2: Add Product Image to WC #834

**Files:**
- Create: `frontend/designs/fermliving/cdn/shop/files/meridian-lamp-black.webp` (copy from reference)
- Modify: WC product #834 (via WP-CLI)

**Interfaces:**
- Consumes: Reference images from `C:\Users\hamma\Downloads\SiteOne-Crawler\fermliving-template-ready\cdn\shop\files\`
- Produces: WC product with featured image set

- [ ] **Step 1: Find matching Meridian Lamp images in reference**

```powershell
Get-ChildItem "C:\Users\hamma\Downloads\SiteOne-Crawler\fermliving-template-ready\cdn\shop\files\" -Filter "*eridian*" | Select-Object Name, Length
```

- [ ] **Step 2: Copy matching images to Ferm pack**

Copy the main Meridian Lamp image to the pack's cdn/shop/files directory:
```powershell
Copy-Item "C:\Users\hamma\Downloads\SiteOne-Crawler\fermliving-template-ready\cdn\shop\files\<matched-file>" "C:\Users\hamma\Downloads\phantom\wordpress\frontend\designs\fermliving\cdn\shop\files\meridian-lamp-black.webp"
```

- [ ] **Step 3: Copy image to Docker container**

```powershell
docker cp "C:\Users\hamma\Downloads\phantom\wordpress\frontend\designs\fermliving\cdn\shop\files\meridian-lamp-black.webp" aureon_wp:/var/www/html/wp-content/frontend/designs/fermliving/cdn/shop/files/meridian-lamp-black.webp
```

- [ ] **Step 4: Import image to WordPress media library**

```bash
docker exec aureon_wp wp media import /var/www/html/wp-content/frontend/designs/fermliving/cdn/shop/files/meridian-lamp-black.webp --title="Meridian Lamp Black" --user=1 --allow-root 2>&1
```

Note the returned attachment ID from this command.

- [ ] **Step 5: Set featured image on product #834**

Replace `ATTACHMENT_ID` with the ID from step 4:
```bash
docker exec aureon_wp wp post update 834 --post_thumbnail=ATTACHMENT_ID --user=1 --allow-root 2>&1
```

- [ ] **Step 6: Verify featured image**

```bash
docker exec aureon_wp wp post meta get 834 _thumbnail_id --user=1 --allow-root 2>&1
```

- [ ] **Step 7: Commit**

No git commit needed — this is a WP database + media change.

---

## Task 3: Trace product.js Data Source

**Files:**
- Read: `frontend/designs/fermliving/cdn/shop/t/164/assets/product.fa97565a5f.js`
- Read: `frontend/designs/fermliving/cdn/shop/t/164/assets/ferm-data-shims.js`

**Interfaces:**
- Produces: Confirmed data source for product.js (Shopify.product vs FermPageData vs DOM)

**Purpose:** Before writing any bridge code, verify exactly how product.js reads product data. The spec requires tracing the actual data source first.

- [ ] **Step 1: Check if product.js reads from Shopify.product**

Search the minified product.js for `Shopify` references. If product.js accesses `Shopify.product` or `Shopify.meta.product`, the shims can feed data through the Shopify compatibility layer.

```powershell
$content = Get-Content "C:\Users\hamma\Downloads\phantom\wordpress\frontend\designs\fermliving\cdn\shop\t\164\assets\product.fa97565a5f.js" -Raw
$shopifyRefs = [regex]::Matches($content, 'Shopify\.\w+')
Write-Output "Shopify references: $($shopifyRefs.Count)"
$shopifyRefs | ForEach-Object { Write-Output "  $($_.Value)" }
```

- [ ] **Step 2: Check if product.js reads from DOM data-attributes**

```powershell
$dataset = [regex]::Matches($content, '\.dataset\.\w+')
Write-Output "dataset access:"
$dataset | ForEach-Object { Write-Object "  $($_.Value)" } | Select-Object -Unique
```

- [ ] **Step 3: Check if product.js reads from FermPageData**

```powershell
$fermRefs = [regex]::Matches($content, 'FermPageData')
Write-Output "FermPageData references: $($fermRefs.Count)"
```

- [ ] **Step 4: Check DOM data-attributes in frozen HTML**

```powershell
$html = Get-Content "C:\Users\hamma\Downloads\phantom\wordpress\frontend\designs\fermliving\products\meridian-lamp-black.html" -Raw
$dataAttrs = [regex]::Matches($html, 'data-[\w-]+="[^"]*"')
Write-Output "Data attributes in frozen HTML:"
$dataAttrs | ForEach-Object { $name = [regex]::Match($_.Value, '^data-[\w-]+').Value; $name } | Sort-Object -Unique
```

- [ ] **Step 5: Document findings**

Record which data source product.js actually uses. This determines the bridge approach:
- If Shopify.product → feed data through Shopify shim
- If DOM data-attributes → set attributes in frozen HTML via bridge JS
- If FermPageData → inject directly

- [ ] **Step 6: Commit**

No code changes — analysis only.

---

## Task 4: Extend composer.php — Add `ferm_build_product_page_data()`

**Files:**
- Modify: `frontend/designs/fermliving/composer.php` (add new function + hook)
- Modify: `frontend/designs/fermliving/composer.php` (copy to Docker container)

**Interfaces:**
- Consumes: `aether_adapter_product_data` filter output (from `adapter-product.php`)
- Produces: `window.FermPageData.product` object injected via `wp_localize_script`

- [ ] **Step 1: Read current composer.php**

Read `frontend/designs/fermliving/composer.php` to find the existing `ferm_product_data()` function and the `ferm_build_page_data()` function. Identify where to add the new function.

- [ ] **Step 2: Add `ferm_build_product_page_data()` function**

Add this function after the existing `ferm_remap_product()` function:

```php
/**
 * Build FermPageData.product from adapter data for single product pages.
 *
 * Injects real WooCommerce product data into the Ferm JS context
 * so the frozen product DOM can display live data.
 *
 * @param int $product_id WC product ID.
 * @return array Product data in Ferm-compatible schema.
 */
function ferm_build_product_page_data( $product_id ) {
    $product = wc_get_product( $product_id );
    if ( ! $product ) {
        return array();
    }

    // Gallery images.
    $gallery = array();
    $image_id = $product->get_image_id();
    if ( $image_id ) {
        $gallery[] = array(
            'src' => wp_get_attachment_url( $image_id ),
            'alt' => $product->get_name(),
        );
    }
    foreach ( $product->get_gallery_image_ids() as $gid ) {
        $gallery[] = array(
            'src' => wp_get_attachment_url( $gid ),
            'alt' => $product->get_name(),
        );
    }

    // If no gallery images, use placeholder.
    if ( empty( $gallery ) ) {
        $pack_dir = aether_active_design_dir();
        $placeholder = $pack_dir . 'cdn/shop/files/meridian-lamp-black.webp';
        if ( file_exists( $placeholder ) ) {
            $gallery[] = array(
                'src' => content_url( 'frontend/designs/fermliving/cdn/shop/files/meridian-lamp-black.webp' ),
                'alt' => $product->get_name(),
            );
        }
    }

    // Price in cents (Ferm Shopify format).
    $price_cents = 0;
    if ( $product->get_price() ) {
        $price_cents = (int) round( (float) $product->get_price() * 100 );
    }

    // Availability.
    $availability = 'out-of-stock';
    if ( $product->is_in_stock() ) {
        $availability = $product->managing_stock() && $product->get_stock_quantity() <= 5
            ? 'low-stock'
            : 'in-stock';
    }

    return array(
        'id'              => $product->get_id(),
        'variant_id'      => null, // Simple product.
        'title'           => $product->get_name(),
        'handle'          => $product->get_slug(),
        'slug'            => $product->get_slug(),
        'url'             => $product->get_permalink(),
        'sku'             => $product->get_sku(),
        'price'           => $price_cents,
        'price_html'      => $product->get_price_html(),
        'compare_at_price' => $product->get_sale_price() ? (int) round( (float) $product->get_regular_price() * 100 ) : null,
        'currency'        => get_woocommerce_currency(),
        'availability'    => $availability,
        'description'     => $product->get_short_description() ?: $product->get_description(),
        'gallery'         => $gallery,
        'options'         => array(),
        'variants'        => array(),
        'badge'           => null,
        'product_type'    => $product->get_type(),
        'tags'            => wp_get_post_terms( $product->get_id(), 'product_tag', array( 'fields' => 'names' ) ),
    );
}
```

- [ ] **Step 3: Hook into aether_adapter_product_data to inject into FermPageData**

Find the existing `ferm_product_data()` function and add product data injection. The key is to store the product data so it can be included in the `FermPageData` localization.

Add this near the existing `ferm_enqueue_cart_bridge()` function:

```php
// Store product data for FermPageData injection on single product pages.
add_action( 'wp', 'ferm_store_product_page_data' );
function ferm_store_product_page_data() {
    if ( ! is_product() ) {
        return;
    }
    global $post;
    if ( ! $post ) {
        return;
    }
    // Store for later use by ferm_build_page_data.
    $GLOBALS['ferm_product_page_data'] = ferm_build_product_page_data( $post->ID );
}
```

- [ ] **Step 4: Inject product data into FermPageData localization**

Find where `wp_localize_script( ..., 'FermPageData', ... )` is called and add the product data:

```php
// In the ferm_build_page_data function, add product data if available.
$page_data = array( /* existing cart/customer/shop/navigation/config */ );

// Inject product data on single product pages.
if ( ! empty( $GLOBALS['ferm_product_page_data'] ) ) {
    $page_data['product'] = $GLOBALS['ferm_product_page_data'];
}
```

- [ ] **Step 5: Copy modified composer.php to Docker container**

```powershell
docker cp "C:\Users\hamma\Downloads\phantom\wordpress\frontend\designs\fermliving\composer.php" aureon_wp:/var/www/html/wp-content/frontend/designs/fermliving/composer.php
```

- [ ] **Step 6: Verify PHP syntax**

```bash
docker exec aureon_wp php -l /var/www/html/wp-content/frontend/designs/fermliving/composer.php 2>&1
```

Expected: No syntax errors

- [ ] **Step 7: Verify FermPageData injection**

```bash
docker exec aureon_wp wp eval 'echo json_encode(ferm_build_product_page_data(834));' --allow-root 2>&1
```

Expected: JSON object with product #834 data (title, price, sku, gallery, etc.)

- [ ] **Step 8: Commit**

```bash
git add frontend/designs/fermliving/composer.php
git commit -m "feat: inject FermPageData.product for single product pages"
```

---

## Task 5: Extend ferm-data-shims.js — DOM Update Bridge

**Files:**
- Modify: `frontend/designs/fermliving/cdn/shop/t/164/assets/ferm-data-shims.js` (extend FermPageData merge block)
- Modify: Docker container copy

**Interfaces:**
- Consumes: `window.FermPageData.product` (from Task 4)
- Produces: Updated frozen DOM data-attributes for product.js consumption

**Purpose:** Bridge the gap between FermPageData.product and the frozen DOM data-attributes that product.js reads.

- [ ] **Step 1: Read current ferm-data-shims.js**

Read `frontend/designs/fermliving/cdn/shop/t/164/assets/ferm-data-shims.js` to find the FermPageData merge block (near the end, around line 436).

- [ ] **Step 2: Add product DOM update bridge**

After the existing FermPageData merge block, add a product bridge section:

```javascript
// ============================================================
// PRODUCT DOM BRIDGE
// Updates frozen Ferm product DOM with real WooCommerce data
// from FermPageData.product.
// ============================================================
(function() {
  var pd = window.FermPageData;
  if (!pd || !pd.product) return;

  var product = pd.product;
  var root = document.documentElement;

  // Update body data attributes.
  if (pd.config && pd.config.template) {
    root.setAttribute('data-template', pd.config.template);
  }
  if (pd.shop && pd.shop.money_format) {
    root.setAttribute('data-money-format', pd.shop.money_format);
  }

  // Find the product section.
  var productSection = document.querySelector('[data-section-type="product"]') ||
                       document.querySelector('[data-component="productPage"]');
  if (!productSection) return; // Not a product page.

  // Update product ID.
  var idEl = productSection.querySelector('[data-product-id]');
  if (idEl) idEl.setAttribute('data-product-id', product.id);

  // Update variant ID.
  var variantEl = productSection.querySelector('[data-variant-id]');
  if (variantEl) {
    var variantId = product.variant_id || product.id;
    variantEl.setAttribute('data-variant-id', variantId);
  }

  // Update SKU.
  var skuEl = productSection.querySelector('[data-sku]');
  if (skuEl) {
    skuEl.textContent = product.sku || '';
    skuEl.setAttribute('data-sku', product.sku || '');
  }

  // Update product title.
  var titleEl = productSection.querySelector('[data-product-title]');
  if (titleEl) {
    titleEl.textContent = product.title;
    titleEl.setAttribute('data-product-title', product.title);
  }

  // Update price display.
  var priceEl = productSection.querySelector('[data-product-price]');
  if (priceEl && product.price_html) {
    priceEl.innerHTML = product.price_html;
  }

  // Update compare/sale price.
  var compareEl = productSection.querySelector('[data-compare-price]');
  if (compareEl) {
    if (product.compare_at_price && product.compare_at_price > product.price) {
      var fmt = window.Shopify && Shopify.formatMoney
        ? Shopify.formatMoney(product.compare_at_price, Shopify.money_format)
        : (product.compare_at_price / 100).toFixed(2);
      compareEl.textContent = fmt;
      compareEl.style.display = '';
    } else {
      compareEl.style.display = 'none';
    }
  }

  // Update gallery images.
  if (product.gallery && product.gallery.length > 0) {
    var mainImg = productSection.querySelector('[data-featured-image-container] img') ||
                  productSection.querySelector('.product-gallery img');
    if (mainImg && product.gallery[0].src) {
      mainImg.src = product.gallery[0].src;
      mainImg.alt = product.gallery[0].alt || product.title;
    }

    // Update variant image mapping.
    var mediaEl = productSection.querySelector('[data-media]');
    if (mediaEl) {
      var mediaData = product.gallery.map(function(img) {
        return { src: img.src, alt: img.alt || product.title };
      });
      mediaEl.setAttribute('data-media', JSON.stringify(mediaData));
    }
  }

  // Update availability/CTA state.
  var ctaState = productSection.querySelector('[data-cta-state]');
  if (ctaState) {
    if (product.availability === 'in-stock' || product.availability === 'low-stock') {
      ctaState.setAttribute('data-cta-state', 'add');
    } else {
      ctaState.setAttribute('data-cta-state', 'sold-out');
    }
  }

  // Update store name.
  var storeEl = productSection.querySelector('[data-store-name]');
  if (storeEl && pd.shop) {
    storeEl.setAttribute('data-store-name', pd.shop.name || '');
  }

  // Update product title text.
  var headingEl = productSection.querySelector('h1') ||
                  productSection.querySelector('[data-product-title]');
  if (headingEl && product.title) {
    headingEl.textContent = product.title;
  }
})();
```

- [ ] **Step 3: Copy modified shims to Docker container**

```powershell
docker cp "C:\Users\hamma\Downloads\phantom\wordpress\frontend\designs\fermliving\cdn\shop\t\164\assets\ferm-data-shims.js" aureon_wp:/var/www/html/wp-content/frontend/designs/fermliving/cdn/shop/t/164/assets/ferm-data-shims.js
```

- [ ] **Step 4: Verify no syntax errors**

Open the browser console and reload. Check for JS syntax errors in ferm-data-shims.js.

- [ ] **Step 5: Commit**

```bash
git add frontend/designs/fermliving/cdn/shop/t/164/assets/ferm-data-shims.js
git commit -m "feat: add product DOM bridge for FermPageData.product"
```

---

## Task 6: Verify FermPageData Injection

**Files:**
- No file changes

**Interfaces:**
- Consumes: Tasks 4 and 5 outputs
- Produces: Verified FermPageData.product in browser

- [ ] **Step 1: Navigate to product page**

Navigate browser to `http://localhost:8080/product/meridian-lamp-black/`

- [ ] **Step 2: Check FermPageData.product in console**

```javascript
JSON.stringify(window.FermPageData.product, null, 2)
```

Expected: Object with real WC data (id: 834, title: "Meridian Lamp Black", sku: "FL-LAMP-MER-001", price: 18900, etc.)

- [ ] **Step 3: Check FermPageData.config**

```javascript
JSON.stringify(window.FermPageData.config, null, 2)
```

Expected: `template: "product"`, real ajax_url, real nonce

- [ ] **Step 4: Check DOM data-attributes**

```javascript
var v = document.querySelector('[data-variant-id]');
console.log('variant-id:', v ? v.getAttribute('data-variant-id') : 'NOT FOUND');

var s = document.querySelector('[data-sku]');
console.log('sku:', s ? s.textContent : 'NOT FOUND');
```

- [ ] **Step 5: Verify no Shopify API calls**

Open Network tab, filter by "shopify". Reload page. Expected: 0 requests.

---

## Task 7: Visual Verification — Product Page

**Files:**
- No file changes
- Screenshots saved to `screenshots/`

**Interfaces:**
- Consumes: Tasks 1-6 outputs
- Produces: Visual proof of correct product rendering

- [ ] **Step 1: Navigate to product page**

`http://localhost:8080/product/meridian-lamp-black/`

- [ ] **Step 2: Check title**

Browser snapshot: product title should show "Meridian Lamp Black"

- [ ] **Step 3: Check price**

Browser snapshot: price should show formatted WC price (not hardcoded)

- [ ] **Step 4: Check SKU**

Browser snapshot: SKU should show "FL-LAMP-MER-001"

- [ ] **Step 5: Check product image**

Browser snapshot: product image should be visible (from local pack)

- [ ] **Step 6: Capture 1440px screenshot**

```javascript
await page.setViewportSize({ width: 1440, height: 900 });
await page.screenshot({ path: 'screenshots/product-1440.png', scale: 'css' });
```

- [ ] **Step 7: Capture 390px screenshot**

```javascript
await page.setViewportSize({ width: 390, height: 844 });
await page.screenshot({ path: 'screenshots/product-390.png', scale: 'css' });
```

- [ ] **Step 8: Compare with standalone Ferm product template**

Open `C:\Users\hamma\Downloads\SiteOne-Crawler\fermliving-template-ready\products\meridian-lamp-black.html` in a separate browser tab. Compare layout, typography, spacing, gallery structure.

---

## Task 8: Test Add-to-Cart

**Files:**
- No file changes

**Interfaces:**
- Consumes: Tasks 1-7 outputs
- Produces: Verified WC cart integration

- [ ] **Step 1: Open Network tab**

Filter by "ajax" or "cart" in browser dev tools.

- [ ] **Step 2: Click Add to Cart button**

Click the CTA button on the product page.

- [ ] **Step 3: Verify WC AJAX request**

Network tab should show a request to `admin-ajax.php` with action `ferm_cart_add`.

- [ ] **Step 4: Verify request payload**

Request body should contain:
- `action`: `ferm_cart_add`
- `product_id`: `834` (or the correct WC product ID)
- `quantity`: `1`

- [ ] **Step 5: Verify response**

Response should be JSON with:
- `item_count`: 1
- `items`: array with one item containing product #834 data

- [ ] **Step 6: Verify cart count update**

Header cart count should update to 1 (or increment by 1).

- [ ] **Step 7: Verify zero Shopify calls**

Network tab: filter by "shopify". Expected: 0 requests.

- [ ] **Step 8: Verify zero console errors**

Console: expected 0 errors.

---

## Task 9: Final Network and Console Verification

**Files:**
- No file changes

**Interfaces:**
- Consumes: All previous tasks
- Produces: Clean network/console state

- [ ] **Step 1: Full page reload with Network tab open**

Navigate to `http://localhost:8080/product/meridian-lamp-black/`

- [ ] **Step 2: Count all network requests**

Total requests: ___ (record number)

- [ ] **Step 3: Filter for 404s**

Expected: 0

- [ ] **Step 4: Filter for Shopify/Clerk/external API**

Expected: 0

- [ ] **Step 5: Filter for console errors**

Expected: 0

- [ ] **Step 6: Filter for console warnings**

Expected: minimal (pre-existing warnings acceptable)

- [ ] **Step 7: Capture console messages**

```javascript
// In browser console
performance.getEntriesByType('resource').filter(r => r.name.includes('shopify') || r.name.includes('clerk')).length
```

Expected: 0

---

## Task 10: Report Results

**Files:**
- No code changes

**Interfaces:**
- Produces: Final integration report

- [ ] **Step 1: Collect all verification data**

Record:
- Product ID
- FermPageData.product snapshot
- Route (URL)
- Screenshot paths
- Network results (total requests, 404s, external calls)
- Console results (errors, warnings)
- Cart result (add-to-cart test)

- [ ] **Step 2: Determine pass/block status**

If all verification checkpoints pass:
```
PRODUCT_DYNAMIC_INTEGRATION_PASS
```

If architecture reconstruction was required:
```
PRODUCT_INTEGRATION_ARCHITECTURE_BLOCKED
```

- [ ] **Step 3: Stop**

Do NOT proceed to other products, collections, cart, search, or account.
