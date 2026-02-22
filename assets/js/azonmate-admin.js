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
		initPasswordToggle();
		initTabFormPreserve();
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
			.fail(function () {
				$result.addClass('error').text('Request failed. Please try again.');
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

})(jQuery);
