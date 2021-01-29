<?php
/**
 * Free Shipping Notice.
 *
 * @package WPDesk\FS\TableRate\FreeShipping
 */

namespace WPDesk\FS\TableRate\FreeShipping;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use WC_Cart;
use WC_Session;

/**
 * Can display free shipping notice.
 */
class FreeShippingNotice implements Hookable {

	const FLEXIBLE_SHIPPING_FREE_SHIPPING_NOTICE = 'flexible_shipping_free_shipping_notice';
	const NOTICE_TYPE_SUCCESS = 'success';

	/**
	 * @var \WC_Cart
	 */
	private $cart;

	/**
	 * @var WC_Session
	 */
	private $session;

	/**
	 * FreeShippingNotice constructor.
	 *
	 * @param WC_Cart    $cart    .
	 * @param WC_Session $session .
	 */
	public function __construct( WC_Cart $cart, WC_Session $session ) {
		$this->cart    = $cart;
		$this->session = $session;
	}

	/**
	 * Hooks.
	 */
	public function hooks() {
		// Checkout.
		add_action( 'woocommerce_before_checkout_form', array( $this, 'add_notice_container' ), 20 );
		add_filter( 'woocommerce_update_order_review_fragments', array( $this, 'add_notice_to_fragments' ) );

		// Cart.
		add_action( 'woocommerce_after_calculate_totals', array( $this, 'add_notice_to_cart' ) );
	}

	/**
	 * Add notice to fragments.
	 *
	 * @param array $fragments .
	 *
	 * @return array
	 */
	public function add_notice_to_fragments( $fragments ) {
		$message_text = $this->session->get( FreeShippingNoticeGenerator::SESSION_VARIABLE, '' );

		if ( $this->should_show_notice() && $message_text ) {
			wc_add_notice( $message_text, self::NOTICE_TYPE_SUCCESS, array( self::FLEXIBLE_SHIPPING_FREE_SHIPPING_NOTICE => 'yes' ) );
		}

		$fragments['.wpdesk-notice-container'] = $this->print_notice_container( wc_print_notices( true ) );

		return $fragments;
	}

	/**
	 * Add empty container for custom notices.
	 */
	public function add_notice_container() {
		echo $this->print_notice_container(); // WPCS: XSS OK.
	}

	/**
	 * Add notice to cart.
	 */
	public function add_notice_to_cart() {
		$message_text = $this->session->get( FreeShippingNoticeGenerator::SESSION_VARIABLE, '' );

		if ( ! wp_doing_ajax() && $this->should_show_notice() && is_cart() && $message_text ) {
			wc_add_notice( $message_text, self::NOTICE_TYPE_SUCCESS, array( self::FLEXIBLE_SHIPPING_FREE_SHIPPING_NOTICE => 'yes' ) );
		}
	}

	/**
	 * @param string $message .
	 *
	 * @return string
	 */
	private function print_notice_container( $message = '' ) {
		return '<div class="wpdesk-notice-container">' . wp_kses_post( $message ) . '</div>';
	}

	/**
	 * @return bool
	 */
	private function should_show_notice() {
		return $this->cart->needs_shipping();
	}
}
