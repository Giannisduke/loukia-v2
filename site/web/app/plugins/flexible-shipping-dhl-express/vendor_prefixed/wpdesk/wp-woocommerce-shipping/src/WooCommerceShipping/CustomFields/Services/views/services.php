<?php

namespace DhlVendor;

/**
 * Services template.
 *
 * @package Rendered
 */
/**
 * Params.
 *
 * @var array $params Params.
 */
$params = isset($params) ? $params : array();
?>
<tr style="display:none">
	<th scope="row" class="titledesc">
		<label for="<?php 
echo \esc_attr($params['type']);
?>"><?php 
echo \wp_kses_post($params['title']);
?></label>
	</th>
	<td class="forminp">
		<table class="<?php 
echo \esc_attr($params['field_key']);
?> wpdesk_wc_shipping_custom_services wc_shipping widefat wp-list-table <?php 
echo \esc_attr($params['class']);
?>">
			<thead>
			<tr>
				<th class="sort">&nbsp;</th>
				<th class="service_code"><?php 
\esc_html_e('Code', 'flexible-shipping-dhl-express');
?></th>
				<th class="service_name"><?php 
\esc_html_e('Name', 'flexible-shipping-dhl-express');
?></th>
				<th class="select">
					<?php 
\esc_html_e('Enabled', 'flexible-shipping-dhl-express');
?>
				</th>
			</tr>
			</thead>
			<tbody>
			<?php 
foreach ($services as $service_code => $service_name) {
    if (isset($params['value'][$service_code])) {
        $service_name = !empty($params['value'][$service_code]['name']) ? $params['value'][$service_code]['name'] : $params['options'][$service_code];
    }
    ?>
                <tr>
                    <td class="sort"></td>
                    <td class="service_code"><?php 
    echo \esc_html($service_code);
    ?></td>
                    <td class="service_name">
                        <label>
                            <input name="<?php 
    echo \esc_attr($params['field_key']);
    ?>[<?php 
    echo \esc_attr($service_code);
    ?>][name]" type="text" value="<?php 
    echo \esc_attr($service_name);
    ?>" class="<?php 
    echo \esc_attr($params['field_key']);
    ?>_service_name wpdesk_wc_shipping_custom_services_service_name"/>
                        </label>
                    </td>
                    <td style="width:10%" class="select" nowrap>
                        <?php 
    $enabled = isset($params['value'][$service_code]['enabled']) && !empty($params['value'][$service_code]['enabled']) ? \true : \false;
    ?>
                        <label><input name="<?php 
    echo \esc_attr($params['field_key']);
    ?>[<?php 
    echo \esc_attr($service_code);
    ?>][enabled]" type="checkbox" class="checkbox-select-service" value="<?php 
    echo \esc_attr($service_code);
    ?>" <?php 
    echo \checked($enabled, \true, \false);
    ?> /></label>
                    </td>
                </tr>
			<?php 
}
?>
			</tbody>
			<tfoot>
			<tr>
				<th colspan="4"><span class="description"><?php 
\esc_html_e('Drag and drop the services to control their display order. Confirm by clicking Save changes button below.', 'flexible-shipping-dhl-express');
?></span></th>
			</tr>
			</tfoot>
		</table>
	</td>
</tr>
<?php 
