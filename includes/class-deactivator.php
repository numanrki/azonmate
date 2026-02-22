<?php
/**
 * Plugin deactivation handler.
 *
 * @package AzonMate
 * @since   1.0.0
 */

namespace AzonMate;

// Abort if this file is called directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Deactivator
 *
 * Handles tasks that run when the plugin is deactivated:
 * - Clear scheduled cron events
 * - Flush rewrite rules
 *
 * @since 1.0.0
 */
class Deactivator {

	/**
	 * Run deactivation tasks.
	 *
	 * @since 1.0.0
	 */
	public static function deactivate() {
		self::clear_cron_events();
		flush_rewrite_rules();
	}

	/**
	 * Remove all scheduled cron events.
	 *
	 * @since 1.0.0
	 */
	private static function clear_cron_events() {
		$timestamp = wp_next_scheduled( 'azon_mate_refresh_cache' );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, 'azon_mate_refresh_cache' );
		}

		$timestamp = wp_next_scheduled( 'azon_mate_cleanup_clicks' );
		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, 'azon_mate_cleanup_clicks' );
		}
	}
}
