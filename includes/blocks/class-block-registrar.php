<?php
/**
 * Gutenberg block registration.
 *
 * @package AzonMate\Blocks
 * @since   1.0.0
 */

namespace AzonMate\Blocks;

// Abort if this file is called directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class BlockRegistrar
 *
 * Registers all Gutenberg blocks with server-side render callbacks.
 *
 * @since 1.0.0
 */
class BlockRegistrar {

	/**
	 * Shortcode manager (used for rendering).
	 *
	 * @since 1.0.0
	 * @var \AzonMate\Shortcodes\ShortcodeManager
	 */
	private $shortcode_manager;

	/**
	 * Block definitions.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	private $blocks = array();

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param \AzonMate\Shortcodes\ShortcodeManager $shortcode_manager Shortcode manager.
	 */
	public function __construct( $shortcode_manager ) {
		$this->shortcode_manager = $shortcode_manager;

		add_action( 'init', array( $this, 'register_blocks' ) );
		add_action( 'enqueue_block_editor_assets', array( $this, 'enqueue_editor_assets' ) );
	}

	/**
	 * Define all block configurations.
	 *
	 * @since 1.0.0
	 */
	private function define_blocks() {
		$this->blocks = array(
			'product-box'      => array(
				'title'       => __( 'AzonMate Product Box', 'azonmate' ),
				'description' => __( 'Display a single Amazon product with image, price, rating, and buy button.', 'azonmate' ),
				'icon'        => 'cart',
				'category'    => 'widgets',
				'attributes'  => array(
					'asin'        => array( 'type' => 'string', 'default' => '' ),
					'template'    => array( 'type' => 'string', 'default' => 'default' ),
					'title'       => array( 'type' => 'string', 'default' => '' ),
					'description' => array( 'type' => 'boolean', 'default' => true ),
					'rating'      => array( 'type' => 'boolean', 'default' => true ),
					'price'       => array( 'type' => 'boolean', 'default' => true ),
					'buttonText'  => array( 'type' => 'string', 'default' => '' ),
					'imageSize'   => array( 'type' => 'string', 'default' => 'medium' ),
				),
			),
			'product-list'     => array(
				'title'       => __( 'AzonMate Product List', 'azonmate' ),
				'description' => __( 'Display multiple Amazon products in a list or grid.', 'azonmate' ),
				'icon'        => 'list-view',
				'category'    => 'widgets',
				'attributes'  => array(
					'asins'    => array( 'type' => 'string', 'default' => '' ),
					'template' => array( 'type' => 'string', 'default' => 'default' ),
					'max'      => array( 'type' => 'number', 'default' => 10 ),
				),
			),
			'comparison-table' => array(
				'title'       => __( 'AzonMate Comparison Table', 'azonmate' ),
				'description' => __( 'Compare Amazon products side by side.', 'azonmate' ),
				'icon'        => 'editor-table',
				'category'    => 'widgets',
				'attributes'  => array(
					'asins'     => array( 'type' => 'string', 'default' => '' ),
					'columns'   => array( 'type' => 'string', 'default' => '' ),
					'highlight' => array( 'type' => 'string', 'default' => '' ),
					'max'       => array( 'type' => 'number', 'default' => 10 ),
					'template'  => array( 'type' => 'string', 'default' => 'default' ),
				),
			),
			'bestseller'       => array(
				'title'       => __( 'AzonMate Bestsellers', 'azonmate' ),
				'description' => __( 'Show top-selling products from an Amazon category.', 'azonmate' ),
				'icon'        => 'star-filled',
				'category'    => 'widgets',
				'attributes'  => array(
					'keyword'  => array( 'type' => 'string', 'default' => '' ),
					'items'    => array( 'type' => 'number', 'default' => 10 ),
					'template' => array( 'type' => 'string', 'default' => 'default' ),
				),
			),
			'text-link'        => array(
				'title'       => __( 'AzonMate Text Link', 'azonmate' ),
				'description' => __( 'Insert an inline Amazon affiliate text link.', 'azonmate' ),
				'icon'        => 'admin-links',
				'category'    => 'inline',
				'attributes'  => array(
					'asin'  => array( 'type' => 'string', 'default' => '' ),
					'title' => array( 'type' => 'string', 'default' => '' ),
					'text'  => array( 'type' => 'string', 'default' => '' ),
				),
			),
		);
	}

	/**
	 * Register all blocks.
	 *
	 * @since 1.0.0
	 */
	public function register_blocks() {
		// Define blocks lazily here so __() calls run after textdomain is loaded.
		if ( empty( $this->blocks ) ) {
			$this->define_blocks();
		}

		foreach ( $this->blocks as $slug => $config ) {
			$block_name = 'azonmate/' . $slug;

			// Check if block.json exists in build directory.
			$block_json = AZON_MATE_PLUGIN_DIR . 'build/' . $slug;

			if ( file_exists( $block_json . '/block.json' ) ) {
				// Use block.json for fully built blocks.
				register_block_type( $block_json );
			} else {
				// Fallback: register programmatically with server-side render.
				register_block_type(
					$block_name,
					array(
						'api_version'     => 2,
						'title'           => $config['title'],
						'description'     => $config['description'],
						'icon'            => $config['icon'],
						'category'        => $config['category'],
						'attributes'      => $config['attributes'],
						'render_callback' => array( $this, 'render_block_' . str_replace( '-', '_', $slug ) ),
						'editor_script'   => 'azonmate-blocks-editor',
						'editor_style'    => 'azonmate-editor-css',
						'style'           => 'azonmate-public-css',
					)
				);
			}
		}
	}

	/**
	 * Enqueue editor-only block assets.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_editor_assets() {
		$build_js = AZON_MATE_PLUGIN_DIR . 'build/blocks.js';

		if ( file_exists( $build_js ) ) {
			wp_enqueue_script(
				'azonmate-blocks-editor',
				AZON_MATE_PLUGIN_URL . 'build/blocks.js',
				array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n', 'wp-data', 'wp-server-side-render' ),
				AZON_MATE_VERSION,
				true
			);
		}

		wp_localize_script(
			'azonmate-blocks-editor',
			'azonMateBlock',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'azon_mate_admin' ),
			)
		);
	}

	/**
	 * Server-side render: Product Box.
	 *
	 * @since 1.0.0
	 *
	 * @param array $attributes Block attributes.
	 * @return string HTML.
	 */
	public function render_block_product_box( $attributes ) {
		if ( empty( $attributes['asin'] ) ) {
			return '<p class="azonmate-block-placeholder">' . esc_html__( 'Select an Amazon product.', 'azonmate' ) . '</p>';
		}

		$shortcode_atts = array(
			'box'         => $attributes['asin'],
			'template'    => $attributes['template'] ?? 'default',
			'title'       => $attributes['title'] ?? '',
			'description' => ( $attributes['description'] ?? true ) ? 'true' : 'false',
			'rating'      => ( $attributes['rating'] ?? true ) ? 'true' : 'false',
			'price'       => ( $attributes['price'] ?? true ) ? 'true' : 'false',
			'button_text' => $attributes['buttonText'] ?? '',
			'image_size'  => $attributes['imageSize'] ?? 'medium',
		);

		return $this->shortcode_manager->render_shortcode( $shortcode_atts, null );
	}

	/**
	 * Server-side render: Product List.
	 *
	 * @since 1.0.0
	 *
	 * @param array $attributes Block attributes.
	 * @return string HTML.
	 */
	public function render_block_product_list( $attributes ) {
		if ( empty( $attributes['asins'] ) ) {
			return '<p class="azonmate-block-placeholder">' . esc_html__( 'Add product ASINs.', 'azonmate' ) . '</p>';
		}

		$shortcode_atts = array(
			'list'     => $attributes['asins'],
			'template' => $attributes['template'] ?? 'default',
			'max'      => $attributes['max'] ?? 10,
		);

		return $this->shortcode_manager->render_shortcode( $shortcode_atts, null );
	}

	/**
	 * Server-side render: Comparison Table.
	 *
	 * @since 1.0.0
	 *
	 * @param array $attributes Block attributes.
	 * @return string HTML.
	 */
	public function render_block_comparison_table( $attributes ) {
		if ( empty( $attributes['asins'] ) ) {
			return '<p class="azonmate-block-placeholder">' . esc_html__( 'Add product ASINs for comparison.', 'azonmate' ) . '</p>';
		}

		$shortcode_atts = array(
			'table'     => $attributes['asins'],
			'columns'   => $attributes['columns'] ?? '',
			'highlight' => $attributes['highlight'] ?? '',
			'max'       => $attributes['max'] ?? 10,
			'template'  => $attributes['template'] ?? 'default',
		);

		return $this->shortcode_manager->render_shortcode( $shortcode_atts, null );
	}

	/**
	 * Server-side render: Bestseller.
	 *
	 * @since 1.0.0
	 *
	 * @param array $attributes Block attributes.
	 * @return string HTML.
	 */
	public function render_block_bestseller( $attributes ) {
		if ( empty( $attributes['keyword'] ) ) {
			return '<p class="azonmate-block-placeholder">' . esc_html__( 'Enter a category or keyword.', 'azonmate' ) . '</p>';
		}

		$shortcode_atts = array(
			'bestseller' => $attributes['keyword'],
			'items'      => $attributes['items'] ?? 10,
			'template'   => $attributes['template'] ?? 'default',
		);

		return $this->shortcode_manager->render_shortcode( $shortcode_atts, null );
	}

	/**
	 * Server-side render: Text Link.
	 *
	 * @since 1.0.0
	 *
	 * @param array $attributes Block attributes.
	 * @return string HTML.
	 */
	public function render_block_text_link( $attributes ) {
		if ( empty( $attributes['asin'] ) ) {
			return '<p class="azonmate-block-placeholder">' . esc_html__( 'Enter a product ASIN.', 'azonmate' ) . '</p>';
		}

		$shortcode_atts = array(
			'link'  => $attributes['asin'],
			'title' => $attributes['title'] ?? '',
		);

		$content = $attributes['text'] ?? '';

		return $this->shortcode_manager->render_shortcode( $shortcode_atts, $content );
	}
}
