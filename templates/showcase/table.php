<?php
/**
 * Template: Showcase – Comparison Table
 *
 * Premium modern comparison table with column highlights and badges.
 *
 * @package AzonMate
 * @since   1.2.0
 *
 * Available variables:
 * @var \AzonMate\Models\Product[] $products    Array of products.
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
?>
<?php
$size_class = '';
if ( ! empty( $size ) ) {
	$size_class = ' azonmate-showcase--size-' . sanitize_html_class( $size );
}
?>
<div class="azonmate-showcase azonmate-showcase--table<?php echo esc_attr( $size_class ); ?>">
	<?php if ( ! empty( $heading ) ) : ?>
		<h2 class="azonmate-showcase__heading"><?php echo esc_html( $heading ); ?></h2>
	<?php endif; ?>

	<div class="azonmate-showcase__table-wrap">
		<table class="azonmate-showcase__table">
			<thead>
				<tr>
					<?php foreach ( $products as $product ) :
						$badge      = $product->get_badge_label();
						$has_badge  = $show_badge && ! empty( $badge );
						$header_cls = $has_badge ? ' azonmate-showcase__th--highlight' : '';
						?>
						<th class="azonmate-showcase__th<?php echo esc_attr( $header_cls ); ?>">
							<?php if ( $has_badge ) : ?>
								<span class="azonmate-showcase__badge azonmate-showcase__badge--table"><?php echo esc_html( $badge ); ?></span>
							<?php endif; ?>
						</th>
					<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>
				<!-- Image Row -->
				<tr class="azonmate-showcase__tr--image">
					<?php foreach ( $products as $product ) :
						$url        = $product->get_detail_page_url();
						$image      = $product->get_image_url( 'medium' );
						$title      = $product->get_title();
						$asin       = $product->get_asin();
						$badge      = $product->get_badge_label();
						$has_badge  = $show_badge && ! empty( $badge );
						$cell_cls   = $has_badge ? ' azonmate-showcase__td--highlight' : '';
						$link_attrs = function_exists( 'azon_mate_link_attributes' ) ? azon_mate_link_attributes( $url, $asin ) : '';
						?>
						<td class="azonmate-showcase__td<?php echo esc_attr( $cell_cls ); ?>">
							<?php if ( $image ) : ?>
								<a <?php echo $link_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
									<img src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $title ); ?>" class="azonmate-showcase__table-image" loading="lazy" />
								</a>
							<?php endif; ?>
						</td>
					<?php endforeach; ?>
				</tr>

				<!-- Title Row -->
				<tr class="azonmate-showcase__tr--title">
					<?php foreach ( $products as $product ) :
						$url        = $product->get_detail_page_url();
						$title      = $product->get_title();
						$asin       = $product->get_asin();
						$brand      = $product->get_brand();
						$badge      = $product->get_badge_label();
						$has_badge  = $show_badge && ! empty( $badge );
						$cell_cls   = $has_badge ? ' azonmate-showcase__td--highlight' : '';
						$link_attrs = function_exists( 'azon_mate_link_attributes' ) ? azon_mate_link_attributes( $url, $asin ) : '';
						?>
						<td class="azonmate-showcase__td<?php echo esc_attr( $cell_cls ); ?>">
							<?php if ( $brand ) : ?>
								<span class="azonmate-showcase__brand"><?php echo esc_html( $brand ); ?></span>
							<?php endif; ?>
							<strong class="azonmate-showcase__table-title">
								<a <?php echo $link_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
									<?php echo esc_html( wp_trim_words( $title, 10 ) ); ?>
								</a>
							</strong>
						</td>
					<?php endforeach; ?>
				</tr>

				<!-- Rating Row -->
				<?php if ( $show_rating ) : ?>
				<tr class="azonmate-showcase__tr--rating">
					<?php foreach ( $products as $product ) :
						$rating    = $product->get_rating();
						$reviews   = $product->get_review_count();
						$badge     = $product->get_badge_label();
						$has_badge = $show_badge && ! empty( $badge );
						$cell_cls  = $has_badge ? ' azonmate-showcase__td--highlight' : '';
						?>
						<td class="azonmate-showcase__td<?php echo esc_attr( $cell_cls ); ?>">
							<?php if ( $rating ) : ?>
								<div class="azonmate-showcase__rating azonmate-showcase__rating--center">
									<?php
									if ( function_exists( 'azon_mate_render_stars' ) ) {
										echo azon_mate_render_stars( $rating ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
									}
									?>
									<?php if ( $reviews ) : ?>
										<span class="azonmate-showcase__reviews">(<?php echo esc_html( number_format_i18n( $reviews ) ); ?>)</span>
									<?php endif; ?>
								</div>
							<?php else : ?>
								<span class="azonmate-showcase__na">&mdash;</span>
							<?php endif; ?>
						</td>
					<?php endforeach; ?>
				</tr>
				<?php endif; ?>

				<!-- Price Row -->
				<?php if ( $show_price ) : ?>
				<tr class="azonmate-showcase__tr--price">
					<?php foreach ( $products as $product ) :
						$price      = $product->get_price();
						$list_price = $product->get_list_price();
						$savings    = $product->get_savings_percentage();
						$is_prime   = $product->is_prime();
						$badge      = $product->get_badge_label();
						$has_badge  = $show_badge && ! empty( $badge );
						$cell_cls   = $has_badge ? ' azonmate-showcase__td--highlight' : '';
						?>
						<td class="azonmate-showcase__td<?php echo esc_attr( $cell_cls ); ?>">
							<?php if ( $price ) : ?>
								<span class="azonmate-showcase__price azonmate-showcase__price--large"><?php echo esc_html( $price ); ?></span>
								<?php if ( $list_price && $list_price !== $price ) : ?>
									<br><span class="azonmate-showcase__old-price"><del><?php echo esc_html( $list_price ); ?></del></span>
								<?php endif; ?>
								<?php if ( $savings ) : ?>
									<span class="azonmate-showcase__savings azonmate-showcase__savings--inline">-<?php echo esc_html( $savings ); ?>%</span>
								<?php endif; ?>
								<?php if ( $is_prime && function_exists( 'azon_mate_render_prime_badge' ) ) : ?>
									<div class="azonmate-showcase__prime">
										<?php echo azon_mate_render_prime_badge(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									</div>
								<?php endif; ?>
							<?php else : ?>
								<span class="azonmate-showcase__na">&mdash;</span>
							<?php endif; ?>
						</td>
					<?php endforeach; ?>
				</tr>
				<?php endif; ?>

				<!-- Button Row -->
				<?php if ( $show_button ) : ?>
				<tr class="azonmate-showcase__tr--action">
					<?php foreach ( $products as $product ) :
						$url        = $product->get_detail_page_url();
						$asin       = $product->get_asin();
						$btn_text   = $product->get_button_text();
						$badge      = $product->get_badge_label();
						$has_badge  = $show_badge && ! empty( $badge );
						$cell_cls   = $has_badge ? ' azonmate-showcase__td--highlight' : '';

						$final_btn_text = ! empty( $btn_text ) ? $btn_text : ( ! empty( $button_text ) ? $button_text : get_option( 'azon_mate_buy_button_text', 'Buy on Amazon' ) );
						?>
						<td class="azonmate-showcase__td<?php echo esc_attr( $cell_cls ); ?>">
							<?php
							if ( function_exists( 'azon_mate_render_buy_button' ) ) {
								echo azon_mate_render_buy_button( $url, $final_btn_text, $asin ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
							?>
						</td>
					<?php endforeach; ?>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</div>

	<?php if ( function_exists( 'azon_mate_render_disclosure' ) ) {
		echo azon_mate_render_disclosure(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	} ?>
</div>
