<?php
/**
 * AJAX-powered product search handler for the editor.
 *
 * @package AzonMate\Admin
 * @since   1.0.0
 */

namespace AzonMate\Admin;

use AzonMate\API\AmazonAPI;
use AzonMate\Cache\CacheManager;

// Abort if this file is called directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class ProductSearch
 *
 * Handles AJAX search requests from both the Classic Editor modal
 * and the Gutenberg block editor.
 *
 * @since 1.0.0
 */
class ProductSearch {

	/**
	 * Amazon API instance.
	 *
	 * @since 1.0.0
	 * @var AmazonAPI
	 */
	private $api;

	/**
	 * Rate limit: max requests per window.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	private $rate_limit = 5;

	/**
	 * Rate limit window in seconds.
	 *
	 * @since 1.0.0
	 * @var int
	 */
	private $rate_window = 10;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param AmazonAPI $api Amazon API instance.
	 */
	public function __construct( AmazonAPI $api ) {
		$this->api = $api;

		// Register AJAX endpoints.
		add_action( 'wp_ajax_azon_mate_search_products', array( $this, 'ajax_search_products' ) );
		add_action( 'wp_ajax_azon_mate_lookup_asin', array( $this, 'ajax_lookup_asin' ) );

		// Classic Editor TinyMCE button.
		add_action( 'admin_init', array( $this, 'register_tinymce_plugin' ) );
	}

	/**
	 * Register the TinyMCE button for Classic Editor.
	 *
	 * @since 1.0.0
	 */
	public function register_tinymce_plugin() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		add_filter( 'mce_buttons', array( $this, 'add_tinymce_button' ) );
		add_filter( 'mce_external_plugins', array( $this, 'add_tinymce_plugin_script' ) );
	}

	/**
	 * Add the AzonMate button to TinyMCE toolbar.
	 *
	 * @since 1.0.0
	 *
	 * @param array $buttons Existing toolbar buttons.
	 * @return array
	 */
	public function add_tinymce_button( $buttons ) {
		$buttons[] = 'azonmate_search';
		return $buttons;
	}

	/**
	 * Register the TinyMCE plugin script.
	 *
	 * @since 1.0.0
	 *
	 * @param array $plugins Existing TinyMCE plugins.
	 * @return array
	 */
	public function add_tinymce_plugin_script( $plugins ) {
		$plugins['azonmate_search'] = AZON_MATE_PLUGIN_URL . 'assets/js/azonmate-search-modal.js';
		return $plugins;
	}

	/**
	 * Check rate limiting for the current user.
	 *
	 * @since 1.0.0
	 *
	 * @return bool True if within rate limit, false if exceeded.
	 */
	private function check_rate_limit() {
		$user_id       = get_current_user_id();
		$transient_key = 'azon_mate_search_rate_' . $user_id;
		$request_count = get_transient( $transient_key );

		if ( false === $request_count ) {
			set_transient( $transient_key, 1, $this->rate_window );
			return true;
		}

		if ( (int) $request_count >= $this->rate_limit ) {
			return false;
		}

		set_transient( $transient_key, (int) $request_count + 1, $this->rate_window );
		return true;
	}

	/**
	 * AJAX: Search for products by keyword.
	 *
	 * @since 1.0.0
	 */
	public function ajax_search_products() {
		// 1. Verify nonce.
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'azon_mate_admin' ) ) {
			wp_send_json_error( array( 'message' => __( 'Security check failed.', 'azonmate' ) ), 403 );
		}

		// 2. Check capabilities.
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized.', 'azonmate' ) ), 403 );
		}

		// 3. Rate limit check.
		if ( ! $this->check_rate_limit() ) {
			wp_send_json_error( array( 'message' => __( 'Too many requests. Please wait a moment.', 'azonmate' ) ), 429 );
		}

		// 4. Sanitize input.
		$keywords    = sanitize_text_field( wp_unslash( $_POST['keywords'] ?? '' ) );
		$marketplace = sanitize_key( $_POST['marketplace'] ?? '' );
		$category    = sanitize_text_field( $_POST['category'] ?? 'All' );
		$page        = absint( $_POST['page'] ?? 1 );

		// 5. Validate input.
		if ( empty( $keywords ) ) {
			wp_send_json_error( array( 'message' => __( 'Please enter search keywords.', 'azonmate' ) ) );
		}

		// 6. Process search — try API first, fallback to manual products.
		$results = $this->api->search_items( $keywords, $marketplace, $page, $category );

		if ( is_wp_error( $results ) ) {
			// API failed — fallback to searching manual products in the local DB.
			$cache          = new CacheManager();
			$manual_results = $cache->search_manual_products( $keywords );

			if ( ! empty( $manual_results ) ) {
				$products_data = array();
				foreach ( $manual_results as $product ) {
					$products_data[] = $product->to_array();
				}

				wp_send_json_success( array(
					'products' => $products_data,
					'total'    => count( $products_data ),
					'pages'    => 1,
					'page'     => 1,
					'source'   => 'manual',
				) );
			}

			wp_send_json_error( array( 'message' => $results->get_error_message() ) );
		}

		// 7. Transform products for response.
		$products_data = array();
		foreach ( $results['products'] as $product ) {
			$products_data[] = $product->to_array();
		}

		wp_send_json_success( array(
			'products' => $products_data,
			'total'    => $results['total'],
			'pages'    => $results['pages'],
			'page'     => $page,
		) );
	}

	/**
	 * AJAX: Look up a single product by ASIN.
	 *
	 * @since 1.0.0
	 */
	public function ajax_lookup_asin() {
		// 1. Verify nonce.
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'azon_mate_admin' ) ) {
			wp_send_json_error( array( 'message' => __( 'Security check failed.', 'azonmate' ) ), 403 );
		}

		// 2. Check capabilities.
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized.', 'azonmate' ) ), 403 );
		}

		// 3. Rate limit check.
		if ( ! $this->check_rate_limit() ) {
			wp_send_json_error( array( 'message' => __( 'Too many requests. Please wait a moment.', 'azonmate' ) ), 429 );
		}

		// 4. Sanitize input.
		$asin        = sanitize_text_field( wp_unslash( $_POST['asin'] ?? '' ) );
		$marketplace = sanitize_key( $_POST['marketplace'] ?? '' );

		// 5. Validate input.
		if ( empty( $asin ) ) {
			wp_send_json_error( array( 'message' => __( 'Please enter an ASIN.', 'azonmate' ) ) );
		}

		// Basic ASIN validation (10 alphanumeric characters).
		if ( ! preg_match( '/^[A-Z0-9]{10}$/i', $asin ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid ASIN format. ASINs are 10 alphanumeric characters.', 'azonmate' ) ) );
		}

		// 6. Fetch product — try API first, fallback to local cache/manual.
		$product = $this->api->get_item( $asin, $marketplace );

		if ( is_wp_error( $product ) ) {
			// API failed — try local DB (manual products or cached).
			$cache         = new CacheManager();
			$local_product = $cache->get_product( $asin, $marketplace ?: get_option( 'azon_mate_marketplace', 'US' ) );

			if ( $local_product ) {
				wp_send_json_success( array(
					'product' => $local_product->to_array(),
					'source'  => 'local',
				) );
			}

			wp_send_json_error( array( 'message' => $product->get_error_message() ) );
		}

		wp_send_json_success( array(
			'product' => $product->to_array(),
		) );
	}
}
