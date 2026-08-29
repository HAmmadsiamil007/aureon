# FERM LIVING DESIGN SPECIFICATION

**Version:** 1.0  
**Date:** August 25, 2026  
**Source:** Frozen Ferm Living Clone (SiteOne-Crawler)  
**Purpose:** Complete UI/UX specification for rebuilding the Ferm Living design pack

---

## 1. SITE ARCHITECTURE OVERVIEW

### 1.1 Technical Stack (Reference)
- **Platform:** Shopify (frozen clone)
- **CSS Framework:** Tailwind CSS (utility-first)
- **Carousel Engine:** Embla Carousel
- **Recommendations:** Clerk.io
- **UGC:** Flowbox
- **Wishlist:** Swym
- **Newsletter:** Klaviyo

### 1.2 AUREON Target Stack
- **Platform:** WordPress + WooCommerce
- **Engine:** AUREON/AETHER rendering system
- **Design Pack:** `frontend/designs/fermliving/`
- **Rendering:** Composer → Sections → Components → Templates

---

## 2. PAGE FAMILIES

### 2.1 Homepage
**Route:** `/`  
**Template:** `sections/section-hero.php` + homepage composition

**Section Sequence (top to bottom):**

| Order | Section | Component | Data Source |
|-------|---------|-----------|-------------|
| 1 | USP Header Bar | `shell/announcement` | Token overrides |
| 2 | Site Header | `shell/header` | `adapter-site` |
| 3 | Hero | `hero/slider` | `adapter-hero` |
| 4 | Room Category Carousel | `section-categories` | Token `aether_category_items` |
| 5 | Text+Image Split | `section-editorial-split` | Token content |
| 6 | Product Grid | `section-bestsellers` | `adapter-wc-products` |
| 7 | Room Grid | `section-room-grid` | Token `aether_room_items` |
| 8 | UGC Section | Custom component | Flowbox (reference) |
| 9 | Newsletter | `section-newsletter` | Token overrides |
| 10 | Site Footer | `shell/footer` | `adapter-footer` |

### 2.2 Shop / All Products
**Route:** `/shop/`  
**Template:** `sections/section-shop-grid.php`

**Composition:**
- Shop Hero (`section-shop-hero`)
- Filter Bar (`section-shop-filter`)
- Product Grid (4-col desktop, 2-col mobile)
- Pagination

### 2.3 Category / Collection
**Routes:** `/shop/furniture/`, `/shop/lighting/`, etc.  
**Template:** Same as Shop with category filter pre-applied

**Categories (7):**
1. Furniture
2. Lighting
3. Accessories
4. Kids
5. Textiles
6. Kitchen
7. Outdoor Living

### 2.4 Product Detail
**Route:** `/shop/?p=1` (sample)  
**Template:** `sections/section-product.php`

**Layout (6+6 split on desktop):**
- Left: Image Gallery (`product/gallery`)
- Right: Product Info (`product/info`)
- Below: Related Products (`section-related`)

### 2.5 About
**Route:** `/about/`  
**Template:** `page-about.php`

**Sections:**
- Mission (`section-mission`)
- Features (`section-features`)
- Story (`section-story`)
- Values (`section-values`)
- Stats (`section-stats`)
- Team (`section-team`)

### 2.6 Blog
**Route:** `/blog/`  
**Template:** `sections/section-blog-grid.php`

**Composition:**
- Blog Hero (page-title)
- Post Grid (3-col desktop, 1-col mobile)

### 2.7 Contact
**Route:** `/contact/`  
**Template:** `page-contact.php`

**Composition:**
- Contact Hero (page-title)
- Contact Section (`section-contact`)

### 2.8 Cart
**Route:** `/cart/`  
**Template:** `sections/section-cart.php`

**Composition:**
- Cart Items (`cart/items`)
- Cart Summary (`cart/summary`)

### 2.9 My Account
**Route:** `/my-account/`  
**Template:** `myaccount/my-account.php`

---

## 3. GLOBAL SHELL

### 3.1 Announcement Bar
**Component:** `shell/announcement`

**Behavior:**
- Rotating USP carousel (4 items)
- Auto-rotate every 4 seconds
- Dark background (`bg-black`), light text (`text-cream`)

**Content:**
1. "Free shipping on orders over EUR 150"
2. "Newsletter - Get 10% off your first order"
3. "Customer service - We're here to help"
4. "Fast delivery - 2-5 business days"

### 3.2 Header
**Component:** `shell/header`

**Layout:**
```
┌─────────────────────────────────────────────────────────┐
│ [Logo Left]    [Nav Center]    [Search] [Account] [Cart] │
└─────────────────────────────────────────────────────────┘
```

**Behavior:**
- Sticky on scroll
- Logo: "ferm living" text SVG
- Navigation: 4 items (Shop, Inspiration, Rooms, Professionals)
- Mega menu on hover (Shop)
- Search modal on click
- Cart indicator with count badge

### 3.3 Mobile Header
**Component:** `shell/mobile-chrome`

**Layout:**
```
┌─────────────────────────────┐
│ [Hamburger] [Logo] [Search] [Cart] │
└─────────────────────────────┘
```

**Behavior:**
- Hamburger opens slide-in menu from left
- 3-level submenu accordion
- Bottom navigation bar (optional)

### 3.4 Mega Menu
**Trigger:** Hover on "Shop" nav item

**Structure:**
```
┌─────────────────────────────────────────────────────────┐
│ [Quick Links]  [Categories Grid]  [Featured Image]      │
│                                                           │
│ Gift Guides    Kids               [Category Image]       │
│ Classics       Outdoor Living                           │
│ Bestsellers    Accessories                               │
│ Gift Card      Furniture                                 │
│                Sofas                                     │
│                Lighting                                  │
│                Kitchen                                   │
│                Textiles                                  │
│                Rugs                                      │
└─────────────────────────────────────────────────────────┘
```

### 3.5 Footer
**Component:** `shell/footer`

**Layout:**
```
┌─────────────────────────────────────────────────────────┐
│ USP Row (4 items: Free Shipping, Newsletter, Phone, Fast)│
├─────────────────────────────────────────────────────────┤
│ [Newsletter Signup]                                      │
├─────────┬───────────┬───────────┬───────────────────────┤
│Customer │Information│Professionals│                      │
│Service  │           │             │                      │
│Contact  │About      │B2B Login   │                      │
│Find a   │Career     │Image Bank  │                      │
│Retailer │Responsibility│Showrooms │                      │
│FAQ      │Boutique   │Catalogues  │                      │
│Shipping │Care       │Contract    │                      │
│Returns  │Assembly   │Company Info│                      │
├─────────┴───────────┴───────────┴───────────────────────┤
│ Terms | Cookies | Privacy | Follow Us                    │
│ Company: Ferm Living ApS CVR No. 30070186               │
│ Payment Icons                                            │
└─────────────────────────────────────────────────────────┘
```

---

## 4. COMPONENT SPECIFICATIONS

### 4.1 Product Card
**Component:** `cards/product`

**Layout:**
```
┌─────────────────────┐
│ [Image Carousel]     │
│ [Badges] [Heart]     │
│                      │
├─────────────────────┤
│ Product Name         │
│ Price                │
│ Color Swatches       │
│ [+Add Button]        │
└─────────────────────┘
```

**States:**
- Default
- Hover (image zoom)
- Loading (skeleton)
- Out of stock

**Data Attributes:**
- `data-product-title`
- `data-product-price` (in cents)
- `data-product-id`
- `data-variant-id`
- `data-web-color-hex`
- `data-web-color-name`
- `data-secondary-color-hex`
- `data-secondary-color-name`
- `data-compare-at-price`
- `data-variant-available`
- `data-certified`
- `data-certified-reason`

### 4.2 Category Card
**Component:** `cards/category`

**Layout:**
```
┌─────────────────────┐
│ [Background Image]   │
│                      │
│ Category Name        │
│ (overlay text)       │
└─────────────────────┘
```

**Behavior:**
- Full-width image
- Text overlay at bottom
- Hover: subtle zoom

### 4.3 Color Swatches

**Shape:** 45-degree rotated circles (`rotate-45 rounded-full`)

**Size:** 12px × 12px

**Border:** `border border-black/5`

**Dual-Color Support:**
- Split 50/50 with absolute-positioned halves
- Left half: primary color
- Right half: secondary color

### 4.4 Buttons

**Primary:**
```css
bg-black text-cream border-black
h-12 px-[14px] text-sm font-medium
transition-all duration-300 ease-in-out
hover: bg-cream text-black
disabled: opacity-40 cursor-not-allowed
```

**Secondary:**
```css
bg-transparent text-black border-black
h-12 px-[14px] text-sm font-medium
transition-all duration-300 ease-in-out
hover: bg-black text-cream
```

### 4.5 Product Badges

**Position:** Absolute top-left (`left-3.5 top-3.5`)

**Style:**
```css
bg-cream text-black uppercase text-xxs leading-[1]
px-1.5 py-[9px]
```

**Types:**
- "New" - New arrivals
- "Certified" - Sustainability certified
- "Sale" - On sale

### 4.6 Accordion

**Component:** `data-component='accordion'`

**Behavior:**
- Click to expand/collapse
- Smooth height transition
- Only one section open at a time

---

## 5. DESIGN TOKENS

### 5.1 Typography

| Token | Font Family | Weights | Usage |
|-------|------------|---------|-------|
| `font-primary` | **Canela** (serif) | 400 | Headlines, product names, hero text |
| `font-secondary` | **Teka** (sans-serif) | 400, 500 | Body text, buttons, navigation |

**Font Sizes:**
| Class | Size | Usage |
|-------|------|-------|
| `text-xxs` | 10px | Badges, small labels |
| `text-xs` | 12px | Shipping messages |
| `text-sm` | 13-14px | Product titles, footer links |
| `text-base` | 16px | Body text, prices |
| `text-lg` | 18-20px | Section headings |
| `text-xl` | 20-24px | Subheadings |
| `text-2xl` | 24-32px | Page headings |
| `text-[32px]` | 32px | Product page titles |
| `text-[48px]` | 48px | Hero text (tablet) |
| `text-[64px]` | 64px | Hero text (desktop) |
| `text-[80px]` | 80px | Large hero text |

**Line Heights:**
- `leading-[1]` - Tight (badges)
- `leading-tight` - Headings (~1.15)
- `leading-snug` - Subheadings (~1.25)
- `leading-relaxed` - Body text (~1.75)

### 5.2 Colors

| Token | Hex | Usage |
|-------|-----|-------|
| `cream` | `#fffefa` | Primary background, text on dark |
| `black` | `#383838` | Primary text, borders, dark bg |
| `full-black` | `#000000` | Overlays, progress bar |
| `black/80` | `rgba(56,56,56,0.8)` | Secondary text |
| `black/50` | `rgba(56,56,56,0.5)` | Tertiary text |
| `black/5` | `rgba(0,0,0,0.05)` | Subtle borders |
| `black/10` | `rgba(0,0,0,0.1)` | Progress bar bg |
| `red` | (custom) | Error states |

### 5.3 Spacing

| Token | Value | Usage |
|-------|-------|-------|
| `gutter-sm` | 16px | Mobile gutter |
| `gutter-md` | 24px | Desktop gutter |
| `section-spacing-md` | 60px | Tablet section spacing |
| `section-spacing-lg` | 100px | Desktop section spacing |

### 5.4 Layout

| Token | Value | Usage |
|-------|-------|-------|
| `site-max-width` | 1920px | Max container width |
| Grid columns | 12 | Page grid |
| Grid gap (mobile) | 16px | Column gap |
| Grid gap (desktop) | 24px | Column gap |

### 5.5 Breakpoints

| Token | Min-Width | Target |
|-------|-----------|--------|
| `sm` | 448px | Small phones |
| `mobile` | 600px | Large phones |
| `tab_p` | 768px | Portrait tablets |
| `tab_l` | 1024px | Landscape tablets |
| `md` | 992px | Medium desktops |
| `desktop` | 1200px | Full desktop |
| `lg` | 1440px | Large desktops |
| `xl` | 1920px | Extra-large desktops |

---

## 6. RESPONSIVE BEHAVIOR

### 6.1 Mobile (< 768px)
- Single-column layout
- Hamburger menu (slide-in from left)
- Product grid: 2 columns
- Cart drawer: full-width
- USP bar: scrollable
- Product images: 1:1.53 aspect ratio

### 6.2 Tablet Portrait (768px - 1023px)
- 12-column grid activates
- Product grid: 2 columns
- Section spacing: 60px
- Text-image sections: row-reverse

### 6.3 Tablet Landscape / Desktop (1024px+)
- Mega menu activates
- Product grid: 4 columns
- Section spacing: 100px
- Sticky header: 136px height
- Room carousels: 4 items visible

### 6.4 Large Desktop (1200px+)
- 5-item carousels
- Full navigation visible

### 6.5 Extra-Large (1920px+)
- Max container width: 1920px
- Additional horizontal padding: 104px

---

## 7. ANIMATIONS & INTERACTIONS

### 7.1 Transitions
- **Duration:** 300ms (default), 800ms (cart drawer)
- **Easing:** `ease-in-out`
- **Properties:** color, background-color, border-color, opacity, transform

### 7.2 Hover Effects
- **Product Card:** Image zoom (scale)
- **Buttons:** Color swap (primary) or fill (secondary)
- **Links:** Underline or color change
- **Category Card:** Subtle zoom

### 7.3 Scroll Effects
- **Sticky Header:** Compact on scroll
- **Section Reveal:** Fade-in on viewport entry
- **Parallax:** Hero images (optional)

### 7.4 Carousels
- **Engine:** Embla Carousel
- **Navigation:** Dots + prev/next arrows
- **Autoplay:** Optional, paused on hover
- **Drag:** Enabled on touch devices

---

## 8. ASSETS INVENTORY

### 8.1 Fonts
```
assets/fonts/
├── CanelaText-Regular.woff
├── CanelaText-Regular.woff2
├── KHTeka-Regular.woff
├── KHTeka-Regular.woff2
├── KHTeka-RegularItalic.woff
├── KHTeka-RegularItalic.woff2
├── KHTeka-Medium.woff
└── KHTeka-Medium.woff2
```

### 8.2 Images
```
assets/
├── hero/
│   ├── bestsellers.webp
│   └── dining.webp
├── categories/
│   ├── furniture.webp
│   ├── lighting.webp
│   ├── accessories.webp
│   ├── kids.webp
│   ├── textiles.webp
│   ├── kitchen.webp
│   └── outdoor.webp
├── rooms/
│   ├── living-room.webp
│   ├── dining-room.webp
│   ├── kitchen.webp
│   ├── bedroom.webp
│   ├── bathroom.webp
│   └── kids-room.webp
├── editorial/
│   ├── about.webp
│   ├── sustainability.webp
│   └── showroom.webp
├── products/
│   └── (8 product images)
└── common/
    └── card-icons.png
```

### 8.3 CSS
```
css/
├── ferm.css        # Ferm-specific styles
└── fonts.css       # Font-face declarations
```

### 8.4 JavaScript
```
js/
└── ferm.js         # Header, mega menu, USP rotation
```

---

## 9. CONTENT MODEL

### 9.1 Product Card Data
```json
{
  "name": "Product Name",
  "price": "EUR 35,00",
  "price_cents": 3500,
  "compare_at_price": null,
  "image": "url",
  "images": ["url1", "url2"],
  "url": "/products/slug",
  "id": 123,
  "variant_id": 456,
  "colors": [
    {
      "name": "Undyed",
      "hex": "#F2EDE4",
      "secondary_name": "Dark Sand",
      "secondary_hex": "#9F907B"
    }
  ],
  "badges": ["New", "Certified"],
  "certified_reason": "OCS Blended Certified",
  "in_stock": true,
  "inventory": 183
}
```

### 9.2 Category Data
```json
{
  "name": "Furniture",
  "url": "/collections/furniture",
  "image": "url",
  "count": 156
}
```

### 9.3 Navigation Data
```json
{
  "main": [
    {
      "label": "Shop",
      "url": "/collections/all",
      "children": [
        {
          "label": "Furniture",
          "url": "/collections/furniture",
          "children": [...]
        }
      ]
    }
  ]
}
```

---

## 10. REFERENCE DATA REQUIREMENTS

### 10.1 Products (Minimum 25)
The Ferm pack must provide demo products covering:
- Furniture (5+): Sofas, chairs, tables, storage
- Lighting (5+): Portable, floor, table, pendant
- Accessories (5+): Vases, mirrors, hooks
- Kids (5+): Toys, textiles, furniture
- Textiles (3+): Cushions, throws, towels
- Kitchen (2+): Serveware, glasses

### 10.2 Categories (7)
1. Furniture
2. Lighting
3. Accessories
4. Kids
5. Textiles
6. Kitchen
7. Outdoor Living

### 10.3 Blog Posts (3+)
- About Ferm Living
- Sustainability
- Design philosophy

### 10.4 Pages (5+)
- About
- Contact
- FAQ
- Shipping & Returns
- Responsibility

---

## 11. ACCEPTANCE CRITERIA

### 11.1 Visual Parity
- [ ] Typography matches reference (Canela + Teka)
- [ ] Colors match reference (#fffefa + #383838)
- [ ] Spacing matches reference (16px/24px gutters)
- [ ] Layout matches reference (12-col grid)
- [ ] Components match reference (cards, buttons, swatches)

### 11.2 Responsive Parity
- [ ] Mobile (< 768px) matches reference
- [ ] Tablet (768px-1023px) matches reference
- [ ] Desktop (1024px+) matches reference
- [ ] Large desktop (1440px+) matches reference

### 11.3 Interaction Parity
- [ ] Mega menu works correctly
- [ ] Mobile menu works correctly
- [ ] Carousels work correctly
- [ ] Cart drawer works correctly
- [ ] Product swatches work correctly

### 11.4 Content Parity
- [ ] Homepage sections match reference
- [ ] Shop page structure matches reference
- [ ] Product page structure matches reference
- [ ] Footer content matches reference

---

*End of FERM-DESIGN-SPEC.md*
