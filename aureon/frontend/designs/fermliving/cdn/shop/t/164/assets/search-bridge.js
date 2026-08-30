/**
 * search-bridge.js — Minimal search bridge for frozen Ferm HTML.
 *
 * Hooks [data-search] buttons to open a search overlay and submit
 * to WordPress/?s= using the existing FermPageData.config.search_url.
 *
 * Preserves frozen Ferm DOM. No new visual components created —
 * uses existing Ferm CSS classes and design tokens.
 */
(function () {
  'use strict';

  var SEARCH_URL = (window.FermPageData && window.FermPageData.config && window.FermPageData.config.search_url) || '/?s=';

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
        '<form action="' + SEARCH_URL + '" method="get" role="search">' +
          '<div style="display:flex;align-items:center;border-bottom:2px solid #000;padding-bottom:12px;">' +
            '<input type="search" name="s" placeholder="Search" ' +
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
