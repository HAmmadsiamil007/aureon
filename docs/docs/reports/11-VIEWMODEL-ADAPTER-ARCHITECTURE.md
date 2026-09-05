# 11 — VIEWMODEL / ADAPTER ARCHITECTURE

## Data Flow

```
WordPress/WooCommerce
    ↓
Adapter (adapter-*.php) — ONLY layer allowed to touch WP/WC
    ↓
ViewModel Normalization (viewmodel.php)
    ↓
Component Data ($componentData / $sectionData)
    ↓
Template (components/**/*.php)
```

## Adapter Inventory

| Adapter | File | Input | Output |
|---------|------|-------|--------|
| Product | adapter-product.php | WC product | Gallery, badge, title, price, colors, sizes, specs, reviews |
| Cart | adapter-cart.php | WC()->cart | Items, totals, actions |
| Menu | adapter-menu.php | WP nav menus | Tree structure |
| Site | adapter-site.php | WP site data | Name, logo, tagline |
| Hero | adapter-hero.php | Customizer slides | Slide data |
| WC Products | adapter-wc-products.php | WP_Query | Product cards |
| WC Categories | adapter-wc-categories.php | get_terms | Category cards |
| WC Filter | adapter-wc-filter.php | Customizer | Filter options |
| Account | adapter-account.php | WC customer | Profile, orders |
| Auth | adapter-auth.php | WP auth | Login state |
| Blog | adapter-blog.php | WP posts | Blog grid |
| Article | adapter-article.php | WP post | Article content |
| About | adapter-about.php | Customizer | About content |
| Contact | adapter-contact.php | Customizer | Contact info |
| FAQ | adapter-faq.php | Customizer | FAQ items |
| Order | adapter-order.php | WC order | Order details |
| Team | adapter-team.php | Customizer | Team members |
| Testimonials | adapter-testimonials.php | Customizer | Testimonials |
| Wishlist | adapter-wishlist.php | User meta | Wishlist items |
| Coming Soon | adapter-coming-soon.php | Customizer | Countdown data |
| Shop Hero | adapter-shop-hero.php | Customizer | Shop hero |
| Options | adapter-options.php | Customizer | General options |
| Shell | adapter-shell.php | Various | Shell data |

## ViewModel Helpers

### `aether_viewmodel_image($attachment)`
Normalizes image to `{id, url, alt, sizes}`.

### `aether_viewmodel_resolve_image($src)`
Resolves relative paths against content root.

### `aether_sanitize_overlay_color($color)`
Validates hex/rgb/rgba colors.

### `aether_viewmodel_merge($data, $defaults)`
Merges component defaults with supplied data.

### `aether_viewmodel_behavior($behavior)`
Filters behavior flags based on Customizer motion toggles.

## Section Registry

```php
aether_register_section($id, [
    'template'     => 'sections/section-hero.php',
    'adapter'      => 'adapter-hero.php',
    'adapter_args' => [],
    'behavior'     => ['reveal' => true],
]);
```

## ViewModel Key Normalization

Aliases mapped to canonical:
- `paged` ↔ `pagination`
- `crumbs` ↔ `breadcrumb`
- `stats.items` → `stats`
