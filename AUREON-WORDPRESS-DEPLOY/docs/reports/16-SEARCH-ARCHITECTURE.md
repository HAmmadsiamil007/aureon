# 16 — SEARCH ARCHITECTURE

## Search Flow

### Component Mode
```
User opens search modal
    ↓
User types query
    ↓
Form submits to /?s={query}
    ↓
WordPress search results
    ↓
index.php renders results
```

### Complete-Page (Ferm)
```
User opens search (frozen HTML)
    ↓
search-bridge.js intercepts
    ↓
Searches WordPress via AJAX or redirects to /?s={query}
    ↓
Results rendered via blogs/stories.html fallback
```

## Search Configuration

```php
// adapter search data
'search' => [
    'placeholder' => 'Search Ferm Living...',
    'suggestions' => ['Furniture', 'Lighting', 'Accessories', 'Kids', 'Kitchen'],
]
```

## Search Components

| Component | Purpose |
|-----------|---------|
| Search modal | Desktop search overlay |
| Mobile search | Mobile search interface |
| Search form | Form element |
| Search results | Results display |

## Search Bridge (Ferm)

`search-bridge.js` provides:
- Search input handling
- Result display
- Close/Escape behavior
- Mobile search

## Search URLs

- Component: `home_url('/?s=')`
- Ferm: `FermPageData.config.search_url`
