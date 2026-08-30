/* Lumen design JS (M10 proof pack).
 * Delegated, tiny, contract-safe: header scroll state + IntersectionObserver
 * reveal fallback. All platform behavior (cart, search, newsletter, contact,
 * drawer, motion watchdog) lives in aether-main — never duplicated here. */
(function () {
  'use strict';

  var root = document.documentElement;

  function headerScrollState() {
    var header = document.querySelector('.lumen-header');
    if (!header) return;
    header.classList.toggle('is-scrolled', window.scrollY > 24);
  }

  function initReveals() {
    var els = document.querySelectorAll('[data-lumen-reveal]');
    if (!('IntersectionObserver' in window) || els.length === 0) return;

    var io = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) {
            entry.target.classList.add('is-visible');
            io.unobserve(entry.target);
          }
        });
      },
      { threshold: 0.12 }
    );
    els.forEach(function (el) { io.observe(el); });
  }

  function settleReveals() {
    if (!window.gsap || !window.ScrollTrigger) return;
    // 1) once:true triggers whose start is already above the current scroll
    //    (stale positions from the cold-CDN race) — complete their animation.
    ScrollTrigger.getAll().forEach(function (t) {
      if (t.vars.once && t.animation && t.start <= window.scrollY) {
        t.animation.progress(1, true);
      }
    });
    // 2) Any reveal element sitting in the viewport (first-paint case).
    gsap.utils.toArray('[data-reveal], [data-reveal-item]').forEach(function (el) {
      var r = el.getBoundingClientRect();
      if (r.top < window.innerHeight && r.bottom > 0) {
        gsap.set(el, { clearProps: 'all', autoAlpha: 1 });
      }
    });
  }

  window.addEventListener('load', settleReveals);
  if (window.ScrollTrigger) {
    ScrollTrigger.addEventListener('refresh', settleReveals);
  }
  // Settle window: while lazy images are still shifting layout (≤8s), any
  // reveal element that is in the viewport is force-completed. After that,
  // ScrollTrigger positions are stable and scroll-driven reveals own the
  // page. The platform's rule is content must never be stuck hidden.
  var settleTimer = window.setInterval(settleReveals, 800);
  window.setTimeout(function () { window.clearInterval(settleTimer); }, 8000);

  document.addEventListener('DOMContentLoaded', function () {
    headerScrollState();
    initReveals();
  });

  window.addEventListener('scroll', headerScrollState, { passive: true });
})();