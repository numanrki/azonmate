<?php
/**
 * Analytics page HTML template.
 *
 * @package AzonMate\Admin\Views
 * @since   1.0.0
 */

// Abort if this file is called directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$analytics    = new \AzonMate\Admin\Analytics();
$days         = isset( $_GET['days'] ) ? absint( $_GET['days'] ) : 30;
$allowed_days = array( 7, 30, 90 );
if ( ! in_array( $days, $allowed_days, true ) ) {
	$days = 30;
}

$total_clicks   = $analytics->get_click_count( $days );
$top_products   = $analytics->get_top_products( $days, 20 );
$top_posts      = $analytics->get_top_posts( $days, 20 );
$clicks_per_day = $analytics->get_clicks_per_day( $days );
?>

<div class="wrap azonmate-analytics">
	<h1>
		<span class="dashicons dashicons-chart-bar"></span>
		<?php esc_html_e( 'AzonMate Analytics', 'azonmate' ); ?>
	</h1>

	<!-- Date Range Filter -->
	<div class="azonmate-analytics__filters">
		<label><?php esc_html_e( 'Date Range:', 'azonmate' ); ?></label>
		<?php foreach ( $allowed_days as $d ) : ?>
			<a href="<?php echo esc_url( add_query_arg( 'days', $d ) ); ?>"
			   class="button <?php echo $days === $d ? 'button-primary' : ''; ?>">
				<?php
				printf(
					/* translators: %d: Number of days */
					esc_html__( 'Last %d Days', 'azonmate' ),
					$d
				);
				?>
			</a>
		<?php endforeach; ?>

		<a href="<?php echo esc_url( admin_url( 'admin-ajax.php?action=azon_mate_export_csv&nonce=' . wp_create_nonce( 'azon_mate_admin' ) . '&days=' . $days ) ); ?>"
		   class="button" style="margin-left: 10px;">
			<?php esc_html_e( 'Export CSV', 'azonmate' ); ?>
		</a>
	</div>

	<!-- Overview Stats -->
	<div class="azonmate-analytics__overview">
		<div class="azonmate-analytics__stat-box">
			<h3><?php echo absint( $total_clicks ); ?></h3>
			<p>
				<?php
				printf(
					/* translators: %d: Number of days */
					esc_html__( 'Total Clicks (%d Days)', 'azonmate' ),
					$days
				);
				?>
			</p>
		</div>
		<div class="azonmate-analytics__stat-box">
			<h3><?php echo count( $top_products ); ?></h3>
			<p><?php esc_html_e( 'Unique Products Clicked', 'azonmate' ); ?></p>
		</div>
		<div class="azonmate-analytics__stat-box">
			<h3><?php echo count( $top_posts ); ?></h3>
			<p><?php esc_html_e( 'Posts with Clicks', 'azonmate' ); ?></p>
		</div>
	</div>

	<!-- Clicks Per Day Chart (Simple Table) -->
	<div class="azonmate-analytics__section">
		<h2><?php esc_html_e( 'Clicks Per Day', 'azonmate' ); ?></h2>
		<?php if ( ! empty( $clicks_per_day ) ) : ?>
			<div class="azonmate-analytics__chart">
				<table class="widefat striped">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Date', 'azonmate' ); ?></th>
							<th><?php esc_html_e( 'Clicks', 'azonmate' ); ?></th>
							<th><?php esc_html_e( 'Bar', 'azonmate' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$max_clicks = 1;
						foreach ( $clicks_per_day as $row ) {
							if ( (int) $row->clicks > $max_clicks ) {
								$max_clicks = (int) $row->clicks;
							}
						}
						?>
						<?php foreach ( $clicks_per_day as $row ) : ?>
							<?php $percentage = ( (int) $row->clicks / $max_clicks ) * 100; ?>
							<tr>
								<td><?php echo esc_html( $row->date ); ?></td>
								<td><strong><?php echo absint( $row->clicks ); ?></strong></td>
								<td>
									<div class="azonmate-analytics__bar" style="width: <?php echo esc_attr( $percentage ); ?>%;"></div>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php else : ?>
			<p><?php esc_html_e( 'No click data for this period.', 'azonmate' ); ?></p>
		<?php endif; ?>
	</div>

	<div class="azonmate-analytics__columns">
		<!-- Top Products -->
		<div class="azonmate-analytics__section">
			<h2><?php esc_html_e( 'Top Products', 'azonmate' ); ?></h2>
			<?php if ( ! empty( $top_products ) ) : ?>
				<table class="widefat striped">
					<thead>
						<tr>
							<th>#</th>
							<th><?php esc_html_e( 'ASIN', 'azonmate' ); ?></th>
							<th><?php esc_html_e( 'Clicks', 'azonmate' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $top_products as $index => $item ) : ?>
							<tr>
								<td><?php echo absint( $index + 1 ); ?></td>
								<td><code><?php echo esc_html( $item->asin ); ?></code></td>
								<td><strong><?php echo absint( $item->click_count ); ?></strong></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php else : ?>
				<p><?php esc_html_e( 'No product click data.', 'azonmate' ); ?></p>
			<?php endif; ?>
		</div>

		<!-- Top Posts -->
		<div class="azonmate-analytics__section">
			<h2><?php esc_html_e( 'Top Posts', 'azonmate' ); ?></h2>
			<?php if ( ! empty( $top_posts ) ) : ?>
				<table class="widefat striped">
					<thead>
						<tr>
							<th>#</th>
							<th><?php esc_html_e( 'Post', 'azonmate' ); ?></th>
							<th><?php esc_html_e( 'Clicks', 'azonmate' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $top_posts as $index => $item ) : ?>
							<?php $post_title = get_the_title( $item->post_id ); ?>
							<tr>
								<td><?php echo absint( $index + 1 ); ?></td>
								<td>
									<a href="<?php echo esc_url( get_edit_post_link( $item->post_id ) ); ?>">
										<?php echo esc_html( $post_title ? $post_title : '#' . $item->post_id ); ?>
									</a>
								</td>
								<td><strong><?php echo absint( $item->click_count ); ?></strong></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php else : ?>
				<p><?php esc_html_e( 'No post click data.', 'azonmate' ); ?></p>
			<?php endif; ?>
		</div>
	</div>
</div>
