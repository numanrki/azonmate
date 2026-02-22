/**
 * AzonMate – Search Modal (Classic Editor / TinyMCE)
 *
 * Provides the AJAX-powered product search modal for the Classic Editor.
 *
 * @package AzonMate
 * @since   1.0.0
 */

(function ($) {
	'use strict';

	/* global azonMateAdmin, ajaxurl, tinyMCE */

	if (typeof azonMateAdmin === 'undefined') {
		return;
	}

	var $overlay, $modal, $searchInput, $categorySelect, $results, $loadingEl;
	var searchTimeout = null;

	$(document).ready(function () {
		cacheElements();
		bindEvents();
	});

	/* ======================================================================
	   Initialisation
	   ====================================================================== */

	function cacheElements() {
		$overlay        = $('.azonmate-search-modal-overlay');
		$modal          = $overlay.find('.azonmate-search-modal');
		$searchInput    = $modal.find('.azonmate-search-input');
		$categorySelect = $modal.find('.azonmate-search-category');
		$results        = $modal.find('.azonmate-search-results');
		$loadingEl      = $modal.find('.azonmate-search-loading');
	}

	function bindEvents() {
		// Open modal via toolbar button or keyboard shortcut.
		$(document).on('click', '.azonmate-open-search', function (e) {
			e.preventDefault();
			openModal();
		});

		// Close modal.
		$overlay.on('click', function (e) {
			if (e.target === this) {
				closeModal();
			}
		});

		$modal.on('click', '.azonmate-search-modal__close', function (e) {
			e.preventDefault();
			closeModal();
		});

		// Escape key.
		$(document).on('keydown', function (e) {
			if (e.key === 'Escape' && $overlay.hasClass('active')) {
				closeModal();
			}
		});

		// Search form.
		$modal.on('click', '.azonmate-search-btn', function (e) {
			e.preventDefault();
			performSearch();
		});

		$modal.on('keydown', '.azonmate-search-input', function (e) {
			if (e.key === 'Enter') {
				e.preventDefault();
				performSearch();
			}
		});

		// ASIN lookup.
		$modal.on('click', '.azonmate-lookup-btn', function (e) {
			e.preventDefault();
			performLookup();
		});

		// Insert shortcode.
		$modal.on('click', '.azonmate-insert-box', function () {
			var asin = $(this).data('asin');
			insertShortcode('[azonmate box="' + asin + '"]');
			closeModal();
		});

		$modal.on('click', '.azonmate-insert-link', function () {
			var asin = $(this).data('asin');
			var title = $(this).data('title') || '';
			insertShortcode('[azonmate link="' + asin + '"]' + escapeHtml(title) + '[/azonmate]');
			closeModal();
		});

		$modal.on('click', '.azonmate-insert-image', function () {
			var asin = $(this).data('asin');
			insertShortcode('[azonmate image="' + asin + '"]');
			closeModal();
		});
		// Browse manual products button.
		$modal.on('click', '.azonmate-browse-manual', function (e) {
			e.preventDefault();
			browseManualProducts();
		});
	}

	/* ======================================================================
	   Modal Open / Close
	   ====================================================================== */

	function openModal() {
		$overlay.addClass('active');
		$searchInput.val('').focus();
		$results.empty();
		$loadingEl.hide();
	}

	function closeModal() {
		$overlay.removeClass('active');
	}

	/* ======================================================================
	   Search
	   ====================================================================== */

	function performSearch() {
		var keyword = $.trim($searchInput.val());
		if (!keyword) {
			return;
		}

		var category = $categorySelect.val() || '';

		$results.empty();
		$loadingEl.show();

		$.post(ajaxurl, {
			action: 'azon_mate_search_products',
			nonce: azonMateAdmin.nonce,
			keywords: keyword,
			category: category,
		})
		.done(function (response) {
			$loadingEl.hide();

			if (response.success && response.data.products && response.data.products.length) {
				renderResults(response.data.products);
			} else {
				$results.html('<p class="azonmate-search-empty">No products found.</p>');
			}
		})
		.fail(function () {
			$loadingEl.hide();
			$results.html('<p class="azonmate-search-error">Search failed. Please try again.</p>');
		});
	}

	/* ======================================================================
	   ASIN Lookup
	   ====================================================================== */

	function performLookup() {
		var asinInput = $modal.find('.azonmate-asin-input');
		var asin = $.trim(asinInput.val());
		if (!asin) {
			return;
		}

		$results.empty();
		$loadingEl.show();

		$.post(ajaxurl, {
			action: 'azon_mate_lookup_asin',
			nonce: azonMateAdmin.nonce,
			asin: asin,
		})
		.done(function (response) {
			$loadingEl.hide();

			if (response.success && response.data.product) {
				renderResults([response.data.product]);
			} else {
				$results.html('<p class="azonmate-search-empty">Product not found.</p>');
			}
		})
		.fail(function () {
			$loadingEl.hide();
			$results.html('<p class="azonmate-search-error">Lookup failed. Please try again.</p>');
		});
	}

	/* ======================================================================
	   Render Results
	   ====================================================================== */

	function renderResults(products) {
		$results.empty();

		products.forEach(function (product) {
			var imgUrl = product.image_medium || product.image_small || product.image_large || product.image || '';
			var priceDisplay = product.price_display || product.price || '';

			var $item = $(
				'<div class="azonmate-search-result">' +
					'<div class="azonmate-search-result__image">' +
						'<img src="' + escapeAttr(imgUrl) + '" alt="" />' +
					'</div>' +
					'<div class="azonmate-search-result__info">' +
						'<div class="azonmate-search-result__title">' + escapeHtml(product.title || '') + '</div>' +
						'<div class="azonmate-search-result__meta">' +
							'<span>ASIN: ' + escapeHtml(product.asin || '') + '</span>' +
							(priceDisplay ? '<span>' + escapeHtml(priceDisplay) + '</span>' : '') +
							(product.rating ? '<span>★ ' + escapeHtml(String(product.rating)) + '</span>' : '') +
						'</div>' +
					'</div>' +
					'<div class="azonmate-search-result__actions">' +
						'<button type="button" class="button azonmate-insert-box" data-asin="' + escapeAttr(product.asin) + '">Insert Box</button>' +
						'<button type="button" class="button azonmate-insert-link" data-asin="' + escapeAttr(product.asin) + '" data-title="' + escapeAttr(product.title || '') + '">Insert Link</button>' +
						'<button type="button" class="button azonmate-insert-image" data-asin="' + escapeAttr(product.asin) + '">Insert Image</button>' +
					'</div>' +
				'</div>'
			);

			$results.append($item);
		});
	}

	/* ======================================================================
	   Browse Manual Products
	   ====================================================================== */

	function browseManualProducts() {
		$results.empty();
		$loadingEl.show();

		$.post(ajaxurl, {
			action: 'azon_mate_get_manual_products',
			nonce: azonMateAdmin.nonce,
			search: $.trim($searchInput.val()) || '',
		})
		.done(function (response) {
			$loadingEl.hide();

			if (response.success && response.data.products && response.data.products.length) {
				renderResults(response.data.products);
			} else {
				$results.html(
					'<p class="azonmate-search-empty">No manual products found. ' +
					'<a href="' + escapeAttr((azonMateAdmin.adminUrl || '/wp-admin/') + 'admin.php?page=azonmate-products') + '">Add products here</a>.</p>'
				);
			}
		})
		.fail(function () {
			$loadingEl.hide();
			$results.html('<p class="azonmate-search-error">Failed to load products.</p>');
		});
	}

	/* ======================================================================
	   Insert into Editor
	   ====================================================================== */

	function insertShortcode(shortcode) {
		// TinyMCE (Visual Editor).
		if (typeof tinyMCE !== 'undefined' && tinyMCE.activeEditor && !tinyMCE.activeEditor.isHidden()) {
			tinyMCE.activeEditor.execCommand('mceInsertContent', false, shortcode);
			return;
		}

		// Text / HTML editor fallback.
		var textarea = document.getElementById('content');
		if (textarea) {
			insertAtCaret(textarea, shortcode);
		}
	}

	function insertAtCaret(textarea, text) {
		var start = textarea.selectionStart;
		var end   = textarea.selectionEnd;
		var value = textarea.value;

		textarea.value = value.substring(0, start) + text + value.substring(end);
		textarea.selectionStart = textarea.selectionEnd = start + text.length;
		textarea.focus();

		// Trigger change event.
		$(textarea).trigger('change');
	}

	/* ======================================================================
	   Helpers
	   ====================================================================== */

	function escapeHtml(str) {
		var div = document.createElement('div');
		div.appendChild(document.createTextNode(str));
		return div.innerHTML;
	}

	function escapeAttr(str) {
		return String(str)
			.replace(/&/g, '&amp;')
			.replace(/"/g, '&quot;')
			.replace(/'/g, '&#39;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;');
	}

})(jQuery);
