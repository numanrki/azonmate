<?php
/**
 * Main plugin class (Singleton).
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
 * Class Plugin
 *
 * The core plugin class that initializes all components.
 * Uses the Singleton pattern to ensure only one instance exists.
 *
 * @since 1.0.0
 */
class Plugin {

	/**
	 * The single instance of the class.
	 *
	 * @since 1.0.0
	 * @var Plugin|null
	 */
	private static $instance = null;

	/**
	 * Admin Settings instance.
	 *
	 * @since 1.0.0
	 * @var \AzonMate\Admin\Settings|null
	 */
	private $settings = null;

	/**
	 * Product Search instance.
	 *
	 * @since 1.0.0
	 * @var \AzonMate\Admin\ProductSearch|null
	 */
	private $product_search = null;

	/**
	 * Analytics instance.
	 *
	 * @since 1.0.0
	 * @var \AzonMate\Admin\Analytics|null
	 */
	private $analytics = null;

	/**
	 * Amazon API instance.
	 *
	 * @since 1.0.0
	 * @var \AzonMate\API\AmazonAPI|null
	 */
	private $amazon_api = null;

	/**
	 * Cache Manager instance.
	 *
	 * @since 1.0.0
	 * @var \AzonMate\Cache\CacheManager|null
	 */
	private $cache_manager = null;

	/**
	 * Shortcode Manager instance.
	 *
	 * @since 1.0.0
	 * @var \AzonMate\Shortcodes\ShortcodeManager|null
	 */
	private $shortcode_manager = null;

	/**
	 * Block Registrar instance.
	 *
	 * @since 1.0.0
	 * @var \AzonMate\Blocks\BlockRegistrar|null
	 */
	private $block_registrar = null;

	/**
	 * Template Renderer instance.
	 *
	 * @since 1.0.0
	 * @var \AzonMate\Templates\TemplateRenderer|null
	 */
	private $template_renderer = null;

	/**
	 * Geo-Targeting instance.
	 *
	 * @since 1.0.0
	 * @var \AzonMate\Geo\GeoTargeting|null
	 */
	private $geo_targeting = null;

	/**
	 * Click Tracker instance.
	 *
	 * @since 1.0.0
	 * @var \AzonMate\Tracking\ClickTracker|null
	 */
	private $click_tracker = null;

	/**
	 * Cron Refresh instance.
	 *
	 * @since 1.0.0
	 * @var \AzonMate\Cache\CronRefresh|null
	 */
	private $cron_refresh = null;

	/**
	 * Manual Products admin instance.
	 *
	 * @since 1.1.0
	 * @var \AzonMate\Admin\ManualProducts|null
	 */
	private $manual_products = null;

	/**
	 * Showcase Builder admin instance.
	 *
	 * @since 1.2.0
	 * @var \AzonMate\Admin\ShowcaseBuilder|null
	 */
	private $showcase_builder = null;

	/**
	 * Private constructor to prevent direct object creation.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {
		// Singleton: use get_instance().
	}

	/**
	 * Prevent cloning of the instance.
	 *
	 * @since 1.0.0
	 */
	private function __clone() {
		// Singleton: cloning is not allowed.
	}

	/**
	 * Prevent unserializing of the instance.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup() {
		throw new \Exception( 'Cannot unserialize singleton.' );
	}

	/**
	 * Get the single instance of the plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return Plugin
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Initialize and run the plugin.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$this->init_components();
		$this->register_hooks();

		// Defer textdomain loading and DB upgrade check to 'init' (required since WP 6.7).
		add_action( 'init', array( $this, 'load_textdomain' ) );
		add_action( 'init', array( $this, 'maybe_upgrade_db' ) );
	}

	/**
	 * Check if the DB schema needs upgrading and run dbDelta if so.
	 *
	 * @since 1.1.0
	 */
	public function maybe_upgrade_db() {
		$installed_version = get_option( 'azonmate_version', '0' );

		if ( version_compare( $installed_version, AZON_MATE_VERSION, '<' ) ) {
			\AzonMate\Activator::activate();
		}
	}

	/**
	 * Load plugin text domain for translations.
	 *
	 * @since 1.0.0
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'azonmate',
			false,
			dirname( AZON_MATE_PLUGIN_BASENAME ) . '/languages'
		);
	}

	/**
	 * Initialize all plugin components.
	 *
	 * @since 1.0.0
	 */
	private function init_components() {
		// Load template helper functions (not autoloaded since they're not in a class).
		require_once AZON_MATE_PLUGIN_DIR . 'includes/templates/template-functions.php';

		// Core components (always loaded).
		$this->cache_manager    = new \AzonMate\Cache\CacheManager();
		$this->amazon_api       = new \AzonMate\API\AmazonAPI( $this->cache_manager );
		$this->template_renderer = new \AzonMate\Templates\TemplateRenderer();
		$this->shortcode_manager = new \AzonMate\Shortcodes\ShortcodeManager( $this->amazon_api, $this->template_renderer, $this->cache_manager );
		$this->click_tracker     = new \AzonMate\Tracking\ClickTracker();
		$this->cron_refresh      = new \AzonMate\Cache\CronRefresh( $this->amazon_api, $this->cache_manager );

		// Geo-targeting (if enabled).
		if ( '1' === get_option( 'azon_mate_geo_enabled', '0' ) ) {
			$this->geo_targeting = new \AzonMate\Geo\GeoTargeting();
		}

		// Admin-only components.
		if ( is_admin() ) {
			$this->settings           = new \AzonMate\Admin\Settings();
			$this->product_search     = new \AzonMate\Admin\ProductSearch( $this->amazon_api );
			$this->analytics          = new \AzonMate\Admin\Analytics();
			$this->manual_products    = new \AzonMate\Admin\ManualProducts( $this->cache_manager );
			$this->showcase_builder   = new \AzonMate\Admin\ShowcaseBuilder( $this->cache_manager );
		}

		// Block editor components.
		$this->block_registrar = new \AzonMate\Blocks\BlockRegistrar( $this->shortcode_manager );
	}

	/**
	 * Register all WordPress hooks.
	 *
	 * @since 1.0.0
	 */
	private function register_hooks() {
		// Frontend asset loading.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_frontend_assets' ) );

		// Admin asset loading.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );

		// Plugin action links on the plugins page.
		add_filter( 'plugin_action_links_' . AZON_MATE_PLUGIN_BASENAME, array( $this, 'add_plugin_action_links' ) );
	}

	/**
	 * Enqueue frontend CSS and JS (only on pages that use the plugin).
	 *
	 * @since 1.0.0
	 */
	public function enqueue_frontend_assets() {
		global $post;

		// Only load on pages/posts that have our shortcodes or blocks.
		$should_load = false;

		if ( is_a( $post, 'WP_Post' ) ) {
			if ( has_shortcode( $post->post_content, 'azonmate' ) ) {
				$should_load = true;
			}
			if ( has_block( 'azonmate/', $post ) ) {
				$should_load = true;
			}
		}

		/**
		 * Filter whether to load AzonMate frontend assets.
		 *
		 * @since 1.0.0
		 *
		 * @param bool $should_load Whether assets should be loaded.
		 */
		$should_load = apply_filters( 'azon_mate_load_frontend_assets', $should_load );

		if ( ! $should_load ) {
			return;
		}

		// Frontend CSS.
		if ( '0' !== get_option( 'azon_mate_disable_css', '0' ) ) {
			return;
		}

		wp_enqueue_style(
			'azonmate-public',
			AZON_MATE_PLUGIN_URL . 'assets/css/azonmate-public.css',
			array(),
			AZON_MATE_VERSION
		);

		// Showcase premium CSS.
		wp_enqueue_style(
			'azonmate-showcase',
			AZON_MATE_PLUGIN_URL . 'assets/css/azonmate-showcase.css',
			array( 'azonmate-public' ),
			AZON_MATE_VERSION
		);

		// Collage CSS.
		wp_enqueue_style(
			'azonmate-collage',
			AZON_MATE_PLUGIN_URL . 'assets/css/azonmate-collage.css',
			array( 'azonmate-public' ),
			AZON_MATE_VERSION
		);

		// Frontend JS (geo-targeting, lazy load, click tracking).
		wp_enqueue_script(
			'azonmate-public',
			AZON_MATE_PLUGIN_URL . 'assets/js/azonmate-public.js',
			array(),
			AZON_MATE_VERSION,
			true
		);

		wp_localize_script( 'azonmate-public', 'azonMatePublic', array(
			'ajaxUrl'        => admin_url( 'admin-ajax.php' ),
			'nonce'          => wp_create_nonce( 'azon_mate_public' ),
			'geoEnabled'     => get_option( 'azon_mate_geo_enabled', '0' ),
			'trackingEnabled' => get_option( 'azon_mate_tracking_enabled', '1' ),
		) );

		// Click tracking JS.
		if ( '1' === get_option( 'azon_mate_tracking_enabled', '1' ) ) {
			wp_enqueue_script(
				'azonmate-click-tracker',
				AZON_MATE_PLUGIN_URL . 'assets/js/azonmate-click-tracker.js',
				array( 'azonmate-public' ),
				AZON_MATE_VERSION,
				true
			);
		}
	}

	/**
	 * Enqueue admin CSS and JS.
	 *
	 * @since 1.0.0
	 *
	 * @param string $hook_suffix The current admin page.
	 */
	public function enqueue_admin_assets( $hook_suffix ) {
		// Only load on our settings pages and post editor.
		$our_pages = array(
			'toplevel_page_azonmate',
			'azonmate_page_azonmate-analytics',
			'azonmate_page_azonmate-products',
			'azonmate_page_azonmate-showcase',
			'post.php',
			'post-new.php',
		);

		if ( ! in_array( $hook_suffix, $our_pages, true ) ) {
			return;
		}

		// Admin CSS.
		wp_enqueue_style(
			'azonmate-admin',
			AZON_MATE_PLUGIN_URL . 'assets/css/azonmate-admin.css',
			array(),
			AZON_MATE_VERSION
		);

		// Admin JS.
		wp_enqueue_script(
			'azonmate-admin',
			AZON_MATE_PLUGIN_URL . 'assets/js/azonmate-admin.js',
			array( 'jquery', 'wp-color-picker' ),
			AZON_MATE_VERSION,
			true
		);

		wp_localize_script( 'azonmate-admin', 'azonMateAdmin', array(
			'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
			'adminUrl' => admin_url(),
			'nonce'    => wp_create_nonce( 'azon_mate_admin' ),
			'i18n'    => array(
				'searching'      => __( 'Searching...', 'azonmate' ),
				'noResults'      => __( 'No products found.', 'azonmate' ),
				'error'          => __( 'An error occurred. Please try again.', 'azonmate' ),
				'testSuccess'    => __( 'Connection successful!', 'azonmate' ),
				'testFailed'     => __( 'Connection failed. Please check your credentials.', 'azonmate' ),
				'cacheClearedOk' => __( 'Cache cleared successfully.', 'azonmate' ),
				'insert'         => __( 'Insert', 'azonmate' ),
				'insertAs'       => __( 'Insert as...', 'azonmate' ),
				'productBox'     => __( 'Product Box', 'azonmate' ),
				'textLink'       => __( 'Text Link', 'azonmate' ),
				'imageLink'      => __( 'Image Link', 'azonmate' ),
				'tableRow'       => __( 'Table Row', 'azonmate' ),
				'asinOnly'       => __( 'ASIN Only', 'azonmate' ),
			),
		) );

		// Search modal (for Classic Editor).
		if ( in_array( $hook_suffix, array( 'post.php', 'post-new.php' ), true ) ) {
			wp_enqueue_style( 'wp-jquery-ui-dialog' );

			wp_enqueue_script(
				'azonmate-search-modal',
				AZON_MATE_PLUGIN_URL . 'assets/js/azonmate-search-modal.js',
				array( 'jquery', 'jquery-ui-dialog', 'azonmate-admin' ),
				AZON_MATE_VERSION,
				true
			);

			wp_enqueue_style(
				'azonmate-editor',
				AZON_MATE_PLUGIN_URL . 'assets/css/azonmate-editor.css',
				array(),
				AZON_MATE_VERSION
			);
		}

		// Manual products page JS.
		if ( 'azonmate_page_azonmate-products' === $hook_suffix ) {
			wp_enqueue_media();
			wp_enqueue_script(
				'azonmate-manual-products',
				AZON_MATE_PLUGIN_URL . 'assets/js/azonmate-manual-products.js',
				array( 'jquery', 'azonmate-admin' ),
				AZON_MATE_VERSION,
				true
			);
		}
	}

	/**
	 * Add action links to the plugin listing on the Plugins page.
	 *
	 * @since 1.0.0
	 *
	 * @param array $links Existing action links.
	 * @return array Modified action links.
	 */
	public function add_plugin_action_links( $links ) {
		$settings_link = sprintf(
			'<a href="%s">%s</a>',
			admin_url( 'admin.php?page=azonmate' ),
			__( 'Settings', 'azonmate' )
		);
		array_unshift( $links, $settings_link );
		return $links;
	}

	/**
	 * Get the Amazon API instance.
	 *
	 * @since 1.0.0
	 *
	 * @return \AzonMate\API\AmazonAPI
	 */
	public function get_amazon_api() {
		return $this->amazon_api;
	}

	/**
	 * Get the Cache Manager instance.
	 *
	 * @since 1.0.0
	 *
	 * @return \AzonMate\Cache\CacheManager
	 */
	public function get_cache_manager() {
		return $this->cache_manager;
	}

	/**
	 * Get the Template Renderer instance.
	 *
	 * @since 1.0.0
	 *
	 * @return \AzonMate\Templates\TemplateRenderer
	 */
	public function get_template_renderer() {
		return $this->template_renderer;
	}

	/**
	 * Get the Geo-Targeting instance.
	 *
	 * @since 1.0.0
	 *
	 * @return \AzonMate\Geo\GeoTargeting|null
	 */
	public function get_geo_targeting() {
		return $this->geo_targeting;
	}

	/**
	 * Check if debug mode is enabled.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	public static function is_debug_enabled() {
		return '1' === get_option( 'azon_mate_debug_mode', '0' );
	}
}

/**
 * Helper function to check if plugin debug mode is enabled.
 *
 * @since 1.0.0
 *
 * @return bool
 */
function azon_mate_is_debug_enabled() {
	return Plugin::is_debug_enabled();
}
