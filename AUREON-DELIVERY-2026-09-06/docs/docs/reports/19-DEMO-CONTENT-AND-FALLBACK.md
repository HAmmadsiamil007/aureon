# 19 — DEMO CONTENT AND FALLBACK

## Demo Content System

### Master Switch
`aether_demo_content` (default: `true`)

When `true`: demo content shown when no real content exists.
When `false`: only real data shown; empty states displayed.

## How It Works

### Products
```
WC Product Query
    ↓
No real products found?
    ↓
aether_demo_content = true?
    YES → Load from aether_product_items (Customizer)
    NO  → Show empty state
```

### Categories
```
WC Category Query
    ↓
No real categories found?
    ↓
aether_demo_content = true?
    YES → Load from aether_category_items (Customizer)
    NO  → Show empty state
```

## Demo Filtering

When real products exist, demo products are automatically hidden:

```php
// ferm_filter_demo_products()
add_action('woocommerce_product_query', 'ferm_filter_demo_products');
// Filters out products with aureon_demo=1 meta
```

When real categories exist, demo categories are hidden:

```php
// ferm_filter_demo_categories()
add_filter('get_terms', 'ferm_filter_demo_categories', 10, 3);
// Filters out terms with aureon_demo_category=1 meta
```

## Demo Data Files (Ferm)

```
aureon/frontend/designs/fermliving/data/
├── products.json     # Demo product data
└── categories.json   # Demo category data
```

## Fallback Behavior

| State | Products | Categories |
|-------|----------|------------|
| No real + demo=true | Demo shown | Demo shown |
| No real + demo=false | Empty state | Empty state |
| Real exists + demo=true | Real shown | Real shown |
| Real exists + demo=false | Real shown | Real shown |

## Demo Records Are Never Deleted

Demo products/categories remain in the database. They are:
- Filtered from queries when real content exists
- Preserved when real content is removed
- Restored to view when appropriate

## Default Logo/Hero

When no Customizer value is set:
- Logo: site name text
- Hero: default slides from tokens.php
- Announcement: default text from tokens.php
