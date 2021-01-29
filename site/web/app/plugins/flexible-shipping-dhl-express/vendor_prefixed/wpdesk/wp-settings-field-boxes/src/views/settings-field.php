<?php

namespace DhlVendor;

/**
 * Display field.
 *
 * @package WpDesk\WooCommerce\ShippingMethod
 *
 * @var string $field_key
 * @var string $field_title
 * @var string $tooltip_html
 * @var array  $labels
 * @var string $json_value
 * @var array  $built_in_boxes
 * @var string $description
 */
?><tr valign="top">
    <th scope="row" class="titledesc">
        <label for="<?php 
echo \esc_attr($field_key);
?>"><?php 
echo \wp_kses_post($field_title);
echo $tooltip_html;
?></label>
    </th>
    <td class="forminp">
        <fieldset
            class="settings-field-boxes"
            id="<?php 
echo \esc_attr($field_key);
?>_fieldset"
            data-value="<?php 
echo \esc_attr($json_value);
?>"
            data-name="<?php 
echo \esc_attr($field_key);
?>"
            data-builtinboxes="<?php 
echo \esc_attr(\json_encode(\array_values($built_in_boxes)));
?>"
            data-labels="<?php 
echo \esc_attr(\json_encode($labels, \JSON_FORCE_OBJECT));
?>"
            data-description="<?php 
echo \esc_attr($description);
?>"
        >
        </fieldset>
    </td>
</tr>
<?php 
