<?php

namespace DhlVendor;

/**
 * @var string $settings_prefix
 * @var string $price_adjustment_type_field
 * @var string $price_adjustment_value_field
 * @var string $price_adjustment_type_none
 */
?><script type="text/javascript">

    jQuery(document).ready(function(){

        let $price_adjustment_type_field = jQuery('#<?php 
echo \esc_attr($price_adjustment_type_field);
?>');

        function price_adjustment_type_change() {
            let $price_adjustment_type_value = $price_adjustment_type_field.val();
            let $tr_price_adjustment_value = jQuery('#<?php 
echo \esc_attr($price_adjustment_value_field);
?>').closest('tr');
            if ( $price_adjustment_type_field.is(':hidden') || $price_adjustment_type_value === '<?php 
echo \esc_attr($price_adjustment_type_none);
?>' ) {
                $tr_price_adjustment_value.hide()
            } else {
                $tr_price_adjustment_value.show()
            }
        }

        $price_adjustment_type_field.change(function () {
            price_adjustment_type_change()
        });

        price_adjustment_type_change();

    });

</script>
<?php 
