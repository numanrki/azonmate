<?php
/**
 * AzonMate – Amazon Affiliate Product Engine for WordPress
 *
 * @package           AzonMate
 * @author            Numan
 * @copyright         2026 Numan / AzonMate
 * @license           Proprietary – Free for personal use
 *
 * @wordpress-plugin
 * Plugin Name:       AzonMate
 * Plugin URI:        https://azonmate.com
 * Description:       Search, display, and monetize Amazon products directly from your WordPress posts. Connects to Amazon PA-API 5.0 for live product data, comparison tables, bestseller lists, and more.
 * Version:           1.3.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Numan
 * Author URI:        https://github.com/numanrki
 * Text Domain:       azonmate
 * Domain Path:       /languages
 * License:           Free for personal use
 * License URI:       https://github.com/numanrki/azonmate#license
 *
 * @since 1.0.0
 */

// Abort if this file is called directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Current plugin version.
 *
 * @since 1.0.0
 */
define( 'AZON_MATE_VERSION', '1.3.0' );

/**
 * Plugin directory path (with trailing slash).
 *
 * @since 1.0.0
 */
define( 'AZON_MATE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

/**
 * Plugin directory URL (with trailing slash).
 *
 * @since 1.0.0
 */
define( 'AZON_MATE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Plugin basename (e.g., azonmate/azonmate.php).
 *
 * @since 1.0.0
 */
define( 'AZON_MATE_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Minimum PHP version required.
 *
 * @since 1.0.0
 */
define( 'AZON_MATE_MINIMUM_PHP_VERSION', '7.4' );

/**
 * Minimum WordPress version required.
 *
 * @since 1.0.0
 */
define( 'AZON_MATE_MINIMUM_WP_VERSION', '6.0' );

/**
 * Plugin text domain.
 *
 * @since 1.0.0
 */
define( 'AZON_MATE_TEXT_DOMAIN', 'azonmate' );

/**
 * Check minimum requirements before loading the plugin.
 *
 * @since 1.0.0
 *
 * @return bool True if requirements are met.
 */
function azon_mate_check_requirements() {
	$errors = array();

	if ( version_compare( PHP_VERSION, AZON_MATE_MINIMUM_PHP_VERSION, '<' ) ) {
		$errors[] = sprintf(
			'AzonMate requires PHP %1$s or higher. You are running PHP %2$s.',
			AZON_MATE_MINIMUM_PHP_VERSION,
			PHP_VERSION
		);
	}

	if ( version_compare( get_bloginfo( 'version' ), AZON_MATE_MINIMUM_WP_VERSION, '<' ) ) {
		$errors[] = sprintf(
			'AzonMate requires WordPress %1$s or higher. You are running WordPress %2$s.',
			AZON_MATE_MINIMUM_WP_VERSION,
			get_bloginfo( 'version' )
		);
	}

	if ( ! empty( $errors ) ) {
		add_action( 'admin_notices', function () use ( $errors ) {
			foreach ( $errors as $error ) {
				printf(
					'<div class="notice notice-error"><p><strong>AzonMate:</strong> %s</p></div>',
					esc_html( $error )
				);
			}
		} );
		return false;
	}

	return true;
}

/**
 * Load the custom autoloader.
 *
 * @since 1.0.0
 */
require_once AZON_MATE_PLUGIN_DIR . 'includes/class-autoloader.php';

/**
 * Register activation hook.
 *
 * @since 1.0.0
 */
register_activation_hook( __FILE__, function () {
	require_once AZON_MATE_PLUGIN_DIR . 'includes/class-activator.php';
	\AzonMate\Activator::activate();
} );

/**
 * Register deactivation hook.
 *
 * @since 1.0.0
 */
register_deactivation_hook( __FILE__, function () {
	require_once AZON_MATE_PLUGIN_DIR . 'includes/class-deactivator.php';
	\AzonMate\Deactivator::deactivate();
} );

/**
 * Begin execution of the plugin.
 *
 * @since 1.0.0
 */
function azon_mate_init() {
	if ( ! azon_mate_check_requirements() ) {
		return;
	}

	$plugin = \AzonMate\Plugin::get_instance();
	$plugin->run();
}
add_action( 'plugins_loaded', 'azon_mate_init' );
