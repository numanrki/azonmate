<?php
/**
 * GitHub-based auto-updater.
 *
 * Hooks into WordPress's native update system to check the public GitHub
 * Releases API for newer versions and inject them into the plugin update
 * transient.  No API key required — the endpoint is public.
 *
 * @package AzonMate
 * @since   2.2.2
 */

namespace AzonMate;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class Updater
 *
 * @since 2.2.2
 */
class Updater {

	/**
	 * GitHub API endpoint for the latest release.
	 *
	 * @var string
	 */
	private const API_URL = 'https://api.github.com/repos/numanrki/azonmate/releases/latest';

	/**
	 * Transient key used to cache the GitHub response.
	 *
	 * @var string
	 */
	private const TRANSIENT_KEY = 'azonmate_github_update';

	/**
	 * Cache duration in seconds (12 hours).
	 *
	 * @var int
	 */
	private const CACHE_TTL = 43200;

	/**
	 * Plugin basename (e.g. azonmate/azonmate.php).
	 *
	 * @var string
	 */
	private string $basename;

	/**
	 * Plugin slug (directory name).
	 *
	 * @var string
	 */
	private string $slug;

	/**
	 * Constructor — register WordPress hooks.
	 */
	public function __construct() {
		$this->basename = AZON_MATE_PLUGIN_BASENAME;
		$this->slug     = dirname( $this->basename );

		add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'check_for_update' ) );
		add_filter( 'plugins_api', array( $this, 'plugin_info' ), 10, 3 );
		add_filter( 'upgrader_post_install', array( $this, 'after_install' ), 10, 3 );
	}

	/**
	 * Fetch the latest release data from GitHub (with caching).
	 *
	 * @return array|false Decoded JSON body or false on failure.
	 */
	private function fetch_release_data() {
		$data = get_transient( self::TRANSIENT_KEY );

		if ( false !== $data ) {
			return $data;
		}

		$response = wp_remote_get( self::API_URL, array(
			'headers' => array(
				'Accept'     => 'application/vnd.github.v3+json',
				'User-Agent' => 'AzonMate/' . AZON_MATE_VERSION . ' WordPress/' . get_bloginfo( 'version' ),
			),
			'timeout' => 10,
		) );

		if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
			return false;
		}

		$body = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( empty( $body['tag_name'] ) ) {
			return false;
		}

		set_transient( self::TRANSIENT_KEY, $body, self::CACHE_TTL );

		return $body;
	}

	/**
	 * Compare the remote version with the installed version and inject an
	 * update object into WordPress's update transient when a newer tag exists.
	 *
	 * @param  object $transient The update_plugins transient.
	 * @return object
	 */
	public function check_for_update( $transient ) {
		if ( empty( $transient->checked ) ) {
			return $transient;
		}

		$release = $this->fetch_release_data();

		if ( ! $release ) {
			return $transient;
		}

		$remote_version = ltrim( $release['tag_name'], 'v' );

		if ( version_compare( $remote_version, AZON_MATE_VERSION, '>' ) ) {
			$download_url = $release['zipball_url'] ?? '';

			if ( $download_url ) {
				$transient->response[ $this->basename ] = (object) array(
					'slug'        => $this->slug,
					'plugin'      => $this->basename,
					'new_version' => $remote_version,
					'url'         => 'https://github.com/numanrki/azonmate',
					'package'     => $download_url,
				);
			}
		}

		return $transient;
	}

	/**
	 * Provide plugin details for the WordPress "View details" modal.
	 *
	 * @param  false|object|array $result The result object or array.
	 * @param  string             $action The API action (e.g. 'plugin_information').
	 * @param  object             $args   Plugin API arguments.
	 * @return false|object
	 */
	public function plugin_info( $result, $action, $args ) {
		if ( 'plugin_information' !== $action || ( $args->slug ?? '' ) !== $this->slug ) {
			return $result;
		}

		$release = $this->fetch_release_data();

		if ( ! $release ) {
			return $result;
		}

		$remote_version = ltrim( $release['tag_name'], 'v' );

		return (object) array(
			'name'          => 'AzonMate',
			'slug'          => $this->slug,
			'version'       => $remote_version,
			'author'        => '<a href="https://github.com/numanrki">Numan Rashed</a>',
			'homepage'      => 'https://github.com/numanrki/azonmate',
			'download_link' => $release['zipball_url'] ?? '',
			'sections'      => array(
				'description' => 'Amazon Affiliate Product Engine for WordPress.',
				'changelog'   => nl2br( esc_html( $release['body'] ?? '' ) ),
			),
		);
	}

	/**
	 * After WordPress extracts the ZIP, rename the extracted folder back to
	 * the expected plugin slug so WordPress can locate the plugin.
	 *
	 * GitHub ZIPs extract to "owner-repo-hash/" — we need "azonmate/".
	 *
	 * @param  bool  $response   Installation response.
	 * @param  array $hook_extra Extra arguments (contains 'plugin').
	 * @param  array $result     Installation result data.
	 * @return array Modified result with corrected destination.
	 */
	public function after_install( $response, $hook_extra, $result ) {
		if ( ! isset( $hook_extra['plugin'] ) || $hook_extra['plugin'] !== $this->basename ) {
			return $result;
		}

		global $wp_filesystem;

		$proper_destination = WP_PLUGIN_DIR . '/' . $this->slug;

		$wp_filesystem->move( $result['destination'], $proper_destination );

		$result['destination']      = $proper_destination;
		$result['destination_name'] = $this->slug;

		// Re-activate plugin after update.
		activate_plugin( $this->basename );

		return $result;
	}
}
