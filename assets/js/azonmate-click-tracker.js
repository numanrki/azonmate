/**
 * AzonMate – Click Tracker
 *
 * Tracks clicks on affiliate links via AJAX.
 *
 * @package AzonMate
 * @since   1.0.0
 */

(function () {
	'use strict';

	/* global azonMatePublic */

	if (typeof azonMatePublic === 'undefined') {
		return;
	}

	/**
	 * Initialise click tracking on all affiliate links.
	 */
	document.addEventListener('DOMContentLoaded', function () {
		bindClickTracking();
	});

	function bindClickTracking() {
		// All trackable elements: buy buttons, text links, image links.
		var selectors = [
			'a.azonmate-buy-btn',
			'a.azonmate-text-link',
			'a.azonmate-image-link',
			'.azonmate-product-box__title a',
			'.azonmate-product-list__title a',
			'.azonmate-bestseller__title a',
			'.azonmate-comparison-table a'
		];

		var links = document.querySelectorAll(selectors.join(', '));

		links.forEach(function (link) {
			link.addEventListener('click', function (e) {
				trackClick(this);
			});
		});
	}

	/**
	 * Send the click to the server.
	 *
	 * @param {HTMLElement} element The clicked element.
	 */
	function trackClick(element) {
		var asin = getAsin(element);
		if (!asin) {
			return;
		}

		var postId = azonMatePublic.postId || 0;
		var country = azonMatePublic.country || '';

		// Use Beacon API for reliability (won't block navigation).
		if (navigator.sendBeacon) {
			var formData = new FormData();
			formData.append('action', 'azon_mate_track_click');
			formData.append('nonce', azonMatePublic.nonce);
			formData.append('asin', asin);
			formData.append('postId', postId);
			formData.append('country', country);

			navigator.sendBeacon(azonMatePublic.ajaxUrl, formData);
		} else {
			// Fallback: async XHR.
			var xhr = new XMLHttpRequest();
			xhr.open('POST', azonMatePublic.ajaxUrl, true);
			xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
			xhr.send(
				'action=azon_mate_track_click' +
				'&nonce=' + encodeURIComponent(azonMatePublic.nonce) +
				'&asin=' + encodeURIComponent(asin) +
				'&postId=' + encodeURIComponent(postId) +
				'&country=' + encodeURIComponent(country)
			);
		}
	}

	/**
	 * Extract the ASIN from the element or its parent container.
	 *
	 * @param {HTMLElement} el Element.
	 * @return {string|null} ASIN or null.
	 */
	function getAsin(el) {
		// data-asin on the element itself.
		if (el.dataset && el.dataset.asin) {
			return el.dataset.asin;
		}

		// Walk up to find a parent with data-asin.
		var parent = el.closest('[data-asin]');
		if (parent) {
			return parent.dataset.asin;
		}

		// Try to extract from href (/dp/ASIN).
		var href = el.getAttribute('href') || '';
		var match = href.match(/\/dp\/([A-Z0-9]{10})/i);
		if (match) {
			return match[1].toUpperCase();
		}

		return null;
	}

})();
