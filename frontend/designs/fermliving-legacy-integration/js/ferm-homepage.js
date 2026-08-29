/**
 * Ferm Living Design Pack — Homepage Behaviors
 *
 * Carousel initialization (Embla), hero zoom animation, scroll reveals,
 * product thumb carousels. All Shopify code removed.
 *
 * @package Aureon
 */

(function () {
	'use strict';

	/* ── Embla Carousel Init ──────────────────────────────────── */
	function initEmblaCarousels() {
		if (typeof EmblaCarousel === 'undefined') return;

		/* Categories + Rooms carousels with navigation buttons */
		document.querySelectorAll('.section-slider-with-images .embla, .ferm-room-slider .embla').forEach(function (emblaEl) {
			var embla = EmblaCarousel(emblaEl, {
				align: 'start',
				loop: false,
				slidesToScroll: 1,
				dragFree: true,
			});

			var prevBtn = emblaEl.querySelector('.embla__button--prev');
			var nextBtn = emblaEl.querySelector('.embla__button--next');

			if (prevBtn) {
				prevBtn.addEventListener('click', function () {
					embla.scrollPrev();
				});
			}
			if (nextBtn) {
				nextBtn.addEventListener('click', function () {
					embla.scrollNext();
				});
			}
		});
	}

	/* ── Product Thumb Carousels ──────────────────────────────── */
	function initProductThumbCarousels() {
		if (typeof EmblaCarousel === 'undefined') return;

		document.querySelectorAll('.product-thumb-carousel').forEach(function (carouselEl) {
			var embla = EmblaCarousel(carouselEl, {
				align: 'start',
				loop: false,
				slidesToScroll: 1,
				dragFree: false,
			_containScroll: 'trimSnaps',
			});

			var prevBtn = carouselEl.closest('.product').querySelector('.product-thumb-carousel__prev');
			var nextBtn = carouselEl.closest('.product').querySelector('.product-thumb-carousel__next');

			if (prevBtn) {
				prevBtn.addEventListener('click', function (e) {
					e.preventDefault();
					embla.scrollPrev();
				});
			}
			if (nextBtn) {
				nextBtn.addEventListener('click', function (e) {
					e.preventDefault();
					embla.scrollNext();
				});
			}
		});
	}

	/* ── Hero Zoom Animation ──────────────────────────────────── */
	function initHeroZoom() {
		var panels = document.querySelectorAll('.ferm-hero-panel');
		if (!panels.length) return;

		panels.forEach(function (panel) {
			var observer = new IntersectionObserver(function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						entry.target.classList.add('is-visible');
						observer.unobserve(entry.target);
					}
				});
			}, { threshold: 0.1 });

			observer.observe(panel);
		});
	}

	/* ── Section Scroll Reveals ───────────────────────────────── */
	function initScrollReveals() {
		var revealElements = document.querySelectorAll(
			'.ferm-editorial-split, .ferm-products-row, .section-newsletter'
		);

		revealElements.forEach(function (el) {
			el.style.opacity = '0';
			el.style.transform = 'translateY(24px)';
			el.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';

			var observer = new IntersectionObserver(function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						entry.target.style.opacity = '1';
						entry.target.style.transform = 'translateY(0)';
						observer.unobserve(entry.target);
					}
				});
			}, { threshold: 0.15 });

			observer.observe(el);
		});
	}

	/* ── Init on DOM Ready ────────────────────────────────────── */
	function init() {
		initHeroZoom();
		initEmblaCarousels();
		initProductThumbCarousels();
		initScrollReveals();
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
