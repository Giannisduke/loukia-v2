<?php
/**
 * @var string $settings_field_id
 * @var string $settings_field_name
 * @var string $settings_field_title
 * @var array  $rules_settings
 * @var array  $translations
 * @var array  $available_conditions
 * @var array  $cost_settings_fields
 * @var array  $additional_cost_fields
 * @var array  $special_action_fields
 * @var array  $rules_table_settings
 */
?><tr valign="top" class="flexible_shipping_method_rules">
	<th class="forminp" colspan="2">
		<label for="<?php echo esc_attr( $settings_field_name ); ?>"><?php echo $settings_field_title; ?></label>

        <?php
            $fs_pro_link = get_locale() === 'pl_PL' ? 'https://www.wpdesk.pl/sklep/flexible-shipping-pro-woocommerce/' : 'https://flexibleshipping.com/table-rate/';

            if ( ! in_array( 'flexible-shipping-pro/flexible-shipping-pro.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ):
        ?>
            <p><?php printf( __( 'Check %sFlexible Shipping PRO%s to add advanced rules based on shipment classes, product/item count or additional handling fees/insurance.', 'flexible-shipping' ), '<a href="' . $fs_pro_link . '?utm_campaign=flexible-shipping&utm_source=user-site&utm_medium=link&utm_term=flexible-shipping-pro&utm_content=fs-shippingzone-addnew-rules" target="_blank">', '</a>' ); ?></p>
        <?php endif; ?>

	</th>
</tr>
<tr valign="top" class="flexible-shipping-method-rules-settings">
    <td colspan="2" style="padding:0;">
	    <?php do_action( 'flexible-shipping/method-rules-settings/table/before' ); ?>

	    <div class="flexible-shipping-rules-instruction" style="margin-bottom: 15px;">
		    <p><?php echo wp_kses_post( __( 'Please mind that the ranges you define must not overlap each other and make sure there are no gaps between them.', 'flexible-shipping' ) ); ?></p>
		    <p><?php echo wp_kses_post( sprintf( __( '%1$sExample%2$s: If your rules are based on %1$sprice%2$s and the first range covers $0-$100, the next one should start from %1$s$100.01%2$s, not from %1$s$101%2$s, etc.', 'flexible-shipping' ), '<strong>', '</strong>' ) ); ?></p>
	    </div>

        <div
            class="flexible-shipping-rules-settings"
            id="<?php echo esc_attr( $settings_field_id ); ?>"
            data-settings-field-name='<?php echo esc_attr( json_encode( $settings_field_name ) ); ?>'
            data-rules-settings='<?php echo esc_attr( json_encode( $rules_settings ) ); ?>'
            data-table-settings='<?php echo esc_attr( json_encode( $rules_table_settings ) ); ?>'
            data-translations='<?php echo esc_attr( json_encode( $translations ) ); ?>'
            data-available-conditions='<?php echo esc_attr( json_encode( $available_conditions ) ); ?>'
            data-cost-settings-fields='<?php echo esc_attr( json_encode( $cost_settings_fields ) ); ?>'
            data-special-action-fields='<?php echo esc_attr( json_encode( $special_action_fields ) ); ?>'
            data-additional-cost-fields='<?php echo esc_attr( json_encode( $additional_cost_fields ) ); ?>'
        ></div>

	    <?php do_action( 'flexible-shipping/method-rules-settings/table/after' ); ?>
    </td>
</tr>
