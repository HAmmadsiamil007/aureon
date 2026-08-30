# PHASE 08 — REMAINING SURFACES AUDIT

> **Phase:** 8 · **Date:** 2026-08-14 · **Method:** static (templates + sections) + live HTTP probes (localhost:8080)
> **Scope:** every theme surface not yet deep-audited: static pages (about/team/contact/faq/coming-soon), blog surfaces (home/archive/search/single), 404, plus their section renderers and empty-state behavior
> **Result:** all surfaces bind through `aether_render_component`/`aether_render_section`; 4 findings (1 MED, 3 LOW); no change required

---

## 1. Surface → composition map (verified)

| Surface | Template | Composition | Gating | Live |
|---|---|---|---|---|
| About | `page-about.php` | mission → features → story → stats → team → newsletter | per-section `aether_section_*` options (all default true) | ✅ 200: banner, mission, team, newsletter |
| Team | `page-team.php` | hero/page-title + team + values + newsletter | `aether_section_team` / `aether_section_values` | ✅ 200: "Meet the Team", "Our Values" |
| Contact | `page-contact.php` | hero/page-title + contact + newsletter | `aether_section_contact` | ✅ 200 |
| FAQ | `page-faq.php` | hero/page-title + faq (`cta_url → /contact/`) + newsletter | `aether_section_faq` | ✅ 200 |
| Coming soon | `page-coming-soon.php` | coming-soon (countdown hero only) | `aether_section_coming_soon` | ✅ 200 |
| Blog index | `home.php` | hero/page-title + blog-grid + newsletter | newsletter opt | ✅ 200 |
| Archive | `archive.php` | hero/page-title (real `get_the_archive_title/description`) + blog-grid + newsletter | newsletter opt | ✅ 200 |
| Search | `search.php` | hero/page-title ("Results for …") + blog-grid (`s` passthrough) + newsletter | newsletter opt | ✅ 200 (empty results) |
| Single post | `single.php` | blog-single (`post_id`) + related blog-grid (3 posts, `post__not_in`, `category_name`) + newsletter | newsletter opt | — |
| 404 | `404.php` | error/404 + newsletter | newsletter opt | ✅ 404 status (correct) |
| Register/Login | `page-register.php`, `page-login.php` | AETHER-styled forms | — | — |
| Styleguide | `page-styleguide.php` | token demo page (consumes `--muted`, `--surface-2/3`) | — | — |

## 2. Demo-gate verification on remaining surfaces

`aether_demo_content` gate confirmed at all adapter fallback sites (F3-6 carry-over): `adapter-wc-products:123`, `adapter-wc-categories:51`, `adapter-testimonials:37`, `adapter-team:35`, `adapter-product:49/145/163`, `adapter-faq:34`. Content adapters (team/testimonials/faq) read their CPTs first — real content wins; demo only when empty AND gate on.

## 3. Findings

| ID | Sev | Finding |
|---|---|---|
| F8-1 | MED | **No graceful empty state on blog surfaces.** `section-blog-grid.php:23-25` silently returns when `items` AND `paged` are both empty — verified live: `/?s=sneaker` renders hero + newsletter but the blog grid vanishes with no message (no "no results", no CTA). Same for `home.php`/`archive.php` when no posts exist. Contrast: cart has a premium empty state. This is the visible half of the G11 gap (blog module neutralized at frontend.php:54-101; `adapter-blog` returns empty). Fix candidate for Phase 12-15: render a minimal empty-state component when empty (demo-gated, mirroring cart). |
| F8-2 | LOW | **Static-page heroes are hardcoded strings** (`page-faq.php:19-24`, `page-contact.php`, `page-team.php`, `home.php`, `404.php`) — not option-driven, not demo-gated (carry-over of F3-1 for FAQ/team/contact heroes). Content is design-copy, fine for now; flag only. |
| F8-3 | LOW | `single.php` related-posts grid passes `posts_per_page=3` + `post__not_in` — with zero posts (blog empty) the grid silently disappears (same F8-1 mechanism); the "Continue Reading" heading never shows. Cosmetic until blog content exists. |
| F8-4 | LOW | `search.php` has no `no-results.php` fallback wiring — `no-results.php` template exists in the theme but the blog-grid section never invokes it (section returns early instead). Dead template file. |

## 4. Verdict

All remaining surfaces are pure section composition with consistent per-section option gating and correct routing (404 → 404 template + status). The only functional gap is the blog surfaces' missing empty state (F8-1), which is the visible counterpart of G11 and belongs in the Phase 12-15 change-gate work. No immediate change required.