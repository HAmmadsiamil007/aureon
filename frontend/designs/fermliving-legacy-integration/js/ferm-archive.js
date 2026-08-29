/**
 * Ferm Living Design Pack — Archive / PLP JavaScript
 *
 * Handles: product image carousel (desktop arrows + mobile swipe),
 * AJAX filter/sort/pagination, wishlist toggle, and filter panel open/close.
 *
 * @package Aureon\Designs\FermLiving
 */
(function () {
	'use strict';

	/* ── Helpers ─────────────────────────────────────────────── */
	var $ = function (sel, ctx) { return (ctx || document).querySelectorAll(sel); };
	var on = function (el, evt, fn, opts) { if (el) el.addEventListener(evt, fn, opts || false); };
	var qs = function (sel, ctx) { return (ctx || document).querySelector(sel); };

	/* ── Product Image Carousel ─────────────────────────────── */
	function initCarousels() {
		$('.product-thumb-carousel').forEach(function (carousel) {
			var track = qs('.product-thumb-carousel-track', carousel);
			var slides = qs('.product-thumb-carousel-track', carousel).children;
			var prevBtn = qs('.product-thumb-carousel__prev', carousel);
			var nextBtn = qs('.product-thumb-carousel__next', carousel);
			var dots = qs('.product-thumb-carousel__dots--desktop', carousel);
			var mobileDots = qs('.product-thumb-carousel__dots--mobile', carousel);
			var current = 0;
			var total = slides.length;

			if (total <= 1) return;

			function goTo(index) {
				if (index < 0) index = 0;
				if (index >= total) index = total - 1;
				current = index;
				track.style.transform = 'translateX(-' + (current * 100) + '%)';
				updateDots();
			}

			function updateDots() {
				[dots, mobileDots].forEach(function (dotContainer) {
					if (!dotContainer) return;
					var allDots = $('button', dotContainer);
					allDots.forEach(function (dot, i) {
						dot.classList.toggle('product-thumb-carousel__dot--active', i === current);
						if (i === current) dot.setAttribute('aria-current', 'true');
						else dot.removeAttribute('aria-current');
					});
				});
			}

			on(prevBtn, 'click', function () { goTo(current - 1); });
			on(nextBtn, 'click', function () { goTo(current + 1); });

			[dots, mobileDots].forEach(function (dotContainer) {
				if (!dotContainer) return;
				$('button', dotContainer).forEach(function (dot, i) {
					on(dot, 'click', function () { goTo(i); });
				});
			});

			/* Mobile swipe */
			var startX = 0;
			var deltaX = 0;
			var swiping = false;

			on(carousel, 'touchstart', function (e) {
				startX = e.touches[0].clientX;
				deltaX = 0;
				swiping = true;
			}, { passive: true });

			on(carousel, 'touchmove', function (e) {
				if (!swiping) return;
				deltaX = e.touches[0].clientX - startX;
			}, { passive: true });

			on(carousel, 'touchend', function () {
				if (!swiping) return;
				swiping = false;
				if (Math.abs(deltaX) > 40) {
					if (deltaX < 0) goTo(current + 1);
					else goTo(current - 1);
				}
			});
		});
	}

	/* ── AJAX Filter / Sort / Pagination ────────────────────── */
	function initAjaxNav() {
		var grid = qs('.ferm-product-grid');
		if (!grid) return;

		var archiveUrl = grid.getAttribute('data-archive-url') || window.location.href;

		function fetchProducts(url) {
			fetch(url, { credentials: 'same-origin' })
				.then(function (r) { return r.text(); })
				.then(function (html) {
					var doc = new DOMParser().parseFromString(html, 'text/html');
					var newGrid = qs('.ferm-product-grid', doc);
					var newPagination = qs('.ferm-pagination', doc);
					var newToolbar = qs('.ferm-toolbar', doc);

					if (newGrid) {
						grid.innerHTML = newGrid.innerHTML;
						initCarousels();
						grid.setAttribute('data-page', newGrid.getAttribute('data-page') || '1');
					}

					/* Replace pagination */
					var oldPag = qs('.ferm-pagination');
					if (oldPag && newPagination) {
						oldPag.replaceWith(newPagination);
					} else if (oldPag && !newPagination) {
						oldPag.remove();
					}

					/* Update active filter */
					if (newToolbar) {
						var newBtns = $('.ferm-toolbar-filter-btn', newToolbar);
						var oldBtns = $('.ferm-toolbar-filter-btn');
						newBtns.forEach(function (btn, i) {
							if (oldBtns[i]) {
								oldBtns[i].classList.toggle('is-active', btn.classList.contains('is-active'));
							}
						});
					}

					/* Update sort select */
					var newSort = qs('#ferm-sort-select', doc);
					var oldSort = qs('#ferm-sort-select');
					if (oldSort && newSort) {
						oldSort.value = newSort.value;
					}

					window.history.pushState({}, '', url);
				})
				.catch(function () {
					window.location.href = url;
				});
		}

		/* Delegated click on filter buttons + pagination links */
		on(grid, 'click', function (e) {
			var link = e.target.closest('.ferm-toolbar-filter-btn, .ferm-pagination a');
			if (!link) return;
			e.preventDefault();
			var url = link.getAttribute('data-filter-url') || link.getAttribute('href');
			if (url) fetchProducts(url);
		});

		/* Toolbar filter buttons (outside grid) */
		var toolbar = qs('.ferm-toolbar');
		if (toolbar) {
			on(toolbar, 'click', function (e) {
				var btn = e.target.closest('.ferm-toolbar-filter-btn');
				if (!btn) return;
				e.preventDefault();
				var url = btn.getAttribute('data-filter-url') || btn.getAttribute('href');
				if (url) fetchProducts(url);
			});
		}

		/* Sort change */
		var sortSelect = qs('#ferm-sort-select');
		if (sortSelect) {
			on(sortSelect, 'change', function () {
				var url = this.value;
				if (url) fetchProducts(url);
			});
		}

		/* Browser back/forward */
		on(window, 'popstate', function () {
			fetchProducts(window.location.href);
		});
	}

	/* ── Wishlist Toggle ────────────────────────────────────── */
	function initWishlist() {
		$('[data-wishlist-button]').forEach(function (btn) {
			on(btn, 'click', function (e) {
				e.preventDefault();
				e.stopPropagation();
				btn.classList.toggle('is-active');

				var productId = btn.getAttribute('data-product-id');
				if (productId && typeof fetch === 'function') {
					fetch('/wp-json/aether/v1/wishlist/toggle', {
						method: 'POST',
						credentials: 'same-origin',
						headers: {
							'Content-Type': 'application/json',
							'X-WP-Nonce': (window.aetherFrontend && window.aetherFrontend.nonce) || ''
						},
						body: JSON.stringify({ product_id: parseInt(productId, 10) })
					}).catch(function () {});
				}
			});
		});
	}

	/* ── Filter Panel (mobile) ──────────────────────────────── */
	function initFilterPanel() {
		var overlay = qs('.ferm-filter-overlay');
		if (!overlay) return;

		var openBtn = qs('[data-filter-open]');
		var closeBtn = qs('.ferm-filter-panel-close', overlay);

		on(openBtn, 'click', function () {
			overlay.classList.add('is-open');
			document.body.style.overflow = 'hidden';
		});

		on(closeBtn, 'click', function () {
			overlay.classList.remove('is-open');
			document.body.style.overflow = '';
		});

		on(overlay, 'click', function (e) {
			if (e.target === overlay) {
				overlay.classList.remove('is-open');
				document.body.style.overflow = '';
			}
		});
	}

	/* ── Init ───────────────────────────────────────────────── */
	function init() {
		initCarousels();
		initAjaxNav();
		initWishlist();
		initFilterPanel();
	}

	if (document.readyState === 'loading') {
		on(document, 'DOMContentLoaded', init);
	} else {
		init();
	}
})();
