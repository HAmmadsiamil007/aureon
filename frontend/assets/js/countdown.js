/**
 * AETHER countdown — ticks the coming-soon countdown toward its data-target.
 *
 * Selects [data-unit] cells inside any element carrying a data-target ISO date.
 * No-ops when no target is present; renders 00s once the target has passed.
 *
 * @package Aureon
 */
(function () {
	'use strict';

	function pad(n) {
		return String(n).padStart(2, '0');
	}

	function tick(el) {
		var target = Date.parse(el.getAttribute('data-target'));
		if (isNaN(target)) {
			return;
		}
		var diff = Math.max(0, target - Date.now());
		var cells = {
			days: Math.floor(diff / 86400000),
			hours: Math.floor((diff % 86400000) / 3600000),
			minutes: Math.floor((diff % 3600000) / 60000),
			seconds: Math.floor((diff % 60000) / 1000)
		};
		var nums = el.querySelectorAll('.countdown-number[data-unit]');
		Array.prototype.forEach.call(nums, function (num) {
			var unit = num.getAttribute('data-unit');
			if (unit in cells) {
				num.textContent = pad(cells[unit]);
			}
		});
	}

	function init() {
		var els = document.querySelectorAll('[data-target].countdown');
		Array.prototype.forEach.call(els, function (el) {
			tick(el);
			setInterval(function () { tick(el); }, 1000);
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
