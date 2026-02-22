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

// Calculate daily average.
$daily_avg = ! empty( $clicks_per_day ) ? round( $total_clicks / count( $clicks_per_day ), 1 ) : 0;

// Find peak day.
$peak_clicks = 0;
$peak_date   = '';
foreach ( $clicks_per_day as $row ) {
	if ( (int) $row->clicks > $peak_clicks ) {
		$peak_clicks = (int) $row->clicks;
		$peak_date   = $row->date;
	}
}

// Max clicks for chart scale.
$max_clicks = max( $peak_clicks, 1 );

if ( ! function_exists( 'azonmate_render_admin_header' ) ) {
	require_once __DIR__ . '/partials/admin-bar.php';
}
?>

<div class="wrap azonmate-dash">
	<?php azonmate_render_admin_header(); ?>

	<!-- Page Hero Header -->
	<div class="azonmate-page-hero">
		<div class="azonmate-page-hero__icon">
			<span class="dashicons dashicons-chart-bar"></span>
		</div>
		<div class="azonmate-page-hero__content">
			<h1><?php esc_html_e( 'Analytics Dashboard', 'azonmate' ); ?></h1>
			<p>
				<?php
				printf(
					/* translators: %d: Number of days */
					esc_html__( 'Click performance overview for the last %d days.', 'azonmate' ),
					$days
				);
				?>
			</p>
		</div>
	</div>

	<!-- Dashboard Controls -->
	<div class="azonmate-dash__header">
		<div class="azonmate-dash__header-right">
			<div class="azonmate-dash__range-pills">
				<?php foreach ( $allowed_days as $d ) : ?>
					<a href="<?php echo esc_url( add_query_arg( 'days', $d ) ); ?>"
					   class="azonmate-dash__pill <?php echo $days === $d ? 'azonmate-dash__pill--active' : ''; ?>">
						<?php printf( esc_html__( '%dd', 'azonmate' ), $d ); ?>
					</a>
				<?php endforeach; ?>
			</div>
			<a href="<?php echo esc_url( admin_url( 'admin-ajax.php?action=azon_mate_export_csv&nonce=' . wp_create_nonce( 'azon_mate_admin' ) . '&days=' . $days ) ); ?>"
			   class="azonmate-dash__export-btn">
				<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px; margin-right: 4px;">
					<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
					<polyline points="7 10 12 15 17 10"></polyline>
					<line x1="12" y1="15" x2="12" y2="3"></line>
				</svg>
				<?php esc_html_e( 'Export CSV', 'azonmate' ); ?>
			</a>
		</div>
	</div>

	<!-- Stat Cards -->
	<div class="azonmate-dash__cards">
		<div class="azonmate-dash__card azonmate-dash__card--clicks">
			<div class="azonmate-dash__card-icon">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
					<polyline points="10 17 15 12 10 7"></polyline>
					<line x1="15" y1="12" x2="3" y2="12"></line>
				</svg>
			</div>
			<div class="azonmate-dash__card-body">
				<span class="azonmate-dash__card-value"><?php echo number_format_i18n( $total_clicks ); ?></span>
				<span class="azonmate-dash__card-label"><?php esc_html_e( 'Total Clicks', 'azonmate' ); ?></span>
			</div>
		</div>

		<div class="azonmate-dash__card azonmate-dash__card--avg">
			<div class="azonmate-dash__card-icon">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
				</svg>
			</div>
			<div class="azonmate-dash__card-body">
				<span class="azonmate-dash__card-value"><?php echo esc_html( $daily_avg ); ?></span>
				<span class="azonmate-dash__card-label"><?php esc_html_e( 'Daily Average', 'azonmate' ); ?></span>
			</div>
		</div>

		<div class="azonmate-dash__card azonmate-dash__card--products">
			<div class="azonmate-dash__card-icon">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
					<path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
				</svg>
			</div>
			<div class="azonmate-dash__card-body">
				<span class="azonmate-dash__card-value"><?php echo count( $top_products ); ?></span>
				<span class="azonmate-dash__card-label"><?php esc_html_e( 'Products Clicked', 'azonmate' ); ?></span>
			</div>
		</div>

		<div class="azonmate-dash__card azonmate-dash__card--posts">
			<div class="azonmate-dash__card-icon">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
					<polyline points="14 2 14 8 20 8"></polyline>
					<line x1="16" y1="13" x2="8" y2="13"></line>
					<line x1="16" y1="17" x2="8" y2="17"></line>
				</svg>
			</div>
			<div class="azonmate-dash__card-body">
				<span class="azonmate-dash__card-value"><?php echo count( $top_posts ); ?></span>
				<span class="azonmate-dash__card-label"><?php esc_html_e( 'Posts with Clicks', 'azonmate' ); ?></span>
			</div>
		</div>

		<div class="azonmate-dash__card azonmate-dash__card--peak">
			<div class="azonmate-dash__card-icon">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
				</svg>
			</div>
			<div class="azonmate-dash__card-body">
				<span class="azonmate-dash__card-value"><?php echo absint( $peak_clicks ); ?></span>
				<span class="azonmate-dash__card-label"><?php esc_html_e( 'Peak Day Clicks', 'azonmate' ); ?></span>
			</div>
		</div>
	</div>

	<!-- Click Trend Chart -->
	<div class="azonmate-dash__panel">
		<div class="azonmate-dash__panel-header">
			<h2 class="azonmate-dash__panel-title">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px; margin-right: 4px;">
					<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
				</svg>
				<?php esc_html_e( 'Click Trend', 'azonmate' ); ?>
			</h2>
			<?php if ( $peak_date ) : ?>
				<span class="azonmate-dash__panel-meta">
					<?php
					printf(
						/* translators: %s: date string */
						esc_html__( 'Peak: %s', 'azonmate' ),
						esc_html( date_i18n( get_option( 'date_format' ), strtotime( $peak_date ) ) )
					);
					?>
				</span>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $clicks_per_day ) ) : ?>
			<div class="azonmate-dash__chart">
				<?php foreach ( $clicks_per_day as $row ) :
					$pct   = ( (int) $row->clicks / $max_clicks ) * 100;
					$label = date_i18n( 'M j', strtotime( $row->date ) );
				?>
					<div class="azonmate-dash__chart-col">
						<span class="azonmate-dash__chart-val"><?php echo absint( $row->clicks ); ?></span>
						<div class="azonmate-dash__chart-bar" style="height:<?php echo esc_attr( max( $pct, 2 ) ); ?>%"></div>
						<span class="azonmate-dash__chart-label"><?php echo esc_html( $label ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
		<?php else : ?>
			<div class="azonmate-dash__empty">
				<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="#ccc" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
					<line x1="18" y1="20" x2="18" y2="10"></line>
					<line x1="12" y1="20" x2="12" y2="4"></line>
					<line x1="6" y1="20" x2="6" y2="14"></line>
				</svg>
				<p><?php esc_html_e( 'No click data for this period.', 'azonmate' ); ?></p>
			</div>
		<?php endif; ?>
	</div>

	<!-- Two-Column: Top Products + Top Posts -->
	<div class="azonmate-dash__grid-2col">

		<!-- Top Products -->
		<div class="azonmate-dash__panel">
			<div class="azonmate-dash__panel-header">
				<h2 class="azonmate-dash__panel-title">
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px; margin-right: 4px;">
						<circle cx="9" cy="21" r="1"></circle>
						<circle cx="20" cy="21" r="1"></circle>
						<path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
					</svg>
					<?php esc_html_e( 'Top Products', 'azonmate' ); ?>
				</h2>
				<span class="azonmate-dash__panel-badge"><?php echo count( $top_products ); ?></span>
			</div>

			<?php if ( ! empty( $top_products ) ) : ?>
				<?php
				$product_max = 1;
				foreach ( $top_products as $p ) {
					if ( (int) $p->click_count > $product_max ) {
						$product_max = (int) $p->click_count;
					}
				}
				?>
				<div class="azonmate-dash__table-wrap">
					<table class="azonmate-dash__table">
						<thead>
							<tr>
								<th class="azonmate-dash__th--rank">#</th>
								<th><?php esc_html_e( 'ASIN', 'azonmate' ); ?></th>
								<th class="azonmate-dash__th--bar"><?php esc_html_e( 'Performance', 'azonmate' ); ?></th>
								<th class="azonmate-dash__th--num"><?php esc_html_e( 'Clicks', 'azonmate' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $top_products as $index => $item ) :
								$p_pct = ( (int) $item->click_count / $product_max ) * 100;
							?>
								<tr>
									<td class="azonmate-dash__td--rank">
										<?php if ( $index < 3 ) : ?>
											<span class="azonmate-dash__rank azonmate-dash__rank--<?php echo absint( $index + 1 ); ?>"><?php echo absint( $index + 1 ); ?></span>
										<?php else : ?>
											<?php echo absint( $index + 1 ); ?>
										<?php endif; ?>
									</td>
									<td>
										<code class="azonmate-dash__asin"><?php echo esc_html( $item->asin ); ?></code>
									</td>
									<td class="azonmate-dash__td--bar">
										<div class="azonmate-dash__progress">
											<div class="azonmate-dash__progress-fill" style="width:<?php echo esc_attr( $p_pct ); ?>%"></div>
										</div>
									</td>
									<td class="azonmate-dash__td--num">
										<strong><?php echo number_format_i18n( $item->click_count ); ?></strong>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			<?php else : ?>
				<div class="azonmate-dash__empty">
					<p><?php esc_html_e( 'No product click data.', 'azonmate' ); ?></p>
				</div>
			<?php endif; ?>
		</div>

		<!-- Top Posts -->
		<div class="azonmate-dash__panel">
			<div class="azonmate-dash__panel-header">
				<h2 class="azonmate-dash__panel-title">
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px; margin-right: 4px;">
						<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
						<polyline points="14 2 14 8 20 8"></polyline>
					</svg>
					<?php esc_html_e( 'Top Posts', 'azonmate' ); ?>
				</h2>
				<span class="azonmate-dash__panel-badge"><?php echo count( $top_posts ); ?></span>
			</div>

			<?php if ( ! empty( $top_posts ) ) : ?>
				<?php
				$post_max = 1;
				foreach ( $top_posts as $p ) {
					if ( (int) $p->click_count > $post_max ) {
						$post_max = (int) $p->click_count;
					}
				}
				?>
				<div class="azonmate-dash__table-wrap">
					<table class="azonmate-dash__table">
						<thead>
							<tr>
								<th class="azonmate-dash__th--rank">#</th>
								<th><?php esc_html_e( 'Post', 'azonmate' ); ?></th>
								<th class="azonmate-dash__th--bar"><?php esc_html_e( 'Performance', 'azonmate' ); ?></th>
								<th class="azonmate-dash__th--num"><?php esc_html_e( 'Clicks', 'azonmate' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $top_posts as $index => $item ) :
								$post_title = get_the_title( $item->post_id );
								$pp_pct     = ( (int) $item->click_count / $post_max ) * 100;
							?>
								<tr>
									<td class="azonmate-dash__td--rank">
										<?php if ( $index < 3 ) : ?>
											<span class="azonmate-dash__rank azonmate-dash__rank--<?php echo absint( $index + 1 ); ?>"><?php echo absint( $index + 1 ); ?></span>
										<?php else : ?>
											<?php echo absint( $index + 1 ); ?>
										<?php endif; ?>
									</td>
									<td>
										<a href="<?php echo esc_url( get_edit_post_link( $item->post_id ) ); ?>" class="azonmate-dash__post-link">
											<?php echo esc_html( $post_title ? $post_title : '#' . $item->post_id ); ?>
										</a>
									</td>
									<td class="azonmate-dash__td--bar">
										<div class="azonmate-dash__progress">
											<div class="azonmate-dash__progress-fill azonmate-dash__progress-fill--blue" style="width:<?php echo esc_attr( $pp_pct ); ?>%"></div>
										</div>
									</td>
									<td class="azonmate-dash__td--num">
										<strong><?php echo number_format_i18n( $item->click_count ); ?></strong>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			<?php else : ?>
				<div class="azonmate-dash__empty">
					<p><?php esc_html_e( 'No post click data.', 'azonmate' ); ?></p>
				</div>
			<?php endif; ?>
		</div>

	</div>
	<?php azonmate_render_admin_footer(); ?>
</div>
