<?php
/**
 * Multicurrency plugins integrations.
 *
 * @package Flexible Shipping
 */

use FSVendor\WPDesk\WooCommerce\CurrencySwitchers\Switcher\WooCommerceMultiCurrency;

/**
 * Can integrate Flexible Shipping with multicurrency plugins.
 */
class WPDesk_Flexible_Shipping_Multicurrency {

	/**
	 * @var Flexible_Shipping_Plugin
	 */
	private $plugin;

	/**
	 * WPDesk_Flexible_Shipping_Multicurrency constructor.
	 *
	 * @param Flexible_Shipping_Plugin $plugin .
	 */
	public function __construct( Flexible_Shipping_Plugin $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * @TODO move this functionality to wpdesk/wc-currency-switchers-integrations library.
	 */
	private function hooks() {
		add_filter( 'flexible_shipping_value_in_currency', array( $this, 'flexible_shipping_value_in_currency_wpml' ), 1 );

		if ( class_exists( 'Aelia\WC\CurrencySwitcher\WC_Aelia_CurrencySwitcher' ) ) {
			add_filter( 'flexible_shipping_value_in_currency', array( $this, 'flexible_shipping_value_in_currency_aelia_namespaces' ), 0 );
		} elseif ( class_exists( 'WC_Aelia_CurrencySwitcher' ) ) {
			add_filter( 'flexible_shipping_value_in_currency', array( $this, 'flexible_shipping_value_in_currency_aelia' ), 1 );
		}

		if ( function_exists( 'wmcs_convert_price' ) ) {
			add_filter( 'flexible_shipping_value_in_currency', array( $this, 'flexible_shipping_value_in_currency_wmcs' ), 1 );
		}

		if ( isset( $GLOBALS['WOOCS'] ) ) {
			add_filter( 'flexible_shipping_value_in_currency', array( $this, 'flexible_shipping_value_in_currency_woocs' ), 1 );
		}

		if ( class_exists( '\WOOMC\Price\Controller' ) ) {
			add_filter( 'flexible_shipping_value_in_currency', array( $this, 'flexible_shipping_value_in_currency_woomc' ), 1 );
		}
	}

	/**
	 * @param float $value .
	 *
	 * @return float
	 */
	public function flexible_shipping_value_in_currency_aelia( $value ) {
		$aelia          = WC_Aelia_CurrencySwitcher::instance();
		$aelia_settings = WC_Aelia_CurrencySwitcher::settings();
		$from_currency  = $aelia_settings->base_currency();
		$to_currency    = $aelia->get_selected_currency();
		$value          = $aelia->convert( $value, $from_currency, $to_currency );

		return $value;
	}

	/**
	 * @param float $value .
	 *
	 * @return float
	 */
	public function flexible_shipping_value_in_currency_aelia_namespaces( $value ) {
		$aelia          = Aelia\WC\CurrencySwitcher\WC_Aelia_CurrencySwitcher::instance();
		$aelia_settings = Aelia\WC\CurrencySwitcher\WC_Aelia_CurrencySwitcher::settings();
		$from_currency  = $aelia_settings->base_currency();
		$to_currency    = $aelia->get_selected_currency();
		$value          = $aelia->convert( $value, $from_currency, $to_currency );

		return $value;
	}

	/**
	 * @param float $value .
	 *
	 * @return float
	 */
	public function flexible_shipping_value_in_currency_wmcs( $value ) {
		$value = wmcs_convert_price( $value );

		return $value;
	}

	/**
	 * @param float $value .
	 *
	 * @return float
	 */
	public function flexible_shipping_value_in_currency_wpml( $value ) {
		return apply_filters( 'wcml_raw_price_amount', $value );
	}

	/**
	 * @param float $value .
	 *
	 * @return float
	 */
	public function flexible_shipping_value_in_currency_woocs( $value ) {
		return $GLOBALS['WOOCS']->woocs_exchange_value( $value );
	}

	/**
	 * Convert value using WooCommerce Multicurrency switcher.
	 *
	 * @see https://woocommerce.com/products/multi-currency/
	 *
	 * @param float $value .
	 *
	 * @return float
	 */
	public function flexible_shipping_value_in_currency_woomc( $value ) {
		$converter = new WooCommerceMultiCurrency\Converter();

		return $converter->convert( $value );
	}

}
