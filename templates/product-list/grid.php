<?php
/**
 * Template: Product List – Grid
 *
 * @package AzonMate
 * @since   1.0.0
 *
 * @var \AzonMate\Models\Product[] $products Array of products.
 * @var string                     $template Template name.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( empty( $products ) ) {
	return;
}
?>
<div class="azonmate-product-list azonmate-product-list--grid">
	<?php
	foreach ( $products as $product ) :
		$url        = $product->get_detail_page_url();
		$image      = $product->get_image_url();
		$title      = $product->get_title();
		$price      = $product->get_price();
		$rating     = $product->get_rating();
		$is_prime   = $product->is_prime();
		$asin       = $product->get_asin();
		$link_attrs = function_exists( 'azon_mate_link_attributes' ) ? azon_mate_link_attributes( $url, $asin ) : '';
		$button_text = get_option( 'azon_mate_button_text', __( 'View on Amazon', 'azonmate' ) );
		?>
		<div class="azonmate-product-list__grid-item" data-asin="<?php echo esc_attr( $asin ); ?>">
			<div class="azonmate-product-list__grid-image">
				<?php if ( $image ) : ?>
					<a <?php echo $link_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<img
							src="<?php echo esc_url( $image ); ?>"
							alt="<?php echo esc_attr( $title ); ?>"
							loading="lazy"
						/>
					</a>
				<?php endif; ?>
			</div>

			<h4 class="azonmate-product-list__grid-title">
				<a <?php echo $link_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<?php echo esc_html( wp_trim_words( $title, 12 ) ); ?>
				</a>
			</h4>

			<?php if ( $rating && function_exists( 'azon_mate_render_stars' ) ) : ?>
				<div class="azonmate-product-list__grid-rating">
					<?php echo azon_mate_render_stars( $rating ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			<?php endif; ?>

			<?php if ( $price ) : ?>
				<div class="azonmate-product-list__grid-price">
					<?php echo esc_html( $price ); ?>
					<?php if ( $is_prime && function_exists( 'azon_mate_render_prime_badge' ) ) : ?>
						<?php echo azon_mate_render_prime_badge(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="azonmate-product-list__grid-action">
				<?php
				if ( function_exists( 'azon_mate_render_buy_button' ) ) {
					echo azon_mate_render_buy_button( $url, $button_text, $asin ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</div>
		</div>
	<?php endforeach; ?>
</div>
