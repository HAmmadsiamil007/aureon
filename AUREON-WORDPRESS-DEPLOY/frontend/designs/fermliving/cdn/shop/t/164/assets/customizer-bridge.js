/**
 * Customizer Bridge — Ferm Living
 *
 * Consumes FermPageData.customizer and updates the frozen Ferm DOM
 * with WordPress Customizer values. Only modifies elements when
 * Customizer values are present and non-empty.
 *
 * Architecture:
 *   Frozen Ferm HTML (default content)
 *       ↓
 *   FermPageData.customizer (Customizer values)
 *       ↓
 *   This bridge (updates DOM where values differ)
 *
 * @package Aureon
 */
(function () {
  "use strict";

  var d = window.FermPageData && FermPageData.customizer;
  if (!d || typeof d !== "object") return;

  // --- Logo ---
  // Server-side provides FermPageData.customizer.site.logo_url
  // (Custom logo → demo logo → site name text).
  // Client-side: update the logo element if a URL is provided.
  if (d.site && d.site.logo_url) {
    var logoImg = document.querySelector(
      '[data-logo],[class*=header] img[class*=logo],[class*=logo] img,.logo img'
    );
    if (logoImg && logoImg.tagName === 'IMG') {
      logoImg.src = d.site.logo_url;
      logoImg.alt = d.site.name || 'Logo';
      logoImg.style.display = '';
    }
  }

  // --- Announcement bar items ---
  // The announcement bar rotates through items in a marquee/ticker.
  // If Customizer provides announcement items, inject them.
  if (d.announcement && d.announcement.length) {
    var ticker = document.querySelector(
      "[data-announcement-ticker],[data-ticker-items],[class*=announcement] [class*=ticker]"
    );
    if (ticker) {
      // Build replacement items from Customizer data.
      var items = d.announcement
        .map(function (item) {
          var text = item.text || item;
          if (typeof text !== "string") return "";
          return '<span class="ticker__item">' + text + "</span>";
        })
        .filter(Boolean)
        .join("");
      if (items) {
        // Preserve structure, replace content.
        var existingItems = ticker.querySelectorAll(
          ".ticker__item,span[data-ticker-item]"
        );
        if (existingItems.length === d.announcement.length) {
          // Same count — update text only (safe, no layout shift).
          existingItems.forEach(function (el, i) {
            var text = d.announcement[i].text || d.announcement[i];
            if (typeof text === "string" && text) {
              el.textContent = text;
            }
          });
        }
      }
    }
  }

  // --- Newsletter heading/subtitle ---
  if (d.newsletter) {
    if (d.newsletter.heading) {
      var nlHeading = document.querySelector(
        "[data-newsletter-heading],[class*=newsletter] [class*=heading],[class*=newsletter] h2,[class*=newsletter] h3"
      );
      if (nlHeading) nlHeading.textContent = d.newsletter.heading;
    }
    if (d.newsletter.subtitle) {
      var nlSub = document.querySelector(
        "[data-newsletter-subtitle],[class*=newsletter] [class*=subtitle],[class*=newsletter] p"
      );
      if (nlSub) nlSub.textContent = d.newsletter.subtitle;
    }
  }

  // --- Social links ---
  if (d.social && d.social.length) {
    var socialLinks = document.querySelectorAll(
      "[data-social-links] a,[class*=footer] [class*=social] a"
    );
    if (socialLinks.length) {
      // Map social items by label (case-insensitive).
      var socialMap = {};
      d.social.forEach(function (s) {
        if (s.label && s.url) {
          socialMap[s.label.toLowerCase()] = s.url;
        }
      });
      socialLinks.forEach(function (a) {
        var label = (a.textContent || "").trim().toLowerCase();
        var href = a.getAttribute("href") || "";
        // Match by label or existing URL pattern.
        Object.keys(socialMap).forEach(function (key) {
          if (
            label.indexOf(key) !== -1 ||
            href.indexOf(key) !== -1 ||
            key.indexOf(label) !== -1
          ) {
            a.href = socialMap[key];
          }
        });
      });
    }
  }

  // --- Footer columns ---
  // Footer content is structural — we only update links, not rebuild DOM.
  // This is intentionally minimal to preserve the frozen presentation.
  if (d.footer && d.footer.length) {
    var footerSections = document.querySelectorAll(
      "[data-footer-columns] [data-footer-column],[class*=footer] [class*=column]"
    );
    if (footerSections.length === d.footer.length) {
      footerSections.forEach(function (section, i) {
        var col = d.footer[i];
        if (!col) return;
        // Update heading if present.
        var heading = section.querySelector(
          "h3,h4,[class*=heading],[class*=title]"
        );
        if (heading && col.heading) heading.textContent = col.heading;
        // Update links if present.
        if (col.links && col.links.length) {
          var links = section.querySelectorAll("a");
          col.links.forEach(function (link, j) {
            if (links[j] && link.label && link.url) {
              links[j].textContent = link.label;
              links[j].href = link.url;
            }
          });
        }
      });
    }
  }

  // --- Hero fallback ---
  // If Customizer or demo provides hero slides, update the frozen DOM.
  // Demo hero data is already merged server-side into FermPageData.customizer.hero.
  if (d.hero && d.hero.length) {
    var heroSection = document.querySelector(
      '[data-hero],[data-section-type=hero],[class*=hero],[class*=slideshow]'
    );
    if (heroSection) {
      var heroSlide = d.hero[0]; // Primary slide.
      if (heroSlide) {
        // Update headline.
        var headline = heroSection.querySelector(
          '[data-hero-headline],[class*=hero] [class*=headline],[class*=hero] h1,[class*=hero] h2'
        );
        if (headline && heroSlide.title) headline.textContent = heroSlide.title;

        // Update subline.
        var subline = heroSection.querySelector(
          '[data-hero-subline],[class*=hero] [class*=subline],[class*=hero] p'
        );
        if (subline && heroSlide.subtitle) subline.textContent = heroSlide.subtitle;

        // Update hero image.
        var heroImg = heroSection.querySelector(
          '[data-hero-image] img,[class*=hero] img,[class*=slideshow] img'
        );
        if (heroImg && heroSlide.image) {
          heroImg.src = heroSlide.image;
          heroImg.alt = heroSlide.title || '';
        }
      }
    }
  }

  // --- Heading fallback ---
  // If Customizer or demo provides a heading, update the site heading element.
  if (d.heading) {
    var headingEl = document.querySelector(
      '[data-site-heading],[class*=header] [class*=site-name],[class*=logo] [class*=text]'
    );
    if (headingEl) headingEl.textContent = d.heading;
  }

  // --- Categories title/label ---
  if (d.categories && d.categories.length) {
    // Category items are in the frozen HTML — update only text labels.
    var catTitle = document.querySelector(
      '[data-categories-title],[class*=categories] [class*=heading],[class*=categories] h2'
    );
    // Don't overwrite — categories title comes from aether_categories_title.
  }

  // --- Color tokens → CSS custom properties ---
  // Only inject when AETHER CSS is suppressed (complete-page mode).
  // This allows the frozen Ferm CSS to use var(--aureon-*) if desired.
  if (d.colors) {
    var root = document.documentElement;
    var colorMap = {
      bg: "--aureon-color-bg",
      surface: "--aureon-color-surface",
      text: "--aureon-color-text",
      muted: "--aureon-color-muted",
      accent: "--aureon-color-accent",
      accent_hover: "--aureon-color-accent-hover",
      border: "--aureon-color-border",
    };
    Object.keys(colorMap).forEach(function (key) {
      if (d.colors[key]) {
        root.style.setProperty(colorMap[key], d.colors[key]);
      }
    });
  }

  // --- Font tokens → CSS custom properties ---
  if (d.fonts) {
    var root2 = document.documentElement;
    if (d.fonts.heading) {
      root2.style.setProperty("--aureon-font-heading", d.fonts.heading);
    }
    if (d.fonts.body) {
      root2.style.setProperty("--aureon-font-body", d.fonts.body);
    }
  }

  // ========================================================================
  // REMOTE DEMO ASSET RUNTIME FALLBACK
  // ========================================================================
  // Handles failed remote demo images at runtime.
  // Required invariant:
  //   REMOTE DEMO ASSET
  //     → LOAD OK: use remote
  //     → LOAD FAIL: use fallback, no broken-image icon, no fatal error
  //
  // This is critical for InfinityFree and shared hosting where external
  // URLs may be slow, blocked, or unavailable.
  // ========================================================================
  var DEMO_PLACEHOLDER = 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSIjZjVmMGU4Ii8+PHRleHQgeD0iNTAlIiB5PSI1MCUiIGZvbnQtZmFtaWx5PSJzYW5zLXNlcmlmIiBmb250LXNpemU9IjE0IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIiBkeT0iLjNlbSIgZmlsbD0iIzg4OCI+TG9hZGluZy4uLjwvdGV4dD48L3N2Zz4=';

  // Fallback handler for all images on the page.
  // Detects failed loads and replaces with a safe neutral placeholder.
  function fermHandleImageFallback(img) {
    if (!img || img.getAttribute('data-fallback-applied') === 'true') return;

    var src = img.getAttribute('src') || '';
    if (!src || src.indexOf('data:') === 0) return; // Already a data URI.

    img.setAttribute('data-fallback-applied', 'true');
    img.style.objectFit = 'contain';
    img.style.backgroundColor = '#f5f0e8';
    img.src = DEMO_PLACEHOLDER;
  }

  // Attach fallback to all current images.
  document.querySelectorAll('img').forEach(function(img) {
    img.addEventListener('error', function() {
      fermHandleImageFallback(this);
    });
  });

  // MutationObserver: catch any dynamically added images.
  var fallbackObserver = new MutationObserver(function(mutations) {
    mutations.forEach(function(mutation) {
      mutation.addedNodes.forEach(function(node) {
        if (!node.querySelectorAll) return;
        node.querySelectorAll('img').forEach(function(img) {
          img.addEventListener('error', function() {
            fermHandleImageFallback(this);
          });
        });
      });
    });
  });
  fallbackObserver.observe(document.documentElement, {
    childList: true,
    subtree: true
  });
})();
