/**
 * AzonMate – Public Frontend JavaScript
 *
 * Handles geo-targeting cookie, lazy-loading, and general frontend behavior.
 *
 * @package AzonMate
 * @since   1.0.0
 */

(function () {
	'use strict';

	/* global azonMatePublic */

	var AM = window.AzonMate = window.AzonMate || {};

	/**
	 * Initialise on DOMContentLoaded.
	 */
	document.addEventListener('DOMContentLoaded', function () {
		AM.init();
	});

	/**
	 * Main init.
	 */
	AM.init = function () {
		AM.initLazyImages();
		AM.initExternalLinks();
	};

	/* ======================================================================
	   Lazy Image Loading (fallback for browsers without native loading=lazy)
	   ====================================================================== */

	AM.initLazyImages = function () {
		if ('loading' in HTMLImageElement.prototype) {
			return; // Native support — nothing to do.
		}

		var images = document.querySelectorAll('.azonmate-product-box img[loading="lazy"], .azonmate-product-list img[loading="lazy"], .azonmate-bestseller img[loading="lazy"]');

		if (!images.length) {
			return;
		}

		if ('IntersectionObserver' in window) {
			var observer = new IntersectionObserver(function (entries) {
				entries.forEach(function (entry) {
					if (entry.isIntersecting) {
						var img = entry.target;
						if (img.dataset.src) {
							img.src = img.dataset.src;
							img.removeAttribute('data-src');
						}
						observer.unobserve(img);
					}
				});
			}, { rootMargin: '200px' });

			images.forEach(function (img) {
				observer.observe(img);
			});
		}
	};

	/* ======================================================================
	   External Link Handling (open in new tab, noopener)
	   ====================================================================== */

	AM.initExternalLinks = function () {
		var links = document.querySelectorAll('a.azonmate-buy-btn, a.azonmate-text-link, a.azonmate-image-link');
		links.forEach(function (link) {
			if (!link.getAttribute('target')) {
				link.setAttribute('target', '_blank');
			}
			var rel = link.getAttribute('rel') || '';
			if (rel.indexOf('noopener') === -1) {
				link.setAttribute('rel', (rel + ' noopener').trim());
			}
		});
	};

})();
