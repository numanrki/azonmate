<?php
/**
 * OAuth 2.0 client for Amazon Creators API.
 *
 * @package AzonMate\API
 * @since   2.0.0
 */

namespace AzonMate\API;

// Abort if this file is called directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class RequestSigner
 *
 * Handles OAuth 2.0 token acquisition and authenticated API requests
 * for the Amazon Creators API.
 *
 * @since 2.0.0
 */
class RequestSigner {

	/**
	 * Credential ID.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	private $credential_id;

	/**
	 * Credential Secret.
	 *
	 * @since 2.0.0
	 * @var string
	 */
	private $credential_secret;

	/**
	 * Credential version (e.g. 2.1, 3.1).
	 *
	 * @since 2.0.0
	 * @var string
	 */
	private $version;

	/**
	 * Marketplace domain (e.g. www.amazon.com).
	 *
	 * @since 2.0.0
	 * @var string
	 */
	private $marketplace_domain;

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 *
	 * @param string $credential_id     Credential ID.
	 * @param string $credential_secret Credential Secret.
	 * @param string $version           Credential version.
	 * @param string $marketplace_domain Marketplace domain (e.g. www.amazon.com).
	 */
	public function __construct( $credential_id, $credential_secret, $version, $marketplace_domain ) {
		$this->credential_id     = $credential_id;
		$this->credential_secret = $credential_secret;
		$this->version           = $version;
		$this->marketplace_domain = $marketplace_domain;
	}

	/**
	 * Get a valid OAuth access token, using cache when possible.
	 *
	 * @since 2.0.0
	 *
	 * @return string|\WP_Error Access token or WP_Error on failure.
	 */
	private function get_access_token() {
		$transient_key = 'azon_mate_oauth_token_' . md5( $this->credential_id . $this->version );
		$token         = get_transient( $transient_key );

		if ( ! empty( $token ) ) {
			return $token;
		}

		$token_data = $this->fetch_token();
		if ( is_wp_error( $token_data ) ) {
			return $token_data;
		}

		// Cache for 3500 seconds (just under the 3600s expiry).
		set_transient( $transient_key, $token_data['access_token'], 3500 );

		return $token_data['access_token'];
	}

	/**
	 * Fetch a new OAuth token from the token endpoint.
	 *
	 * @since 2.0.0
	 *
	 * @return array|\WP_Error Token data array or WP_Error on failure.
	 */
	private function fetch_token() {
		$token_url = Marketplace::get_token_endpoint( $this->version );
		$major     = (int) $this->version;

		if ( 3 === $major ) {
			// v3.x — LwA: JSON body, scope uses ::
			$body = wp_json_encode( array(
				'grant_type'    => 'client_credentials',
				'client_id'     => $this->credential_id,
				'client_secret' => $this->credential_secret,
				'scope'         => 'creatorsapi::default',
			) );

			$response = wp_remote_post( $token_url, array(
				'headers' => array( 'Content-Type' => 'application/json' ),
				'body'    => $body,
				'timeout' => 15,
			) );
		} else {
			// v2.x — Cognito: form-encoded body, scope uses /
			$body = array(
				'grant_type'    => 'client_credentials',
				'client_id'     => $this->credential_id,
				'client_secret' => $this->credential_secret,
				'scope'         => 'creatorsapi/default',
			);

			$response = wp_remote_post( $token_url, array(
				'headers' => array( 'Content-Type' => 'application/x-www-form-urlencoded' ),
				'body'    => $body,
				'timeout' => 15,
			) );
		}

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = wp_remote_retrieve_response_code( $response );
		$body = wp_remote_retrieve_body( $response );

		if ( 200 !== $code ) {
			return new \WP_Error(
				'azon_mate_token_error',
				sprintf(
					/* translators: 1: HTTP status code, 2: Response body */
					__( 'OAuth token request failed (%1$d): %2$s', 'azonmate' ),
					$code,
					$body
				)
			);
		}

		$decoded = json_decode( $body, true );

		if ( empty( $decoded['access_token'] ) ) {
			return new \WP_Error( 'azon_mate_token_error', __( 'OAuth response did not contain an access token.', 'azonmate' ) );
		}

		return $decoded;
	}

	/**
	 * Send an authenticated request to the Amazon Creators API.
	 *
	 * @since 2.0.0
	 *
	 * @param string $operation  The API operation (e.g., 'searchItems', 'getItems').
	 * @param array  $payload    The request payload (will be JSON-encoded).
	 * @return array|\WP_Error   Decoded response body or WP_Error on failure.
	 */
	public function send_request( $operation, $payload ) {
		$access_token = $this->get_access_token();
		if ( is_wp_error( $access_token ) ) {
			return $access_token;
		}

		$url = Marketplace::get_endpoint( '', $operation );

		$payload_json = wp_json_encode( $payload );
		if ( false === $payload_json ) {
			return new \WP_Error( 'azon_mate_json_encode_error', __( 'Failed to encode request payload.', 'azonmate' ) );
		}

		// Build Authorization header.
		$major = (int) $this->version;
		if ( 2 === $major ) {
			$auth_header = sprintf( 'Bearer %s, Version %s', $access_token, $this->version );
		} else {
			$auth_header = sprintf( 'Bearer %s', $access_token );
		}

		$request_headers = array(
			'Content-Type'  => 'application/json',
			'Authorization' => $auth_header,
			'x-marketplace' => $this->marketplace_domain,
		);

		// Log request in debug mode.
		if ( \AzonMate\Plugin::is_debug_enabled() ) {
			error_log( sprintf( '[AzonMate] API Request: %s %s', $operation, $url ) );
		}

		$response = wp_remote_post( $url, array(
			'headers' => $request_headers,
			'body'    => $payload_json,
			'timeout' => 15,
		) );

		if ( is_wp_error( $response ) ) {
			if ( \AzonMate\Plugin::is_debug_enabled() ) {
				error_log( '[AzonMate] API Error: ' . $response->get_error_message() );
			}
			return $response;
		}

		$response_code = wp_remote_retrieve_response_code( $response );
		$response_body = wp_remote_retrieve_body( $response );

		if ( \AzonMate\Plugin::is_debug_enabled() ) {
			error_log( sprintf( '[AzonMate] API Response: %d', $response_code ) );
		}

		if ( 200 !== $response_code ) {
			$error_message = sprintf(
				/* translators: 1: HTTP status code, 2: Response body */
				__( 'Amazon API returned error %1$d: %2$s', 'azonmate' ),
				$response_code,
				$response_body
			);
			return new \WP_Error( 'azon_mate_api_error', $error_message );
		}

		$decoded = json_decode( $response_body, true );

		if ( null === $decoded ) {
			return new \WP_Error( 'azon_mate_json_decode_error', __( 'Failed to parse API response.', 'azonmate' ) );
		}

		// Check for API-level errors.
		if ( isset( $decoded['errors'] ) ) {
			$api_error = $decoded['errors'][0];
			$error_msg = isset( $api_error['message'] ) ? $api_error['message'] : __( 'Unknown API error', 'azonmate' );
			return new \WP_Error( 'azon_mate_api_error', $error_msg );
		}

		return $decoded;
	}
}
}
