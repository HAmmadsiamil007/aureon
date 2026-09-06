# JavaScript Audit Report

## 1. `main.js` (AETHER — Main JavaScript)
This is the core frontend application script containing all major interactive modules and event listeners.

**Major Functions & Modules:**
*   **Initialization & Preloader:** Handles the preloader progress animation with a robust fallback that removes it regardless of other JS errors. Registers GSAP ScrollTrigger if available.
*   **Header & Navigation:** Includes Smart Sticky Header logic, Mobile Menu (hamburger toggle, slide-out overlay), and dropdown handling.
*   **Sliders (Swiper.js):** Initializes the `hero-swiper` (with autoplay progress bar) and `reviews-swiper`.
*   **AJAX Forms:**
    *   **Newsletter:** Submits to `aether_newsletter_subscribe` via admin-ajax.
    *   **Contact Form:** Submits to a hidden action via admin-ajax and surfaces JSON status.
*   **WooCommerce AJAX integration:**
    *   **Add to Cart:** Intercepts clicks for simple products, posting to `?wc-ajax=add_to_cart` and updating the header cart count dynamically.
    *   **Wishlist:** Posts to `aether_wishlist_toggle` to add/remove products and updates the wishlist counter.
*   **Modals:** Quick View modal (fetches product details via AJAX) and Forgot Password modal.
*   **Product Detail Page (PDP):** Includes logic for gallery swiper thumbnails, color/size selection, quantity increment/decrement, a sticky "add to cart" bar, accordion tabs, and a size guide modal.
*   **Miscellaneous:** Password visibility toggle, password strength indicator, FAQ accordions, and smooth scrolling for anchor links.

## 2. `phantom-bridge.js`
**What is it?**
It is a lightweight utility script defining a global `PhantomBridge` object. It acts as a bridge for common utility functions that might be needed across different scripts or integrations.

**What does it do?**
It provides reusable, environment-agnostic helper functions:
*   `getSetting(key, defaultValue)`: Safely retrieves configuration from a globally localized `window.phantomData.settings` object.
*   `getCookie(name)` / `setCookie(name, value, days)`: Standard cookie getters/setters.
*   `debounce(func, wait)` / `throttle(func, limit)`: Core performance wrappers for rate-limiting rapid events (like scrolling or resizing).
*   `isMobile()`: Quickly checks if the viewport is $\le 768px$.
*   `scrollTo(selector, offset)`: Helper for smooth scrolling to an element.

## 3. `animations.js` (AETHER — Premium Animation System v4.0)
**What it does:**
This file powers the cinematic motion and scroll-based reveals for the entire site. It applies premium animations (sliding, fading, scaling, blurring) to elements as they enter the viewport.
*   **Safety/Accessibility:** Detects `prefers-reduced-motion` and includes a watchdog timer (`MOTION_READY_TIMEOUT`) to prevent content from remaining hidden if the animation libraries fail to load.
*   **Text Reveals:** Splits text into words or lines for staggered, staggered typographic entrances.
*   **Scroll Reveals:** Automatically assigns directional reveals (left/right/up) to cards, section headers, features, and buttons based on DOM structures and grids (`autoAssignReveals`).
*   **Advanced Motion:** Includes image clip reveals, section snapping (parallax and full-page snapping), magnetic hover effects for buttons, image parallax on scroll, and 3D card tilt effects based on mouse position.

**Dependencies:**
*   **GSAP (`gsap`)**: Core animation engine.
*   **ScrollTrigger (`ScrollTrigger`)**: GSAP plugin for scroll-based triggers.

## 4. Load Order (Enqueues)
JavaScript enqueuing is primarily handled in `aureon/frontend/views/assets.php`.

**Asset Pipeline:**
1.  **Platform CDNs:** 
    *   Bootstrap 5.3.3 (`aether-bootstrap-js`)
    *   Swiper 11 (`aether-swiper-js`)
    *   GSAP 3.12.5 (`aether-gsap`)
    *   GSAP ScrollTrigger (`aether-scrolltrigger` — depends on `aether-gsap`)
2.  **Platform Contract JS:**
    *   `animations.js` (`aether-animations`) — *Depends on `aether-bootstrap-js` and `aether-gsap`*
    *   `main.js` (`aether-main`) — *Depends on `aether-animations`*
    *   `countdown.js` (`aether-countdown`)
    *   Localization script (`wp_localize_script` for `aether-main` to pass AJAX/REST URLs and nonces).
3.  **Pack Assets:**
    *   If a specific design pack provides a `manifest.json`, its CSS/JS files are dynamically enqueued *after* the platform scripts. The manifest can specify dependencies to ensure proper order.

*Note: If `aether_is_complete_page_design()` evaluates to true, the platform CDNs and contract JS are entirely skipped, and only the pack's manifest assets are loaded to ensure total design isolation.*

## 5. Duplicate Loading Risks
*   **Bridge vs. Design Pack:** The `assets.php` file actively prevents the Luxury (bridge) assets and standard Design Pack assets from colliding. If a design pack directory is present, it bypasses the bridge's luxury enqueues.
*   **Handle Matching:** The `aether_enqueue_pack_asset` function parses the basename of files loaded via the manifest to generate handles (e.g., `base_name`). This ensures that if a manifest declares a dependency already enqueued by the platform, WordPress's dependency resolution prevents double-loading.
*   **Potential Risk:** If external plugins (like page builders or WooCommerce extensions) blindly enqueue their own versions of `Swiper.js` or `GSAP` without checking for existing handles (`aether-swiper-js`, `aether-gsap`), duplicate libraries may load on the page, leading to conflicts or console errors.

## 6. Known JS Errors
*   No `console-errors.txt` file was found in the project directory, so there are no known JS errors to report at this time.
