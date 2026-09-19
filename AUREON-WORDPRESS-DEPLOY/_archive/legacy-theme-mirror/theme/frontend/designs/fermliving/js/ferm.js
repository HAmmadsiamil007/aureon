/**
 * Ferm Living design pack JS — guarded behaviors only.
 *
 * Keeps platform JS (aether-main, aether-animations, AJAX cart, search drawer)
 * intact. This file adds ONLY Ferm-specific presentation behaviors:
 *
 *   1. Header hide-on-scroll / show-on-scroll-up + transparency toggle
 *   2. USP announcement bar auto-rotation with counter
 *   3. Mega menu open/close on nav hover
 *   4. Category carousel navigation
 *
 * All behaviors degrade gracefully when JS is unavailable — content remains visible.
 *
 * @package Aureon
 */

(function () {
	'use strict';

	/* ======================================================================
	   1. HEADER SCROLL STATE
	   ====================================================================== */

	var header = document.getElementById('header');
	if (header) {
		var lastScroll = 0;
		var scrollThreshold = 10;
		var isTransparent = header.hasAttribute('data-header-transparent');
		var solidClassAdded = false;

		function onScroll() {
			var currentScroll = window.pageYOffset || document.documentElement.scrollTop;

			// Toggle solid background on scroll for transparent headers.
			// Real Ferm Living keeps header visible at all times — no hide-on-scroll.
			if (isTransparent) {
				if (currentScroll > 100) {
					if (!header.classList.contains('header--solid')) {
						header.classList.add('header--solid');
						solidClassAdded = true;
					}
				} else if (solidClassAdded) {
					header.classList.remove('header--solid');
					solidClassAdded = false;
				}
			}

			lastScroll = currentScroll;
		}

		// Respect reduced motion preference.
		if (!window.matchMedia || !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
			var ticking = false;
			window.addEventListener('scroll', function () {
				if (!ticking) {
					window.requestAnimationFrame(function () {
						onScroll();
						ticking = false;
					});
					ticking = true;
				}
			}, { passive: true });
		}
	}

	/* ======================================================================
	   2. USP ANNOUNCEMENT BAR ROTATION
	   ====================================================================== */

	var announcementBar = document.querySelector('[data-announcement-bar]');
	if (announcementBar) {
		var allItems = announcementBar.querySelectorAll('[data-announcement-item]');
		var speed = parseInt(announcementBar.getAttribute('data-speed'), 10) || 4000;
		var totalSlides = parseInt(announcementBar.getAttribute('data-total'), 10) || allItems.length;
		var currentIndex = 0;
		var counterCurrent = announcementBar.querySelector('.announcement-counter-current');

		// Items are rendered in 2 passes (marquee), so group by pass:
		// pass 0 = items 0..N-1, pass 1 = items N..2N-1
		var passSize = Math.floor(allItems.length / 2);
		var slides = [];
		for (var i = 0; i < passSize; i++) {
			// Each slide = the same text from both passes
			slides.push([allItems[i], allItems[i + passSize]]);
		}

		// Activate first slide on load.
		if (slides[0]) {
			slides[0].forEach(function(el) {
				el.classList.add('is-active');
				el.setAttribute('aria-hidden', 'false');
			});
		}

		function rotateUSP() {
			if (slides.length <= 1) return;

			// Hide current.
			if (slides[currentIndex]) {
				slides[currentIndex].forEach(function(el) {
					el.classList.remove('is-active');
					el.setAttribute('aria-hidden', 'true');
				});
			}

			// Advance index.
			currentIndex = (currentIndex + 1) % slides.length;

			// Show next.
			if (slides[currentIndex]) {
				slides[currentIndex].forEach(function(el) {
					el.classList.add('is-active');
					el.setAttribute('aria-hidden', 'false');
				});
			}

			// Update counter.
			if (counterCurrent) {
				counterCurrent.textContent = currentIndex + 1;
			}
		}

		// Only rotate if no reduced motion.
		if (!window.matchMedia || !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
			setInterval(rotateUSP, speed);
		}
	}

	/* ======================================================================
	   3. MEGA MENU HOVER BEHAVIOR (Desktop only)
	   ====================================================================== */

	if (window.matchMedia && window.matchMedia('(min-width: 1024px)').matches) {
		var megaMenu = document.querySelector('[data-component="megaMenu"]');
		var megaTriggers = document.querySelectorAll('[data-mega-trigger]');

		if (megaMenu && megaTriggers.length) {
			var megaPanels = megaMenu.querySelectorAll('[data-megamenu]');
			var activePanel = null;
			var closeTimeout = null;

			function openMega(point) {
				// Close any open panel.
				closeAllPanels();

				// Find matching panel.
				for (var i = 0; i < megaPanels.length; i++) {
					if (megaPanels[i].getAttribute('data-megamenu-menu-point') === point) {
						megaPanels[i].classList.add('is-open');
						megaPanels[i].setAttribute('aria-hidden', 'false');
						activePanel = megaPanels[i];
						break;
					}
				}
			}

			function closeAllPanels() {
				for (var i = 0; i < megaPanels.length; i++) {
					megaPanels[i].classList.remove('is-open');
					megaPanels[i].setAttribute('aria-hidden', 'true');
				}
				activePanel = null;
			}

			// Attach hover events to triggers.
			for (var t = 0; t < megaTriggers.length; t++) {
				(function (trigger) {
					var point = trigger.getAttribute('data-mega-trigger');

					trigger.addEventListener('mouseenter', function () {
						if (closeTimeout) {
							clearTimeout(closeTimeout);
							closeTimeout = null;
						}
						openMega(point);
					});

					trigger.addEventListener('mouseleave', function () {
						closeTimeout = setTimeout(function () {
							closeAllPanels();
						}, 200);
					});
				})(megaTriggers[t]);
			}

			// Keep mega open when hovering over the mega panel itself.
			if (megaMenu) {
				megaMenu.addEventListener('mouseenter', function () {
					if (closeTimeout) {
						clearTimeout(closeTimeout);
						closeTimeout = null;
					}
				});

				megaMenu.addEventListener('mouseleave', function () {
					closeTimeout = setTimeout(function () {
						closeAllPanels();
					}, 200);
				});
			}
		}
	}


	/* ======================================================================
	   4. MOBILE MENU (3-level deep panel navigation)
	   ====================================================================== */

	var mobileOverlay = document.getElementById('mobileMenuOverlay');
	var mobileHamburger = document.getElementById('mobileHamburger');

	if (mobileOverlay && mobileHamburger) {
		var level1 = mobileOverlay.querySelector('.mobile-menu-level-1');

		// Open mobile menu.
		mobileHamburger.addEventListener('click', function () {
			mobileOverlay.classList.add('is-open');
			mobileOverlay.setAttribute('aria-hidden', 'false');
			mobileHamburger.setAttribute('aria-expanded', 'true');
			document.body.style.overflow = 'hidden';
			// Reset to level 1.
			resetToLevel1();
		});

		// Close mobile menu (X button).
		var closeBtn = mobileOverlay.querySelector('.mobile-menu-close');
		if (closeBtn) {
			closeBtn.addEventListener('click', closeMobileMenu);
		}

		// Close on overlay click (outside menu).
		mobileOverlay.addEventListener('click', function (e) {
			if (e.target === mobileOverlay) {
				closeMobileMenu();
			}
		});

		// Close on any link click inside the menu.
		var menuLinks = mobileOverlay.querySelectorAll('a');
		for (var ml = 0; ml < menuLinks.length; ml++) {
			menuLinks[ml].addEventListener('click', closeMobileMenu);
		}

		function closeMobileMenu() {
			mobileOverlay.classList.remove('is-open');
			mobileOverlay.setAttribute('aria-hidden', 'true');
			mobileHamburger.setAttribute('aria-expanded', 'false');
			document.body.style.overflow = '';
		}

		function resetToLevel1() {
			var levels = mobileOverlay.querySelectorAll('.mobile-menu-level');
			for (var i = 0; i < levels.length; i++) {
				levels[i].classList.remove('is-active');
				levels[i].setAttribute('aria-hidden', 'true');
			}
			if (level1) {
				level1.classList.add('is-active');
				level1.setAttribute('aria-hidden', 'false');
			}
		}

		// Level 1 → Level 2: tap on menu item with submenus.
		var submenuTriggers = mobileOverlay.querySelectorAll('[data-mobile-submenu]');
		for (var s = 0; s < submenuTriggers.length; s++) {
			submenuTriggers[s].addEventListener('click', function () {
				var point = this.getAttribute('data-mobile-submenu');
				var panel = mobileOverlay.querySelector('[data-mobile-submenu-panel="' + point + '"]');
				if (panel) {
					// Hide level 1.
					if (level1) {
						level1.classList.remove('is-active');
						level1.setAttribute('aria-hidden', 'true');
					}
					// Show level 2.
					panel.classList.add('is-active');
					panel.setAttribute('aria-hidden', 'false');
				}
			});
		}

		// Level 2 back → Level 1.
		var backButtons = mobileOverlay.querySelectorAll('[data-mobile-submenu-close]');
		for (var b = 0; b < backButtons.length; b++) {
			backButtons[b].addEventListener('click', function () {
				// Hide all levels.
				var levels = mobileOverlay.querySelectorAll('.mobile-menu-level');
				for (var i = 0; i < levels.length; i++) {
					levels[i].classList.remove('is-active');
					levels[i].setAttribute('aria-hidden', 'true');
				}
				// Show level 1.
				if (level1) {
					level1.classList.add('is-active');
					level1.setAttribute('aria-hidden', 'false');
				}
			});
		}

		// Level 2 → Level 3: tap on tertiary menu link.
		var tertiaryTriggers = mobileOverlay.querySelectorAll('[data-tertiary-menu-link]');
		for (var t = 0; t < tertiaryTriggers.length; t++) {
			tertiaryTriggers[t].addEventListener('click', function () {
				var point = this.getAttribute('data-tertiary-menu-link');
				var panel = mobileOverlay.querySelector('[data-tertiary-menu="' + point + '"]');
				if (panel) {
					// Hide all levels.
					var levels = mobileOverlay.querySelectorAll('.mobile-menu-level');
					for (var i = 0; i < levels.length; i++) {
						levels[i].classList.remove('is-active');
						levels[i].setAttribute('aria-hidden', 'true');
					}
					// Show level 3.
					panel.classList.add('is-active');
					panel.setAttribute('aria-hidden', 'false');
				}
			});
		}

		// Level 3 back → Level 2.
		var tertiaryBack = mobileOverlay.querySelectorAll('[data-tertiary-menu-close]');
		for (var tb = 0; tb < tertiaryBack.length; tb++) {
			tertiaryBack[tb].addEventListener('click', function () {
				// Find the parent level-2 panel and go back to it.
				var levels = mobileOverlay.querySelectorAll('.mobile-menu-level');
				for (var i = 0; i < levels.length; i++) {
					levels[i].classList.remove('is-active');
					levels[i].setAttribute('aria-hidden', 'true');
				}
				// Show level 1 (simplified — in production, restore the specific level-2).
				if (level1) {
					level1.classList.add('is-active');
					level1.setAttribute('aria-hidden', 'false');
				}
			});
		}
	}

	/* ======================================================================
	   5. PRODUCT CARD IMAGE CAROUSEL (dots + hover cycle)
	   ====================================================================== */

	var reducedMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
	var carousels = document.querySelectorAll('[data-component="productThumbCarousel"]');

	for (var c = 0; c < carousels.length; c++) {
		(function (carousel) {
			var track = carousel.querySelector('.product-card-carousel-track');
			if (!track) return;

			var slides = track.children.length;
			if (slides < 2) return;

			var index = 0;
			var hoverTimer = null;

			function goTo(i) {
				index = ((i % slides) + slides) % slides;
				track.style.transform = 'translateX(-' + (index * 100) + '%)';

				var dots = carousel.querySelectorAll('.product-card-dot');
				for (var d = 0; d < dots.length; d++) {
					var active = d === index;
					dots[d].classList.toggle('is-active', active);
					if (active) {
						dots[d].setAttribute('aria-current', 'true');
					} else {
						dots[d].removeAttribute('aria-current');
					}
				}
			}

			// Dot navigation.
			var dotButtons = carousel.querySelectorAll('.product-card-dot');
			for (var b = 0; b < dotButtons.length; b++) {
				(function (dot, i) {
					dot.addEventListener('click', function (e) {
						e.preventDefault();
						goTo(i);
					});
				})(dotButtons[b], b);
			}

			// Hover cycle — mirrors the source site's auto-advance on hover.
			if (!reducedMotion) {
				carousel.addEventListener('mouseenter', function () {
					hoverTimer = window.setInterval(function () {
						goTo(index + 1);
					}, 1200);
				});

				carousel.addEventListener('mouseleave', function () {
					if (hoverTimer) {
						window.clearInterval(hoverTimer);
						hoverTimer = null;
					}
					goTo(0);
				});
			}
				})(carousels[c]);
	}

	/* ======================================================================
	   5. CATEGORY CAROUSEL NAVIGATION
	   ====================================================================== */

	var categoryCarousels = document.querySelectorAll('[data-carousel]');
	for (var cc = 0; cc < categoryCarousels.length; cc++) {
		(function (carousel) {
			var prevBtn = carousel.parentElement ? carousel.parentElement.querySelector('[data-carousel-prev]') : null;
			var nextBtn = carousel.parentElement ? carousel.parentElement.querySelector('[data-carousel-next]') : null;

			if (prevBtn) {
				prevBtn.addEventListener('click', function () {
					carousel.scrollBy({ left: -carousel.offsetWidth * 0.8, behavior: 'smooth' });
				});
			}

			if (nextBtn) {
				nextBtn.addEventListener('click', function () {
					carousel.scrollBy({ left: carousel.offsetWidth * 0.8, behavior: 'smooth' });
				});
			}
		})(categoryCarousels[cc]);
	}

	/* ======================================================================
	   6. MOBILE HEADER TRANSPARENCY TOGGLE
	   ====================================================================== */

	var mobileHeader = document.getElementById('mobileHeader');
	if (mobileHeader && mobileHeader.hasAttribute('data-mobile-header-transparent')) {
		var mobileLastScroll = 0;

		function onMobileScroll() {
			var currentScroll = window.pageYOffset || document.documentElement.scrollTop;

			if (currentScroll > 100) {
				if (!mobileHeader.classList.contains('mobile-header--solid')) {
					mobileHeader.classList.add('mobile-header--solid');
				}
			} else {
				mobileHeader.classList.remove('mobile-header--solid');
			}

			mobileLastScroll = currentScroll;
		}

		if (!window.matchMedia || !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
			var mobileTicking = false;
			window.addEventListener('scroll', function () {
				if (!mobileTicking) {
					window.requestAnimationFrame(function () {
						onMobileScroll();
						mobileTicking = false;
					});
					mobileTicking = true;
				}
			}, { passive: true });
		}
	}

})();
