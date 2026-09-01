/**
 * search-bridge.js — Search bridge for frozen Ferm HTML.
 *
 * Hooks [data-search] buttons to open a search overlay and submit
 * to WordPress/?s= using the existing FermPageData.config.search_url.
 *
 * For demo mode (no real WC products), provides instant client-side
 * search across demo products from FermPageData.demoProducts.
 *
 * Preserves frozen Ferm DOM. No new visual components created —
 * uses existing Ferm CSS classes and design tokens.
 */
(function () {
  'use strict';

  var SEARCH_URL = (window.FermPageData && window.FermPageData.config && window.FermPageData.config.search_url) || '/?s=';

  // ========================================================================
  // DEMO PRODUCT SEARCH
  // When no real WooCommerce products exist, provide instant search
  // across demo products loaded from the client-pack JSON.
  // ========================================================================
  var demoProducts = [];
  (function loadDemoProducts() {
    // Try to get demo products from FermPageData.collection first.
    var pd = window.FermPageData;
    if (pd && pd.collection && pd.collection.products && pd.collection.products.length > 0) {
      demoProducts = pd.collection.products;
      return;
    }
    // Fallback: try to load from a demo-products endpoint.
    // This is a no-op in standalone mode — demo products come from the bridge.
  })();

  function searchDemoProducts(query) {
    if (!query || query.length < 2) return [];
    var q = query.toLowerCase();
    return demoProducts.filter(function(p) {
      var title = (p.title || '').toLowerCase();
      var handle = (p.handle || '').toLowerCase();
      return title.indexOf(q) !== -1 || handle.indexOf(q) !== -1;
    }).slice(0, 12);
  }

  function renderSearchResults(results, query) {
    var container = document.getElementById('ferm-search-results');
    if (!container) return;
    container.innerHTML = '';

    if (results.length === 0) {
      container.innerHTML = '<p style="text-align:center;color:#666;padding:24px 0;">No results found for "' + query + '"</p>';
      return;
    }

    var grid = document.createElement('div');
    grid.style.cssText = 'display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:16px;padding:24px 0;';

    results.forEach(function(product) {
      var card = document.createElement('a');
      card.href = product.url || '#';
      card.style.cssText = 'text-decoration:none;color:inherit;display:block;';

      var imgWrap = document.createElement('div');
      imgWrap.style.cssText = 'aspect-ratio:1/1.33;overflow:hidden;margin-bottom:8px;background:#f5f0e8;';

      var img = document.createElement('img');
      img.src = product.image || '';
      img.alt = product.title || '';
      img.loading = 'lazy';
      img.style.cssText = 'width:100%;height:100%;object-fit:cover;';
      imgWrap.appendChild(img);

      var title = document.createElement('div');
      title.style.cssText = 'font-size:13px;line-height:1.4;margin-bottom:4px;';
      title.textContent = product.title || '';

      var price = document.createElement('div');
      price.style.cssText = 'font-size:11px;text-transform:uppercase;color:#666;';
      price.textContent = product.price_html || '';

      card.appendChild(imgWrap);
      card.appendChild(title);
      card.appendChild(price);
      grid.appendChild(card);
    });

    container.appendChild(grid);
  }

  function createOverlay() {
    if (document.getElementById('ferm-search-overlay')) return;

    var overlay = document.createElement('div');
    overlay.id = 'ferm-search-overlay';
    overlay.setAttribute('role', 'dialog');
    overlay.setAttribute('aria-modal', 'true');
    overlay.setAttribute('aria-label', 'Search');
    overlay.style.cssText = 'position:fixed;inset:0;z-index:99999;background:var(--bg-canvas,#f7f5ef);display:flex;align-items:flex-start;justify-content:center;padding-top:120px;opacity:0;transition:opacity 0.3s ease;';

    overlay.innerHTML =
      '<div style="width:100%;max-width:600px;padding:0 24px;">' +
        '<form id="ferm-search-form" action="' + SEARCH_URL + '" method="get" role="search">' +
          '<div style="display:flex;align-items:center;border-bottom:2px solid #000;padding-bottom:12px;">' +
            '<input type="search" id="ferm-search-input" name="s" placeholder="Search" ' +
              'autofocus autocomplete="off" ' +
              'style="flex:1;border:none;background:transparent;font-size:24px;font-family:inherit;outline:none;color:#000;" />' +
            '<button type="submit" ' +
              'style="border:none;background:transparent;cursor:pointer;padding:8px;" aria-label="Search">' +
              '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">' +
                '<circle cx="11.1589" cy="11.1589" r="6.40893" stroke="currentColor" stroke-width="1.25"/>' +
                '<path d="M19.2508 19.2498L17.3281 17.3271" stroke="currentColor" stroke-width="1.25" stroke-linecap="square" stroke-linejoin="round"/>' +
              '</svg>' +
            '</button>' +
          '</div>' +
        '</form>' +
        '<div id="ferm-search-results"></div>' +
        '<button id="ferm-search-close" type="button" ' +
          'aria-label="Close search" ' +
          'style="position:absolute;top:24px;right:24px;border:none;background:transparent;cursor:pointer;padding:8px;">' +
          '<svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">' +
            '<path d="M1.05078 11L10.9503 1.10051" stroke="currentColor" stroke-width="1.25" stroke-linejoin="round" stroke-linecap="square"/>' +
            '<path d="M1.05078 1L10.9503 10.8995" stroke="currentColor" stroke-width="1.25" stroke-linejoin="round" stroke-linecap="square"/>' +
          '</svg>' +
        '</button>' +
      '</div>';

    document.body.appendChild(overlay);

    // Animate in
    requestAnimationFrame(function () { overlay.style.opacity = '1'; });

    // Focus the input
    var input = overlay.querySelector('input[type=search]');
    if (input) setTimeout(function () { input.focus(); }, 100);

    // --- Demo product live search ---
    // When real WC products exist, the form submits to WordPress search.
    // When only demo products exist, provide instant client-side search.
    if (demoProducts.length > 0) {
      var searchInput = document.getElementById('ferm-search-input');
      var searchForm = document.getElementById('ferm-search-form');
      if (searchInput && searchForm) {
        // Prevent form submission in demo mode — use client-side search.
        searchForm.addEventListener('submit', function(e) {
          e.preventDefault();
          var results = searchDemoProducts(searchInput.value);
          renderSearchResults(results, searchInput.value);
        });

        // Live search on input.
        var debounceTimer;
        searchInput.addEventListener('input', function() {
          clearTimeout(debounceTimer);
          var self = this;
          debounceTimer = setTimeout(function() {
            var results = searchDemoProducts(self.value);
            renderSearchResults(results, self.value);
          }, 200);
        });
      }
    }

    // Close handlers
    var close = function () {
      overlay.style.opacity = '0';
      setTimeout(function () { overlay.remove(); }, 300);
    };

    document.getElementById('ferm-search-close').addEventListener('click', close);
    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) close();
    });
    document.addEventListener('keydown', function handler(e) {
      if (e.key === 'Escape') { close(); document.removeEventListener('keydown', handler); }
    });
  }

  // Delegate click on [data-search] buttons
  document.addEventListener('click', function (e) {
    var btn = e.target.closest('[data-search]');
    if (!btn) return;
    e.preventDefault();
    createOverlay();
  });
})();
