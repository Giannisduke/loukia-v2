<?php

namespace WCML\Rest;

use WPML_URL_Filters;

class Functions {

	/**
	 * Check if is request to the WooCommerce REST API.
	 *
	 * @return bool
	 */
	public static function isRestApiRequest() {
		return apply_filters( 'woocommerce_rest_is_request_to_rest_api', self::checkEndpoint( 'wc/v' . self::getApiRequestVersion() . '/' ) );
	}

	/**
	 * Check if is request to the WooCommerce Analytics REST API.
	 *
	 * @return bool
	 */
	public static function isAnalyticsRestRequest() {
		return apply_filters( 'woocommerce_rest_is_request_to_analytics_api', self::checkEndpoint( 'wc-analytics/' ) );
	}

	/**
	 * @return int
	 * Returns the version number of the API used for the current request
	 */
	public static function getApiRequestVersion() {

		$version = 0;

		if ( empty( $_SERVER['REQUEST_URI'] ) ) {
			return $version;
		}

		$restPrefix = trailingslashit( rest_get_url_prefix() );
		if ( preg_match( "@" . $restPrefix . "wc/v([0-9]+)/@", $_SERVER['REQUEST_URI'], $matches ) ) {
			$version = intval( $matches[1] );
		}

		return $version;
	}

	/**
	 * @param string $endpoint
	 *
	 * @return bool
	 */
	private static function checkEndpoint( $endpoint = 'wc/' ) {
		if ( empty( $_SERVER['REQUEST_URI'] ) ) {
			return false;
		}

		$rest_prefix = trailingslashit( rest_get_url_prefix() );
		return ( false !== strpos( $_SERVER['REQUEST_URI'], $rest_prefix . $endpoint ) );
	}

	/**
	 * Use url without the language parameter. Needed for the signature match.
	 */
	public static function removeWpmlGlobalUrlFilters() {
		global $wpml_url_filters;
		remove_filter( 'home_url', [ $wpml_url_filters, 'home_url_filter' ], - 10 );
	}
}
