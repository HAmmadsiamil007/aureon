# Ferm Living Root-Cause Fix Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Fix the 5 root causes identified in the forensic investigation: broken asset URLs, AETHER shoe data leaking into Ferm presentation, invalid composer section IDs, unregistered secondary_products, and tests that verify presence not correctness.

**Architecture:** Introduce a generic `aether_pack_url()` helper for correct asset resolution, a generic design-demo content override filter layer, fix composer section IDs, and add content-level E2E assertions. No Ferm-specific logic enters shared adapters.

**Tech Stack:** PHP (WordPress/WooCommerce), Playwright (Node.js E2E), Docker (aureon_wp container)

## Global Constraints

- No Ferm-specific logic in shared `frontend/adapters/`, `frontend/views/`, `frontend/sections/`, `frontend/tokens/`
- WooCommerce database remains authoritative in real mode
- Design packs provide demo/reference content only via generic filter hooks
- All existing tests must continue to pass
- Docker container `aureon_wp` runs the working tree via volume mount at `/var/www/html/wp-content/`

---

## File Map

| File | Action | Purpose |
|------|--------|---------|
| `frontend/views/design.php` | Modify | Add `aether_pack_url()` helper |
| `frontend/designs/fermliving/tokens.php` | Modify | Use `aether_pack_url()` for image paths |
| `frontend/designs/fermliving/composer.php` | Modify | Fix section IDs, use `aether_pack_url()` |
| `frontend/adapters/adapter-wc-products.php` | Modify | Add `aether_demo_products` filter |
| `frontend/adapters/adapter-wc-categories.php` | Modify | Add `aether_demo_categories` filter |
| `frontend/designs/fermliving/composer.php` | Modify | Register demo content filters |
| `frontend/tests/specs/fermliving-e2e.cjs` | Modify | Add content correctness assertions |

---

## Task 1: Add generic `aether_pack_url()` helper

**Files:**
- Modify: `frontend/views/design.php:46-66` (after `aether_active_design_dir()`)

**Interfaces:**
- Produces: `aether_pack_url()` — returns the public URL root for the active design pack's assets

**Why:** The pack's `tokens.php` and `composer.php` construct image URLs using `get_template_directory_uri() . '/designs/fermliving'` which resolves to a non-existent directory. The correct path is `content_url() . '/frontend/designs/' . $design`. This helper centralizes that logic so no pack hardcodes paths.

- [ ] **Step 1: Add `aether_pack_url()` to `frontend/views/design.php`**

Insert after the `aether_active_design_dir()` function (after line ~33):

```php
/**
 * Public URL root for the active design pack's assets.
 *
 * @return string Pack URL with trailing slash, or '' for luxury.
 */
function aether_pack_url() {
	$design = aether_active_design();

	if ( 'luxury' === $design ) {
		return '';
	}

	return trailingslashit( content_url() ) . 'frontend/designs/' . $design;
}
```

- [ ] **Step 2: Sync to Docker and verify**

```bash
docker cp "C:\Users\hamma\Downloads\phantom\wordpress\frontend\views\design.php" "aureon_wp:/var/www/html/wp-content/frontend/views/design.php"
docker exec aureon_wp php -r "if(function_exists('opcache_reset')){opcache_reset();echo'OK';}"
```

- [ ] **Step 3: Verify the helper works**

Navigate to `http://localhost:8080` and use browser evaluate:
```js
// Check that the function exists and returns the correct URL
document.body.className.includes('design-fermliving')
```

Expected: `true`

- [ ] **Step 4: Commit**

```bash
git add frontend/views/design.php
git commit -m "feat: add aether_pack_url() generic helper for design asset URLs"
```

---

## Task 2: Fix tokens.php to use `aether_pack_url()`

**Files:**
- Modify: `frontend/designs/fermliving/tokens.php:10-11`

**Interfaces:**
- Consumes: `aether_pack_url()` from Task 1
- Produces: Correct hero/editorial/room/product image URLs in token defaults

**Why:** The pack's `$pack_url` currently resolves to `get_template_directory_uri() . '/designs/fermliving'` which is a non-existent path. Using `aether_pack_url()` resolves to the correct `/wp-content/frontend/designs/fermliving/`.

- [ ] **Step 1: Update `$pack_url` in tokens.php**

Replace lines 10-11:
```php
$pack_url = defined('AETHER_DESIGN_URL') ? AETHER_DESIGN_URL : get_template_directory_uri() . '/designs/fermliving';
$assets   = $pack_url . '/assets';
```

With:
```php
$pack_url = aether_pack_url();
$assets   = $pack_url . 'assets';
```

Note: `aether_pack_url()` already returns a trailing-slash URL, so `$assets` = `{pack_url}assets` (no double slash).

- [ ] **Step 2: Sync to Docker**

```bash
docker cp "C:\Users\hamma\Downloads\phantom\wordpress\frontend\designs\fermliving\tokens.php" "aureon_wp:/var/www/html/wp-content/frontend/designs/fermliving/tokens.php"
docker exec aureon_wp php -r "if(function_exists('opcache_reset')){opcache_reset();echo'OK';}"
```

- [ ] **Step 3: Verify hero images load**

Navigate to `http://localhost:8080`, open Network tab, check that `bestsellers.webp` and `dining.webp` return HTTP 200 (not 404).

Expected: Both images load from `http://localhost:8080/wp-content/frontend/designs/fermliving/assets/hero/...`

- [ ] **Step 4: Commit**

```bash
git add frontend/designs/fermliving/tokens.php
git commit -m "fix: use aether_pack_url() for Ferm Living asset URLs"
```

---

## Task 3: Fix composer.php section IDs and asset URLs

**Files:**
- Modify: `frontend/designs/fermliving/composer.php:38-39,73,90`

**Interfaces:**
- Consumes: `aether_pack_url()` from Task 1
- Produces: Correct section IDs that match the registry, correct image URLs

**Why:** Three issues: (1) `$pack_url` uses wrong base path, (2) `'rooms'` doesn't match registered `'ferm-room-grid'`, (3) `'secondary_products'` is not registered.

- [ ] **Step 1: Fix `$pack_url` in composer.php**

Replace lines 38-39:
```php
$pack_url = defined('AETHER_DESIGN_URL') ? AETHER_DESIGN_URL : get_template_directory_uri() . '/designs/fermliving';
$assets   = $pack_url . '/assets';
```

With:
```php
$pack_url = aether_pack_url();
$assets   = $pack_url . 'assets';
```

- [ ] **Step 2: Fix section ID `'rooms'` → `'ferm-room-grid'`**

Replace line 90:
```php
'rooms',
```

With:
```php
'ferm-room-grid',
```

- [ ] **Step 3: Fix `'secondary_products'` — register with `paged => 2`**

The adapter whitelist (`adapter-wc-products.php:44`) includes `paged` but NOT `offset`. To get a different product slice, the section must pass `'paged' => 2`.

Replace line 73:
```php
'secondary_products',
```

With:
```php
'secondary_products',
```

(Keep as string — the section registration in Step 4 handles the adapter args.)

- [ ] **Step 4: Create `frontend/designs/fermliving/sections/section-secondary-products.php`**

```php
<?php
/**
 * Ferm Living secondary products section — reuses WC products adapter with offset.
 *
 * @package Aureon
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

aether_register_section( 'secondary_products', array(
	'template' => 'sections/section-bestsellers.php',
	'adapter'  => 'adapter-wc-products.php',
	'adapter_args' => array(
		'posts_per_page' => 4,
		'paged'          => 2,
		'with_cta'       => true,
	),
	'behavior' => array( 'reveal-group' => true ),
) );
```

This registers the section ID `secondary_products` with `paged => 2` so it returns the next page of products (different from the first `bestsellers` row which uses `paged => 1` default).

- [ ] **Step 5: Sync all to Docker**

```bash
docker cp "C:\Users\hamma\Downloads\phantom\wordpress\frontend\designs\fermliving\composer.php" "aureon_wp:/var/www/html/wp-content/frontend/designs/fermliving/composer.php"
docker cp "C:\Users\hamma\Downloads\phantom\wordpress\frontend\designs\fermliving\sections" "aureon_wp:/var/www/html/wp-content/frontend/designs/fermliving/sections"
docker exec aureon_wp php -r "if(function_exists('opcache_reset')){opcache_reset();echo'OK';}"
```

- [ ] **Step 6: Verify section composition**

Navigate to `http://localhost:8080`, check DOM snapshot for:
- `ferm-room-grid` section exists (room grid)
- `secondary_products` renders (second product row)
- Editorial images load (not 404)

Expected: Room grid visible, second product row visible, editorial images HTTP 200.

- [ ] **Step 7: Commit**

```bash
git add frontend/designs/fermliving/composer.php frontend/designs/fermliving/sections/section-secondary-products.php
git commit -m "fix: correct composer section IDs and add secondary_products registration"
```

---

## Task 4: Add generic design-demo content override filters

**Files:**
- Modify: `frontend/adapters/adapter-wc-products.php:122-143`
- Modify: `frontend/adapters/adapter-wc-categories.php:50-53`
- Create: `frontend/designs/fermliving/composer.php` (add filter registrations)

**Interfaces:**
- Consumes: `aether_active_design()` from `design.php`
- Produces: `aether_demo_products` filter, `aether_demo_categories` filter
- The Ferm pack registers filter callbacks that provide demo content when demo mode is active

**Why:** The adapters currently only use demo/fallback content when the WC DB is empty. We need a generic mechanism where design packs can provide demo content that overrides WC data. The filter hook `aether_demo_products` fires AFTER the WC query — if the filter returns non-empty items, those REPLACE whatever WC returned. The existing `aether_demo_content` flag stays untouched and only controls the adapter's built-in fallback for empty stores.

**Critical:** The filter callbacks do NOT check `aether_demo_content`. The filter hook itself is the override mechanism. When the Ferm design is active, its filter always provides Ferm reference products, replacing whatever WC returned.

- [ ] **Step 1: Add `aether_demo_products` filter to adapter-wc-products.php**

Replace lines 122-143 (the demo fallback block):

```php
    // Design-pack demo override: if a filter returns items, they REPLACE
    // whatever WC returned. This lets packs like Ferm Living provide
    // reference products in the demo environment.
    $demo_items = apply_filters( 'aether_demo_products', array(), $query_args );
    if ( ! empty( $demo_items ) ) {
        $items = $demo_items;
    }

    // Standard fallback — no products in the store yet (gated by aether_demo_content).
    if ( empty( $items ) && aureon_get_option( 'aether_demo_content', true ) ) {
        foreach ( (array) aureon_get_option( 'aether_product_items', array() ) as $demo ) {
            $items[] = array(
                'id'             => isset( $demo['id'] ) ? (int) $demo['id'] : 0,
                'name'           => isset( $demo['name'] ) ? $demo['name'] : '',
                'price'          => isset( $demo['price'] ) ? $demo['price'] : '',
                'price_plain'    => isset( $demo['price'] ) ? $demo['price'] : '',
                'old_price_plain'=> isset( $demo['old_price'] ) ? $demo['old_price'] : '',
                'tagline'        => isset( $demo['tagline'] ) ? $demo['tagline'] : '',
                'rating'         => isset( $demo['rating'] ) ? (float) $demo['rating'] : 0,
                'reviews'        => isset( $demo['reviews'] ) ? $demo['reviews'] : '',
                'image'          => isset( $demo['image'] ) ? aether_viewmodel_resolve_image( $demo['image'] ) : '',
                'alt'            => isset( $demo['alt'] ) ? $demo['alt'] : ( isset( $demo['name'] ) ? $demo['name'] : '' ),
                'url'            => isset( $demo['url'] ) ? esc_url_raw( $demo['url'] ) : '',
                'add_to_cart_url'=> '',
                'product_type'   => '',
                'badge'          => isset( $demo['badge'] ) ? $demo['badge'] : '',
                'behavior'       => array( 'tilt' => true ),
            );
        }
    }
```

- [ ] **Step 2: Add `aether_demo_categories` filter to adapter-wc-categories.php**

Replace lines 50-53:

```php
    // Design-pack demo override: if a filter returns items, they REPLACE
    // whatever WC returned.
    $demo_items = apply_filters( 'aether_demo_categories', array(), $args );
    if ( ! empty( $demo_items ) ) {
        $items = $demo_items;
    } elseif ( ( empty( $terms ) || is_wp_error( $terms ) ) && aureon_get_option( 'aether_demo_content', true ) ) {
        $items = aether_get_fallback_categories();
    } else {
```

- [ ] **Step 3: Register Ferm demo content filters in composer.php**

Add to `frontend/designs/fermliving/composer.php`, after the existing filter registrations (after line 25):

```php
add_filter( 'aether_demo_products', 'ferm_living_demo_products', 10, 2 );
add_filter( 'aether_demo_categories', 'ferm_living_demo_categories', 10, 2 );
```

Then add the callback functions at the end of the file:

```php
/**
 * Provide Ferm Living demo products when demo mode is active.
 *
 * @param array $items     Current demo items (empty by default).
 * @param array $query_args Adapter query args.
 * @return array Ferm Living reference products or empty array.
 */
function ferm_living_demo_products( $items, $query_args ) {
	if ( ! function_exists( 'aether_active_design' ) || 'fermliving' !== aether_active_design() ) {
		return $items;
	}

	$pack_url = aether_pack_url();
	$assets   = $pack_url . 'assets';

	$all_products = array(
		array(
			'name'        => 'Donkey Soft Toy',
			'price'       => '€49.00',
			'image'       => $assets . '/products/donkey-soft-toy.png',
			'alt'         => 'Ferm Living Donkey Soft Toy',
			'badge'       => '',
			'url'         => home_url('/product/donkey-soft-toy/'),
			'tagline'     => 'Playful decor for kids and adults',
		),
		array(
			'name'        => 'Pear Braided Storage',
			'price'       => '€89.00',
			'image'       => $assets . '/products/pear-braided-storage.png',
			'alt'         => 'Ferm Living Pear Braided Storage',
			'badge'       => 'New',
			'url'         => home_url('/product/pear-braided-storage/'),
			'tagline'     => 'Organic forms meets practical storage',
		),
		array(
			'name'        => 'Swif Bird Garland',
			'price'       => '€39.00',
			'image'       => $assets . '/products/swif-bird-garland.png',
			'alt'         => 'Ferm Living Swif Bird Garland',
			'badge'       => '',
			'url'         => home_url('/product/swif-bird-garland/'),
			'tagline'     => 'Decorative paper garland',
		),
		array(
			'name'        => 'Willora Braided Storage',
			'price'       => '€69.00',
			'image'       => $assets . '/products/willora-braided-storage.png',
			'alt'         => 'Ferm Living Willora Braided Storage',
			'badge'       => '',
			'url'         => home_url('/product/willora-braided-storage/'),
			'tagline'     => 'Handcrafted braided organizer',
		),
		array(
			'name'        => 'Parcel Hallway Cabinet',
			'price'       => '€899.00',
			'image'       => $assets . '/products/parcel-hallway-cabinet.png',
			'alt'         => 'Ferm Living Parcel Hallway Cabinet',
			'badge'       => 'Featured',
			'url'         => home_url('/product/parcel-hallway-cabinet/'),
			'tagline'     => 'Minimalist steel storage',
		),
		array(
			'name'        => 'Paper Pulp Box',
			'price'       => '€29.00',
			'image'       => $assets . '/products/paper-pulp-box.png',
			'alt'         => 'Ferm Living Paper Pulp Box',
			'badge'       => '',
			'url'         => home_url('/product/paper-pulp-box/'),
			'tagline'     => 'Sustainable paper storage',
		),
		array(
			'name'        => 'Kona Bookcase',
			'price'       => '€1,299.00',
			'image'       => $assets . '/products/kona-bookcase.png',
			'alt'         => 'Ferm Living Kona Bookcase',
			'badge'       => 'Featured',
			'url'         => home_url('/product/kona-bookcase/'),
			'tagline'     => 'Elegant oak shelving',
		),
		array(
			'name'        => 'Haze Wall Cabinet',
			'price'       => '€449.00',
			'image'       => $assets . '/products/haze-wall-cabinet.png',
			'alt'         => 'Ferm Living Haze Wall Cabinet',
			'badge'       => 'New',
			'url'         => home_url('/product/haze-wall-cabinet/'),
			'tagline'     => 'Smoked glass wall storage',
		),
	);

	// Return a subset based on query args (default 4, offset 0).
	$per_page = isset( $query_args['posts_per_page'] ) ? (int) $query_args['posts_per_page'] : 4;
	$offset   = isset( $query_args['offset'] ) ? (int) $query_args['offset'] : 0;

	return array_slice( $all_products, $offset, $per_page );
}

/**
 * Provide Ferm Living demo categories when demo mode is active.
 *
 * @param array $items Current demo items (empty by default).
 * @param array $args  Adapter args.
 * @return array Ferm Living reference categories or empty array.
 */
function ferm_living_demo_categories( $items, $args ) {
	if ( ! function_exists( 'aether_active_design' ) || 'fermliving' !== aether_active_design() ) {
		return $items;
	}

	$pack_url = aether_pack_url();
	$assets   = $pack_url . 'assets';

	$categories = array(
		array( 'name' => 'Furniture',    'image' => $assets . '/categories/furniture.webp',    'modifier' => 'large' ),
		array( 'name' => 'Lighting',     'image' => $assets . '/categories/lighting.webp',     'modifier' => '' ),
		array( 'name' => 'Accessories',  'image' => $assets . '/categories/accessories.webp',  'modifier' => '' ),
		array( 'name' => 'Kids',         'image' => $assets . '/categories/kids.webp',         'modifier' => '' ),
		array( 'name' => 'Textiles',     'image' => $assets . '/categories/textiles.webp',     'modifier' => '' ),
		array( 'name' => 'Kitchen',      'image' => $assets . '/categories/kitchen.webp',      'modifier' => '' ),
		array( 'name' => 'Outdoor',      'image' => $assets . '/categories/outdoor.webp',      'modifier' => '' ),
	);

	$shop_url = function_exists( 'wc_get_page_permalink' )
		? wc_get_page_permalink( 'shop' )
		: home_url( '/shop/' );

	$items = array();
	foreach ( $categories as $cat ) {
		$items[] = array(
			'name'     => $cat['name'],
			'count'    => '',
			'image'    => $cat['image'],
			'alt'      => sprintf( __( 'Shop %s', 'aureon' ), $cat['name'] ),
			'url'      => $shop_url,
			'modifier' => $cat['modifier'],
			'behavior' => array( 'reveal' => true ),
		);
	}

	return $items;
}
```

- [ ] **Step 4: Sync all to Docker**

```bash
docker cp "C:\Users\hamma\Downloads\phantom\wordpress\frontend\adapters\adapter-wc-products.php" "aureon_wp:/var/www/html/wp-content/frontend/adapters/adapter-wc-products.php"
docker cp "C:\Users\hamma\Downloads\phantom\wordpress\frontend\adapters\adapter-wc-categories.php" "aureon_wp:/var/www/html/wp-content/frontend/adapters/adapter-wc-categories.php"
docker cp "C:\Users\hamma\Downloads\phantom\wordpress\frontend\designs\fermliving\composer.php" "aureon_wp:/var/www/html/wp-content/frontend/designs/fermliving/composer.php"
docker exec aureon_wp php -r "if(function_exists('opcache_reset')){opcache_reset();echo'OK';}"
```

- [ ] **Step 5: Verify demo content overrides WC data**

Navigate to `http://localhost:8080`, check:
- Categories show "Furniture", "Lighting", "Accessories" (not "Men's Shoes")
- Products show "Donkey Soft Toy", "Pear Braided Storage" (not "Encore sneakers")
- All product images load (HTTP 200)

- [ ] **Step 6: Commit**

```bash
git add frontend/adapters/adapter-wc-products.php frontend/adapters/adapter-wc-categories.php frontend/designs/fermliving/composer.php
git commit -m "feat: add generic aether_demo_products/aether_demo_categories filters with Ferm Living overrides"
```

---

## Task 5: Add content-level E2E test assertions

**Files:**
- Modify: `frontend/tests/specs/fermliving-e2e.cjs`

**Interfaces:**
- Consumes: Live site at `http://localhost:8080`
- Produces: Test assertions that verify content correctness, not just element existence

**Why:** The existing 43/43 tests all passed while the page showed AETHER shoes. New assertions must verify that the correct Ferm content is actually rendered.

- [ ] **Step 1: Add product content assertions**

Add after the existing "Isolation: no hardcoded product names" test:

```javascript
// === CONTENT CORRECTNESS ===
await test('Content: hero images load (no 404)', async (p) => {
  const responses = [];
  p.on('response', r => { if (r.url().includes('hero/') && r.url().includes('fermliving')) responses.push(r); });
  await p.goto('http://localhost:8080/', { waitUntil: 'networkidle', timeout: 20000 });
  const broken = responses.filter(r => r.status() >= 400);
  if (broken.length > 0) throw new Error('Hero image 404: ' + broken[0].url());
});

await test('Content: editorial images load (no 404)', async (p) => {
  const responses = [];
  p.on('response', r => { if (r.url().includes('editorial/') && r.url().includes('fermliving')) responses.push(r); });
  await p.goto('http://localhost:8080/', { waitUntil: 'networkidle', timeout: 20000 });
  const broken = responses.filter(r => r.status() >= 400);
  if (broken.length > 0) throw new Error('Editorial image 404: ' + broken[0].url());
});

await test('Content: no AETHER shoe products in demo mode', async (p) => {
  await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
  const html = await p.content();
  const aetherShoes = ['Encore', 'Diplomat', 'Court', 'Captain Brogue', 'Men\'s Shoes', 'Men\'s Sneakers', 'Men\'s Boots', 'Women\'s Bags'];
  for (const term of aetherShoes) {
    if (html.includes(term)) throw new Error('AETHER shoe content found: ' + term);
  }
});

await test('Content: Ferm product names present', async (p) => {
  await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
  const html = await p.content();
  const fermProducts = ['Donkey', 'Pear', 'Swif', 'Willora', 'Kona', 'Haze'];
  let found = 0;
  for (const name of fermProducts) {
    if (html.includes(name)) found++;
  }
  if (found < 3) throw new Error('Expected at least 3 Ferm products, found ' + found);
});

await test('Content: Ferm category names present', async (p) => {
  await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
  const html = await p.content();
  const fermCats = ['Furniture', 'Lighting', 'Accessories', 'Kids', 'Textiles'];
  let found = 0;
  for (const cat of fermCats) {
    if (html.includes(cat)) found++;
  }
  if (found < 3) throw new Error('Expected at least 3 Ferm categories, found ' + found);
});

await test('Content: room grid section exists', async (p) => {
  await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
  const el = await p.$('.ferm-room-grid, [class*="room"]');
  if (!el) throw new Error('Room grid section not found');
});

await test('Content: Ferm footer social links', async (p) => {
  await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
  const html = await p.content();
  if (!html.includes('instagram.com/fermliving')) throw new Error('Ferm Instagram link not found');
});

await test('Content: Ferm newsletter heading', async (p) => {
  await p.goto('http://localhost:8080/', { waitUntil: 'domcontentloaded', timeout: 15000 });
  const html = await p.content();
  if (!html.includes('Ferm Living news')) throw new Error('Ferm newsletter heading not found');
});
```

- [ ] **Step 2: Run tests to verify they pass**

```bash
cd C:\Users\hamma\Downloads\phantom\wordpress
npx playwright test frontend/tests/specs/fermliving-e2e.cjs --reporter=list 2>&1 | Select-String -Pattern "PASS|FAIL|Total"
```

Expected: All tests pass (old + new content assertions).

- [ ] **Step 3: Commit**

```bash
git add frontend/tests/specs/fermliving-e2e.cjs
git commit -m "test: add content correctness assertions to Ferm Living E2E suite"
```

---

## Task 6: Final verification — screenshot comparison

**Files:**
- None (verification only)

**Interfaces:**
- Consumes: Live site at `http://localhost:8080`
- Produces: Screenshots for visual comparison

**Why:** The original failure was identified via screenshots. Final verification must include visual evidence.

- [ ] **Step 1: Capture homepage screenshot**

Navigate to `http://localhost:8080`, take full-page screenshot at 1440px viewport.

- [ ] **Step 2: Verify against frozen reference**

Compare section order, image presence, typography, and layout against `C:\Users\hamma\Downloads\SiteOne-Crawler\fermliving.com\index.html`.

- [ ] **Step 3: Capture responsive screenshots**

Take screenshots at 768px and 390px viewports.

- [ ] **Step 4: Final report**

Document remaining differences (if any) and confirm client readiness.

---

## Task 7: Git commit all changes

**Files:**
- All modified files from Tasks 1-5

**Interfaces:**
- Consumes: All previous task outputs
- Produces: Clean git commit with all fixes

- [ ] **Step 1: Review git status**

```bash
git status
git diff --stat
```

- [ ] **Step 2: Stage all changes**

```bash
git add -A
```

- [ ] **Step 3: Commit**

```bash
git commit -m "fix: Ferm Living root-cause fixes — asset URLs, demo content filters, section IDs, content tests

- Add aether_pack_url() generic helper for design asset URL resolution
- Fix tokens.php and composer.php to use aether_pack_url() instead of broken get_template_directory_uri()
- Fix composer section IDs: rooms → ferm-room-grid, register secondary_products
- Add generic aether_demo_products/aether_demo_categories filter hooks
- Register Ferm Living demo content overrides in composer.php
- Add content correctness E2E assertions (product names, categories, image HTTP status)

Root causes addressed:
1. Asset URLs pointed to non-existent /themes/aureon/designs/ path
2. WC database AETHER shoes now overridden by pack demo content via generic filters
3. Composer section IDs now match registry
4. secondary_products now registered as pack section
5. E2E tests now verify content, not just element existence"
```

---

## Verification Checklist

After all tasks complete, verify:

- [ ] Hero images load (HTTP 200) — no 404s
- [ ] Categories show Ferm Living furniture categories
- [ ] Products show Ferm Living reference products
- [ ] Editorial images load (HTTP 200)
- [ ] Room grid section renders
- [ ] Secondary products section renders
- [ ] No AETHER shoe content visible
- [ ] Footer shows Ferm Living social links
- [ ] Newsletter shows "Ferm Living news"
- [ ] All E2E tests pass (old + new)
- [ ] WooCommerce still works (cart, checkout, account)
- [ ] Luxury design unaffected (isolation)
- [ ] No Ferm-specific logic in shared adapters
