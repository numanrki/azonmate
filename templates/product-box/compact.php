<?php
/**
 * Template: Product Box – Compact
 *
 * @package AzonMate
 * @since   1.0.0
 *
 * @var \AzonMate\Models\Product $product
 * @var string                   $title
 * @var bool                     $show_rating
 * @var bool                     $show_price
 * @var string                   $button_text
 * @var string                   $image_size
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$url        = $product->get_detail_page_url();
$image      = $product->get_image_url();
$price      = $product->get_price();
$rating     = $product->get_rating();
$is_prime   = $product->is_prime();
$asin       = $product->get_asin();
$link_attrs = function_exists( 'azon_mate_link_attributes' ) ? azon_mate_link_attributes( $url, $asin ) : '';
?>
<div class="azonmate-product-box azonmate-product-box--compact" data-asin="<?php echo esc_attr( $asin ); ?>">

	<div class="azonmate-product-box__image-wrap">
		<?php if ( $image ) : ?>
			<a <?php echo $link_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<img
					src="<?php echo esc_url( $image ); ?>"
					alt="<?php echo esc_attr( $title ); ?>"
					class="azonmate-product-box__image azonmate-product-box__image--small"
					loading="lazy"
				/>
			</a>
		<?php endif; ?>
	</div>

	<div class="azonmate-product-box__content">
		<h4 class="azonmate-product-box__title">
			<a <?php echo $link_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<?php echo esc_html( $title ); ?>
			</a>
		</h4>

		<div class="azonmate-product-box__meta">
			<?php if ( $show_rating && $rating ) : ?>
				<span class="azonmate-product-box__rating-inline">
					<?php
					if ( function_exists( 'azon_mate_render_stars' ) ) {
						echo azon_mate_render_stars( $rating ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>
				</span>
			<?php endif; ?>

			<?php if ( $show_price && $price ) : ?>
				<span class="azonmate-product-box__price"><?php echo esc_html( $price ); ?></span>
			<?php endif; ?>

			<?php if ( $is_prime && function_exists( 'azon_mate_render_prime_badge' ) ) : ?>
				<?php echo azon_mate_render_prime_badge(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php endif; ?>
		</div>
	</div>

</div>
