<?php
/**
 * Template: Showcase – Grid Cards
 *
 * Premium responsive card grid with badges, ratings, pricing, and CTA buttons.
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

$cols_class = '';
if ( ! empty( $columns ) && $columns > 0 ) {
	$cols_class = ' azonmate-showcase-grid--cols-' . absint( $columns );
}
?>
<div class="azonmate-showcase azonmate-showcase--grid<?php echo esc_attr( $cols_class ); ?>">
	<?php if ( ! empty( $heading ) ) : ?>
		<h2 class="azonmate-showcase__heading"><?php echo esc_html( $heading ); ?></h2>
	<?php endif; ?>

	<div class="azonmate-showcase__grid">
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
			$btn_text    = $product->get_button_text();
			$link_attrs  = function_exists( 'azon_mate_link_attributes' ) ? azon_mate_link_attributes( $url, $asin ) : '';

			// Use per-product button text, then shortcode override, then global default.
			$final_btn_text = ! empty( $btn_text ) ? $btn_text : ( ! empty( $button_text ) ? $button_text : get_option( 'azon_mate_buy_button_text', 'Buy on Amazon' ) );
			?>
			<div class="azonmate-showcase__card" data-asin="<?php echo esc_attr( $asin ); ?>">

				<?php if ( $show_badge && ! empty( $badge ) ) : ?>
					<span class="azonmate-showcase__badge"><?php echo esc_html( $badge ); ?></span>
				<?php endif; ?>

				<?php if ( $savings ) : ?>
					<span class="azonmate-showcase__savings">-<?php echo esc_html( $savings ); ?>%</span>
				<?php endif; ?>

				<div class="azonmate-showcase__image-wrap">
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

					<h3 class="azonmate-showcase__title">
						<a <?php echo $link_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
							<?php echo esc_html( wp_trim_words( $title, 14 ) ); ?>
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
						<p class="azonmate-showcase__desc"><?php echo esc_html( wp_trim_words( $description, 18 ) ); ?></p>
					<?php endif; ?>
				</div>

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
		<?php endforeach; ?>
	</div>
</div>
