<?php
/**
 * Template: Collage – Dynamic Product Grid
 *
 * Auto-adjusting product collage with hover-reveal buy buttons.
 * Grid layout adapts based on product count for optimal visual display.
 *
 * @package AzonMate
 * @since   1.6.0
 *
 * Available variables:
 * @var \AzonMate\Models\Product[] $products    Array of products.
 * @var int                        $count       Number of products.
 * @var bool                       $show_price  Show prices.
 * @var bool                       $show_rating Show ratings.
 * @var bool                       $show_badge  Show badge labels.
 * @var bool                       $show_button Show buy button.
 * @var string                     $button_text Default button text.
 * @var string                     $heading     Optional section heading.
 * @var int                        $gap         Gap between cards in px.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( empty( $products ) ) {
	return;
}

$count     = count( $products );
$gap_px    = ! empty( $gap ) ? absint( $gap ) : 12;
$layout_id = 'azonmate-collage-' . wp_unique_id();

// Determine layout class based on product count.
$layout_class = 'azonmate-collage--auto';
if ( $count === 1 ) {
	$layout_class = 'azonmate-collage--hero';
} elseif ( $count === 2 ) {
	$layout_class = 'azonmate-collage--duo';
} elseif ( $count === 3 ) {
	$layout_class = 'azonmate-collage--trio';
} elseif ( $count === 4 ) {
	$layout_class = 'azonmate-collage--quad';
} elseif ( $count === 5 ) {
	$layout_class = 'azonmate-collage--penta';
}
?>
<div class="azonmate-collage <?php echo esc_attr( $layout_class ); ?>" id="<?php echo esc_attr( $layout_id ); ?>" style="--azonmate-collage-gap: <?php echo esc_attr( $gap_px ); ?>px;">

	<?php if ( ! empty( $heading ) ) : ?>
		<h2 class="azonmate-collage__heading"><?php echo esc_html( $heading ); ?></h2>
	<?php endif; ?>

	<div class="azonmate-collage__grid">
		<?php
		foreach ( $products as $index => $product ) :
			$url         = $product->get_detail_page_url();
			$image       = $product->get_image_url( 'large' );
			$title       = $product->get_title();
			$price       = $product->get_price();
			$list_price  = $product->get_list_price();
			$rating      = $product->get_rating();
			$reviews     = $product->get_review_count();
			$is_prime    = $product->is_prime();
			$savings     = $product->get_savings_percentage();
			$asin        = $product->get_asin();
			$badge       = $product->get_badge_label();
			$brand       = $product->get_brand();
			$btn_text    = $product->get_button_text();
			$link_attrs  = function_exists( 'azon_mate_link_attributes' ) ? azon_mate_link_attributes( $url, $asin ) : '';

			$final_btn_text = ! empty( $btn_text ) ? $btn_text : ( ! empty( $button_text ) ? $button_text : get_option( 'azon_mate_buy_button_text', 'Buy on Amazon' ) );
			?>
			<div class="azonmate-collage__card" data-asin="<?php echo esc_attr( $asin ); ?>">

				<?php if ( $show_badge && ! empty( $badge ) ) : ?>
					<span class="azonmate-collage__badge"><?php echo esc_html( $badge ); ?></span>
				<?php endif; ?>

				<?php if ( $savings ) : ?>
					<span class="azonmate-collage__savings">-<?php echo esc_html( $savings ); ?>%</span>
				<?php endif; ?>

				<div class="azonmate-collage__image-wrap">
					<?php if ( $image ) : ?>
						<a <?php echo $link_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
							<img
								src="<?php echo esc_url( $image ); ?>"
								alt="<?php echo esc_attr( $title ); ?>"
								class="azonmate-collage__image"
								loading="lazy"
							/>
						</a>
					<?php endif; ?>
				</div>

				<div class="azonmate-collage__overlay">
					<div class="azonmate-collage__info">
						<h3 class="azonmate-collage__title">
							<a <?php echo $link_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
								<?php echo esc_html( wp_trim_words( $title, 10 ) ); ?>
							</a>
						</h3>

						<?php if ( $show_rating && $rating ) : ?>
							<div class="azonmate-collage__rating">
								<?php
								if ( function_exists( 'azon_mate_render_stars' ) ) {
									echo azon_mate_render_stars( $rating ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								}
								?>
								<?php if ( $reviews ) : ?>
									<span class="azonmate-collage__reviews">(<?php echo esc_html( number_format_i18n( $reviews ) ); ?>)</span>
								<?php endif; ?>
							</div>
						<?php endif; ?>

						<?php if ( $show_price && $price ) : ?>
							<div class="azonmate-collage__price-wrap">
								<span class="azonmate-collage__price"><?php echo esc_html( $price ); ?></span>
								<?php if ( $list_price && $list_price !== $price ) : ?>
									<span class="azonmate-collage__old-price"><del><?php echo esc_html( $list_price ); ?></del></span>
								<?php endif; ?>
							</div>
						<?php endif; ?>
					</div>

					<?php if ( $show_button ) : ?>
						<div class="azonmate-collage__action">
							<?php
							if ( function_exists( 'azon_mate_render_buy_button' ) ) {
								echo azon_mate_render_buy_button( $url, $final_btn_text, $asin ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
							?>
						</div>
					<?php endif; ?>
				</div>

			</div>
		<?php endforeach; ?>
	</div>

	<?php if ( function_exists( 'azon_mate_render_disclosure' ) ) {
		echo azon_mate_render_disclosure(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} ?>
</div>
