<?php
/**
 * Click tracking analytics dashboard.
 *
 * @package AzonMate\Admin
 * @since   1.0.0
 */

namespace AzonMate\Admin;

// Abort if this file is called directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Analytics
 *
 * Provides a dashboard for viewing click-tracking analytics,
 * including a wp-admin dashboard widget and full analytics page.
 *
 * @since 1.0.0
 */
class Analytics {

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

		add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widget' ) );
		add_action( 'wp_ajax_azon_mate_get_analytics', array( $this, 'ajax_get_analytics' ) );
		add_action( 'wp_ajax_azon_mate_export_csv', array( $this, 'ajax_export_csv' ) );
	}

	/**
	 * Add the AzonMate Clicks widget to the WordPress dashboard.
	 *
	 * @since 1.0.0
	 */
	public function add_dashboard_widget() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		wp_add_dashboard_widget(
			'azonmate_clicks_widget',
			__( 'AzonMate Clicks', 'azonmate' ),
			array( $this, 'render_dashboard_widget' )
		);
	}

	/**
	 * Render the dashboard widget content.
	 *
	 * @since 1.0.0
	 */
	public function render_dashboard_widget() {
		$total_7days  = $this->get_click_count( 7 );
		$total_30days = $this->get_click_count( 30 );
		$top_products = $this->get_top_products( 7, 5 );

		echo '<div class="azonmate-dashboard-widget">';
		echo '<p>';
		printf(
			/* translators: %d: Number of clicks */
			esc_html__( 'Last 7 days: %d clicks', 'azonmate' ),
			absint( $total_7days )
		);
		echo '<br>';
		printf(
			/* translators: %d: Number of clicks */
			esc_html__( 'Last 30 days: %d clicks', 'azonmate' ),
			absint( $total_30days )
		);
		echo '</p>';

		if ( ! empty( $top_products ) ) {
			echo '<h4>' . esc_html__( 'Top Products (7 days)', 'azonmate' ) . '</h4>';
			echo '<ol>';
			foreach ( $top_products as $item ) {
				printf(
					'<li><code>%s</code> — %d %s</li>',
					esc_html( $item->asin ),
					absint( $item->click_count ),
					esc_html( _n( 'click', 'clicks', $item->click_count, 'azonmate' ) )
				);
			}
			echo '</ol>';
		} else {
			echo '<p>' . esc_html__( 'No click data yet.', 'azonmate' ) . '</p>';
		}

		printf(
			'<p><a href="%s">%s</a></p>',
			esc_url( admin_url( 'admin.php?page=azonmate-analytics' ) ),
			esc_html__( 'View Full Analytics →', 'azonmate' )
		);
		echo '</div>';
	}

	/**
	 * Get total click count for a date range.
	 *
	 * @since 1.0.0
	 *
	 * @param int $days Number of days to look back.
	 * @return int
	 */
	public function get_click_count( $days = 7 ) {
		global $wpdb;

		$date_from = gmdate( 'Y-m-d H:i:s', time() - ( $days * DAY_IN_SECONDS ) );

		return (int) $wpdb->get_var(
			$wpdb->prepare(
				"SELECT COUNT(*) FROM {$this->table} WHERE clicked_at >= %s",
				$date_from
			)
		);
	}

	/**
	 * Get top clicked products for a date range.
	 *
	 * @since 1.0.0
	 *
	 * @param int $days  Number of days to look back.
	 * @param int $limit Max results.
	 * @return array
	 */
	public function get_top_products( $days = 7, $limit = 10 ) {
		global $wpdb;

		$date_from = gmdate( 'Y-m-d H:i:s', time() - ( $days * DAY_IN_SECONDS ) );

		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT asin, COUNT(*) as click_count FROM {$this->table} WHERE clicked_at >= %s GROUP BY asin ORDER BY click_count DESC LIMIT %d",
				$date_from,
				$limit
			)
		);
	}

	/**
	 * Get top clicked posts for a date range.
	 *
	 * @since 1.0.0
	 *
	 * @param int $days  Number of days to look back.
	 * @param int $limit Max results.
	 * @return array
	 */
	public function get_top_posts( $days = 7, $limit = 10 ) {
		global $wpdb;

		$date_from = gmdate( 'Y-m-d H:i:s', time() - ( $days * DAY_IN_SECONDS ) );

		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT post_id, COUNT(*) as click_count FROM {$this->table} WHERE clicked_at >= %s AND post_id IS NOT NULL GROUP BY post_id ORDER BY click_count DESC LIMIT %d",
				$date_from,
				$limit
			)
		);
	}

	/**
	 * Get clicks per day for a date range.
	 *
	 * @since 1.0.0
	 *
	 * @param int $days Number of days.
	 * @return array
	 */
	public function get_clicks_per_day( $days = 30 ) {
		global $wpdb;

		$date_from = gmdate( 'Y-m-d H:i:s', time() - ( $days * DAY_IN_SECONDS ) );

		return $wpdb->get_results(
			$wpdb->prepare(
				"SELECT DATE(clicked_at) as date, COUNT(*) as clicks FROM {$this->table} WHERE clicked_at >= %s GROUP BY DATE(clicked_at) ORDER BY date ASC",
				$date_from
			)
		);
	}

	/**
	 * AJAX: Get analytics data.
	 *
	 * @since 1.0.0
	 */
	public function ajax_get_analytics() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'azon_mate_admin' ) ) {
			wp_send_json_error( array( 'message' => __( 'Security check failed.', 'azonmate' ) ), 403 );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized.', 'azonmate' ) ), 403 );
		}

		$days = absint( $_POST['days'] ?? 7 );
		if ( ! in_array( $days, array( 7, 30, 90 ), true ) ) {
			$days = 7;
		}

		wp_send_json_success( array(
			'total_clicks'  => $this->get_click_count( $days ),
			'top_products'  => $this->get_top_products( $days ),
			'top_posts'     => $this->get_top_posts( $days ),
			'clicks_per_day' => $this->get_clicks_per_day( $days ),
		) );
	}

	/**
	 * AJAX: Export click data as CSV.
	 *
	 * @since 1.0.0
	 */
	public function ajax_export_csv() {
		if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['nonce'] ) ), 'azon_mate_admin' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'azonmate' ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized.', 'azonmate' ) );
		}

		global $wpdb;

		$days      = absint( $_GET['days'] ?? 30 );
		$date_from = gmdate( 'Y-m-d H:i:s', time() - ( $days * DAY_IN_SECONDS ) );

		$clicks = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT asin, post_id, country, clicked_at FROM {$this->table} WHERE clicked_at >= %s ORDER BY clicked_at DESC",
				$date_from
			)
		);

		// Set CSV headers.
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=azonmate-clicks-' . gmdate( 'Y-m-d' ) . '.csv' );

		$output = fopen( 'php://output', 'w' );
		fputcsv( $output, array( 'ASIN', 'Post ID', 'Country', 'Clicked At' ) );

		foreach ( $clicks as $click ) {
			fputcsv( $output, array(
				$click->asin,
				$click->post_id,
				$click->country,
				$click->clicked_at,
			) );
		}

		fclose( $output );
		exit;
	}
}
