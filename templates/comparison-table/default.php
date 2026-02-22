<?php
/**
 * Template: Comparison Table – Default
 *
 * @package AzonMate
 * @since   1.0.0
 *
 * @var \AzonMate\Models\Product[] $products       Array of products.
 * @var array                      $columns        Columns to display.
 * @var string                     $highlight_asin ASIN to highlight as "Best Pick".
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( empty( $products ) ) {
	return;
}

$button_text = get_option( 'azon_mate_button_text', __( 'View on Amazon', 'azonmate' ) );
?>
<div class="azonmate-comparison-table-wrap">
	<table class="azonmate-comparison-table">
		<thead>
			<tr>
				<?php foreach ( $products as $product ) :
					$asin       = $product->get_asin();
					$is_highlight = ( $asin === $highlight_asin );
					?>
					<th class="azonmate-comparison-table__header<?php echo $is_highlight ? ' azonmate-comparison-table__header--highlight' : ''; ?>">
						<?php if ( $is_highlight ) : ?>
							<span class="azonmate-comparison-table__badge"><?php esc_html_e( 'Best Pick', 'azonmate' ); ?></span>
						<?php endif; ?>
					</th>
				<?php endforeach; ?>
			</tr>
		</thead>
		<tbody>
			<?php if ( in_array( 'image', $columns, true ) ) : ?>
				<tr class="azonmate-comparison-table__row azonmate-comparison-table__row--image">
					<?php foreach ( $products as $product ) :
						$url        = $product->get_detail_page_url();
						$asin       = $product->get_asin();
						$link_attrs = function_exists( 'azon_mate_link_attributes' ) ? azon_mate_link_attributes( $url, $asin ) : '';
						?>
						<td class="azonmate-comparison-table__cell">
							<?php if ( $product->get_image_url() ) : ?>
								<a <?php echo $link_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
									<img
										src="<?php echo esc_url( $product->get_image_url() ); ?>"
										alt="<?php echo esc_attr( $product->get_title() ); ?>"
										class="azonmate-comparison-table__image"
										loading="lazy"
									/>
								</a>
							<?php endif; ?>
						</td>
					<?php endforeach; ?>
				</tr>
			<?php endif; ?>

			<?php if ( in_array( 'title', $columns, true ) ) : ?>
				<tr class="azonmate-comparison-table__row azonmate-comparison-table__row--title">
					<?php foreach ( $products as $product ) :
						$url        = $product->get_detail_page_url();
						$asin       = $product->get_asin();
						$link_attrs = function_exists( 'azon_mate_link_attributes' ) ? azon_mate_link_attributes( $url, $asin ) : '';
						?>
						<td class="azonmate-comparison-table__cell">
							<a <?php echo $link_attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
								<?php echo esc_html( $product->get_title() ); ?>
							</a>
						</td>
					<?php endforeach; ?>
				</tr>
			<?php endif; ?>

			<?php if ( in_array( 'price', $columns, true ) ) : ?>
				<tr class="azonmate-comparison-table__row azonmate-comparison-table__row--price">
					<?php foreach ( $products as $product ) : ?>
						<td class="azonmate-comparison-table__cell">
							<span class="azonmate-comparison-table__price">
								<?php echo esc_html( $product->get_price() ?: '—' ); ?>
							</span>
						</td>
					<?php endforeach; ?>
				</tr>
			<?php endif; ?>

			<?php if ( in_array( 'old_price', $columns, true ) ) : ?>
				<tr class="azonmate-comparison-table__row azonmate-comparison-table__row--old-price">
					<?php foreach ( $products as $product ) : ?>
						<td class="azonmate-comparison-table__cell">
							<?php if ( $product->get_list_price() ) : ?>
								<del><?php echo esc_html( $product->get_list_price() ); ?></del>
							<?php else : ?>
								<span>—</span>
							<?php endif; ?>
						</td>
					<?php endforeach; ?>
				</tr>
			<?php endif; ?>

			<?php if ( in_array( 'rating', $columns, true ) ) : ?>
				<tr class="azonmate-comparison-table__row azonmate-comparison-table__row--rating">
					<?php foreach ( $products as $product ) : ?>
						<td class="azonmate-comparison-table__cell">
							<?php if ( $product->get_rating() ) : ?>
								<?php
								if ( function_exists( 'azon_mate_render_stars' ) ) {
									echo azon_mate_render_stars( $product->get_rating() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								}
								?>
								<?php if ( $product->get_review_count() ) : ?>
									<span class="azonmate-comparison-table__reviews">(<?php echo esc_html( number_format_i18n( $product->get_review_count() ) ); ?>)</span>
								<?php endif; ?>
							<?php else : ?>
								<span>—</span>
							<?php endif; ?>
						</td>
					<?php endforeach; ?>
				</tr>
			<?php endif; ?>

			<?php if ( in_array( 'prime', $columns, true ) ) : ?>
				<tr class="azonmate-comparison-table__row azonmate-comparison-table__row--prime">
					<?php foreach ( $products as $product ) : ?>
						<td class="azonmate-comparison-table__cell">
							<?php if ( $product->is_prime() && function_exists( 'azon_mate_render_prime_badge' ) ) : ?>
								<?php echo azon_mate_render_prime_badge(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<?php else : ?>
								<span>—</span>
							<?php endif; ?>
						</td>
					<?php endforeach; ?>
				</tr>
			<?php endif; ?>

			<?php if ( in_array( 'features', $columns, true ) ) : ?>
				<tr class="azonmate-comparison-table__row azonmate-comparison-table__row--features">
					<?php foreach ( $products as $product ) : ?>
						<td class="azonmate-comparison-table__cell">
							<?php
							$features = $product->get_features();
							if ( ! empty( $features ) ) :
								?>
								<ul class="azonmate-comparison-table__features">
									<?php foreach ( array_slice( $features, 0, 5 ) as $feature ) : ?>
										<li><?php echo esc_html( $feature ); ?></li>
									<?php endforeach; ?>
								</ul>
							<?php else : ?>
								<span>—</span>
							<?php endif; ?>
						</td>
					<?php endforeach; ?>
				</tr>
			<?php endif; ?>

			<?php if ( in_array( 'button', $columns, true ) ) : ?>
				<tr class="azonmate-comparison-table__row azonmate-comparison-table__row--button">
					<?php foreach ( $products as $product ) : ?>
						<td class="azonmate-comparison-table__cell">
							<?php
							if ( function_exists( 'azon_mate_render_buy_button' ) ) {
								echo azon_mate_render_buy_button(
									$product->get_detail_page_url(),
									$button_text,
									$product->get_asin()
								); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
							?>
						</td>
					<?php endforeach; ?>
				</tr>
			<?php endif; ?>
		</tbody>
	</table>
</div>
