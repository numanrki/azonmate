<?php
/**
 * Manual products management page.
 *
 * @package AzonMate\Admin\Views
 * @since   1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$marketplace = get_option( 'azon_mate_marketplace', 'US' );
$marketplaces = \AzonMate\API\Marketplace::get_all_options();

if ( ! function_exists( 'azonmate_render_admin_header' ) ) {
	require_once __DIR__ . '/partials/admin-bar.php';
}
?>

<div class="wrap azonmate-products-page">
	<?php azonmate_render_admin_header(); ?>
	<h1>
		<span class="dashicons dashicons-cart" style="margin-right: 8px;"></span>
		<?php esc_html_e( 'AzonMate Products', 'azonmate' ); ?>
		<button type="button" id="azonmate-add-product-btn" class="page-title-action">
			<?php esc_html_e( 'Add New Product', 'azonmate' ); ?>
		</button>
	</h1>

	<p class="description" style="margin-bottom: 20px;">
		<?php esc_html_e( 'Manually create product cards without requiring Amazon API access. Add your affiliate products here and use them in posts with shortcodes or Gutenberg blocks.', 'azonmate' ); ?>
	</p>

	<!-- Products List -->
	<div id="azonmate-products-list">
		<div class="azonmate-products-loading">
			<span class="spinner is-active" style="float: none;"></span>
			<?php esc_html_e( 'Loading products...', 'azonmate' ); ?>
		</div>
	</div>

	<!-- Add/Edit Product Form Modal -->
	<div id="azonmate-product-modal" class="azonmate-modal" style="display: none;">
		<div class="azonmate-modal-overlay"></div>
		<div class="azonmate-modal-content">
			<div class="azonmate-modal-header">
				<h2 id="azonmate-modal-title"><?php esc_html_e( 'Add New Product', 'azonmate' ); ?></h2>
				<button type="button" class="azonmate-modal-close" aria-label="<?php esc_attr_e( 'Close', 'azonmate' ); ?>">&times;</button>
			</div>
			<div class="azonmate-modal-body">
				<form id="azonmate-product-form">
					<div class="azonmate-form-columns">
						<!-- Left Column -->
						<div class="azonmate-form-col">
							<h3><?php esc_html_e( 'Basic Information', 'azonmate' ); ?></h3>

							<div class="azonmate-field">
								<label for="azonmate-product-asin"><?php esc_html_e( 'Product ID / ASIN', 'azonmate' ); ?> <span class="required">*</span></label>
								<input type="text" id="azonmate-product-asin" name="asin" required
									   placeholder="<?php esc_attr_e( 'e.g., B09V4K3GZQ or any unique ID', 'azonmate' ); ?>" />
								<p class="description"><?php esc_html_e( 'Amazon ASIN or a custom unique identifier. This is used in shortcodes.', 'azonmate' ); ?></p>
							</div>

							<div class="azonmate-field">
								<label for="azonmate-product-title"><?php esc_html_e( 'Product Title', 'azonmate' ); ?> <span class="required">*</span></label>
								<input type="text" id="azonmate-product-title" name="title" required
									   placeholder="<?php esc_attr_e( 'e.g., Wireless Bluetooth Headphones', 'azonmate' ); ?>" />
							</div>

							<div class="azonmate-field">
								<label for="azonmate-product-url"><?php esc_html_e( 'Product / Affiliate URL', 'azonmate' ); ?></label>
								<input type="url" id="azonmate-product-url" name="url"
									   placeholder="<?php esc_attr_e( 'https://www.amazon.com/dp/B09V4K3GZQ?tag=yourtag-20', 'azonmate' ); ?>" />
								<p class="description"><?php esc_html_e( 'Full affiliate link. Leave blank to auto-generate from ASIN + your partner tag.', 'azonmate' ); ?></p>
							</div>

							<div class="azonmate-field">
								<label for="azonmate-product-image"><?php esc_html_e( 'Product Image URL', 'azonmate' ); ?></label>
								<div class="azonmate-image-field">
									<input type="url" id="azonmate-product-image" name="image_url"
										   placeholder="<?php esc_attr_e( 'https://example.com/image.jpg', 'azonmate' ); ?>" />
									<button type="button" class="button azonmate-upload-image" data-target="#azonmate-product-image">
										<?php esc_html_e( 'Upload', 'azonmate' ); ?>
									</button>
								</div>
								<div id="azonmate-image-preview" class="azonmate-image-preview" style="display: none;">
									<img src="" alt="Preview" />
								</div>
							</div>

							<div class="azonmate-field">
								<label for="azonmate-product-brand"><?php esc_html_e( 'Brand', 'azonmate' ); ?></label>
								<input type="text" id="azonmate-product-brand" name="brand"
									   placeholder="<?php esc_attr_e( 'e.g., Sony', 'azonmate' ); ?>" />
							</div>

							<div class="azonmate-field">
								<label for="azonmate-product-description"><?php esc_html_e( 'Description', 'azonmate' ); ?></label>
								<textarea id="azonmate-product-description" name="description" rows="3"
										  placeholder="<?php esc_attr_e( 'Short product description...', 'azonmate' ); ?>"></textarea>
							</div>

							<div class="azonmate-field">
								<label for="azonmate-product-features"><?php esc_html_e( 'Features (one per line)', 'azonmate' ); ?></label>
								<textarea id="azonmate-product-features" name="features" rows="4"
										  placeholder="<?php esc_attr_e( "Active Noise Cancellation\n40-hour battery life\nBluetooth 5.2\nComfortable fit", 'azonmate' ); ?>"></textarea>
							</div>

							<hr />
							<h3><?php esc_html_e( 'Showcase Options', 'azonmate' ); ?></h3>

							<div class="azonmate-field">
								<label for="azonmate-product-badge-label"><?php esc_html_e( 'Badge Label', 'azonmate' ); ?></label>
								<input type="text" id="azonmate-product-badge-label" name="badge_label"
									   placeholder="<?php esc_attr_e( 'e.g., Best Value, Premium Pick, Editor\'s Choice', 'azonmate' ); ?>" />
								<p class="description"><?php esc_html_e( 'A badge shown on Showcase layouts. Leave empty for no badge.', 'azonmate' ); ?></p>
							</div>

							<div class="azonmate-field">
								<label for="azonmate-product-button-text"><?php esc_html_e( 'Custom Button Text', 'azonmate' ); ?></label>
								<input type="text" id="azonmate-product-button-text" name="button_text"
									   placeholder="<?php esc_attr_e( 'e.g., Check Price, Get Deal', 'azonmate' ); ?>" />
								<p class="description"><?php esc_html_e( 'Override CTA button text for this product. Leave empty to use the global default.', 'azonmate' ); ?></p>
							</div>
						</div>

						<!-- Right Column -->
						<div class="azonmate-form-col">
							<h3><?php esc_html_e( 'Pricing & Details', 'azonmate' ); ?></h3>

							<div class="azonmate-field-row">
								<div class="azonmate-field">
									<label for="azonmate-product-price-display"><?php esc_html_e( 'Display Price', 'azonmate' ); ?></label>
									<input type="text" id="azonmate-product-price-display" name="price_display"
										   placeholder="<?php esc_attr_e( '$29.99', 'azonmate' ); ?>" />
									<p class="description"><?php esc_html_e( 'Shown to visitors (e.g., $29.99)', 'azonmate' ); ?></p>
								</div>
								<div class="azonmate-field">
									<label for="azonmate-product-price-amount"><?php esc_html_e( 'Price Amount', 'azonmate' ); ?></label>
									<input type="number" id="azonmate-product-price-amount" name="price_amount"
										   step="0.01" min="0" placeholder="29.99" />
								</div>
							</div>

							<div class="azonmate-field-row">
								<div class="azonmate-field">
									<label for="azonmate-product-list-price"><?php esc_html_e( 'Original Price (if on sale)', 'azonmate' ); ?></label>
									<input type="number" id="azonmate-product-list-price" name="list_price_amount"
										   step="0.01" min="0" placeholder="49.99" />
								</div>
								<div class="azonmate-field">
									<label for="azonmate-product-savings"><?php esc_html_e( 'Savings %', 'azonmate' ); ?></label>
									<input type="number" id="azonmate-product-savings" name="savings_percentage"
										   min="0" max="99" placeholder="40" />
								</div>
							</div>

							<div class="azonmate-field-row">
								<div class="azonmate-field">
									<label for="azonmate-product-rating"><?php esc_html_e( 'Rating (0–5)', 'azonmate' ); ?></label>
									<input type="number" id="azonmate-product-rating" name="rating"
										   step="0.1" min="0" max="5" placeholder="4.5" />
								</div>
								<div class="azonmate-field">
									<label for="azonmate-product-reviews"><?php esc_html_e( 'Review Count', 'azonmate' ); ?></label>
									<input type="number" id="azonmate-product-reviews" name="review_count"
										   min="0" placeholder="1234" />
								</div>
							</div>

							<div class="azonmate-field">
								<label>
									<input type="checkbox" id="azonmate-product-prime" name="is_prime" value="1" />
									<?php esc_html_e( 'Prime Eligible', 'azonmate' ); ?>
								</label>
							</div>

							<div class="azonmate-field">
								<label for="azonmate-product-category"><?php esc_html_e( 'Category', 'azonmate' ); ?></label>
								<input type="text" id="azonmate-product-category" name="browse_node"
									   placeholder="<?php esc_attr_e( 'e.g., Electronics', 'azonmate' ); ?>" />
							</div>

							<div class="azonmate-field">
								<label for="azonmate-product-marketplace"><?php esc_html_e( 'Marketplace', 'azonmate' ); ?></label>
								<select id="azonmate-product-marketplace" name="marketplace">
									<?php foreach ( $marketplaces as $code => $label ) : ?>
										<option value="<?php echo esc_attr( $code ); ?>"
											<?php selected( $marketplace, $code ); ?>>
											<?php echo esc_html( $label ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</div>

							<hr />

							<h3><?php esc_html_e( 'Usage', 'azonmate' ); ?></h3>
							<div id="azonmate-usage-info" class="azonmate-usage-info" style="display: none;">
								<p><strong><?php esc_html_e( 'Shortcodes:', 'azonmate' ); ?></strong></p>
								<code id="azonmate-usage-box"></code><br />
								<code id="azonmate-usage-link"></code><br />
								<code id="azonmate-usage-image"></code><br />
								<code id="azonmate-usage-showcase"></code>
								<p class="description"><?php esc_html_e( 'Copy and paste these into your posts. Use showcase with comma-separated ASINs for multi-product displays.', 'azonmate' ); ?></p>
							</div>
						</div>
					</div>

					<div class="azonmate-modal-footer">
						<span id="azonmate-save-result" class="azonmate-save-result"></span>
						<button type="button" class="button azonmate-modal-cancel"><?php esc_html_e( 'Cancel', 'azonmate' ); ?></button>
						<button type="submit" class="button button-primary" id="azonmate-save-product-btn">
							<?php esc_html_e( 'Save Product', 'azonmate' ); ?>
						</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<?php azonmate_render_admin_footer(); ?>
</div>

<style>
.azonmate-products-page .azonmate-products-loading {
	padding: 40px;
	text-align: center;
	color: #666;
}

/* Product cards grid */
.azonmate-products-grid {
	display: grid;
	grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
	gap: 20px;
	margin-top: 10px;
}

.azonmate-product-card {
	background: #fff;
	border: 1px solid #ddd;
	border-radius: 8px;
	overflow: hidden;
	transition: box-shadow 0.2s;
}

.azonmate-product-card:hover {
	box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
}

.azonmate-product-card-inner {
	display: flex;
	gap: 15px;
	padding: 16px;
}

.azonmate-product-card-image {
	flex: 0 0 80px;
	height: 80px;
	background: #f9f9f9;
	border-radius: 4px;
	overflow: hidden;
	display: flex;
	align-items: center;
	justify-content: center;
}

.azonmate-product-card-image img {
	max-width: 100%;
	max-height: 100%;
	object-fit: contain;
}

.azonmate-product-card-image .dashicons {
	font-size: 32px;
	color: #ccc;
}

.azonmate-product-card-info {
	flex: 1;
	min-width: 0;
}

.azonmate-product-card-info h3 {
	margin: 0 0 4px;
	font-size: 14px;
	line-height: 1.4;
	overflow: hidden;
	text-overflow: ellipsis;
	white-space: nowrap;
}

.azonmate-product-card-meta {
	font-size: 12px;
	color: #666;
	margin-bottom: 4px;
}

.azonmate-product-card-meta .azonmate-card-price {
	color: #B12704;
	font-weight: 600;
	font-size: 14px;
}

.azonmate-product-card-meta .azonmate-card-asin {
	background: #f0f0f0;
	padding: 1px 6px;
	border-radius: 3px;
	font-family: monospace;
	font-size: 11px;
}

.azonmate-product-card-meta .azonmate-card-badge {
	background: linear-gradient(135deg, #ff9900, #e68a00);
	color: #000;
	padding: 1px 8px;
	border-radius: 3px;
	font-size: 10px;
	font-weight: 700;
	text-transform: uppercase;
	letter-spacing: 0.3px;
}

.azonmate-product-card-actions {
	display: flex;
	gap: 8px;
	border-top: 1px solid #eee;
	padding: 10px 16px;
	background: #fafafa;
}

.azonmate-product-card-actions .button {
	font-size: 12px;
}

.azonmate-product-card-actions .azonmate-copy-shortcode {
	margin-left: auto;
}

.azonmate-products-empty {
	text-align: center;
	padding: 60px 20px;
	background: #fff;
	border: 2px dashed #ddd;
	border-radius: 8px;
}

.azonmate-products-empty .dashicons {
	font-size: 48px;
	width: 48px;
	height: 48px;
	color: #ccc;
	margin-bottom: 10px;
}

.azonmate-products-empty h2 {
	margin: 0 0 10px;
	color: #333;
}

.azonmate-products-empty p {
	color: #666;
	max-width: 500px;
	margin: 0 auto 20px;
}

/* Modal */
.azonmate-modal {
	position: fixed;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	z-index: 160000;
	display: flex;
	align-items: center;
	justify-content: center;
}

.azonmate-modal-overlay {
	position: absolute;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background: rgba(0, 0, 0, 0.6);
}

.azonmate-modal-content {
	position: relative;
	background: #fff;
	border-radius: 8px;
	width: 90%;
	max-width: 900px;
	max-height: 90vh;
	overflow-y: auto;
	box-shadow: 0 8px 40px rgba(0, 0, 0, 0.3);
}

.azonmate-modal-header {
	display: flex;
	justify-content: space-between;
	align-items: center;
	padding: 16px 24px;
	border-bottom: 1px solid #eee;
}

.azonmate-modal-header h2 {
	margin: 0;
	font-size: 18px;
}

.azonmate-modal-close {
	background: none;
	border: none;
	font-size: 24px;
	cursor: pointer;
	color: #666;
	padding: 0;
	line-height: 1;
}

.azonmate-modal-close:hover {
	color: #d63638;
}

.azonmate-modal-body {
	padding: 24px;
}

.azonmate-form-columns {
	display: grid;
	grid-template-columns: 1fr 1fr;
	gap: 24px;
}

@media (max-width: 782px) {
	.azonmate-form-columns {
		grid-template-columns: 1fr;
	}
}

.azonmate-form-col h3 {
	margin: 0 0 16px;
	padding-bottom: 8px;
	border-bottom: 1px solid #eee;
	font-size: 14px;
}

.azonmate-field {
	margin-bottom: 16px;
}

.azonmate-field label {
	display: block;
	margin-bottom: 4px;
	font-weight: 600;
	font-size: 13px;
}

.azonmate-field .required {
	color: #d63638;
}

.azonmate-field input[type="text"],
.azonmate-field input[type="url"],
.azonmate-field input[type="number"],
.azonmate-field textarea,
.azonmate-field select {
	width: 100%;
}

.azonmate-field .description {
	margin-top: 4px;
	font-size: 12px;
}

.azonmate-field-row {
	display: grid;
	grid-template-columns: 1fr 1fr;
	gap: 12px;
}

.azonmate-image-field {
	display: flex;
	gap: 8px;
}

.azonmate-image-field input {
	flex: 1;
}

.azonmate-image-preview {
	margin-top: 8px;
}

.azonmate-image-preview img {
	max-width: 120px;
	max-height: 120px;
	border: 1px solid #ddd;
	border-radius: 4px;
	object-fit: contain;
}

.azonmate-usage-info code {
	display: block;
	margin-bottom: 6px;
	padding: 4px 8px;
	background: #f0f0f0;
	border-radius: 3px;
	font-size: 12px;
	cursor: pointer;
}

.azonmate-usage-info code:hover {
	background: #e0e0e0;
}

.azonmate-modal-footer {
	display: flex;
	justify-content: flex-end;
	align-items: center;
	gap: 10px;
	padding-top: 20px;
	border-top: 1px solid #eee;
	margin-top: 20px;
}

.azonmate-save-result {
	margin-right: auto;
	font-size: 13px;
}

.azonmate-save-result.success {
	color: #00a32a;
}

.azonmate-save-result.error {
	color: #d63638;
}

/* Copied toast */
.azonmate-copied-toast {
	position: fixed;
	bottom: 30px;
	right: 30px;
	background: #333;
	color: #fff;
	padding: 10px 20px;
	border-radius: 4px;
	font-size: 13px;
	z-index: 170000;
	animation: azonmate-fade-in-out 2s ease;
}

@keyframes azonmate-fade-in-out {
	0% { opacity: 0; transform: translateY(10px); }
	15% { opacity: 1; transform: translateY(0); }
	85% { opacity: 1; transform: translateY(0); }
	100% { opacity: 0; transform: translateY(-10px); }
}
</style>
