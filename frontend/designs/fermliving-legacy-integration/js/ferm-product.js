/**
 * Ferm Living — Product Page JS (Frozen DOM)
 *
 * Initializes frozen Ferm product components:
 *   - Embla carousel (gallery)
 *   - Quantity stepper
 *   - Accordion toggle
 *   - Sticky add-to-cart bar
 *   - Back button
 *   - Read-more toggle
 *   - Delivery estimate
 *
 * Reads data from window.FermPageData (injected by section-product.php).
 * No Shopify API calls — all cart operations go through ferm-cart-bridge.js.
 *
 * @package Aureon\Designs\FermLiving
 */
(function () {
  'use strict';

  var FDP = window.FermPageData || {};

  /* ── Helpers ──────────────────────────────────────────────── */
  function qs(sel, ctx) { return (ctx || document).querySelector(sel); }
  function qsa(sel, ctx) { return Array.from((ctx || document).querySelectorAll(sel)); }

  /* ── 1. Embla Carousel (Gallery) ──────────────────────────── */
  function initGallery() {
    var el = qs('[data-component="productImages"]');
    if (!el) return;

    var container = qs('.embla__container', el);
    if (!container) return;

    var slides = qsa('.embla__slide', el);
    var bullets = qsa('[data-bullet]', el);
    var current = 0;
    var total = slides.length;

    function goTo(idx) {
      if (idx < 0 || idx >= total) return;
      current = idx;

      /* On mobile: scroll horizontally. On desktop: scroll vertically via flex-col */
      var isDesktop = window.matchMedia('(min-width: 768px)').matches;

      slides.forEach(function (slide, i) {
        if (isDesktop) {
          /* Desktop: slides stack vertically, show/hide */
          slide.style.display = (i === idx) ? '' : 'none';
        } else {
          /* Mobile: scroll into view */
          slide.style.display = '';
          if (i === idx) {
            slide.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'start' });
          }
        }
      });

      bullets.forEach(function (b, i) {
        b.classList.toggle('opacity-100', i === idx);
        b.classList.toggle('opacity-40', i !== idx);
      });
    }

    /* Bullet clicks */
    bullets.forEach(function (b, i) {
      b.addEventListener('click', function () { goTo(i); });
    });

    /* Touch swipe on mobile */
    var startX = 0;
    var isDragging = false;
    el.addEventListener('touchstart', function (e) {
      startX = e.touches[0].clientX;
      isDragging = true;
    }, { passive: true });

    el.addEventListener('touchend', function (e) {
      if (!isDragging) return;
      isDragging = false;
      var diff = startX - e.changedTouches[0].clientX;
      if (Math.abs(diff) > 50) {
        if (diff > 0 && current < total - 1) goTo(current + 1);
        else if (diff < 0 && current > 0) goTo(current - 1);
      }
    }, { passive: true });

    /* Initialize: show first slide */
    goTo(0);

    /* Expose for variant image switching */
    el._fermGoTo = goTo;
    el._fermCurrent = function () { return current; };
  }

  /* ── 2. Quantity Stepper ──────────────────────────────────── */
  function initQuantity() {
    var container = qs('[data-quantity-container]');
    if (!container) return;

    var input = qs('[data-quantity]', container);
    var decBtn = qs('[data-decrease-quantity]', container);
    var incBtn = qs('[data-increase-quantity]', container);

    function getVal() { return input ? (parseInt(input.value, 10) || 1) : 1; }
    function setVal(v) { v = Math.max(1, v); if (input) input.value = v; }

    if (decBtn) decBtn.addEventListener('click', function () { setVal(getVal() - 1); });
    if (incBtn) incBtn.addEventListener('click', function () { setVal(getVal() + 1); });
  }

  /* ── 3. Accordion Toggle ──────────────────────────────────── */
  function initAccordion() {
    qsa('[data-component="accordion"] [data-accordion-button]').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var item = btn.closest('[data-accordion-item]');
        if (!item) return;

        var content = qs('[data-accordion-content]', item);
        var expandIcon = qs('[data-expand-icon]', item);
        var collapseIcon = qs('[data-collapse-icon]', item);
        var isOpen = item.getAttribute('data-start-open') === 'true' || btn.getAttribute('aria-expanded') === 'true';

        if (isOpen) {
          /* Close */
          btn.setAttribute('aria-expanded', 'false');
          if (content) content.setAttribute('aria-hidden', 'true');
          if (expandIcon) expandIcon.classList.remove('hidden');
          if (collapseIcon) collapseIcon.classList.add('hidden');
        } else {
          /* Open */
          btn.setAttribute('aria-expanded', 'true');
          if (content) content.setAttribute('aria-hidden', 'false');
          if (expandIcon) expandIcon.classList.add('hidden');
          if (collapseIcon) collapseIcon.classList.remove('hidden');
        }
      });
    });
  }

  /* ── 4. Sticky Add-to-Cart Bar ────────────────────────────── */
  function initStickyBar() {
    var bar = qs('[data-sticky-atc]');
    var productSection = qs('[data-component="productPage"]');
    if (!bar || !productSection) return;

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          bar.classList.add('invisible');
          bar.classList.remove('translate-y-full');
          bar.style.transform = 'translateY(100%)';
        } else {
          bar.classList.remove('invisible');
          bar.style.transform = 'translateY(0)';
        }
      });
    }, { threshold: 0 });

    observer.observe(productSection);

    /* Sticky ATC button triggers the main ATC */
    var stickyBtn = qs('[data-sticky-atc-button]', bar);
    var mainBtn = qs('[data-button-add-to-cart]');
    if (stickyBtn && mainBtn) {
      stickyBtn.addEventListener('click', function () { mainBtn.click(); });
    }
  }

  /* ── 5. Back Button ───────────────────────────────────────── */
  function initBackButton() {
    var btn = qs('[data-back-button]');
    if (btn) {
      btn.addEventListener('click', function () {
        if (window.history.length > 1) {
          window.history.back();
        } else {
          window.location.href = '/';
        }
      });
    }
  }

  /* ── 6. Read More / Read Less Toggle ──────────────────────── */
  function initReadMore() {
    qsa('[data-read-more-button]').forEach(function (btn) {
      var content = btn.closest('[data-accordion-content]');
      if (!content) return;

      var innerContent = qs('[data-inner-content]', content);
      if (!innerContent) return;

      /* Check if content overflows */
      var isClamped = innerContent.scrollHeight > innerContent.clientHeight + 10;
      if (!isClamped) {
        btn.classList.add('hidden');
        return;
      }

      btn.classList.remove('hidden');
      btn.addEventListener('click', function () {
        var isExpanded = innerContent.classList.contains('line-clamp-3');
        if (isExpanded) {
          innerContent.classList.remove('line-clamp-3');
          innerContent.classList.add('line-clamp-none');
          btn.textContent = btn.getAttribute('data-read-less-text') || '- Read less';
        } else {
          innerContent.classList.add('line-clamp-3');
          innerContent.classList.remove('line-clamp-none');
          btn.textContent = btn.getAttribute('data-read-more-text') || '+ Read more';
        }
      });
    });
  }

  /* ── 7. Delivery Estimate ─────────────────────────────────── */
  function initDeliveryEstimate() {
    var el = qs('[data-mto-delivery-date]');
    if (!el) return;

    var days = parseInt(el.getAttribute('data-delivery-time'), 10) || 56;
    var weekString = el.getAttribute('data-week-string') || 'Week';
    var label = el.getAttribute('data-string') || 'Estimated delivery';

    var now = new Date();
    var delivery = new Date(now.getTime() + days * 24 * 60 * 60 * 1000);

    /* Calculate week range */
    var startWeek = getWeekNumber(new Date(now.getTime() + (days - 7) * 24 * 60 * 60 * 1000));
    var endWeek = getWeekNumber(delivery);

    if (startWeek === endWeek) {
      el.textContent = label + ': ' + weekString + ' ' + startWeek;
    } else {
      el.textContent = label + ': ' + weekString + ' ' + startWeek + '-' + endWeek;
    }
  }

  function getWeekNumber(d) {
    var onejan = new Date(d.getFullYear(), 0, 1);
    var weekNum = Math.ceil(((d - onejan) / 86400000 + onejan.getDay() + 1) / 7);
    return weekNum;
  }

  /* ── 8. Variant Image Switching ───────────────────────────── */
  function initVariantImages() {
    var galleryEl = qs('[data-component="productImages"]');
    if (!galleryEl) return;

    qsa('[data-color-handle]', galleryEl.closest('[data-component="variantInfo"]') || document).forEach(function (swatch) {
      swatch.addEventListener('click', function (e) {
        /* Let the link navigate for now — variant switching is handled by page reload in MVP */
      });
    });
  }

  /* ── 9. Cart Count Sync ───────────────────────────────────── */
  function initCartCountSync() {
    function updateCount(count) {
      qsa('[data-cart-count]').forEach(function (el) {
        el.textContent = count;
        el.style.display = count > 0 ? '' : 'none';
      });
      var mainBtn = qs('[data-main-cart-button]');
      if (mainBtn) {
        mainBtn.setAttribute('data-cart-count-number', count);
      }
    }

    /* Listen for cart updates from bridge */
    document.addEventListener('cart:update', function () {
      fetch('/wp-admin/admin-ajax.php?action=ferm_cart_get', { credentials: 'same-origin' })
        .then(function (r) { return r.json(); })
        .then(function (data) {
          if (data.success && data.data) {
            updateCount(data.data.item_count || 0);
          }
        })
        .catch(function () {});
    });
  }

  /* ── Init ─────────────────────────────────────────────────── */
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }

  function boot() {
    initGallery();
    initQuantity();
    initAccordion();
    initStickyBar();
    initBackButton();
    initReadMore();
    initDeliveryEstimate();
    initVariantImages();
    initCartCountSync();
  }
})();
