# Ferm Living Visual Parity Progress

## Date: 2026-08-25

## Status: Active - AETHER content cleanup complete

## Key Architecture Rules
- Ferm pack = client frontend only; AETHER/AUREON = engine/data/contracts
- No `if ($design === 'fermliving')` in shared core code
- Filters in composer.php override adapter data at runtime
- Docker container: `aureon_wp`, mounts: `C:\Users\hamma\Downloads\wordpress\aureon\plugin` (frontend), `C:\Users\hamma\Downloads\wordpress\aureon\theme` (theme)

## Completed This Session
1. **adapter-contact.php** - Fixed `$data` undefined bug, added `aether_adapter_contact_data` filter
2. **Contact address** - Now shows "Ferm Living ApS, Refshalevej 163A, 1432 Copenhagen K, Denmark" instead of "123 Innovation Drive, SF"
3. **Contact hours** - Now shows "Mon—Fri 9am—5pm CET" instead of "Mon—Fri 9am—6pm PST"
4. **Blog section title** - "From Ferm Living" (was "The AETHER Dispatch")
5. **Blog post titles** - "The Art of Simple Living" / "Introducing Ferm Living" (was "AETHER Sample Post" / "Hello world!")
6. **Blog post excerpts** - Replaced AETHER text with Ferm Living-appropriate content
7. **adapter-blog.php** - Added `aether_adapter_blog_data` filter hook
8. **Blog card data filter** - `ferm_living_blog_adapter_filter` in composer.php

## Critical Finding: Filter Ordering
- `the_title` filter (priority 999) runs BEFORE adapter output filter
- So adapter data already has replaced titles when `aether_adapter_blog_data` fires
- Must match on REPLACED titles, not original AETHER titles
- Pattern: `'The Art of Simple Living'` not `'AETHER Sample Post — Step Into the Void'`

## Remaining Known Issues
- Shop page category tags show AETHER DB categories (Men's Boots etc.)
- Editorial split images (kids.webp, storage.webp, sustainability.webp) not rendering
- `aetherAjax` JS variable name contains "aether" (engine-level, not visible)
- No blog post featured images
