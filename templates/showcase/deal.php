<?php
/**
 * Template: Showcase – Deal Card (Single Product, Price-Drop Focus)
 *
 * Emphasis on savings: shows current price, old price, and savings badge.
 * Orange accent border draws attention. Best when product has a list price.
 *
 * @package AzonMate
 * @since   1.3.0
 *
 * Available variables:
 * @var \AzonMate\Models\Product[] $products    Array of products (uses first).
 * @var string                     $layout      Layout name.
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

$product    = $products[0];
$url        = $product->get_detail_page_url();
$image      = $product->get_image_url( 'medium' );
$title      = $product->get_title();
$price      = $product->get_price();
$list_price = $product->get_list_price();
$rating     = $product->get_rating();
$reviews    = $product->get_review_count();
$savings    = $product->get_savings_percentage();
$asin       = $product->get_asin();
$badge      = $product->get_badge_label();
$brand      = $product->get_brand();
$btn_text   = $product->get_button_text();
$link_attrs = function_exists( 'azon_mate_link_attributes' ) ? azon_mate_link_attributes( $url, $asin ) : '';

$final_btn_text = ! empty( $btn_text ) ? $btn_text : ( ! empty( $button_text ) ? $button_text : get_option( 'azon_mate_buy_button_text', 'Buy on Amazon' ) );
?>
<div class="azonmate-showcase azonmate-showcase--deal">
	<?php if ( ! empty( $heading ) ) : ?>
		<h2 class="azonmate-showcase__heading"><?php echo esc_html( $heading ); ?></h2>
	<?php endif; ?>

	<div class="azonmate-showcase__deal" data-asin="<?php echo esc_attr( $asin ); ?>">

		<div class="azonmate-showcase__deal-image">
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

		<div class="azonmate-showcase__deal-body">
			<?php if ( $show_badge && ! empty( $badge ) ) : ?>
				<span class="azonmate-showcase__badge" style="position:static;margin-bottom:0.4rem;"><?php echo esc_html( $badge ); ?></span>
			<?php endif; ?>

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
				<div class="azonmate-showcase__deal-prices">
					<span class="azonmate-showcase__price"><?php echo esc_html( $price ); ?></span>
					<?php if ( $list_price && $list_price !== $price ) : ?>
						<span class="azonmate-showcase__old-price"><del><?php echo esc_html( $list_price ); ?></del></span>
					<?php endif; ?>
					<?php if ( $savings ) : ?>
						<span class="azonmate-showcase__deal-save"><?php /* translators: %d: savings percentage */ printf( esc_html__( 'Save %d%%', 'azonmate' ), absint( $savings ) ); ?></span>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>

		<?php if ( $show_button ) : ?>
			<div class="azonmate-showcase__deal-side">
				<div class="azonmate-showcase__action">
					<?php
					if ( function_exists( 'azon_mate_render_buy_button' ) ) {
						echo azon_mate_render_buy_button( $url, $final_btn_text, $asin ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>
				</div>
			</div>
		<?php endif; ?>
	</div>

	<?php if ( function_exists( 'azon_mate_render_disclosure' ) ) {
		echo azon_mate_render_disclosure(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} ?>
</div>
