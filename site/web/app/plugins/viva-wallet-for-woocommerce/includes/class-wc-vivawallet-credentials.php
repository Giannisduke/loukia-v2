<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}
/**
 * Class WC_Vivawallet_Helper
 *
 * @class   WC_Vivawallet_Helper
 * @package WooCommerce/WC_Vivawallet_Credentials
 */
class WC_Vivawallet_Credentials {

	/**
	 * Get credentials
	 *
	 * @param string $scope scope.
	 * @param string $test_mode test_mode.
	 * @param string $client_id client_id.
	 * @param string $client_secret client_secret.
	 * @param string $source_code source_code.
	 *
	 * @return array
	 */
	public static function get_credentials( $scope, $test_mode, $client_id, $client_secret, $source_code ) {
		return array(
			'test_mode'     => $test_mode,
			'client_id'     => $client_id,
			'client_secret' => $client_secret,
			'source_code'   => $source_code,
			'token'         => WC_Vivawallet_Helper::get_token( $client_id, $client_secret, $test_mode, $scope ),
		);
	}

}
