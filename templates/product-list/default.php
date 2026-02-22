<?php
/**
 * Template: Product List – Default (vertical)
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
<div class="azonmate-product-list azonmate-product-list--default">
	<?php
	$index = 0;
	foreach ( $products as $product ) :
		$index++;
		$url        = $product->get_detail_page_url();
		$image      = $product->get_image_url();
		$title      = $product->get_title();
		$price      = $product->get_price();
		$list_price = $product->get_list_price();
		$rating     = $product->get_rating();
		$reviews    = $product->get_review_count();
		$is_prime   = $product->is_prime();
		$asin       = $product->get_asin();
		$link_attrs = function_exists( 'azon_mate_link_attributes' ) ? azon_mate_link_attributes( $url, $asin ) : '';
		$button_text = get_option( 'azon_mate_button_text', __( 'View on Amazon', 'azonmate' ) );
		?>
		<div class="azonmate-product-list__item" data-asin="<?php echo esc_attr( $asin ); ?>">
			<span class="azonmate-product-list__rank"><?php echo esc_html( $index ); ?></span>

			<div class="azonmate-product-list__image-wrap">
				<?php if ( $image ) : ?>
					<a <?php echo $link_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<img
							src="<?php echo esc_url( $image ); ?>"
							alt="<?php echo esc_attr( $title ); ?>"
							class="azonmate-product-list__image"
							loading="lazy"
						/>
					</a>
				<?php endif; ?>
			</div>

			<div class="azonmate-product-list__details">
				<h4 class="azonmate-product-list__title">
					<a <?php echo $link_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
						<?php echo esc_html( $title ); ?>
					</a>
				</h4>

				<?php if ( $rating ) : ?>
					<div class="azonmate-product-list__rating">
						<?php
						if ( function_exists( 'azon_mate_render_stars' ) ) {
							echo azon_mate_render_stars( $rating ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						?>
						<?php if ( $reviews ) : ?>
							<span class="azonmate-product-list__review-count">(<?php echo esc_html( number_format_i18n( $reviews ) ); ?>)</span>
						<?php endif; ?>
					</div>
				<?php endif; ?>
			</div>

			<div class="azonmate-product-list__price-col">
				<?php if ( $price ) : ?>
					<span class="azonmate-product-list__price"><?php echo esc_html( $price ); ?></span>
				<?php endif; ?>
				<?php if ( $list_price && $list_price !== $price ) : ?>
					<span class="azonmate-product-list__list-price"><del><?php echo esc_html( $list_price ); ?></del></span>
				<?php endif; ?>

				<?php if ( $is_prime && function_exists( 'azon_mate_render_prime_badge' ) ) : ?>
					<div class="azonmate-product-list__prime">
						<?php echo azon_mate_render_prime_badge(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				<?php endif; ?>

				<div class="azonmate-product-list__action">
					<?php
					if ( function_exists( 'azon_mate_render_buy_button' ) ) {
						echo azon_mate_render_buy_button( $url, $button_text, $asin ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
</div>
