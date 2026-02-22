<?php
/**
 * AWS Signature Version 4 request signer for Amazon PA-API 5.0.
 *
 * @package AzonMate\API
 * @since   1.0.0
 */

namespace AzonMate\API;

// Abort if this file is called directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class RequestSigner
 *
 * Implements AWS Signature Version 4 (HMAC-SHA256) for signing
 * Amazon PA-API 5.0 requests.
 *
 * @since 1.0.0
 */
class RequestSigner {

	/**
	 * AWS access key.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $access_key;

	/**
	 * AWS secret key.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $secret_key;

	/**
	 * AWS region (e.g., us-east-1).
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $region;

	/**
	 * API host (e.g., webservices.amazon.com).
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $host;

	/**
	 * The AWS service name.
	 *
	 * @since 1.0.0
	 * @var string
	 */
	private $service = 'ProductAdvertisingAPI';

	/**
	 * Constructor.
	 *
	 * @since 1.0.0
	 *
	 * @param string $access_key AWS access key.
	 * @param string $secret_key AWS secret key.
	 * @param string $region     AWS region.
	 * @param string $host       API host.
	 */
	public function __construct( $access_key, $secret_key, $region, $host ) {
		$this->access_key = $access_key;
		$this->secret_key = $secret_key;
		$this->region     = $region;
		$this->host       = $host;
	}

	/**
	 * Sign and send a request to the Amazon PA-API.
	 *
	 * @since 1.0.0
	 *
	 * @param string $operation  The API operation (e.g., 'SearchItems', 'GetItems').
	 * @param array  $payload    The request payload (will be JSON-encoded).
	 * @return array|WP_Error    Decoded response body or WP_Error on failure.
	 */
	public function send_request( $operation, $payload ) {
		$path       = '/paapi5/' . strtolower( $operation );
		$payload_json = wp_json_encode( $payload );

		if ( false === $payload_json ) {
			return new \WP_Error( 'azon_mate_json_encode_error', __( 'Failed to encode request payload.', 'azonmate' ) );
		}

		$amz_target = Marketplace::get_amz_target( $operation );

		// Current timestamp in ISO 8601 format.
		$timestamp  = gmdate( 'Ymd\THis\Z' );
		$date_stamp = gmdate( 'Ymd' );

		// Build headers.
		$headers = array(
			'content-encoding' => 'amz-1.0',
			'content-type'     => 'application/json; charset=UTF-8',
			'host'             => $this->host,
			'x-amz-date'       => $timestamp,
			'x-amz-target'     => $amz_target,
		);

		// Create canonical request.
		$canonical_request = $this->create_canonical_request( 'POST', $path, $headers, $payload_json );

		// Create string to sign.
		$credential_scope = $date_stamp . '/' . $this->region . '/' . $this->service . '/aws4_request';
		$string_to_sign   = $this->create_string_to_sign( $timestamp, $credential_scope, $canonical_request );

		// Calculate signature.
		$signing_key = $this->get_signing_key( $date_stamp );
		$signature   = hash_hmac( 'sha256', $string_to_sign, $signing_key );

		// Build authorization header.
		$signed_headers = $this->get_signed_headers( $headers );
		$authorization  = sprintf(
			'AWS4-HMAC-SHA256 Credential=%s/%s, SignedHeaders=%s, Signature=%s',
			$this->access_key,
			$credential_scope,
			$signed_headers,
			$signature
		);

		// Prepare WordPress HTTP request args.
		$request_headers = array(
			'Content-Type'     => 'application/json; charset=UTF-8',
			'Content-Encoding' => 'amz-1.0',
			'X-Amz-Date'       => $timestamp,
			'X-Amz-Target'     => $amz_target,
			'Authorization'    => $authorization,
			'Host'             => $this->host,
		);

		$url = 'https://' . $this->host . $path;

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
		if ( isset( $decoded['Errors'] ) ) {
			$api_error = $decoded['Errors'][0];
			$error_msg = isset( $api_error['Message'] ) ? $api_error['Message'] : __( 'Unknown API error', 'azonmate' );
			return new \WP_Error( 'azon_mate_api_error', $error_msg );
		}

		return $decoded;
	}

	/**
	 * Create the canonical request string.
	 *
	 * @since 1.0.0
	 *
	 * @param string $method  HTTP method.
	 * @param string $path    Request path.
	 * @param array  $headers Request headers.
	 * @param string $payload Request payload.
	 * @return string
	 */
	private function create_canonical_request( $method, $path, $headers, $payload ) {
		// Sort headers by lowercase key name.
		$canonical_headers = '';
		$sorted_headers    = array();

		foreach ( $headers as $key => $value ) {
			$sorted_headers[ strtolower( $key ) ] = trim( $value );
		}
		ksort( $sorted_headers );

		foreach ( $sorted_headers as $key => $value ) {
			$canonical_headers .= $key . ':' . $value . "\n";
		}

		$signed_headers = implode( ';', array_keys( $sorted_headers ) );
		$payload_hash   = hash( 'sha256', $payload );

		$canonical_request = implode( "\n", array(
			$method,
			$path,
			'', // Empty query string.
			$canonical_headers,
			$signed_headers,
			$payload_hash,
		) );

		return $canonical_request;
	}

	/**
	 * Create the string to sign.
	 *
	 * @since 1.0.0
	 *
	 * @param string $timestamp        ISO 8601 timestamp.
	 * @param string $credential_scope Credential scope string.
	 * @param string $canonical_request The canonical request.
	 * @return string
	 */
	private function create_string_to_sign( $timestamp, $credential_scope, $canonical_request ) {
		return implode( "\n", array(
			'AWS4-HMAC-SHA256',
			$timestamp,
			$credential_scope,
			hash( 'sha256', $canonical_request ),
		) );
	}

	/**
	 * Derive the signing key.
	 *
	 * @since 1.0.0
	 *
	 * @param string $date_stamp Date stamp (Ymd).
	 * @return string Binary signing key.
	 */
	private function get_signing_key( $date_stamp ) {
		$k_date    = hash_hmac( 'sha256', $date_stamp, 'AWS4' . $this->secret_key, true );
		$k_region  = hash_hmac( 'sha256', $this->region, $k_date, true );
		$k_service = hash_hmac( 'sha256', $this->service, $k_region, true );
		$k_signing = hash_hmac( 'sha256', 'aws4_request', $k_service, true );
		return $k_signing;
	}

	/**
	 * Get the signed headers string.
	 *
	 * @since 1.0.0
	 *
	 * @param array $headers Request headers.
	 * @return string Semicolon-delimited list of signed header names.
	 */
	private function get_signed_headers( $headers ) {
		$keys = array();
		foreach ( array_keys( $headers ) as $key ) {
			$keys[] = strtolower( $key );
		}
		sort( $keys );
		return implode( ';', $keys );
	}
}
