# 32 — CLIENT INTEGRATION REQUIREMENTS

## Integration Contract

Any future premium frontend must provide:

## Page Families

| Page | Required? | Route |
|------|-----------|-------|
| Homepage | ✅ Yes | / |
| Shop/Collection | ✅ Yes | /shop/, /product-category/* |
| Product | ✅ Yes | /product/* |
| Cart | ✅ Yes | /cart/ |
| Checkout | ✅ Yes | /checkout/ |
| Account | ✅ Yes | /my-account/ |
| Blog | ⚠️ Recommended | /blog/ |
| About | ⚠️ Recommended | /about/ |
| Contact | ⚠️ Recommended | /contact/ |
| 404 | ⚠️ Recommended | /* |

## DOM Hooks

### Required IDs/Classes
- `#main` — skip-link target
- `.cart-count` — cart count display
- `.cart-count-bubble` — Ferm cart bubble

### Required Data Attributes
- `data-reveal-item` — motion reveal
- `data-reveal-group` — group reveal
- `data-tilt` — tilt effect
- `data-parallax` — parallax effect
- `data-motion-text` — text animation

## Data Requirements

### Product Data
- id, title, slug, url
- price (cents for complete-page)
- gallery images
- availability
- variants (for variable products)

### Cart Data
- items (key, id, quantity, price)
- item_count
- total_price (cents)

### Navigation Data
- main menu items
- footer menu items
- children hierarchy

### Customizer Data
- site name/logo
- announcement content
- hero slides
- footer columns
- social links
- colors/fonts

## Business Actions

| Action | Method | Nonce |
|--------|--------|-------|
| Add to cart | AJAX | ferm_cart_nonce |
| Update cart | AJAX | ferm_cart_nonce |
| Get cart | AJAX | ferm_cart_nonce |
| Search | Form/AJAX | — |
| Login | Form | — |
| Logout | Link | — |
| Checkout | WC native | WC nonce |

## Asset Requirements

### Complete-Page
- Pack CSS (self-contained)
- Pack JS (self-contained)
- Pack fonts (self-hosted)
- Pack images (local or CDN with rewriting)

### Component-Mode
- Platform CDNs (Bootstrap, FA, Swiper, GSAP)
- Pack CSS (additional styling)
- Pack JS (additional behavior)
- Platform contract JS (animations, main)

## Form Behavior

- Login form → WP auth
- Contact form → AJAX + email
- Newsletter → AJAX + email
- Cart form → WC AJAX
- Checkout form → WC native

## Accessibility

- Skip link targeting #main
- ARIA labels on interactive elements
- Keyboard navigation support
- Screen reader text where needed
