<?php
/**
 * Template: Showcase – Masonry / Collage
 *
 * Pinterest-style staggered grid with varying card heights.
 *
 * @package AzonMate
 * @since   1.2.0
 *
 * Available variables:
 * @var \AzonMate\Models\Product[] $products    Array of products.
 * @var string                     $layout      Layout name.
 * @var int                        $columns     Columns override (0 = auto).
 * @var bool                       $show_price  Show prices.
 * @var bool                       $show_rating Show ratings.
 * @var bool                       $show_badge  Show badge labels.
 * @var bool                       $show_button Show buy button.
 * @var string                     $button_text Default button text.
 * @var string                     $heading     Optional section heading.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( empty( $products ) ) {
	return;
}

$cols = ( ! empty( $columns ) && $columns > 0 ) ? absint( $columns ) : 3;
?>
<?php
$size_class = '';
if ( ! empty( $size ) ) {
	$size_class = ' azonmate-showcase--size-' . sanitize_html_class( $size );
}
?>
<div class="azonmate-showcase azonmate-showcase--masonry<?php echo esc_attr( $size_class ); ?>" style="--azonmate-masonry-cols: <?php echo esc_attr( $cols ); ?>;">
	<?php if ( ! empty( $heading ) ) : ?>
		<h2 class="azonmate-showcase__heading"><?php echo esc_html( $heading ); ?></h2>
	<?php endif; ?>

	<div class="azonmate-showcase__masonry">
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
			$description = $product->get_description();
			$features    = $product->get_features();
			$btn_text    = $product->get_button_text();
			$link_attrs  = function_exists( 'azon_mate_link_attributes' ) ? azon_mate_link_attributes( $url, $asin ) : '';

			$final_btn_text = ! empty( $btn_text ) ? $btn_text : ( ! empty( $button_text ) ? $button_text : get_option( 'azon_mate_buy_button_text', 'Buy on Amazon' ) );
			?>
			<div class="azonmate-showcase__masonry-item" data-asin="<?php echo esc_attr( $asin ); ?>">

				<?php if ( $show_badge && ! empty( $badge ) ) : ?>
					<span class="azonmate-showcase__badge"><?php echo esc_html( $badge ); ?></span>
				<?php endif; ?>

				<div class="azonmate-showcase__image-wrap">
					<?php if ( $savings ) : ?>
						<span class="azonmate-showcase__savings">-<?php echo esc_html( $savings ); ?>%</span>
					<?php endif; ?>
					<?php if ( $image ) : ?>
						<a <?php echo $link_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
							<img
								src="<?php echo esc_url( $image ); ?>"
								alt="<?php echo esc_attr( $title ); ?>"
								class="azonmate-showcase__image"
								loading="lazy"
							/>
						</a>
					<?php endif; ?>
				</div>

				<div class="azonmate-showcase__content">
					<?php if ( $brand ) : ?>
						<span class="azonmate-showcase__brand"><?php echo esc_html( $brand ); ?></span>
					<?php endif; ?>

					<h3 class="azonmate-showcase__title azonmate-showcase__title--compact">
						<a <?php echo $link_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
							<?php echo esc_html( wp_trim_words( $title, 12 ) ); ?>
						</a>
					</h3>

					<?php if ( $show_rating && $rating ) : ?>
						<div class="azonmate-showcase__rating">
							<?php
							if ( function_exists( 'azon_mate_render_stars' ) ) {
								echo azon_mate_render_stars( $rating ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
							?>
							<?php if ( $reviews ) : ?>
								<span class="azonmate-showcase__reviews">(<?php echo esc_html( number_format_i18n( $reviews ) ); ?>)</span>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if ( $show_price && $price ) : ?>
						<div class="azonmate-showcase__price-wrap">
							<span class="azonmate-showcase__price"><?php echo esc_html( $price ); ?></span>
							<?php if ( $list_price && $list_price !== $price ) : ?>
								<span class="azonmate-showcase__old-price"><del><?php echo esc_html( $list_price ); ?></del></span>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if ( $is_prime && function_exists( 'azon_mate_render_prime_badge' ) ) : ?>
						<div class="azonmate-showcase__prime">
							<?php echo azon_mate_render_prime_badge(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						</div>
					<?php endif; ?>

					<?php if ( $description ) : ?>
						<p class="azonmate-showcase__desc"><?php echo esc_html( wp_trim_words( $description, 15 ) ); ?></p>
					<?php endif; ?>

					<?php if ( ! empty( $features ) ) : ?>
						<ul class="azonmate-showcase__features azonmate-showcase__features--short">
							<?php foreach ( array_slice( $features, 0, 2 ) as $feature ) : ?>
								<li><?php echo esc_html( wp_trim_words( $feature, 8 ) ); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>

					<?php if ( $show_button ) : ?>
						<div class="azonmate-showcase__action">
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
