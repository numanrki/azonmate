<?php
/**
 * Template: Image Link – Default
 *
 * @package AzonMate
 * @since   1.0.0
 *
 * @var \AzonMate\Models\Product $product    Product object.
 * @var string                   $image_size Image size (small|medium|large).
 * @var string                   $asin       Product ASIN.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$url        = $product->get_detail_page_url();
$image      = $product->get_image_url();
$title      = $product->get_title();
$link_attrs = function_exists( 'azon_mate_link_attributes' ) ? azon_mate_link_attributes( $url, $asin ) : '';

if ( empty( $image ) ) {
	return;
}
?>
<a <?php echo $link_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
	class="azonmate-image-link azonmate-image-link--<?php echo esc_attr( $image_size ); ?>"
>
	<img
		src="<?php echo esc_url( $image ); ?>"
		alt="<?php echo esc_attr( $title ); ?>"
		class="azonmate-image-link__img"
		loading="lazy"
	/>
</a>
