/**
 * Ferm Living content pages JS — FAQ accordion, blog filtering.
 *
 * Extracted from frozen source (fermliving.com theme JS) content section behaviors.
 * Scoped under `.design-fermliving` to avoid conflicts.
 *
 * @package Aureon
 */

(function () {
	'use strict';

	/* ======================================================================
	   1. FAQ ACCORDION — contact page
	   ====================================================================== */

	var faqContainers = document.querySelectorAll(
		'.design-fermliving [data-component="faqAccordion"]'
	);

	faqContainers.forEach(function (container) {
		var buttons = container.querySelectorAll('[data-faq-question]');

		buttons.forEach(function (btn) {
			btn.addEventListener('click', function () {
				var isExpanded = btn.getAttribute('aria-expanded') === 'true';
				var answerId = btn.getAttribute('aria-controls');
				var answer = answerId ? document.getElementById(answerId) : null;
				var plusIcon = btn.querySelector('[data-plus]');
				var minusIcon = btn.querySelector('[data-minus]');

				if (!answer) return;

				if (isExpanded) {
					// Collapse
					btn.setAttribute('aria-expanded', 'false');
					answer.setAttribute('aria-hidden', 'true');
					answer.style.maxHeight = '0';
					if (plusIcon) plusIcon.style.display = '';
					if (minusIcon) minusIcon.style.display = 'none';
				} else {
					// Expand
					btn.setAttribute('aria-expanded', 'true');
					answer.setAttribute('aria-hidden', 'false');
					answer.style.maxHeight = answer.scrollHeight + 'px';
					if (plusIcon) plusIcon.style.display = 'none';
					if (minusIcon) minusIcon.style.display = '';
				}
			});
		});
	});

	/* ======================================================================
	   2. BLOG TAG FILTERING — blog listing page
	   ====================================================================== */

	var blogSection = document.querySelector(
		'.design-fermliving [data-component="blog"]'
	);

	if (blogSection) {
		var tagLinks = blogSection.querySelectorAll('.blog__tags a');
		var posts = blogSection.querySelectorAll('.blog-thumb');

		tagLinks.forEach(function (link) {
			link.addEventListener('click', function (e) {
				// If using native nav, just let it go; filtering is server-side in WP
				// For now, mark active tag visually
				tagLinks.forEach(function (l) {
					l.classList.remove('underline', 'font-medium');
				});
				link.classList.add('underline', 'font-medium');
			});
		});
	}

	/* ======================================================================
	   3. ARTICLE SHARE LINKS — open in new tab
	   ====================================================================== */

	var articleShareLinks = document.querySelectorAll(
		'.design-fermliving .article a[target="_blank"]'
	);

	articleShareLinks.forEach(function (link) {
		if (!link.getAttribute('rel')) {
			link.setAttribute('rel', 'noopener noreferrer');
		}
	});
})();
