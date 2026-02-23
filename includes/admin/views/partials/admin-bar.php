<?php
/**
 * Shared admin top bar & footer bar for all AzonMate pages.
 *
 * Renders a branded header with GitHub link, author, version, and star CTA,
 * plus a compact footer with the same info.
 *
 * Usage:
 *   include __DIR__ . '/partials/admin-bar.php';
 *   azonmate_render_admin_header();   // after <div class="wrap ...">
 *   azonmate_render_admin_footer();   // before closing </div>
 *
 * @package AzonMate\Admin\Views
 * @since   1.3.2
 */

// Abort if this file is called directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Render the AzonMate admin top bar.
 */
function azonmate_render_admin_header() {
	$version = defined( 'AZON_MATE_VERSION' ) ? AZON_MATE_VERSION : '1.0.0';
	?>
	<div class="azonmate-admin-bar">
		<div class="azonmate-admin-bar__left">
			<span class="azonmate-admin-bar__brand">
				<svg class="azonmate-admin-bar__logo" width="22" height="22" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
					<rect width="64" height="64" rx="14" fill="#FF9900"/>
					<text x="32" y="25" font-family="Arial, Helvetica, sans-serif" font-size="13" font-weight="bold" fill="#fff" text-anchor="middle" dominant-baseline="central">Azon</text>
					<text x="32" y="42" font-family="Arial, Helvetica, sans-serif" font-size="13" font-weight="bold" fill="#fff" text-anchor="middle" dominant-baseline="central">Mate</text>
					<path d="M14 50 C22 56, 34 56, 44 52" stroke="#fff" stroke-width="2.5" fill="none" stroke-linecap="round"/>
					<polygon points="44,49 50,52 44,55" fill="#fff"/>
				</svg>
				<strong>AzonMate</strong>
			</span>
			<span class="azonmate-admin-bar__sep"></span>
			<span class="azonmate-admin-bar__version">v<?php echo esc_html( $version ); ?></span>
			<span class="azonmate-admin-bar__sep"></span>
			<span class="azonmate-admin-bar__author">
				<?php
				printf(
					/* translators: %s = author name */
					esc_html__( 'by %s', 'azonmate' ),
					'<a href="https://github.com/numanrki" target="_blank" rel="noopener">Numan Rashed</a>'
				);
				?>
			</span>
		</div>
		<div class="azonmate-admin-bar__right">
			<a href="https://github.com/numanrki/azonmate" target="_blank" rel="noopener" class="azonmate-admin-bar__gh-link">
				<svg class="azonmate-admin-bar__gh-icon" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
					<path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/>
				</svg>
				<?php esc_html_e( 'GitHub', 'azonmate' ); ?>
			</a>
			<a href="https://github.com/numanrki/azonmate" target="_blank" rel="noopener" class="azonmate-admin-bar__star-btn">
				<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
					<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
				</svg>
				<?php esc_html_e( 'Star on GitHub', 'azonmate' ); ?>
			</a>
		</div>
	</div>
	<?php
}

/**
 * Render the AzonMate admin footer bar.
 */
function azonmate_render_admin_footer() {
	$version = defined( 'AZON_MATE_VERSION' ) ? AZON_MATE_VERSION : '1.0.0';
	?>
	<div class="azonmate-admin-footer">
		<span class="azonmate-admin-footer__left">
			AzonMate v<?php echo esc_html( $version ); ?> &mdash;
			<?php
			printf(
				/* translators: %s = author link */
				esc_html__( 'Built with %s by Numan Rashed', 'azonmate' ),
				'<span style="color:#e25555;">&#10084;</span>'
			);
			?>
		</span>
		<span class="azonmate-admin-footer__right">
			<a href="https://github.com/numanrki/azonmate" target="_blank" rel="noopener">
				<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" style="vertical-align:-2px; margin-right:3px;">
					<path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0024 12c0-6.63-5.37-12-12-12z"/>
				</svg>
				<?php esc_html_e( 'View on GitHub', 'azonmate' ); ?>
			</a>
			&nbsp;&middot;&nbsp;
			<a href="https://github.com/numanrki/azonmate/issues" target="_blank" rel="noopener">
				<?php esc_html_e( 'Report a Bug', 'azonmate' ); ?>
			</a>
			&nbsp;&middot;&nbsp;
			<a href="https://github.com/numanrki/azonmate/releases/latest" target="_blank" rel="noopener">
				<?php esc_html_e( 'Releases', 'azonmate' ); ?>
			</a>
		</span>
	</div>
	<?php
}
