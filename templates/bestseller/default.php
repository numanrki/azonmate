<?php
/**
 * Template: Bestseller List – Default
 *
 * @package AzonMate
 * @since   1.0.0
 *
 * @var \AzonMate\Models\Product[] $products Array of products.
 * @var string                     $keyword  Category/keyword.
 * @var string                     $template Template name.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( empty( $products ) ) {
	return;
}

$button_text = get_option( 'azon_mate_button_text', __( 'View on Amazon', 'azonmate' ) );
?>
<div class="azonmate-bestseller azonmate-bestseller--default">

	<?php if ( ! empty( $keyword ) ) : ?>
		<h3 class="azonmate-bestseller__heading">
			<?php
			printf(
				/* translators: %s: category or keyword */
				esc_html__( 'Best Sellers in %s', 'azonmate' ),
				esc_html( $keyword )
			);
			?>
		</h3>
	<?php endif; ?>

	<div class="azonmate-bestseller__list">
		<?php
		$rank = 0;
		foreach ( $products as $product ) :
			$rank++;
			$url        = $product->get_detail_page_url();
			$image      = $product->get_image_url();
			$title      = $product->get_title();
			$price      = $product->get_price();
			$rating     = $product->get_rating();
			$reviews    = $product->get_review_count();
			$is_prime   = $product->is_prime();
			$asin       = $product->get_asin();
			$link_attrs = function_exists( 'azon_mate_link_attributes' ) ? azon_mate_link_attributes( $url, $asin ) : '';
			?>
			<div class="azonmate-bestseller__item" data-asin="<?php echo esc_attr( $asin ); ?>">
				<span class="azonmate-bestseller__rank">#<?php echo esc_html( $rank ); ?></span>

				<div class="azonmate-bestseller__image-wrap">
					<?php if ( $image ) : ?>
						<a <?php echo $link_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
							<img
								src="<?php echo esc_url( $image ); ?>"
								alt="<?php echo esc_attr( $title ); ?>"
								class="azonmate-bestseller__image"
								loading="lazy"
							/>
						</a>
					<?php endif; ?>
				</div>

				<div class="azonmate-bestseller__details">
					<h4 class="azonmate-bestseller__title">
						<a <?php echo $link_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
							<?php echo esc_html( $title ); ?>
						</a>
					</h4>

					<?php if ( $rating ) : ?>
						<div class="azonmate-bestseller__rating">
							<?php
							if ( function_exists( 'azon_mate_render_stars' ) ) {
								echo azon_mate_render_stars( $rating ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
							?>
							<?php if ( $reviews ) : ?>
								<span class="azonmate-bestseller__review-count">(<?php echo esc_html( number_format_i18n( $reviews ) ); ?>)</span>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<div class="azonmate-bestseller__price-row">
						<?php if ( $price ) : ?>
							<span class="azonmate-bestseller__price"><?php echo esc_html( $price ); ?></span>
						<?php endif; ?>

						<?php if ( $is_prime && function_exists( 'azon_mate_render_prime_badge' ) ) : ?>
							<?php echo azon_mate_render_prime_badge(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php endif; ?>
					</div>
				</div>

				<div class="azonmate-bestseller__action">
					<?php
					if ( function_exists( 'azon_mate_render_buy_button' ) ) {
						echo azon_mate_render_buy_button( $url, $button_text, $asin ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>
				</div>
			</div>
		<?php endforeach; ?>
	</div>

</div>
