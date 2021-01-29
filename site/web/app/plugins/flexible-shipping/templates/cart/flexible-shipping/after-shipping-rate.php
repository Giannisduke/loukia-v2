<?php
/**
 * Checkout before customer details
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/flexible-shipping/after_shipping_rate.php
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>

<p class="shipping-method-description">
	<?php echo esc_html( $method_description ); ?>
</p>
