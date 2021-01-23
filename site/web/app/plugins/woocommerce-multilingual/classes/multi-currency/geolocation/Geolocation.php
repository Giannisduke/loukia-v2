<?php

namespace WCML\MultiCurrency;

use WPML\FP\Obj;

class Geolocation {

	const DEFAULT_COUNTRY_CURRENCY_CONFIG = 'country-currency.json';
	const MODE_BY_LANGUAGE = 'by_language';
	const MODE_BY_LOCATION = 'by_location';

	/**
	 * Get country code by user IP
	 *
	 * @return string
	 */
	private static function getCountryByUserIp() {

		$ip = \WC_Geolocation::get_ip_address();

		$country_info = \WC_Geolocation::geolocate_ip( $ip, true );

		return isset( $country_info['country'] ) ? $country_info['country'] : '';
	}

	/**
	 * Get country currency config file
	 *
	 * @return array
	 */
	private static function parseConfigFile() {
		$config             = [];
		$configuration_file = WCML_PLUGIN_PATH . '/res/geolocation/' . self::DEFAULT_COUNTRY_CURRENCY_CONFIG;

		if ( file_exists( $configuration_file ) ) {
			$json_content = file_get_contents( $configuration_file );
			$config       = json_decode( $json_content, true );
		}

		return $config;
	}

	/**
	 * Get currency code by user country
	 *
	 * @return string|bool
	 */
	public static function getCurrencyCodeByUserCountry() {

		$country = self::getUserCountry();

		if ( $country ) {
			$config = self::parseConfigFile();

			return isset( $config[ $country ] ) ? $config[ $country ] : false;
		}

		return false;
	}

	/**
	 * Get first available country currency from settings if default country currency not active
	 *
	 * @param array $currenciesSettings
	 *
	 * @return string|bool
	 */
	public static function getFirstAvailableCountryCurrencyFromSettings( $currenciesSettings ) {

		$currency = false;

		wpml_collect( $currenciesSettings )->each( function ( $settings, $code ) use ( &$currency ) {
			if ( self::isCurrencyAvailableForCountry( $settings ) ) {
				$currency = $code;
			}
		} );

		return $currency;
	}

	/**
	 * @return string
	 */
	public static function getUserCountry(){

		if ( defined( 'WCML_GEOLOCATED_COUNTRY' ) ) {
			return WCML_GEOLOCATED_COUNTRY;
		}

		$allUserCountries = [
			'billing'     => self::getUserCountryByAddress( 'billing' ),
			'shipping'    => self::getUserCountryByAddress( 'shipping' ),
			'geolocation' => self::getCountryByUserIp()
		];
		$userCountry      = $allUserCountries['billing'] ?: $allUserCountries['geolocation'];

		/**
		 * This filter allows to override the address country declared by the user.
		 *
		 * @since 4.11.0
		 *
		 * @param string $userCountry Billing address used if set otherwise geolocation country used.
		 * @param array  $allUserCountries {
		 *      @type string $billing The billing address country
		 *      @type string $shipping The shipping address country
		 *      @type string $geolocation The geolocation country
		 * }
		 *
		 * @return string
		 */
		return apply_filters( 'wcml_geolocation_get_user_country', $userCountry, $allUserCountries );
	}

	/**
	 * Get country code from address if user logged-in.
	 *
	 * @param string $addressType Shipping or Billing address.
	 *
	 * @return string
	 */
	private static function getUserCountryByAddress( $addressType ){

		$orderCountry = self::getUserCountryFromOrder( $addressType );
		if( $orderCountry ){
			return $orderCountry;
		}

		$current_user_id = get_current_user_id();

		if ( $current_user_id ) {
			$customer = new \WC_Customer( $current_user_id, WC()->session ? true : false );

			return 'shipping' === $addressType ? $customer->get_shipping_country() : $customer->get_billing_country();
		}

		return '';
	}

	/**
	 * Get country code from order based on address.
	 *
	 * @param string $addressType Shipping or Billing address.
	 *
	 * @return string
	 */
	private static function getUserCountryFromOrder( $addressType ) {

		$country = '';
		$wcAjax  = Obj::prop( 'wc-ajax', $_GET );

		if ( 'update_order_review' === $wcAjax && isset( $_POST['country'] ) ) {
			$country = $_POST['country'];
		} elseif ( 'checkout' === $wcAjax && isset( $_POST[ $addressType . '_country' ] ) ) {
			$country = $_POST[ $addressType . '_country' ];
		}

		return wc_clean( wp_unslash( $country ) );
	}

	/**
	 * @param array $currencySettings
	 *
	 * @return bool
	 */
	public static function isCurrencyAvailableForCountry( $currencySettings ) {

		if ( isset( $currencySettings['location_mode'] ) ) {

			if ( 'all' === $currencySettings['location_mode'] ) {
				return true;
			}

			if ( 'include' === $currencySettings['location_mode'] && in_array( self::getUserCountry(), $currencySettings['countries'] ) ) {
				return true;
			}

			if ( 'exclude' === $currencySettings['location_mode'] && ! in_array( self::getUserCountry(), $currencySettings['countries'] ) ) {
				return true;
			}

		}

		return false;
	}
}