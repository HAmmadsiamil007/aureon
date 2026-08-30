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
  // Handled server-side by ferm-page.php (custom_logo → img injection).
  // Client-side: nothing needed here.

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

  // --- Categories title/label ---
  if (d.categories && d.categories.length) {
    // Category items are in the frozen HTML — update only text labels.
    var catTitle = document.querySelector(
      "[data-categories-title],[class*=categories] [class*=heading],[class*=categories] h2"
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
})();
