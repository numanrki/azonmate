<?php
/**
 * Settings page HTML template.
 *
 * @package AzonMate\Admin\Views
 * @since   1.0.0
 */

// Abort if this file is called directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$active_tab = isset( $_GET['tab'] ) ? sanitize_key( $_GET['tab'] ) : 'api';
$tabs = array(
	'api'      => __( 'API Configuration', 'azonmate' ),
	'display'  => __( 'Display Settings', 'azonmate' ),
	'cache'    => __( 'Cache Settings', 'azonmate' ),
	'geo'      => __( 'Geo-Targeting', 'azonmate' ),
	'tracking' => __( 'Tracking', 'azonmate' ),
	'advanced' => __( 'Advanced', 'azonmate' ),
);

$marketplaces = \AzonMate\API\Marketplace::get_all_options();
$current_marketplace = get_option( 'azon_mate_marketplace', 'US' );
?>

<div class="wrap azonmate-settings">
	<h1>
		<span class="dashicons dashicons-amazon" style="margin-right: 8px;"></span>
		<?php esc_html_e( 'AzonMate Settings', 'azonmate' ); ?>
		<span class="azonmate-version"><?php echo esc_html( 'v' . AZON_MATE_VERSION ); ?></span>
	</h1>

	<!-- Tabs Navigation -->
	<nav class="nav-tab-wrapper">
		<?php foreach ( $tabs as $tab_key => $tab_label ) : ?>
			<a href="<?php echo esc_url( add_query_arg( 'tab', $tab_key, admin_url( 'admin.php?page=azonmate' ) ) ); ?>"
			   class="nav-tab <?php echo $active_tab === $tab_key ? 'nav-tab-active' : ''; ?>">
				<?php echo esc_html( $tab_label ); ?>
			</a>
		<?php endforeach; ?>
	</nav>

	<div class="azonmate-settings-content">

		<?php if ( 'api' === $active_tab ) : ?>
			<!-- API Configuration Tab -->
			<form method="post" action="options.php">
				<?php settings_fields( 'azon_mate_api_settings' ); ?>

				<table class="form-table" role="presentation">
					<tr>
						<th scope="row">
							<label for="azon_mate_access_key"><?php esc_html_e( 'Access Key', 'azonmate' ); ?></label>
						</th>
						<td>
							<?php $has_access_key = ! empty( get_option( 'azon_mate_access_key', '' ) ); ?>
							<input type="text" id="azon_mate_access_key" name="azon_mate_access_key"
								   value="" class="regular-text"
								   placeholder="<?php echo $has_access_key ? esc_attr( '••••••••••••  (saved)' ) : esc_attr__( 'Enter your Amazon PA-API Access Key', 'azonmate' ); ?>"
								   autocomplete="off" />
							<?php if ( $has_access_key ) : ?>
								<p class="description" style="color: #00a32a;">&#10003; <?php esc_html_e( 'Key is stored encrypted. Leave blank to keep current key, or enter a new value to replace it.', 'azonmate' ); ?></p>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="azon_mate_secret_key"><?php esc_html_e( 'Secret Key', 'azonmate' ); ?></label>
						</th>
						<td>
							<?php $has_secret_key = ! empty( get_option( 'azon_mate_secret_key', '' ) ); ?>
							<input type="password" id="azon_mate_secret_key" name="azon_mate_secret_key"
								   value="" class="regular-text"
								   placeholder="<?php echo $has_secret_key ? esc_attr( '••••••••••••  (saved)' ) : esc_attr__( 'Enter your Amazon PA-API Secret Key', 'azonmate' ); ?>"
								   autocomplete="off" />
							<?php if ( $has_secret_key ) : ?>
								<p class="description" style="color: #00a32a;">&#10003; <?php esc_html_e( 'Key is stored encrypted. Leave blank to keep current key, or enter a new value to replace it.', 'azonmate' ); ?></p>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="azon_mate_partner_tag"><?php esc_html_e( 'Partner/Affiliate Tag', 'azonmate' ); ?></label>
						</th>
						<td>
							<input type="text" id="azon_mate_partner_tag" name="azon_mate_partner_tag"
								   value="<?php echo esc_attr( get_option( 'azon_mate_partner_tag', '' ) ); ?>"
								   class="regular-text"
								   placeholder="<?php esc_attr_e( 'e.g., mysite-20', 'azonmate' ); ?>" />
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="azon_mate_marketplace"><?php esc_html_e( 'Default Marketplace', 'azonmate' ); ?></label>
						</th>
						<td>
							<select id="azon_mate_marketplace" name="azon_mate_marketplace">
								<?php foreach ( $marketplaces as $code => $label ) : ?>
									<option value="<?php echo esc_attr( $code ); ?>"
										<?php selected( $current_marketplace, $code ); ?>>
										<?php echo esc_html( $label ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
				</table>

				<?php submit_button(); ?>

				<hr />

				<h3><?php esc_html_e( 'Test Connection', 'azonmate' ); ?></h3>
				<p class="description"><?php esc_html_e( 'Click the button below to verify your API credentials with a sample request.', 'azonmate' ); ?></p>
				<button type="button" id="azonmate-test-connection" class="button button-secondary">
					<?php esc_html_e( 'Test Connection', 'azonmate' ); ?>
				</button>
				<span id="azonmate-test-result" class="azonmate-test-result"></span>
			</form>

		<?php elseif ( 'display' === $active_tab ) : ?>
			<!-- Display Settings Tab -->
			<form method="post" action="options.php">
				<?php settings_fields( 'azon_mate_display_settings' ); ?>

				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Default Template', 'azonmate' ); ?></th>
						<td>
							<select name="azon_mate_default_template">
								<option value="default" <?php selected( get_option( 'azon_mate_default_template', 'default' ), 'default' ); ?>>
									<?php esc_html_e( 'Product Box (Default)', 'azonmate' ); ?>
								</option>
								<option value="horizontal" <?php selected( get_option( 'azon_mate_default_template', 'default' ), 'horizontal' ); ?>>
									<?php esc_html_e( 'Horizontal', 'azonmate' ); ?>
								</option>
								<option value="compact" <?php selected( get_option( 'azon_mate_default_template', 'default' ), 'compact' ); ?>>
									<?php esc_html_e( 'Compact', 'azonmate' ); ?>
								</option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Show/Hide Elements', 'azonmate' ); ?></th>
						<td>
							<fieldset>
								<label>
									<input type="checkbox" name="azon_mate_show_prices" value="1"
										<?php checked( get_option( 'azon_mate_show_prices', '1' ), '1' ); ?> />
									<?php esc_html_e( 'Show prices', 'azonmate' ); ?>
								</label><br />
								<label>
									<input type="checkbox" name="azon_mate_show_ratings" value="1"
										<?php checked( get_option( 'azon_mate_show_ratings', '1' ), '1' ); ?> />
									<?php esc_html_e( 'Show star ratings', 'azonmate' ); ?>
								</label><br />
								<label>
									<input type="checkbox" name="azon_mate_show_prime_badge" value="1"
										<?php checked( get_option( 'azon_mate_show_prime_badge', '1' ), '1' ); ?> />
									<?php esc_html_e( 'Show Prime badge', 'azonmate' ); ?>
								</label><br />
								<label>
									<input type="checkbox" name="azon_mate_show_description" value="1"
										<?php checked( get_option( 'azon_mate_show_description', '1' ), '1' ); ?> />
									<?php esc_html_e( 'Show product description/features', 'azonmate' ); ?>
								</label><br />
								<label>
									<input type="checkbox" name="azon_mate_show_buy_button" value="1"
										<?php checked( get_option( 'azon_mate_show_buy_button', '1' ), '1' ); ?> />
									<?php esc_html_e( 'Show buy button', 'azonmate' ); ?>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="azon_mate_buy_button_text"><?php esc_html_e( 'Buy Button Text', 'azonmate' ); ?></label>
						</th>
						<td>
							<input type="text" id="azon_mate_buy_button_text" name="azon_mate_buy_button_text"
								   value="<?php echo esc_attr( get_option( 'azon_mate_buy_button_text', __( 'Buy on Amazon', 'azonmate' ) ) ); ?>"
								   class="regular-text" />
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="azon_mate_buy_button_color"><?php esc_html_e( 'Buy Button Color', 'azonmate' ); ?></label>
						</th>
						<td>
							<input type="text" id="azon_mate_buy_button_color" name="azon_mate_buy_button_color"
								   value="<?php echo esc_attr( get_option( 'azon_mate_buy_button_color', '#FF9900' ) ); ?>"
								   class="azonmate-color-picker" />
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Link Behavior', 'azonmate' ); ?></th>
						<td>
							<fieldset>
								<label>
									<input type="checkbox" name="azon_mate_open_new_tab" value="1"
										<?php checked( get_option( 'azon_mate_open_new_tab', '1' ), '1' ); ?> />
									<?php esc_html_e( 'Open links in new tab', 'azonmate' ); ?>
								</label><br />
								<label>
									<input type="checkbox" name="azon_mate_nofollow_links" value="1"
										<?php checked( get_option( 'azon_mate_nofollow_links', '1' ), '1' ); ?> />
									<?php esc_html_e( 'Add rel="nofollow sponsored" to links', 'azonmate' ); ?>
								</label>
							</fieldset>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Affiliate Disclosure', 'azonmate' ); ?></th>
						<td>
							<fieldset>
								<label>
									<input type="checkbox" name="azon_mate_show_disclosure" value="1"
										<?php checked( get_option( 'azon_mate_show_disclosure', '1' ), '1' ); ?> />
									<?php esc_html_e( 'Show "As an Amazon Associate, I earn from qualifying purchases." in showcase blocks', 'azonmate' ); ?>
								</label>
							</fieldset>
							<p class="description"><?php esc_html_e( 'Required by the Amazon Associates program. Displays once per showcase.', 'azonmate' ); ?></p>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="azon_mate_custom_css"><?php esc_html_e( 'Custom CSS', 'azonmate' ); ?></label>
						</th>
						<td>
							<textarea id="azon_mate_custom_css" name="azon_mate_custom_css"
									  rows="8" class="large-text code"><?php echo esc_textarea( get_option( 'azon_mate_custom_css', '' ) ); ?></textarea>
							<p class="description"><?php esc_html_e( 'Add custom CSS to override default styles.', 'azonmate' ); ?></p>
						</td>
					</tr>
				</table>

				<?php submit_button(); ?>
			</form>

		<?php elseif ( 'cache' === $active_tab ) : ?>
			<!-- Cache Settings Tab -->
			<form method="post" action="options.php">
				<?php settings_fields( 'azon_mate_cache_settings' ); ?>

				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable Caching', 'azonmate' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="azon_mate_cache_enabled" value="1"
									<?php checked( get_option( 'azon_mate_cache_enabled', '1' ), '1' ); ?> />
								<?php esc_html_e( 'Enable product data caching', 'azonmate' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="azon_mate_cache_duration"><?php esc_html_e( 'Cache Duration', 'azonmate' ); ?></label>
						</th>
						<td>
							<input type="number" id="azon_mate_cache_duration" name="azon_mate_cache_duration"
								   value="<?php echo esc_attr( get_option( 'azon_mate_cache_duration', 24 ) ); ?>"
								   min="1" max="168" step="1" class="small-text" />
							<span class="description"><?php esc_html_e( 'hours (max 24 recommended by Amazon policy)', 'azonmate' ); ?></span>
						</td>
					</tr>
				</table>

				<?php submit_button(); ?>
			</form>

			<hr />

			<h3><?php esc_html_e( 'Clear Cache', 'azonmate' ); ?></h3>
			<?php
			$cache = new \AzonMate\Cache\CacheManager();
			$total = $cache->get_total_cached();
			?>
			<p>
				<?php
				printf(
					/* translators: %d: number of cached products */
					esc_html__( 'Currently caching %d products.', 'azonmate' ),
					absint( $total )
				);
				?>
			</p>
			<button type="button" id="azonmate-clear-cache" class="button button-secondary">
				<?php esc_html_e( 'Clear All Cache', 'azonmate' ); ?>
			</button>
			<span id="azonmate-cache-result" class="azonmate-test-result"></span>

		<?php elseif ( 'geo' === $active_tab ) : ?>
			<!-- Geo-Targeting Tab -->
			<form method="post" action="options.php">
				<?php settings_fields( 'azon_mate_geo_settings' ); ?>

				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Enable Geo-Targeting', 'azonmate' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="azon_mate_geo_enabled" value="1"
									<?php checked( get_option( 'azon_mate_geo_enabled', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Detect visitor country and swap affiliate tags', 'azonmate' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Fallback Marketplace', 'azonmate' ); ?></th>
						<td>
							<select name="azon_mate_geo_fallback">
								<?php foreach ( $marketplaces as $code => $label ) : ?>
									<option value="<?php echo esc_attr( $code ); ?>"
										<?php selected( get_option( 'azon_mate_geo_fallback', 'US' ), $code ); ?>>
										<?php echo esc_html( $label ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Per-Country Affiliate Tags', 'azonmate' ); ?></th>
						<td>
							<?php
							$geo_tags = get_option( 'azon_mate_geo_tags', array() );
							if ( ! is_array( $geo_tags ) ) {
								$geo_tags = array();
							}
							?>
							<table class="widefat azonmate-geo-tags-table" style="max-width: 500px;">
								<thead>
									<tr>
										<th><?php esc_html_e( 'Country', 'azonmate' ); ?></th>
										<th><?php esc_html_e( 'Affiliate Tag', 'azonmate' ); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ( $marketplaces as $code => $label ) : ?>
										<tr>
											<td>
												<strong><?php echo esc_html( $code ); ?></strong>
												<span class="description"><?php echo esc_html( $label ); ?></span>
											</td>
											<td>
												<input type="text"
													   name="azon_mate_geo_tags[<?php echo esc_attr( $code ); ?>]"
													   value="<?php echo esc_attr( $geo_tags[ $code ] ?? '' ); ?>"
													   class="regular-text"
													   placeholder="<?php esc_attr_e( 'e.g., mysite-20', 'azonmate' ); ?>" />
											</td>
										</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						</td>
					</tr>
				</table>

				<?php submit_button(); ?>
			</form>

		<?php elseif ( 'tracking' === $active_tab ) : ?>
			<!-- Tracking Tab -->
			<form method="post" action="options.php">
				<?php settings_fields( 'azon_mate_tracking_settings' ); ?>

				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Click Tracking', 'azonmate' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="azon_mate_tracking_enabled" value="1"
									<?php checked( get_option( 'azon_mate_tracking_enabled', '1' ), '1' ); ?> />
								<?php esc_html_e( 'Enable click tracking for affiliate links', 'azonmate' ); ?>
							</label>
						</td>
					</tr>
				</table>

				<?php submit_button(); ?>
			</form>

			<hr />

			<h3><?php esc_html_e( 'Quick Stats', 'azonmate' ); ?></h3>
			<?php
			$analytics = new \AzonMate\Admin\Analytics();
			?>
			<table class="widefat" style="max-width: 400px;">
				<tbody>
					<tr>
						<td><?php esc_html_e( 'Last 7 Days', 'azonmate' ); ?></td>
						<td><strong><?php echo absint( $analytics->get_click_count( 7 ) ); ?></strong> <?php esc_html_e( 'clicks', 'azonmate' ); ?></td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'Last 30 Days', 'azonmate' ); ?></td>
						<td><strong><?php echo absint( $analytics->get_click_count( 30 ) ); ?></strong> <?php esc_html_e( 'clicks', 'azonmate' ); ?></td>
					</tr>
					<tr>
						<td><?php esc_html_e( 'Last 90 Days', 'azonmate' ); ?></td>
						<td><strong><?php echo absint( $analytics->get_click_count( 90 ) ); ?></strong> <?php esc_html_e( 'clicks', 'azonmate' ); ?></td>
					</tr>
				</tbody>
			</table>

			<p>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=azonmate-analytics' ) ); ?>" class="button">
					<?php esc_html_e( 'View Full Analytics', 'azonmate' ); ?>
				</a>
			</p>

		<?php elseif ( 'advanced' === $active_tab ) : ?>
			<!-- Advanced Tab -->
			<form method="post" action="options.php">
				<?php settings_fields( 'azon_mate_advanced_settings' ); ?>

				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><?php esc_html_e( 'Disable Plugin CSS', 'azonmate' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="azon_mate_disable_css" value="1"
									<?php checked( get_option( 'azon_mate_disable_css', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Use your own CSS (disable all plugin styles)', 'azonmate' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="azon_mate_api_throttle"><?php esc_html_e( 'API Request Throttle', 'azonmate' ); ?></label>
						</th>
						<td>
							<input type="number" id="azon_mate_api_throttle" name="azon_mate_api_throttle"
								   value="<?php echo esc_attr( get_option( 'azon_mate_api_throttle', 1 ) ); ?>"
								   min="1" max="10" step="1" class="small-text" />
							<span class="description"><?php esc_html_e( 'requests per second (default: 1)', 'azonmate' ); ?></span>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Debug Mode', 'azonmate' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="azon_mate_debug_mode" value="1"
									<?php checked( get_option( 'azon_mate_debug_mode', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Log API calls to the error log', 'azonmate' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Amazon Disclaimer', 'azonmate' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="azon_mate_show_disclaimer" value="1"
									<?php checked( get_option( 'azon_mate_show_disclaimer', '1' ), '1' ); ?> />
								<?php esc_html_e( 'Show affiliate disclaimer', 'azonmate' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th scope="row">
							<label for="azon_mate_disclaimer_text"><?php esc_html_e( 'Disclaimer Text', 'azonmate' ); ?></label>
						</th>
						<td>
							<input type="text" id="azon_mate_disclaimer_text" name="azon_mate_disclaimer_text"
								   value="<?php echo esc_attr( get_option( 'azon_mate_disclaimer_text', __( 'As an Amazon Associate, I earn from qualifying purchases.', 'azonmate' ) ) ); ?>"
								   class="large-text" />
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Disclaimer Position', 'azonmate' ); ?></th>
						<td>
							<select name="azon_mate_disclaimer_position">
								<option value="footer" <?php selected( get_option( 'azon_mate_disclaimer_position', 'footer' ), 'footer' ); ?>>
									<?php esc_html_e( 'Footer', 'azonmate' ); ?>
								</option>
								<option value="before_content" <?php selected( get_option( 'azon_mate_disclaimer_position', 'footer' ), 'before_content' ); ?>>
									<?php esc_html_e( 'Before Content', 'azonmate' ); ?>
								</option>
								<option value="after_content" <?php selected( get_option( 'azon_mate_disclaimer_position', 'footer' ), 'after_content' ); ?>>
									<?php esc_html_e( 'After Content', 'azonmate' ); ?>
								</option>
							</select>
						</td>
					</tr>
					<tr>
						<th scope="row"><?php esc_html_e( 'Uninstall Behavior', 'azonmate' ); ?></th>
						<td>
							<label>
								<input type="checkbox" name="azon_mate_uninstall_delete" value="1"
									<?php checked( get_option( 'azon_mate_uninstall_delete', '0' ), '1' ); ?> />
								<?php esc_html_e( 'Delete all data on uninstall (tables, options, transients)', 'azonmate' ); ?>
							</label>
						</td>
					</tr>
				</table>

				<?php submit_button(); ?>
			</form>

		<?php endif; ?>

	</div>
</div>
