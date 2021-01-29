<?php
/**
 * Free Shipping Notice Generator.
 *
 * @package WPDesk\FS\TableRate\FreeShipping
 */

namespace WPDesk\FS\TableRate\FreeShipping;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;

/**
 * Can generate free shipping notice and save it on session.
 */
class FreeShippingNoticeGenerator implements Hookable {

	const SETTING_METHOD_FREE_SHIPPING = 'method_free_shipping';
	const SESSION_VARIABLE = 'flexible_shipping_free_shipping_notice';
	const META_DATA_FS_METHOD = '_fs_method';

	/**
	 * @var \WC_Cart
	 */
	private $cart;

	/**
	 * @var \WC_Session
	 */
	private $session;

	/**
	 * FreeShippingNotice constructor.
	 *
	 * @param \WC_Cart    $cart .
	 * @param \WC_Session $session .
	 */
	public function __construct( $cart, $session ) {
		$this->cart = $cart;
		$this->session = $session;
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
		add_filter( 'woocommerce_package_rates', array( $this, 'add_free_shipping_notice_if_should' ), 10, 2 );
	}

	/**
	 * @param array $package_rates .
	 * @param array $package .
	 *
	 * @internal Triggered by filter. Must return $package_rates.
	 */
	public function add_free_shipping_notice_if_should( $package_rates, $package ) {
		if ( $this->cart->needs_shipping() && $this->has_shipping_rate_with_free_shipping( $package_rates ) && ! $this->has_free_shipping_rate( $package_rates ) ) {
			$this->add_free_shipping_notice_to_session( $package_rates );
		} else {
			$this->session->set( self::SESSION_VARIABLE, '' );
		}

		return $package_rates;
	}

	/**
	 * Add free shipping notice.
	 *
	 * @param array $package_rates .
	 */
	private function add_free_shipping_notice_to_session( $package_rates ) {
		$lowest_free_shipping_limit = $this->get_lowest_free_shipping_limit( $package_rates );
		$amount = $lowest_free_shipping_limit - $this->get_cart_value();

		$this->session->set( self::SESSION_VARIABLE, $this->prepare_notice_text( $amount ) );
	}

	/**
	 * @param float $amount .
	 *
	 * @return string
	 */
	private function prepare_notice_text( $amount ) {
		$notice_text = sprintf(
			// Translators: cart value and shop link.
			__( 'You only need %1$s more to get free shipping! %2$sContinue shopping%3$s', 'flexible-shipping' ),
			wc_price( $amount ),
			'<a class="button" href="' . esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ) . '">',
			'</a>'
		);

		/**
		 * Notice text for Free Shipping.
		 *
		 * @param string $notice_text Notice text.
		 * @param float  $amount Amount left to free shipping.
		 *
		 * @return string Message text.
		 */
		return apply_filters( 'flexible_shipping_free_shipping_notice_text', $notice_text, $amount );
	}

	/**
	 * Has package free shipping rate?
	 *
	 * @param array $package_rates .
	 *
	 * @return bool
	 */
	private function has_free_shipping_rate( $package_rates ) {
		/** @var \WC_Shipping_Rate $package_rate */
		foreach ( $package_rates as $package_rate ) {
			if ( floatval( $package_rate->get_cost() ) === 0.0 && ! $this->is_excluded_shipping_method( $package_rate->get_method_id() ) ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Is shipping method excluded from free shipping?
	 *
	 * @param string $method_id .
	 *
	 * @return bool
	 */
	private function is_excluded_shipping_method( $method_id ) {
		/**
		 * Exclude methods from free shipping.
		 *
		 * @param array $excluded_methods
		 *
		 * @return array
		 */
		$excluded_methods = apply_filters( 'flexible_shipping_free_shipping_notice_excluded_methods', array( 'local_pickup' ) );
		return in_array(
			$method_id,
			$excluded_methods,
			true
		);
	}

	/**
	 * Has package rate with free shipping?
	 *
	 * @param array $package_rates .
	 *
	 * @return bool
	 */
	private function has_shipping_rate_with_free_shipping( $package_rates ) {
		/** @var \WC_Shipping_Rate $package_rate */
		foreach ( $package_rates as $package_rate ) {
			if ( $package_rate->get_method_id() === \WPDesk_Flexible_Shipping::METHOD_ID ) {
				$meta_data = $package_rate->get_meta_data();
				if ( isset( $meta_data[ self::META_DATA_FS_METHOD ] ) ) {
					if ( $this->has_shipping_method_free_shipping_notice_enabled( $meta_data[ self::META_DATA_FS_METHOD ] ) ) {
						return true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * @param array $fs_method .
	 *
	 * @return bool
	 */
	private function has_shipping_method_free_shipping_notice_enabled( array $fs_method ) {
		return ! empty( $fs_method[ self::SETTING_METHOD_FREE_SHIPPING ] )
			&& isset( $fs_method[ \WPDesk_Flexible_Shipping::SETTING_METHOD_FREE_SHIPPING_NOTICE ] )
			&& 'yes' === $fs_method[ \WPDesk_Flexible_Shipping::SETTING_METHOD_FREE_SHIPPING_NOTICE ]
			&& apply_filters( 'flexible-shipping/shipping-method/free-shipping-notice-allowed', true, $fs_method );
	}

	/**
	 * Returns current cart value.
	 *
	 * @return float
	 */
	private function get_cart_value() {
		return $this->cart->display_prices_including_tax() ? $this->cart->get_cart_contents_total() + $this->cart->get_cart_contents_tax() : $this->cart->get_cart_contents_total();
	}

	/**
	 * Returns lowest free shipping limit from available rates.
	 *
	 * @param array $package_rates .
	 *
	 * @return float
	 */
	private function get_lowest_free_shipping_limit( $package_rates ) {
		$lowest_free_shipping_limit = null;
		/** @var \WC_Shipping_Rate $package_rate */
		foreach ( $package_rates as $package_rate ) {
			if ( $this->is_package_rate_from_flexible_shipping( $package_rate ) ) {
				$meta_data = $package_rate->get_meta_data();
				$fs_method = isset( $meta_data[ self::META_DATA_FS_METHOD ] ) ? $meta_data[ self::META_DATA_FS_METHOD ] : array();
				if ( $this->has_shipping_method_free_shipping_notice_enabled( $fs_method ) ) {
					$method_free_shipping_limit = round( floatval( $fs_method[ self::SETTING_METHOD_FREE_SHIPPING ] ), wc_get_rounding_precision() );
					$lowest_free_shipping_limit = min(
						$method_free_shipping_limit,
						null === $lowest_free_shipping_limit ? $method_free_shipping_limit : $lowest_free_shipping_limit
					);
				}
			}
		}

		return $lowest_free_shipping_limit;
	}

	/**
	 * @param \WC_Shipping_Rate $package_rate .
	 *
	 * @return bool
	 */
	private function is_package_rate_from_flexible_shipping( \WC_Shipping_Rate $package_rate ) {
		return $package_rate->get_method_id() === \WPDesk_Flexible_Shipping::METHOD_ID;
	}

}
