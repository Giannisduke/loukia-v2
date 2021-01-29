<?php

namespace DhlVendor;

/**
 * Checkout HTML field for collection points.
 *
 * @package WPDesk\WooCommerceShipping\CollectionPoints
 */
/**
 * Variables.
 *
 * @var array  $select_options .
 * @var string $selected_access_point .
 * @var string $label .
 * @var string $unavailable_points_label.
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
		<?php 
if (\count($select_options)) {
    ?>
            <p><?php 
    echo \esc_html($description);
    ?></p>
            <input type="hidden" name="<?php 
    echo \esc_attr($field_name);
    ?>" value="<?php 
    echo \esc_attr($selected_access_point);
    ?>" />
            <p><?php 
    echo $select_options[$selected_access_point];
    // WPCS: XSS ok.
    ?></p>
		<?php 
} else {
    ?>
            <strong class="no-collection-points"><?php 
    echo \esc_html($unavailable_points_label);
    ?></strong>
		<?php 
}
?>
    </td>
</tr>
<?php 
