<?php
/**
 * Search modal HTML template for the Classic Editor.
 *
 * @package AzonMate\Admin\Views
 * @since   1.0.0
 */

// Abort if this file is called directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$marketplaces = \AzonMate\API\Marketplace::get_all_options();
$categories   = \AzonMate\API\Marketplace::get_categories();
?>

<div id="azonmate-search-modal" class="azonmate-search-modal" style="display: none;">
	<div class="azonmate-search-modal__header">
		<h2><?php esc_html_e( 'Search Amazon Products', 'azonmate' ); ?></h2>
	</div>

	<div class="azonmate-search-modal__body">
		<!-- Search Form -->
		<div class="azonmate-search-modal__form">
			<div class="azonmate-search-modal__row">
				<div class="azonmate-search-modal__field azonmate-search-modal__field--keywords">
					<label for="azonmate-search-keywords"><?php esc_html_e( 'Keywords', 'azonmate' ); ?></label>
					<input type="text" id="azonmate-search-keywords"
						   placeholder="<?php esc_attr_e( 'Search Amazon products...', 'azonmate' ); ?>"
						   autocomplete="off" />
				</div>
				<div class="azonmate-search-modal__field">
					<label for="azonmate-search-category"><?php esc_html_e( 'Category', 'azonmate' ); ?></label>
					<select id="azonmate-search-category">
						<?php foreach ( $categories as $value => $label ) : ?>
							<option value="<?php echo esc_attr( $value ); ?>"><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="azonmate-search-modal__field">
					<label for="azonmate-search-marketplace"><?php esc_html_e( 'Marketplace', 'azonmate' ); ?></label>
					<select id="azonmate-search-marketplace">
						<option value=""><?php esc_html_e( 'Default', 'azonmate' ); ?></option>
						<?php foreach ( $marketplaces as $code => $label ) : ?>
							<option value="<?php echo esc_attr( $code ); ?>"><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="azonmate-search-modal__field azonmate-search-modal__field--button">
					<button type="button" id="azonmate-search-btn" class="button button-primary">
						<?php esc_html_e( 'Search', 'azonmate' ); ?>
					</button>
				</div>
			</div>

			<!-- ASIN Lookup -->
			<div class="azonmate-search-modal__row azonmate-search-modal__row--asin">
				<div class="azonmate-search-modal__field">
					<label for="azonmate-asin-input"><?php esc_html_e( 'Or look up by ASIN:', 'azonmate' ); ?></label>
					<input type="text" id="azonmate-asin-input"
						   placeholder="<?php esc_attr_e( 'Enter ASIN (e.g., B08N5WRWNW)', 'azonmate' ); ?>"
						   maxlength="10" autocomplete="off" />
				</div>
				<div class="azonmate-search-modal__field azonmate-search-modal__field--button">
					<button type="button" id="azonmate-asin-btn" class="button">
						<?php esc_html_e( 'Lookup', 'azonmate' ); ?>
					</button>
				</div>
				<div class="azonmate-search-modal__field azonmate-search-modal__field--button">
					<button type="button" class="button azonmate-browse-manual" style="background: #2271b1; color: #fff; border-color: #2271b1;">
						<?php esc_html_e( '📦 My Products', 'azonmate' ); ?>
					</button>
				</div>
			</div>
		</div>

		<!-- Loading Spinner -->
		<div id="azonmate-search-loading" class="azonmate-search-modal__loading" style="display: none;">
			<span class="spinner is-active"></span>
			<span><?php esc_html_e( 'Searching...', 'azonmate' ); ?></span>
		</div>

		<!-- Results Grid -->
		<div id="azonmate-search-results" class="azonmate-search-modal__results"></div>

		<!-- Pagination -->
		<div id="azonmate-search-pagination" class="azonmate-search-modal__pagination" style="display: none;">
			<button type="button" id="azonmate-prev-page" class="button" disabled>&laquo; <?php esc_html_e( 'Previous', 'azonmate' ); ?></button>
			<span id="azonmate-page-info" class="azonmate-search-modal__page-info"></span>
			<button type="button" id="azonmate-next-page" class="button"><?php esc_html_e( 'Next', 'azonmate' ); ?> &raquo;</button>
		</div>

		<!-- No Results Message -->
		<div id="azonmate-no-results" class="azonmate-search-modal__no-results" style="display: none;">
			<p><?php esc_html_e( 'No products found. Try different keywords or category.', 'azonmate' ); ?></p>
		</div>
	</div>

	<!-- Insert Options Dropdown Template -->
	<div id="azonmate-insert-options-template" style="display: none;">
		<div class="azonmate-insert-options">
			<button type="button" class="azonmate-insert-option" data-type="box">
				<?php esc_html_e( 'Product Box', 'azonmate' ); ?>
			</button>
			<button type="button" class="azonmate-insert-option" data-type="link">
				<?php esc_html_e( 'Text Link', 'azonmate' ); ?>
			</button>
			<button type="button" class="azonmate-insert-option" data-type="image">
				<?php esc_html_e( 'Image Link', 'azonmate' ); ?>
			</button>
			<button type="button" class="azonmate-insert-option" data-type="table">
				<?php esc_html_e( 'Table Row', 'azonmate' ); ?>
			</button>
			<button type="button" class="azonmate-insert-option" data-type="asin">
				<?php esc_html_e( 'ASIN Only', 'azonmate' ); ?>
			</button>
		</div>
	</div>
</div>
