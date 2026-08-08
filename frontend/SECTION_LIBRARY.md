# SECTION_LIBRARY

**Phase:** 17 — Frontend Integration Framework (Step 3: Component Extraction)
**Date:** 2026-08-06
**Status:** Complete — section composition mapped per page

---

## 1. Section Anatomy

A **section** is a page-level block composed of components. Sections map to Aureon template parts (front-page, archive, single, page, Woo templates) and are rendered from a `$sectionData` array assembled by the section library engine.

**Section contract:**

```php
$sectionData = array(
    'id'         => 'aether/home/hero',
    'components' => array( /* component IDs + their data */ ),
    'layout'     => array( 'class' => 'hero-slider', 'id' => 'heroSlider' ),
    'behavior'   => array( 'parallax' => true, 'mouse-parallax' => true, 'swiper' => array(...) ),
    'attrs'      => array( 'data-phantom-bg' => 'hero' ),
);
```

---

## 2. Section Register

### 2.1 Shared/Global Sections (all 22 pages)

| ID | Section | Components | Notes |
|---|---|---|---|
| global/preloader | #preloader | shell/preloader | fade-out on load |
| global/fog | #fog-system | shell/fog | 3 layers, fixed |
| global/mobile-chrome | mobile header + overlay | shell/mobile-header, shell/mobile-menu | — |
| global/announcement | announcement bar | shell/announcement | — |
| global/header | sticky header | shell/header, nav/menu, nav/search, nav/mini-cart | `data-phantom-menu` |
| global/footer | footer | shell/footer, form/newsletter | 19 newsletter instances |
| global/back-to-top | back-to-top | shell/back-to-top | — |

### 2.2 Page Sections

#### home-page (index.html) — 6 sections
| Order | Section | Components | Behavior |
|---|---|---|---|
| 1 | `hero-slider` | hero/slider + hero/slide | Swiper, mouse-parallax, parallax-section, motion-text |
| 2 | `categories` | section/header + card/category | reveal-group, motion-text |
| 3 | `bestsellers` | section/header + card/product-slider | Swiper, section-cta |
| 4 | `reviews` | section/header + card/review | Swiper |
| 5 | `faq-section` | section/header + block/accordion | motion-text |
| 6 | `newsletter-section` | section/header + form/newsletter | — |

#### shop-page (shop.html) — 4 sections
| Order | Section | Components | Behavior |
|---|---|---|---|
| 1 | `page-hero` | hero/page-title | parallax-section |
| 2 | `filter-bar` | block/filter (price, category, sort) | — |
| 3 | `shop-grid-section` | card/product ×12 + block/pagination | reveal-item, tilt |
| 4 | `newsletter-section` | form/newsletter | — |

#### product-page (product-detail.html) — 5 sections
| Order | Section | Components | Behavior |
|---|---|---|---|
| 1 | `pd-hero` | product/gallery, product/meta, product/qty, card/order-item | image-zoom, tilt, sticky bar |
| 2 | `pd-specs` | product/tabs, product/rating | — |
| 3 | `pd-reviews` | section/header + card/review | — |
| 4 | `pd-related` | section/header + card/product-slider | Swiper |
| 5 | `newsletter-section` | form/newsletter | — |

#### cart-page (cart.html) — 3 sections
| Order | Section | Components | Behavior |
|---|---|---|---|
| 1 | `page-hero` | hero/page-title | parallax-section |
| 2 | `cart-section` | cart/table, card/cart-item, checkout/summary | — |
| 3 | `newsletter-section` | form/newsletter | — |

#### checkout-page (checkout.html) — 2 sections
| Order | Section | Components | Behavior |
|---|---|---|---|
| 1 | `page-hero` | hero/page-title | parallax-section |
| 2 | `checkout-section` | checkout/form, checkout/summary, card/order-item | — |

#### wishlist-page (wishlist.html) — 5 sections
| 1 | `page-hero` | hero/page-title | parallax-section |
| 2 | `wishlist-grid` | card/product ×4 | tilt, reveal-item |
| 3 | `newsletter-section` | form/newsletter | — |

#### login-page / register-page — 1 section each
| 1 | `auth-section` | form/login or form/register, block/auth-footer | — |

#### account-page (account.html) — 0 `<section>` (div-based dashboard)
| `account-shell` | nav/account-menu, content/account-overview | — |

#### thankyou-page (thank-you.html) — 4 sections
| 1 | `page-hero` | hero/page-title | — |
| 2 | `order-confirmation` | order/success | — |
| 3 | `order-summary` | card/order-item | — |
| 4 | `newsletter-section` | form/newsletter | — |

#### blog-page (blog.html) — 5 sections
| 1 | `page-hero` | hero/page-title | parallax-section |
| 2 | `blog-header` | block/category-nav | — |
| 3 | `blog-grid` | card/blog-card ×9 | reveal-item |
| 4 | `pagination` | block/pagination | — |
| 5 | `newsletter-section` | form/newsletter | — |

#### single-blog-page (single-blog.html) — 8 sections
| 1 | `page-hero` | hero/page-title | parallax-section |
| 2 | `article-hero` | article/meta, block/image | — |
| 3 | `article-body` | article/body, article/quote | — |
| 4 | `article-author` | author_bio block | — |
| 5 | `article-nav` | prev/next post | — |
| 6 | `related-posts` | card/blog-card ×3 | — |
| 7 | `newsletter-section` | form/newsletter | — |

#### faq-page (faq.html) — 5 sections
| 1 | `page-hero` | hero/page-title | parallax-section |
| 2 | `faq-categories` | block/tab-nav | — |
| 3 | `faq-list` | block/accordion | — |
| 4 | `faq-cta` | block/cta (`faq_cta*`) | — |
| 5 | `newsletter-section` | form/newsletter | — |

#### testimonials-page (testimonials.html) — 6 sections
| 1 | `page-hero` | hero/page-title | parallax-section |
| 2 | `rating-overview` | product/rating aggregate | — |
| 3 | `testimonials-grid` | card/review ×6 | reveal-item |
| 4 | `reviews-cta` | block/cta (`review_cta*`) | — |
| 5 | `newsletter-section` | form/newsletter | — |

#### team-page (team.html) — 9 sections
| 1 | `page-hero` | hero/page-title | parallax-section |
| 2 | `mission` | block/story | — |
| 3 | `team-grid` | card/team ×6 | tilt, reveal-item |
| 4 | `values` | block/feature-grid | — |
| 5 | `join-cta` | block/cta | — |
| 6 | `newsletter-section` | form/newsletter | — |

#### contact-page (contact.html) — 5 sections
| 1 | `page-hero` | hero/page-title | parallax-section |
| 2 | `contact-info` | block/info-cards | — |
| 3 | `contact-form` | form/contact | — |
| 4 | `map` | block/map | — |
| 5 | `newsletter-section` | form/newsletter | — |

#### legal pages (3) — 3-4 sections
| 1 | `page-hero` | hero/page-title | — |
| 2 | `legal-content` | content/page (`page_content`, `effective_date`) | — |
| 3 | `newsletter-section` | form/newsletter | — |

#### error-page (404.html) — 3 sections
| 1 | `error-hero` | hero/page-title | — |
| 2 | `error-content` | error/404 (`error_code`) | — |
| 3 | `newsletter-section` | form/newsletter | — |

#### coming-soon-page (coming-soon.html) — 1 section
| 1 | `coming-soon` | soon/countdown, form/notify (`notify_form`, `notify_button`) | — |

---

## 3. Newsletter Recurrence (integration risk)

`newsletter-section` appears on **every page** (19 footer instances + 2 inline). **Recommendation:** render once in the global footer; the standalone `#newsletter` sections on home/shop/blog are optional toggles via Customizer.

---

## 4. Section → WP Template Mapping

| Section ID | WP Template |
|---|---|
| hero-slider | front-page (theme front-page.php) |
| categories / bestsellers / reviews / faq | front-page or Page Builder sections |
| page-hero | all templates via structure/header |
| shop-grid / filter-bar | WooCommerce shop archive |
| pd-hero / pd-specs / pd-reviews / pd-related | WooCommerce single product |
| cart-section | WooCommerce cart |
| checkout-section | WooCommerce checkout |
| auth-section | WooCommerce my-account / login |
| blog-grid | home (posts) / archive |
| article-* | single post |
| order-confirmation | WooCommerce order-received |
| coming-soon | maintenance mode |

---

## 5. Section Registration API (target)

Sections are registered in the framework like:

```php
aether_register_section( 'hero-slider', array(
    'template'  => 'sections/hero-slider.php',
    'render'    => 'aether_render_hero_slider',   // adapter-assembling renderer
    'behavior'  => array( 'swiper' => true ),
    'areas'     => array( 'front-page', 'customize' ),
) );
```

The section library engine (Step 7 build) resolves `$sectionData` → renders components → injects behavior attributes → enqueues behavior JS once.