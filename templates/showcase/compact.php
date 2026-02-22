<?php
/**
 * Template: Showcase – Compact Inline Card (Single Product)
 *
 * Slim horizontal card for mid-article placements. Image + title/price + button.
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
$image      = $product->get_image_url( 'small' );
$title      = $product->get_title();
$price      = $product->get_price();
$list_price = $product->get_list_price();
$rating     = $product->get_rating();
$reviews    = $product->get_review_count();
$asin       = $product->get_asin();
$badge      = $product->get_badge_label();
$btn_text   = $product->get_button_text();
$link_attrs = function_exists( 'azon_mate_link_attributes' ) ? azon_mate_link_attributes( $url, $asin ) : '';

$final_btn_text = ! empty( $btn_text ) ? $btn_text : ( ! empty( $button_text ) ? $button_text : get_option( 'azon_mate_buy_button_text', 'Buy on Amazon' ) );
?>
<div class="azonmate-showcase azonmate-showcase--compact">
	<?php if ( ! empty( $heading ) ) : ?>
		<h2 class="azonmate-showcase__heading"><?php echo esc_html( $heading ); ?></h2>
	<?php endif; ?>

	<div class="azonmate-showcase__compact" data-asin="<?php echo esc_attr( $asin ); ?>">

		<?php if ( $show_badge && ! empty( $badge ) ) : ?>
			<span class="azonmate-showcase__badge" style="position:static;margin-right:0.5rem;"><?php echo esc_html( $badge ); ?></span>
		<?php endif; ?>

		<div class="azonmate-showcase__compact-image">
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

		<div class="azonmate-showcase__compact-body">
			<h3 class="azonmate-showcase__title">
				<a <?php echo $link_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
					<?php echo esc_html( wp_trim_words( $title, 12 ) ); ?>
				</a>
			</h3>

			<div class="azonmate-showcase__compact-meta">
				<?php if ( $show_rating && $rating ) : ?>
					<span class="azonmate-showcase__rating" style="margin-bottom:0;">
						<?php
						if ( function_exists( 'azon_mate_render_stars' ) ) {
							echo azon_mate_render_stars( $rating ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						?>
					</span>
				<?php endif; ?>

				<?php if ( $show_price && $price ) : ?>
					<span class="azonmate-showcase__price"><?php echo esc_html( $price ); ?></span>
					<?php if ( $list_price && $list_price !== $price ) : ?>
						<span class="azonmate-showcase__old-price"><del><?php echo esc_html( $list_price ); ?></del></span>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( $show_button ) : ?>
			<div class="azonmate-showcase__compact-side">
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
