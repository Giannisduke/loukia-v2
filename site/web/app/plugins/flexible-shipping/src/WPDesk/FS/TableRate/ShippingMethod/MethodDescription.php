<?php
/**
 * Class MethodDescription
 *
 * @package WPDesk\FS\TableRate\ShippingMethod
 */

namespace WPDesk\FS\TableRate\ShippingMethod;

use FSVendor\WPDesk\PluginBuilder\Plugin\Hookable;
use FSVendor\WPDesk\View\Renderer\Renderer;

/**
 * Can display method description.
 */
class MethodDescription implements Hookable {

	/**
	 * Renderer.
	 *
	 * @var Renderer;
	 */
	private $renderer;

	/**
	 * MethodDescription constructor.
	 *
	 * @param Renderer $renderer .
	 */
	public function __construct( Renderer $renderer ) {
		$this->renderer = $renderer;
	}


	/**
	 * Hooks.
	 */
	public function hooks() {
		add_action( 'woocommerce_after_shipping_rate', array( $this, 'display_description_if_present' ), 10, 2 );
	}

	/**
	 * @param WC_Shipping_Rate $method .
	 * @param int              $index .
	 *
	 * @internal
	 */
	public function display_description_if_present( $method, $index ) {
		if ( 'flexible_shipping' === $method->method_id ) {
			$meta_data = $method->get_meta_data();
			$description = isset( $meta_data['description'] ) ? $meta_data['description'] : '';
			if ( $description && '' !== $description ) {
				echo $this->renderer->render(
					'cart/flexible-shipping/after-shipping-rate',
					array(
						'method_description' => $description,
					)
				); // WPCS: XSS OK.
			}
		}
	}

}
