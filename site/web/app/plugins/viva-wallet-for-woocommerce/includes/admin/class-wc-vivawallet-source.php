<?php


if ( ! defined( 'ABSPATH' ) ) {
	exit();
}


/**
 * WC_Vivawallet_Source
 */
class WC_Vivawallet_Source {

	/**
	 * Create source
	 *
	 * @param array  $credentials credentials.
	 *
	 * @param string $source_code source_code.
	 *
	 * @return string
	 */
	public static function create_source( $token, $source_code, $test_mode ) {
		$site_url = get_site_url();
		$domain   = wp_parse_url( $site_url, PHP_URL_HOST );
		$name     = $source_code;

		$body = array(
			'domain'     => $domain,
			'sourceCode' => $name,
			'name'       => 'Viva Wallet For WC - ' . $domain,
		);

		$args = array(
			'body'    => wp_json_encode( $body ),
			'headers' => array(
				'Authorization' => 'Bearer ' . $token,
				'Content-Type'  => 'application/json',
			),
		);

		$url = WC_Vivawallet_Helper::get_api_url_endpoint( $test_mode, WC_Vivawallet_Helper::ENDPOINT_GET_SOURCES );

		$response = wp_remote_post( $url, $args );

		if ( 204 === $response['response']['code'] ) {
			return 'yes';
		} else {
			$res = json_decode( $response['body'] );
			return $res->message;
		}
	}
}
