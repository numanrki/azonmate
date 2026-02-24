/**
 * AzonMate – Manual Products Admin Page JS
 *
 * Handles the product CRUD operations on the Products admin page.
 *
 * @package AzonMate
 * @since   1.1.0
 */
(function ($) {
	'use strict';

	var $modal = $('#azonmate-product-modal');
	var $form = $('#azonmate-product-form');
	var $list = $('#azonmate-products-list');
	var isEditing = false;

	/**
	 * Initialize the page.
	 */
	function init() {
		loadProducts();
		bindEvents();
	}

	/**
	 * Bind all UI events.
	 */
	function bindEvents() {
		// Add new product.
		$('#azonmate-add-product-btn').on('click', function () {
			openModal();
		});

		// Close modal.
		$modal.on('click', '.azonmate-modal-close, .azonmate-modal-cancel, .azonmate-modal-overlay', function () {
			closeModal();
		});

		// Escape key.
		$(document).on('keydown', function (e) {
			if (e.key === 'Escape' && $modal.is(':visible')) {
				closeModal();
			}
		});

		// Save product.
		$form.on('submit', function (e) {
			e.preventDefault();
			saveProduct();
		});

		// ASIN field → update usage preview.
		$form.find('[name="asin"]').on('input', updateUsagePreview);

		// Image URL → preview.
		$form.find('[name="image_url"]').on('input', function () {
			var url = $(this).val().trim();
			var $preview = $('#azonmate-image-preview');
			if (url) {
				$preview.show().find('img').attr('src', url);
			} else {
				$preview.hide();
			}
		});

		// Media uploader.
		$modal.on('click', '.azonmate-upload-image', function (e) {
			e.preventDefault();
			var $target = $($(this).data('target'));
			if (wp.media) {
				var frame = wp.media({
					title: 'Select Product Image',
					multiple: false,
					library: { type: 'image' },
				});
				frame.on('select', function () {
					var attachment = frame.state().get('selection').first().toJSON();
					$target.val(attachment.url).trigger('input');
				});
				frame.open();
			}
		});

		// Delegate events on product list.
		$list.on('click', '.azonmate-edit-product', function () {
			var asin = $(this).data('asin');
			editProduct(asin);
		});

		$list.on('click', '.azonmate-delete-product', function () {
			var asin = $(this).data('asin');
			var title = $(this).data('title');
			deleteProduct(asin, title);
		});

		$list.on('click', '.azonmate-fetch-product', function () {
			var asin = $(this).data('asin');
			fetchProduct(asin, $(this));
		});

		$list.on('click', '.azonmate-copy-shortcode', function () {
			var shortcode = $(this).data('shortcode');
			copyToClipboard(shortcode);
		});
	}

	/**
	 * Load products and render the list.
	 */
	function loadProducts() {
		$list.html('<div class="azonmate-products-loading"><span class="spinner is-active" style="float:none;"></span> Loading products...</div>');

		$.post(azonMateAdmin.ajaxUrl, {
			action: 'azon_mate_get_manual_products',
			nonce: azonMateAdmin.nonce,
		}, function (response) {
			if (response.success && response.data.products.length > 0) {
				renderProducts(response.data.products);
			} else {
				renderEmpty();
			}
		}).fail(function () {
			renderEmpty();
		});
	}

	/**
	 * Render the products grid.
	 */
	function renderProducts(products) {
		var html = '<div class="azonmate-products-grid">';

		products.forEach(function (p) {
			var imgHtml = p.image_medium || p.image_small || p.image_large
				? '<img src="' + escHtml(p.image_medium || p.image_small || p.image_large) + '" alt="' + escHtml(p.title) + '" />'
				: '<span class="dashicons dashicons-format-image"></span>';

			var priceHtml = p.price_display ? '<span class="azonmate-card-price">' + escHtml(p.price_display) + '</span> · ' : '';
			var ratingHtml = p.rating > 0 ? '★ ' + p.rating + ' · ' : '';
			var badgeHtml = p.badge_label ? '<span class="azonmate-card-badge" title="Badge label">' + escHtml(p.badge_label) + '</span> · ' : '';
			var shortcode = '[azonmate box="' + p.asin + '"]';

			html += '<div class="azonmate-product-card" data-asin="' + escHtml(p.asin) + '">';
			html += '<div class="azonmate-product-card-inner">';
			html += '<div class="azonmate-product-card-image">' + imgHtml + '</div>';
			html += '<div class="azonmate-product-card-info">';
			html += '<h3 title="' + escHtml(p.title) + '">' + escHtml(p.title) + '</h3>';
			html += '<div class="azonmate-product-card-meta">' + badgeHtml + priceHtml + ratingHtml + '<span class="azonmate-card-asin">' + escHtml(p.asin) + '</span></div>';
			if (p.brand) {
				html += '<div class="azonmate-product-card-meta">' + escHtml(p.brand) + '</div>';
			}
			html += '</div>';
			html += '</div>';
			html += '<div class="azonmate-product-card-actions">';
			html += '<button type="button" class="button button-small azonmate-edit-product" data-asin="' + escHtml(p.asin) + '"><span class="dashicons dashicons-edit" style="font-size:14px;vertical-align:text-bottom;"></span> Edit</button>';
			html += '<button type="button" class="button button-small azonmate-fetch-product" data-asin="' + escHtml(p.asin) + '" title="Fetch fresh data from Amazon API"><span class="dashicons dashicons-update" style="font-size:14px;vertical-align:text-bottom;"></span> Fetch</button>';
			html += '<button type="button" class="button button-small button-link-delete azonmate-delete-product" data-asin="' + escHtml(p.asin) + '" data-title="' + escHtml(p.title) + '"><span class="dashicons dashicons-trash" style="font-size:14px;vertical-align:text-bottom;"></span> Delete</button>';
			html += '<button type="button" class="button button-small azonmate-copy-shortcode" data-shortcode="' + escHtml(shortcode) + '" title="Copy shortcode"><span class="dashicons dashicons-clipboard" style="font-size:14px;vertical-align:text-bottom;"></span> Copy Shortcode</button>';
			html += '</div>';
			html += '</div>';
		});

		html += '</div>';
		$list.html(html);
	}

	/**
	 * Render empty state.
	 */
	function renderEmpty() {
		$list.html(
			'<div class="azonmate-products-empty">' +
			'<span class="dashicons dashicons-cart"></span>' +
			'<h2>No products yet</h2>' +
			'<p>Add your first product manually — no Amazon API required! You can add Amazon affiliate products or any custom product cards.</p>' +
			'<button type="button" class="button button-primary button-hero" id="azonmate-add-product-btn-empty">Add Your First Product</button>' +
			'</div>'
		);

		$list.find('#azonmate-add-product-btn-empty').on('click', function () {
			openModal();
		});
	}

	/**
	 * Open the modal (for new product).
	 */
	function openModal(product) {
		isEditing = !!product;
		$form[0].reset();
		$('#azonmate-image-preview').hide();
		$('#azonmate-usage-info').hide();
		$('#azonmate-save-result').text('').removeClass('success error');

		if (product) {
			$('#azonmate-modal-title').text('Edit Product');
			$form.find('[name="asin"]').val(product.asin).prop('readonly', true);
			$form.find('[name="title"]').val(product.title);
			$form.find('[name="url"]').val(product.url);
			$form.find('[name="image_url"]').val(product.image_medium || product.image_small || '');
			$form.find('[name="brand"]').val(product.brand);
			$form.find('[name="description"]').val(product.description);
			$form.find('[name="price_display"]').val(product.price_display);
			$form.find('[name="price_amount"]').val(product.price_amount || '');
			$form.find('[name="list_price_amount"]').val(product.list_price_amount || '');
			$form.find('[name="savings_percentage"]').val(product.savings_percentage || '');
			$form.find('[name="rating"]').val(product.rating || '');
			$form.find('[name="review_count"]').val(product.review_count || '');
			$form.find('[name="is_prime"]').prop('checked', product.is_prime);
			$form.find('[name="browse_node"]').val(product.browse_node);
			$form.find('[name="marketplace"]').val(product.marketplace);
			$form.find('[name="badge_label"]').val(product.badge_label || '');
			$form.find('[name="button_text"]').val(product.button_text || '');

			if (Array.isArray(product.features) && product.features.length) {
				$form.find('[name="features"]').val(product.features.join('\n'));
			}

			var imgUrl = product.image_medium || product.image_small || product.image_large;
			if (imgUrl) {
				$('#azonmate-image-preview').show().find('img').attr('src', imgUrl);
			}

			updateUsagePreview();
		} else {
			$('#azonmate-modal-title').text('Add New Product');
			$form.find('[name="asin"]').prop('readonly', false);
		}

		$modal.show();
	}

	/**
	 * Close the modal.
	 */
	function closeModal() {
		$modal.hide();
	}

	/**
	 * Save the product via AJAX.
	 */
	function saveProduct() {
		var $btn = $('#azonmate-save-product-btn');
		var $result = $('#azonmate-save-result');
		$btn.prop('disabled', true).text('Saving...');
		$result.text('').removeClass('success error');

		var formData = {
			action: 'azon_mate_save_manual_product',
			nonce: azonMateAdmin.nonce,
		};

		// Gather form fields.
		$form.serializeArray().forEach(function (item) {
			formData[item.name] = item.value;
		});

		// Checkbox.
		formData.is_prime = $form.find('[name="is_prime"]').is(':checked') ? '1' : '0';

		$.post(azonMateAdmin.ajaxUrl, formData, function (response) {
			$btn.prop('disabled', false).text('Save Product');

			if (response.success) {
				$result.text(response.data.message).addClass('success');
				setTimeout(function () {
					closeModal();
					loadProducts();
				}, 800);
			} else {
				$result.text(response.data.message || 'Error saving product.').addClass('error');
			}
		}).fail(function () {
			$btn.prop('disabled', false).text('Save Product');
			$result.text('Network error. Please try again.').addClass('error');
		});
	}

	/**
	 * Load a product for editing.
	 */
	function editProduct(asin) {
		// Find product from current list.
		var $card = $list.find('[data-asin="' + asin + '"]');
		if (!$card.length) return;

		// Fetch fresh data.
		$.post(azonMateAdmin.ajaxUrl, {
			action: 'azon_mate_get_manual_products',
			nonce: azonMateAdmin.nonce,
			search: asin,
		}, function (response) {
			if (response.success && response.data.products.length > 0) {
				var product = response.data.products.find(function (p) {
					return p.asin === asin;
				});
				if (product) {
					openModal(product);
				}
			}
		});
	}

	/**
	 * Delete a product.
	 */
	function deleteProduct(asin, title) {
		if (!confirm('Delete "' + title + '"?\n\nThis cannot be undone. Any shortcodes using this product will stop working.')) {
			return;
		}

		$.post(azonMateAdmin.ajaxUrl, {
			action: 'azon_mate_delete_manual_product',
			nonce: azonMateAdmin.nonce,
			asin: asin,
		}, function (response) {
			if (response.success) {
				loadProducts();
			} else {
				alert(response.data.message || 'Failed to delete product.');
			}
		});
	}

	/**
	 * Fetch fresh product data from Amazon API for a single product.
	 */
	function fetchProduct(asin, $btn) {
		var origHtml = $btn.html();
		$btn.prop('disabled', true).html('<span class="dashicons dashicons-update" style="font-size:14px;vertical-align:text-bottom;animation:rotation 1s infinite linear;"></span> Fetching...');

		$.post(azonMateAdmin.ajaxUrl, {
			action: 'azon_mate_fetch_product',
			nonce: azonMateAdmin.nonce,
			asin: asin,
		}, function (response) {
			$btn.prop('disabled', false).html(origHtml);

			if (response.success) {
				showToast(response.data.message || 'Product updated!');
				loadProducts();
			} else {
				alert(response.data.message || 'Fetch failed.');
			}
		}).fail(function () {
			$btn.prop('disabled', false).html(origHtml);
			alert('Network error. Please try again.');
		});
	}

	/**
	 * Update the usage preview section.
	 */
	function updateUsagePreview() {
		var asin = $form.find('[name="asin"]').val().trim();
		var $usage = $('#azonmate-usage-info');

		if (asin) {
			$('#azonmate-usage-box').text('[azonmate box="' + asin + '"]');
			$('#azonmate-usage-link').text('[azonmate link="' + asin + '"]Click here[/azonmate]');
			$('#azonmate-usage-image').text('[azonmate image="' + asin + '"]');
			$('#azonmate-usage-showcase').text('[azonmate showcase="' + asin + '" layout="grid"]');
			$usage.show();
		} else {
			$usage.hide();
		}
	}

	/**
	 * Copy text to clipboard with toast notification.
	 */
	function copyToClipboard(text) {
		if (navigator.clipboard) {
			navigator.clipboard.writeText(text).then(function () {
				showToast('Copied: ' + text);
			});
		} else {
			var $temp = $('<textarea>');
			$('body').append($temp);
			$temp.val(text).select();
			document.execCommand('copy');
			$temp.remove();
			showToast('Copied: ' + text);
		}
	}

	/**
	 * Show a brief toast notification.
	 */
	function showToast(message) {
		var $toast = $('<div class="azonmate-copied-toast">' + escHtml(message) + '</div>');
		$('body').append($toast);
		setTimeout(function () {
			$toast.remove();
		}, 2200);
	}

	/**
	 * Simple HTML escape.
	 */
	function escHtml(str) {
		if (typeof str !== 'string') return '';
		return str
			.replace(/&/g, '&amp;')
			.replace(/</g, '&lt;')
			.replace(/>/g, '&gt;')
			.replace(/"/g, '&quot;');
	}

	// GO.
	$(document).ready(init);

})(jQuery);
