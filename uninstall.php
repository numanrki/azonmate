<?php
/**
 * Uninstall AzonMate.
 *
 * Fires when the plugin is deleted via the WordPress dashboard.
 * Removes all plugin data if the user opted in.
 *
 * @package AzonMate
 * @since   1.0.0
 */

// Abort if not called from WordPress uninstall.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Only delete data if user opted in via Settings → Advanced → Delete data on uninstall.
$delete_data = get_option( 'azon_mate_uninstall_delete', false );

if ( ! $delete_data ) {
	return;
}

global $wpdb;

/* -----------------------------------------------------------------------
   1. Drop custom database tables
   ----------------------------------------------------------------------- */

$tables = array(
	$wpdb->prefix . 'azonmate_products',
	$wpdb->prefix . 'azonmate_clicks',
	$wpdb->prefix . 'azonmate_comparison_tables',
);

foreach ( $tables as $table ) {
	$wpdb->query( "DROP TABLE IF EXISTS {$table}" ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
}

/* -----------------------------------------------------------------------
   2. Delete all plugin options
   ----------------------------------------------------------------------- */

$options = array(
	'azon_mate_access_key',
	'azon_mate_secret_key',
	'azon_mate_partner_tag',
	'azon_mate_marketplace',
	'azon_mate_button_text',
	'azon_mate_button_style',
	'azon_mate_link_target',
	'azon_mate_nofollow',
	'azon_mate_sponsored',
	'azon_mate_default_template',
	'azon_mate_image_size',
	'azon_mate_show_disclaimer',
	'azon_mate_disclaimer_text',
	'azon_mate_disclaimer_position',
	'azon_mate_show_disclosure',
	'azon_mate_disclosure_text',
	'azon_mate_disclosure_font_size',
	'azon_mate_disclosure_color',
	'azon_mate_disclosure_align',
	'azon_mate_cache_duration',
	'azon_mate_cache_auto_refresh',
	'azon_mate_geo_enabled',
	'azon_mate_geo_tags',
	'azon_mate_click_tracking',
	'azon_mate_anonymize_ip',
	'azon_mate_lazy_load',
	'azon_mate_async_css',
	'azon_mate_uninstall_delete',
	'azon_mate_db_version',
);

foreach ( $options as $option ) {
	delete_option( $option );
}

/* -----------------------------------------------------------------------
   3. Delete transients
   ----------------------------------------------------------------------- */

$wpdb->query(
	"DELETE FROM {$wpdb->options}
	 WHERE option_name LIKE '_transient_azonmate_%'
	    OR option_name LIKE '_transient_timeout_azonmate_%'"
);

/* -----------------------------------------------------------------------
   4. Unschedule cron events
   ----------------------------------------------------------------------- */

$cron_hooks = array(
	'azon_mate_refresh_cache',
	'azon_mate_cleanup_clicks',
);

foreach ( $cron_hooks as $hook ) {
	$timestamp = wp_next_scheduled( $hook );
	if ( $timestamp ) {
		wp_unschedule_event( $timestamp, $hook );
	}
}

/* -----------------------------------------------------------------------
   5. Flush rewrite rules
   ----------------------------------------------------------------------- */

flush_rewrite_rules();
