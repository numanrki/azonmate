<?php
/**
 * Shortcode manager — registers and dispatches all shortcodes.
 *
 * @package AzonMate\Shortcodes
 * @since   1.0.0
 */

namespace AzonMate\Shortcodes;

// Abort if this file is called directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class ShortcodeManager
 *
 * Registers the single `[azonmate]` shortcode and routes
 * attributes to the correct handler class.
 *
 * @since 1.0.0
 */
class ShortcodeManager {

	/**
	 * API instance.
	 *
	 * @since 1.0.0
	 * @var \AzonMate\API\AmazonAPI
	 */
	private $api;

	/**
	 * Template renderer instance.
	 *
	 * @since 1.0.0
	 * @var \AzonMate\Templates\TemplateRenderer
	 */
	private $renderer;

	/**
	 * Cache manager instance.
	 *
	 * @since 1.0.0
	 * @var \AzonMate\Cache\CacheManager
	 */
	private $cache;

	/**
	 * Individual shortcode handlers.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private $handlers = array();

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param \AzonMate\API\AmazonAPI              $api      Amazon API client.
	 * @param \AzonMate\Templates\TemplateRenderer  $renderer Template renderer.
	 * @param \AzonMate\Cache\CacheManager          $cache    Cache manager.
	 */
	public function __construct( $api, $renderer, $cache ) {
		$this->api      = $api;
		$this->renderer = $renderer;
		$this->cache    = $cache;

		$this->register_handlers();

		add_shortcode( 'azonmate', array( $this, 'render_shortcode' ) );
	}

	/**
	 * Instantiate all individual shortcode handlers.
	 *
	 * @since 1.0.0
	 */
	private function register_handlers() {
		$this->handlers = array(
			'box'          => new ProductBox( $this->api, $this->renderer, $this->cache ),
			'link'         => new TextLink( $this->api, $this->renderer, $this->cache ),
			'image'        => new ImageLink( $this->api, $this->renderer, $this->cache ),
			'field'        => new Field( $this->api, $this->renderer, $this->cache ),
			'list'         => new ProductList( $this->api, $this->renderer, $this->cache ),
			'bestseller'   => new Bestseller( $this->api, $this->renderer, $this->cache ),
			'new_releases' => new Bestseller( $this->api, $this->renderer, $this->cache ),
			'table'        => new ComparisonTable( $this->api, $this->renderer, $this->cache ),
			'showcase'     => new Showcase( $this->api, $this->renderer, $this->cache ),
			'collage'      => new Collage( $this->api, $this->renderer, $this->cache ),
		);
	}

	/**
	 * Render the [azonmate] shortcode.
	 *
	 * Detects which type of shortcode is being used based on
	 * the first recognised attribute key.
	 *
	 * @since 1.0.0
	 *
	 * @param array|string $atts    Shortcode attributes.
	 * @param string|null  $content Enclosed content (for text links).
	 * @return string HTML output.
	 */
	public function render_shortcode( $atts, $content = null ) {
		$atts = is_array( $atts ) ? $atts : array();

		// Determine the shortcode type.
		$type_keys = array( 'box', 'link', 'image', 'field', 'list', 'bestseller', 'new_releases', 'table', 'showcase', 'collage' );

		$type = '';
		foreach ( $type_keys as $key ) {
			if ( isset( $atts[ $key ] ) ) {
				$type = $key;
				break;
			}
		}

		if ( empty( $type ) || ! isset( $this->handlers[ $type ] ) ) {
			if ( current_user_can( 'edit_posts' ) ) {
				return '<!-- AzonMate: Unknown shortcode type. Use box, link, image, field, list, bestseller, new_releases, table, showcase, or collage. -->';
			}
			return '';
		}

		// Flag new_releases type for the Bestseller handler.
		if ( 'new_releases' === $type ) {
			$atts['_sort'] = 'newest';
		}

		try {
			return $this->handlers[ $type ]->render( $atts, $content );
		} catch ( \Exception $e ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				return '<!-- AzonMate Error: ' . esc_html( $e->getMessage() ) . ' -->';
			}
			return '';
		}
	}
}
