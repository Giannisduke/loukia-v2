<?php

namespace DhlVendor;

/**
 * Settings scripts.
 *
 * @package WPDesk\WooCommerceShippingPro\CustomFields
 *
 * @var string $packaging_method_field
 * @var string $packaging_boxes_field
 * @var string $shipping_boxes_field
 * @var string $packaging_method_box
 */
?>
<script type="text/javascript">
	(function($) {
		function shipping_method_pro_packaging_method_change() {
			let packaging_method_value = $('#<?php 
echo \esc_attr($packaging_method_field);
?>').val();
			let tr_packaging_boxes = $('#<?php 
echo \esc_attr($packaging_boxes_field);
?>').closest('tr');
			let tr_shipping_boxes = $('#<?php 
echo \esc_attr($shipping_boxes_field);
?>_fieldset').closest('tr');
			if ( packaging_method_value === '<?php 
echo \esc_attr($packaging_method_box);
?>' ) {
				tr_packaging_boxes.show()
				tr_shipping_boxes.show()
			} else {
				tr_packaging_boxes.hide()
				tr_shipping_boxes.hide()
			}
		}

		$(document).ready(function () {
			shipping_method_pro_packaging_method_change();

			if (jQuery.fn.selectWoo) {
				$('#<?php 
echo \esc_attr($packaging_boxes_field);
?>').selectWoo()
			} else {
				if (jQuery.fn.select2) {
					$('#<?php 
echo \esc_attr($packaging_boxes_field);
?>').select2()
				}
			}
		});

		$('#<?php 
echo \esc_attr($packaging_method_field);
?>').change(function () {
			shipping_method_pro_packaging_method_change()
		});
	})(jQuery);
</script>
<?php 
