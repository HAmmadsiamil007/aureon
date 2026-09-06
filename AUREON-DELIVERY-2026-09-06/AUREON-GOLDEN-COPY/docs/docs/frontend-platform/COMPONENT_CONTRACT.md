# AETHER COMPONENT CONTRACT (M2)

> **Status:** FROZEN · **Date:** 2026-08-14 · **Source of truth:** `frontend/manifest/components.php` (ids/templates) + per-file docblocks in `frontend/components/**` + `docs/frontend-platform/DATA_CONTRACT.md`.
> **Rule:** Components consume normalized ViewModels only. They never call WP/WC and never hardcode colors. A design pack shadows a template via pack-first resolution (`aether_resolve_design_path()`); it never edits the base file.

## 1. Component record fields

| Field | Meaning |
|---|---|
| `id` | manifest key (stable) |
| `template` | path relative to `frontend/` |
| `purpose` | one-line role |
| `input` | ViewModel fields consumed (all optional; safe defaults) |
| `states` | loading / added / active / empty / error where applicable |
| `tokens` | CSS custom properties consumed (generic contract names) |
| `js` | behaviors that attach (via whitelist attrs or delegated class hooks) |
| `a11y` | roles/landmarks/alt requirements |

## 2. Component matrix (52)

### Shell (7)
| id | purpose | input | a11y / notes |
|---|---|---|---|
| `shell/preloader` | full-screen loader | `brand`, `name` | hidden when motion disabled |
| `shell/fog` | ambient fog layer | — | decorative |
| `shell/skip-link` | keyboard skip to `#main` | `url` | must be first focusable |
| `shell/announcement` | marquee announcement bar | `items[{text}]` | `role="region"` aria-label |
| `shell/header` | desktop header | HeaderViewModel | nav landmarks |
| `shell/mobile-chrome` | mobile header + drawer | HeaderViewModel, MenuViewModel | aria-expanded drawer |
| `shell/footer` | footer | SiteViewModel | complementary landmark |

### Hero (4)
| id | purpose | input | notes |
|---|---|---|---|
| `hero/slider` | hero carousel container | `slides`, `behavior` | swup-friendly |
| `hero/slide` | single hero slide | HeroViewModel slide fields | alt required |
| `hero/page-title` | page header title | `label,title,subtitle` | used on inner pages |
| `hero/page-banner` | banner with bg image | `label,title,subtitle,image` | — |

### Section (5)
| id | purpose | input | notes |
|---|---|---|---|
| `section/header` | section title block | `label,title,subtitle` | — |
| `section/filter-bar` | shop filter buttons | `buttons[]` | — |
| `section/accordion` | FAQ accordion | `items[{question,answer}]` | button/aria-controls |
| `section/cta` | call-to-action banner | `title,subtitle,cta{label,url}` | — |
| `section/newsletter` | email capture | `title,subtitle` | form/action in section template |
| `section/pagination` | page pager | `pagination{current,total,base}` | — |

### Cards (6)
| id | purpose | input | notes |
|---|---|---|---|
| `card/product` | product card | ProductViewModel(cards) | layouts home/shop; `wc_price` HTML passthrough (documented) |
| `card/category` | category card | CategoryViewModel item | modifiers large/accent |
| `card/blog` | blog card | BlogPostViewModel item | — |
| `card/review` | testimonial card | `items[{name,role,verified,stars,title,quote,date}]` | — |
| `card/team` | team member card | `items[{name,role,bio,image}]` | — |
| `card/wishlist` | wishlist item | ProductViewModel + `remove_url` | — |

### Cart / Checkout / Account (5)
| id | purpose | input | notes |
|---|---|---|---|
| `cart/items` | cart line items | CartViewModel `items` | qty stepper JS |
| `cart/summary` | cart totals | CartViewModel totals + actions | — |
| `checkout/order-items` | thank-you order lines | OrderViewModel | — |
| `account/profile` | account dashboard | AccountViewModel | stats list `{number,label}` |
| `account/orders` | order history | `aether_adapter_account_orders` | — |

### Auth (1)
| id | purpose | input | notes |
|---|---|---|---|
| `auth/password-strength` | strength meter | `strength` | — |

### Order (1)
| id | purpose | input | notes |
|---|---|---|---|
| `order/confirmation` | order confirmation | OrderViewModel | — |

### Commerce (2)
| id | purpose | input | notes |
|---|---|---|---|
| `commerce/rating` | star rating | `rating,rating_text` | aria-label |
| `commerce/quick-view` | quick-view modal | ProductViewModel | dialog a11y |

### Product (8)
| id | purpose | input | notes |
|---|---|---|---|
| `product/breadcrumb` | breadcrumb trail | `breadcrumb` (alias `crumbs`) | nav/aria-label |
| `product/gallery` | image gallery | `gallery[{src}]` | zoom behavior |
| `product/info` | title/price/variant/CTA | SingleProductViewModel core | AJAX add-to-cart |
| `product/sticky-bar` | sticky CTA bar | SingleProductViewModel core | — |
| `product/specs` | spec accordion | `specs[{icon,title,body}]` | — |
| `product/reviews` | reviews block | `reviews_items, reviews_bars, reviews_score, reviews_count` | — |
| `product/related` | related products | ProductViewModel(cards) `items` | — |
| `product/size-guide` | size table | `size_table[{us,eu,uk,cm}]` | table semantics |

### Content (6)
| id | purpose | input | notes |
|---|---|---|---|
| `content/page` | static page body | `content` | the_content passthrough |
| `content/article-hero` | single post hero | ArticleViewModel top | — |
| `content/article-meta` | post meta row | `author,date,category` | — |
| `content/article-body` | post body | `content` | — |
| `content/author-bio` | author box | `author,author_bio,avatar` | — |
| `content/story` | mission/story split | `label,title,subtitle,body,image` | — |

### Forms (5)
| id | purpose | input | notes |
|---|---|---|---|
| `form/contact` | contact form | ContactViewModel `fields,action,nonce` | AJAX handler |
| `form/login` | login form | AuthViewModel | — |
| `form/register` | register form | AuthViewModel | password-strength |
| `form/newsletter` | newsletter form | `aetherAjax` | AJAX handler |
| `form/forgot-password` | reset form | AuthViewModel | — |

### Utility (2)
| id | purpose | input | notes |
|---|---|---|---|
| `error/404` | 404 panel | `title,subtitle,url` | — |
| `soon/countdown` | coming-soon countdown | `target` | countdown JS |

## 3. Contract rules for design packs

1. **Reuse > extend > new reusable component.** A new design maps its UI to existing ids first; only genuinely new UI becomes a new manifest id (via `aether_component_manifest` filter) — never a fork of an existing id's meaning.
2. **Shadow, don't edit:** pack template at the same relative path wins (pack-first); base files stay frozen.
3. **Markup is pack-owned, data is contract-owned.** A pack changes classes/structure freely — it may NOT change what fields a component reads from the ViewModel.
4. **JS hooks:** behavior attrs come from the whitelist (`aether_behavior_attrs()`); additional pack behaviors attach via delegated data-attributes, never by editing base `main.js`.
5. **Colors:** only CSS custom properties (contract names like `--gold`, `--surface`); hex gate in verify.sh.