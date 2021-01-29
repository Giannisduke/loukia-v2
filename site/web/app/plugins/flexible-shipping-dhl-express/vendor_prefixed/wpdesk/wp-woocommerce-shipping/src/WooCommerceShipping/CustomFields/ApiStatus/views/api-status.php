<?php

namespace DhlVendor;

/**
 * API status template.
 *
 * @package WPDesk\WooCommerceShipping\CustomFields
 */
/**
 * Params.
 *
 * @var string $field_id .
 * @var string $title .
 * @var string $tooltip .
 * @var string $default .
 * @var string $description .
 * @var string $class .
 * @var string $css .
 * @var string $security_nonce .
 * @var string $shipping_service_id .
 * @var string $ajax_url .
 */
?>
<tr valign="top">
    <th scope="row" class="titledesc">
        <label for="<?php 
echo \esc_attr($field_id);
?>">
            <?php 
echo \wp_kses_post($title);
?>
	        <?php 
if ($tooltip) {
    ?>
                <span class="woocommerce-help-tip" data-tip="<?php 
    echo \esc_attr($tooltip);
    ?>"></span>
	        <?php 
}
?>
        </label>
    </th>
    <td class="forminp">
        <legend class="screen-reader-text"><span><?php 
echo \wp_kses_post($title);
?></span></legend>
        <span
            class="wpdesk_wc_shipping_api_status <?php 
echo \esc_attr($class);
?>"
            id="<?php 
echo \esc_attr($field_id);
?>"
            style="<?php 
echo \esc_attr($css);
?>"
            data-nonce="<?php 
echo \esc_attr($security_nonce);
?>"
            data-shipping_service_id="<?php 
echo \esc_attr($shipping_service_id);
?>"
            data-ajax_url="<?php 
echo \esc_attr($ajax_url);
?>"
        >
            <?php 
echo $default;
?>
        </span>
        <?php 
if ($description) {
    ?>
            <p class="description"><?php 
    echo $description;
    ?></p>
        <?php 
}
?>
    </td>
</tr>
<?php 
