# FRONTEND REPLACEMENT PROMPT

> Use this prompt to instruct an AI or developer to replace the frontend of the AUREON/AETHER WordPress theme with a new design.

---

## The Prompt

```
You are working on a WordPress theme called AUREON with an integrated frontend engine called AETHER.

## CRITICAL RULES (READ BEFORE ANYTHING)

1. NEVER edit `aureon/theme/**` or `aureon/plugin/**` — these are versioned and updatable
2. ONLY edit files inside `frontend/designs/{your-pack}/` — this is your isolated design pack
3. The engine kernel at `frontend/views/` is shared infrastructure — do NOT modify
4. Adapters at `frontend/adapters/` are the ONLY WP/WC touchpoint — modify only if changing data contracts
5. Components at `frontend/components/` and sections at `frontend/sections/` are base templates — your pack shadows them by placing files at the same relative path

## ARCHITECTURE (6-Layer Data Flow)

```
WordPress/WooCommerce/Customizer
    → 23 Adapters (ONLY WP/WC touchpoint)
    → Normalized data arrays
    → ViewModels (data normalization)
    → Renderer → Composer → 53 Components + 26 Sections
    → Design Pack (presentation: HTML/CSS/JS)
```

## YOUR TASK

Replace the frontend with a new design by creating/modifying a design pack at:
`frontend/designs/{your-pack}/`

## DESIGN PACK STRUCTURE

```
frontend/designs/{your-pack}/
  manifest.json              ← Pack descriptor (name, version, CSS/JS assets)
  tokens.php                 ← Design token overrides (colors, typography, spacing)
  composer.php               ← Section ordering + adapter filters
  css/
    fonts.css                ← Font imports (@font-face)
    {pack}.css               ← Your main stylesheet
  js/
    {pack}.js                ← Your main JavaScript
    bridge.js                ← Cart/wishlist bridge (optional, ≤150 lines)
  components/                ← Component template overrides (shadow base)
    shell/
      header.php
      footer.php
      mobile-chrome.php
      announcement.php
      preloader.php
    cards/
      product.php
      category.php
    product/
      info.php
      gallery.php
      related.php
  sections/                  ← Section template overrides (shadow base)
    hero.php
    categories.php
    bestsellers.php
    newsletter.php
    shop-hero.php
    shop-filter.php
    shop-grid.php
    product.php
    section-cart.php
    checkout.php
    order-confirmation.php
    blog-grid.php
    blog-single.php
    wishlist.php
    auth.php
    account.php
  assets/                    ← Your images, fonts, icons
    fonts/
    images/
```

## STEP-BY-STEP REPLACEMENT PROCESS

### Step 1: Study the Existing Architecture

Read these files first:
- `docs/FRONTEND_REPLACEMENT_AND_EDITING_GUIDE.md` — Authoritative guide
- `docs/forensics/CORE-THEME-AUDIT.md` — What's safe to touch
- `docs/forensics/CORE-TO-FERM-INTEGRATION-MAP.md` — Data flow mapping
- `docs/forensics/FERM-TEMPLATE-AUDIT.md` — Template family analysis
- `frontend/manifest/components.php` — All 53 component registrations
- `frontend/views/registry.php` — All 26 section registrations

### Step 2: Study the Data Contracts

Every component receives pre-normalized data from adapters. Key contracts:

**Product Card** (from `adapter-wc-products.php`):
- `id`, `name`, `price`, `image`, `url`, `badge`, `variants`, `rating`, `review_count`

**Product Page** (from `adapter-product.php`):
- `id`, `name`, `price`, `description`, `short_description`, `images`, `variants`, `attributes`, `add_to_cart_url`, `gallery`, `reviews`, `rating`, `review_count`, `related`

**Cart** (from `adapter-cart.php`):
- `items[]` (key, id, name, price, image, quantity, subtotal, variants, remove_url)
- `subtotal`, `shipping`, `total`, `count`, `is_empty`

**Shell/Header** (from `adapter-shell.php`):
- `logo`, `nav_items[]`, `footer_links[]`, `cart_count`, `search_url`, `account_url`, `announcement`

### Step 3: Create Your Pack Directory

```bash
mkdir -p frontend/designs/{your-pack}/css
mkdir -p frontend/designs/{your-pack}/js
mkdir -p frontend/designs/{your-pack}/components/shell
mkdir -p frontend/designs/{your-pack}/components/cards
mkdir -p frontend/designs/{your-pack}/components/product
mkdir -p frontend/designs/{your-pack}/sections
mkdir -p frontend/designs/{your-pack}/assets/fonts
mkdir -p frontend/designs/{your-pack}/assets/images
```

### Step 4: Create manifest.json

```json
{
  "name": "{your-pack}",
  "version": "1.0.0",
  "description": "Your custom design pack",
  "author": "Your Name",
  "assets": {
    "css": ["css/{pack}.css"],
    "js": ["js/{pack}.js"]
  }
}
```

### Step 5: Create tokens.php

```php
<?php
return [
    'colors' => [
        'primary'    => '#000000',
        'secondary'  => '#ffffff',
        'accent'     => '#c8a97e',
        'background' => '#f8f6f3',
        'text'       => '#1a1a1a',
        'muted'      => '#6b7280',
    ],
    'typography' => [
        'heading_font' => '"Your Heading Font", serif',
        'body_font'    => '"Your Body Font", sans-serif',
        'base_size'    => '16px',
        'scale'        => '1.25',
    ],
    'spacing' => [
        'section_gap' => '6rem',
        'container_width' => '1280px',
    ],
];
```

### Step 6: Create composer.php

```php
<?php
// Control section ordering on homepage
add_filter('aether_frontpage_sections', function($sections) {
    return ['hero', 'categories', 'bestsellers', 'newsletter'];
});
```

### Step 7: Shadow Components

Copy base templates to your pack and modify:

```bash
# Example: override product card
cp frontend/components/cards/product.php frontend/designs/{your-pack}/components/cards/product.php
# Edit the copy — base remains untouched
```

### Step 8: Create Your CSS

```css
@import url('fonts.css');

:root {
    --color-primary: #000000;
    --color-secondary: #ffffff;
}

/* Your styles here */
```

### Step 9: Create Your JS

```javascript
(function() {
    'use strict';
    // Your pack JavaScript
    // Safe: DOM manipulation, animations, UI interactions
    // Forbidden: calling WP/WC functions, modifying AETHER globals
})();
```

### Step 10: Activate Your Pack

Set the `aether_active_design` option to your pack slug, or use WordPress Customizer → AETHER Design.

## COMPONENT TEMPLATE PATTERN

Every component follows this pattern:

```php
<?php
// $data is the normalized data array from the adapter
// NEVER call WP/WC functions here
// ONLY use $data fields and escape output

$name  = esc_html($data['name'] ?? '');
$price = $data['price'] ?? '';
$image = esc_url($data['image'] ?? '');
$url   = esc_url($data['url'] ?? '');
?>

<div class="product-card">
    <?php if ($image): ?>
        <img src="<?php echo $image; ?>" alt="<?php echo $name; ?>" loading="lazy">
    <?php endif; ?>
    <h3 class="product-card__title">
        <a href="<?php echo $url; ?>"><?php echo $name; ?></a>
    </h3>
    <div class="product-card__price"><?php echo $price; ?></div>
</div>
```

## WHAT YOU CAN DO

| Operation | Safe? | Where |
|-----------|-------|-------|
| Override component templates | YES | `pack/components/**/*.php` |
| Override section templates | YES | `pack/sections/**/*.php` |
| Modify CSS | YES | `pack/css/*.css` |
| Modify JS | YES | `pack/js/*.js` |
| Change fonts | YES | `pack/css/fonts.css` + `pack/assets/fonts/` |
| Change colors | YES | `pack/tokens.php` or CSS custom properties |
| Reorder sections | YES | `pack/composer.php` |
| Add new sections | YES | Create section + adapter, register in composer |
| Change logo | YES | `pack/components/shell/header.php` |
| Modify product card | YES | `pack/components/cards/product.php` |

## WHAT YOU CANNOT DO

| Operation | Forbidden? | Why |
|-----------|------------|-----|
| Edit `aureon/theme/**` | YES | Theme updates overwrite changes |
| Edit `aureon/plugin/**` | YES | Plugin updates overwrite changes |
| Edit `frontend/views/**` | YES | Engine kernel — shared infrastructure |
| Edit `frontend/adapters/**` | CAUTION | Data contract boundary — breaks all components |
| Call WP/WC functions in templates | YES | Components receive normalized data only |
| Modify AETHER globals | YES | Breaks engine behavior |
| Override platform JS (GSAP, Swiper) | YES | Platform dependency |

## TESTING CHECKLIST

After making changes, verify:

- [ ] Homepage renders at 1440px, 1024px, 768px, 390px
- [ ] Shop page renders at all breakpoints
- [ ] Product page renders at all breakpoints
- [ ] Cart page renders at all breakpoints
- [ ] Add to cart works (simple + variable products)
- [ ] Cart update/remove works
- [ ] Checkout flow completes
- [ ] My Account login works
- [ ] Wishlist add/remove works
- [ ] All images load correctly
- [ ] All fonts load correctly
- [ ] No CSS conflicts
- [ ] No JS errors in console
- [ ] Mobile menu opens/closes
- [ ] Mobile cart count updates

## ROLLBACK

Every change is in git. To rollback:

```bash
# Rollback specific file
git checkout HEAD -- frontend/designs/{your-pack}/css/{pack}.css

# Rollback last commit
git revert HEAD

# Switch packs instantly
# Change aether_active_design option → clear cache → done
```

## REFERENCE FILES

- `docs/FRONTEND_REPLACEMENT_AND_EDITING_GUIDE.md` — Full guide
- `docs/forensics/CORE-TO-FERM-INTEGRATION-MAP.md` — Data flow
- `docs/FRONTEND_DATA_CONTRACT.md` — Adapter data contracts
- `docs/CUSTOMIZER_FRONTEND_BINDING_MATRIX.md` — Customizer bindings
- `docs/WOO_FRONTEND_BINDING_MATRIX.md` — WooCommerce bindings
- `frontend/manifest/components.php` — Component registry
- `frontend/views/registry.php` — Section registry
```

---

## How to Use This Prompt

1. **Copy the prompt** inside the code block above
2. **Replace `{your-pack}`** with your pack name (e.g., `mybrand`)
3. **Replace `{pack}`** with your pack shortname (e.g., `mybrand`)
4. **Paste into your AI assistant** or give to your developer
5. **Follow the step-by-step process** in order
6. **Test everything** using the checklist before deploying

## Customization Points

| What to Change | Where to Edit |
|----------------|---------------|
| Colors | `tokens.php` or CSS `:root` variables |
| Fonts | `css/fonts.css` + `assets/fonts/` |
| Layout | Component/section templates + CSS |
| Section order | `composer.php` filter |
| Logo | `components/shell/header.php` |
| Navigation | WP Admin → Appearance → Menus |
| Product card | `components/cards/product.php` |
| Cart page | `sections/section-cart.php` |
| Checkout | WC native (wrapped in shell) |
| Footer | `components/shell/footer.php` |
| Mobile menu | `components/shell/mobile-chrome.php` |
