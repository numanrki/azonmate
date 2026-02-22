<?php
/**
 * Field shortcode: [azonmate field="price" asin="ASIN"]
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
 * Class Field
 *
 * Outputs a single data point for a product.
 *
 * @since 1.0.0
 */
class Field extends AbstractShortcode {

	/**
	 * Render an individual field.
	 *
	 * @since 1.0.0
	 *
	 * @param array       $atts    Shortcode attributes.
	 * @param string|null $content Not used.
	 * @return string HTML.
	 */
	public function render( array $atts, $content = null ) {
		$atts = shortcode_atts(
			array(
				'field' => '',
				'asin'  => '',
			),
			$atts,
			'azonmate'
		);

		$field = sanitize_key( $atts['field'] );
		$asin  = sanitize_text_field( $atts['asin'] );

		if ( empty( $field ) || empty( $asin ) ) {
			return '';
		}

		$product = $this->get_product( $asin );
		if ( ! $product ) {
			return '';
		}

		switch ( $field ) {
			case 'price':
				$value = $product->get_price();
				return $value
					? '<span class="azonmate-field azonmate-field--price">' . esc_html( $value ) . '</span>'
					: '';

			case 'title':
				$value = $product->get_title();
				return $value
					? '<span class="azonmate-field azonmate-field--title">' . esc_html( $value ) . '</span>'
					: '';

			case 'rating':
				$rating = $product->get_rating();
				if ( $rating ) {
					return '<span class="azonmate-field azonmate-field--rating">' . azon_mate_render_stars( $rating ) . '</span>';
				}
				return '';

			case 'image':
				$image = $product->get_image_url();
				if ( $image ) {
					return sprintf(
						'<img src="%s" alt="%s" class="azonmate-field azonmate-field--image" loading="lazy" />',
						esc_url( $image ),
						esc_attr( $product->get_title() )
					);
				}
				return '';

			case 'url':
				return esc_url( $product->get_detail_page_url() );

			case 'prime':
				return $product->is_prime()
					? '<span class="azonmate-field azonmate-field--prime">' . azon_mate_render_prime_badge() . '</span>'
					: '';

			case 'savings':
				$savings = $product->get_savings_percentage();
				return $savings
					? '<span class="azonmate-field azonmate-field--savings">-' . esc_html( $savings ) . '%</span>'
					: '';

			default:
				/**
				 * Allow developers to add custom field renderers.
				 *
				 * @since 1.0.0
				 *
				 * @param string                    $output  HTML output (empty by default).
				 * @param string                    $field   Field name.
				 * @param \AzonMate\Models\Product  $product Product object.
				 */
				return apply_filters( 'azon_mate_field_output', '', $field, $product );
		}
	}
}
