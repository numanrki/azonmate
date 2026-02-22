<?php
/**
 * Showcase Builder – visual shortcode generator.
 *
 * @package AzonMate\Admin
 * @since   1.2.0
 */

namespace AzonMate\Admin;

use AzonMate\Cache\CacheManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class ShowcaseBuilder
 *
 * Provides a visual UI to pick layouts, select products, and generate
 * ready-to-use showcase shortcodes.
 *
 * @since 1.2.0
 */
class ShowcaseBuilder {

	/**
	 * @var CacheManager
	 */
	private $cache;

	/**
	 * Constructor.
	 *
	 * @param CacheManager $cache Cache manager.
	 */
	public function __construct( CacheManager $cache ) {
		$this->cache = $cache;
		add_action( 'admin_menu', array( $this, 'add_submenu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_preview_assets' ) );
	}

	/**
	 * Register the submenu page.
	 */
	public function add_submenu() {
		add_submenu_page(
			'azonmate',
			__( 'Showcase Builder', 'azonmate' ),
			__( 'Showcase Builder', 'azonmate' ),
			'manage_options',
			'azonmate-showcase',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Enqueue the same frontend CSS on the builder page so the
	 * WYSIWYG preview renders identically to the live post.
	 *
	 * @param string $hook_suffix Current admin page.
	 */
	public function enqueue_preview_assets( $hook_suffix ) {
		if ( 'azonmate_page_azonmate-showcase' !== $hook_suffix ) {
			return;
		}

		wp_enqueue_style(
			'azonmate-public',
			AZON_MATE_PLUGIN_URL . 'assets/css/azonmate-public.css',
			array(),
			AZON_MATE_VERSION
		);

		wp_enqueue_style(
			'azonmate-showcase',
			AZON_MATE_PLUGIN_URL . 'assets/css/azonmate-showcase.css',
			array( 'azonmate-public' ),
			AZON_MATE_VERSION
		);
	}

	/**
	 * Render the page.
	 */
	public function render_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized', 'azonmate' ) );
		}

		// Fetch all manual products for the picker.
		$products = $this->cache->search_manual_products( '' );

		include AZON_MATE_PLUGIN_DIR . 'includes/admin/views/showcase-builder-page.php';
	}
}
