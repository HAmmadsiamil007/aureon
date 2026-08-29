/**
 * Ferm Living shell JS — frozen source header/mega menu/mobile nav behaviors.
 *
 * Extracted from frozen source (fermliving.com theme JS).
 * These behaviors complement ferm.js and handle the frozen source's
 * specific data attribute patterns and DOM structure.
 *
 * @package Aureon
 */

(function () {
	'use strict';

	/* ======================================================================
	   1. HEADER SCROLL — hide/show on scroll direction (frozen source style)
	   ====================================================================== */

	var header = document.querySelector('[data-component="header"]');
	if (header) {
		var lastScrollY = 0;
		var ticking = false;
		var headerBar = header.querySelector('[data-header-bar]');
		var headerNav = header.querySelector('[data-header-nav]');

		function updateHeader() {
			var scrollY = window.pageYOffset || document.documentElement.scrollTop;
			var delta = scrollY - lastScrollY;

			// Only apply scroll behavior above the threshold
			if (Math.abs(delta) < 5) {
				ticking = false;
				return;
			}

			if (scrollY > 100) {
				// Scrolled down — add solid background
				if (headerBar) {
					headerBar.classList.add('bg-cream');
					headerBar.classList.remove('bg-canvas');
				}
				if (headerNav) {
					headerNav.classList.add('bg-cream');
				}
			} else {
				// At top — restore transparent state on homepage
				var isHome = document.body.classList.contains('home') ||
							 document.body.classList.contains('page-template-frontpage');
				if (isHome && headerBar) {
					headerBar.classList.remove('bg-cream');
					headerBar.classList.add('bg-canvas');
				}
			}

			lastScrollY = scrollY;
			ticking = false;
		}

		if (!window.matchMedia || !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
			window.addEventListener('scroll', function () {
				if (!ticking) {
					window.requestAnimationFrame(updateHeader);
					ticking = true;
				}
			}, { passive: true });
		}
	}

	/* ======================================================================
	   2. USP ANNOUNCEMENT BAR — auto-rotate with slide animation
	   ====================================================================== */

	var uspHeader = document.querySelector('[data-component="uspHeader"]');
	if (uspHeader) {
		var uspItems = uspHeader.querySelectorAll('[data-usp-item]');
		var speed = parseInt(uspHeader.getAttribute('data-speed'), 10) || 4000;
		var currentIndex = 0;
		var counterEl = uspHeader.querySelector('[data-usp-current-index]');

		if (uspItems.length > 1) {
			function rotateUSP() {
				// Hide current
				var current = uspItems[currentIndex];
				current.classList.remove('animate-in');
				current.classList.add('animate-out');
				current.setAttribute('aria-hidden', 'true');

				// Advance
				currentIndex = (currentIndex + 1) % uspItems.length;

				// Show next after animation
				setTimeout(function () {
					current.classList.remove('animate-out');
					var next = uspItems[currentIndex];
					next.classList.add('animate-in');
					next.setAttribute('aria-hidden', 'false');

					if (counterEl) {
						counterEl.textContent = currentIndex + 1;
					}
				}, 350);
			}

			if (!window.matchMedia || !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
				setInterval(rotateUSP, speed);
			}
		}
	}

	/* ======================================================================
	   3. MEGA MENU — open/close on nav hover (frozen source behavior)
	   ====================================================================== */

	if (window.matchMedia && window.matchMedia('(min-width: 1024px)').matches) {
		var megaWrapper = document.querySelector('[data-component="megaMenu"]');
		var headerLinks = document.querySelectorAll('[data-header-link]');

		if (megaWrapper) {
			var megaPanels = megaWrapper.querySelectorAll('[data-megamenu]');
			var overlay = megaWrapper.querySelector('[data-megamenu-overlay]');
			var activePanel = null;
			var closeTimeout = null;

			function openMega(menuPoint) {
				closeAllMega();

				for (var i = 0; i < megaPanels.length; i++) {
					if (megaPanels[i].getAttribute('data-megamenu-menu-point') === menuPoint) {
						megaPanels[i].classList.remove('closed');
						megaPanels[i].setAttribute('aria-hidden', 'false');
						megaWrapper.classList.add('menu-open');
						activePanel = megaPanels[i];
						break;
					}
				}
			}

			function closeAllMega() {
				for (var i = 0; i < megaPanels.length; i++) {
					megaPanels[i].classList.add('closed');
					megaPanels[i].setAttribute('aria-hidden', 'true');
				}
				if (megaWrapper) {
					megaWrapper.classList.remove('menu-open');
				}
				activePanel = null;
			}

			// Attach hover to header nav links
			for (var h = 0; h < headerLinks.length; h++) {
				(function (link) {
					var menuPoint = link.textContent.trim();

					link.addEventListener('mouseenter', function () {
						if (closeTimeout) {
							clearTimeout(closeTimeout);
							closeTimeout = null;
						}
						openMega(menuPoint);
					});

					link.addEventListener('mouseleave', function () {
						closeTimeout = setTimeout(closeAllMega, 200);
					});
				})(headerLinks[h]);
			}

			// Keep mega open when hovering over the panel
			megaWrapper.addEventListener('mouseenter', function () {
				if (closeTimeout) {
					clearTimeout(closeTimeout);
					closeTimeout = null;
				}
			});

			megaWrapper.addEventListener('mouseleave', function () {
				closeTimeout = setTimeout(closeAllMega, 200);
			});

			// Dynamic submenu hover (frozen source data-dynamic-menu-parent)
			var dynamicParents = megaWrapper.querySelectorAll('[data-dynamic-menu-parent]');
			for (var dp = 0; dp < dynamicParents.length; dp++) {
				(function (parent) {
					parent.addEventListener('mouseenter', function () {
						var parentName = parent.getAttribute('data-dynamic-menu-parent');
						var children = megaWrapper.querySelectorAll('[data-dynamic-menu-child]');
						for (var c = 0; c < children.length; c++) {
							if (children[c].getAttribute('data-dynamic-menu-child') === parentName) {
								children[c].classList.remove('hidden');
								children[c].classList.add('animate-slide-left');
							} else {
								children[c].classList.add('hidden');
								children[c].classList.remove('animate-slide-left');
							}
						}
					});
				})(dynamicParents[dp]);
			}
		}
	}

	/* ======================================================================
	   4. MOBILE MENU — 3-level slide panels (frozen source behavior)
	   ====================================================================== */

	var mobileMenu = document.querySelector('[data-component="mobileMenu"]');
	if (mobileMenu) {
		var menuLink = document.querySelector('[data-mobile-menu-link]');
		var submenus = mobileMenu.querySelectorAll('[data-mobile-submenu]');
		var submenuCloses = mobileMenu.querySelectorAll('[data-mobile-submenu-close]');
		var tertiaryLinks = mobileMenu.querySelectorAll('[data-tertiary-menu-link]');
		var tertiaryCloses = mobileMenu.querySelectorAll('[data-tertiary-menu-close]');

		// Open mobile menu
		if (menuLink) {
			menuLink.addEventListener('click', function () {
				mobileMenu.classList.remove('hidden');
				mobileMenu.classList.add('is-open');
				menuLink.setAttribute('aria-expanded', 'true');
				document.body.style.overflow = 'hidden';
			});
		}

		// Level 1 → Level 2
		for (var sm = 0; sm < submenus.length; sm++) {
			submenus[sm].addEventListener('click', function (e) {
				e.preventDefault();
				var target = this.getAttribute('data-mobile-submenu');
				var panel = mobileMenu.querySelector('[data-mobile-submenu="' + target + '"]');
				if (panel) {
					panel.classList.add('is-active');
				}
			});
		}

		// Level 2 → Level 1 (back)
		for (var sc = 0; sc < submenuCloses.length; sc++) {
			submenuCloses[sc].addEventListener('click', function (e) {
				e.preventDefault();
				var panel = this.closest('[data-mobile-submenu]');
				if (panel) {
					panel.classList.remove('is-active');
				}
			});
		}

		// Level 2 → Level 3
		for (var tl = 0; tl < tertiaryLinks.length; tl++) {
			tertiaryLinks[tl].addEventListener('click', function (e) {
				e.preventDefault();
				var target = this.getAttribute('data-tertiary-menu-link');
				var panel = mobileMenu.querySelector('[data-tertiary-menu="' + target + '"]');
				if (panel) {
					panel.classList.add('is-active');
				}
			});
		}

		// Level 3 → Level 2 (back)
		for (var tc = 0; tc < tertiaryCloses.length; tc++) {
			tertiaryCloses[tc].addEventListener('click', function (e) {
				e.preventDefault();
				var panel = this.closest('[data-tertiary-menu]');
				if (panel) {
					panel.classList.remove('is-active');
				}
			});
		}
	}

	/* ======================================================================
	   5. SEARCH OVERLAY — toggle on search button click
	   ====================================================================== */

	var searchOverlay = document.getElementById('searchOverlay');
	if (searchOverlay) {
		var searchBtns = document.querySelectorAll('[data-search]');
		var searchClose = searchOverlay.querySelector('.search-overlay-close');
		var searchInput = searchOverlay.querySelector('.search-overlay-input');

		for (var sb = 0; sb < searchBtns.length; sb++) {
			searchBtns[sb].addEventListener('click', function () {
				searchOverlay.classList.add('is-open');
				searchOverlay.setAttribute('aria-hidden', 'false');
				document.body.style.overflow = 'hidden';
				if (searchInput) {
					searchInput.focus();
				}
			});
		}

		if (searchClose) {
			searchClose.addEventListener('click', function () {
				searchOverlay.classList.remove('is-open');
				searchOverlay.setAttribute('aria-hidden', 'true');
				document.body.style.overflow = '';
			});
		}

		// Close on Escape
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && searchOverlay.classList.contains('is-open')) {
				searchOverlay.classList.remove('is-open');
				searchOverlay.setAttribute('aria-hidden', 'true');
				document.body.style.overflow = '';
			}
		});
	}

})();
