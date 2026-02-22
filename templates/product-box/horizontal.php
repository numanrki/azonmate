<?php
/**
 * Template: Product Box – Horizontal
 *
 * @package AzonMate
 * @since   1.0.0
 *
 * @var \AzonMate\Models\Product $product
 * @var string                   $title
 * @var bool                     $show_desc
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
$list_price = $product->get_list_price();
$rating     = $product->get_rating();
$reviews    = $product->get_review_count();
$features   = $product->get_features();
$is_prime   = $product->is_prime();
$savings    = $product->get_savings_percentage();
$asin       = $product->get_asin();
$link_attrs = function_exists( 'azon_mate_link_attributes' ) ? azon_mate_link_attributes( $url, $asin ) : '';
?>
<div class="azonmate-product-box azonmate-product-box--horizontal" data-asin="<?php echo esc_attr( $asin ); ?>">

	<div class="azonmate-product-box__image-wrap">
		<?php if ( $savings ) : ?>
			<span class="azonmate-product-box__savings">-<?php echo esc_html( $savings ); ?>%</span>
		<?php endif; ?>

		<?php if ( $image ) : ?>
			<a <?php echo $link_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<img
					src="<?php echo esc_url( $image ); ?>"
					alt="<?php echo esc_attr( $title ); ?>"
					class="azonmate-product-box__image azonmate-product-box__image--<?php echo esc_attr( $image_size ); ?>"
					loading="lazy"
				/>
			</a>
		<?php endif; ?>
	</div>

	<div class="azonmate-product-box__body">
		<h3 class="azonmate-product-box__title">
			<a <?php echo $link_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<?php echo esc_html( $title ); ?>
			</a>
		</h3>

		<?php if ( $show_rating && $rating ) : ?>
			<div class="azonmate-product-box__rating">
				<?php
				if ( function_exists( 'azon_mate_render_stars' ) ) {
					echo azon_mate_render_stars( $rating ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
				<?php if ( $reviews ) : ?>
					<span class="azonmate-product-box__review-count">(<?php echo esc_html( number_format_i18n( $reviews ) ); ?>)</span>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $show_desc && ! empty( $features ) ) : ?>
			<ul class="azonmate-product-box__features">
				<?php foreach ( array_slice( $features, 0, 3 ) as $feature ) : ?>
					<li><?php echo esc_html( $feature ); ?></li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>

	<div class="azonmate-product-box__sidebar">
		<?php if ( $show_price && $price ) : ?>
			<div class="azonmate-product-box__price-wrap">
				<span class="azonmate-product-box__price"><?php echo esc_html( $price ); ?></span>
				<?php if ( $list_price && $list_price !== $price ) : ?>
					<span class="azonmate-product-box__list-price"><del><?php echo esc_html( $list_price ); ?></del></span>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $is_prime && function_exists( 'azon_mate_render_prime_badge' ) ) : ?>
			<div class="azonmate-product-box__prime">
				<?php echo azon_mate_render_prime_badge(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</div>
		<?php endif; ?>

		<div class="azonmate-product-box__actions">
			<?php
			if ( function_exists( 'azon_mate_render_buy_button' ) ) {
				echo azon_mate_render_buy_button( $url, $button_text, $asin ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			?>
		</div>
	</div>

</div>
