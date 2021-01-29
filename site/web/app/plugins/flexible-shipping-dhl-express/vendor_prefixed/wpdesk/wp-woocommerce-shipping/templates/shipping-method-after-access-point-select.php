<?php

namespace DhlVendor;

/**
 * Checkout select field for collection points.
 *
 * @package WPDesk\WooCommerceShipping\CollectionPoints
 */
/**
 * Variables.
 *
 * @var array  $select_options .
 * @var string $selected_access_point .
 * @var string $label .
 * @var string $description .
 * @var string $shipping_method_id .
 * @var string $field_name .
 */
?>
<tr class="shipping <?php 
echo \esc_attr($shipping_method_id);
?>-shipping">
	<td colspan="2">
		<h4><?php 
echo \esc_html($label);
// wpcs: XSS ok.
?></h4>
		<span class="description"><?php 
echo \esc_html($description);
?></span>
		<?php 
$field_args = array('type' => 'select', 'options' => $select_options);
\woocommerce_form_field($field_name, $field_args, $selected_access_point);
?>
		<script type="text/javascript">
            jQuery(document).ready(function() {
                let collection_point_value = jQuery('#<?php 
echo \esc_attr($field_name);
?>').val();
                if ( jQuery().selectWoo ) {
                    jQuery('#<?php 
echo \esc_attr($field_name);
?>').selectWoo();
                };
                jQuery(document).on( 'change', '#<?php 
echo \esc_attr($field_name);
?>', function(e) {
                    if ( collection_point_value !== jQuery('#<?php 
echo \esc_attr($field_name);
?>').val() ) {
                        e.preventDefault();
                        collection_point_value = jQuery('#<?php 
echo \esc_attr($field_name);
?>').val();
                        jQuery('#<?php 
echo \esc_attr($field_name);
?>').selectWoo( 'destroy' );
                        jQuery(document.body).trigger( 'update_checkout' );
                    }
                });
            });
		</script>
	</td>
</tr>
<?php 
