<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit();
}
/**
 * Class WC_Vivawallet_Helper
 *
 * @class   WC_Vivawallet_Helper
 * @package WooCommerce/WC_Vivawallet_Helper
 */
class WC_Vivawallet_Helper {

	const BASE_URL_LIVE = 'https://www.vivapayments.com';
	const BASE_URL_TEST = 'https://demo.vivapayments.com';

	const BASE_URL_API_LIVE = 'https://api.vivapayments.com';
	const BASE_URL_API_TEST = 'https://demo-api.vivapayments.com';

	const URL_GET_TOKEN_LIVE = 'https://accounts.vivapayments.com/connect/token';
	const URL_GET_TOKEN_TEST = 'https://demo-accounts.vivapayments.com/connect/token';


	const ENDPOINT_NATIVE_JS = '/web/checkout/v2/js';

	const NATIVE_JS_VERSION = '2.0';

	const SOURCE_IDENTIFIER = 'WC-';

	const ENDPOINT_INSTALLMENTS = '/nativecheckout/v2/installments';

	const ENDPOINT_GET_SOURCES = '/plugins/v1/sources';

	const ENDPOINT_TRANSACTIONS = '/nativecheckout/v2/transactions';

	const POST_META_VW_ORDER_REF = '_vivawallet_order_reference';


	const POST_META_VW_TXN = '_vivawallet_order_transaction_id';


	const POST_META_VW_ORDER_STATUS = '_vivawallet_order_payment_status';


	const POST_META_VW_REFUND_DATA = '_vivawallet_order_refund_data';


	const POST_META_WC_ORDER_PAID = '_paid_date';


	const ORDER_STATUS_PENDING = 'pending';


	const ORDER_STATUS_REFUNDED = 'refunded';


	const ORDER_STATUS_ON_HOLD = 'on-hold';


	const ORDER_STATUS_PROCESSING = 'processing';


	const ORDER_STATUS_CANCELLED = 'cancelled';


	const ORDER_STATUS_COMPLETE = 'completed';


	const VW_ORDER_STATUS_I = 'I';


	const VW_ORDER_STATUS_P = 'P';


	const VW_ORDER_STATUS_F = 'F';


	const DEFAULT_LOCALE = 'en-US';


	const ALLOWED_LOCALES = array(
		'el-GR',
		'en-US',
	);

	// todo fix the links to perm values..
	const VW_CHECKOUT_PAYMENT_LOGOS_URL = 'https://images.prismic.io/vivawallet/331cc7de-f5a3-4120-861b-bb65ec068195_vw.svg';
	const VW_CC_LOGOS_URL               = 'https://images.prismic.io/vivawallet/464ed2b5-00ed-4eac-957d-2ff943150c8f_logos.png';
	const VW_LOGO_URL                   = 'https://images.prismic.io/vivawallet/1a64b4db-f5ad-4a4a-848a-a06954ebbe99_vivawallet-logo.svg';

	const ALLOWED_CURRENCIES = array(
		'GBP',
		'BGN',
		'RON',
		'EUR',
		'PLN',
	);

	/**
	 * Get_url_token
	 *
	 * @param string $is_test_mode test mode.

	 * @return string
	 */
	public static function get_token_url( $is_test_mode ) {
		if ( 'yes' === $is_test_mode ) {
			return self::URL_GET_TOKEN_TEST;
		} else {
			return self::URL_GET_TOKEN_LIVE;
		}
	}


	/**
	 * Get_base_url
	 *
	 * @param string $is_test_mode Is test mode.
	 *
	 * @return string
	 */
	public static function get_base_url( $is_test_mode ) {
		if ( 'yes' === $is_test_mode ) {
			return self::BASE_URL_TEST;
		} else {
			return self::BASE_URL_LIVE;
		}
	}


	/**
	 * Get_api_url
	 *
	 * @param string $is_test_mode Is test mode.
	 *
	 * @return string
	 */
	public static function get_api_url( $is_test_mode ) {
		if ( 'yes' === $is_test_mode ) {
			return self::BASE_URL_API_TEST;
		} else {
			return self::BASE_URL_API_LIVE;
		}
	}


	/**
	 * Get_api_url_endpoint
	 * returns url for api calls
	 *
	 * @param string $is_test_mode test mode.
	 *
	 * @param string $endpoint url.
	 *
	 * @return string
	 */
	public static function get_api_url_endpoint( $is_test_mode, $endpoint ) {

		switch ( $endpoint ) {
			case self::ENDPOINT_TRANSACTIONS:
				return self::get_api_url( $is_test_mode ) . self::ENDPOINT_TRANSACTIONS;
			case self::ENDPOINT_INSTALLMENTS:
				return self::get_api_url( $is_test_mode ) . self::ENDPOINT_INSTALLMENTS;
			case self::ENDPOINT_NATIVE_JS:
				return self::get_base_url( $is_test_mode ) . self::ENDPOINT_NATIVE_JS;
			case self::ENDPOINT_GET_SOURCES:
				return self::get_api_url( $is_test_mode ) . self::ENDPOINT_GET_SOURCES;

		}
	}

	/**
	 * Get_currency_symbol
	 * get woocommerce currency and convert to vw type
	 *
	 * @param string $currency_code woocommerce currency type.
	 *
	 * @return int
	 */
	public static function get_currency_symbol( $currency_code ) {
		switch ( $currency_code ) {
			case 'EUR':
				$currency_symbol = 978;
				break;
			case 'GBP':
				$currency_symbol = 826;
				break;
			case 'BGN':
				$currency_symbol = 975;
				break;
			case 'RON':
				$currency_symbol = 946;
				break;
			case 'PLN':
				$currency_symbol = 985;
				break;
			default:
				$currency_symbol = 978;
		}
		return $currency_symbol;
	}

	/**
	 * Get token
	 *
	 * @param string $client_id client_id.
	 *
	 * @param string $client_secret client_secret.
	 *
	 * @param string $test_mode test_mode.
	 *
	 * @param string $scope scope.
	 *
	 * @return bool|string
	 */
	public static function get_token( $client_id, $client_secret, $test_mode, $scope ) {

		$url = self::get_token_url( $test_mode );

		$front_scope = 'urn:viva:payments:core:api:nativecheckoutv2';

		$back_end_scope = 'urn:viva:payments:core:api:plugins urn:viva:payments:core:api:nativecheckoutv2 urn:viva:payments:core:api:plugins:woocommerce';

		if ( 'back' === $scope ) {
			$scope = $back_end_scope;
		} else {
			$scope = $front_scope;
		}

		if ( ! isset( $client_id ) && 0 === strlen( $client_id ) && ! isset( $client_secret ) && 0 === strlen( $client_secret ) ) {
			return false;
		}

		$header_args = array(
			'Accept'        => 'application/json',
			'Authorization' => 'Basic ' . base64_encode( $client_id . ':' . $client_secret ),
		);

		$post_args = array(
			'grant_type' => 'client_credentials',
			'scope'      => $scope,
		);

		$result = wp_remote_post(
			$url,
			array(
				'headers'     => $header_args,
				'body'        => $post_args,
				'httpversion' => '1.0',
				'method'      => 'POST',
			)
		);

		if ( isset( $result->errors ) ) {
			if ( function_exists( 'wc_add_notice' ) ) {
				wc_add_notice( __( 'There was a connection problem with Viva Wallet API services. Please try again later.', 'woocommerce_vivawallet' ), 'error' );
			}
			WC_Admin_Settings::add_error( __( 'There was a connection problem with Viva Wallet API services. Please try again later.', 'woocommerce_vivawallet' ) );
			return false;
		} else {
			$result = json_decode( $result['body'] );
			if ( isset( $result->access_token ) ) {
				return $result->access_token;
			} else {
				return false;
			}
		}
	}



	/**
	 * Do create transaction api call
	 *
	 * @param array  $order order.
	 * @param string $access_token access_token.
	 * @param string $test_mode test_mode.
	 * @param string $source_code source_code.
	 * @param string $charge_token charge_token.
	 * @param string $installments_post_val installments_post_val.
	 *
	 * @return array|WP_Error
	 */
	public static function transaction_api_call( $order, $access_token, $test_mode, $source_code, $charge_token, $installments_post_val ) {

		$url      = self::get_api_url_endpoint( $test_mode, self::ENDPOINT_TRANSACTIONS );
		$amount   = $order->get_total();
		$amount   = floatval( $amount ) * 100;
		$currency = $order->get_currency();
		$currency = self::get_currency_symbol( $currency );
		$name     = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
		$phone    = $order->get_billing_phone();
		$phone    = preg_replace( '/[^0-9]/', '', $phone ); // clean up phone number..

		if ( strlen( $phone ) <= 1 ) {
			$phone = '0111111111';  // inject default value if empty or has a value of 1.
		}

		$email   = $order->get_billing_email();
		$ln      = get_locale();
		$country = $order->get_billing_country();

		$site_url = get_site_url();

		$domain = wp_parse_url( $site_url, PHP_URL_HOST );

		$merchant_message = $domain . ' - ' . $order->get_payment_method_title() . ' - ' . $order->get_order_number();
		$customer_message = $domain . ' - ' . $order->get_payment_method_title() . ' - ' . $order->get_order_number();

		if ( isset( $ln ) && strlen( $ln ) > 2 ) {
			$ln = substr( $ln, 0, 2 );
		} else {
			$ln = 'en'; // fallback to en if the lang is not properly defined in wp.
		}

		$header_args = array(
			'Authorization' => 'Bearer ' . $access_token,
			'Accept'        => 'application/json',
			'Content-Type'  => 'application/json',
		);

		$post_args = array(
			'amount'       => $amount,
			'preauth'      => false,
			'sourceCode'   => $source_code,
			'chargeToken'  => $charge_token,
			'installments' => $installments_post_val,
			'merchantTrns' => $merchant_message,
			'customerTrns' => $customer_message,
			'currencyCode' => $currency,
			'customer'     => array(
				'email'       => $email,
				'phone'       => $phone,
				'fullname'    => $name,
				'requestLang' => $ln,
			),
		);

		$result = wp_remote_post(
			$url,
			array(
				'headers'     => $header_args,
				'body'        => wp_json_encode( $post_args ),
				'httpversion' => '1.0',
				'method'      => 'POST',
			)
		);

		return $result;

	}


	/**
	 * Get sources
	 *
	 * @param string $bearer bearer token.
	 *
	 * @param string $test_mode test mode.
	 *
	 * @return  array|string
	 */
	public static function get_sources( $bearer, $test_mode ) {

		$url = self::get_api_url_endpoint( $test_mode, self::ENDPOINT_GET_SOURCES );

		$header_args = array(
			'Accept'        => 'application/json',
			'Authorization' => 'Bearer ' . $bearer,
		);
		$result      = wp_remote_request(
			$url,
			array(
				'headers'     => $header_args,
				'httpversion' => '1.0',
				'method'      => 'GET',
			)
		);

		if ( isset( $result['response']['code'] ) ) {
			if ( 200 === $result['response']['code'] ) { // sources found.
				$result = json_decode( $result['body'] );
				return $result;
			}
		}
		return 'error';
	}


	/**
	 * Check source
	 *
	 * @param string $bearer bearer token.
	 *
	 * @param string $source source.
	 *
	 * @param string $test_mode test mode.
	 *
	 * @return  string
	 */
	public static function check_source( $bearer, $source, $test_mode ) {
		$url         = self::get_api_url_endpoint( $test_mode, self::ENDPOINT_GET_SOURCES ) . '?sourceCode=' . $source;
		$header_args = array(
			'Accept'        => 'application/json',
			'Authorization' => 'Bearer ' . $bearer,
		);
		$result      = wp_remote_request(
			$url,
			array(
				'headers'     => $header_args,
				'httpversion' => '1.0',
				'method'      => 'GET',
			)
		);

		if ( isset( $result['response']['code'] ) ) {
			if ( 200 === $result['response']['code'] ) { // source found.
				$result = json_decode( $result['body'] );
				if ( isset( $result[0]->state ) ) {
					if ( 1 === $result[0]->state ) {
						return 'Active';
					} elseif ( 2 === $result[0]->state ) {
						return 'Pending';
					} elseif ( 0 === $result[0]->state ) {
						return 'InActive';
					}
				}
			}
		}
		return 'error';

		/*
			Active = 1,
			Pending = 2,
			InProgress = 3
		*/
	}

	/**
	 * Is_valid_domain_name
	 *
	 * @param string $url url.
	 *
	 * @return  boolean
	 */
	public static function is_valid_domain_name( $url ) {
		return ( preg_match( '/^(?!\-)(?:(?:[a-zA-Z\d][a-zA-Z\d\-]{0,61})?[a-zA-Z\d]\.){1,126}(?!\d+)[a-zA-Z\d]{1,63}$/', $url ) );
	}


	/**
	 * Is valid currency
	 *
	 * @return bool
	 */
	public static function is_valid_currency() {
		return in_array( get_woocommerce_currency(), self::ALLOWED_CURRENCIES, true ) ? true : false;
	}



}
