<?php
/**
 * Click tracking for affiliate links.
 *
 * @package AzonMate\Tracking
 * @since   1.0.0
 */

namespace AzonMate\Tracking;

// Abort if this file is called directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class ClickTracker
 *
 * Records clicks on affiliate links via AJAX,
 * storing ASIN, post ID, country, and hashed IP.
 *
 * @since 1.0.0
 */
class ClickTracker {

	/**
	 * The clicks table name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $table;

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		global $wpdb;
		$this->table = $wpdb->prefix . 'azonmate_clicks';

		add_action( 'wp_ajax_azon_mate_track_click', array( $this, 'ajax_track_click' ) );
		add_action( 'wp_ajax_nopriv_azon_mate_track_click', array( $this, 'ajax_track_click' ) );
	}

	/**
	 * Record a click.
	 *
	 * @since 1.0.0
	 *
	 * @param string $asin    Product ASIN.
	 * @param int    $post_id Post ID (optional).
	 * @param string $country Country code (optional).
	 * @return bool True on success.
	 */
	public function record_click( $asin, $post_id = 0, $country = '' ) {
		global $wpdb;

		if ( empty( $asin ) ) {
			return false;
		}

		// Hash the IP for privacy.
		$ip_hash = $this->get_ip_hash();

		// Deduplicate: don't count same IP + ASIN within 1 minute.
		$recent = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$this->table} WHERE asin = %s AND ip_hash = %s AND clicked_at > DATE_SUB(NOW(), INTERVAL 1 MINUTE)",
				$asin,
				$ip_hash
			)
		);

		if ( $recent > 0 ) {
			return false;
		}

		$result = $wpdb->insert(
			$this->table,
			array(
				'asin'       => sanitize_text_field( $asin ),
				'post_id'    => absint( $post_id ),
				'country'    => sanitize_key( $country ),
				'ip_hash'    => $ip_hash,
				'clicked_at' => current_time( 'mysql' ),
			),
			array( '%s', '%d', '%s', '%s', '%s' )
		);

		return false !== $result;
	}

	/**
	 * AJAX: Track a click from the frontend.
	 *
	 * @since 1.0.0
	 */
	public function ajax_track_click() {
		// Verify nonce.
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'azon_mate_public' ) ) {
			wp_send_json_error( array( 'message' => 'Invalid nonce' ), 403 );
		}

		$asin    = sanitize_text_field( wp_unslash( $_POST['asin'] ?? '' ) );
		$post_id = absint( $_POST['postId'] ?? 0 );
		$country = sanitize_key( $_POST['country'] ?? '' );

		$this->record_click( $asin, $post_id, $country );

		wp_send_json_success();
	}

	/**
	 * Get a privacy-safe hash of the visitor's IP.
	 *
	 * @since 1.0.0
	 *
	 * @return string SHA-256 hash.
	 */
	private function get_ip_hash() {
		$ip   = $this->get_visitor_ip();
		$salt = defined( 'AUTH_SALT' ) ? AUTH_SALT : 'azonmate-salt';
		return hash( 'sha256', $ip . $salt . gmdate( 'Y-m-d' ) );
	}

	/**
	 * Get the visitor's IP address.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	private function get_visitor_ip() {
		$headers = array(
			'HTTP_CF_CONNECTING_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_REAL_IP',
			'REMOTE_ADDR',
		);

		foreach ( $headers as $header ) {
			if ( ! empty( $_SERVER[ $header ] ) ) {
				$ip = sanitize_text_field( wp_unslash( $_SERVER[ $header ] ) );
				if ( strpos( $ip, ',' ) !== false ) {
					$ip = trim( explode( ',', $ip )[0] );
				}
				return $ip;
			}
		}

		return '0.0.0.0';
	}
}
