<?php
/**
 * PSR-4 style autoloader for the AzonMate plugin.
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
 * Class Autoloader
 *
 * Maps AzonMate namespace classes to the includes/ directory structure.
 *
 * @since 1.0.0
 */
class Autoloader {

	/**
	 * The namespace prefix for this autoloader.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	const NAMESPACE_PREFIX = 'AzonMate\\';

	/**
	 * The base directory for the namespace prefix.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private static $base_dir = '';

	/**
	 * Register the autoloader with SPL.
	 *
	 * @since 1.0.0
	 */
	public static function register() {
		self::$base_dir = AZON_MATE_PLUGIN_DIR . 'includes/';
		spl_autoload_register( array( __CLASS__, 'autoload' ) );
	}

	/**
	 * Autoload callback.
	 *
	 * Converts a fully qualified class name to a file path and includes it.
	 *
	 * Mapping rules:
	 * - AzonMate\Plugin                → includes/class-plugin.php
	 * - AzonMate\Admin\Settings        → includes/admin/class-settings.php
	 * - AzonMate\API\AmazonAPI         → includes/api/class-amazon-api.php
	 * - AzonMate\Cache\CacheManager    → includes/cache/class-cache-manager.php
	 *
	 * @since 1.0.0
	 *
	 * @param string $class The fully-qualified class name.
	 */
	public static function autoload( $class ) {
		// Check if the class uses our namespace prefix.
		$len = strlen( self::NAMESPACE_PREFIX );
		if ( strncmp( self::NAMESPACE_PREFIX, $class, $len ) !== 0 ) {
			return;
		}

		// Get the relative class name (without namespace prefix).
		$relative_class = substr( $class, $len );

		// Convert namespace separators to directory separators.
		$relative_path = str_replace( '\\', '/', $relative_class );

		// Split into directory parts and class name.
		$parts     = explode( '/', $relative_path );
		$class_name = array_pop( $parts );

		// Convert class name from PascalCase to kebab-case.
		$file_name = 'class-' . self::pascal_to_kebab( $class_name ) . '.php';

		// Convert directory parts to lowercase.
		$directories = array_map( 'strtolower', $parts );

		// Build the full file path.
		$file = self::$base_dir;
		if ( ! empty( $directories ) ) {
			$file .= implode( '/', $directories ) . '/';
		}
		$file .= $file_name;

		// Include the file if it exists.
		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}

	/**
	 * Convert PascalCase to kebab-case.
	 *
	 * Examples:
	 * - Plugin           → plugin
	 * - AmazonAPI        → amazon-api
	 * - CacheManager     → cache-manager
	 * - RequestSigner    → request-signer
	 * - TemplateRenderer → template-renderer
	 * - BlockRegistrar   → block-registrar
	 * - GeoTargeting     → geo-targeting
	 * - ClickTracker     → click-tracker
	 *
	 * @since 1.0.0
	 *
	 * @param string $string PascalCase string.
	 * @return string Kebab-case string.
	 */
	private static function pascal_to_kebab( $string ) {
		// Insert hyphen before uppercase letters that follow a lowercase letter or another uppercase+lowercase sequence.
		$result = preg_replace( '/([a-z])([A-Z])/', '$1-$2', $string );
		$result = preg_replace( '/([A-Z]+)([A-Z][a-z])/', '$1-$2', $result );
		return strtolower( $result );
	}
}

// Register the autoloader immediately.
Autoloader::register();
