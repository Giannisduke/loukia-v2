<?php
/**
 * @var $integration_checkbox bool
 * @var $show_fs_connect bool
 *
 * @package Flexible Shipping
 */

$pl = get_locale() === 'pl_PL';
$youtube_url = 'https://www.youtube.com/embed/qsFvYoiNDgU';
$how_to_add_new_shipping_method_url = $pl ? 'https://www.wpdesk.pl/docs/flexible-shipping-pro-woocommerce-docs/?utm_source=flexible-shipping-info&utm_medium=link&utm_campaign=flexible-shipping-resources-box&utm_content=adding-a-shipping-method#metody-wysylki' : 'https://docs.flexibleshipping.com/article/919-flexible-shipping-how-to-add-new-flexible-shipping-method?utm_source=add-shipping-method&utm_medium=link&utm_campaign=flexible-shipping';
$complete_guide_url = $pl ? 'https://www.wpdesk.pl/docs/flexible-shipping-pro-woocommerce-docs/?utm_source=flexible-shipping-info&utm_medium=link&utm_campaign=flexible-shipping-resources-box&utm_content=general-settings#ustawienia-glowne' : 'https://docs.flexibleshipping.com/article/29-flexible-shipping-shipping-methods-complete-guide?utm_source=complete-guide&utm_medium=link&utm_campaign=flexible-shipping';
$currency_support_url = $pl ? 'https://www.wpdesk.pl/docs/flexible-shipping-pro-woocommerce-docs/?utm_source=flexible-shipping-info&utm_medium=link&utm_campaign=flexible-shipping-resources-box&utm_content=currency-support#waluty' : 'https://docs.flexibleshipping.com/article/30-flexible-shipping-currency-support?utm_source=currency&utm_medium=link&utm_campaign=flexible-shipping';
$weight_based_shipping_url = $pl ? 'https://www.wpdesk.pl/docs/flexible-shipping-pro-woocommerce-docs/?utm_source=flexible-shipping-info&utm_medium=link&utm_campaign=flexible-shipping-resources-box&utm_content=weight-based-shipping#koszt-na-wage' : 'https://docs.flexibleshipping.com/article/32-weight-based-shipping-woocommerce?utm_source=weight&utm_medium=link&utm_campaign=flexible-shipping';
$shipping_insurance_url = $pl ? 'https://www.wpdesk.pl/docs/flexible-shipping-pro-woocommerce-docs/?utm_source=flexible-shipping-info&utm_medium=link&utm_campaign=flexible-shipping-resources-box&utm_content=shipping-insurance#ubezpieczenie-przesylki' : 'https://docs.flexibleshipping.com/article/34-fixed-shipping-insurance?utm_source=insurance&utm_medium=link&utm_campaign=flexible-shipping';
$conditional_cash_on_delivery_url = $pl ? 'https://www.wpdesk.pl/docs/flexible-shipping-pro-woocommerce-docs/?utm_source=flexible-shipping-info&utm_medium=link&utm_campaign=flexible-shipping-resources-box&utm_content=conditional-cash-on-delivery#przesylka-za-pobraniem' : 'https://docs.flexibleshipping.com/article/35-conditional-cash-on-delivery-cod?utm_source=cod&utm_medium=link&utm_campaign=flexible-shipping';
?>
</table>
<div class="fs-page-wrap">
	<div class="fs-box">
		<h3 class="wc-settings-sub-title"><?php echo esc_html( __( 'How to use Flexible Shipping?', 'flexible-shipping' ) ); ?></h3>

		<ol>
			<li>
				<?php
				echo wp_kses_post(
					sprintf(
						// Translators: settings link.
						__( 'To add first Flexible Shipping method go to %1$sShipping zones%2$s and add Flexible Shipping to a shipping zone.', 'flexible-shipping' ),
						'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=shipping&section' ) . '">',
						'</a>'
					)
				);
				?>
			</li>

			<li><?php echo esc_html( __( 'You can start the configuration by clicking the Flexible Shipping link in the Shipping methods table.', 'flexible-shipping' ) ); ?></li>
		</ol>

		<h4><?php echo esc_html( __( 'Quick Video Overview', 'flexible-shipping' ) ); ?></h4>

		<div class="flexible-shipping-video">
			<iframe width="688" height="387" src="<?php echo esc_url( $youtube_url ); ?>?rel=0&amp;showinfo=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
		</div>

		<h4><?php echo esc_html( __( 'More resources', 'flexible-shipping' ) ); ?></h4>

		<ul>
			<li><a target="_blank" href="<?php echo esc_url( $how_to_add_new_shipping_method_url ); ?>"><?php echo esc_html( __( 'How to add a new shipping method handled by Flexible Shipping?', 'flexible-shipping' ) ); ?></a></li>
			<li><a target="_blank" href="<?php echo esc_url( $complete_guide_url ); ?>"><?php echo esc_html( __( 'A complete guide to shipping methods', 'flexible-shipping' ) ); ?></a></li>
			<li><a target="_blank" href="<?php echo esc_url( $currency_support_url ); ?>"><?php echo esc_html( __( 'Currency Support', 'flexible-shipping' ) ); ?></a></li>
			<li><a target="_blank" href="<?php echo esc_url( $weight_based_shipping_url ); ?>"><?php echo esc_html( __( 'Weight Based Shipping', 'flexible-shipping' ) ); ?></a></li>
			<li><a target="_blank" href="<?php echo esc_url( $shipping_insurance_url ); ?>"><?php echo esc_html( __( 'Shipping Insurance', 'flexible-shipping' ) ); ?></a></li>
			<li><a target="_blank" href="<?php echo esc_url( $conditional_cash_on_delivery_url ); ?>"><?php echo esc_html( __( 'Conditional Cash on Delivery', 'flexible-shipping' ) ); ?></a></li>
		</ul>

	</div>
</div>
<table>
