<?php
/**
 * Settings sidebar.
 *
 * @package WPDesk\FlexibleShippingDhl
 */

/**
 * Params.
 *
 * @var $pro_url string .
 */
?>
<div class="wpdesk-metabox">
	<div class="wpdesk-stuffbox">
		<h3 class="title"><?php esc_html_e( 'Get DHL Express WooCommerce Live Rates PRO!', 'flexible-shipping-dhl-express' ); ?></h3>
		<div class="inside">
			<div class="main">
				<ul>
					<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Handling Fees', 'flexible-shipping-dhl-express' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Delivery Dates', 'flexible-shipping-dhl-express' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Automatic Box Packing', 'flexible-shipping-dhl-express' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Premium Support', 'flexible-shipping-dhl-express' ); ?></li>
					<li><span class="dashicons dashicons-yes"></span> <?php esc_html_e( 'Multicurrency Support', 'flexible-shipping-dhl-express' ); ?></li>
				</ul>

				<a class="button button-primary" href="<?php echo esc_attr( $pro_url ); ?>"
				   target="_blank"><?php esc_html_e( 'Upgrade Now &rarr;', 'flexible-shipping-dhl-express' ); ?></a>
			</div>
		</div>
	</div>
</div>
