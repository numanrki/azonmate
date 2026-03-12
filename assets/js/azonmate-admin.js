/**
 * AzonMate – Admin JavaScript
 *
 * Handles admin settings page interactions: tabs, AJAX test connection,
 * clear cache, and other admin UI enhancements.
 *
 * @package AzonMate
 * @since   1.0.0
 */

(function ($) {
	'use strict';

	/* global azonMateAdmin, ajaxurl */

	if (typeof azonMateAdmin === 'undefined') {
		return;
	}

	$(document).ready(function () {
		initTabs();
		initTestConnection();
		initClearCache();
		initMasterFetch();
		initPasswordToggle();
		initTabFormPreserve();
		initUpdateChecker();
	});

	/* ======================================================================
	   Settings Tabs
	   ====================================================================== */

	function initTabs() {
		var $tabs = $('.azonmate-settings-tabs .nav-tab');
		var $sections = $('.azonmate-settings-section');

		if (!$tabs.length) {
			return;
		}

		$tabs.on('click', function (e) {
			e.preventDefault();
			var target = $(this).data('tab');

			$tabs.removeClass('nav-tab-active');
			$(this).addClass('nav-tab-active');

			$sections.removeClass('active');
			$('#azonmate-tab-' + target).addClass('active');

			// Update URL hash without scroll jump.
			if (history.replaceState) {
				history.replaceState(null, null, '#' + target);
			}
		});

		// Restore active tab from URL hash.
		var hash = window.location.hash.replace('#', '');
		if (hash) {
			var $target = $tabs.filter('[data-tab="' + hash + '"]');
			if ($target.length) {
				$target.trigger('click');
			}
		}
	}

	/* ======================================================================
	   Test Connection
	   ====================================================================== */

	function initTestConnection() {
		$('#azonmate-test-connection').on('click', function (e) {
			e.preventDefault();

			var $btn = $(this);
			var $result = $('#azonmate-test-result');

			$btn.prop('disabled', true).text(azonMateAdmin.i18n.testing || 'Testing...');
			$result.removeClass('success error').text('');

			$.post(ajaxurl, {
				action: 'azon_mate_test_connection',
				nonce: azonMateAdmin.nonce,
			})
			.done(function (response) {
				if (response.success) {
					$result.addClass('success').text(response.data.message || 'Connection successful!');
				} else {
					$result.addClass('error').text(response.data.message || 'Connection failed.');
				}
			})
			.fail(function (jqXHR) {
				var msg = 'Request failed.';
				try {
					var json = JSON.parse(jqXHR.responseText);
					if (json && json.data && json.data.message) {
						msg = json.data.message;
					}
				} catch (e) {
					if (jqXHR.responseText) {
						msg = 'Server error (' + jqXHR.status + '): ' + jqXHR.responseText.substring(0, 200);
					}
				}
				$result.addClass('error').text(msg);
			})
			.always(function () {
				$btn.prop('disabled', false).text(azonMateAdmin.i18n.testConnection || 'Test Connection');
			});
		});
	}

	/* ======================================================================
	   Clear Cache
	   ====================================================================== */

	function initClearCache() {
		$('#azonmate-clear-cache').on('click', function (e) {
			e.preventDefault();

			if (!confirm(azonMateAdmin.i18n.confirmClear || 'Are you sure you want to clear the product cache?')) {
				return;
			}

			var $btn = $(this);
			var $result = $('#azonmate-cache-result');

			$btn.prop('disabled', true);
			$result.text('');

			$.post(ajaxurl, {
				action: 'azon_mate_clear_cache',
				nonce: azonMateAdmin.nonce,
			})
			.done(function (response) {
				if (response.success) {
					$result.addClass('success').text(response.data.message || 'Cache cleared.');
				} else {
					$result.addClass('error').text(response.data.message || 'Failed to clear cache.');
				}
			})
			.fail(function () {
				$result.addClass('error').text('Request failed.');
			})
			.always(function () {
				$btn.prop('disabled', false);
			});
		});
	}

	/* ======================================================================
	   Password/Secret Field Toggle
	   ====================================================================== */

	function initMasterFetch() {
		$('#azonmate-master-fetch').on('click', function (e) {
			e.preventDefault();

			if (!confirm(azonMateAdmin.i18n.confirmMasterFetch || 'This will re-fetch ALL products from Amazon API. This may take a while. Continue?')) {
				return;
			}

			var $btn = $(this);
			var $result = $('#azonmate-master-fetch-result');

			$btn.prop('disabled', true).text(azonMateAdmin.i18n.fetching || 'Fetching...');
			$result.removeClass('success error').text('');

			$.post(ajaxurl, {
				action: 'azon_mate_master_fetch',
				nonce: azonMateAdmin.nonce,
			})
			.done(function (response) {
				if (response.success) {
					$result.addClass('success').text(response.data.message || 'All products refreshed!');
				} else {
					$result.addClass('error').text(response.data.message || 'Master fetch failed.');
				}
			})
			.fail(function (jqXHR) {
				var msg = 'Request failed.';
				try {
					var json = JSON.parse(jqXHR.responseText);
					if (json && json.data && json.data.message) {
						msg = json.data.message;
					}
				} catch (e) {
					if (jqXHR.responseText) {
						msg = 'Server error (' + jqXHR.status + '): ' + jqXHR.responseText.substring(0, 200);
					}
				}
				$result.addClass('error').text(msg);
			})
			.always(function () {
				$btn.prop('disabled', false).text(azonMateAdmin.i18n.masterFetch || 'Fetch All Products');
			});
		});
	}

	function initPasswordToggle() {
		$('.azonmate-toggle-secret').on('click', function (e) {
			e.preventDefault();
			var $input = $(this).prev('input');
			var type = $input.attr('type') === 'password' ? 'text' : 'password';
			$input.attr('type', type);
			$(this).find('.dashicons')
				.toggleClass('dashicons-visibility')
				.toggleClass('dashicons-hidden');
		});
	}

	/* ======================================================================
	   Preserve Active Tab on Form Submit
	   ====================================================================== */

	function initTabFormPreserve() {
		$('.azonmate-settings-section form').on('submit', function () {
			var $active = $('.azonmate-settings-tabs .nav-tab-active');
			if ($active.length) {
				var tab = $active.data('tab');
				var $referer = $(this).find('input[name="_wp_http_referer"]');
				if ($referer.length && tab) {
					var url = $referer.val().split('#')[0];
					$referer.val(url + '#' + tab);
				}
			}
		});
	}

	/* ======================================================================
	   Plugin Update Checker (Settings → Updates tab)
	   ====================================================================== */

	function initUpdateChecker() {
		var $checkBtn   = $('#azonmate-check-update');
		var $installBtn = $('#azonmate-install-update');
		var $releaseLink = $('#azonmate-release-link');
		var $remoteVer  = $('#azonmate-remote-version');
		var $status     = $('#azonmate-update-status');
		var $result     = $('#azonmate-update-result');

		if (!$checkBtn.length) {
			return;
		}

		/* ---- Check for Updates ---- */
		$checkBtn.on('click', function (e) {
			e.preventDefault();
			$checkBtn.prop('disabled', true).find('.dashicons').addClass('azonmate-spin');
			$status.text('Checking for updates\u2026');
			$result.hide().removeClass('success error').text('');
			$installBtn.hide();
			$releaseLink.hide();

			$.post(ajaxurl, {
				action: 'azon_mate_check_update',
				nonce: azonMateAdmin.nonce,
			})
			.done(function (response) {
				if (response.success) {
					var d = response.data;
					$remoteVer.text(d.remote_version);

					if (d.has_update) {
						$status.html('<span style="color:#d63638;font-weight:600;">' + d.message + '</span>');
						$installBtn.show();
						if (d.release_url) {
							$releaseLink.attr('href', d.release_url).show();
						}
					} else {
						$status.html('<span style="color:#00a32a;font-weight:600;">' + d.message + '</span>');
					}
				} else {
					$status.html('<span style="color:#d63638;">' + (response.data.message || 'Check failed.') + '</span>');
				}
			})
			.fail(function () {
				$status.html('<span style="color:#d63638;">Request failed. Check your connection.</span>');
			})
			.always(function () {
				$checkBtn.prop('disabled', false).find('.dashicons').removeClass('azonmate-spin');
			});
		});

		/* ---- Install Update ---- */
		$installBtn.on('click', function (e) {
			e.preventDefault();

			if (!confirm('Install the latest AzonMate update now?')) {
				return;
			}

			$installBtn.prop('disabled', true).text('Installing\u2026');
			$result.show().removeClass('success error').text('Installing update, please wait\u2026');

			$.post(ajaxurl, {
				action: 'azon_mate_install_update',
				nonce: azonMateAdmin.nonce,
			})
			.done(function (response) {
				if (response.success) {
					$result.addClass('success').text(response.data.message || 'Updated successfully!');
					setTimeout(function () {
						window.location.hash = '#updates';
						window.location.reload();
					}, 1500);
				} else {
					$result.addClass('error').text(response.data.message || 'Update failed.');
					$installBtn.prop('disabled', false).html('<span class="dashicons dashicons-download" style="margin-top:4px;"></span> Install Update');
				}
			})
			.fail(function () {
				$result.addClass('error').text('Request failed.');
				$installBtn.prop('disabled', false).html('<span class="dashicons dashicons-download" style="margin-top:4px;"></span> Install Update');
			});
		});
	}

})(jQuery);
