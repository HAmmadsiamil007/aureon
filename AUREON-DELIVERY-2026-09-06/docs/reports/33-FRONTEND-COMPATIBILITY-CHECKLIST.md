# 33 — FRONTEND COMPATIBILITY CHECKLIST

## Pre-Flight Checklist

### Source
- [ ] Complete frontend source obtained
- [ ] Source frozen (no modifications during integration)
- [ ] All assets local or CDN with rewriting capability

### HTML
- [ ] All page families present (home, product, cart, checkout, account)
- [ ] No Shopify-specific markup (unless bridge handles)
- [ ] No hardcoded external URLs (unless bridge handles)
- [ ] Accessible structure (headings, landmarks, ARIA)

### CSS
- [ ] All styles self-contained
- [ ] No external font dependencies (unless self-hosted)
- [ ] Responsive breakpoints defined
- [ ] No global namespace conflicts

### JavaScript
- [ ] All scripts self-contained
- [ ] No direct Shopify/WooCommerce API calls
- [ ] Event delegation for dynamic content
- [ ] Graceful degradation without JS

### Assets
- [ ] All images local or CDN with path rewriting
- [ ] All fonts self-hosted
- [ ] No broken asset references
- [ ] Favicon present

### Fonts
- [ ] All fonts have valid licenses
- [ ] Redistribution rights confirmed
- [ ] Fallback fonts defined

### Dependencies
- [ ] All vendor libraries identified
- [ ] No prohibited business APIs
- [ ] CDN dependencies documented

### Routes
- [ ] All WordPress routes mapped to HTML files
- [ ] Product routes handle slugs
- [ ] Category routes handle slugs
- [ ] Fallback route defined

### Products
- [ ] Simple product template present
- [ ] Variable product template present
- [ ] Product data slots identified

### Variations
- [ ] Variation selection UI present
- [ ] Price change on variation
- [ ] Image change on variation

### Categories
- [ ] Category grid/list present
- [ ] Category image handling
- [ ] Category link handling

### Menus
- [ ] Primary navigation present
- [ ] Footer navigation present
- [ ] Mobile navigation present
- [ ] Menu data hooks identified

### Search
- [ ] Search input present
- [ ] Search results handling
- [ ] Empty state handling

### Account
- [ ] Login form present
- [ ] Account dashboard handling
- [ ] Logout handling

### Cart
- [ ] Cart display present
- [ ] Add to cart handling
- [ ] Quantity update handling
- [ ] Remove item handling
- [ ] Cart count display

### Checkout
- [ ] Checkout form handling (or WC native)

### Customizer
- [ ] Logo target identified
- [ ] Hero target identified
- [ ] Announcement target identified
- [ ] Footer target identified
- [ ] Color/font targets identified

### Demo
- [ ] Demo product handling
- [ ] Demo category handling
- [ ] Fallback content defined

### Security
- [ ] Nonce handling for AJAX
- [ ] Input validation present
- [ ] No secrets in client code

### Responsive
- [ ] 1440px layout verified
- [ ] 1024px layout verified
- [ ] 768px layout verified
- [ ] 390px layout verified

### Network
- [ ] Zero Shopify API calls
- [ ] Zero Clerk dependencies
- [ ] Zero unexpected external requests

### Console
- [ ] Zero JS errors
- [ ] Zero missing dependencies
