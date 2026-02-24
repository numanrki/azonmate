<?php
/**
 * Manual product management (no API required).
 *
 * @package AzonMate\Admin
 * @since   1.1.0
 */

namespace AzonMate\Admin;

use AzonMate\Models\Product;
use AzonMate\Cache\CacheManager;

// Abort if this file is called directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class ManualProducts
 *
 * Provides a UI and AJAX endpoints for creating, editing, and deleting
 * products manually — works without Amazon API access.
 *
 * @since 1.1.0
 */
class ManualProducts {

	/**
	 * Cache manager instance.
	 *
	 * @since 1.1.0
	 * @var CacheManager
	 */
	private $cache;

	/**
	 * Constructor.
	 *
	 * @since 1.1.0
	 *
	 * @param CacheManager $cache Cache manager.
	 */
	public function __construct( CacheManager $cache ) {
		$this->cache = $cache;

		// Admin menu.
		add_action( 'admin_menu', array( $this, 'add_submenu' ) );

		// AJAX endpoints.
		add_action( 'wp_ajax_azon_mate_save_manual_product', array( $this, 'ajax_save_product' ) );
		add_action( 'wp_ajax_azon_mate_delete_manual_product', array( $this, 'ajax_delete_product' ) );
		add_action( 'wp_ajax_azon_mate_get_manual_products', array( $this, 'ajax_get_products' ) );
		add_action( 'wp_ajax_azon_mate_fetch_product', array( $this, 'ajax_fetch_product' ) );
	}

	/**
	 * Add submenu page under AzonMate.
	 *
	 * @since 1.1.0
	 */
	public function add_submenu() {
		add_submenu_page(
			'azonmate',
			__( 'Products', 'azonmate' ),
			__( 'Products', 'azonmate' ),
			'manage_options',
			'azonmate-products',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Render the manual products management page.
	 *
	 * @since 1.1.0
	 */
	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized', 'azonmate' ) );
		}

		include AZON_MATE_PLUGIN_DIR . 'includes/admin/views/manual-products-page.php';
	}

	/**
	 * AJAX: Save (create or update) a manual product.
	 *
	 * @since 1.1.0
	 */
	public function ajax_save_product() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'azon_mate_admin' ) ) {
			wp_send_json_error( array( 'message' => __( 'Security check failed.', 'azonmate' ) ), 403 );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized.', 'azonmate' ) ), 403 );
		}

		// Gather and validate input.
		$data = array(
			'asin'               => sanitize_text_field( wp_unslash( $_POST['asin'] ?? '' ) ),
			'marketplace'        => sanitize_key( $_POST['marketplace'] ?? get_option( 'azon_mate_marketplace', 'US' ) ),
			'title'              => sanitize_text_field( wp_unslash( $_POST['title'] ?? '' ) ),
			'url'                => esc_url_raw( wp_unslash( $_POST['url'] ?? '' ) ),
			'image_url'          => esc_url_raw( wp_unslash( $_POST['image_url'] ?? '' ) ),
			'price_display'      => sanitize_text_field( wp_unslash( $_POST['price_display'] ?? '' ) ),
			'price_amount'       => floatval( $_POST['price_amount'] ?? 0 ),
			'price_currency'     => sanitize_text_field( wp_unslash( $_POST['price_currency'] ?? 'USD' ) ),
			'list_price_amount'  => floatval( $_POST['list_price_amount'] ?? 0 ),
			'savings_percentage' => absint( $_POST['savings_percentage'] ?? 0 ),
			'rating'             => floatval( $_POST['rating'] ?? 0 ),
			'review_count'       => absint( $_POST['review_count'] ?? 0 ),
			'is_prime'           => ! empty( $_POST['is_prime'] ),
			'availability'       => sanitize_text_field( wp_unslash( $_POST['availability'] ?? 'In Stock' ) ),
			'brand'              => sanitize_text_field( wp_unslash( $_POST['brand'] ?? '' ) ),
			'description'        => sanitize_textarea_field( wp_unslash( $_POST['description'] ?? '' ) ),
			'features'           => sanitize_textarea_field( wp_unslash( $_POST['features'] ?? '' ) ),
			'browse_node'        => sanitize_text_field( wp_unslash( $_POST['browse_node'] ?? '' ) ),
			'badge_label'        => sanitize_text_field( wp_unslash( $_POST['badge_label'] ?? '' ) ),
			'button_text'        => sanitize_text_field( wp_unslash( $_POST['button_text'] ?? '' ) ),
		);

		// Validate required fields.
		if ( empty( $data['asin'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Product ID/ASIN is required.', 'azonmate' ) ) );
		}

		if ( empty( $data['title'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Product title is required.', 'azonmate' ) ) );
		}

		// Auto-generate Amazon URL if not provided.
		if ( empty( $data['url'] ) ) {
			$tag    = get_option( 'azon_mate_partner_tag', '' );
			$market = \AzonMate\API\Marketplace::get_config( $data['marketplace'] );
			$domain = $market ? $market['domain'] : 'www.amazon.com';
			$data['url'] = sprintf( 'https://%s/dp/%s?tag=%s', $domain, $data['asin'], $tag );
		}

		// Create product model.
		$product = Product::from_manual_input( $data );

		// Save to cache/DB.
		$saved = $this->cache->save_product( $product );

		if ( ! $saved ) {
			wp_send_json_error( array( 'message' => __( 'Failed to save product. Please try again.', 'azonmate' ) ) );
		}

		wp_send_json_success( array(
			'message' => __( 'Product saved successfully!', 'azonmate' ),
			'product' => $product->to_array(),
		) );
	}

	/**
	 * AJAX: Delete a manual product.
	 *
	 * @since 1.1.0
	 */
	public function ajax_delete_product() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'azon_mate_admin' ) ) {
			wp_send_json_error( array( 'message' => __( 'Security check failed.', 'azonmate' ) ), 403 );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized.', 'azonmate' ) ), 403 );
		}

		$asin = sanitize_text_field( wp_unslash( $_POST['asin'] ?? '' ) );

		if ( empty( $asin ) ) {
			wp_send_json_error( array( 'message' => __( 'Product ID is required.', 'azonmate' ) ) );
		}

		$deleted = $this->cache->delete_manual_product( $asin );

		if ( ! $deleted ) {
			wp_send_json_error( array( 'message' => __( 'Failed to delete product.', 'azonmate' ) ) );
		}

		wp_send_json_success( array(
			'message' => __( 'Product deleted.', 'azonmate' ),
		) );
	}

	/**
	 * AJAX: Get all manual products (for listing / search modal).
	 *
	 * @since 1.1.0
	 */
	public function ajax_get_products() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'azon_mate_admin' ) ) {
			wp_send_json_error( array( 'message' => __( 'Security check failed.', 'azonmate' ) ), 403 );
		}

		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized.', 'azonmate' ) ), 403 );
		}

		$search   = sanitize_text_field( wp_unslash( $_POST['search'] ?? '' ) );
		$products = $this->cache->search_manual_products( $search );

		$products_data = array();
		foreach ( $products as $product ) {
			$products_data[] = $product->to_array();
		}

		wp_send_json_success( array(
			'products' => $products_data,
			'total'    => count( $products_data ),
		) );
	}

	/**
	 * AJAX: Fetch fresh product data from Amazon API.
	 *
	 * Pulls real-time data for a single ASIN, bypassing cache,
	 * and updates the stored product with the latest price, rating, images, etc.
	 *
	 * @since 1.6.0
	 */
	public function ajax_fetch_product() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'azon_mate_admin' ) ) {
			wp_send_json_error( array( 'message' => __( 'Security check failed.', 'azonmate' ) ), 403 );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized.', 'azonmate' ) ), 403 );
		}

		$asin = sanitize_text_field( wp_unslash( $_POST['asin'] ?? '' ) );

		if ( empty( $asin ) ) {
			wp_send_json_error( array( 'message' => __( 'Product ASIN is required.', 'azonmate' ) ) );
		}

		// Check if API is configured.
		$access_key = get_option( 'azon_mate_access_key', '' );
		$secret_key = get_option( 'azon_mate_secret_key', '' );

		if ( empty( $access_key ) || empty( $secret_key ) ) {
			wp_send_json_error( array( 'message' => __( 'Amazon API is not configured. Set your API keys in Settings.', 'azonmate' ) ) );
		}

		$api    = new \AzonMate\API\AmazonAPI( $this->cache );
		$result = $api->get_item( $asin, '', true ); // force_fresh = true

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		if ( ! $result ) {
			wp_send_json_error( array( 'message' => __( 'Product not found on Amazon.', 'azonmate' ) ) );
		}

		wp_send_json_success( array(
			'message' => sprintf(
				/* translators: %s: Product ASIN */
				__( 'Product %s updated with fresh Amazon data.', 'azonmate' ),
				$asin
			),
			'product' => $result->to_array(),
		) );
	}
}
