/**
 * Vineta Path Bridge — rewrites frozen-HTML links to WordPress permalinks.
 *
 * The generic ferm-page.php rewriter handles Shopify-style paths
 * (collections/, products/, blogs/). Vineta uses flat file names
 * (shop-default.html, product-detail.html, etc.) that need their own
 * mapping. This script runs on DOMContentLoaded AFTER the Ferm rewriter
 * and fixes any links the generic rewriter didn't handle correctly.
 *
 * @package Aureon/Vineta
 */
(function () {
  'use strict';

  var S = (window.vineta_bridge && window.vineta_bridge.site_url) ? window.vineta_bridge.site_url.replace(/\/$/, '') : '';

  // Vineta flat-file → WordPress permalink map.
  var MAP = {
    // Shop pages
    'shop-default.html':                '/shop/',
    'shop-left-sidebar.html':           '/shop/',
    'shop-right-sidebar.html':          '/shop/',
    'shop-horizontal-filter.html':      '/shop/',
    'shop-filter-drawer.html':          '/shop/',
    'shop-collection-list.html':        '/shop/',
    'shop-sub-collection.html':         '/shop/',
    'shop-sub-collection-02.html':      '/shop/',
    'shop-grid-3-columns.html':         '/shop/',
    'shop-fullwidth.html':              '/shop/',
    'shop-load-more-button.html':       '/shop/',
    'shop-infinity-scroll.html':        '/shop/',
    'shop-filter-sidebar.html':         '/shop/',
    'shop-filter-hidden.html':          '/shop/',
    'shop-collection.html':             '/shop/',

    // Product pages (generic — individual products mapped via slug)
    'product-style-01.html':            '/shop/',
    'product-style-02.html':            '/shop/',
    'product-detail.html':              '/shop/',
    'product-left-sidebar.html':        '/shop/',
    'product-full-width.html':          '/shop/',
    'product-360-view.html':            '/shop/',
    'product-video.html':               '/shop/',
    'product-sticky-add-cart.html':     '/shop/',

    // Blog pages
    'blog-grid-01.html':                '/blog/',
    'blog-grid-02.html':                '/blog/',
    'blog-grid-03.html':                '/blog/',
    'blog-left-sidebar.html':           '/blog/',
    'blog-right-sidebar.html':          '/blog/',
    'blog-list.html':                   '/blog/',
    'blog-single.html':                 '/blog/',
    'blog-masonry.html':                '/blog/',
    'blog-standard.html':               '/blog/',

    // Cart / Checkout
    'view-cart.html':                   '/cart/',
    'cart.html':                        '/cart/',
    'checkout.html':                    '/checkout/',
    'order-complete.html':              '/checkout/',

    // Account
    'account-page.html':                '/my-account/',
    'login.html':                       '/my-account/',
    'register.html':                    '/my-account/',
    'forgot-password.html':             '/my-account/',
    'order-tracking.html':              '/my-account/',

    // Static pages
    'about-us.html':                    '/about-us/',
    'contact-us.html':                  '/contact-us/',
    'faq.html':                         '/faq/',
    'shipping.html':                    '/shipping/',
    'return-and-refund.html':           '/return-and-refund/',
    'privacy-policy.html':              '/privacy-policy/',
    'term-and-condition.html':          '/term-and-condition/',
    'store-location.html':              '/store-location/',
    'cookies.html':                     '/cookies/',
    'wishlist.html':                    '/my-account/',
    'compare.html':                     '/shop/',
    'coming-soon.html':                 '/coming-soon/',
    'error-404.html':                   '/404/',
  };

  function rewriteValue(val) {
    if (!val) return val;
    // Skip anchors, absolute URLs, mailto, tel, javascript
    if (val.charAt(0) === '#' || val.indexOf('http') === 0 ||
        val.indexOf('mailto:') === 0 || val.indexOf('tel:') === 0 ||
        val.indexOf('javascript:') === 0) return val;
    // Skip WordPress admin/API paths (already correct)
    if (val.indexOf('/wp-') === 0 || val.indexOf('/cart') === 0 ||
        val.indexOf('/checkout') === 0 || val.indexOf('/my-account') === 0 ||
        val.indexOf('/product/') === 0 || val.indexOf('/shop') === 0 ||
        val.indexOf('/blog') === 0) return val;
    // Skip pure anchors or empty
    if (val === '/' || val === '#') return val;

    // Strip leading ../ or ./
    var clean = val.replace(/^\.+\//, '');
    // Look up in map
    if (MAP[clean]) {
      return S + MAP[clean];
    }
    // Fallback: strip .html, prepend site root
    return S + '/' + clean.replace(/\.html$/, '');
  }

  function rewriteLinks() {
    // Rewrite <a href> links
    document.querySelectorAll('a[href]').forEach(function (a) {
      var h = a.getAttribute('href');
      var rewritten = rewriteValue(h);
      if (rewritten !== h) a.href = rewritten;
    });

    // Rewrite <form action> links (checkout, login, register, etc.)
    document.querySelectorAll('form[action]').forEach(function (f) {
      var action = f.getAttribute('action');
      var rewritten = rewriteValue(action);
      if (rewritten !== action) f.action = rewritten;
    });
  }

  // Run after DOMContentLoaded (after the Ferm rewriter)
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', rewriteLinks);
  } else {
    rewriteLinks();
  }

  // Also observe for dynamically added links (Ferm rewriter may add some)
  if (typeof MutationObserver !== 'undefined') {
    var obs = new MutationObserver(function (mutations) {
      mutations.forEach(function (m) {
        m.addedNodes.forEach(function (n) {
          if (n.querySelectorAll) rewriteLinks();
        });
      });
    });
    obs.observe(document.documentElement, { childList: true, subtree: true });
  }
})();
