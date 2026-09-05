# 15 — MENU ARCHITECTURE

## Menu Locations

| Location | Purpose |
|----------|---------|
| `primary` | Main navigation (desktop + mobile) |
| `footer` | Footer navigation |

## Menu Data Flow

### Component Mode
```
WordPress nav_menu_locations
    ↓
aether_adapter_menu($location)
    ↓
wp_get_nav_menu_items()
    ↓
aether_build_menu_tree($items)
    ↓
[{label, url, active, children: [{label, url, active}]}]
    ↓
Shell component (header.php / mobile-chrome.php)
```

### Complete-Page (Ferm)
```
WordPress nav_menu_locations
    ↓
ferm_get_nav_menu($location)
    ↓
wp_get_nav_menu_items()
    ↓
[{title, url, children: [{title, url}]}]
    ↓
FermPageData.navigation.main / .footer
    ↓
Client JS reads globals
```

## Menu Tree Structure

```json
[
  {
    "label": "Shop",
    "url": "https://site/shop/",
    "active": false,
    "children": [
      {"label": "Men", "url": "...", "active": false},
      {"label": "Women", "url": "...", "active": false}
    ]
  },
  {
    "label": "About",
    "url": "https://site/about/",
    "active": true,
    "children": []
  }
]
```

## Fallback Menu

When no menu is assigned or menu is too sparse (< 4 items), a curated fallback is used:
- Home, Shop (with dropdown), About, Blog, Contact

## Active State Detection

```php
function aether_menu_item_is_active($item, $object) {
    if ($object && $object->ID === $item->object_id) {
        return true;
    }
    return false;
}
```

## Social Links

```php
aether_adapter_socials() → [
    {icon: 'fab fa-instagram', label: 'Instagram', url: '...'},
    {icon: 'fab fa-twitter', label: 'Twitter', url: '...'},
    ...
]
```
