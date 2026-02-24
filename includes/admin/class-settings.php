<?php
/**
 * Admin settings page.
 *
 * @package AzonMate\Admin
 * @since   1.0.0
 */

namespace AzonMate\Admin;

use AzonMate\API\AmazonAPI;
use AzonMate\API\Marketplace;

// Abort if this file is called directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Settings
 *
 * Manages the AzonMate settings page in the WordPress admin.
 * Provides tabs for API config, display, cache, geo-targeting, tracking, and advanced settings.
 *
 * @since 1.0.0
 */
class Settings {

	/**
	 * Constructor. Register hooks.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'wp_ajax_azon_mate_test_connection', array( $this, 'ajax_test_connection' ) );
		add_action( 'wp_ajax_azon_mate_clear_cache', array( $this, 'ajax_clear_cache' ) );
		add_action( 'wp_ajax_azon_mate_master_fetch', array( $this, 'ajax_master_fetch' ) );
	}

	/**
	 * Add the AzonMate menu and submenu pages.
	 *
	 * @since 1.0.0
	 */
	public function add_admin_menu() {
		// Main menu page.
		add_menu_page(
			__( 'AzonMate Settings', 'azonmate' ),
			__( 'AzonMate', 'azonmate' ),
			'manage_options',
			'azonmate',
			array( $this, 'render_settings_page' ),
			'dashicons-amazon',
			80
		);

		// Settings submenu (same as main).
		add_submenu_page(
			'azonmate',
			__( 'Settings', 'azonmate' ),
			__( 'Settings', 'azonmate' ),
			'manage_options',
			'azonmate',
			array( $this, 'render_settings_page' )
		);

		// Analytics submenu.
		add_submenu_page(
			'azonmate',
			__( 'Analytics', 'azonmate' ),
			__( 'Analytics', 'azonmate' ),
			'manage_options',
			'azonmate-analytics',
			array( $this, 'render_analytics_page' )
		);
	}

	/**
	 * Register all settings with the Settings API.
	 *
	 * @since 1.0.0
	 */
	public function register_settings() {
		// API Settings.
		register_setting( 'azon_mate_api_settings', 'azon_mate_access_key', array(
			'sanitize_callback' => array( $this, 'sanitize_encrypt_key' ),
		) );
		register_setting( 'azon_mate_api_settings', 'azon_mate_secret_key', array(
			'sanitize_callback' => array( $this, 'sanitize_encrypt_key' ),
		) );
		register_setting( 'azon_mate_api_settings', 'azon_mate_partner_tag', array(
			'sanitize_callback' => 'sanitize_text_field',
		) );
		register_setting( 'azon_mate_api_settings', 'azon_mate_marketplace', array(
			'sanitize_callback' => 'sanitize_key',
		) );

		// Display Settings.
		$display_settings = array(
			'azon_mate_default_template'      => 'sanitize_key',
			'azon_mate_show_prices'           => 'absint',
			'azon_mate_show_ratings'          => 'absint',
			'azon_mate_show_prime_badge'      => 'absint',
			'azon_mate_show_description'      => 'absint',
			'azon_mate_show_buy_button'       => 'absint',
			'azon_mate_buy_button_text'       => 'sanitize_text_field',
			'azon_mate_buy_button_color'      => 'sanitize_hex_color',
			'azon_mate_open_new_tab'          => 'absint',
			'azon_mate_nofollow_links'        => 'absint',
			'azon_mate_show_disclosure'       => 'absint',
			'azon_mate_disclosure_text'       => 'sanitize_text_field',
			'azon_mate_disclosure_font_size'  => 'sanitize_text_field',
			'azon_mate_disclosure_color'      => 'sanitize_text_field',
			'azon_mate_disclosure_align'      => 'sanitize_key',
			'azon_mate_custom_css'            => array( $this, 'sanitize_custom_css' ),
		);

		foreach ( $display_settings as $option => $sanitize ) {
			register_setting( 'azon_mate_display_settings', $option, array(
				'sanitize_callback' => $sanitize,
			) );
		}

		// Cache Settings.
		register_setting( 'azon_mate_cache_settings', 'azon_mate_cache_enabled', array(
			'sanitize_callback' => 'absint',
		) );
		register_setting( 'azon_mate_cache_settings', 'azon_mate_cache_duration', array(
			'sanitize_callback' => 'absint',
		) );

		// Geo Settings.
		register_setting( 'azon_mate_geo_settings', 'azon_mate_geo_enabled', array(
			'sanitize_callback' => 'absint',
		) );
		register_setting( 'azon_mate_geo_settings', 'azon_mate_geo_tags', array(
			'sanitize_callback' => array( $this, 'sanitize_geo_tags' ),
		) );
		register_setting( 'azon_mate_geo_settings', 'azon_mate_geo_fallback', array(
			'sanitize_callback' => 'sanitize_key',
		) );

		// Tracking Settings.
		register_setting( 'azon_mate_tracking_settings', 'azon_mate_tracking_enabled', array(
			'sanitize_callback' => 'absint',
		) );

		// Advanced Settings.
		$advanced_settings = array(
			'azon_mate_disable_css'         => 'absint',
			'azon_mate_api_throttle'        => 'absint',
			'azon_mate_debug_mode'          => 'absint',
			'azon_mate_uninstall_delete'    => 'absint',
		);

		foreach ( $advanced_settings as $option => $sanitize ) {
			register_setting( 'azon_mate_advanced_settings', $option, array(
				'sanitize_callback' => $sanitize,
			) );
		}
	}

	/**
	 * Sanitize and encrypt an API key before saving.
	 *
	 * @since 1.0.0
	 *
	 * @param string $value The raw input value.
	 * @return string Encrypted key, or existing value if input is empty.
	 */
	public function sanitize_encrypt_key( $value ) {
		$value = sanitize_text_field( $value );

		if ( empty( $value ) ) {
			// Preserve existing encrypted value when field is submitted empty
			// (fields are always rendered blank for security).
			$option = str_replace( 'sanitize_option_', '', current_filter() );
			return get_option( $option, '' );
		}

		return AmazonAPI::encrypt_key( $value );
	}

	/**
	 * Sanitize custom CSS input.
	 *
	 * @since 1.0.0
	 *
	 * @param string $css Raw CSS input.
	 * @return string Sanitized CSS.
	 */
	public function sanitize_custom_css( $css ) {
		// Strip tags but allow CSS.
		return wp_strip_all_tags( $css );
	}

	/**
	 * Sanitize the geo-targeting tags array.
	 *
	 * @since 1.0.0
	 *
	 * @param array $tags Array of country => tag mappings.
	 * @return array Sanitized array.
	 */
	public function sanitize_geo_tags( $tags ) {
		if ( ! is_array( $tags ) ) {
			return array();
		}

		$sanitized = array();
		foreach ( $tags as $country => $tag ) {
			$country = sanitize_key( $country );
			$tag     = sanitize_text_field( $tag );
			if ( ! empty( $country ) && ! empty( $tag ) ) {
				$sanitized[ $country ] = $tag;
			}
		}

		return $sanitized;
	}

	/**
	 * Render the settings page.
	 *
	 * @since 1.0.0
	 */
	public function render_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized', 'azonmate' ) );
		}

		include AZON_MATE_PLUGIN_DIR . 'includes/admin/views/settings-page.php';
	}

	/**
	 * Render the analytics page.
	 *
	 * @since 1.0.0
	 */
	public function render_analytics_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized', 'azonmate' ) );
		}

		include AZON_MATE_PLUGIN_DIR . 'includes/admin/views/analytics-page.php';
	}

	/**
	 * AJAX: Test API connection.
	 *
	 * @since 1.0.0
	 */
	public function ajax_test_connection() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'azon_mate_admin' ) ) {
			wp_send_json_error( array( 'message' => __( 'Security check failed.', 'azonmate' ) ), 403 );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized.', 'azonmate' ) ), 403 );
		}

		$cache  = new \AzonMate\Cache\CacheManager();
		$api    = new \AzonMate\API\AmazonAPI( $cache );
		$result = $api->test_connection();

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		wp_send_json_success( array( 'message' => __( 'Connection successful! Your API credentials are working.', 'azonmate' ) ) );
	}

	/**
	 * AJAX: Clear product cache.
	 *
	 * @since 1.0.0
	 */
	public function ajax_clear_cache() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'azon_mate_admin' ) ) {
			wp_send_json_error( array( 'message' => __( 'Security check failed.', 'azonmate' ) ), 403 );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized.', 'azonmate' ) ), 403 );
		}

		$cache   = new \AzonMate\Cache\CacheManager();
		$cleared = $cache->clear_all();

		wp_send_json_success( array(
			'message' => sprintf(
				/* translators: %d: Number of cached products cleared */
				__( 'Cache cleared successfully. %d products removed.', 'azonmate' ),
				$cleared
			),
		) );
	}

	/**
	 * AJAX: Master Fetch — refresh all cached products from Amazon API.
	 *
	 * Iterates over every stored product, re-fetches from PA-API with force_fresh,
	 * and updates price, discount, rating, availability, and images.
	 *
	 * @since 1.6.0
	 */
	public function ajax_master_fetch() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'azon_mate_admin' ) ) {
			wp_send_json_error( array( 'message' => __( 'Security check failed.', 'azonmate' ) ), 403 );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized.', 'azonmate' ) ), 403 );
		}

		// Check if API is configured.
		$access_key = get_option( 'azon_mate_access_key', '' );
		$secret_key = get_option( 'azon_mate_secret_key', '' );

		if ( empty( $access_key ) || empty( $secret_key ) ) {
			wp_send_json_error( array( 'message' => __( 'Amazon API is not configured. Set your API keys in Settings → API tab.', 'azonmate' ) ) );
		}

		$cache   = new \AzonMate\Cache\CacheManager();
		$api     = new \AzonMate\API\AmazonAPI( $cache );
		$grouped = $cache->get_all_product_asins();

		if ( empty( $grouped ) ) {
			wp_send_json_error( array( 'message' => __( 'No products found in the database.', 'azonmate' ) ) );
		}

		$updated = 0;
		$failed  = 0;

		foreach ( $grouped as $marketplace => $asins ) {
			// PA-API allows max 10 ASINs per batch.
			$batches = array_chunk( $asins, 10 );

			foreach ( $batches as $batch ) {
				$result = $api->get_items( implode( ',', $batch ), $marketplace, true );

				if ( is_wp_error( $result ) ) {
					$failed += count( $batch );
					continue;
				}

				if ( is_array( $result ) ) {
					$updated += count( $result );
					$failed  += count( $batch ) - count( $result );
				}

				// Small delay to respect API rate limits.
				if ( count( $batches ) > 1 ) {
					usleep( 500000 ); // 0.5 seconds.
				}
			}
		}

		wp_send_json_success( array(
			'message' => sprintf(
				/* translators: 1: number of updated products, 2: number of failed products */
				__( 'Master Fetch complete. %1$d products updated, %2$d failed.', 'azonmate' ),
				$updated,
				$failed
			),
		) );
	}
}
