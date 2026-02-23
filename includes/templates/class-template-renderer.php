<?php
/**
 * Template loading and rendering engine.
 *
 * @package AzonMate\Templates
 * @since   1.0.0
 */

namespace AzonMate\Templates;

use AzonMate\Models\Product;

// Abort if this file is called directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class TemplateRenderer
 *
 * Loads and renders display templates for product boxes, lists, tables, etc.
 * Supports theme template overrides.
 *
 * Template loading priority:
 * 1. wp-content/themes/{theme}/azonmate/{template}.php
 * 2. wp-content/plugins/azonmate/templates/{template}.php
 *
 * @since 1.0.0
 */
class TemplateRenderer {

	/**
	 * Render a template with the given data.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template_path Template path relative to templates dir (e.g., 'product-box/default').
	 * @param array  $data          Template variables.
	 * @return string Rendered HTML output.
	 */
	public function render( $template_path, $data = array() ) {
		$file = $this->locate_template( $template_path );

		if ( ! $file ) {
			if ( \AzonMate\Plugin::is_debug_enabled() ) {
				error_log( '[AzonMate] Template not found: ' . $template_path );
			}
			return '';
		}

		// Extract data for use in template.
		// phpcs:ignore WordPress.PHP.DontExtract
		extract( $data, EXTR_SKIP );

		ob_start();
		include $file;
		$output = ob_get_clean();

		/**
		 * Filter rendered template output.
		 *
		 * @since 1.0.0
		 *
		 * @param string $output        Rendered HTML.
		 * @param string $template_path Template path.
		 * @param array  $data          Template data.
		 */
		return apply_filters( 'azon_mate_template_output', $output, $template_path, $data );
	}

	/**
	 * Render a product box.
	 *
	 * @since 1.0.0
	 *
	 * @param Product $product  The product to display.
	 * @param array   $options  Display options.
	 * @return string HTML output.
	 */
	public function render_product_box( Product $product, $options = array() ) {
		$defaults = array(
			'template'    => get_option( 'azon_mate_default_template', 'default' ),
			'title'       => $product->get_title(),
			'show_price'  => '1' === get_option( 'azon_mate_show_prices', '1' ),
			'show_rating' => '1' === get_option( 'azon_mate_show_ratings', '1' ),
			'show_prime'  => '1' === get_option( 'azon_mate_show_prime_badge', '1' ),
			'show_desc'   => '1' === get_option( 'azon_mate_show_description', '1' ),
			'show_button' => '1' === get_option( 'azon_mate_show_buy_button', '1' ),
			'button_text' => get_option( 'azon_mate_buy_button_text', 'Buy on Amazon' ),
			'image_size'  => 'medium',
			'new_tab'     => '1' === get_option( 'azon_mate_open_new_tab', '1' ),
			'nofollow'    => '1' === get_option( 'azon_mate_nofollow_links', '1' ),
		);

		$options = wp_parse_args( $options, $defaults );

		$template_name = $options['template'];

		// Pass product + all options as flat template variables.
		$data            = $options;
		$data['product'] = $product;

		return $this->render( 'product-box/' . $template_name, $data );
	}

	/**
	 * Render a product list.
	 *
	 * @since 1.0.0
	 *
	 * @param Product[] $products  Array of products.
	 * @param array     $options   Display options.
	 * @return string HTML output.
	 */
	public function render_product_list( $products, $options = array() ) {
		$defaults = array(
			'template' => 'default',
			'max'      => 10,
		);

		$options  = wp_parse_args( $options, $defaults );
		$products = array_slice( $products, 0, $options['max'] );

		$data             = $options;
		$data['products'] = $products;

		return $this->render( 'product-list/' . $options['template'], $data );
	}

	/**
	 * Render a comparison table.
	 *
	 * @since 1.0.0
	 *
	 * @param Product[] $products  Array of products.
	 * @param array     $options   Display options.
	 * @return string HTML output.
	 */
	public function render_comparison_table( $products, $options = array() ) {
		$defaults = array(
			'template'       => 'default',
			'columns'        => array( 'image', 'title', 'price', 'rating', 'prime', 'button' ),
			'highlight_asin' => '',
			'highlight'      => '',
			'max'            => 10,
		);

		$options  = wp_parse_args( $options, $defaults );
		$products = array_slice( $products, 0, $options['max'] );

		// Support 'highlight' as alias for 'highlight_asin'.
		if ( empty( $options['highlight_asin'] ) && ! empty( $options['highlight'] ) ) {
			$options['highlight_asin'] = $options['highlight'];
		}

		$data             = $options;
		$data['products'] = $products;

		return $this->render( 'comparison-table/' . $options['template'], $data );
	}

	/**
	 * Render a bestseller list.
	 *
	 * @since 1.0.0
	 *
	 * @param Product[] $products  Array of products.
	 * @param array     $options   Display options.
	 * @return string HTML output.
	 */
	public function render_bestseller_list( $products, $options = array() ) {
		$defaults = array(
			'template' => 'default',
			'keyword'  => '',
			'max'      => 10,
		);

		$options  = wp_parse_args( $options, $defaults );
		$products = array_slice( $products, 0, $options['max'] );

		$data             = $options;
		$data['products'] = $products;

		return $this->render( 'bestseller/' . $options['template'], $data );
	}

	/**
	 * Render a text link.
	 *
	 * @since 1.0.0
	 *
	 * @param Product $product The product.
	 * @param string  $anchor  Anchor text (if empty, uses product title).
	 * @param array   $options Display options.
	 * @return string HTML output.
	 */
	public function render_text_link( Product $product, $anchor = '', $options = array() ) {
		$data = is_array( $options ) ? $options : array();
		$data['product'] = $product;
		if ( ! empty( $anchor ) ) {
			$data['anchor'] = $anchor;
		}
		if ( empty( $data['anchor'] ) ) {
			$data['anchor'] = $product ? $product->get_title() : '';
		}

		return $this->render( 'text-link/default', $data );
	}

	/**
	 * Render an image link.
	 *
	 * @since 1.0.0
	 *
	 * @param Product $product The product.
	 * @param array   $options Display options.
	 * @return string HTML output.
	 */
	public function render_image_link( Product $product, $options = array() ) {
		$defaults = array(
			'image_size' => 'medium',
		);
		$options = wp_parse_args( $options, $defaults );

		$data            = $options;
		$data['product'] = $product;

		return $this->render( 'image-link/default', $data );
	}

	/**
	 * Render a premium product showcase (grid, list, masonry, or table).
	 *
	 * @since 1.2.0
	 *
	 * @param Product[] $products Array of products.
	 * @param array     $options  Display options.
	 * @return string HTML output.
	 */
	public function render_showcase( $products, $options = array() ) {
		$defaults = array(
			'layout'      => get_option( 'azon_mate_showcase_layout', 'grid' ),
			'columns'     => absint( get_option( 'azon_mate_showcase_columns', 3 ) ),
			'max'         => 12,
			'show_price'  => '1' === get_option( 'azon_mate_show_prices', '1' ),
			'show_rating' => '1' === get_option( 'azon_mate_show_ratings', '1' ),
			'show_badge'  => true,
			'show_button' => true,
			'button_text' => get_option( 'azon_mate_buy_button_text', 'Buy on Amazon' ),
			'new_tab'     => '1' === get_option( 'azon_mate_open_new_tab', '1' ),
			'nofollow'    => '1' === get_option( 'azon_mate_nofollow_links', '1' ),
			'heading'     => '',
			'size'        => '',
		);

		$options  = wp_parse_args( $options, $defaults );
		$products = array_slice( $products, 0, absint( $options['max'] ) );

		// Ensure valid layout.
		$valid_layouts = array( 'grid', 'list', 'masonry', 'table', 'hero', 'compact', 'split', 'deal' );
		if ( ! in_array( $options['layout'], $valid_layouts, true ) ) {
			$options['layout'] = 'grid';
		}

		$data             = $options;
		$data['products'] = $products;

		return $this->render( 'showcase/' . $options['layout'], $data );
	}

	/**
	 * Locate a template file, checking theme override first.
	 *
	 * @since 1.0.0
	 *
	 * @param string $template_path Relative template path without .php extension.
	 * @return string|false Full file path or false if not found.
	 */
	private function locate_template( $template_path ) {
		$template_path = ltrim( $template_path, '/' );

		// 1. Check theme override.
		$theme_file = get_stylesheet_directory() . '/azonmate/' . $template_path . '.php';
		if ( file_exists( $theme_file ) ) {
			return $theme_file;
		}

		// Also check parent theme.
		$parent_theme_file = get_template_directory() . '/azonmate/' . $template_path . '.php';
		if ( $parent_theme_file !== $theme_file && file_exists( $parent_theme_file ) ) {
			return $parent_theme_file;
		}

		// 2. Check plugin templates directory.
		$plugin_file = AZON_MATE_PLUGIN_DIR . 'templates/' . $template_path . '.php';
		if ( file_exists( $plugin_file ) ) {
			return $plugin_file;
		}

		return false;
	}
}
